<?php
/*
 * Plugin Name: FlexiEdu Courses
 * Plugin URI: http://elvirainfotech.com/
 * Description: Course Management Plugin for FlexiEdu
 * Author: Raihan Reza
 * Author URI: https://elvirainfotech.com/
 * Version: 1.0.0
 * Requires at least: 6.5
 * Tested up to: 6.8
 * Text Domain: flexiedu-courses
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

        require_once( FlexiEdu_Courses_PATH . 'includes/posttype-taxanomy.php' );
        $FlexiEdu_Courses_Posttype_Taxonomy_Module = new FlexiEdu_Courses_Posttype_Taxonomy_Module();


        }

        public function define_constants(){
			define( 'FlexiEdu_Courses_PATH', plugin_dir_path( __FILE__ ) );
			define( 'FlexiEdu_Courses_URL', plugin_dir_url( __FILE__ ) );
			define( 'FlexiEdu_Courses_VERSION', '1.0.0' );
		}

        public static function activate(){

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