<?php
/**
 * Instructor Dashboard Settings Template
 *
 * @package LearnDash\Instructor_Role
 *
 * cspell:ignore instuctor // ignoring misspelled words that we can't change now.
 */

defined( 'ABSPATH' ) || exit;
?>
<?php if ( 'step_1' === $create_dashboard_status && false !== $onboarding ) : ?>
			<div class="ir-onboarding-container">
		<h3><?php esc_html_e( 'Dashboard setup', 'wdm_instructor_role' ); ?></h3>
				<span><?php esc_html_e( 'Click on new dashboard, it will redirect you to a new page where you can customize your dashboard', 'wdm_instructor_role' ); ?></span>
		<a></a>
			</div>

<?php elseif ( 'step_3' === $create_dashboard_status && $ir_is_gutenberg_enabled && false !== $onboarding ) : ?>
	<div class="ir-onboarding-container">
		<h3><?php esc_html_e( 'Dashboard setup', 'wdm_instructor_role' ); ?></h3>
			<span><?php esc_html_e( 'Your dashboard creation is complete, you can edit or view your dashboard or continue with setup', 'wdm_instructor_role' ); ?></span>
			<a class="setup-button ir-primary-btn" href="<?php echo admin_url( 'admin.php?page=instuctor&tab=setup' ); ?>" rel="noopener noreferrer">
				<?php esc_html_e( 'Resume Setup', 'wdm_instructor_role' ); ?>
			</a>
		</div>
<?php elseif ( 'step_3' === $create_dashboard_status && ! $ir_is_gutenberg_enabled && false !== $onboarding ) : ?>
	<div class="ir-onboarding-container">
		<h3><?php esc_html_e( 'Dashboard setup', 'wdm_instructor_role' ); ?></h3>
			<span><?php esc_html_e( 'Your dashboard creation is complete, you can edit or view your dashboard, customize its tabs or edit appearance in below settings', 'wdm_instructor_role' ); ?></span>
			<a class="setup-button ir-primary-btn" href="<?php echo admin_url( 'admin.php?page=instuctor&tab=setup' ); ?>" rel="noopener noreferrer">
				<?php esc_html_e( 'Resume Setup', 'wdm_instructor_role' ); ?>
			</a>
		</div>
		<?php endif; ?>
<div class="ir-instructor-settings-tab-content">

	<div class="justified-top" style="margin-bottom:40px;">
		<div class="flex-column">
		<div class="admin-setting-heading">
			<?php esc_html_e( 'Dashboard Settings', 'wdm_instructor_role' ); ?>
		</div>
		</div>
	</div>
	<form method="post" id="ir-frontend-setting-form">
	<div class="dashboard-settings-frontend-instructor-dashboard">
		<div class="justified-top ir-hide-on-click" style="margin-bottom:10px;">
			<div class="flex-column">
				<div class="admin-setting-heading" style="margin-bottom:8px;">
					<?php esc_html_e( 'Frontend Instructor Dashboard', 'wdm_instructor_role' ); ?>
				</div>
				<div class="title-desc">
					<span style="color:#868E96; font-weight:600;"><?php esc_html_e( 'Note:', 'wdm_instructor_role' ); ?></span>
					<?php esc_html_e( 'Disable Backend (WP) Instructor Dashboard when your Frontend Dashboard is active.', 'wdm_instructor_role' ); ?>
				</div>
			</div>
		<?php if ( false !== $ir_frontend_dashboard_launched && false !== $ir_frontend_dashboard_page ) : ?>
			<a class="button button-primary new-dashboard" href="<?php echo esc_url( $create_frontend_dashboard_link ); ?>" >
			<svg  xmlns="http://www.w3.org/2000/svg"  width="20"  height="20"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
			<?php esc_html_e( 'New Dashboard', 'wdm_instructor_role' ); ?>
		</a>
		<?php else : ?>
	<button class = "button button-primary new-dashboard" id="ir_create_frontend_dashboard">
	<svg  xmlns="http://www.w3.org/2000/svg"  width="20"  height="20"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
			<?php esc_html_e( 'Create Dashboard', 'wdm_instructor_role' ); ?>
		</button>

	<?php endif; ?>
		</div>
		<hr class="ir-hide-on-click" style="color:#D6D8E7; margin-bottom:32px;">
		<div class="flex-column ir-hide-on-click" style="gap:12px; margin-bottom:40px;">
			<label class="checkbox-label" style="align-self: flex-start;">
				<input type="checkbox" name="wdm_id_ir_dash_pri_menu" id="wdm_id_ir_dash_pri_menu" <?php echo ( 'off' != $wdm_id_ir_dash_pri_menu ) ? esc_html( 'checked' ) : ''; ?>/>
				<?php esc_html_e( 'Add Frontend Dashboard link titled “Instructor Dashboard” to the header menu', 'wdm_instructor_role' ); ?>
			</label>
			<label class="checkbox-label" style="align-self: flex-start;">
				<input type="checkbox" name="wdm_login_redirect" id="wdm_login_redirect" <?php echo ( 'off' != $wdm_login_redirect ) ? esc_html( 'checked' ) : ''; ?>/>
				<?php esc_html_e( 'Redirect Instructors to Frontend Dashboard upon login', 'wdm_instructor_role' ); ?>
			</label>
		</div>
		<div class="justified-top ir-hide-on-click">
			<div class="flex-row ir-flex-1">
				<div class="title-14px-600">
					<?php esc_html_e( 'Select Dashboard:', 'wdm_instructor_role' ); ?>
				</div>
				<div class="ir-dropdown ir-dropdown-new">
			<?php
			wp_dropdown_pages(
				[
					'name'             => 'ir_frontend_dashboard_page',
					'id'               => 'ir_frontend_dashboard_page',
					'sort_column'      => 'menu_order',
					'sort_order'       => 'ASC',
					'show_option_none' => __( 'Select a Page', 'wdm_instructor_role' ),
					'class'            => 'ir_frontend_dashboard_page',
					'echo'             => 1,
					'selected'         => $dashboard_page_id,
				]
			);
			?>
			</div>
		<?php if ( $dashboard_page_id ) : ?>
		<div class="justified-center" style="margin-left:auto;">
					<div class="flex-row">
						<a href="<?php echo esc_attr( get_permalink( $dashboard_page_id ) ); ?>" target="_blank" class="button button-primary new-instructor" >
							<span><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg></span>
							<?php esc_html_e( 'View Dashboard', 'wdm_instructor_role' ); ?>
						</a>
			<?php if ( true === $ir_is_gutenberg_enabled ) : ?>
						<a class="button button-primary new-instructor" href="<?php echo esc_attr( get_edit_post_link( $dashboard_page_id, 'edit' ) ); ?>" target="_blank">
							<span><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-palette" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21a9 9 0 0 1 0 -18c4.97 0 9 3.582 9 8c0 1.06 -.474 2.078 -1.318 2.828c-.844 .75 -1.989 1.172 -3.182 1.172h-2.5a2 2 0 0 0 -1 3.75a1.3 1.3 0 0 1 -1 2.25" /><path d="M8.5 10.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12.5 7.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M16.5 10.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /></svg></span>
							<?php esc_html_e( 'Edit Dashboard', 'wdm_instructor_role' ); ?>
						</a>
			<?php else : ?>
				<a class="button button-primary new-dashboard edit-appearance-btn">
							<span><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-palette" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21a9 9 0 0 1 0 -18c4.97 0 9 3.582 9 8c0 1.06 -.474 2.078 -1.318 2.828c-.844 .75 -1.989 1.172 -3.182 1.172h-2.5a2 2 0 0 0 -1 3.75a1.3 1.3 0 0 1 -1 2.25" /><path d="M8.5 10.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12.5 7.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M16.5 10.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /></svg></span>
							<?php esc_html_e( 'Edit Appearance', 'wdm_instructor_role' ); ?>
						</a>
			<?php endif; ?>
					</div>
				</div>
		<?php endif; ?>
			</div>

	</div>
		<?php if ( $dashboard_page_id ) : ?>
			<div class="dashboard-selected-page">

			<?php if ( false === $ir_is_gutenberg_enabled ) : ?>
				<div class="dashboard-menu-settings">
					<div style="font-size:16px; font-weight:700; color: #2E353C;">
						<?php esc_html_e( 'Menu settings', 'wdm_instructor_role' ); ?>
					</div>
					<div class="dashboard-menu-item">
						<div class="justified-center">
							<div class="flex-row">
								<label for="ir_frontend_overview_block" class="ir-switch">
									<input type="checkbox" name="ir_frontend_overview_block" id="ir_frontend_overview_block" <?php checked( ir_get_settings( 'ir_frontend_overview_block' ), 'on' ); ?>/>
									<span class="ir-slider round"></span>
								</label>
								<div class="ir-switch-info-new">
									<?php esc_html_e( 'Overview', 'wdm_instructor_role' ); ?>
								</div>
							</div>
							<div id="drawerIcon">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-down" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 9l6 6l6 -6" /></svg>
							</div>
						</div>
						<div class="drawer-content" id="drawerContent" style="display:none;">
							<hr>
							<!-- dashboard settings -->
							<div class="ir-overview-menu-setting">
					<label for="ir_frontend_overview_course_tile_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_overview_course_tile_block" id="ir_frontend_overview_course_tile_block" <?php checked( ir_get_settings( 'ir_frontend_overview_course_tile_block' ), 'on' ); ?> />
					<span class="ir-slider round"></span>
					</label>
					<div class="ir-switch-info-new">
					<?php
					echo esc_html(
						sprintf(
							/* translators: Course label */
							__( '%s Tile', 'wdm_instructor_role' ),
							\LearnDash_Custom_Label::get_label( 'course' )
						)
					);
					?>
					</div>
				</div>

				<div class="ir-overview-menu-setting">
					<label for="ir_frontend_overview_student_tile_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_overview_student_tile_block" id="ir_frontend_overview_student_tile_block" <?php checked( ir_get_settings( 'ir_frontend_overview_student_tile_block' ), 'on' ); ?> />
					<span class="ir-slider round"></span>
					</label>
					<div class="ir-switch-info-new">
					<?php esc_html_e( 'Student Tile', 'wdm_instructor_role' ); ?>
					</div>
				</div>

				<div class="ir-overview-menu-setting">
					<label for="ir_frontend_overview_submissions_tile_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_overview_submissions_tile_block" id="ir_frontend_overview_submissions_tile_block" <?php checked( ir_get_settings( 'ir_frontend_overview_submissions_tile_block' ), 'on' ); ?> />
					<span class="ir-slider round"></span>
					</label>
					<div class="ir-switch-info-new">
					<?php esc_html_e( 'Submissions Tile', 'wdm_instructor_role' ); ?>
					</div>
				</div>

				<div class="ir-overview-menu-setting">
					<label for="ir_frontend_overview_quiz_attempts_tile_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_overview_quiz_attempts_tile_block" id="ir_frontend_overview_quiz_attempts_tile_block" <?php checked( ir_get_settings( 'ir_frontend_overview_quiz_attempts_tile_block' ), 'on' ); ?> />
					<span class="ir-slider round"></span>
					</label>
					<div class="ir-switch-info-new">
					<?php
					echo esc_html(
						sprintf(
						/* translators: Quiz Label */
							__( '%s Attempts Tile', 'wdm_instructor_role' ),
							\LearnDash_Custom_Label::get_label( 'quiz' )
						)
					);
					?>
					</div>
				</div>

				<div class="ir-overview-menu-setting">
					<label for="ir_frontend_overview_course_progress_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_overview_course_progress_block" id="ir_frontend_overview_course_progress_block" <?php checked( ir_get_settings( 'ir_frontend_overview_course_progress_block' ), 'on' ); ?> />
					<span class="ir-slider round"></span>
					</label>
					<div class="ir-switch-info-new">
					<?php
					echo esc_html(
						sprintf(
							/* translators: Course Label */
							__( '%s Progress', 'wdm_instructor_role' ),
							\LearnDash_Custom_Label::get_label( 'course' )
						)
					);
					?>
					</div>
				</div>

				<div class="ir-overview-menu-setting">
					<label for="ir_frontend_overview_top_courses_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_overview_top_courses_block" id="ir_frontend_overview_top_courses_block" <?php checked( ir_get_settings( 'ir_frontend_overview_top_courses_block' ), 'on' ); ?> />
					<span class="ir-slider round"></span>
					</label>
					<div class="ir-switch-info-new">
					<?php
					echo esc_html(
						sprintf(
							/* translators: Course Label */
							__( 'Top %s', 'wdm_instructor_role' ),
							\LearnDash_Custom_Label::get_label( 'courses' )
						)
					);
					?>
					</div>
				</div>

				<div class="ir-overview-menu-setting">
					<label for="ir_frontend_overview_earnings_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_overview_earnings_block" id="ir_frontend_overview_earnings_block" <?php checked( ir_get_settings( 'ir_frontend_overview_earnings_block' ), 'on' ); ?> />
					<span class="ir-slider round"></span>
					</label>
					<div class="ir-switch-info-new">
					<?php esc_html_e( 'Earnings', 'wdm_instructor_role' ); ?>
					</div>
				</div>

				<div class="ir-overview-menu-setting">
					<label for="ir_frontend_overview_submissions_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_overview_submissions_block" id="ir_frontend_overview_submissions_block" <?php checked( ir_get_settings( 'ir_frontend_overview_submissions_block' ), 'on' ); ?> />
					<span class="ir-slider round"></span>
					</label>
					<div class="ir-switch-info-new">
					<?php esc_html_e( 'Latest Submissions', 'wdm_instructor_role' ); ?>
					</div>
				</div>
						</div>
					</div>
					<div class="dashboard-menu-item">
						<div class="flex-row">
						<label for="ir_frontend_courses_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_courses_block" id="ir_frontend_courses_block" <?php checked( ir_get_settings( 'ir_frontend_courses_block' ), 'on' ); ?>/>
					<span class="ir-slider round"></span>
					</label>
								<div class="ir-switch-info-new">
									<?php esc_html_e( 'Courses', 'wdm_instructor_role' ); ?>
								</div>
						</div>
					</div>
					<div class="dashboard-menu-item">
						<div class="flex-row">
						<label for="ir_frontend_quizzes_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_quizzes_block" id="ir_frontend_quizzes_block" <?php checked( ir_get_settings( 'ir_frontend_quizzes_block' ), 'on' ); ?>/>
					<span class="ir-slider round"></span>
					</label>
								<div class="ir-switch-info-new">
									<?php esc_html_e( 'Quizzes', 'wdm_instructor_role' ); ?>
								</div>
						</div>
					</div>
					<div class="dashboard-menu-item" id="hidden-item">
						<div class="flex-row">
						<label for="ir_frontend_products_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_products_block" id="ir_frontend_products_block" <?php checked( ir_get_settings( 'ir_frontend_products_block' ), 'on' ); ?>/>
					<span class="ir-slider round"></span>
					</label>
								<div class="ir-switch-info-new">
									<?php esc_html_e( 'Products', 'wdm_instructor_role' ); ?>
								</div>
						</div>
					</div>

					<div class="dashboard-menu-item" id="hidden-item">
						<div class="flex-row">
						<label for="ir_frontend_commissions_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_commissions_block" id="ir_frontend_commissions_block" <?php checked( ir_get_settings( 'ir_frontend_commissions_block' ), 'on' ); ?>/>
					<span class="ir-slider round"></span>
					</label>
								<div class="ir-switch-info-new">
									<?php esc_html_e( 'Commissions', 'wdm_instructor_role' ); ?>
								</div>
						</div>
					</div>
					<div class="dashboard-menu-item" id="hidden-item">
						<div class="flex-row">
						<label for="ir_frontend_assignments_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_assignments_block" id="ir_frontend_assignments_block" <?php checked( ir_get_settings( 'ir_frontend_assignments_block' ), 'on' ); ?>/>
					<span class="ir-slider round"></span>
					</label>
								<div class="ir-switch-info-new">
									<?php esc_html_e( 'Assignments', 'wdm_instructor_role' ); ?>
								</div>
						</div>
					</div>
					<div class="dashboard-menu-item" id="hidden-item">
						<div class="flex-row">
						<label for="ir_frontend_essays_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_essays_block" id="ir_frontend_essays_block" <?php checked( ir_get_settings( 'ir_frontend_essays_block' ), 'on' ); ?>/>
					<span class="ir-slider round"></span>
					</label>
								<div class="ir-switch-info-new">
									<?php esc_html_e( 'Essays', 'wdm_instructor_role' ); ?>
								</div>
						</div>
					</div>

					<div class="dashboard-menu-item" id="hidden-item">
						<div class="flex-row">
						<label for="ir_frontend_quiz_attempts_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_quiz_attempts_block" id="ir_frontend_quiz_attempts_block" <?php checked( ir_get_settings( 'ir_frontend_quiz_attempts_block' ), 'on' ); ?>/>
					<span class="ir-slider round"></span>
					</label>
								<div class="ir-switch-info-new">
								<?php
								echo esc_html(
									sprintf(
									/* translators: Quiz Label */
										__( '%s Attempts', 'wdm_instructor_role' ),
										\LearnDash_Custom_Label::get_label( 'quiz' )
									)
								);
								?>
								</div>
						</div>
					</div>
					<div class="dashboard-menu-item" id="hidden-item">
						<div class="flex-row">
						<label for="ir_frontend_comments_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_comments_block" id="ir_frontend_comments_block" <?php checked( ir_get_settings( 'ir_frontend_comments_block' ), 'on' ); ?>/>
					<span class="ir-slider round"></span>
					</label>
								<div class="ir-switch-info-new">
									<?php esc_html_e( 'Comments', 'wdm_instructor_role' ); ?>
								</div>
						</div>
					</div>

					<div class="dashboard-menu-item" id="hidden-item">
						<div class="flex-row">
						<label for="ir_frontend_course_reports_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_course_reports_block" id="ir_frontend_course_reports_block" <?php checked( ir_get_settings( 'ir_frontend_course_reports_block' ), 'on' ); ?>/>
					<span class="ir-slider round"></span>
					</label>
								<div class="ir-switch-info-new">
								<?php
								echo esc_html(
									sprintf(
										/* translators: Course Label */
										__( '%s Reports', 'wdm_instructor_role' ),
										\LearnDash_Custom_Label::get_label( 'course' )
									)
								);
								?>
								</div>
						</div>
					</div>
					<div class="dashboard-menu-item" id="hidden-item">
						<div class="flex-row">
						<label for="ir_frontend_groups_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_groups_block" id="ir_frontend_groups_block" <?php checked( ir_get_settings( 'ir_frontend_groups_block' ), 'on' ); ?>/>
					<span class="ir-slider round"></span>
					</label>
								<div class="ir-switch-info-new">
								<?php
								echo esc_html(
									sprintf(
										\LearnDash_Custom_Label::get_label( 'group' )
									)
								);
								?>
								</div>
						</div>
					</div>

					<div class="dashboard-menu-item">
						<div class="flex-row">
						<label for="ir_frontend_settings_block" class="ir-switch">
					<input type="checkbox" name="ir_frontend_settings_block" id="ir_frontend_settings_block" <?php checked( ir_get_settings( 'ir_frontend_settings_block' ), 'on' ); ?>/>
					<span class="ir-slider round"></span>
					</label>
								<div class="ir-switch-info-new">
									<?php esc_html_e( 'Settings', 'wdm_instructor_role' ); ?>
								</div>
						</div>
					</div>

					<div class="dashboard-menu-item">
						<div class="flex-row" style="justify-content: center;">
							<a class="show-all" id="show-all" >
								<?php esc_html_e( 'Show All', 'wdm_instructor_role' ); ?>
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-down" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 9l6 6l6 -6" /></svg>
							</a>
							<a class="show-less" id="show-less" style="display:none;">
								<?php esc_html_e( 'Show Less', 'wdm_instructor_role' ); ?>
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-up" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 15l6 -6l6 6" /></svg>
							</a>
						</div>
					</div>

				</div>
		<?php endif; ?>
		<p style="text-align:<?php echo ( is_rtl() ) ? 'left' : 'right'; ?>">
				<input id="ir_frontend_settings_save" type="submit" class="button button-primary ir-primary-btn" name="ir_frontend_settings_save" value="<?php esc_html_e( 'Save Settings', 'wdm_instructor_role' ); ?>">
				<?php wp_nonce_field( 'ir_frontend_settings_nonce', 'ir_nonce' ); ?>
			</p>
			</div>
		</form>
		<!-- Edit appearance when gutenberg disabled -->
<div id="appearance_settings_fd" class="ir-appearance-fd ir-tab <?php echo ( $ir_is_gutenberg_enabled ) ? 'ir-disable' : ''; ?>">
<div class="ir-inline-flex align-center ir-primary-color-setting ir-back ir-back-gd-settings">
				<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-narrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l4 4" /><path d="M5 12l4 -4" /></svg>
				<span class="">Back</span>
			</div>
			<div class="ir-flex justify-apart align-center">
				<div class="ir-heading-wrap">
					<div class="ir-tab-heading">
					<?php
					echo esc_html(
						/* translators: Course Label. */
						sprintf( __( 'Appearance', 'wdm_instructor_role' ), \LearnDash_Custom_Label::get_label( 'course' ) )
					);
					?>
					</div>
				</div>
			</div>
	<form id="ir_frontend_dashboard_appearance_settings" method="post">
		<p class="ir-tab-desc-note">
			<?php esc_html_e( 'These settings will help you style the dashboard as per you needs.', 'wdm_instructor_role' ); ?>
		<a href="https://learndash.com/support/docs/add-ons/how-to-customize-the-frontend-dashboard-gutenberg-editor-and-global-settings"><?php esc_html_e( 'Learn more', 'wdm_instructor_role' ); ?></a>
		</p>
		<div class="ir-no-block-editor">
		<div class="ir-no-block-editor-img">
			<span class="dashicons dashicons-info-outline"></span>
		</div>
		<div class="ir-no-block-editor-content">
			<p>
			<?php esc_html_e( 'The Gutenberg Editor is disabled on your site but still you can customize the frontend dashboard by using these global settings', 'wdm_instructor_role' ); ?>
			</p>
			<p>
			<?php
			echo wp_kses(
				sprintf(
				/* translators: 1. Dashboard URL 2.Dashboard Page Title */
					__( 'Note: <strong>These settings will only work on the frontend dashboard set on the page</strong> - <a href="%1$s">%2$s</a>. To set any other page as frontend dashboard go to the <strong>view tab</strong>', 'wdm_instructor_role' ),
					get_permalink( $dashboard_page_id ),
					get_the_title( $dashboard_page_id )
				),
				[
					'strong' => [],
					'a'      => [
						'href'   => [],
						'target' => [],
					],
				]
			);
			?>
			</p>
		</div>
		</div>
		<p class="ir-setting-title-2"><?php esc_html_e( 'Layout', 'wdm_instructor_role' ); ?></p>
		<div class="ir-custom-color-settings">
		<p class="ir-setting-title-3"><?php esc_html_e( 'Color Schemes', 'wdm_instructor_role' ); ?></p>
		<div class="ir-color-patterns">
			<div class="ir-color-scheme <?php echo ( 'calm_ocean' === $ir_frontend_appearance_color_scheme ) ? 'ir-active-color' : ''; ?>">
			<div class="ir-color-scheme-name" name="calm_ocean">
				<?php esc_html_e( 'Calm ocean', 'wdm_instructor_role' ); ?>
				<span class="<?php echo ( 'calm_ocean' !== $ir_frontend_appearance_color_scheme ) ? 'ir-hide' : ''; ?> dashicons dashicons-yes-alt" style="color: rgb(0, 127, 255);"></span>
			</div>
			<div class="ir-colors" name="calm_ocean">
				<span name="calm_ocean" style="background-color: #2E353C; border: 1px solid rgb(255, 255, 255);"></span>
				<span name="calm_ocean" style="background-color: #FFFFFF; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
				<span name="calm_ocean" style="background-color: #2067FA; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
			</div>
			<input style="display:none;" type="radio" name="ir_frontend_appearance_color_scheme" class="ir_frontend_appearance_color_scheme" value="calm_ocean" <?php checked( $ir_frontend_appearance_color_scheme, 'calm_ocean' ); ?>>
			</div>
			<div class="ir-color-scheme <?php echo ( 'wise_pink' === $ir_frontend_appearance_color_scheme ) ? 'ir-active-color' : ''; ?>">
			<div class="ir-color-scheme-name" name="wise_pink">
				<?php esc_html_e( 'Wise Pink', 'wdm_instructor_role' ); ?>
				<span class="<?php echo ( 'wise_pink' !== $ir_frontend_appearance_color_scheme ) ? 'ir-hide' : ''; ?> dashicons dashicons-yes-alt" style="color: rgb(0, 127, 255);"></span>
			</div>
			<div class="ir-colors" name="wise_pink">
				<span name="wise_pink" style="background-color: #3B2E3B; border: 1px solid rgb(255, 255, 255);"></span>
				<span name="wise_pink" style="background-color: #FFFFFF; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
				<span name="wise_pink" style="background-color: #E339D8; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
			</div>
			<input style="display:none;" type="radio" name="ir_frontend_appearance_color_scheme" class="ir_frontend_appearance_color_scheme" value="wise_pink" <?php checked( $ir_frontend_appearance_color_scheme, 'wise_pink' ); ?>>
			</div>
			<div class="ir-color-scheme <?php echo ( 'friendly_mustang' === $ir_frontend_appearance_color_scheme ) ? 'ir-active-color' : ''; ?>">
			<div class="ir-color-scheme-name" name="friendly_mustang">
				<?php esc_html_e( 'Friendly Mustang', 'wdm_instructor_role' ); ?>
				<span class="<?php echo ( 'friendly_mustang' !== $ir_frontend_appearance_color_scheme ) ? 'ir-hide' : ''; ?> dashicons dashicons-yes-alt" style="color: rgb(0, 127, 255);"></span>
			</div>
			<div class="ir-colors" name="friendly_mustang">
				<span name="friendly_mustang" style="background-color: #3C352E; border: 1px solid rgb(255, 255, 255);"></span>
				<span name="friendly_mustang" style="background-color: #FFFFFF; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
				<span name="friendly_mustang" style="background-color: #FC9618; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
			</div>
			<input style="display:none;" type="radio" name="ir_frontend_appearance_color_scheme" class="ir_frontend_appearance_color_scheme" value="friendly_mustang" <?php checked( $ir_frontend_appearance_color_scheme, 'friendly_mustang' ); ?>>
			</div>
			<div class="ir-color-scheme <?php echo ( 'natural_green' === $ir_frontend_appearance_color_scheme ) ? 'ir-active-color' : ''; ?>">
			<div class="ir-color-scheme-name" name="natural_green">
				<?php esc_html_e( 'Natural Green', 'wdm_instructor_role' ); ?>
				<span class="<?php echo ( 'natural_green' !== $ir_frontend_appearance_color_scheme ) ? 'ir-hide' : ''; ?> dashicons dashicons-yes-alt" style="color: rgb(0, 127, 255);"></span>
			</div>
			<div class="ir-colors" name="natural_green">
				<span name="natural_green" style="background-color: #354538; border: 1px solid rgb(255, 255, 255);"></span>
				<span name="natural_green" style="background-color: #00533A; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
				<span name="natural_green" style="background-color: #21CF3D; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
			</div>
			<input style="display:none;" type="radio" name="ir_frontend_appearance_color_scheme" class="ir_frontend_appearance_color_scheme" value="natural_green" <?php checked( $ir_frontend_appearance_color_scheme, 'natural_green' ); ?>>
			</div>
			<div class="ir-color-scheme <?php echo ( 'royal_purple' === $ir_frontend_appearance_color_scheme ) ? 'ir-active-color' : ''; ?>">
			<div class="ir-color-scheme-name" name="royal_purple">
				<?php esc_html_e( 'Royal Purple', 'wdm_instructor_role' ); ?>
				<span class="<?php echo ( 'royal_purple' !== $ir_frontend_appearance_color_scheme ) ? 'ir-hide' : ''; ?> dashicons dashicons-yes-alt" style="color: rgb(0, 127, 255);"></span>
			</div>
			<div class="ir-colors" name="royal_purple">
				<span name="royal_purple" style="background-color: #3F3444; border: 1px solid rgb(255, 255, 255);"></span>
				<span name="royal_purple" style="background-color: #20003F; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
				<span name="royal_purple" style="background-color: #954FB6; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
			</div>
			<input style="display:none;" type="radio" name="ir_frontend_appearance_color_scheme" class="ir_frontend_appearance_color_scheme" value="royal_purple" <?php checked( $ir_frontend_appearance_color_scheme, 'royal_purple' ); ?>>
			</div>
			<div class="ir-color-scheme <?php echo ( 'custom' === $ir_frontend_appearance_color_scheme ) ? 'ir-active-color' : ''; ?>">
			<div class="ir-color-scheme-name" name="custom">
				<?php esc_html_e( 'Custom', 'wdm_instructor_role' ); ?>
				<span class="<?php echo ( 'custom' !== $ir_frontend_appearance_color_scheme ) ? 'ir-hide' : ''; ?> dashicons dashicons-yes-alt" style="color: rgb(0, 127, 255);"></span>
			</div>
			<div class="ir-colors" name="custom">
				<span name="custom" style="background-color: #364246; border: 1px solid rgb(255, 255, 255);"></span>
				<span name="custom" style="background-color: #96B4CC; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
				<span name="custom" style="background-color: #021768; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
			</div>
			<input style="display:none;" type="radio" name="ir_frontend_appearance_color_scheme" class="ir_frontend_appearance_color_scheme" value="custom" <?php checked( $ir_frontend_appearance_color_scheme, 'custom' ); ?>>
			</div>
		</div>
		<div class="ir-custom-color-pattern" <?php echo ( 'custom' !== $ir_frontend_appearance_color_scheme ) ? 'style="display:none;"' : ''; ?>>
			<p class="ir-setting-title-4"><?php esc_html_e( 'Custom colors', 'wdm_instructor_role' ); ?></p>
			<div>
			<div class="ir-custom-color">
				<div class="ir-custom-color-label">
				<?php esc_html_e( 'Primary', 'wdm_instructor_role' ); ?>
				<div class="tooltip">
					<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
					<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'Primary color is the main color of the theme used in Buttons, links, icons etc.', 'wdm_instructor_role' ); ?></span>
				</div>
				</div>
				<div class="ir-custom-color-value">
				<input type="color" name="ir_frontend_appearance_custom_primary" id="ir_frontend_appearance_custom_primary" value="<?php echo esc_attr( $ir_frontend_appearance_custom_primary ); ?>">
				</div>
			</div>
			<div class="ir-custom-color">
				<div class="ir-custom-color-label">
				<?php esc_html_e( 'Accent', 'wdm_instructor_role' ); ?>
				<div class="tooltip">
					<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
					<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'Accents color is used in side bar CTA selection.', 'wdm_instructor_role' ); ?></span>
				</div>
				</div>
				<div class="ir-custom-color-value">
				<input type="color" name="ir_frontend_appearance_custom_accent" id="ir_frontend_appearance_custom_accent" value="<?php echo esc_attr( $ir_frontend_appearance_custom_accent ); ?>">
				</div>
			</div>
			<div class="ir-custom-color">
				<div class="ir-custom-color-label">
				<?php esc_html_e( 'Background', 'wdm_instructor_role' ); ?>
				<div class="tooltip">
					<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
					<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'This color is used as the background color.', 'wdm_instructor_role' ); ?></span>
				</div>
				</div>
				<div class="ir-custom-color-value">
				<input type="color" name="ir_frontend_appearance_custom_background" id="ir_frontend_appearance_custom_background" value="<?php echo esc_attr( $ir_frontend_appearance_custom_background ); ?>">
				</div>
			</div>
			<div class="ir-custom-color">
				<div class="ir-custom-color-label">
				<?php esc_html_e( 'Headings', 'wdm_instructor_role' ); ?>
				<div class="tooltip">
					<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
					<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'This color is used for all the headings of text.', 'wdm_instructor_role' ); ?></span>
				</div>
				</div>
				<div class="ir-custom-color-value">
				<input type="color" name="ir_frontend_appearance_custom_headings" id="ir_frontend_appearance_custom_headings" value="<?php echo esc_attr( $ir_frontend_appearance_custom_headings ); ?>">
				</div>
			</div>
			<div class="ir-custom-color">
				<div class="ir-custom-color-label">
				<?php esc_html_e( 'Text', 'wdm_instructor_role' ); ?>
				<div class="tooltip">
					<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
					<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'This color is used for normal text and side bar icon.', 'wdm_instructor_role' ); ?></span>
				</div>
				</div>
				<div class="ir-custom-color-value">
				<input type="color" name="ir_frontend_appearance_custom_text" id="ir_frontend_appearance_custom_text" value="<?php echo esc_attr( $ir_frontend_appearance_custom_text ); ?>">
				</div>
			</div>
			<div class="ir-custom-color">
				<div class="ir-custom-color-label">
				<?php esc_html_e( 'Border', 'wdm_instructor_role' ); ?>
				<div class="tooltip">
					<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
					<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'This color is used for borders.', 'wdm_instructor_role' ); ?></span>
				</div>
				</div>
				<div class="ir-custom-color-value">
				<input type="color" name="ir_frontend_appearance_custom_border" id="ir_frontend_appearance_custom_border" value="<?php echo esc_attr( $ir_frontend_appearance_custom_border ); ?>">
				</div>
			</div>
			</div>
			<div>
			<div class="ir-advanced-color-title">
				<span><?php esc_html_e( 'Advance Colors', 'wdm_instructor_role' ); ?></span>
				<span class="dashicons dashicons-arrow-down-alt2"></span>
			</div>
			<div class="ir-advanced-colors">
				<div class="ir-custom-color">
				<div class="ir-custom-color-label">
					<?php esc_html_e( 'Sidebar Background', 'wdm_instructor_role' ); ?>
					<div class="tooltip">
					<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
					<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'This color is used for Side bar background.', 'wdm_instructor_role' ); ?></span>
				</div>
				</div>
				<div class="ir-custom-color-value">
					<input type="color" name="ir_frontend_appearance_custom_side_bg" id="ir_frontend_appearance_custom_side_bg" value="<?php echo esc_attr( $ir_frontend_appearance_custom_side_bg ); ?>">
				</div>
				</div>
				<div class="ir-custom-color">
				<div class="ir-custom-color-label">
					<?php esc_html_e( 'Sidebar Menu Text', 'wdm_instructor_role' ); ?>
					<div class="tooltip">
					<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
					<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'This color is used for Side bar menu text.', 'wdm_instructor_role' ); ?></span>
				</div>
				</div>
				<div class="ir-custom-color-value">
					<input type="color" name="ir_frontend_appearance_custom_side_mt" id="ir_frontend_appearance_custom_side_mt" value="<?php echo esc_attr( $ir_frontend_appearance_custom_side_mt ); ?>">
				</div>
				</div>
				<div class="ir-custom-color">
				<div class="ir-custom-color-label">
					<?php esc_html_e( 'Text - Light', 'wdm_instructor_role' ); ?>
					<div class="tooltip">
					<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
					<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'This color is used for secondary text and icons.', 'wdm_instructor_role' ); ?></span>
				</div>
				</div>
				<div class="ir-custom-color-value">
					<input type="color" name="ir_frontend_appearance_custom_text_light" id="ir_frontend_appearance_custom_text_light" value="<?php echo esc_attr( $ir_frontend_appearance_custom_text_light ); ?>">
				</div>
				</div>
				<div class="ir-custom-color">
				<div class="ir-custom-color-label">
					<?php esc_html_e( 'Text - Extra Light', 'wdm_instructor_role' ); ?>
					<div class="tooltip">
					<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
					<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'This color is used for Placeholder input text and icons.', 'wdm_instructor_role' ); ?></span>
				</div>
				</div>
				<div class="ir-custom-color-value">
					<input type="color" name="ir_frontend_appearance_custom_text_ex_light" id="ir_frontend_appearance_custom_text_ex_light" value="<?php echo esc_attr( $ir_frontend_appearance_custom_text_ex_light ); ?>">
				</div>
				</div>
				<div class="ir-custom-color">
				<div class="ir-custom-color-label"><?php esc_html_e( 'Text - Primary Button', 'wdm_instructor_role' ); ?><div class="tooltip">
					<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
					<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'This color is used for text in Primary buttons.', 'wdm_instructor_role' ); ?></span>
					</div>
				</div>
				<div class="ir-custom-color-value">
					<input type="color" name="ir_frontend_appearance_custom_text_primary_btn" id="ir_frontend_appearance_custom_text_primary_btn" value="<?php echo esc_attr( $ir_frontend_appearance_custom_text_primary_btn ); ?>">
				</div>
				</div>
			</div>
			</div>
		</div>
		</div>
		<div class="ir-additional-settings">
		<p class="ir-setting-title-2"><?php esc_html_e( 'Font', 'wdm_instructor_role' ); ?></p>
		<div>
			<div class="ir-setting-section">
			<span class="ir-setting-label"><?php esc_html_e( 'Font Family : ', 'wdm_instructor_role' ); ?></span>
			<div class="ir-dropdown">
				<select class="ir-setting-select" name="ir_frontend_appearance_font_family" id="ir_frontend_appearance_font_family">
				<?php // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Should be checked later. ?>
				<?php foreach ( $fonts as $key => $value ) : ?>
					<option value='<?php echo esc_attr( $value ); ?>' <?php selected( $ir_frontend_appearance_font_family, $value ); ?>><?php echo esc_attr( $value ); ?></option>
				<?php endforeach; ?>
				</select>
				<span class="dashicons dashicons-arrow-down-alt2"></span>
			</div>
			</div>
			<div class="ir-setting-section">
			<span class="ir-setting-label"><?php esc_html_e( 'Font Size : ', 'wdm_instructor_role' ); ?></span>
			<div class="ir-dropdown">
				<select class="ir-setting-select" name="ir_frontend_appearance_font_size" id="ir_frontend_appearance_font_size">
				<option value="14px" <?php selected( $ir_frontend_appearance_font_size, '14px' ); ?>>
					<?php esc_html_e( 'Small', 'wdm_instructor_role' ); ?>
				</option>
				<option value="16px" <?php selected( $ir_frontend_appearance_font_size, '16px' ); ?>>
					<?php esc_html_e( 'Normal', 'wdm_instructor_role' ); ?>
				</option>
				<option value="18px" <?php selected( $ir_frontend_appearance_font_size, '18px' ); ?>>
					<?php esc_html_e( 'Large', 'wdm_instructor_role' ); ?>
				</option>
				<option value="20px" <?php selected( $ir_frontend_appearance_font_size, '20px' ); ?>>
					<?php esc_html_e( 'Larger', 'wdm_instructor_role' ); ?>
				</option>
				</select>
				<span class="dashicons dashicons-arrow-down-alt2"></span>
			</div>
			</div>
		</div>
		</div>
			<?php wp_nonce_field( 'frontend_dashboard_appearance_settings_nonce', 'ir_nonce' ); ?>
		<p style="text-align:<?php echo ( is_rtl() ) ? 'left' : 'right'; ?>">
			<?php submit_button( __( 'Save Settings', 'wdm_instructor_role' ), 'primary', 'ir-right-align', false ); ?>
		</p>
	</form>
	</div>
		<?php endif; ?>
	</div>


	<div class="dashboard-settings-frontend-instructor-dashboard" style="margin-top:32px;">
		<div class="justified-top ir-border">
			<div class="flex-column">
				<div class="admin-setting-heading" style="margin-bottom:8px;">
					<?php esc_html_e( 'Backend (WP) Instructor Dashboard', 'wdm_instructor_role' ); ?>
				</div>
				<div class="title-desc">
					<span style="color:#868E96; font-weight:600;"><?php esc_html_e( 'Note: ', 'wdm_instructor_role' ); ?></span>
					<?php esc_html_e( 'Backend (WP) Instructor Dashboard Settings can be disable on if the Frontend Dashboard is active. You can configure the frontend Dashboard from here ', 'wdm_instructor_role' ); ?>
				</div>
			</div>

			<div class="flex-row">
				<label for="ir_disable_backend_dashboard" class="ir-switch ir-ajax">
					<input type="checkbox" name="ir_disable_backend_dashboard" id="ir_disable_backend_dashboard" <?php checked( ir_get_settings( 'ir_disable_backend_dashboard' ), '' ); ?>/>
					<span class="ir-slider round"></span>
				</label>
			</div>
		</div>
			<div class="justified-center ir-hide-menu-setting ir-flex-js ir-disable-backend <?php echo ir_get_settings( 'ir_disable_backend_dashboard' ) ? 'ir-hide' : ''; ?> ">
				<div style="font-size:16px; font-weight:700; color: #2E353C;">
						<?php esc_html_e( 'Menu settings', 'wdm_instructor_role' ); ?>
						<div class="title-desc">
						<?php esc_html_e( 'Click on save after changing Backend (WP) Instructor Dashboard Settings. If not then the changes will not be applied', 'wdm_instructor_role' ); ?>
						</div>
				</div>
				<div style="display: flex; gap: 16px;">
				<a class="button button-primary new-dashboard ir-overview-settings">
							<?php esc_html_e( 'Overview Page', 'wdm_instructor_role' ); ?>
				</a>
				<a class="button button-primary new-dashboard ir-edit-das-settings">
							<span><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-palette" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21a9 9 0 0 1 0 -18c4.97 0 9 3.582 9 8c0 1.06 -.474 2.078 -1.318 2.828c-.844 .75 -1.989 1.172 -3.182 1.172h-2.5a2 2 0 0 0 -1 3.75a1.3 1.3 0 0 1 -1 2.25" /><path d="M8.5 10.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12.5 7.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M16.5 10.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /></svg></span>
							<?php esc_html_e( 'Edit Appearance', 'wdm_instructor_role' ); ?>
				</a>
				</div>
			</div>
			<div class="ir-backend-dashboard-settings ir-disable-backend <?php echo ir_get_settings( 'ir_disable_backend_dashboard' ) ? 'ir-hide' : ''; ?>">
			<?php
				// Template render.
				ir_get_template(
					INSTRUCTOR_ROLE_ABSPATH . 'modules/templates/settings/ir-backend-dashboard-settings.template.php',
					[
						'instance'   => $instance,
						'banner_img' => $banner_img,
					]
				);
				?>


	</div>



	</div>
</div>

