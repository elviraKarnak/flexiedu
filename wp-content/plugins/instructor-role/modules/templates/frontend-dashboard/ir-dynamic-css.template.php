<?php
/**
 * Frontend Dashboard Dynamic CSS Template.
 *
 * @since 5.0.0
 *
 * @param string $primary
 * @param string $accent
 * @param string $background
 * @param string $headings
 * @param string $text
 * @param string $border
 * @param string $side_bg
 * @param string $side_mt
 * @param string $text_light
 * @param string $text_ex_light
 * @param string $text_primary_btn
 * @param string $font_family
 * @param string $font_size
 *
 * @package LearnDash\Instructor_Role
 *
 * cspell:ignore coures // ignoring misspelled words that we can't change now.
 */

defined( 'ABSPATH' ) || exit;
?>

.wp-block-instructor-role-wisdm-tabs * {
	font-family: <?php echo esc_attr( $font_family ); ?> !important
}
.wp-block-instructor-role-wisdm-tabs > .tab-labels .tab-label, .ir-toggle-sidebar{
	color: <?php echo esc_attr( $side_mt ); ?> !important
}
.wp-block-instructor-role-wisdm-tabs .tab-labels .tab-label.active, .overview svg, .wp-block-instructor-role-wisdm-tabs > .tab-labels .tab-label:hover{
	color: <?php echo esc_attr( $primary ); ?> !important
}
.ir-text-colour{
	color: <?php echo esc_attr( $text ); ?> !important
}
/*Nikhil Styling added ir-primary-bg*/
#ir-coures-progress-donut span.apexcharts-legend-marker:after, #ir-learner-progress-donut span.apexcharts-legend-marker:after, .primary-bg, .ir-primary-bg, .mantine-Pagination-item[data-active], .mantine-Stepper-stepIcon[data-completed], .wp-block-instructor-role-wisdm-tabs .mantine-Pagination-item:hover, .wp-block-instructor-role-submitted-essays .mantine-Menu-item:active,.wp-block-instructor-role-submitted-essays .mantine-Menu-item:hover{
	background-color: <?php echo esc_attr( $primary ); ?> !important
}
/*Nikhil Styling added ir-primary-colour*/
.primary-colour, .ir-primary-color, .ir-primary-colour, .ir-courses-tabs > .active, .wp-block-instructor-role-dashboard-settings .mantine-Tabs-tab[data-active="true"]{
	color: <?php echo esc_attr( $primary ); ?> !important
}
/*Nikhil Styling for bordered button*/
.ir-primary-border-button{
	color: <?php echo esc_attr( $primary ); ?> !important;
	border-color: <?php echo esc_attr( $primary ); ?> !important;
}
.ir-disabled-border-button{
	color: <?php echo esc_attr( $text ); ?> !important;
	border-color: <?php echo esc_attr( $text ); ?> !important;
	background-color: transparent !important;
}
.mantine-Tabs-tab[data-active]{
	border-color: <?php echo esc_attr( $primary ); ?> !important;
}
/*For Radial Bar 1*/
.apexcharts-legend-text[rel="1"]{
	color: <?php echo esc_attr( $primary ); ?> !important;
	filter: brightness(60%) !important;
}
.apexcharts-legend-marker[rel="1"]{
	color: <?php echo esc_attr( $primary ); ?> !important;
	background: <?php echo esc_attr( $primary ); ?> !important;
	filter: brightness(60%) !important;
}
.apexcharts-radial-series[rel="1"] path{
	stroke: <?php echo esc_attr( $primary ); ?> !important;
	filter: brightness(60%) !important;
}
/*For Radial Bar 2*/
.apexcharts-legend-text[rel="2"]{
	color: <?php echo esc_attr( $primary ); ?> !important;
}
.apexcharts-legend-marker[rel="2"]{
	color: <?php echo esc_attr( $primary ); ?> !important;
	background: <?php echo esc_attr( $primary ); ?> !important;
}
.apexcharts-radial-series[rel="2"] path{
	stroke: <?php echo esc_attr( $primary ); ?> !important;
}
/*For Radial Bar 3*/
.apexcharts-legend-text[rel="3"]{
	color: <?php echo esc_attr( $primary ); ?> !important;
	filter: brightness(140%) !important;
}
.apexcharts-legend-marker[rel="3"]{
	color: <?php echo esc_attr( $primary ); ?> !important;
	background: <?php echo esc_attr( $primary ); ?> !important;
	filter: brightness(140%) !important;
}
.apexcharts-radial-series[rel="3"] path{
	stroke: <?php echo esc_attr( $primary ); ?> !important;
	filter: brightness(140%) !important;
}
/*Earnings Graph*/
.apexcharts-tooltip-marker{
	background-color: <?php echo esc_attr( $primary ); ?> !important;
}
.apexcharts-series-markers circle, .wp-block-instructor-role-wisdm-course-reports #ir-coures-progress-donut .apexcharts-series > path, .wp-block-instructor-role-wisdm-course-reports #ir-learner-progress-donut .apexcharts-series > path{
	fill: <?php echo esc_attr( $primary ); ?> !important;
}
linearGradient stop[offset="0"]{
	stop-color: <?php echo esc_attr( $primary ); ?> !important;
}
.apexcharts-series path.apexcharts-area, .mantine-RingProgress-root.ir-primary-stroke > svg > circle:nth-last-child(1){
	stroke: <?php echo esc_attr( $primary ); ?> !important;
}
/*Dropdown Select*/
.mantine-Select-item[data-selected], .mantine-Menu-item:active, .mantine-Menu-item:hover{
	background-color: <?php echo esc_attr( $primary ); ?> !important;
}
.primary-colour, .ir-primary-color, .ir-primary-colour, .ir-courses-tabs > .active{
		color: <?php echo esc_attr( $primary ); ?> !important
	}
.ir-tel{
	color: <?php echo esc_attr( $text_ex_light ); ?> !important
}
svg.ir-tel{
	stroke: <?php echo esc_attr( $text_ex_light ); ?> !important
}
.ir-light-primary-colour{
	color: <?php echo esc_attr( $text_light ); ?> !important
}
.ir-heading-color, .ir-heading-colour{
	color: <?php echo esc_attr( $headings ); ?> !important
}
.wp-block-instructor-role-wisdm-tabs > .tab-labels{
	background-color: <?php echo esc_attr( $side_bg ); ?> !important
}
.wp-block-instructor-role-wisdm-tabs > .tab-content{
	background-color: <?php echo esc_attr( $background ); ?> !important
}
html{
	font-size: <?php echo esc_attr( $font_size ); ?> !important
}
.wp-block-instructor-role-wisdm-tabs .tab-labels .tab-label.active, .ir-commissions table thead, .wp-block-instructor-role-wisdm-tabs .tab-labels .tab-label:hover  {
	background-color: <?php echo esc_attr( $accent ); ?> !important
}
.wp-block-instructor-role-wisdm-tabs > .tab-labels .ir-divider{
	background-color: <?php echo esc_attr( $border ); ?> !important
}
.ir-courses-wrap > div > .mantine-Paper-root, .wp-block-instructor-role-wisdm-tabs .mantine-Input-input, .mantine-Pagination-item,
.wp-block-instructor-role-overview-page .user-info, .wp-block-instructor-role-overview-page .overview,
.wp-block-instructor-role-overview-page .block{
	border: 1px solid <?php echo esc_attr( $border ); ?> !important
}
.ir-border-color, .wp-block-instructor-role-wisdm-tabs > .tab-labels{
	border-color: <?php echo esc_attr( $border ); ?> !important
}
.ir-active-comment{
	border-color: <?php echo esc_attr( $primary ); ?> !important;
	background: <?php echo esc_attr( $accent ); ?>;
}
.ir-primary-button, button.ir-primary-bg{
	color: <?php echo esc_attr( $text_primary_btn ); ?> !important
}
.wp-block-instructor-role-wisdm-all-courses .ir-courses-tabs button:hover, .wp-block-instructor-role-wisdm-all-quizzes .ir-courses-tabs button:hover{
	border-bottom: 1px solid <?php echo esc_attr( $primary ); ?> !important
}
svg.ir-primary-hover:hover{
	stroke: <?php echo esc_attr( $primary ); ?> !important
}
