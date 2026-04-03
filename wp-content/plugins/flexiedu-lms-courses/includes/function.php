<?php 
    function flexiedu_fix_org_post_type_caps() {

        global $wp_post_types;

        if (!isset($wp_post_types['organization'])) return;

        $wp_post_types['organization']->capability_type = ['organization', 'organizations'];
        $wp_post_types['organization']->map_meta_cap = true;

    }

    add_action('init', 'flexiedu_fix_org_post_type_caps', 20);

    function flexiedu_add_org_caps_to_group_leader() {

        $role = get_role('group_leader');

        if (!$role) return;

        // Basic post capabilities for organization CPT
        $caps = [
            'read',
            'edit_posts',
            'edit_published_posts',
            'publish_posts',
            'delete_posts',
            'delete_published_posts'
        ];

        foreach ($caps as $cap) {
            $role->add_cap($cap);
        }

    }
    add_action('init', 'flexiedu_add_org_caps_to_group_leader');

    function flexiedu_sync_org_author_with_user_meta($post_id) {

        // Prevent autosave / revisions
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (wp_is_post_revision($post_id)) return;

        // Get author
        $author_id = get_post_field('post_author', $post_id);

        if (!$author_id) return;

        // Remove old mappings for this organization
        $users = get_users([
            'meta_key'   => 'organization_id',
            'meta_value' => $post_id,
            'fields'     => 'ID'
        ]);

        foreach ($users as $user_id) {
            if ($user_id != $author_id) {
                delete_user_meta($user_id, 'organization_id');
            }
        }

        // Assign new author
        update_user_meta($author_id, 'organization_id', $post_id);

    }

    add_action('save_post_organization', 'flexiedu_sync_org_author_with_user_meta');


    add_filter('login_redirect', 'flexiedu_login_redirect_organizer', 10, 3);

    function flexiedu_login_redirect_organizer($redirect_to, $request, $user) {

        // Safety
        if (!isset($user->ID)) {
            return $redirect_to;
        }

        $user_id = $user->ID;

        // =========================
        // ORGANIZER
        // =========================

        if (in_array('organizer', (array) $user->roles)) {

            $org_id = get_user_meta($user_id, 'organization_id', true);

            if ($org_id) {
                $org = get_post($org_id);

                if ($org && $org->post_type === 'organization') {
                    return home_url('/organization/' . $org->post_name);
                }
            }
        }

        // =========================
        // SUBSCRIBER (LEARNER)
        // =========================
        elseif (in_array('subscriber', (array) $user->roles)) {

            // ✅ Get user groups
            $group_ids = learndash_get_users_group_ids($user_id);

            if (!empty($group_ids)) {

                // Take first group
                $group_id = $group_ids[0];

                // ✅ Get group leaders
                $leaders = learndash_get_groups_administrator_ids($group_id);

                if (!empty($leaders)) {

                    $leader_id = $leaders[0];

                    $org_id = get_user_meta($leader_id, 'organization_id', true);

                    if ($org_id) {
                        $org = get_post($org_id);

                        if ($org && $org->post_type === 'organization') {
                            return home_url('/organization/' . $org->post_name . '/learner-dashboard');
                        }
                    }
                }
            }
        }

        return $redirect_to;
    }


    add_action('wp_ajax_add_document', 'handle_add_document');
    
    function handle_add_document() {

        if (!is_user_logged_in()) {
            wp_send_json_error('Unauthorized');
        }

        $user_id  = get_current_user_id();
        $post_id  = intval($_POST['post_id']); //  ADD THIS
        $title    = sanitize_text_field($_POST['title']);
        $doc_types = json_decode(stripslashes($_POST['doc_types']), true);

        // UPDATE MODE
        if ($post_id) {

            wp_update_post([
                'ID'         => $post_id,
                'post_title' => $title,
            ]);

        } else {

            // CREATE
            $post_id = wp_insert_post([
                'post_title'  => $title,
                'post_status' => 'publish',
                'post_type'   => 'document',
                'post_author' => $user_id
            ]);
        }

        if (is_wp_error($post_id)) {
            wp_send_json_error('Save failed');
        }

        // File upload (replace if new file uploaded)
        if (!empty($_FILES['document_file']['name'])) {

            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            $attachment_id = media_handle_upload('document_file', $post_id);

            if (!is_wp_error($attachment_id)) {
                update_post_meta($post_id, '_document_file', $attachment_id);
            }
        }

        // Taxonomy update
        if (!empty($doc_types)) {
            wp_set_object_terms($post_id, $doc_types, 'document-type');
        }

        wp_send_json_success([
            'post_id' => $post_id
        ]);
    }

    add_action('wp_ajax_get_document', 'get_document_data');

    function get_document_data() {

        if (!is_user_logged_in()) {
            wp_send_json_error('Unauthorized');
        }

        $post_id = intval($_POST['post_id']);
        $post    = get_post($post_id);

        if (!$post || $post->post_type !== 'document') {
            wp_send_json_error('Invalid document');
        }

        // Taxonomy
        $terms = wp_get_object_terms($post_id, 'document-type', [
            'fields' => 'slugs'
        ]);

        // File
        $file_id  = get_post_meta($post_id, '_document_file', true);
        $file_url = $file_id ? wp_get_attachment_url($file_id) : '';

        wp_send_json_success([
            'title' => $post->post_title,
            'types' => $terms,
            'file'  => $file_url
        ]);
    }


    add_filter('use_block_editor_for_post_type', function($use_block_editor, $post_type) {
    
    if ($post_type === 'live-class') {
        return false; // Disable Gutenberg
    }

    return $use_block_editor;

}, 10, 2);


add_action('wp_ajax_get_live_classes', 'flexiedu_get_live_classes');
add_action('wp_ajax_nopriv_get_live_classes', 'flexiedu_get_live_classes');

function flexiedu_get_live_classes() {

    $args = [
        'post_type' => 'live-class',
        'posts_per_page' => -1
    ];

    $query = new WP_Query($args);
    $events = [];

    while ($query->have_posts()) {
        $query->the_post();

        $start = get_field('start_datetime');
        $end   = get_field('end_datetime');
        $color = get_field('event_color');
        $link  = get_field('webinar_link');

        if (!$start) continue;

        $events[] = [
            'title' => get_the_title(),
            'start' => date('c', strtotime($start)),
            'end'   => $end ? date('c', strtotime($end)) : null,
            'url'   => get_permalink(),
            'color' => $color ?: '#3788d8',
            'extendedProps' => [
                'webinar_link' => $link
            ]
        ];
    }

    wp_reset_postdata();

    wp_send_json($events);
}

    add_action('wp_ajax_create_course', 'create_course_cb');
    add_action('wp_ajax_nopriv_create_course', 'create_course_cb');

    function create_course_cb() {

        // Validate input
    
        $author_id    = isset($_POST['authorId']) ? intval($_POST['authorId']) : get_current_user_id();

        if (empty($author_id )) {
            wp_send_json_error(['message' => 'Author ID is required']);
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
            wp_send_json_error(['message' => 'Failed to create course']);
        }

        // Optional: Initialize LearnDash default settings (important for stability)
        if (function_exists('learndash_update_setting')) {
            learndash_update_setting($course_id, '_ld_course_price_type', 'open');
        }

        // Return course ID
        wp_send_json_success([
            'course_id' => $course_id,
            'message'   => 'Course created successfully'
        ]);
    }

    add_action('template_redirect', 'flexiedu_instructor_dashboard_redirect', 1);

    function flexiedu_instructor_dashboard_redirect() {

        // Only logged-in users
        if (!is_user_logged_in()) return;

        // Check current URL is instructor-dashboard
        if (!is_page('super-lms-bord')) return;

        $user = wp_get_current_user();

        // Only for organizer
        if (!in_array('organizer', (array) $user->roles)) {
            return;
        }

        // Get organization
        $org_id = get_user_meta($user->ID, 'organization_id', true);

        if (!$org_id) return;

        $org = get_post($org_id);

        if (!$org || $org->post_type !== 'organization') return;

        // Redirect to org dashboard
        wp_redirect(home_url('/organization/' . $org->post_name));
        exit;
    }


    add_action('wp_ajax_delete_document', 'handle_delete_document');

    function handle_delete_document() {

        if (!is_user_logged_in()) {
            wp_send_json_error('Unauthorized');
        }

        $user_id = get_current_user_id();
        $post_id = intval($_POST['post_id']);

        if (!$post_id) {
            wp_send_json_error('Invalid ID');
        }

        $post = get_post($post_id);

        if (!$post || $post->post_type !== 'document') {
            wp_send_json_error('Invalid document');
        }

        // Security: only author can delete
        if ($post->post_author != $user_id && !current_user_can('delete_others_posts')) {
            wp_send_json_error('Permission denied');
        }

        // Delete permanently
        wp_delete_post($post_id, true);

        wp_send_json_success('Deleted successfully');
    }

    add_action('wp_ajax_flexi_request_enrollment', 'flexi_request_enrollment');

    function flexi_request_enrollment() {

        if (!is_user_logged_in()) {
            wp_send_json_error('Login required');
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
            wp_send_json_error('Already requested');
        }

        // ✅ Insert
        $inserted = $wpdb->insert($table, [
            'user_id'         => $user_id,
            'course_id'       => $course_id,
            'organization_id' => $org_id,
            'status'          => 'pending'
        ]);

        if ($inserted) {
            wp_send_json_success('Request sent');
        } else {
            wp_send_json_error('DB error');
        }
    }

    add_action('wp_ajax_accept_application', 'accept_application_cb');

    function accept_application_cb() {

        if (!is_user_logged_in()) {
            wp_send_json_error('Login required');
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
            wp_send_json_error('Request not found');
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
            'message'    => 'Approved successfully',
            'courseName' => get_the_title($course_id),
            'user_id'    => $user_id
        ]);
    }


    add_action('wp_ajax_reject_application', 'reject_application_cb');

    function reject_application_cb() {

        if (!is_user_logged_in()) {
            wp_send_json_error('Login required');
        }

        global $wpdb;

        $table = $wpdb->prefix . 'flexi_enrollment_requests';

        $rowId      = isset($_POST['row_id']) ? intval($_POST['row_id']) : 0;
        $applyAfter = isset($_POST['apply_after']) ? intval($_POST['apply_after']) : 0;

        if (!$rowId || !$applyAfter) {
            wp_send_json_error('Invalid data');
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
                'message' => 'Rejected successfully',
                'reapply_after' => $reapplyTimestamp
            ]);
        } else {
            wp_send_json_error('DB update failed');
        }
    }

    add_filter('wp_nav_menu_items', 'flexi_dynamic_dash_menu', 9999, 2);

    function flexi_dynamic_dash_menu($items, $args) {
    
        if ($args->theme_location !== 'menu-6') {
            return $items;
        }
    
        if (!is_user_logged_in()) {
            return $items;
        }

        $user = wp_get_current_user();
        $user_id = $user->ID;

        
        if (in_array('organizer', (array) $user->roles) || in_array('subscriber', (array) $user->roles)) {


   
    
        $menu = '';
    
        if (in_array('organizer', (array) $user->roles)) {
    
            $org_id = get_user_meta($user_id, 'organization_id', true);
    
            if ($org_id) {
    
                $slug = get_post_field('post_name', $org_id);
                $base = home_url('/organization/' . $slug);
    
                $menu .= '<li><a href="'.$base.'">Dashboard</a></li>';
                $menu .= '<li><a href="'.$base.'/profile">Profile</a></li>';
                $menu .= '<li><a href="'.$base.'/courses">Course</a></li>';
                $menu .= '<li><a href="'.wp_logout_url().'">Logout</a></li>';
    
            } else {
                $menu .= '<li><a href="#">No Org Assigned</a></li>';
            }
    
        } else {
    
            $dashboard = flexi_get_user_org_url($user_id);
    
            if ($dashboard) {
                $menu .= '<li><a href="'.$dashboard.'">Dashboard</a></li>';
                $menu .= '<li><a href="'.$dashboard.'/profile">Profile</a></li>';
                $menu .= '<li><a href="'.$dashboard.'/courses">Course</a></li>';
            }
    
            $menu .= '<li><a href="'.wp_logout_url().'">Logout</a></li>';
        }
    
            return $menu ?: $items;
        }else{
            return $items; 
        }
    }


        

    // add_filter('wp_nav_menu_items', 'flexi_dynamic_menu', 10, 2);

    // function flexi_dynamic_menu($items, $args) {

    //     // Target menu
    //     if ($args->theme_location !== 'menu-5') {
    //         return $items;
    //     }

    //     if (!is_user_logged_in()) {
    //         return $items;
    //     }

    //     $user = wp_get_current_user();
    //     $user_id = $user->ID;

    //     // Reset menu
    //     $items = '';

    //     // ==========================
    //     // ORGANIZER + SUBSCRIBER
    //     // ==========================
    //     if (
    //         in_array('organizer', (array) $user->roles) ||
    //         in_array('subscriber', (array) $user->roles)
    //     ) {

    //         $dashboard = flexi_get_user_org_url($user_id);

    //         $items .= '<li><a href="'.$dashboard.'">Dashboard</a></li>';
    //     }

    //     return $items;
    // }

    function flexi_get_user_org_url($user_id = 0, $type = 'dashboard') {

        if (!$user_id) {
            $user_id = get_current_user_id();
        }
    
        if (!$user_id) return false;
    
        $user = get_userdata($user_id);
    
        if (!$user) return false;
    
        // =========================
        // ORGANIZER
        // =========================

        if (in_array('organizer', (array) $user->roles)) {
    
            $org_id = get_user_meta($user_id, 'organization_id', true);
    
            if ($org_id) {
                $org = get_post($org_id);
    
                if ($org && $org->post_type === 'organization') {
    
                    $base = home_url('/organization/' . $org->post_name);
    
                    switch ($type) {
                        case 'courses':
                            return $base . '/courses';
                        case 'applications':
                            return $base . '/received-application';
                        default:
                            return $base;
                    }
                }
            }
        }
    
        // =========================
        // SUBSCRIBER (LEARNER)
        // =========================
        if (in_array('subscriber', (array) $user->roles)) {
    
            $group_ids = learndash_get_users_group_ids($user_id);
    
            if (!empty($group_ids)) {
    
                $group_id = $group_ids[0];
    
                $leaders = learndash_get_groups_administrator_ids($group_id);
    
                if (!empty($leaders)) {
    
                    $leader_id = $leaders[0];
    
                    $org_id = get_user_meta($leader_id, 'organization_id', true);
    
                    if ($org_id) {

                        $org = get_post($org_id);
    
                        if ($org && $org->post_type === 'organization') {
    
                            $base = home_url('/organization/' . $org->post_name);
    
                            switch ($type) {
                                case 'courses':
                                    return $base . '/courses';
                                case 'dashboard':
                                    return $base . '/learner-dashboard';
                                default:
                                    return $base;
                                    
                            }
                        }
                    }
                }
            }
        }
    
        return home_url('/');
    }

        add_action('admin_init', 'flexi_redirect_profile_page');

        function flexi_redirect_profile_page() {
        
            // Only run in admin
            if (!is_admin()) return;
        
            // Check current page
            global $pagenow;
        
            if ($pagenow !== 'profile.php') return;
        
            // Allow admins only
            if (current_user_can('administrator')) return;
        
            // Redirect others
            wp_redirect(wc_get_page_permalink('myaccount'));
            exit;
        }

        add_action('after_setup_theme', function () {
            if (!current_user_can('administrator')) {
                show_admin_bar(false);
            }
        });

        
        add_action('wp_ajax_flexi_save_profile', 'flexi_save_profile_cb');

        function flexi_save_profile_cb() {

            // if (!isset($_POST['account_nonce']) ||
            //     !wp_verify_nonce($_POST['account_nonce'], 'save_account_details')) {
            //     wp_send_json_error('Invalid request');
            // }

            if (!is_user_logged_in()) {
                wp_send_json_error('Not logged in');
            }

            $user_id = get_current_user_id();
            $current_user = wp_get_current_user();

            // First Name
            if (!empty($_POST['first_name'])) {
                update_user_meta($user_id, 'first_name', sanitize_text_field($_POST['first_name']));
            }

            // Last Name
            if (!empty($_POST['last_name'])) {
                update_user_meta($user_id, 'last_name', sanitize_text_field($_POST['last_name']));
            }

            // Email
            if (!empty($_POST['user_email'])) {

                $email = sanitize_email($_POST['user_email']);

                if (!is_email($email)) {
                    wp_send_json_error('Invalid email');
                }

                if ($email !== $current_user->user_email && email_exists($email)) {
                    wp_send_json_error('Email already in use');
                }

                wp_update_user([
                    'ID' => $user_id,
                    'user_email' => $email
                ]);
            }

            // Password
            if (!empty($_POST['new_password'])) {

                if ($_POST['new_password'] !== $_POST['confirm_password']) {
                    wp_send_json_error('Passwords do not match');
                }

                wp_set_password($_POST['new_password'], $user_id);
                wp_set_auth_cookie($user_id);
            }

            // Phone
            if (!empty($_POST['phone'])) {
                update_user_meta($user_id, 'phone', sanitize_text_field($_POST['phone']));
            }

            // Image Upload
            if (!empty($_FILES['profile_image']['name'])) {

                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');
                require_once(ABSPATH . 'wp-admin/includes/image.php');

                $attachment_id = media_handle_upload('profile_image', 0);

                if (!is_wp_error($attachment_id)) {

                    $old = get_user_meta($user_id, 'profile_image', true);
                    if ($old) {
                        wp_delete_attachment($old, true);
                    }

                    update_user_meta($user_id, 'profile_image', $attachment_id);

                } else {
                    wp_send_json_error('Image upload failed');
                }
            }

            wp_send_json_success('Profile updated successfully');
        }


    /**
     * Handle Registration
     */
    
     add_action('wp_ajax_flexi_register_user', 'flexi_register_user');
     add_action('wp_ajax_nopriv_flexi_register_user', 'flexi_register_user');

    function flexi_register_user(){

        check_ajax_referer('flexiedu_register_action', 'flexiedu_nonce');


        $fname = sanitize_text_field($_POST['first_name']);
        $lname = sanitize_text_field($_POST['last_name']);
        $org_name = sanitize_text_field($_POST['org_name']);
        $username = sanitize_user($_POST['username']);
        $email    = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $bio      = sanitize_textarea_field($_POST['bio']);

        // Validation
        if (username_exists($username)) {
            wp_send_json_error('Username already exists');
        }

        if (email_exists($email)) {
            wp_send_json_error('Email already registered');
        }

        // 1️⃣ Create user
        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            wp_send_json_error($user_id->get_error_message());
        }

        $user = new WP_User($user_id);

        $user->set_role('organizer'); 
        $user->add_role('group_leader');
        $user->add_role('wdm_instructor');

        if (!empty($fname)) {
            update_user_meta($user_id, 'first_name', $fname);
        }
        
        if (!empty($lname)) {
            update_user_meta($user_id, 'last_name', $lname);
        }

        if (!empty($bio)) {
            update_user_meta($user_id, 'description', $bio);
        }

        // 🖼 Upload image
        if (!empty($_FILES['profile_pic']['name'])) {

            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            $attachment_id = media_handle_upload('profile_pic', 0);

            if (!is_wp_error($attachment_id)) {
                update_user_meta($user_id, 'profile_picture', $attachment_id);
            }
        }

        // 2️⃣ Create organization
        $org_id = wp_insert_post([
            'post_title'  => $org_name,
            'post_type'   => 'organization',
            'post_status' => 'publish',
            'post_author' => $user_id,
        ]);

        // Mapping
        update_user_meta($user_id, 'organization_id', $org_id);
        update_post_meta($org_id, 'organization_owner', $user_id);

        // // Login
        // wp_set_current_user($user_id);
        // wp_set_auth_cookie($user_id);

        // 3️⃣ Create LearnDash Group
        $group_id = wp_insert_post([
            'post_title'  => $org_name . ' Group',
            'post_type'   => 'groups',
            'post_status' => 'publish',
            'post_author' => $user_id,
        ]);

        if (!is_wp_error($group_id)) {

            // Assign user as Group Leader
            if (function_exists('ld_update_leader_group_access')) {
                ld_update_leader_group_access($user_id, $group_id);
            }

            // Optional: assign user to group (as member also)
            if (function_exists('ld_update_group_access')) {
                ld_update_group_access($user_id, $group_id);
            }

            // Save mapping
            update_post_meta($org_id, 'groups', $group_id);
            update_user_meta($user_id, 'ld_group_id', $group_id);
        }


        // Redirect URL
        $redirect = home_url('lms-console/groups');

        wp_send_json_success([
            'message' => 'Registration successful',
            'redirect' => $redirect
        ]);
    }