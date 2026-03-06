<?php
/**
 * Accent color style template for Layout 2
 *
 * Dynamic styling for instructor dashboard.
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
#adminmenu .wp-submenu, .wp-menu-open, #adminmenu li.menu-top.current, #adminmenu li.menu-top.current:hover,
#adminmenu li.menu-top.wp-has-current-submenu:hover{
	background-color: <?php echo esc_attr( $secondary ); ?>
}

#adminmenu .wp-submenu a:hover, #adminmenu .wp-submenu a:focus{
	color: <?php echo esc_attr( $accent ); ?>
}

#adminmenu li:hover div.wp-menu-image:before{
	color: <?php echo esc_attr( $accent ); ?>
}

li.menu-top:hover .wp-menu-name{
	color: <?php echo esc_attr( $accent ); ?>
}

li.wp-has-current-submenu.menu-top:hover .wp-menu-name, #ir-collapse-menu-item > div #collapse-button{
	color: <?php echo esc_attr( $text_color_2 ); ?>;
}

#ir-collapse-menu-item > div > span{
	color: <?php echo esc_attr( $text_color_2 ); ?>;
}

.wp-menu-name, .wp-menu-image, #adminmenu .wp-submenu a, #adminmenu .wp-submenu li.current a,
#adminmenu .opensub .wp-submenu li.current a{
	color: <?php echo esc_attr( $text_color_2 ); ?>;
}

#adminmenu .wp-has-current-submenu div.wp-menu-image:before, #adminmenu .current div.wp-menu-image:before,
#adminmenu li.wp-has-current-submenu:hover div.wp-menu-image:before{
	color: <?php echo esc_attr( $text_color_2 ); ?>;
}
#adminmenu li.wp-has-current-submenu a:focus div.wp-menu-image:before{
	color: <?php echo esc_attr( $text_color_2 ); ?>;
}
#adminmenu .wp-submenu li.current a:hover, #adminmenu .wp-submenu li.current a:focus{
	color: <?php echo esc_attr( $text_color_2 ); ?>;
}

#adminmenu div.wp-menu-image:before{
	color: <?php echo esc_attr( $text_color_2 ); ?>;
}

#adminmenu li a:focus div.wp-menu-image:before{
	color: <?php echo esc_attr( $accent ); ?>
}

#adminmenu .wp-submenu li.current, #adminmenuback, #adminmenuwrap, #adminmenu{
	background-color: <?php echo esc_attr( $tertiary ); ?>
}

#adminmenu .wp-not-current-submenu.wp-has-submenu:hover > a{
	background-color: <?php echo esc_attr( $secondary ); ?>
}


body{
	background-color: <?php echo esc_attr( $background ); ?>
}

@media screen and (max-width: 1024px){
div#ir-primary-navigation ul#ir-primary-menu li.menu-item a, div#ir-primary-navigation ul#ir-primary-menu li.menu-item, div#ir-primary-navigation ul#ir-primary-menu li.page_item a, div#ir-primary-navigation ul#ir-primary-menu li.page_item {
	background-color: <?php echo esc_attr( $background ); ?>;
}
div#ir-primary-navigation ul#ir-primary-menu:after {
	border-color: transparent transparent <?php echo esc_attr( $background ); ?> transparent;
}

div#ir-primary-navigation ul#ir-primary-menu li .sub-menu li {
	background-color: <?php echo esc_attr( $background ); ?>;
}

}

.irbn-overview-wrap .irbn-overview .irbn-charts .irbn-tile .irbn-tile-header,
.irbn-overview-wrap .irbn-overview .irbn-sub .irbn-tile .irbn-tile-header,
.irbn-overview-wrap .irbn-overview .irbn-sub .ir-assignment-table-header th,.irbn-overview-wrap .irbn-overview .irbn-tiles-wrap .irbn-tile .irbn-tile-right{
	color: <?php echo esc_attr( $text_color_1 ); ?>;
}

.irbn-overview-wrap .irbn-overview .irbn-tiles-wrap .irbn-tile .irbn-tile-left .irbn-icon i{
	color: <?php echo esc_attr( $primary ); ?>
}

div#ir-primary-navigation .wdm-mob-menu .dashicons{
	color: <?php echo esc_attr( $primary ); ?>
}

div#ir-submissions-content .paginate_button{
	color: <?php echo esc_attr( $accent ); ?> !important;
}

div#ir-submissions-content .paginate_button.current{
	background-color: <?php echo esc_attr( $accent ); ?> !important;
}

/* Commenting since don't know where else is this code being used. */

/* div#wdm_report_tbl_paginate .paginate_button{
	color: <?php echo esc_attr( $accent ); ?> !important;
}

div#wdm_report_tbl_paginate .paginate_button.current{
	background-color: <?php echo esc_attr( $accent ); ?> !important;
} */

.dataTables_wrapper .dataTables_paginate .paginate_button:hover{
	color: <?php echo esc_attr( $accent ); ?> !important;
}

body.post-type-sfwd-courses #sfwd-header div.ld-tab-buttons, body.post-type-sfwd-lessons #sfwd-header div.ld-tab-buttons, body.post-type-sfwd-topic #sfwd-header div.ld-tab-buttons, body.post-type-sfwd-quiz #sfwd-header div.ld-tab-buttons, body.post-type-sfwd-question #sfwd-header div.ld-tab-buttons, body.post-type-sfwd-certificates #sfwd-header div.ld-tab-buttons, body.post-type-groups #sfwd-header div.ld-tab-buttons, body.post-type-ld-exam #sfwd-header div.ld-tab-buttons, body.post-type-sfwd-assignment #sfwd-header div.ld-tab-buttons, body.post-type-sfwd-essays #sfwd-header div.ld-tab-buttons {
	background-color: <?php echo esc_attr( $tertiary ); ?>;
}

body.post-type-sfwd-courses #sfwd-header div.ld-tab-buttons button, body.post-type-sfwd-lessons #sfwd-header div.ld-tab-buttons button, body.post-type-sfwd-topic #sfwd-header div.ld-tab-buttons button, body.post-type-sfwd-quiz #sfwd-header div.ld-tab-buttons button, body.post-type-sfwd-question #sfwd-header div.ld-tab-buttons button, body.post-type-sfwd-certificates #sfwd-header div.ld-tab-buttons button, body.post-type-groups #sfwd-header div.ld-tab-buttons button, body.post-type-ld-exam #sfwd-header div.ld-tab-buttons button, body.post-type-sfwd-assignment #sfwd-header div.ld-tab-buttons button, body.post-type-sfwd-essays #sfwd-header div.ld-tab-buttons button {
	color: <?php echo esc_attr( $text_color_2 ); ?> !important;
}

button#ir-collapse-button-mobile .dashicons:before {
	color: <?php echo esc_attr( $text_color_2 ); ?>;
}
