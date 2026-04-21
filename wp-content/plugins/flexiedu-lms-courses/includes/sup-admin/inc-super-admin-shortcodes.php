<?php 
if (!defined('ABSPATH')){

    exit;
} 

if( !class_exists( 'FlexiEdu_Admin_Shortcodes' )){

    class FlexiEdu_Admin_Shortcodes {

        function __construct() {

            add_shortcode('flexiedu-admin-dashboard', [$this,'render_admin_dashboard']);
            add_shortcode('flexiedu-group-registration', [$this,'render_gp']);
            add_shortcode('flexiedu-sa-documents', [$this,'render_documents']);
            add_shortcode('flexiedu-org-features', [$this,'render_org_features']);
        }

        function render_admin_dashboard(){
            require FlexiEdu_Courses_PATH . 'views/sup-admin-dashboard.php';
        }

        function render_gp(){
            require_once( FlexiEdu_Courses_PATH . 'views/group-registration.php');
        }

         function render_documents(){
            require_once( FlexiEdu_Courses_PATH . 'views/sa-documents.php');
        }

        function render_org_features(){
            require_once( FlexiEdu_Courses_PATH . 'views/org-features.php');
        }

    }
}