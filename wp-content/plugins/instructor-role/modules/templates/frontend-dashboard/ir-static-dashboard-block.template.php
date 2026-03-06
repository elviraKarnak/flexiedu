<?php
/**
 * Instructor Dashboard Block Preset Static Template.
 *
 * @since 5.0.0
 *
 * @package LearnDash\Instructor_Role
 *
 * cspell:ignore coures // ignoring misspelled words that we can't change now.
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<!-- wp:instructor-role/wisdm-tabs {"tabLabelsArray":["Overview","Course List","Quiz List","Products","Commissions","Assignments","Essays","Quiz Attempts","Comments","Course Reports","Groups","Certificates","Settings"],"classNameArray":["overview-page","wisdm-all-courses","wisdm-all-quizzes","wisdm-instructor-products","wisdm-instructor-commissions","ir-assignments","submitted-essays","wisdm-quiz-attempts","wisdm-instructor-comments","wisdm-course-reports","wisdm-groups","wisdm-certificates","dashboard-settings"],"tabIconArray":[0,1,4,3,11,15,26,28,5,17,10,29,7],"tabIndexArray":[0,1,2,3,4,5,6,7,8,9,10,11,12],"tooltipTextArray":["","","","","","","","","","","","",""],"topButton":"+ Create New Course","isCourseUrl":true} -->
<div class="wp-block-instructor-role-wisdm-tabs"><style>
		.wp-block-instructor-role-wisdm-tabs * {
			font-family:  !important
		}
		.wp-block-instructor-role-wisdm-tabs > .tab-labels .tab-label, .ir-toggle-sidebar{
		color: #666666 !important
		}
		.wp-block-instructor-role-wisdm-tabs .tab-labels .tab-label.active, .overview svg, .wp-block-instructor-role-wisdm-tabs > .tab-labels .tab-label:hover{
			color: #2067FA !important
		}
		.ir-text-colour{
		color: #666666 !important
		}
		#ir-coures-progress-donut span.apexcharts-legend-marker:after, #ir-learner-progress-donut span.apexcharts-legend-marker:after, .mantine-Progress-bar, .primary-bg, .ir-primary-bg, .mantine-Pagination-item[data-active], .mantine-Stepper-stepIcon[data-completed], .wp-block-instructor-role-wisdm-tabs .mantine-Pagination-item:hover, .wp-block-instructor-role-wisdm-tabs .mantine-Pagination-item:active, .wp-block-instructor-role-submitted-essays .mantine-Menu-item:active,.wp-block-instructor-role-submitted-essays .mantine-Menu-item:hover{
			background-color: #2067FA !important
		}
		.primary-colour, .ir-primary-color, .ir-primary-colour, .ir-courses-tabs > .active, .wp-block-instructor-role-dashboard-settings .mantine-Tabs-tab[data-active="true"]{
			color: #2067FA !important
		}
		.ir-primary-border-button{
			color: #2067FA !important;
			border-color: #2067FA !important;
		}
		.ir-disabled-border-button{
			color: #666666 !important;
			border-color: #666666 !important;
			background-color: transparent !important;
		}
		.mantine-Tabs-tab[data-active]{
			border-color: #2067FA !important;
		}
		/*For Radial Bar 1*/
		.apexcharts-legend-text[rel="1"]{
		color: #2067FA !important;
		filter: brightness(60%) !important;
		}
		.apexcharts-legend-marker[rel="1"]{
		color: #2067FA !important;
		background: #2067FA !important;
		filter: brightness(60%) !important;
		}
		.apexcharts-radial-series[rel="1"] path{
		stroke: #2067FA !important;
		filter: brightness(60%) !important;
		}
		/*For Radial Bar 2*/
		.apexcharts-legend-text[rel="2"]{
		color: #2067FA !important;
		}
		.apexcharts-legend-marker[rel="2"]{
		color: #2067FA !important;
		background: #2067FA !important;
		}
		.apexcharts-radial-series[rel="2"] path{
		stroke: #2067FA !important;
		}
		/*For Radial Bar 3*/
		.apexcharts-legend-text[rel="3"]{
		color: #2067FA !important;
		filter: brightness(140%) !important;
		}
		.apexcharts-legend-marker[rel="3"]{
		color: #2067FA !important;
		background: #2067FA !important;
		filter: brightness(140%) !important;
		}
		.apexcharts-radial-series[rel="3"] path{
		stroke: #2067FA !important;
		filter: brightness(140%) !important;
		}
		/*Earnings Graph*/
		.apexcharts-tooltip-marker{
		background-color: #2067FA !important;
		}
		.apexcharts-series-markers circle, .wp-block-instructor-role-wisdm-course-reports #ir-coures-progress-donut .apexcharts-series > path, .wp-block-instructor-role-wisdm-course-reports #ir-learner-progress-donut .apexcharts-series > path{
		fill: #2067FA !important;
		}
		linearGradient stop[offset="0"]{
		stop-color: #2067FA !important;
		}
		.apexcharts-series path.apexcharts-area, .mantine-RingProgress-root.ir-primary-stroke > svg > circle:nth-last-child(1){
		stroke: #2067FA !important;
		}
		/*Dropdown Select*/
		.mantine-Select-item[data-selected], .mantine-Menu-item:active, .mantine-Menu-item:hover{
		background-color: #2067FA !important;
		}
		.primary-colour, .ir-primary-color, .ir-primary-colour, .ir-courses-tabs > .active{
			color: #2067FA !important
		}
		.ir-tel{
			color: #ADB5BD !important
		}
		svg.ir-tel{
		stroke: #ADB5BD !important
		}
		.ir-light-primary-colour{
			color: #868E96 !important
		}
		.ir-heading-color, .ir-heading-colour{
		color: #2E353C !important
		}
		.wp-block-instructor-role-wisdm-tabs > .tab-labels{
		background-color: #FFFFFF !important
		}
		.wp-block-instructor-role-wisdm-tabs > .tab-content{
		background-color: #FBFCFF !important
		}
		html{
			font-size: 16px !important
		}
		.wp-block-instructor-role-wisdm-tabs .tab-labels .tab-label.active, .ir-commissions table thead, .wp-block-instructor-role-wisdm-tabs .tab-labels .tab-label:hover  {
		background-color: #F3F9FB !important
		}
		.wp-block-instructor-role-wisdm-tabs > .tab-labels .ir-divider{
		background-color: #D6D8E7 !important
		}
		.ir-courses-wrap > div > .mantine-Paper-root, .wp-block-instructor-role-wisdm-tabs .mantine-Input-input, .mantine-Pagination-item,
		.wp-block-instructor-role-overview-page .user-info, .wp-block-instructor-role-overview-page .overview,
		.wp-block-instructor-role-overview-page .block{
		border: 1px solid #D6D8E7 !important
		}
		.ir-border-color, .wp-block-instructor-role-wisdm-tabs > .tab-labels{
		border-color: #D6D8E7 !important
		}
		.ir-active-comment{
		border-color: #2067FA !important;
		background: #F3F9FB;
		}
		.ir-primary-button, button.ir-primary-bg{
		color: #ffffff !important
		}
		.wp-block-instructor-role-wisdm-all-courses .ir-courses-tabs button:hover, .wp-block-instructor-role-wisdm-all-quizzes .ir-courses-tabs button:hover{
			border-bottom: 1px solid #2067FA !important
		}
		svg.ir-primary-svg{
		stroke: #2067FA !important
		}
		svg.ir-primary-hover:hover{
		stroke: #2067FA !important
		}
		</style><script>var fontScript = document.createElement('link'); fontScript.setAttribute('href','https://fonts.googleapis.com/css?family='); fontScript.setAttribute('rel','stylesheet'); document.head.appendChild(fontScript);</script><ul class="tab-labels refresh" role="tablist" aria-label="tabbed content"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ir-toggle-sidebar"><path d="M4 6l10 0"></path><path d="M4 18l10 0"></path><path d="M4 12h17l-3 -3m0 6l3 -3"></path></svg><span class="ir-divider"></span><a href="<?php echo add_query_arg( [ 'action' => 'ir_fcb_new_course' ], admin_url( 'admin-ajax.php' ), ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>" class="topButton primary-bg ir-primary-button" target="_blank" rel="noopener">+ Create New Course</a><a class="ir-tab-link" href="?tab=0"><li class="tab-label " role="tab" aria-selected="true" aria-controls="Overview" tabindex="0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M13 5h8"></path><path d="M13 9h5"></path><path d="M13 15h8"></path><path d="M13 19h5"></path><path d="M3 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path><path d="M3 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path></svg><span class="ir-label">Overview</span></li></a><a class="ir-tab-link" href="?tab=1"><li class="tab-label " role="tab" aria-selected="false" aria-controls="Course List" tabindex="1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"></path><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"></path><path d="M3 6l0 13"></path><path d="M12 6l0 13"></path><path d="M21 6l0 13"></path></svg><span class="ir-label">Course List</span></li></a><a class="ir-tab-link" href="?tab=2"><li class="tab-label " role="tab" aria-selected="false" aria-controls="Quiz List" tabindex="2"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M4 4m0 1a1 1 0 0 1 1 -1h14a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-14a1 1 0 0 1 -1 -1z"></path><path d="M4 8h16"></path><path d="M8 4v4"></path><path d="M9.5 14.5l1.5 1.5l3 -3"></path></svg><span class="ir-label">Quiz List</span></li></a><a class="ir-tab-link" href="?tab=3"><li class="tab-label " role="tab" aria-selected="false" aria-controls="Products" tabindex="3"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M12 6m-8 0a8 3 0 1 0 16 0a8 3 0 1 0 -16 0"></path><path d="M4 6v6a8 3 0 0 0 16 0v-6"></path><path d="M4 12v6a8 3 0 0 0 16 0v-6"></path></svg><span class="ir-label">Products</span></li></a><a class="ir-tab-link" href="?tab=4"><li class="tab-label " role="tab" aria-selected="false" aria-controls="Commissions" tabindex="4"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M3 3m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"></path><path d="M9 15v2"></path><path d="M12 11v6"></path><path d="M15 13v4"></path></svg><span class="ir-label">Commissions</span></li></a><a class="ir-tab-link" href="?tab=5"><li class="tab-label " role="tab" aria-selected="false" aria-controls="Assignments" tabindex="5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M14 3v4a1 1 0 0 0 1 1h4"></path><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path></svg><span class="ir-label">Assignments</span></li></a><a class="ir-tab-link" href="?tab=6"><li class="tab-label " role="tab" aria-selected="false" aria-controls="Essays" tabindex="6"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M12 4l-8 4l8 4l8 -4l-8 -4"></path><path d="M4 12l8 4l8 -4"></path><path d="M4 16l8 4l8 -4"></path></svg><span class="ir-label">Essays</span></li></a><a class="ir-tab-link" href="?tab=7"><li class="tab-label " role="tab" aria-selected="false" aria-controls="Quiz Attempts" tabindex="7"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path><path d="M6 21v-2a4 4 0 0 1 4 -4h3.5"></path><path d="M19 22v.01"></path><path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path></svg><span class="ir-label">Quiz Attempts</span></li></a><a class="ir-tab-link" href="?tab=8"><li class="tab-label " role="tab" aria-selected="false" aria-controls="Comments" tabindex="8"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M8 9h8"></path><path d="M8 13h6"></path><path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z"></path></svg><span class="ir-label">Comments</span></li></a><a class="ir-tab-link" href="?tab=9"><li class="tab-label " role="tab" aria-selected="false" aria-controls="Course Reports" tabindex="9"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M12 3v9h9"></path><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path></svg><span class="ir-label">Course Reports</span></li></a><a class="ir-tab-link" href="?tab=10"><li class="tab-label " role="tab" aria-selected="false" aria-controls="Groups" tabindex="10"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path><path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path></svg><span class="ir-label">Groups</span></li></a><a class="ir-tab-link" href="?tab=11"><li class="tab-label " role="tab" aria-selected="false" aria-controls="Certificates" tabindex="11"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M15 15m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path><path d="M13 17.5v4.5l2 -1.5l2 1.5v-4.5"></path><path d="M10 19h-5a2 2 0 0 1 -2 -2v-10c0 -1.1 .9 -2 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -1 1.73"></path><path d="M6 9l12 0"></path><path d="M6 12l3 0"></path><path d="M6 15l2 0"></path></svg><span class="ir-label">Certificates</span></li></a><a class="ir-tab-link" href="?tab=12"><li class="tab-label " role="tab" aria-selected="false" aria-controls="Settings" tabindex="12"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"></path><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path></svg><span class="ir-label">Settings</span></li></a></ul><div class="tab-content"><!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Overview","blockIndex":0,"className":"ir-overview-tab"} -->
<div class="wp-block-instructor-role-wisdm-tab-item tab-panel ir-overview-tab" role="tabpanel" data-index="0"><!-- wp:instructor-role/overview-page -->
<div class="wp-block-instructor-role-overview-page"><div class="overview-page" data-earnings="true" data-courses="true" data-reports="true" data-sub="true" data-courses-count="true" data-students-count="true" data-sub-count="true" data-quiz-count="true"></div></div>
<!-- /wp:instructor-role/overview-page --></div>
<!-- /wp:instructor-role/wisdm-tab-item -->

<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Course List","tabIcon":1,"blockIndex":1,"className":"ir-course-list-tab"} -->
<div class="wp-block-instructor-role-wisdm-tab-item tab-panel ir-course-list-tab" role="tabpanel" data-index="1"><!-- wp:instructor-role/wisdm-all-courses -->
<div class="wp-block-instructor-role-wisdm-all-courses"><div class="wisdm-all-courses"></div></div>
<!-- /wp:instructor-role/wisdm-all-courses --></div>
<!-- /wp:instructor-role/wisdm-tab-item -->

<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Quiz List","tabIcon":4,"blockIndex":2,"className":"ir-quiz-list-tab"} -->
<div class="wp-block-instructor-role-wisdm-tab-item tab-panel ir-quiz-list-tab" role="tabpanel" data-index="2"><!-- wp:instructor-role/wisdm-all-quizzes -->
<div class="wp-block-instructor-role-wisdm-all-quizzes"><div class="wisdm-all-quizzes"></div></div>
<!-- /wp:instructor-role/wisdm-all-quizzes --></div>
<!-- /wp:instructor-role/wisdm-tab-item -->

<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Products","tabIcon":3,"blockIndex":3,"className":"ir-products-tab"} -->
<div class="wp-block-instructor-role-wisdm-tab-item tab-panel ir-products-tab" role="tabpanel" data-index="3"><!-- wp:instructor-role/wisdm-instructor-products -->
<div class="wp-block-instructor-role-wisdm-instructor-products"><div class="wisdm-instructor-products"></div></div>
<!-- /wp:instructor-role/wisdm-instructor-products --></div>
<!-- /wp:instructor-role/wisdm-tab-item -->

<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Commissions","tabIcon":11,"blockIndex":4,"className":"ir-commissions-tab"} -->
<div class="wp-block-instructor-role-wisdm-tab-item tab-panel ir-commissions-tab" role="tabpanel" data-index="4"><!-- wp:instructor-role/wisdm-instructor-commissions -->
<div class="wp-block-instructor-role-wisdm-instructor-commissions"><div class="wisdm-instructor-commissions"></div></div>
<!-- /wp:instructor-role/wisdm-instructor-commissions --></div>
<!-- /wp:instructor-role/wisdm-tab-item -->

<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Assignments","tabIcon":15,"blockIndex":5,"className":"ir-assignments-tab"} -->
<div class="wp-block-instructor-role-wisdm-tab-item tab-panel ir-assignments-tab" role="tabpanel" data-index="5"><!-- wp:instructor-role/ir-assignments -->
<div class="wp-block-instructor-role-ir-assignments"><div class="ir-assignments"></div></div>
<!-- /wp:instructor-role/ir-assignments --></div>
<!-- /wp:instructor-role/wisdm-tab-item -->

<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Essays","tabIcon":26,"blockIndex":6} -->
<div class="wp-block-instructor-role-wisdm-tab-item tab-panel" role="tabpanel" data-index="6"><!-- wp:instructor-role/submitted-essays {"className":"ir-essays-tab"} -->
<div class="wp-block-instructor-role-submitted-essays ir-essays-tab"><div class="submitted-essays">Save</div></div>
<!-- /wp:instructor-role/submitted-essays --></div>
<!-- /wp:instructor-role/wisdm-tab-item -->

<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Quiz Attempts","tabIcon":28,"blockIndex":7} -->
<div class="wp-block-instructor-role-wisdm-tab-item tab-panel" role="tabpanel" data-index="7"><!-- wp:instructor-role/wisdm-quiz-attempts -->
<div class="wp-block-instructor-role-wisdm-quiz-attempts"><div class="wisdm-quiz-attempts"></div></div>
<!-- /wp:instructor-role/wisdm-quiz-attempts --></div>
<!-- /wp:instructor-role/wisdm-tab-item -->

<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Comments","tabIcon":5,"blockIndex":8} -->
<div class="wp-block-instructor-role-wisdm-tab-item tab-panel" role="tabpanel" data-index="8"><!-- wp:instructor-role/wisdm-instructor-comments -->
<div class="wp-block-instructor-role-wisdm-instructor-comments"><div class="ir-comments"></div></div>
<!-- /wp:instructor-role/wisdm-instructor-comments --></div>
<!-- /wp:instructor-role/wisdm-tab-item -->

<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Course Reports","tabIcon":17,"blockIndex":9} -->
<div class="wp-block-instructor-role-wisdm-tab-item tab-panel" role="tabpanel" data-index="9"><!-- wp:instructor-role/wisdm-course-reports -->
<div class="wp-block-instructor-role-wisdm-course-reports"><div class="wisdm-course-reports"></div></div>
<!-- /wp:instructor-role/wisdm-course-reports --></div>
<!-- /wp:instructor-role/wisdm-tab-item -->

<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Groups","tabIcon":10,"blockIndex":10} -->
<div class="wp-block-instructor-role-wisdm-tab-item tab-panel" role="tabpanel" data-index="10"><!-- wp:instructor-role/wisdm-groups -->
<div class="wp-block-instructor-role-wisdm-groups"><div class="wisdm-groups"></div></div>
<!-- /wp:instructor-role/wisdm-groups --></div>
<!-- /wp:instructor-role/wisdm-tab-item -->

<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Certificates","tabIcon":29,"blockIndex":11} -->
<div class="wp-block-instructor-role-wisdm-tab-item tab-panel" role="tabpanel" data-index="11"><!-- wp:instructor-role/wisdm-certificates -->
<div class="wp-block-instructor-role-wisdm-certificates"><div class="wisdm-certificates"></div></div>
<!-- /wp:instructor-role/wisdm-certificates --></div>
<!-- /wp:instructor-role/wisdm-tab-item -->

<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Settings","tabIcon":7,"blockIndex":12} -->
<div class="wp-block-instructor-role-wisdm-tab-item tab-panel" role="tabpanel" data-index="12"><!-- wp:instructor-role/dashboard-settings -->
<div class="wp-block-instructor-role-dashboard-settings"><div class="dashboard-settings" data-paypal="true"></div></div>
<!-- /wp:instructor-role/dashboard-settings --></div>
<!-- /wp:instructor-role/wisdm-tab-item --></div></div>
<!-- /wp:instructor-role/wisdm-tabs -->

</div>
</div>
<p><!-- /wp:instructor-role/wisdm-tabs --></p>
