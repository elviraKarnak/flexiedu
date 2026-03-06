<?php
/**
 * Frontend Dashboard Settings Template.
 *
 * @since 5.0.0
 *
 * @package LearnDash\Instructor_Role
 *
 * cspell:ignore instuctor // ignoring misspelled words that we can't change now.
 */

defined( 'ABSPATH' ) || exit;
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter&family=Open+Sans&display=swap" rel="stylesheet">
<div class="ir-frontend-dashboard-settings">
	<h2><?php esc_html_e( 'Frontend Dashboard', 'wdm_instructor_role' ); ?></h2>
	<div class="ir-settings-banner">
		<div class="ir-banner-img">
			<img src="<?php echo esc_attr( $banner_img ); ?>" alt="<?php echo esc_html_e( 'Introducing Frontend Dashboard', 'wdm_instructor_role' ); ?>">
		</div>
		<div class="ir-banner-content">
			<div class="ir-banner-title"><?php echo esc_html_e( 'Introducing Frontend Dashboard', 'wdm_instructor_role' ); ?></div>
			<div class="ir-banner-text">
				<?php
				echo esc_html(
					sprintf(
					/* translators: 1. Courses Label 2.Quizzes Label 3.Courses lowercase label 4. Quizzes lowercase label */
						__( 'This first version of the frontend dashboard consists of four tabs: Overview, %1$s, %2$s, and Settings. It empowers instructors to effortlessly create and manage their %3$s and %4$s.', 'wdm_instructor_role' ),
						\LearnDash_Custom_Label::get_label( 'courses' ),
						\LearnDash_Custom_Label::get_label( 'quizzes' ),
						\LearnDash_Custom_Label::label_to_lower( 'courses' ),
						\LearnDash_Custom_Label::label_to_lower( 'quizzes' )
					)
				);
				?>
				<a href="https://learndash.com/support/docs/add-ons/frontend-dashboard-installation-guide" target="_blank"><?php esc_html_e( 'Learn More', 'wdm_instructor_role' ); ?></a>
			</div>
			<div class="ir-banner-text">
				<?php
				echo wp_kses(
					__( '<strong>If all your instructor\'s tasks can be performed using the frontend dashboard, you now have the option to switch to it.</strong> In an upcoming update, we will incorporate all instructor functionalities into the frontend dashboard.', 'wdm_instructor_role' ),
					[ 'strong' => [] ]
				);
				?>
			</div>
			<div class="ir-banner-text">
				<?php
				echo wp_kses(
					__( 'From now on, our <strong>previous instructor dashboard accessed at /wp-admin/ will be referred to as the Backend (WP) Dashboard.</strong> You have complete control over which dashboard to utilize for your instructors based on your preferences and requirements.', 'wdm_instructor_role' ),
					[ 'strong' => [] ]
				);
				?>
			</div>
			<div class="ir-banner-help-note">
			<?php
				echo wp_kses(
					__( 'You can enable / disable this Backend(WP) Instructor Dashboard  from the General setting area. <a href="?page=instuctor&tab=settings" target="_blank">Click here</a>.', 'wdm_instructor_role' ),
					[
						'a' => [
							'href'   => [],
							'target' => [],
						],
					]
				);
				?>
			</div>
		</div>
	</div>
	<div>
		<ul class="ir-tabs-nav">
			<li <?php echo ( 'view' === $active_menu ) ? 'class="active"' : ''; ?>><a href="#view_settings"><?php esc_html_e( 'View', 'wdm_instructor_role' ); ?></a></li>
			<?php if ( $ir_is_gutenberg_enabled ) : ?>
				<div class="tooltip dark">
					<li>
						<a class="ir-disable"><?php esc_html_e( 'Menu Settings', 'wdm_instructor_role' ); ?></a>
						<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'The Gutenberg editor is active on your site and you can fully customize the frontend dashboard using it instead of these global settings. Please refer to the help section to understand how to customize the dashboard', 'wdm_instructor_role' ); ?></span>
					</li>
					<li>
						<a class="ir-disable"><?php esc_html_e( 'Overview Page', 'wdm_instructor_role' ); ?></a>
					</li>
					<li>
						<a class="ir-disable"><?php esc_html_e( 'Appearance', 'wdm_instructor_role' ); ?></a>
					</li>
				</div>
			<?php else : ?>
				<li <?php echo ( 'menu' === $active_menu ) ? 'class="active"' : ''; ?>><a href="#menu_settings" class="<?php echo ( $ir_is_gutenberg_enabled ) ? 'ir-disable' : ''; ?>"><?php esc_html_e( 'Menu Settings', 'wdm_instructor_role' ); ?></a></li>
				<li><a href="#overview_page" class="<?php echo ( $ir_is_gutenberg_enabled ) ? 'ir-disable' : ''; ?>"><?php esc_html_e( 'Overview Page', 'wdm_instructor_role' ); ?></a></li>
				<li><a href="#appearance_settings" class="<?php echo ( $ir_is_gutenberg_enabled ) ? 'ir-disable' : ''; ?>"><?php esc_html_e( 'Appearance', 'wdm_instructor_role' ); ?></a></li>
			<?php endif; ?>
		</ul>
		<section class="ir-tabs-content">
			<div id="view_settings" class="ir-tab <?php echo ( 'view' === $active_menu ) ? 'active' : ''; ?>">
				<div>
					<h3><?php esc_html_e( 'Instructor Frontend Dashboard Page', 'wdm_instructor_role' ); ?></h2>
					<?php if ( $is_dashboard_launched ) : ?>
						<div class="ir-tab-info">
							<p>
								<?php esc_html_e( 'Select the page on which the instructor dashboard is configured', 'wdm_instructor_role' ); ?>
							</p>
							<p>
								<?php
								echo wp_kses(
									sprintf(
										__( 'or <a href="%s">create a new dashboard page</a>', 'wdm_instructor_role' ),
										$create_frontend_dashboard_link
									),
									[
										'a' => [
											'href' => [],
										],
									]
								);
								?>
							</p>
						</div>
						<form id="ir_frontend_dashboard_view_settings" method="post">
							<div class="ir-view-setting">
								<span>
									<?php esc_html_e( 'Select Frontend Dashboard Page', 'wdm_instructor_role' ); ?>
								</span>
								<div class="ir-dropdown">
									<?php
										wp_dropdown_pages(
											[
												'name'     => 'ir_frontend_dashboard_page',
												'id'       => 'ir_frontend_dashboard_page',
												'sort_column' => 'menu_order',
												'sort_order' => 'ASC',
												'show_option_none' => __( 'Select a Page', 'wdm_instructor_role' ),
												'class'    => 'ir_frontend_dashboard_page',
												'echo'     => 1,
												'selected' => $dashboard_page_id,
											]
										);
									?>
									<span class="dashicons dashicons-arrow-down-alt2"></span>
									</div>
							</div>
							<div>
								<?php if ( $dashboard_page_id ) : ?>
									<a class="ir-primary-btn" href="<?php echo esc_attr( get_permalink( $dashboard_page_id ) ); ?>" target="_blank"><?php esc_html_e( 'View Dashboard', 'wdm_instructor_role' ); ?></a>
								<?php endif; ?>
								<?php if ( ! $ir_enable_frontend_dashboard ) : ?>
									<div class="ir-section-warning">
										<p>
											<?php
											echo esc_html(
												sprintf(
												/* translators: Course Label */
													__( 'Frontend %s Creator is disabled', 'wdm_instructor_role' ),
													\LearnDash_Custom_Label::get_label( 'course' )
												)
											);
											?>
										</p>
										<div>
											<?php
											echo esc_html(
												sprintf(
													/* translators: Course Label */
													__( 'The Frontend %s Creator can be enabled from', 'wdm_instructor_role' ),
													\LearnDash_Custom_Label::get_label( 'course' )
												)
											);
											?>
											<a href="?page=instuctor&tab=ir-frontend-dashboard" target="_blank"><?php esc_html_e( 'here', 'wdm_instructor_role' ); ?></a>
										</div>
									</div>
								<?php endif; ?>
								<?php wp_nonce_field( 'frontend_dashboard_view_settings_nonce', 'ir_nonce' ); ?>
								<?php submit_button( __( 'Save Settings', 'wdm_instructor_role' ) ); ?>
							</div>
						</form>
					<?php else : ?>
						<div class="ir-tab-info">
							<?php esc_html_e( 'Configure the setting below and launch the dashboard.', 'wdm_instructor_role' ); ?>
						</div>
						<?php if ( ir_is_gutenberg_enabled() ) : ?>
							<div class="ir-tab-info-2">
								<?php esc_html_e( 'Upon clicking on "create dashboard," a new page along with the dashboard  will be created on you site’s front-end. You will be redirected to this page - saved as a draft - providing you with an opportunity to personalize this page before making it live  on the site', 'wdm_instructor_role' ); ?>
							</div>
						<?php else : ?>
							<div class="ir-tab-info-2">
								<?php esc_html_e( 'Upon clicking on "create dashboard," a new page along with the dashboard  will be created on your site’s front-end and you will be redirected to this page.', 'wdm_instructor_role' ); ?>
							</div>
						<?php endif; ?>
						<div class="ir-settings-accordion">
							<div class="ir-settings-accordion-title">
								<div class="ir-setting-title-left">
									<?php esc_html_e( 'Settings', 'wdm_instructor_role' ); ?>
								</div>
								<div class="ir-setting-title-right">
									<span class="dashicons dashicons-arrow-up-alt2"></span>
								</div>
							</div>
							<div class="ir-settings-accordion-content">
								<div class="ir-menu-setting">
									<label class="ir-switch">
										<input type="checkbox" name="ir_enable_frontend_dashboard" id="ir_enable_frontend_dashboard" checked>
										<span class="ir-slider round"></span>
									</label>
									<div class="ir-switch-info">
										<?php
										echo esc_html(
											sprintf(
												/* translators: 1.Course Label 2.Quiz Label */
												__( 'Enable Frontend %1$s and %2$s Creator', 'wdm_instructor_role' ),
												\LearnDash_Custom_Label::get_label( 'course' ),
												\LearnDash_Custom_Label::get_label( 'quiz' )
											)
										);
										?>
									</div>
								</div>
								<div class="ir-menu-setting">
									<label class="ir-switch">
										<input type="checkbox" name="wdm_id_ir_dash_pri_menu" id="wdm_id_ir_dash_pri_menu">
										<span class="ir-slider round"></span>
									</label>
									<div class="ir-switch-info">
										<?php esc_html_e( 'Add Frontend Dashboard link titled “Instructor Dashboard” to the header menu', 'wdm_instructor_role' ); ?>
									</div>
								</div>
								<div class="ir-menu-setting">
									<label class="ir-switch">
										<input type="checkbox" name="wdm_login_redirect" id="wdm_login_redirect">
										<span class="ir-slider round"></span>
									</label>
									<div class="ir-switch-info">
										<?php esc_html_e( 'Redirect Instructors to Frontend Dashboard upon login', 'wdm_instructor_role' ); ?>
									</div>
								</div>
								<div class="ir-menu-setting">
									<label class="ir-switch">
										<input type="checkbox" name="ir_disable_backend_dashboard" id="ir_disable_backend_dashboard">
										<span class="ir-slider round ir-imp"></span>
									</label>
									<div class="ir-switch-info">
										<?php esc_html_e( 'Disable Backend(WP) Instructor Dashboard ', 'wdm_instructor_role' ); ?>
										<p class="ir-help-note">
											<?php esc_html_e( 'Disable the previous Instructor dashboard and completely restrict Instructors access to  /wp-admin/ area.', 'wdm_instructor_role' ); ?>
											<a href="https://learndash.com/support/docs/add-ons/how-to-disable-the-backend-dashboard-for-instructors/" target="_blank"><?php esc_html_e( 'Learn More', 'wdm_instructor_role' ); ?></a>
										</p>
									</div>
								</div>
							</div>
							<?php wp_nonce_field( 'frontend_dashboard_view_settings_nonce', 'ir_view_nonce' ); ?>
							<button class="ir-primary-btn" id="ir_create_frontend_dashboard"><?php esc_html_e( 'Create Dashboard', 'wdm_instructor_role' ); ?></button>
						</div>
					<?php endif; ?>
				</div>
				<div class="ir-additional-help-section">
					<div class="ir-additional-help-title">
						<span>
							<img src="<?php echo esc_attr( $help_tooltip_img ); ?>" alt="<?php esc_html_e( 'Question Mark', 'wdm_instructor_role' ); ?>">
						</span>
						<span><?php esc_html_e( 'Looking for Help?', 'wdm_instructor_role' ); ?></span>
					</div>
					<hr>
					<div class="ir-additional-help-content">
						<div class="ir-help-desc">
							<?php esc_html_e( 'Refer the following documentation to configure and customize the frontend dashboard', 'wdm_instructor_role' ); ?>
						</div>
						<ul>
							<li><a href="https://learndash.com/support/docs/add-ons/frontend-dashboard-installation-guide/" target="_blank"><?php esc_html_e( 'Frontend Dashboard: Installation Guide', 'wdm_instructor_role' ); ?></a></li>
							<li><a href="https://learndash.com/support/docs/add-ons/frontend-dashboard-for-instructors-gutenberg-blocks-list/" target="_blank"><?php esc_html_e( 'Frontend Dashboard: Gutenberg Block List', 'wdm_instructor_role' ); ?></a></li>
							<li><a href="https://learndash.com/support/docs/add-ons/how-to-customize-the-frontend-dashboard-gutenberg-editor-and-global-settings" target="_blank"><?php esc_html_e( 'How to Customize the Frontend Dashboard', 'wdm_instructor_role' ); ?></a></li>
							<li><a href="https://learndash.com/support/docs/add-ons/how-to-disable-the-backend-dashboard-for-instructors/" target="_blank"><?php esc_html_e( 'How to disable the backend dashboard (WP) for instructors', 'wdm_instructor_role' ); ?></a></li>
						</ul>
					</div>
				</div>
			</div>

			<div id="menu_settings" class="ir-tab <?php echo ( 'menu' === $active_menu ) ? 'active' : ''; ?> <?php echo ( $ir_is_gutenberg_enabled ) ? 'ir-disable' : ''; ?>">
				<h3><?php esc_html_e( 'Menu Settings', 'wdm_instructor_role' ); ?></h3>
				<form id="ir_frontend_dashboard_menu_settings" method="post">
					<p class="ir-tab-desc"><?php esc_html_e( 'Enable / Disable the following functionalities on the frontend Dashboard', 'wdm_instructor_role' ); ?></p>
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
					<div>
						<div class="ir-menu-setting">
							<label for="ir_frontend_overview_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_overview_block" id="ir_frontend_overview_block" <?php checked( $ir_frontend_overview_block, 'on' ); ?>/>
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php esc_html_e( 'Overview', 'wdm_instructor_role' ); ?>
								<p class="ir-help-note">
									<?php esc_html_e( 'It provides a comprehensive summary and snapshot of the important information and metrics useful for instructors', 'wdm_instructor_role' ); ?>
								</p>
							</div>
						</div>
						<div class="ir-menu-setting">
							<label for="ir_frontend_courses_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_courses_block" id="ir_frontend_courses_block" <?php checked( $ir_frontend_courses_block, 'on' ); ?>/>
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php esc_html_e( 'Courses', 'wdm_instructor_role' ); ?>
								<p class="ir-help-note">
									<?php
									echo esc_html(
										sprintf(
											/* translators: Course Label */
											__( 'It shows all the %1$s created by the instructors and shared with them.  Instructors can create, edit, delete %2$s from here', 'wdm_instructor_role' ),
											\LearnDash_Custom_Label::get_label( 'course' ),
											\LearnDash_Custom_Label::label_to_lower( 'courses' )
										)
									);
									?>
								</p>
							</div>
						</div>
						<div class="ir-menu-setting">
							<label for="ir_frontend_quizzes_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_quizzes_block" id="ir_frontend_quizzes_block" <?php checked( $ir_frontend_quizzes_block, 'on' ); ?>/>
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php esc_html_e( 'Quizzes', 'wdm_instructor_role' ); ?>
								<p class="ir-help-note">
									<?php
									echo esc_html(
										sprintf(
											/* translators: Quiz Label */
											__( 'It shows all the %1$s created by the instructors.  Instructors can create, edit, delete %2$s from here', 'wdm_instructor_role' ),
											\LearnDash_Custom_Label::get_label( 'quiz' ),
											\LearnDash_Custom_Label::label_to_lower( 'quizzes' )
										)
									);
									?>
								</p>
							</div>
						</div>
						<div class="ir-menu-setting">
							<label for="ir_frontend_products_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_products_block" id="ir_frontend_products_block" <?php checked( $ir_frontend_products_block, 'on' ); ?>/>
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php esc_html_e( 'Products', 'wdm_instructor_role' ); ?>
								<p class="ir-help-note">
									<?php esc_html_e( 'It shows all the WooCommerce Products created by the instructor/admin. Instructors can create, edit and trash products from here.', 'wdm_instructor_role' ); ?>
								</p>
							</div>
						</div>
						<div class="ir-menu-setting">
							<label for="ir_frontend_commissions_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_commissions_block" id="ir_frontend_commissions_block" <?php checked( $ir_frontend_commissions_block, 'on' ); ?>/>
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php esc_html_e( 'Commissions', 'wdm_instructor_role' ); ?>
								<p class="ir-help-note">
									<?php esc_html_e( 'It shows the earnings and other commissions related information to the admin/instructors. Admins can make commission payouts and view orders and payment logs for instructors.', 'wdm_instructor_role' ); ?>
								</p>
							</div>
						</div>
						<div class="ir-menu-setting">
							<label for="ir_frontend_assignments_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_assignments_block" id="ir_frontend_assignments_block" <?php checked( $ir_frontend_assignments_block, 'on' ); ?>/>
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php esc_html_e( 'Assignments', 'wdm_instructor_role' ); ?>
								<p class="ir-help-note">
									<?php esc_html_e( 'It shows all the student submissions on the lessons and topics created by the instructor/admin. Instructors can assign points and approve/disapprove student assignments from here.', 'wdm_instructor_role' ); ?>
								</p>
							</div>
						</div>
						<div class="ir-menu-setting">
							<label for="ir_frontend_essays_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_essays_block" id="ir_frontend_essays_block" <?php checked( $ir_frontend_essays_block, 'on' ); ?>/>
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php esc_html_e( 'Essays', 'wdm_instructor_role' ); ?>
								<p class="ir-help-note">
									<?php esc_html_e( 'It shows all the student essay submissions on the essay questions created by the instructor/admin. Instructors can assign points and approve/disapprove student essay submissions from here.', 'wdm_instructor_role' ); ?>
								</p>
							</div>
						</div>
						<div class="ir-menu-setting">
							<label for="ir_frontend_quiz_attempts_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_quiz_attempts_block" id="ir_frontend_quiz_attempts_block" <?php checked( $ir_frontend_quiz_attempts_block, 'on' ); ?>/>
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php
								echo esc_html(
									sprintf(
										/* translators: Quiz Label */
										__( '%s Attempts', 'wdm_instructor_role' ),
										\LearnDash_Custom_Label::get_label( 'quiz' )
									)
								);
								?>
								<p class="ir-help-note">
									<?php
									echo esc_html(
										sprintf(
											/* translators: Quiz Label, Quizzes label */
											__( 'It shows all the %1$s attempts made by students on the %2$s created by the instructors.', 'wdm_instructor_role' ),
											\LearnDash_Custom_Label::get_label( 'quiz' ),
											\LearnDash_Custom_Label::label_to_lower( 'quizzes' )
										)
									);
									?>
								</p>
							</div>
						</div>
						<div class="ir-menu-setting">
							<label for="ir_frontend_comments_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_comments_block" id="ir_frontend_comments_block" <?php checked( $ir_frontend_comments_block, 'on' ); ?>/>
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php esc_html_e( 'Comments', 'wdm_instructor_role' ); ?>
								<p class="ir-help-note">
									<?php
									echo esc_html(
										sprintf(
											/* translators: 1.Courses Label 2.Lessons Label 3.Topics Label 4. Quizzes Label */
											__( 'It shows all the comments made on the %1$s, %2$s, %3$s, %4$s and %4$s accessible to the instructors.', 'wdm_instructor_role' ),
											\LearnDash_Custom_Label::label_to_lower( 'courses' ),
											\LearnDash_Custom_Label::label_to_lower( 'lessons' ),
											\LearnDash_Custom_Label::label_to_lower( 'topics' ),
											\LearnDash_Custom_Label::label_to_lower( 'quizzes' )
										)
									);
									?>
								</p>
							</div>
						</div>
						<div class="ir-menu-setting">
							<label for="ir_frontend_course_reports_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_course_reports_block" id="ir_frontend_course_reports_block" <?php checked( $ir_frontend_course_reports_block, 'on' ); ?>/>
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php
								echo esc_html(
									sprintf(
										/* translators: Course Label */
										__( '%s Reports', 'wdm_instructor_role' ),
										\LearnDash_Custom_Label::get_label( 'course' )
									)
								);
								?>
								<p class="ir-help-note">
									<?php
									echo esc_html(
										sprintf(
											/* translators: 1.Courses Label 2.Lessons Label 3.Topics Label 4. Quizzes Label */
											__( 'It shows detailed %1$s and Learner reports accessible to the instructors and administrators.', 'wdm_instructor_role' ),
											\LearnDash_Custom_Label::get_label( 'course' ),
										)
									);
									?>
								</p>
							</div>
						</div>

						<div class="ir-menu-setting">
							<label for="ir_frontend_groups_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_groups_block" id="ir_frontend_groups_block" <?php checked( $ir_frontend_groups_block, 'on' ); ?>/>
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php
								echo esc_html(
									sprintf(
										\LearnDash_Custom_Label::get_label( 'group' )
									)
								);
								?>
								<p class="ir-help-note">
									<?php
									echo esc_html(
										sprintf(
											/* translators: 1.Courses Label 2.Lessons Label 3.Topics Label 4. Quizzes Label */
											__( 'It shows detailed %1$s information accessible to the instructors and administrators.', 'wdm_instructor_role' ),
											\LearnDash_Custom_Label::get_label( 'group' ),
										)
									);
									?>
								</p>
							</div>
						</div>

						<div class="ir-menu-setting">
							<label for="ir_frontend_certificates_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_certificates_block" id="ir_frontend_certificates_block" <?php checked( $ir_frontend_certificates_block, 'on' ); ?>/>
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php esc_html_e( 'Certificates', 'wdm_instructor_role' ); ?>
								<p class="ir-help-note">
									<?php esc_html_e( 'It shows detailed certificates information accessible to the instructors and administrators.', 'wdm_instructor_role' ); ?>
								</p>
							</div>
						</div>

						<div class="ir-menu-setting">
							<label for="ir_frontend_settings_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_settings_block" id="ir_frontend_settings_block" <?php checked( $ir_frontend_settings_block, 'on' ); ?>/>
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php esc_html_e( 'Settings', 'wdm_instructor_role' ); ?>
								<p class="ir-help-note">
									<?php esc_html_e( 'Instructors can view and edit their profile info from here and configures other relevant settings.', 'wdm_instructor_role' ); ?>
								</p>
							</div>
						</div>
					</div>
					<?php wp_nonce_field( 'frontend_dashboard_menu_settings_nonce', 'ir_nonce' ); ?>
					<?php submit_button( __( 'Save Settings', 'wdm_instructor_role' ) ); ?>
				</form>
			</div>

			<div id="overview_page" class="ir-tab <?php echo ( $ir_is_gutenberg_enabled ) ? 'ir-disable' : ''; ?>">
				<h3><?php esc_html_e( 'Overview Page Settings', 'wdm_instructor_role' ); ?></h3>
				<form id="ir_frontend_dashboard_overview_settings" method="post">
					<p class="ir-tab-desc-note">
						<?php esc_html_e( 'This settings helps to hide and show blocks on the instructor overview page.', 'wdm_instructor_role' ); ?>
						<a href="https://learndash.com/support/docs/add-ons/how-to-customize-the-frontend-dashboard-gutenberg-editor-and-global-settings"><?php esc_html_e( 'Learn More', 'wdm_instructor_role' ); ?></a>
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
					<div>
						<div class="ir-menu-setting">
							<label for="ir_frontend_overview_course_tile_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_overview_course_tile_block" id="ir_frontend_overview_course_tile_block" <?php checked( $ir_frontend_overview_course_tile_block, 'on' ); ?> />
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php
								echo esc_html(
									sprintf(
										/* translators: Course label */
										__( '%s Tile', 'wdm_instructor_role' ),
										\LearnDash_Custom_Label::get_label( 'course' )
									)
								);
								?>
								<p class="ir-help-note">
									<?php
									echo esc_html(
										sprintf(
											/* translators: Course Label */
											__( 'Displays the total number of %s created by and shared with the Instructors.', 'wdm_instructor_role' ),
											\LearnDash_Custom_Label::label_to_lower( 'courses' )
										)
									);
									?>
								</p>
							</div>
						</div>

						<div class="ir-menu-setting">
							<label for="ir_frontend_overview_student_tile_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_overview_student_tile_block" id="ir_frontend_overview_student_tile_block" <?php checked( $ir_frontend_overview_student_tile_block, 'on' ); ?> />
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php esc_html_e( 'Student Tile', 'wdm_instructor_role' ); ?>
								<p class="ir-help-note">
									<?php
									echo esc_html(
										sprintf(
											/* translators: Course Label */
											__( 'Displays the total number of students in the %s.', 'wdm_instructor_role' ),
											\LearnDash_Custom_Label::label_to_lower( 'courses' )
										)
									);
									?>
								</p>
							</div>
						</div>

						<div class="ir-menu-setting">
							<label for="ir_frontend_overview_submissions_tile_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_overview_submissions_tile_block" id="ir_frontend_overview_submissions_tile_block" <?php checked( $ir_frontend_overview_submissions_tile_block, 'on' ); ?> />
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php esc_html_e( 'Submissions Tile', 'wdm_instructor_role' ); ?>
								<p class="ir-help-note">
									<?php esc_html_e( 'Displays the total number of assignments and essays submissions', 'wdm_instructor_role' ); ?>
								</p>
							</div>
						</div>

						<div class="ir-menu-setting">
							<label for="ir_frontend_overview_quiz_attempts_tile_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_overview_quiz_attempts_tile_block" id="ir_frontend_overview_quiz_attempts_tile_block" <?php checked( $ir_frontend_overview_quiz_attempts_tile_block, 'on' ); ?> />
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php
								echo esc_html(
									sprintf(
										/* translators: Quiz Label */
										__( '%s Attempts Tile', 'wdm_instructor_role' ),
										\LearnDash_Custom_Label::get_label( 'quiz' )
									)
								);
								?>
								<p class="ir-help-note">
									<?php
									echo esc_html(
										sprintf(
											/* translators: Quiz Label */
											__( 'Displays the total number of %s attempts', 'wdm_instructor_role' ),
											\LearnDash_Custom_Label::label_to_lower( 'quiz' )
										)
									);
									?>
								</p>
							</div>
						</div>

						<div class="ir-menu-setting">
							<label for="ir_frontend_overview_course_progress_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_overview_course_progress_block" id="ir_frontend_overview_course_progress_block" <?php checked( $ir_frontend_overview_course_progress_block, 'on' ); ?> />
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php
								echo esc_html(
									sprintf(
										/* translators: Course Label */
										__( '%s Progress', 'wdm_instructor_role' ),
										\LearnDash_Custom_Label::get_label( 'course' )
									)
								);
								?>
								<p class="ir-help-note">
									<?php
									echo esc_html(
										sprintf(
											/* translators: 1.Course Label 2.Courses Label */
											__( 'For a %1$s, displays visually the number of students who have completed or not started the %2$s or are in progress ', 'wdm_instructor_role' ),
											\LearnDash_Custom_Label::get_label( 'course' ),
											\LearnDash_Custom_Label::get_label( 'courses' )
										)
									);
									?>
								</p>
							</div>
						</div>

						<div class="ir-menu-setting">
							<label for="ir_frontend_overview_top_courses_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_overview_top_courses_block" id="ir_frontend_overview_top_courses_block" <?php checked( $ir_frontend_overview_top_courses_block, 'on' ); ?> />
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php
								echo esc_html(
									sprintf(
										/* translators: Course Label */
										__( 'Top %s', 'wdm_instructor_role' ),
										\LearnDash_Custom_Label::get_label( 'courses' )
									)
								);
								?>
								<p class="ir-help-note">
									<?php
									echo esc_html(
										sprintf(
											/* translators: 1.Course Label 2.Courses Label */
											__( 'Displays the top %s by the number of students enrolled.', 'wdm_instructor_role' ),
											\LearnDash_Custom_Label::get_label( 'courses' )
										)
									);
									?>
								</p>
							</div>
						</div>

						<div class="ir-menu-setting">
							<label for="ir_frontend_overview_earnings_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_overview_earnings_block" id="ir_frontend_overview_earnings_block" <?php checked( $ir_frontend_overview_earnings_block, 'on' ); ?> />
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php esc_html_e( 'Earnings', 'wdm_instructor_role' ); ?>
								<p class="ir-help-note">
									<?php esc_html_e( 'Displays visually the earning of the instructor with time.', 'wdm_instructor_role' ); ?>
								</p>
							</div>
						</div>

						<div class="ir-menu-setting">
							<label for="ir_frontend_overview_submissions_block" class="ir-switch">
								<input type="checkbox" name="ir_frontend_overview_submissions_block" id="ir_frontend_overview_submissions_block" <?php checked( $ir_frontend_overview_submissions_block, 'on' ); ?> />
								<span class="ir-slider round"></span>
							</label>
							<div class="ir-switch-info">
								<?php esc_html_e( 'Latest Submissions', 'wdm_instructor_role' ); ?>
								<p class="ir-help-note">
									<?php esc_html_e( 'Display the latest assignments and essays submissions and their grading status', 'wdm_instructor_role' ); ?>
								</p>
							</div>
						</div>
					</div>
					<!-- <div class="ir-additional-settings">
						<p class="ir-setting-title"><?php esc_html_e( 'All Block Hidden Message settings', 'wdm_instructor_role' ); ?></p>
						<p class="ir-tab-desc-note"><?php esc_html_e( 'Configure the message that will be displayed when all blocks are hidden.', 'wdm_instructor_role' ); ?></p>
						<textarea name="ir_frontend_overview_empty_message" id="ir_frontend_overview_empty_message" cols="50" rows="5"><?php echo esc_attr( $ir_frontend_overview_empty_message ); ?></textarea>
					</div> -->
					<?php wp_nonce_field( 'frontend_dashboard_overview_settings_nonce', 'ir_nonce' ); ?>
					<?php submit_button( __( 'Save Settings', 'wdm_instructor_role' ) ); ?>
				</form>
			</div>


		</section>
	</div>
</div>
