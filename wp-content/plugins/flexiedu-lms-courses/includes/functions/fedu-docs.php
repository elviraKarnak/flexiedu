<?php 
        
    
         add_action( 'init', 'create_document_type_taxonomy');

            function create_document_type_taxonomy() {
            
            $labels = array(
                'name' => _x( 'Document Type', 'flexiedu-lms-courses' ),
                'singular_name' => _x( 'Single Document Type', 'flexiedu-lms-courses' ),
                'search_items' =>  __( 'Search  Document Types' ),
                'all_items' => __( 'All Document Types' ),
                'parent_item' => __( 'Parent Document Type' ),
                'parent_item_colon' => __( 'Parent Document Type:' ),
                'edit_item' => __( 'Edit Single Document Type' ), 
                'update_item' => __( 'Update Single Document Type' ),
                'add_new_item' => __( 'Add New Single Document Type' ),
                'new_item_name' => __( 'New Single Document Type' ),
                'menu_name' => __( 'Document Types' ),
            );    
            
            // Now register the taxonomy
            register_taxonomy('document_type',array('document'), array(
                'hierarchical' => true,
                'labels' => $labels,
                'show_ui' => true,
                'show_in_rest' => true,
                'show_admin_column' => true,
                'show_in_menu' => true,
                'query_var' => true,
                // 'rewrite' => array( 'slug' => 'bookingtype' ),
            ));
            
            }
                

          
         add_action( 'init', 'create_cat_taxonomy');

            function create_cat_taxonomy() {
            
                $labels = array(
                    'name' => _x( 'Document Category', 'flexiedu-lms-courses' ),
                    'singular_name' => _x( 'Single Document Category', 'flexiedu-lms-courses' ),
                    'search_items' =>  __( 'Search Document Categories' ),
                    'all_items' => __( 'All Document Categories' ),
                    'parent_item' => __( 'Parent Document Category' ),
                    'parent_item_colon' => __( 'Parent Document Category:' ),
                    'edit_item' => __( 'Edit Single Document Category' ), 
                    'update_item' => __( 'Update Single Document Category' ),
                    'add_new_item' => __( 'Add New Single Document Category' ),
                    'new_item_name' => __( 'New Single Document Category' ),
                    'menu_name' => __( 'Document Categories' ),
                );    
                
                // Now register the taxonomy
                register_taxonomy('document_cat',array('document'), array(
                    'hierarchical' => true,
                    'labels' => $labels,
                    'show_ui' => true,
                    'show_in_rest' => true,
                    'show_admin_column' => true,
                    'show_in_menu' => true,
                    'query_var' => true,
                    // 'rewrite' => array( 'slug' => 'bookingtype' ),
                ));
            
            }
            
       
        add_filter('use_block_editor_for_post_type', function($use_block_editor, $post_type) {

            if ($post_type === 'document') {
                return false; // ❌ disable Gutenberg
            }

            return $use_block_editor;

        }, 10, 2);
        
        
        
        add_action('wp_ajax_add_document', 'handle_add_document');
        
        function handle_add_document() {

            flexiedu_verify_ajax_nonce();

            if (!is_user_logged_in()) {
                wp_send_json_error(__('Unauthorized', 'flexiedu-lms-courses'));
            }

            $user_id  = get_current_user_id();
            $post_id  = intval($_POST['post_id']); //  ADD THIS
            $title    = sanitize_text_field($_POST['title']);
            $doc_types = json_decode(stripslashes($_POST['doc_types']), true);
            $docgroup = sanitize_text_field($_POST['docgroup']);


            // print_r($_POST); // Debugging line, can be removed later

            // exit;

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
                wp_send_json_error(__('Save failed', 'flexiedu-lms-courses'));
            }


            //$doc_types = ['career-pathways', 'user-guides-policies'];

                   if(!empty($doc_types) && is_array($doc_types)) {
                        wp_set_object_terms(intval($post_id), $doc_types, 'document_cat', true);
                   }
            //$docgroup = ['other-group'];
                   
                   if(!empty($docgroup) ) {
                    wp_set_object_terms(intval($post_id), [$docgroup], 'document_type', true);
                   }



            // File upload (replace if new file uploaded)
            if (!empty($_FILES['document_file']['name'])) {

                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';

                $attachment_id = media_handle_upload('document_file', $post_id);

                if (!is_wp_error($attachment_id)) {
                    update_post_meta($post_id, '_document_file', $attachment_id);
                } else {
                    wp_send_json_error($attachment_id->get_error_message());
                }
            }

            if (!empty($_FILES['featured_image']['name'])) {

                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';

                $featured_image_id = media_handle_upload('featured_image', $post_id);

                if (!is_wp_error($featured_image_id)) {
                    set_post_thumbnail($post_id, $featured_image_id);
                } else {
                    wp_send_json_error($featured_image_id->get_error_message());
                }
            }

            // // Taxonomy update

       
                


            // if (!empty($doc_types)) {
            //     wp_set_object_terms($post_id, $doc_types, 'document-type');
            // }

            // if (!empty($docgroup) && taxonomy_exists('document-group') && $docgroup == 'other-group') {
            //      wp_set_object_terms($post_id, ['other-group'], 'document-group');
            // }else if(!empty($docgroup) && taxonomy_exists('document-group') && $docgroup == 'admin-group'){
            //         wp_set_object_terms($post_id, ['admin-group'], 'document-group');
            // }
            // Get doc types safely
                // $doc_types = [];

                // if (!empty($_POST['doc_types'])) {
                //     $doc_types = json_decode(stripslashes($_POST['doc_types']), true);
                // } elseif (!empty($_POST['document-type'])) {
                //     $doc_types = (array) $_POST['document-type'];
                // }

                // // Assign taxonomy
                // if (!empty($doc_types) && taxonomy_exists('document-type')) {
                //     wp_set_object_terms($post_id, $doc_types, 'document-type');
                // }

              
            wp_send_json_success([
                'post_id'            => $post_id,
                'document_file_id'   => isset($attachment_id) && !is_wp_error($attachment_id) ? $attachment_id : 0,
                'featured_image_id'  => isset($featured_image_id) && !is_wp_error($featured_image_id) ? $featured_image_id : 0,
                'featured_image_url' => get_post_thumbnail_id($post_id) ? wp_get_attachment_image_url(get_post_thumbnail_id($post_id), 'medium') : '',
            ]);
        }



            add_action('wp_ajax_get_document', 'get_document_data');

            function get_document_data() {

                flexiedu_verify_ajax_nonce();

                if (!is_user_logged_in()) {
                    wp_send_json_error(__('Unauthorized', 'flexiedu-lms-courses'));
                }

                $post_id = intval($_POST['post_id']);
                $post    = get_post($post_id);

                if (!$post || $post->post_type !== 'document') {
                    wp_send_json_error(__('Invalid document', 'flexiedu-lms-courses'));
                }

                // Taxonomy
                $terms = wp_get_object_terms($post_id, 'document_cat', [
                    'fields' => 'slugs'
                ]);

                // File
                $file_id  = get_post_meta($post_id, '_document_file', true);
                $file_url = $file_id ? wp_get_attachment_url($file_id) : '';
                $featured_image_id = get_post_thumbnail_id($post_id);
                $featured_image_url = $featured_image_id ? wp_get_attachment_image_url($featured_image_id, 'medium') : '';

                wp_send_json_success([
                    'title'          => $post->post_title,
                    'types'          => $terms,
                    'file'           => $file_url,
                    'featured_image' => $featured_image_url,
                ]);
            }


            add_action('wp_ajax_delete_document', 'handle_delete_document');

        function handle_delete_document() {

            flexiedu_verify_ajax_nonce();

            if (!is_user_logged_in()) {
                wp_send_json_error(__('Unauthorized', 'flexiedu-lms-courses'));
            }

            $user_id = get_current_user_id();
            $post_id = intval($_POST['post_id']);

            if (!$post_id) {
                wp_send_json_error(__('Invalid ID', 'flexiedu-lms-courses'));
            }

            $post = get_post($post_id);

            if (!$post || $post->post_type !== 'document') {
                wp_send_json_error(__('Invalid document', 'flexiedu-lms-courses'));
            }

            // Security: only author can delete
            if ($post->post_author != $user_id && !current_user_can('delete_others_posts')) {
                wp_send_json_error(__('Permission denied', 'flexiedu-lms-courses'));
            }

            // Delete permanently
            wp_delete_post($post_id, true);

            wp_send_json_success(__('Deleted successfully', 'flexiedu-lms-courses'));
        }