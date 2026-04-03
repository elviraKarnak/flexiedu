<?php 
if (!defined('ABSPATH')){

    exit;
} 

if( !class_exists( 'FlexiEdu_Admin_Shortcodes' )){

    class FlexiEdu_Admin_Shortcodes {

        function __construct() {

            add_shortcode('flexiedu-admin-dashboard', [$this,'render_admin_dashboard']);
            add_shortcode('flexiedu-group-registration', [$this,'render_gp']);
        }

        function render_admin_dashboard(){
            require FlexiEdu_Courses_PATH . 'views/sup-admin-dashboard.php';
     
        }

        function render_gp(){
            require_once( FlexiEdu_Courses_PATH . 'views/group-registration.php');
        }

    }
}

