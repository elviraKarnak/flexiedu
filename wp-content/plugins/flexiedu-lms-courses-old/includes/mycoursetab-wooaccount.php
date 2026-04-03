<?php
if( !class_exists( 'FlexiEdu_My_Course_Dashboard' )){

    class FlexiEdu_My_Course_Dashboard {

        function __construct() {
		
            add_action( 'init', array($this,'register_my_course_endpoint'));  
            add_filter( 'query_vars', array($this,'my_course_query_vars')); 
            add_filter( 'woocommerce_account_menu_items',  array($this,'my_course_query_tab'));
            add_action( 'woocommerce_account_my-courses-lms_endpoint',  array($this,'my_course_query_page_content' ));


        }

                function register_my_course_endpoint() {
                    add_rewrite_endpoint( 'my-courses-lms', EP_ROOT | EP_PAGES );
                }

                function my_course_query_vars( $vars ) {
                    $vars[] = 'My Courses';
                    return $vars;
                }
                function my_course_query_tab( $items ) {
                    $items['my-courses-lms'] = 'My Courses';
                    return $items;
                }

                function my_course_query_page_content(){
                    require_once(FlexiEdu_Courses_OLD_PATH ."views/my-course-view.php");
                }

    }
}