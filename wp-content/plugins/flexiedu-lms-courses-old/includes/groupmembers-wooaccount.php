<?php
if( !class_exists( 'FlexiEdu_Group_Members_Dashboard' )){

    class FlexiEdu_Group_Members_Dashboard {

        function __construct() {
		
            add_action( 'init', array($this,'register_group_members_endpoint'));  
            add_filter( 'query_vars', array($this,'my_gm_query_vars')); 
            add_filter( 'woocommerce_account_menu_items',  array($this,'my_gm_query_tab'));
            add_action( 'woocommerce_account_group-members_endpoint',  array($this,'my_gm_query_page_content' ));

        }

                function register_group_members_endpoint() {
                    add_rewrite_endpoint( 'group-members', EP_ROOT | EP_PAGES );
                }

                function my_gm_query_vars( $vars ) {
                    $vars[] = 'group-members';
                    return $vars;
                }

               function my_gm_query_tab( $items ) {

                    if ( is_user_logged_in() ) {

                        $user = wp_get_current_user();

                        if ( in_array( 'administrator', $user->roles ) || 
                            in_array( 'group_leader', $user->roles ) ) {

                            $items['group-members'] = 'Group Members';
                        }
                    }

                    return $items;
                }   

                function my_gm_query_page_content(){
                    require_once(FlexiEdu_Courses_OLD_PATH ."views/group-members-view.php");
                }

    }
}