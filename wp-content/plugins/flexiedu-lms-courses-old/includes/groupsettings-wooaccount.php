<?php
if( !class_exists( 'FlexiEdu_Group_Settings_Dashboard' )){

    class FlexiEdu_Group_Settings_Dashboard {

        function __construct() {
		
            add_action( 'init', array($this,'register_group_settings_endpoint'));  
            add_filter( 'query_vars', array($this,'my_gs_query_vars')); 
            add_filter( 'woocommerce_account_menu_items',  array($this,'my_gs_query_tab'));
            add_action( 'woocommerce_account_group-settings_endpoint',  array($this,'my_gs_query_page_content' ));


        }

                function register_group_settings_endpoint() {
                    add_rewrite_endpoint( 'group-settings', EP_ROOT | EP_PAGES );
                }

                function my_gs_query_vars( $vars ) {
                    $vars[] = 'group-settings';
                    return $vars;
                }

               function my_gs_query_tab( $items ) {

                    if ( is_user_logged_in() ) {

                        $user = wp_get_current_user();

                        if ( in_array( 'administrator', $user->roles ) || 
                            in_array( 'group_leader', $user->roles ) ) {

                            $items['group-settings'] = 'Group Settings';
                        }
                    }

                    return $items;
                }   

                function my_gs_query_page_content(){
                    require_once(FlexiEdu_Courses_OLD_PATH ."views/group-settings-view.php");
                }

    }
}