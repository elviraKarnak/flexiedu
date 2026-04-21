<?php
/*
 * Plugin Name: FlexiEdu Custom LMS
 * Plugin URI: http://elvirainfotech.com/
 * Description: Course Management Plugin for FlexiEdu
 * Author: Raihan Reza
 * Author URI: https://elvirainfotech.com/
 * Version: 1.0.0
 * Requires at least: 6.5
 * Tested up to: 6.8
 * Text Domain: flexiedu-lms-courses
 *
 * Copyright (c) 2018 Elvira Infotech
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( !class_exists( 'FlexiEdu_Courses' )){

    class FlexiEdu_Courses {

                function __construct() {
                    
                    $this->define_constants(); 

                        require_once( FlexiEdu_Courses_PATH . 'includes/function.php');

                        require_once( FlexiEdu_Courses_PATH . 'includes/event-calender.php');
                        $flexiedu_Event_calender = new FlexiEdu_Event_calender();

                        require_once( FlexiEdu_Courses_PATH . 'includes/sup-admin/inc-super-admin-shortcodes.php');
                        $FlexiEdu_Admin_Shortcodes = new FlexiEdu_Admin_Shortcodes();


                        add_action('init', [$this, 'add_rewrite_rules']);
                        add_filter('query_vars', [$this, 'register_query_vars']);
                        add_action('template_redirect', [$this, 'template_loader']);

                        add_action('load_live_classes_scripts', [$this,'flexiedu_calendar_assets']);
                        add_action('wp_enqueue_scripts', [$this, 'enqueue_admin_assets']);

                    }

                    

                    public function enqueue_admin_assets(){
                    
                        if (is_singular('lms-console')) {
                            $this->flexiedu_calendar_assets();
                            wp_enqueue_editor();
                        }
                    
                    }

                    public function add_rewrite_rules(){

                            add_rewrite_rule(
                                '^organization/([^/]+)/([^/]+)/?$',
                                'index.php?org_name=$matches[1]&org_page=$matches[2]',
                                'top'
                            );

                            add_rewrite_rule(
                                '^organization/([^/]+)/?$',
                                'index.php?org_name=$matches[1]',
                                'top'
                            );
                    }


                    public function register_query_vars($vars){
                        $vars[] = 'org_name';
                        $vars[] = 'org_page';
                        return $vars;
                    }


                    public function template_loader(){

                        $org  = get_query_var('org_name');
                        $page = get_query_var('org_page');

                        if (!$org) return;

                        // Get organization by slug
                        $organization = get_page_by_path($org, OBJECT, 'organization');

                        if (!$organization) return;

                        // Restrict access (login required)
                        if (!is_user_logged_in()) {
                            wp_redirect(home_url('/login'));
                            exit;
                        }

                        // Make org globally available
                        global $flexi_current_org;
                        $flexi_current_org = $organization;

                        $base_path   = FlexiEdu_Courses_PATH . 'templates/organization/pages/';
                        $layout_path = FlexiEdu_Courses_PATH . 'templates/organization/layout/';

                        // Resolve page file
                        if (!$page) {
                            $file = $base_path . 'dashboard.php';
                        } else {
                            $file = $base_path . sanitize_file_name($page) . '.php';
                        }

                        // If page exists → render with wrapper
                        if (file_exists($file)) {

                           get_header('dashboard'); 
                           
                           do_action('load_live_classes_scripts');
                           
                           ?>

                                <div class="dashboard-content-wrapper">

                                        <div class="learner-dashboard-left">
                                            <button type="button" class="btn-close d-block d-md-none close-btn"></button>
                                            <?php  include $layout_path . 'org-l-sidebar.php';?>
                                        </div>

                                        <div class="learner-dashboard-right">

                                            <div class="top_section">
                                                <!-- // ===== HEADER ===== -->
                                            <?php  include $layout_path . 'org-header.php';?>
                                            </div>

                                            <div class="learner-middle-container">
                                                <div class="learner-main">
                                                    <div class="dashboard-main-content">
                                                        <div class="banner_section">
                                                            <?php  include $layout_path . 'org-banner.php';?>
                                                        </div>
                                                        <!-- // ===== MAIN CONTENT ===== -->
                                                        <?php  include $file;?>
                                                    </div>    
                                                    <!-- // ===== RIGHT SIDEBAR (optional control) ===== -->
                                                    <div class="learner-sidebar-right">
                                                        <?php  include $layout_path . 'org-r-sidebar.php';?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="bottom_section">
                                            <?php  include $layout_path . 'org-footer.php';?>
                                            </div>
                                        </div>

                            <?php get_footer('dashboard');

                            exit;
                        }

                        // ===== 404 FALLBACK =====
                        global $wp_query;
                        $wp_query->set_404();
                        status_header(404);
                        get_template_part(404);
                        exit;
                    }

                    function flexiedu_calendar_assets() {

                        wp_enqueue_style(
                            'bootstrap-5',
                            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
                            [],
                            '5.3.3'
                        );

                       wp_enqueue_style(
                            'bootstrap-icons',
                             FlexiEdu_Courses_URL . 'assets/css/bootstrap-icons.min.css',
                            [],
                            1.0,
                        );

                        wp_enqueue_style(
                            'jquery.dataTables-css',
                             FlexiEdu_Courses_URL . 'assets/css/jquery.dataTables.min.css',
                            [],
                            1.0,
                        );

                        wp_enqueue_style(
                            'flexiedu-lms-css',
                             FlexiEdu_Courses_URL . 'assets/css/custom.css',
                            [],
                            time(),
                       );


                        wp_enqueue_script(
                            'bootstrap-5',
                            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
                            [],
                            '5.3.3',
                            true
                        );

                        wp_enqueue_script(
                            'fullcalendar-js',
                             FlexiEdu_Courses_URL . 'assets/js/index.global.min.js',
                            ['jquery', 'bootstrap-5'],
                            null,
                            true
                        );

                        wp_enqueue_script(
                            'jquery.dataTables-js',
                             FlexiEdu_Courses_URL . 'assets/js/jquery.dataTables.min.js',
                            ['jquery'],
                            null,
                            true
                        );

                        wp_enqueue_script(
                            'live-class-calender',
                            FlexiEdu_Courses_URL . 'assets/js/live-class-clender.js',
                            ['jquery', 'fullcalendar-js', 'bootstrap-5'],
                            time(),
                            true
                        );

                        wp_enqueue_script(
                        'flexiedu-lms-js',
                        FlexiEdu_Courses_URL . 'assets/js/custom.js',
                        ['jquery'],
                        time(),
                        true
                        );

                        wp_enqueue_script(
                            'flexiedu-live-class-submission',
                            FlexiEdu_Courses_URL . 'assets/js/live-class-submission.js',
                            ['jquery', 'bootstrap-5'],
                            time(),
                            true
                        );

                        wp_localize_script(
                            'flexiedu-lms-js',
                            'flexiedu_functions',
                            array(
                                'ajax_url' => admin_url('admin-ajax.php'),
                                 'siteURL'=> home_url(),
                                'nonce'    => wp_create_nonce('flexiedu_nonce'),
                                'is_user_logged_in' => is_user_logged_in(),
                            )
                        );

                        wp_localize_script(
                            'flexiedu-live-class-submission',
                            'flexiedu_live_class',
                            array(
                                'ajax_url' => admin_url('admin-ajax.php'),
                                'nonce'    => wp_create_nonce('flexiedu_live_class_action'),
                                'messages' => array(
                                    'processing' => __('Creating Live Class...', 'flexiedu-lms-courses'),
                                    'defaultError' => __('Something went wrong. Please try again.', 'flexiedu-lms-courses'),
                                ),
                            )
                        );


                    }




                public function define_constants(){
                    define( 'FlexiEdu_Courses_PATH', plugin_dir_path( __FILE__ ) );
                    define( 'FlexiEdu_Courses_URL', plugin_dir_url( __FILE__ ) );
                    define( 'FlexiEdu_Courses_VERSION', '1.0.0' );
                }

                public static function activate(){

                require_once FlexiEdu_Courses_PATH . 'database/install.php';

                FlexiEdu_Courses_DB::install();


                }
                
                public static function deactivate(){
                    flush_rewrite_rules();
                }

    }   
}

if( class_exists( 'FlexiEdu_Courses' ) ){
    register_activation_hook( __FILE__, array( 'FlexiEdu_Courses', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'FlexiEdu_Courses', 'deactivate' ) );
    register_uninstall_hook( __FILE__, array( 'FlexiEdu_Courses', 'uninstall' ) );

    $FlexiEdu_Courses = new FlexiEdu_Courses();
}