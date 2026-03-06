<?php
/**
 * Accent color style template
 *
 * Dynamic styling for instructor dashboard for selected accent color.
 *
 * @package LearnDash\Instructor_Role
 *
 * cspell:ignore hndle // ignoring misspelled words that we can't change now.
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

#adminmenu a:hover,#adminmenu li.menu-top>a:focus,#adminmenu .wp-submenu a:hover,#adminmenu .wp-submenu a:focus {
	color: <?php echo esc_attr( $accent ); ?>
}

input[type="text"]:focus,input[type="password"]:focus,input[type="color"]:focus,input[type="date"]:focus,input[type="datetime"]:focus,input[type="datetime-local"]:focus,input[type="email"]:focus,input[type="month"]:focus,input[type="number"]:focus,input[type="search"]:focus,input[type="tel"]:focus,input[type="time"]:focus,input[type="url"]:focus,input[type="week"]:focus,input[type="checkbox"]:focus,input[type="radio"]:focus,select:focus,textarea:focus {
	border-color: <?php echo esc_attr( $accent ); ?>;
}

.ld-node-header .toggle svg path,.ld-expand-collapse-all svg path {
	stroke: <?php echo esc_attr( $accent ); ?> !important
}

.ld-node-header h2 button svg,.ld-node-header h3 button svg {
	fill: <?php echo esc_attr( $accent ); ?> !important
}

.ld__builder--app input[type=submit].is-primary,.components-button.is-primary,#elementor-switch-mode-button {
	background-color: <?php echo esc_attr( $accent ); ?> !important;
}

.components-button.is-tertiary {
	color: <?php echo esc_attr( $accent ); ?> !important
}

.components-button.is-secondary:hover:not(:disabled),.components-button.is-tertiary:hover:not(:disabled) {
	color: <?php echo esc_attr( $accent ); ?>;
}

#poststuff #learndash-course-display-content-settings .inside .ld-radio-input-wrapper .ld-radio-input:checked+.ld-radio-input__label:before,#poststuff #learndash-course-access-settings .inside .ld-radio-input-wrapper .ld-radio-input:checked+.ld-radio-input__label:before,#poststuff #learndash-course-navigation-settings .inside .ld-radio-input-wrapper .ld-radio-input:checked+.ld-radio-input__label:before,#poststuff #sfwd-courses .inside .ld-radio-input-wrapper .ld-radio-input:checked+.ld-radio-input__label:before,#poststuff #learndash-course-grid-meta-box .inside .ld-radio-input-wrapper .ld-radio-input:checked+.ld-radio-input__label:before,#poststuff #learndash-quiz-access-settings .inside .ld-radio-input-wrapper .ld-radio-input:checked+.ld-radio-input__label:before,#poststuff #learndash-quiz-progress-settings .inside .ld-radio-input-wrapper .ld-radio-input:checked+.ld-radio-input__label:before,#poststuff #learndash-quiz-display-content-settings .inside .ld-radio-input-wrapper .ld-radio-input:checked+.ld-radio-input__label:before,#poststuff #learndash-quiz-results-options .inside .ld-radio-input-wrapper .ld-radio-input:checked+.ld-radio-input__label:before,#poststuff #learndash-quiz-admin-data-handling-settings .inside .ld-radio-input-wrapper .ld-radio-input:checked+.ld-radio-input__label:before {
	border: 5px solid <?php echo esc_attr( $accent ); ?>
}
#sfwd-header .ld-global-header .ld-tab-buttons .is-primary {
	color: <?php echo esc_attr( $accent ); ?> !important;
}

#side-sortables #sfwd-course-lessons .inside .ld__builder-sidebar-header a,#side-sortables #sfwd-course-topics .inside .ld__builder-sidebar-header a,#side-sortables #sfwd-course-quizzes .inside .ld__builder-sidebar-header a,#side-sortables #sfwd-quiz-questions .inside .ld__builder-sidebar-header a {
	color: <?php echo esc_attr( $accent ); ?>
}

#side-sortables #sfwd-course-lessons .inside .ld__builder-sidebar-refresh,#side-sortables #sfwd-course-topics .inside .ld__builder-sidebar-refresh,#side-sortables #sfwd-course-quizzes .inside .ld__builder-sidebar-refresh,#side-sortables #sfwd-quiz-questions .inside .ld__builder-sidebar-refresh {
	color: <?php echo esc_attr( $accent ); ?>
}

#learndash_course_builder .inside .ld__builder--app .ld__builder--footer .ld__builder--final-quizzes .ld__builder--new-entities a svg,#learndash_quiz_builder .inside .ld__builder--app .ld__builder--footer .ld__builder--final-quizzes .ld__builder--new-entities a svg {
	fill: <?php echo esc_attr( $accent ); ?>
}
#learndash_course_builder .inside .ld__builder--app .ld__builder--content .ld__builder--new-entities a svg,#learndash_quiz_builder .inside .ld__builder--app .ld__builder--content .ld__builder--new-entities a svg {
	fill: <?php echo esc_attr( $accent ); ?>
}
.irb-btn {
	background-color: <?php echo esc_attr( $accent ); ?> !important;
}

.nav-tab-wrapper .nav-tab.nav-tab-active {
	color: <?php echo esc_attr( $accent ); ?>;
}

.folded #adminmenu li.menu-top .wp-submenu>li.wp-submenu-head {
	background-color: <?php echo esc_attr( $accent ); ?>
}

.irb-icon-add:before {
	color: <?php echo esc_attr( $accent ); ?>
}

.irb-icon-play:before {
	color: <?php echo esc_attr( $accent ); ?>
}

#wdm_report_tbl a,#reports_table_div a,.ir-payout-transactions-container a,.ir-assignments-table a {
	color: <?php echo esc_attr( $accent ); ?>
}

#learndash_course_builder .inside .ld__builder--app .ld__builder--content .ld__builder--new-entities a,#learndash_quiz_builder .inside .ld__builder--app .ld__builder--content .ld__builder--new-entities a {
	color: <?php echo esc_attr( $accent ); ?>
}

#learndash_course_builder .inside .ld__builder--app .ld__builder--footer .ld__builder--final-quizzes .ld__builder--new-entities a,#learndash_quiz_builder .inside .ld__builder--app .ld__builder--footer .ld__builder--final-quizzes .ld__builder--new-entities a {
	color: <?php echo esc_attr( $accent ); ?>
}

a{
	color: <?php echo esc_attr( $accent ); ?>;
}

body .global-new-entity-button{
	background: <?php echo esc_attr( $accent ); ?>;
}

.wp-core-ui .button, .wp-core-ui .button-secondary{
	color: <?php echo esc_attr( $accent ); ?>;
	border-color: <?php echo esc_attr( $accent ); ?>;
}

#adminmenu li.menu-top:hover, #adminmenu li.opensub>a.menu-top, #adminmenu li>a.menu-top:focus {
	color: <?php echo esc_attr( $accent ); ?>;
}


#adminmenuback, #adminmenuwrap, #adminmenu, #adminmenu .wp-submenu {
background-color: <?php echo esc_attr( $primary ); ?>;
}

#adminmenu .wp-submenu {
background-color: <?php echo esc_attr( $secondary ); ?>;
}

#adminmenu .wp-submenu li.current a, #adminmenu .wp-submenu li.current a:hover {
background-color: <?php echo esc_attr( $tertiary ); ?>;
}

#adminmenu .wp-submenu a, #adminmenu a, #adminmenu a.menu-top, #adminmenu li.current a.menu-top, #adminmenu li.menu-top:hover, #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu {
color: <?php echo esc_attr( $text_color_1 ); ?>;
}

#adminmenu .wp-submenu li.current a, #adminmenu .wp-submenu li.current a:hover{
	color: <?php echo esc_attr( $text_color_1 ); ?>;
}

#adminmenu div.wp-menu-image:before {
color: <?php echo esc_attr( $text_color_1 ); ?> !important;
}

.irb-overview-wrap .irb-overview .irb-tiles-wrap .irb-tile .irb-tile-header {
background-color: <?php echo esc_attr( $primary ); ?>;
}

.irb-overview-wrap .irb-overview .irb-tiles-wrap .irb-tile .irb-tile-header .irb-header-text, .irb-overview-wrap .irb-overview .irb-tiles-wrap .irb-tile .irb-tile-header .irb-header-icon{
	color: <?php echo esc_attr( $text_color_1 ); ?>;
}

.is-fullscreen-mode #sfwd-header{
	left: 0;
}

.is-fullscreen-mode #sfwd-header .ld-tab-buttons{
	background-color: <?php echo esc_attr( $primary ); ?>;
}

.is-fullscreen-mode #sfwd-header .ld-global-header .ld-tab-buttons button.is-primary, .is-fullscreen-mode #sfwd-header .ld-global-header .ld-tab-buttons button, .is-fullscreen-mode #sfwd-header .ld-global-header .ld-tab-buttons button:after{
	color: <?php echo esc_attr( $text_color_1 ); ?> !important;
}

.interface-interface-skeleton__content .postbox-header {
background-color: <?php echo esc_attr( $primary ); ?>;
color: <?php echo esc_attr( $text_color_1 ); ?>;
}

#learndash_course_builder .inside .ld__builder--app .ld-entity-builder-header, #learndash_quiz_builder .inside .ld__builder--app .ld-entity-builder-header{
	background-color: <?php echo esc_attr( $primary ); ?>;
}

#learndash_course_builder .inside .ld__builder--app .ld-entity-builder-header h2, #learndash_quiz_builder .inside .ld__builder--app .ld-entity-builder-header h2, .ld-entity-builder-header .ld-undo-button:active, .ld-entity-builder-header .ld-undo-button:hover, .ld-entity-builder-header .ld-expand-collapse-all-button:active, .ld-entity-builder-header .ld-expand-collapse-all-button:hover, .ld-entity-builder-header .ld-undo-button, .ld-entity-builder-header .ld-expand-collapse-all-button{
	color: <?php echo esc_attr( $text_color_1 ); ?> !important;
}

.ld-entity-builder-header .ld-undo-button svg, .ld-entity-builder-header .ld-expand-collapse-all-button svg{
	fill: <?php echo esc_attr( $text_color_1 ); ?> !important;
}

.ld-node-header .toggle svg path, .ld-expand-collapse-all svg path{
	stroke: <?php echo esc_attr( $text_color_1 ); ?> !important;
}

#poststuff .ld_settings_postbox .postbox-header h2.hndle, #poststuff .ld_settings_postbox .postbox-header .ld-metabox-description p{
	color: <?php echo esc_attr( $text_color_1 ); ?> !important;
}

#wdm_report_tbl th, #reports_table_div th, .ir-payout-transactions-container th, .ir-assignments-table th, .ir-manual-commission-logs-container th, .ir-tabs-nav li:not(.active) a{
	color: <?php echo esc_attr( $text_color_1 ); ?> !important;
	background-color: <?php echo esc_attr( $primary ); ?>;
}

.footable > tfoot > tr > th, .footable > tfoot > tr > td, .footable > thead > tr > th, .footable > thead > tr > td {
	background-color: <?php echo esc_attr( $primary ); ?>;
	color: <?php echo esc_attr( $text_color_1 ); ?> !important;
}

#wdm_report_tbl .ir-payout-transaction-table th, #reports_table_div .ir-payout-transaction-table th, .ir-payout-transactions-container .ir-payout-transaction-table th, .ir-assignments-table .ir-payout-transaction-table th{
	color: <?php echo esc_attr( $text_color_1 ); ?> !important;
}

.wl8qcn-email-form #instructor_email_template_variable tbody code{
	background-color: <?php echo esc_attr( $primary ); ?>;
	color: <?php echo esc_attr( $text_color_1 ); ?> !important;
}

.wp-submenu a:hover {
	color: <?php echo esc_attr( $text_color_2 ); ?> !important;
}

div.ir-manual-commission-logs-container button.ir-create-csv-btn {
	background-color: <?php echo esc_attr( $accent ); ?>;
	border-color: <?php echo esc_attr( $accent ); ?>;
}

div.ir-manual-commission-logs-container button.ir-create-csv-btn:hover {
	background-color: <?php echo esc_attr( $primary ); ?> !important;
	border-color: <?php echo esc_attr( $primary ); ?> !important;
}
