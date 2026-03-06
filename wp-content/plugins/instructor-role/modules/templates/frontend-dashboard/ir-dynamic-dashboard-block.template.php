<?php
/**
 * Instructor Dashboard Block Preset Dynamic Template.
 *
 * @since 5.0.0
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<!-- wp:instructor-role/wisdm-tabs <?php echo $tab_settings_json; ?> -->
<div class="wp-block-instructor-role-wisdm-tabs">
	<style><?php echo $custom_styles; ?></style>
	<script>
		var fontScript = document.createElement('link');
		fontScript.setAttribute('href','https://fonts.googleapis.com/css?family=<?php echo esc_attr( $custom_font ); ?>')
		fontScript.setAttribute('rel','stylesheet');
		document.head.appendChild(fontScript);
	</script>
	<ul class="tab-labels refresh" role="tablist" aria-label="tabbed content">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ir-toggle-sidebar"><path d="M4 6l10 0"></path><path d="M4 18l10 0"></path><path d="M4 12h17l-3 -3m0 6l3 -3"></path></svg>
		<span class="ir-divider"></span>
		<a href="<?php echo add_query_arg( [ 'action' => 'ir_fcb_new_course' ], admin_url( 'admin-ajax.php' ), ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>" class="topButton primary-bg ir-primary-button" target="_blank" rel="noopener">
		<?php
		echo esc_html(
			sprintf(
				/* translators: Course Label */
				__( '+ Create New %s', 'wdm_instructor_role' ),
				\LearnDash_Custom_Label::get_label( 'course' )
			)
		);
		?>
		</a>
		<?php if ( $is_overview_tab_active ) : ?>
			<a class="ir-tab-link" href="?tab=<?php echo esc_attr( $tab_index ); ?>">
				<li class="tab-label ir-overview-tab <?php echo ( $tab_index == $active_tab ) ? 'active ' : ''; ?>" role="tab" aria-selected="true" aria-controls="Overview" tabindex="0">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M13 5h8"></path><path d="M13 9h5"></path><path d="M13 15h8"></path><path d="M13 19h5"></path><path d="M3 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path><path d="M3 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path></svg>
					<span class="ir-label"><?php esc_html_e( 'Overview', 'wdm_instructor_role' ); ?></span>
				</li>
			</a>
			<?php ++$tab_index; ?>
		<?php endif; ?>

		<?php if ( $is_courses_tab_active ) : ?>
			<a class="ir-tab-link" href="?tab=<?php echo esc_attr( $tab_index ); ?>">
				<li class="tab-label ir-course-list-tab <?php echo ( $tab_index == $active_tab ) ? 'active' : ''; ?>" role="tab" aria-selected="false" aria-controls="Course List" tabindex="1">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"></path><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"></path><path d="M3 6l0 13"></path><path d="M12 6l0 13"></path><path d="M21 6l0 13"></path></svg>
						<span class="ir-label"><?php echo \LearnDash_Custom_Label::get_label( 'courses' ); ?></span>
				</li>
			</a>
			<?php ++$tab_index; ?>
		<?php endif; ?>

		<?php if ( $is_quizzes_tab_active ) : ?>
			<a class="ir-tab-link" href="?tab=<?php echo esc_attr( $tab_index ); ?>">
				<li class="tab-label <?php echo ( $tab_index == $active_tab ) ? 'active ' : ''; ?>ir-quiz-list-tab" role="tab" aria-selected="false" aria-controls="Quiz List" tabindex="2">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M4 4m0 1a1 1 0 0 1 1 -1h14a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-14a1 1 0 0 1 -1 -1z"></path><path d="M4 8h16"></path><path d="M8 4v4"></path><path d="M9.5 14.5l1.5 1.5l3 -3"></path></svg>
					<span class="ir-label"><?php echo \LearnDash_Custom_Label::get_label( 'quizzes' ); ?></span>
				</li>
			</a>
			<?php ++$tab_index; ?>
		<?php endif; ?>

		<?php if ( $is_products_tab_active ) : ?>
			<a class="ir-tab-link" href="?tab=<?php echo esc_attr( $tab_index ); ?>">
				<li class="tab-label <?php echo ( $tab_index == $active_tab ) ? 'active ' : ''; ?>ir-products-tab" role="tab" aria-selected="false" aria-controls="Products" tabindex="3"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M12 6m-8 0a8 3 0 1 0 16 0a8 3 0 1 0 -16 0"></path><path d="M4 6v6a8 3 0 0 0 16 0v-6"></path><path d="M4 12v6a8 3 0 0 0 16 0v-6"></path></svg><span class="ir-label"><?php esc_html_e( 'Products', 'wdm_instructor_role' ); ?></span></li>
			</a>
			<?php ++$tab_index; ?>
		<?php endif; ?>

		<?php if ( $is_commissions_tab_active ) : ?>
			<a class="ir-tab-link" href="?tab=<?php echo esc_attr( $tab_index ); ?>">
				<li class="tab-label <?php echo ( $tab_index == $active_tab ) ? 'active ' : ''; ?>ir-commissions-tab" role="tab" aria-selected="false" aria-controls="Commissions" tabindex="4"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M3 3m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"></path><path d="M9 15v2"></path><path d="M12 11v6"></path><path d="M15 13v4"></path></svg><span class="ir-label"><?php esc_html_e( 'Commissions', 'wdm_instructor_role' ); ?></span></li>
			</a>
			<?php ++$tab_index; ?>
		<?php endif; ?>

		<?php if ( $is_assignments_tab_active ) : ?>
			<a class="ir-tab-link" href="?tab=<?php echo esc_attr( $tab_index ); ?>">
				<li class="tab-label <?php echo ( $tab_index == $active_tab ) ? 'active ' : ''; ?>ir-assignments-tab" role="tab" aria-selected="false" aria-controls="Assignments" tabindex="5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M14 3v4a1 1 0 0 0 1 1h4"></path><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path></svg><span class="ir-label"><?php esc_html_e( 'Assignments', 'wdm_instructor_role' ); ?></span></li>
			</a>
			<?php ++$tab_index; ?>
		<?php endif; ?>

		<?php if ( $is_essays_tab_active ) : ?>
			<a class="ir-tab-link" href="?tab=<?php echo esc_attr( $tab_index ); ?>">
				<li class="tab-label <?php echo ( $tab_index == $active_tab ) ? 'active ' : ''; ?>ir-essays-tab" role="tab" aria-selected="false" aria-controls="Essays" tabindex="6"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M12 4l-8 4l8 4l8 -4l-8 -4"></path><path d="M4 12l8 4l8 -4"></path><path d="M4 16l8 4l8 -4"></path></svg><span class="ir-label"><?php esc_html_e( 'Essays', 'wdm_instructor_role' ); ?></span></li>
			</a>
			<?php ++$tab_index; ?>
		<?php endif; ?>

		<?php if ( $is_quiz_attempts_tab_active ) : ?>
			<a class="ir-tab-link" href="?tab=<?php echo esc_attr( $tab_index ); ?>">
				<li class="tab-label " role="tab" aria-selected="false" aria-controls="Quiz Attempts" tabindex="7"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path><path d="M6 21v-2a4 4 0 0 1 4 -4h3.5"></path><path d="M19 22v.01"></path><path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path></svg><span class="ir-label"><?php echo esc_html( sprintf( /* translators: Quiz Label. */__( '%s Attempts', 'wdm_instructor_role' ), \LearnDash_Custom_Label::get_label( 'quiz' ) ) ); ?></span></li>
			</a>
			<?php ++$tab_index; ?>
		<?php endif; ?>

		<?php if ( $is_comments_tab_active ) : ?>
			<a class="ir-tab-link" href="?tab=<?php echo esc_attr( $tab_index ); ?>">
				<li class="tab-label " role="tab" aria-selected="false" aria-controls="Comments" tabindex="8"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M8 9h8"></path><path d="M8 13h6"></path><path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z"></path></svg><span class="ir-label"><?php esc_html_e( 'Comments', 'wdm_instructor_role' ); ?></span></li>
			</a>
			<?php ++$tab_index; ?>
		<?php endif; ?>

		<?php if ( $is_course_reports_tab_active ) : ?>
			<a class="ir-tab-link" href="?tab=<?php echo esc_attr( $tab_index ); ?>">
				<li class="tab-label " role="tab" aria-selected="false" aria-controls="<?php echo esc_html( sprintf( /* translators: Course Label. */__( '%s Reports', 'wdm_instructor_role' ), \LearnDash_Custom_Label::get_label( 'course' ) ) ); ?>" tabindex="9"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M12 3v9h9"></path><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path></svg><span class="ir-label"><?php echo esc_html( sprintf( /* translators: Course Label. */__( '%s Reports', 'wdm_instructor_role' ), \LearnDash_Custom_Label::get_label( 'course' ) ) ); ?></span></li>
			</a>
			<?php ++$tab_index; ?>
		<?php endif; ?>

		<?php if ( $is_groups_tab_active ) : ?>
			<a class="ir-tab-link" href="?tab=<?php echo esc_attr( $tab_index ); ?>">
			<li class="tab-label " role="tab" aria-selected="false" aria-controls="Groups" tabindex="10"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path><path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path></svg><span class="ir-label"><?php echo esc_html( \LearnDash_Custom_Label::get_label( 'group' ) ); ?></span></li>
			</a>
			<?php ++$tab_index; ?>
		<?php endif; ?>

		<?php if ( $is_certificates_tab_active ) : ?>
			<a class="ir-tab-link" href="?tab=<?php echo esc_attr( $tab_index ); ?>">
			<li class="tab-label " role="tab" aria-selected="false" aria-controls="Certificates" tabindex="11"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M15 15m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path><path d="M13 17.5v4.5l2 -1.5l2 1.5v-4.5"></path><path d="M10 19h-5a2 2 0 0 1 -2 -2v-10c0 -1.1 .9 -2 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -1 1.73"></path><path d="M6 9l12 0"></path><path d="M6 12l3 0"></path><path d="M6 15l2 0"></path></svg><span class="ir-label"><?php echo esc_html_e( 'Certificates', 'wdm_instructor_role' ); ?></span></li>
			</a>
			<?php ++$tab_index; ?>
		<?php endif; ?>

		<?php if ( $is_settings_tab_active ) : ?>
			<a class="ir-tab-link" href="?tab=<?php echo esc_attr( $tab_index ); ?>">
				<li class="tab-label <?php echo ( $tab_index == $active_tab ) ? 'active ' : ''; ?>ir-settings-tab" role="tab" aria-selected="false" aria-controls="Settings" tabindex="7">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"></path><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path></svg><span class="ir-label"><?php esc_html_e( 'Settings', 'wdm_instructor_role' ); ?></span>
				</li>
			</a>
		<?php endif; ?>
	</ul>
	<div class="tab-content">

		<?php if ( $is_overview_tab_active ) : ?>
			<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Overview","blockIndex":0,"className":"ir-overview-tab"} -->
			<div class="wp-block-instructor-role-wisdm-tab-item tab-panel ir-overview-tab" role="tabpanel" data-index="0">
				<!-- wp:instructor-role/overview-page -->
				<div class="wp-block-instructor-role-overview-page">
					<div class="overview-page" <?php echo $overview_block_setting; ?>></div>
				</div>
				<!-- /wp:instructor-role/overview-page --></div>
			<!-- /wp:instructor-role/wisdm-tab-item -->
		<?php endif; ?>

		<?php if ( $is_courses_tab_active ) : ?>
			<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Course List","tabIcon":1,"blockIndex":1,"className":"ir-course-list-tab"} -->
			<div class="wp-block-instructor-role-wisdm-tab-item tab-panel ir-course-list-tab" role="tabpanel" data-index="1">
				<!-- wp:instructor-role/wisdm-all-courses -->
				<div class="wp-block-instructor-role-wisdm-all-courses"><div class="wisdm-all-courses"></div></div>
				<!-- /wp:instructor-role/wisdm-all-courses --></div>
			<!-- /wp:instructor-role/wisdm-tab-item -->
		<?php endif; ?>

		<?php if ( $is_quizzes_tab_active ) : ?>
			<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Quiz List","tabIcon":4,"blockIndex":2,"className":"ir-quiz-list-tab"} -->
			<div class="wp-block-instructor-role-wisdm-tab-item tab-panel ir-quiz-list-tab" role="tabpanel" data-index="2">
				<!-- wp:instructor-role/wisdm-all-quizzes -->
				<div class="wp-block-instructor-role-wisdm-all-quizzes"><div class="wisdm-all-quizzes"></div></div>
				<!-- /wp:instructor-role/wisdm-all-quizzes --></div>
			<!-- /wp:instructor-role/wisdm-tab-item -->
		<?php endif; ?>

		<?php if ( $is_products_tab_active ) : ?>
			<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Products","tabIcon":3,"blockIndex":3,"className":"ir-products-tab"} -->
			<div class="wp-block-instructor-role-wisdm-tab-item tab-panel ir-products-tab" role="tabpanel" data-index="3">
				<!-- wp:instructor-role/wisdm-instructor-products -->
				<div class="wp-block-instructor-role-wisdm-instructor-products">
					<div class="wisdm-instructor-products"></div>
				</div>
				<!-- /wp:instructor-role/wisdm-instructor-products -->
			</div>
			<!-- /wp:instructor-role/wisdm-tab-item -->
		<?php endif; ?>

		<?php if ( $is_commissions_tab_active ) : ?>
			<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Commissions","tabIcon":11,"blockIndex":4,"className":"ir-commissions-tab"} -->
			<div class="wp-block-instructor-role-wisdm-tab-item tab-panel ir-commissions-tab" role="tabpanel" data-index="4">
				<!-- wp:instructor-role/wisdm-instructor-commissions -->
				<div class="wp-block-instructor-role-wisdm-instructor-commissions">
					<div class="wisdm-instructor-commissions"></div>
				</div>
				<!-- /wp:instructor-role/wisdm-instructor-commissions -->
			</div>
			<!-- /wp:instructor-role/wisdm-tab-item -->
		<?php endif; ?>

		<?php if ( $is_assignments_tab_active ) : ?>
			<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Assignments","tabIcon":15,"blockIndex":5,"className":"ir-assignments-tab"} -->
			<div class="wp-block-instructor-role-wisdm-tab-item tab-panel ir-assignments-tab" role="tabpanel" data-index="5">
				<!-- wp:instructor-role/ir-assignments -->
				<div class="wp-block-instructor-role-ir-assignments">
					<div class="ir-assignments"></div>
				</div>
				<!-- /wp:instructor-role/ir-assignments -->
			</div>
			<!-- /wp:instructor-role/wisdm-tab-item -->
		<?php endif; ?>

		<?php if ( $is_essays_tab_active ) : ?>
			<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Essays","tabIcon":26,"blockIndex":6} -->
			<div class="wp-block-instructor-role-wisdm-tab-item tab-panel" role="tabpanel" data-index="6">
				<!-- wp:instructor-role/submitted-essays {"className":"ir-essays-tab"} -->
				<div class="wp-block-instructor-role-submitted-essays ir-essays-tab">
					<div class="submitted-essays"><?php esc_html_e( 'Save', 'wdm_instructor_role' ); ?></div>
				</div>
				<!-- /wp:instructor-role/submitted-essays -->
			</div>
			<!-- /wp:instructor-role/wisdm-tab-item -->
		<?php endif; ?>

		<?php if ( $is_quiz_attempts_tab_active ) : ?>
			<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Quiz Attempts","tabIcon":28,"blockIndex":7} -->
			<div class="wp-block-instructor-role-wisdm-tab-item tab-panel" role="tabpanel" data-index="7">
				<!-- wp:instructor-role/wisdm-quiz-attempts -->
				<div class="wp-block-instructor-role-wisdm-quiz-attempts">
					<div class="wisdm-quiz-attempts"></div>
				</div>
				<!-- /wp:instructor-role/wisdm-quiz-attempts -->
			</div>
			<!-- /wp:instructor-role/wisdm-tab-item -->
		<?php endif; ?>

		<?php if ( $is_comments_tab_active ) : ?>
			<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Comments","tabIcon":5,"blockIndex":8} -->
			<div class="wp-block-instructor-role-wisdm-tab-item tab-panel" role="tabpanel" data-index="8">
				<!-- wp:instructor-role/wisdm-instructor-comments -->
				<div class="wp-block-instructor-role-wisdm-instructor-comments">
					<div class="ir-comments"></div>
				</div>
				<!-- /wp:instructor-role/wisdm-instructor-comments -->
			</div>
			<!-- /wp:instructor-role/wisdm-tab-item -->
		<?php endif; ?>

		<?php if ( $is_course_reports_tab_active ) : ?>
			<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Course Reports","tabIcon":17,"blockIndex":9} -->
			<div class="wp-block-instructor-role-wisdm-tab-item tab-panel" role="tabpanel" data-index="9">
				<!-- wp:instructor-role/wisdm-course-reports -->
				<div class="wp-block-instructor-role-wisdm-course-reports">
					<div class="wisdm-course-reports"></div>
				</div>
				<!-- /wp:instructor-role/wisdm-course-reports -->
			</div>
			<!-- /wp:instructor-role/wisdm-tab-item -->
		<?php endif; ?>

		<?php if ( $is_groups_tab_active ) : ?>
			<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Groups","tabIcon":10,"blockIndex":10} -->
				<div class="wp-block-instructor-role-wisdm-tab-item tab-panel" role="tabpanel" data-index="10">
					<!-- wp:instructor-role/wisdm-groups -->
					<div class="wp-block-instructor-role-wisdm-groups">
						<div class="wisdm-groups"></div>
					</div>
					<!-- /wp:instructor-role/wisdm-groups -->
				</div>
				<!-- /wp:instructor-role/wisdm-tab-item -->
		<?php endif; ?>

		<?php if ( $is_certificates_tab_active ) : ?>
			<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Certificates","tabIcon":29,"blockIndex":11} -->
			<div class="wp-block-instructor-role-wisdm-tab-item tab-panel" role="tabpanel" data-index="11">
				<!-- wp:instructor-role/wisdm-certificates -->
				<div class="wp-block-instructor-role-wisdm-certificates">
					<div class="wisdm-certificates"></div>
				</div>
				<!-- /wp:instructor-role/wisdm-certificates -->
			</div>
			<!-- /wp:instructor-role/wisdm-tab-item -->
		<?php endif; ?>

		<?php if ( $is_settings_tab_active ) : ?>
			<!-- wp:instructor-role/wisdm-tab-item {"tabLabel":"Settings","tabIcon":7,"blockIndex":12,"className":"ir-settings-tab"} -->
			<div class="wp-block-instructor-role-wisdm-tab-item tab-panel ir-settings-tab" role="tabpanel" data-index="12">
				<!-- wp:instructor-role/dashboard-settings -->
				<div class="wp-block-instructor-role-dashboard-settings">
					<div class="dashboard-settings" data-paypal="true"></div>
				</div>
				<!-- /wp:instructor-role/dashboard-settings -->
			</div>
			<!-- /wp:instructor-role/wisdm-tab-item -->
		<?php endif; ?>

	</div>
</div>
<!-- /wp:instructor-role/wisdm-tabs -->
