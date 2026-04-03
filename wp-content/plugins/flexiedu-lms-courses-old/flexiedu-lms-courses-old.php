<?php
/*
 * Plugin Name: FlexiEdu Custom OLD LMS
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

if( !class_exists( 'FlexiEdu_Courses_OLD' )){

    class FlexiEdu_Courses_OLD {

        function __construct() {
            
			$this->define_constants(); 

                require_once( FlexiEdu_Courses_OLD_PATH . 'includes/course-functions.php' );

                require_once( FlexiEdu_Courses_OLD_PATH . 'includes/mycoursetab-wooaccount.php' );
                $FlexiEdu_My_Course_Dashboard = new FlexiEdu_My_Course_Dashboard();

                require_once( FlexiEdu_Courses_OLD_PATH . 'includes/groupmembers-wooaccount.php' );
                $FlexiEdu_Group_Members_Dashboard = new FlexiEdu_Group_Members_Dashboard();

                require_once( FlexiEdu_Courses_OLD_PATH . 'includes/groupsettings-wooaccount.php' );
                $FlexiEdu_Group_Settings_Dashboard = new FlexiEdu_Group_Settings_Dashboard();

                add_filter( 'woocommerce_account_menu_items',  array($this,'my_course_query_tab_order'));
                add_action( 'woocommerce_thankyou',  array($this,'redirect_to_my_courses'), 20);
                add_action( 'template_redirect', array($this,'redirect_login_to_my_account'));

            }


            function my_course_query_tab_order( $items ) {

                $ordered = [];

                // Define preferred order
                $priority = [
                    'dashboard',
                    'my-courses-lms',
                    'group-members',
                    'group-settings', // <-- include your new tab here
                    'orders',
                    'downloads',
                    'edit-address',
                    'payment-methods',
                    'edit-account',
                    'customer-logout'
                ];

                foreach ( $priority as $key ) {
                    if ( isset( $items[$key] ) ) {
                        $ordered[$key] = $items[$key];
                    }
                }

                // Add any remaining items automatically (future-safe)
                foreach ( $items as $key => $value ) {
                    if ( ! isset( $ordered[$key] ) ) {
                        $ordered[$key] = $value;
                    }
                }

                return $ordered;
            }
                 
            function redirect_to_my_courses( $order_id ) {

                        if ( ! $order_id ) {
                            return;
                        }

                        // Only for logged-in users
                        if ( ! is_user_logged_in() ) {
                            return;
                        }

                        $order = wc_get_order( $order_id );

                        // Make sure order is valid & paid
                        if ( ! $order || ! $order->has_status( [ 'processing', 'completed' ] ) ) {
                            return;
                        }

                        // Prevent redirect loop
                        if ( is_wc_endpoint_url( 'order-received' ) ) {
                                wp_safe_redirect( site_url( '/student-profile/my-courses-lms/' ) );
                            exit;
                        }

                    }

                        function redirect_login_to_my_account() {

                            // Target WooCommerce My Account page
                            if ( is_account_page() && ! is_user_logged_in() ) {

                                $login_url = site_url( '/login/' );

                                // Redirect back to My Account after login
                                $redirect_url = add_query_arg(
                                    'redirect_to',
                                    urlencode( wc_get_page_permalink( 'myaccount' ) ),
                                    $login_url
                                );

                                wp_safe_redirect( $redirect_url );
                                exit;
                            }

                        }


                public function define_constants(){
                    define( 'FlexiEdu_Courses_OLD_PATH', plugin_dir_path( __FILE__ ) );
                    define( 'FlexiEdu_Courses_OLD_URL', plugin_dir_url( __FILE__ ) );
                    define( 'FlexiEdu_Courses_OLD_VERSION', '1.0.0' );
                }

                public static function activate(){

                }
                

                public static function deactivate(){
                    flush_rewrite_rules();
                }

    }   
}

if( class_exists( 'FlexiEdu_Courses_OLD' ) ){
    register_activation_hook( __FILE__, array( 'FlexiEdu_Courses_OLD', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'FlexiEdu_Courses_OLD', 'deactivate' ) );
    register_uninstall_hook( __FILE__, array( 'FlexiEdu_Courses_OLD', 'uninstall' ) );

    $FlexiEdu_Courses_OLD = new FlexiEdu_Courses_OLD();
}