<?php
/**
 * Profile Settings Template
 *
 * @since 3.5.0
 *
 * @var array  $introduction_settings_data       Array of introduction settings section data.
 * @var array  $default_intro_settings_options   Array of default intro setting section options.
 * @var string $course_label                     LearnDash Course Label.
 * @var string $enable_profile_links             Whether the profile links settings are enabled or not.
 *
 * @package LearnDash\Instructor_Role
 *
 * cspell:ignore instuctor // ignoring misspelled words that we can't change now.
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<div class="ir-instructor-settings-tab-content">
	<?php if ( false !== $onboarding_step ) : ?>
		<div class="ir-onboarding-container">
			<h3><?php esc_html_e( 'Instructor Settings', 'wdm_instructor_role' ); ?></h3>
			<span><?php esc_html_e( 'Configure Settings for your instructor after that click on save settings then click on Resume Setup', 'wdm_instructor_role' ); ?></span>
			<a class="setup-button ir-primary-btn" href="<?php echo admin_url( 'admin.php?page=instuctor&tab=setup' ); ?>" rel="noopener noreferrer">
				<?php esc_html_e( 'Resume Setup', 'wdm_instructor_role' ); ?>
			</a>
		</div>

	<?php endif; ?>
	<div class="ir-flex justify-apart align-center">
		<div class="ir-heading-wrap">
			<div class="ir-tab-heading"><?php echo __( 'Instructor settings', 'wdm_instructor_role' ); ?></div>  <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
		</div>
	</div>
	<div class="ir-heading-desc"></div>

	<div class="ir-feature-settings-section ir-settings-section">
		<div class="ir-heading-wrap">
			<div class="ir-tab-subheading"><?php echo __( 'Instructor features', 'wdm_instructor_role' ); ?></div> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
		</div>
		<div class="ir-separator"></div>
		<form method="post" id="ir-instructor-features-form">
		<div class="ir-settings-section-container">
		<?php if ( wdmCheckWooDependency() ) : ?>
				<div class="ir-flex ir-inst-setting">
					<label for="wdmir_review_product" class="ir-switch ">
						<input name="review_product" type="checkbox" id="wdmir_review_product" <?php checked( ir_get_settings( 'review_product' ), 1 ); ?> />
						<span class="ir-slider round"></span>
					</label>
					<div>
						<div class="ir-setting-label"><?php esc_html_e( 'Review Product', 'wdm_instructor_role' ); ?></div>
						<span class="ir-section-setting-note">
							<?php esc_html_e( 'Enable admin approval for WooCommerce product updates.', 'wdm_instructor_role' ); ?>
						</span>
					</div>
				</div>
			<?php endif; ?>

			<div class="ir-flex ir-inst-setting">
				<label for="wdm_enable_instructor_mail" class="ir-switch">
					<input name="instructor_mail" type="checkbox" id="wdm_enable_instructor_mail" <?php checked( ir_get_settings( 'instructor_mail' ), 1 ); ?> />
					<span class="ir-slider round"></span>
				</label>
				<div>
					<div class="ir-setting-label">
						<?php
						echo esc_html(
							sprintf(
								/* translators: Quiz label */
								_x( '%s Completion Emails', 'used for admin settings', 'wdm_instructor_role' ),
								\LearnDash_Custom_Label::get_label( 'Quiz' )
							)
						);
						?>
					</div>
					<span class="ir-section-setting-note">
						<?php
						printf(
							/* translators: Quiz label */
							esc_html__( 'Enable email notification for instructor on %s completion.', 'wdm_instructor_role' ),
							esc_html( \LearnDash_Custom_Label::label_to_lower( 'quiz' ) )
						);
						?>
					</span>
					<div class="ir-flex align-center ir-primary-color-setting">
						<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings-share" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12.004 21c-.732 .002 -1.466 -.437 -1.679 -1.317a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.306 .317 1.64 1.78 1.004 2.684" /><path d="M12 15a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z" /><path d="M16 22l5 -5" /><path d="M21 21.5v-4.5h-4.5" /></svg>
						<span>
						<a href="<?php echo admin_url( 'admin.php?page=instuctor&tab=email' ); ?>" target="_blank" rel="noopener noreferrer" style="text-decoration:none">
							<?php
							printf(
								/* translators: Quiz label */
								esc_html__( 'Configure %s completion email', 'wdm_instructor_role' ),
								esc_html( \LearnDash_Custom_Label::label_to_lower( 'quiz' ) )
							);
							?>
						</a>
					</span>
					</div>
				</div>
			</div>

			<div class="ir-flex ir-inst-setting">
				<label for="wdm_enable_instructor_course_mail" class="ir-switch">
					<input name="wdm_enable_instructor_course_mail" type="checkbox" id="wdm_enable_instructor_course_mail" <?php checked( ir_get_settings( 'wdm_enable_instructor_course_mail' ), 1 ); ?> />
					<span class="ir-slider round"></span>
				</label>
				<div>
					<div class="ir-setting-label">
						<?php
						echo esc_html(
							sprintf(
								/* translators: Course label */
								_x( '%s Purchase Emails', 'used for admin settings', 'wdm_instructor_role' ),
								\LearnDash_Custom_Label::get_label( 'Course' )
							)
						);
						?>
					</div>
					<span class="ir-section-setting-note">
						<?php
						printf(
							/* translators: Course label */
							esc_html__( 'Enable email notification for instructor when a student purchases a %s.', 'wdm_instructor_role' ),
							esc_html( \LearnDash_Custom_Label::label_to_lower( 'course' ) )
						);
						?>
					</span>
					<div class="ir-flex align-center ir-primary-color-setting">
						<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings-share" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12.004 21c-.732 .002 -1.466 -.437 -1.679 -1.317a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.306 .317 1.64 1.78 1.004 2.684" /><path d="M12 15a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z" /><path d="M16 22l5 -5" /><path d="M21 21.5v-4.5h-4.5" /></svg>
						<span>
						<a href="<?php echo admin_url( 'admin.php?page=instuctor&tab=email' ); ?>" target="_blank" rel="noopener noreferrer" style="text-decoration:none">
							<?php
							printf(
								/* translators: Quiz label */
								esc_html__( 'Configure %s purchase email', 'wdm_instructor_role' ),
								esc_html( \LearnDash_Custom_Label::label_to_lower( 'course' ) )
							);
							?>
						</a>
						</span>
					</div>
				</div>
			</div>
		</div>
			<p style="text-align:<?php echo ( is_rtl() ) ? 'left' : 'right'; ?>">
				<input id="ir_instructor_features_save" type="submit" class="button button-primary ir-primary-btn" name="ir_instructor_features_save" value="<?php esc_html_e( 'Save Settings', 'wdm_instructor_role' ); ?>">
				<?php wp_nonce_field( 'ir_instructor_features_nonce', 'ir_nonce' ); ?>
			</p>
		</form>
	</div>
	<form method="post" id="ir-profile-settings-form">
	<div class="ir-feature-settings-section ir-settings-section">
		<div class="ir-heading-wrap">
			<div class="ir-tab-subheading"><?php echo __( 'Profile', 'wdm_instructor_role' ); ?></div> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
		</div>
		<div class="ir-flex ir-inst-setting">
			<label for="ir_enable_profile_links" class="ir-switch">
				<input type="checkbox" name="ir_enable_profile_links" id="ir_enable_profile_links" <?php checked( ir_get_settings( 'ir_enable_profile_links' ), 1 ); ?>/>
				<span class="ir-slider round"></span>
			</label>
			<div>
				<div class="ir-setting-label"><?php esc_html_e( 'Profile Links', 'wdm_instructor_role' ); ?></div>
				<span class="ir-section-setting-note">
				<?php
					echo esc_html(
						sprintf(
							// translators: Course placeholder.
							__( 'Instructor profile links on %s archive and single pages. ', 'wdm_instructor_role' ),
							$course_label
						)
					);
					?>
				</span>
			</div>
		</div>

			<div class="ir-inst-setting">
				<div class="ir-setting-label"><?php esc_html_e( 'Introduction Section', 'wdm_instructor_role' ); ?></div>
				<div class="ir-section-setting-note">
					<?php
						echo esc_html(
							sprintf(
								// translators: Course placeholder.
								__( 'Configure the details to be displayed in the introduction section.', 'wdm_instructor_role' )
							)
						);
						?>
				</div>
			</div>
		<table class="form-table intro-section">
			<tbody>
				<tr>
					<td class="no-padding">
						<div class="ir-profile-intro-settings">
							<table class="ir-profile-settings-table">
								<tbody>
									<?php foreach ( $introduction_settings_data as $key => $setting ) : ?>
										<tr class="ir-profile-settings-row">
											<td class="w-25">
												<span class="dashicons dashicons-sort">
													<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-grip-vertical" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M9 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M9 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M15 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M15 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M15 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /></svg>
												</span>
											</td>
											<td>
												<span id="ir-profile-section-title-<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $setting['title'] ); ?></span>
											</td>
											<td class="ir-right-align" id="ir-profile-section-actions-<?php echo esc_attr( $key ); ?>">
												<a title="<?php esc_attr_e( 'Edit', 'wdm_instructor_role' ); ?>">
													<span data-id=<?php echo esc_attr( $key ); ?> class="dashicons dashicons-admin-tools ir-profile-setting-edit">
													<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
													</span>
												</a>
												<input id="ir-profile-section-data-<?php echo esc_attr( $key ); ?>" type="hidden" name="ir_profile_section[<?php echo esc_attr( $key ); ?>]" value='<?php echo json_encode( $setting ); ?>'>
												<a class="ir-profile-delete-section" title="<?php esc_attr_e( 'Delete', 'wdm_instructor_role' ); ?>">
													<span class="dashicons dashicons-trash">
													<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
													</span>
												</a>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
						<p>
							<a id="ir-profile-add-setting-section" class="button button-secondary ir-btn-outline"><?php esc_html_e( 'Add', 'wdm_instructor_role' ); ?></a>
						</p>
					</td>
				</tr>
			</tbody>
		</table>
		<!-- Add Profile Setting Container -->
		<div id="ir-profile-add-setting-container" class="ir-overlay">
			<div class="ir-profile-add-setting">
				<h3><?php esc_html_e( 'Add/Edit Section', 'wdm_instructor_role' ); ?></h3>
				<a class="close">&times;</a>
				<div class="ir-profile-add-setting-contents">
					<table>
						<tbody>
							<tr>
								<th>
									<?php esc_html_e( 'Title', 'wdm_instructor_role' ); ?>
								</th>
								<td>
									<input id="ir-profile-update-title" type="text" name="ir-profile-update-setting[title]"/>
								</td>
							</tr>
							<tr>
								<th>
									<?php esc_html_e( 'Image', 'wdm_instructor_role' ); ?>
								</th>
								<td>
									<select id="ir-profile-update-image" name="ir-profile-update-setting[image]">
										<?php foreach ( $default_intro_settings_options['image'] as $image_key => $image_value ) : ?>
											<option value="<?php echo esc_attr( $image_key ); ?>"><?php echo esc_attr( $image_value ); ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<div class="ir-profile-image-div">
										<span>
											<a id="ir-profile-update-custom-image">
												<?php esc_html_e( 'Select Image', 'wdm_instructor_role' ); ?>
											</a>
										</span>
										<span>
											<a target="blank" id="ir-profile-view-img-url"><?php esc_html_e( 'View Image', 'wdm_instructor_role' ); ?>
										</span>
										<input type="hidden" name="ir-profile-update-setting[custom_image_url]" class="ir-profile-update-custom-settings"/>
									</div>
								</td>
							</tr>
							<tr>
								<th>
									<?php esc_html_e( 'Meta Key', 'wdm_instructor_role' ); ?>
								</th>
								<td>
									<input id="ir-profile-update-metakey" type="text" name="ir-profile-update-setting[meta_key]"/>
								</td>
								<td class="ir-docs-help-link">
									<a target="blank" href="https://learndash.com/support/docs/add-ons/profile-introduction-sections/#meta-key">
										<span class="dashicons dashicons-info" title="<?php esc_html_e( 'Click here to learn more', 'wdm_instructor_role' ); ?>"></span>
									</a>
								</td>
							</tr>
							<tr>
								<th>
									<?php esc_html_e( 'Data Type', 'wdm_instructor_role' ); ?>
								</th>
								<td>
									<select id="ir-profile-update-datatype" name="ir-profile-update-setting[data_type]">
										<?php foreach ( $default_intro_settings_options['data_type'] as $data_key => $data_value ) : ?>
											<option value="<?php echo esc_attr( $data_key ); ?>"><?php echo esc_attr( $data_value ); ?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr>
								<th>
									<?php esc_html_e( 'Icon', 'wdm_instructor_role' ); ?>
								</th>
								<td>
									<select id="ir-profile-update-icon" name="ir-profile-update-setting[icon]" id="">
										<?php foreach ( $default_intro_settings_options['icon'] as $icon_key => $icon_value ) : ?>
											<option value="<?php echo esc_attr( $icon_key ); ?>"><?php echo esc_attr( $icon_value ); ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<input id="ir-profile-update-custom-dashicon" type="text" name="ir-profile-update-setting[custom_dashicon]" placeholder="<?php esc_html_e( 'eg: dashicons-yes' ); ?>"/>
								</td>
							</tr>
						</tbody>
					</table>
					<p>
						<a id="ir-profile-update-save" class="button ir-profile-update-button"><?php esc_html_e( 'Save', 'wdm_instructor_role' ); ?></a>
						<a id="ir-profile-update-add" class="button ir-profile-update-button"><?php esc_html_e( 'Add', 'wdm_instructor_role' ); ?></a>
						<span class="dashicons dashicons-update"></span>
						<input id="ir-profile-update-id" type="hidden" name="ir-profile-update-setting[id]" value="0" />
					</p>
				</div>
			</div>
		</div>
		<p style="text-align:<?php echo ( is_rtl() ) ? 'left' : 'right'; ?>">
			<input id="ir-profile-save-setting-section" type="submit" class="button button-primary ir-primary-btn" name="ir_profile_settings_save" value="<?php esc_html_e( 'Save Settings', 'wdm_instructor_role' ); ?>">
			<?php wp_nonce_field( 'ir_profile_settings_nonce', 'ir_nonce' ); ?>
		</p>

	</form>
	</div>
	<form method="post" id="ir-student-communication-settings-form">
	<div class="ir-feature-settings-section ir-settings-section">
		<div class="ir-heading-wrap">
			<div class="ir-tab-subheading"><?php echo __( 'Students', 'wdm_instructor_role' ); ?></div> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
		</div>

		<div class="ir-flex ir-inst-setting">
			<label for="ir_student_communication_check" class="ir-switch">
				<input
					name="ir_student_communication_check"
					type="checkbox"
					id="ir_student_communication_check"
					<?php checked( ir_get_settings( 'ir_student_communication_check' ), 1 ); ?>
				/>
				<span class="ir-slider round"></span>
			</label>

				<div>
					<div class="ir-setting-label"><?php esc_html_e( 'Student Teacher Communication', 'wdm_instructor_role' ); ?></div>
					<span class="ir-section-setting-note">
					<?php
					printf(
						// translators: 1: Course label 2:Lesson label 3:Topic label.
						esc_html__( 'Enable students to communicate to %1$s instructors from the %2$s & %3$s page itself.', 'wdm_instructor_role' ),
						esc_html( \LearnDash_Custom_Label::label_to_lower( 'course' ) ),
						esc_html( \LearnDash_Custom_Label::label_to_lower( 'lesson' ) ),
						esc_html( \LearnDash_Custom_Label::label_to_lower( 'topic' ) )
					);
					?>
					<br>
					<span><?php esc_html_e( 'Note: This feature requires the Buddypress plugin to function.', 'wdm_instructor_role' ); ?></span>
					<a href="https://learndash.com/support/docs/add-ons/student-teacher-communication/" target="_blank"><?php esc_html_e( 'Learn More', 'wdm_instructor_role' ); ?></a>
					</span>
					<div class="ir-flex align-center ir-sub-setting">
						<div class="ir-setting-label"><?php esc_html_e( 'Set popup text', 'wdm_instructor_role' ); ?></div>
						<input placeholder="<?php esc_html_e( 'If you have any doubts, feel free to message me.', 'wdm_instructor_role' ); ?>" type="text" name="ir_st_comm_editor_set_popup" id="ir_st_comm_editor_set_popup" value="<?php echo esc_attr( $ir_st_comm_editor_set_popup ); ?>" data-default-color="#00ACD3" />
					</div>
					<div class="ir-flex align-center ir-sub-setting">
						<div class="ir-setting-label"><?php esc_html_e( 'Set button text', 'wdm_instructor_role' ); ?></div>
						<input placeholder="<?php esc_html_e( 'send your doubts', 'wdm_instructor_role' ); ?>" type="text" name="ir_st_comm_editor_set_button" id="ir_st_comm_editor_set_button" value="<?php echo esc_attr( $ir_st_comm_editor_set_button ); ?>" data-default-color="#00ACD3" />
					</div>
					<div class="ir-flex align-center ir-sub-setting">
						<div class="ir-setting-label"><?php esc_html_e( 'Accent color', 'wdm_instructor_role' ); ?></div>
						<input type="text" name="ir_st_comm_editor_accent_color" id="ir_st_comm_editor_accent_color" value="<?php echo esc_attr( $ir_st_comm_editor_accent_color ); ?>" data-default-color="#00ACD3" />
					</div>

				</div>
		</div>
		<div class="ir-flex ir-inst-setting">
			<label for="enable_tabs_access" class="ir-switch">
				<input
					name="enable_tabs_access"
					type="checkbox"
					id="enable_tabs_access"
					<?php checked( ir_get_settings( 'enable_tabs_access' ), 1 ); ?>
				/>
				<span class="ir-slider round"></span>
			</label>
			<div>
				<div class="ir-setting-label"><?php esc_html_e( 'Enable Dashboard Access For All', 'wdm_instructor_role' ); ?>
					<div class="setup-status-pending ir-beta" >
							<?php esc_html_e( 'Beta feature', 'wdm_instructor_role' ); ?>
						</div>
					</div>
				<span class="ir-section-setting-note">
				<?php
					echo esc_html(
						sprintf(
							// translators: Course placeholder.
							__( 'This is a experimental feature to allow your users to create dashboard for students, group leader, etc. By default, only administrators and instructors can view the page with the Instructor Dashboard tabs block and other users are redirected to the home page. If the above setting is enabled, any logged-in user can access a page with the block and view the dashboard contents if they have access to it.', 'wdm_instructor_role' )
						)
					);
					?>
				</span>
			</div>
		</div>
		<p style="text-align:<?php echo ( is_rtl() ) ? 'left' : 'right'; ?>">
			<input id="ir_student_communication_settings_save" type="submit" class="button button-primary ir-primary-btn" name="ir_student_communication_settings_save" value="<?php esc_html_e( 'Save Settings', 'wdm_instructor_role' ); ?>">
			<?php wp_nonce_field( 'ir_stu_com_settings_nonce', 'ir_nonce' ); ?>
		</p>
		</form>
	</div>

</div>
