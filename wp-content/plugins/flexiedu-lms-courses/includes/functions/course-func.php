 <?php
 
    add_action('wp_ajax_create_course', 'create_course_cb');
    add_action('wp_ajax_nopriv_create_course', 'create_course_cb');

        function create_course_cb() {

            flexiedu_verify_ajax_nonce();

            // Validate input
        
            $author_id    = isset($_POST['authorId']) ? intval($_POST['authorId']) : get_current_user_id();

            if (empty($author_id )) {
                wp_send_json_error(['message' => __('Author ID is required', 'flexiedu-lms-courses')]);
            }

            // Create LearnDash Course (post type: sfwd-courses)
            $course_id = wp_insert_post([
                'post_title'   => "Course",
                'post_content' => '', // blank description
                'post_status'  => 'draft',
                'post_type'    => 'sfwd-courses',
                'post_author'  => $author_id,
            ]);

            if (is_wp_error($course_id)) {
                wp_send_json_error(['message' => __('Failed to create course', 'flexiedu-lms-courses')]);
            }

            // Optional: Initialize LearnDash default settings (important for stability)
            if (function_exists('learndash_update_setting')) {
                learndash_update_setting($course_id, '_ld_course_price_type', 'open');
            }

            // Return course ID
            wp_send_json_success([
                'course_id' => $course_id,
                'message'   => __('Course created successfully', 'flexiedu-lms-courses')
            ]);
        }

       
        add_action('wp_ajax_flexi_request_enrollment', 'flexi_request_enrollment');

        function flexi_request_enrollment() {

            flexiedu_verify_ajax_nonce();

            if (!is_user_logged_in()) {
                wp_send_json_error(__('Login required', 'flexiedu-lms-courses'));
            }

            global $wpdb;

            $table = $wpdb->prefix . 'flexi_enrollment_requests';

            $user_id   = get_current_user_id();
            $course_id = intval($_POST['course_id']);
            $org_id    = intval($_POST['org_id']);

            // ✅ Check duplicate
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table 
                WHERE user_id = %d AND course_id = %d LIMIT 1",
                $user_id, $course_id
            ));

            if ($exists) {
                wp_send_json_error(__('Already requested', 'flexiedu-lms-courses'));
            }

            // ✅ Insert
            $inserted = $wpdb->insert($table, [
                'user_id'         => $user_id,
                'course_id'       => $course_id,
                'organization_id' => $org_id,
                'status'          => 'pending'
            ]);

            if ($inserted) {
                wp_send_json_success(__('Request sent', 'flexiedu-lms-courses'));
            } else {
                wp_send_json_error(__('DB error', 'flexiedu-lms-courses'));
            }
        }

        add_action('wp_ajax_accept_application', 'accept_application_cb');

        function accept_application_cb() {

            flexiedu_verify_ajax_nonce();

            if (!is_user_logged_in()) {
                wp_send_json_error(__('Login required', 'flexiedu-lms-courses'));
            }

            global $wpdb;

            $table = $wpdb->prefix . 'flexi_enrollment_requests';

            $rowId = intval($_POST['rowId']);

            // Get course + user
            $row = $wpdb->get_row($wpdb->prepare(
                "SELECT user_id, course_id FROM $table WHERE id = %d",
                $rowId
            ));

            if (!$row) {
                wp_send_json_error(__('Request not found', 'flexiedu-lms-courses'));
            }

            $user_id   = $row->user_id;
            $course_id = $row->course_id;

            // =====================================
            // STEP 1: Assign course to user
            // =====================================

            ld_update_course_access($user_id, $course_id);

            // =====================================
            // STEP 2: Update status in DB
            // =====================================

            $wpdb->update(
                $table,
                ['status' => 'approved'],
                ['id' => $rowId],
                ['%s'],
                ['%d']
            );

            // =====================================
            // RESPONSE
            // =====================================

            wp_send_json_success([
                'message'    => __('Approved successfully', 'flexiedu-lms-courses'),
                'courseName' => get_the_title($course_id),
                'user_id'    => $user_id
            ]);
        }


        add_action('wp_ajax_reject_application', 'reject_application_cb');

        function reject_application_cb() {

            flexiedu_verify_ajax_nonce();

            if (!is_user_logged_in()) {
                wp_send_json_error(__('Login required', 'flexiedu-lms-courses'));
            }

            global $wpdb;

            $table = $wpdb->prefix . 'flexi_enrollment_requests';

            $rowId      = isset($_POST['row_id']) ? intval($_POST['row_id']) : 0;
            $applyAfter = isset($_POST['apply_after']) ? intval($_POST['apply_after']) : 0;

            if (!$rowId || !$applyAfter) {
                wp_send_json_error(__('Invalid data', 'flexiedu-lms-courses'));
            }

            // ✅ Convert days → MySQL DATETIME
            $reapplyTimestamp = date(
                'Y-m-d H:i:s',
                strtotime("+$applyAfter days", current_time('timestamp'))
            );

            // ✅ Update DB
            $updated = $wpdb->update(
                $table,
                [
                    'status'        => 'rejected',
                    'reapply_after' => $reapplyTimestamp
                ],
                ['id' => $rowId],
                ['%s', '%s'], // both string
                ['%d']
            );

            if ($updated !== false) {
                wp_send_json_success([
                    'message' => __('Rejected successfully', 'flexiedu-lms-courses'),
                    'reapply_after' => $reapplyTimestamp
                ]);
            } else {
                wp_send_json_error(__('DB update failed', 'flexiedu-lms-courses'));
            }
        }

        add_action('wp_ajax_flexiedu_create_course_bundle', 'flexiedu_create_course_bundle_cb');

        function flexiedu_create_course_bundle_cb() {

            flexiedu_verify_ajax_nonce();

            if (!is_user_logged_in()) {
                wp_send_json_error(__('Unauthorized', 'flexiedu-lms-courses'));
            }

            $user_id = get_current_user_id();
            $user = wp_get_current_user();

            if (!in_array('organizer', (array) $user->roles)) {
                wp_send_json_error(__('Only organizers can manage course bundles', 'flexiedu-lms-courses'));
            }

            // Org
            $org_id = get_user_meta($user_id, 'organization_id', true);
            if (!$org_id) {
                wp_send_json_error(__('No organization assigned', 'flexiedu-lms-courses'));
            }

            $org = get_post($org_id);
            if (!$org || $org->post_type !== 'organization') {
                wp_send_json_error(__('Invalid organization', 'flexiedu-lms-courses'));
            }

            // Inputs
            $bundle_id  = intval($_POST['bundle_id'] ?? 0);
            $title      = sanitize_text_field($_POST['title'] ?? '');
            $description= sanitize_textarea_field($_POST['description'] ?? '');
            $course_ids_json = $_POST['course_ids'] ?? '';

            if (empty($title) || empty($description)) {
                wp_send_json_error(__('Title & Description required', 'flexiedu-lms-courses'));
            }

            // Decode courses
            $course_ids = json_decode(stripslashes($course_ids_json), true);

            if (!is_array($course_ids) || empty($course_ids)) {
                wp_send_json_error(__('Please select at least one course', 'flexiedu-lms-courses'));
            }

            // Validate courses
            $valid_course_ids = [];

            foreach ($course_ids as $course_id) {
                $course_id = intval($course_id);

                if ($course_id > 0) {
                    $course = get_post($course_id);

                    if ($course && $course->post_type === 'sfwd-courses') {
                        if (sfwd_lms_has_access($course_id, $user_id)) {
                            $valid_course_ids[] = $course_id;
                        }
                    }
                }
            }

            if (empty($valid_course_ids)) {
                wp_send_json_error(__('No valid courses selected', 'flexiedu-lms-courses'));
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'course_bundles';

            // 🔥 UPDATE MODE
            if ($bundle_id > 0) {

                // Check ownership
                $existing = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT * FROM $table_name WHERE id = %d AND created_by = %d",
                        $bundle_id,
                        $user_id
                    )
                );

                if (!$existing) {
                    wp_send_json_error(__('Bundle not found or permission denied', 'flexiedu-lms-courses'));
                }

                $updated = $wpdb->update(
                    $table_name,
                    [
                        'title'       => $title,
                        'description' => $description,
                        'course_ids'  => wp_json_encode($valid_course_ids),
                    ],
                    ['id' => $bundle_id],
                    ['%s','%s','%s'],
                    ['%d']
                );

                if ($updated === false) {
                    wp_send_json_error(__('Failed to update bundle', 'flexiedu-lms-courses'));
                }

                wp_send_json_success([
                    'message'   => __('Bundle updated successfully', 'flexiedu-lms-courses'),
                    'bundle_id' => $bundle_id
                ]);
            }

            // 🔥 CREATE MODE
            else {

                $inserted = $wpdb->insert(
                    $table_name,
                    [
                        'org_slug'    => $org->post_name,
                        'title'       => $title,
                        'description' => $description,
                        'course_ids'  => wp_json_encode($valid_course_ids),
                        'created_by'  => $user_id,
                    ],
                    ['%s','%s','%s','%s','%d']
                );

                if ($inserted === false) {
                    wp_send_json_error(__('Failed to create course bundle', 'flexiedu-lms-courses'));
                }

                wp_send_json_success([
                    'message'   => __('Course bundle created successfully', 'flexiedu-lms-courses'),
                    'bundle_id' => $wpdb->insert_id
                ]);
            }
        }

add_action('wp_ajax_get_bundle', 'get_bundle_cb');

    function get_bundle_cb() {
        global $wpdb;

        $bundle_id = intval($_POST['bundle_id']);

        $table = $wpdb->prefix . 'course_bundles';

        $bundle = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table WHERE id = %d", $bundle_id)
        );

        if (!$bundle) {
            wp_send_json_error(['message' => 'Bundle not found']);
        }

        $bundle->course_ids = json_decode($bundle->course_ids, true);

        wp_send_json_success($bundle);
    }

        add_action('wp_ajax_flexiedu_delete_course_bundle', 'flexiedu_delete_course_bundle_cb');

        function flexiedu_delete_course_bundle_cb() {

            flexiedu_verify_ajax_nonce();

            if (!is_user_logged_in()) {
                wp_send_json_error(__('Unauthorized', 'flexiedu-lms-courses'));
            }

            $user_id = get_current_user_id();
            $user    = wp_get_current_user();

            if (!in_array('organizer', (array) $user->roles, true)) {
                wp_send_json_error(__('Permission denied', 'flexiedu-lms-courses'));
            }

            $bundle_id = intval($_POST['bundle_id'] ?? 0);

            if (!$bundle_id) {
                wp_send_json_error(__('Invalid bundle ID', 'flexiedu-lms-courses'));
            }

            global $wpdb;
            $table = $wpdb->prefix . 'course_bundles';

            // // Check ownership
            // $bundle = $wpdb->get_row(
            //     $wpdb->prepare(
            //         "SELECT id FROM $table WHERE id = %d AND created_by = %d",
            //         $bundle_id,
            //         $user_id
            //     )
            // );

            // if (!$bundle) {
            //     wp_send_json_error(__('Bundle not found or permission denied', 'flexiedu-lms-courses'));
            // }

            //  Delete
            $deleted = $wpdb->delete(
                $table,
                ['id' => $bundle_id],
                ['%d']
            );

            if ($deleted === false) {
                wp_send_json_error(__('Failed to delete bundle', 'flexiedu-lms-courses'));
            }

            wp_send_json_success([
                'message' => __('Bundle deleted successfully', 'flexiedu-lms-courses')
            ]);
        }
