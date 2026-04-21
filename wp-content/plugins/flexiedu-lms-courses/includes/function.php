<?php 


    function flexiedu_verify_ajax_nonce($action = 'flexiedu_nonce', $field = 'nonce') {

        $nonce = '';

        if (isset($_POST[$field])) {
            $nonce = sanitize_text_field(wp_unslash($_POST[$field]));
        } elseif (isset($_REQUEST[$field])) {
            $nonce = sanitize_text_field(wp_unslash($_REQUEST[$field]));
        }

        if (!$nonce && 'nonce' === $field && isset($_POST['flexiedu_nonce'])) {
            $nonce = sanitize_text_field(wp_unslash($_POST['flexiedu_nonce']));
        }

        if (!$nonce || !wp_verify_nonce($nonce, $action)) {
            wp_send_json_error(__('Invalid request', 'flexiedu-lms-courses'));
        }
    }

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
                                    case 'base':
                                        return $base;
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

  
        add_filter('wp_nav_menu_items', 'flexi_dynamic_dash_menu', 9999, 2);

        function flexi_dynamic_dash_menu($items, $args) {
        
            if ($args->theme_location == 'menu-6') {
            
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
            
                        $menu .= '<li class="' . flexi_is_active($base) . '"><a href="'.$base.'">' . esc_html__('Dashboard', 'flexiedu-lms-courses') . '</a></li>';
                        $menu .= '<li class="' . flexi_is_active($base . '/profile') . '"><a href="'.$base.'/profile">' . esc_html__('Profile', 'flexiedu-lms-courses') . '</a></li>';
                        $menu .= '<li class="' . flexi_is_active($base . '/courses') . '"><a href="'.$base.'/courses">' . esc_html__('Course', 'flexiedu-lms-courses') . '</a></li>';
                        $menu .= '<li class="' . flexi_is_active(wp_logout_url()) . '"><a href="'.wp_logout_url().'">' . esc_html__('Logout', 'flexiedu-lms-courses') . '</a></li>';
            
                    } else {
                        $menu .= '<li><a href="#">' . esc_html__('No Org Assigned', 'flexiedu-lms-courses') . '</a></li>';
                    }
            
                } else {
            
                    $dashboard = flexi_get_user_org_url($user_id);
                    $base = flexi_get_user_org_url($user_id, 'base');
            
                    if ($dashboard) {
                        $menu .= '<li class="' . flexi_is_active($dashboard) . '"><a href="'.$dashboard.'">' . esc_html__('Dashboard', 'flexiedu-lms-courses') . '</a></li>';
                        $menu .= '<li class="' . flexi_is_active($base . '/profile') . '"><a href="'.$base.'/profile">' . esc_html__('Profile', 'flexiedu-lms-courses') . '</a></li>';
                        $menu .= '<li class="' . flexi_is_active($base . '/courses') . '"><a href="'.$base.'/courses">' . esc_html__('Course', 'flexiedu-lms-courses') . '</a></li>';
                    }
            
                    $menu .= '<li class="' . flexi_is_active(wp_logout_url()) . '"><a href="'.wp_logout_url().'">' . esc_html__('Logout', 'flexiedu-lms-courses') . '</a></li>';
                }
            
                    return $menu ?: $items;
                }else{
                    return $items; 
                }

            }else if($args->theme_location == 'menu-5'){

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
                    
                                $menu .= '<li class="' . flexi_is_active($base) . '"><a href="'.$base.'">' . esc_html__('Dashboard', 'flexiedu-lms-courses') . '</a></li>';
                                // $menu .= '<li class="' . flexi_is_active($base . '/profile') . '"><a href="'.$base.'/profile">Profile</a></li>';
                                // $menu .= '<li class="' . flexi_is_active($base . '/courses') . '"><a href="'.$base.'/courses">Course</a></li>';
                                // $menu .= '<li class="' . flexi_is_active(wp_logout_url()) . '"><a href="'.wp_logout_url().'">Logout</a></li>';
                    
                            } else {
                                $menu .= '<li class="' . flexi_is_active('#') . '"><a href="#">' . esc_html__('No Org Assigned', 'flexiedu-lms-courses') . '</a></li>';
                            }
                    
                        } else {
                    
                            $dashboard = flexi_get_user_org_url($user_id);
                            //$base = flexi_get_user_org_url($user_id, 'base');
                    
                            if ($dashboard) {
                                $menu .= '<li class="' . flexi_is_active($dashboard) . '"><a href="'.$dashboard.'">' . esc_html__('Dashboard', 'flexiedu-lms-courses') . '</a></li>';
                                // $menu .= '<li class="' . flexi_is_active($dashboard . '/profile') . '"><a href="'.$dashboard.'/profile">Profile</a></li>';
                                // $menu .= '<li class="' . flexi_is_active($dashboard . '/courses') . '"><a href="'.$dashboard.'/courses">Course</a></li>';
                            }
                    
                            // $menu .= '<li><a href="'.wp_logout_url().'">Logout</a></li>';
                        }
                    
                            return $menu ?: $items;
                        }else{
                            return $items; 
                        }

            }else{
                
                return $items;
            }
        }


        function flexi_is_active($url) {
            $current = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
            $target  = trim(parse_url($url, PHP_URL_PATH), '/');

            return ($current === $target || strpos($current, $target) !== false) ? 'current-menu-item' : '';
        }

    
        require_once( FlexiEdu_Courses_PATH . 'includes/functions/course-func.php');
        require_once( FlexiEdu_Courses_PATH . 'includes/functions/fedu-docs.php');
        require_once( FlexiEdu_Courses_PATH . 'includes/functions/live-classes.php');
        require_once( FlexiEdu_Courses_PATH . 'includes/functions/user-functions.php');
