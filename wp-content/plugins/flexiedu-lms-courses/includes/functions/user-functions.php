<?php 

add_action('init', 'flexiedu_fix_org_post_type_caps', 20);


    function flexiedu_fix_org_post_type_caps() {

        global $wp_post_types;

        if (!isset($wp_post_types['organization'])) return;

        $wp_post_types['organization']->capability_type = ['organization', 'organizations'];
        $wp_post_types['organization']->map_meta_cap = true;

    }

add_action('init', 'flexiedu_add_org_caps_to_group_leader');

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


    add_action('save_post_organization', 'flexiedu_sync_org_author_with_user_meta');

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


        add_action('admin_init', 'flexiedu_instructor_admin_redirect');
        add_action('template_redirect', 'flexiedu_instructor_dashboard_redirect', 1);
        
        function flexiedu_instructor_admin_redirect() {
            // Only logged-in users
            if (!is_user_logged_in()) return;
            
            // Check if we're on the specific admin page
            if (!isset($_GET['page']) || $_GET['page'] !== 'ir_instructor_overview') return;
            
            // Check if we're in admin area
            if (!is_admin()) return;
            
            $user = wp_get_current_user();
            
            // Only for organizer role
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

            flexiedu_verify_ajax_nonce();

            if (!is_user_logged_in()) {
                wp_send_json_error(__('Not logged in', 'flexiedu-lms-courses'));
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
                    wp_send_json_error(__('Invalid email', 'flexiedu-lms-courses'));
                }

                if ($email !== $current_user->user_email && email_exists($email)) {
                    wp_send_json_error(__('Email already in use', 'flexiedu-lms-courses'));
                }

                wp_update_user([
                    'ID' => $user_id,
                    'user_email' => $email
                ]);
            }

            // Password
            if (!empty($_POST['new_password'])) {

                if ($_POST['new_password'] !== $_POST['confirm_password']) {
                    wp_send_json_error(__('Passwords do not match', 'flexiedu-lms-courses'));
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
                    wp_send_json_error(__('Image upload failed', 'flexiedu-lms-courses'));
                }
            }

            wp_send_json_success(__('Profile updated successfully', 'flexiedu-lms-courses'));
        }


        /**
         * Handle Registration
         */
        
        add_action('wp_ajax_flexi_register_user', 'flexi_register_user');
        add_action('wp_ajax_nopriv_flexi_register_user', 'flexi_register_user');

        function flexi_register_user(){

            flexiedu_verify_ajax_nonce('flexiedu_register_action', 'flexiedu_nonce');


            $fname = sanitize_text_field($_POST['first_name']);
            $lname = sanitize_text_field($_POST['last_name']);
            $org_name = sanitize_text_field($_POST['org_name']);
            $username = sanitize_user($_POST['username']);
            $email    = sanitize_email($_POST['email']);
            $password = $_POST['password'];
            $bio      = sanitize_textarea_field($_POST['bio']);

            // Validation
            if (username_exists($username)) {
                wp_send_json_error(__('Username already exists', 'flexiedu-lms-courses'));
            }

            if (email_exists($email)) {
                wp_send_json_error(__('Email already registered', 'flexiedu-lms-courses'));
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

            update_post_meta($org_id, 'add_documents_oc', '1');
            update_post_meta($org_id, 'add_course_oc', '1');
            update_post_meta($org_id, 'add_live_classes_oc', '1');
            update_post_meta($org_id, 'pro_panel_oc', '1');
            update_post_meta($org_id, 'accept_application_oc', '1');
            update_post_meta($org_id, 'grade_book_oc', '1');

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
                'message' => __('Registration successful', 'flexiedu-lms-courses'),
                'redirect' => $redirect
            ]);
        }

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
    

        add_action('template_redirect', function () {
            $request = trim($_SERVER['REQUEST_URI'], '/');

                // Redirect only exact /lms-console
                if ($request === 'lms-console') {

                if(!is_user_logged_in() || !current_user_can('manage_options') ){
                    wp_redirect(home_url('/login'));
                    exit;
                }
                if(!current_user_can('manage_options') ){
                    wp_redirect(home_url());
                    exit;
                }
                
                wp_redirect(home_url('lms-console/dashboard'));
                exit;
            }
        });


        add_action('wp_ajax_get_org_features', 'get_org_features');
        add_action('wp_ajax_nopriv_get_org_features', 'get_org_features');

        function get_org_features() {

                    $org_id = intval($_POST['org_id']);

                    $fields = acf_get_fields('group_69e622fb6a175');
                    
                    if ($fields) {
                    ?>

                    <!-- Features -->
                    <form id="featureBox">

                        <input type="hidden" name="org_id" id="org_id" value="<?php echo $org_id; ?>">
                        <input type="hidden" name="action" value="update_org_features">
                        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('flexiedu_nonce'); ?>">


                        <div class="row">
                        <?php 
                        foreach ($fields as $field) {
                            
                            $slug = $field['name']; // e.g. add_documents
                            $value = get_field($slug, $org_id); // true or false
                            
                        ?>
                        
                            <div class="col-md-4 mb-3">
                                <div class="card p-3">
                                    <label>
                                        <input type="checkbox" name="<?php echo $slug; ?>" value="<?php echo $value; ?>" <?php echo $value ? 'checked' : ''; ?>> <?php echo $field['label']; ?>
                                    </label>
                                </div>
                            </div>

                        <?php } ?>
            

                        </div>
                        
                        <button id="saveFeatures" class="btn btn-primary mt-3">
                            Save Features
                        </button>

                    </div>

                </form>
                        <?php }

            exit;
                 
                
        }

           
                add_action('wp_ajax_update_org_features', 'update_org_features_cb');

                function update_org_features_cb() {

                    flexiedu_verify_ajax_nonce('flexiedu_nonce', 'nonce');

                    $data = wp_unslash($_POST);

                    $org_id = isset($data['org_id']) ? intval($data['org_id']) : 0;

                    if (!$org_id) {
                        wp_send_json_error('Invalid org_id');
                    }

                    if (!current_user_can('administrator', $org_id)) {
                        wp_send_json_error('Permission denied');
                    }

                    // remove unwanted keys
                    unset($data['action'], $data['nonce'], $data['org_id']);

                    try {

                        foreach ($data as $feature => $value) {

                            // sanitize value
                            $value = $value ? 1 : 0;

                            // ✅ Use ACF if these are ACF fields
                            update_field($feature, $value, $org_id);

                            // OR if not ACF:
                            // update_post_meta($org_id, $feature, $value);
                        }

                        wp_send_json_success(__('Features updated', 'flexiedu-lms-courses'));

                    } catch (Exception $e) {

                        wp_send_json_error(__('Failed to update features', 'flexiedu-lms-courses'));
                    }
                }
       
    ?>