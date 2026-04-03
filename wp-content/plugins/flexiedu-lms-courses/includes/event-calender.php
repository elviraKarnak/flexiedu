<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( !class_exists( 'FlexiEdu_Event_calender' )){

    class FlexiEdu_Event_calender {

        function __construct() {

            add_shortcode('flexiedu-live-classes', array($this,'flexiedu_live_classes_cb'));
                    
        }


        function flexiedu_live_classes_cb(){

            ob_start();

            do_action('load_live_classes_scripts');
            
            ?>

            <div id='live-classes-calendar'></div>

     


         <?php   return ob_get_clean();
        }   


    }


}