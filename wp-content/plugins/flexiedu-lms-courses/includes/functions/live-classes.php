  <?php 

  add_filter('use_block_editor_for_post_type', function($use_block_editor, $post_type) {
            
            if ($post_type === 'live-class') {
                return false; // Disable Gutenberg
            }

            return $use_block_editor;

        }, 10, 2);


        add_action('wp_ajax_get_live_classes', 'flexiedu_get_live_classes');
        add_action('wp_ajax_nopriv_get_live_classes', 'flexiedu_get_live_classes');

        function flexiedu_get_live_classes() {

            flexiedu_verify_ajax_nonce();

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


         add_action('wp_ajax_flexiedu_create_live_class', 'flexiedu_create_live_class_cb');
        add_action('wp_ajax_nopriv_flexiedu_create_live_class', 'flexiedu_create_live_class_cb');

        function flexiedu_create_live_class_cb() {

            flexiedu_verify_ajax_nonce('flexiedu_live_class_action', 'nonce');

            $user_id  = get_current_user_id();
            $post_id  = intval($_POST['class_id']); 

             
                $title = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '';
                $description = isset($_POST['description']) ? sanitize_textarea_field(wp_unslash($_POST['description'])) : '';
                $start_date_time = isset($_POST['start_date_time']) ? sanitize_text_field(wp_unslash($_POST['start_date_time'])) : '';
                $end_date_time   = isset($_POST['end_date_time']) ? sanitize_text_field(wp_unslash($_POST['end_date_time'])) : '';
                $webinar_link    = isset($_POST['webinar_link']) ? esc_url_raw(wp_unslash($_POST['webinar_link'])) : '';
                $event_color     = isset($_POST['event_color']) ? sanitize_hex_color(wp_unslash($_POST['event_color'])) : '';
                $course          = isset($_POST['course']) ? absint($_POST['course']) : 0;



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
                    'post_type'   => 'live-class',
                    'post_author' => $user_id
                ]);

            }

                if (is_wp_error($post_id)) {
                    wp_send_json_error(__('Save failed', 'flexiedu-lms-courses'));
                }

                update_post_meta($post_id, 'start_datetime', $start_date_time);
                update_post_meta($post_id, 'end_datetime', $end_date_time);
                update_post_meta($post_id, 'event_color', $event_color);
                update_post_meta($post_id, 'webinar_link', $webinar_link);
                update_post_meta($post_id, 'course_id_lc', $course);

           
                wp_send_json_success([
                    'message' => __('Nonce verified. Live Class payload received.', 'flexiedu-lms-courses'),
                    'payload' => $payload,
                ]);
            }

            add_action('wp_ajax_edit_class_by_id', 'edit_class_by_id_cb');

        function edit_class_by_id_cb() {

            global $wpdb;

            flexiedu_verify_ajax_nonce();

            $class_id = intval($_POST['class_id']);

            $args = [
                'post_type' => 'live-class',
                'posts_per_page' => -1,
                'p' => $class_id
            ];

            $query = new WP_Query($args);
            $editData = [];

            while ($query->have_posts()) {

                $query->the_post();

                $start = get_field('start_datetime');
                $end   = get_field('end_datetime');
                $color = get_field('event_color');
                $link  = get_field('webinar_link');
                $course = get_field('course_id_lc');

                if (!$start) continue;

                $editData[] = [
                    'title' => get_the_title(),
                    'start' => date('c', strtotime($start)),
                    'end'   => $end ? date('c', strtotime($end)) : null,
                    'url'   => get_permalink(),
                    'color' => $color ?: '#3788d8',
                    'webinar_link' =>  $link,
                    'course_id' => $course
                ];
            }

            wp_reset_postdata();

            wp_send_json_success($editData);
        }


        add_action('wp_ajax_delete_class_by_id', 'delete_class_by_id_cb');

        function delete_class_by_id_cb() {

            global $wpdb;

            flexiedu_verify_ajax_nonce();

            $class_id = intval($_POST['class_id']);

            $deleted = wp_delete_post($class_id, true);

            if ($deleted) {
                wp_send_json_success(['message' => __('Class deleted successfully', 'flexiedu-lms-courses')]);
            } else {
                wp_send_json_error(['message' => __('Failed to delete class', 'flexiedu-lms-courses')]);
            }
        }


        