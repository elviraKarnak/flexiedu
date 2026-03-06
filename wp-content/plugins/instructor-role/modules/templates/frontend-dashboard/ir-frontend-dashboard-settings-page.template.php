<?php
/**
 * Instructor Frontend Dashboard Settings Template
 *
 * @since 4.4.0
 *
 * @param bool $ir_enable_frontend_dashboard
 * @param bool $ir_disable_ld_links
 * @param string $image_url
 * @param string $admin_image_url
 *
 * @package LearnDash\Instructor_Role
 *
 * cspell:ignore instuctor // ignoring misspelled words that we can't change now.
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter&family=Open+Sans&display=swap" rel="stylesheet">
<div class="ir-instructor-settings-tab-content">
	<form method="POST">
		<div class="ir-no-appearance">
		<?php if ( false !== $onboarding_step ) : ?>
			<div class="ir-onboarding-container">
			<h3><?php esc_html_e( 'Course Creation', 'wdm_instructor_role' ); ?></h3>
			<span><?php esc_html_e( 'Look on the settings that might help you. You can also change appearance of course creator match with the dashboard you created or choose another', 'wdm_instructor_role' ); ?></span>
			<a class="setup-button ir-primary-btn" href="<?php echo admin_url( 'admin.php?page=instuctor&tab=setup' ); ?>" rel="noopener noreferrer">
				<?php esc_html_e( 'Resume Setup', 'wdm_instructor_role' ); ?>
			</a>
		</div>
		<?php endif; ?>
			<div class="ir-flex justify-apart align-center">
				<div class="ir-heading-wrap">
					<div class="ir-tab-heading">
					<?php
					echo esc_html(
						/* translators: Course Label. */
						sprintf( __( '%s Creation', 'wdm_instructor_role' ), \LearnDash_Custom_Label::get_label( 'course' ) )
					);
					?>
					</div>
				</div>
			</div>
			<div class="ir-heading-desc"></div>
			<div class="ir-fcc-section">
				<div class="ir-flex justify-apart align-center">
					<div class="ir-heading-wrap">
						<div class="ir-tab-subheading"><?php echo __( 'Frontend course creation', 'wdm_instructor_role' ); ?></div> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
					</div>
					<label for="ir_enable_frontend_dashboard" class="ir-ajax-fcc ir-switch">
						<input type="checkbox" name="ir_enable_frontend_dashboard" id="ir_enable_frontend_dashboard" <?php checked( ir_get_settings( 'ir_enable_frontend_dashboard' ), 'on' ); ?>/>
						<span class="ir-slider round"></span>
					</label>
				</div>
				<div class="ir-subheading-desc"><?php echo __( 'Note: Backend Course Creation is auto disabled when Frontend Course Creation is Active.', 'wdm_instructor_role' ); ?></div> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
				<div class="ir-fcc-section-setting <?php echo ( ir_get_settings( 'ir_enable_frontend_dashboard' ) !== 'on' ) ? 'ir-hide' : ''; ?>">
					<div class="ir-flex align-center">
						<label for="ir_enable_sync" class=" ir-switch">
							<input type="checkbox" name="ir_enable_sync" id="ir_enable_sync" <?php checked( ir_get_settings( 'ir_enable_sync' ), 'on' ); ?>/>
							<span class="ir-slider round"></span>
						</label>
						<span class="allow-comm-label"><?php echo __( 'Use same theme as frontend dashboard', 'wdm_instructor_role' ); ?></span> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
					</div>
					<div class="ir-or">
						<span><?php echo __( 'OR', 'wdm_instructor_role' ); ?></span> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
					</div>
					<div class="ir-flex align-center">
						<div class="ir-flex edit-app">
							<span class="ir-btn-outline ir-show-appearance ir-flex <?php echo ir_get_settings( 'ir_enable_sync' ) === 'on' ? 'ir-disabled' : ''; ?>">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-palette" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21a9 9 0 0 1 0 -18c4.97 0 9 3.582 9 8c0 1.06 -.474 2.078 -1.318 2.828c-.844 .75 -1.989 1.172 -3.182 1.172h-2.5a2 2 0 0 0 -1 3.75a1.3 1.3 0 0 1 -1 2.25" /><path d="M8.5 10.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12.5 7.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M16.5 10.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /></svg>
								<?php echo __( 'Edit Appearance', 'wdm_instructor_role' ); ?> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
							</span>
						</div>
					</div>
				</div>
				<p style="text-align:<?php echo ( is_rtl() ) ? 'left' : 'right'; ?>">
				<input id="ir_general_course_creation_save" type="submit" class="button button-primary ir-primary-btn" name="ir_general_course_creation_save" value="<?php esc_html_e( 'Save Settings', 'wdm_instructor_role' ); ?>">
				<?php wp_nonce_field( 'ir_general_course_creation_nonce', 'ir_nonce' ); ?>
				</p>
			</div>
			<div class="ir-fcc-section ir-backend-creation <?php echo ( ir_get_settings( 'ir_enable_frontend_dashboard' ) !== 'on' ) ? 'ir-hide' : ''; ?> ">
				<div class="ir-flex justify-apart align-center">
					<div class="ir-heading-wrap">
						<div class="ir-tab-subheading"><?php echo __( 'Disable Backend course creation', 'wdm_instructor_role' ); ?></div> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
					</div>
					<label for="ir_disable_ld_links" class="ir-switch ">
						<input type="checkbox" name="ir_disable_ld_links" id="ir_disable_ld_links" <?php checked( ir_get_settings( 'ir_disable_ld_links' ), 'on' ); ?>/>
						<span class="ir-slider round"></span>
					</label>
				</div>
				<div class="ir-subheading-desc"><?php echo __( 'Suggestion : Use Frontend Course Creation for better efficiency and convenience.', 'wdm_instructor_role' ); ?></div> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
				<p style="text-align:<?php echo ( is_rtl() ) ? 'left' : 'right'; ?>">
				<input id="ir_general_course_creation_save" type="submit" class="button button-primary ir-primary-btn" name="ir_general_course_creation_save" value="<?php esc_html_e( 'Save Settings', 'wdm_instructor_role' ); ?>">
				<?php wp_nonce_field( 'ir_general_course_creation_nonce', 'ir_nonce' ); ?>
				</p>
			</div>
			<form method="post" id="ir-general-course-creation-form">
			<div class="ir-fcc-section">
				<div class="ir-flex justify-apart align-center">
					<div class="ir-heading-wrap">
						<div class="ir-tab-subheading"><?php echo __( 'General Course Creation Settings', 'wdm_instructor_role' ); ?></div> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
					</div>
				</div>
				<div class="ir-subheading-desc"><?php echo __( 'Configure your course settings with options of general and group pricing settings', 'wdm_instructor_role' ); ?></div> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
				<div class="ir-subgroup-heading">
					<?php echo __( 'General', 'wdm_instructor_role' ); ?> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
				</div>
				<div class="ir-flex ir-inst-setting">
					<label for="wdmir_review_course" class="ir-switch ">
						<input name="wdmir_review_course" type="checkbox" id="wdmir_review_course" <?php checked( ir_get_settings( 'review_course' ), 1 ); ?> />
						<span class="ir-slider round"></span>
					</label>
					<div>
						<div class="ir-setting-label">
							<?php
							echo esc_html(
								sprintf(
									// translators: Course placeholder.
									__( 'Review %s', 'wdm_instructor_role' ),
									\LearnDash_Custom_Label::label_to_lower( 'course' ),
								)
							);
							?>
						</div>
						<span class="ir-section-setting-note">
						<?php
							echo esc_html(
								sprintf(
									// translators: Course placeholder.
									__( 'Enable admin approval for LearnDash %s updates.', 'wdm_instructor_role' ),
									\LearnDash_Custom_Label::label_to_lower( 'course' ),
								)
							);
							?>
						</span>
					</div>
				</div>
				<div class="ir-flex ir-inst-setting">
					<label for="enable_ld_category" class="ir-switch ">
						<input type="checkbox" id="enable_ld_category" name="enable_ld_category" <?php checked( ir_get_settings( 'enable_ld_category' ), 'on' ); ?>/>
						<span class="ir-slider round"></span>
					</label>
					<div>
						<div class="ir-setting-label">
							<?php
							echo esc_html(
								sprintf(
									// translators: Course placeholder.
									__( 'Learndash Categories', 'wdm_instructor_role' ),
									\LearnDash_Custom_Label::label_to_lower( 'course' ),
								)
							);
							?>
						</div>
						<span class="ir-section-setting-note">
						<?php
							echo esc_html(
								sprintf(
									// translators: Course placeholder.
									__( 'Enable or Disable LearnDash %s Categories.', 'wdm_instructor_role' ),
									\LearnDash_Custom_Label::label_to_lower( 'course' ),
								)
							);
							?>
						</span>
					</div>
				</div>
				<div class="ir-flex ir-inst-setting">
					<label for="enable_permalinks" class="ir-switch ">
						<input type="checkbox" id="enable_permalinks" name="enable_permalinks" <?php checked( ir_get_settings( 'enable_permalinks' ), 'on' ); ?>/>
						<span class="ir-slider round"></span>
					</label>
					<div>
						<div class="ir-setting-label">
							<?php
							echo esc_html(
								sprintf(
									// translators: Course placeholder.
									__( 'Permalinks', 'wdm_instructor_role' )
								)
							);
							?>
						</div>
						<span class="ir-section-setting-note">
						<?php
							echo esc_html(
								sprintf(
									// translators: Course placeholder.
									__( 'Enable or Disable the Permalinks Metabox.', 'wdm_instructor_role' )
								)
							);
							?>
						</span>
					</div>
				</div>

				<div class="ir-flex ir-inst-setting">
					<label for="ir_ld_category_check" class="ir-switch">
						<input
							name="ir_ld_category_check"
							type="checkbox"
							id="ir_ld_category_check"
							<?php checked( ir_get_settings( 'ir_ld_category_check' ), 1 ); ?>
						/>
						<span class="ir-slider round"></span>
					</label>
					<div>
						<div class="ir-setting-label"><?php esc_html_e( 'Restrict Category', 'wdm_instructor_role' ); ?></div>
						<span class="ir-section-setting-note">
							<?php
							printf(
								/* translators: Course label */
								esc_html__( 'Restrict Instructors from creating new LearnDash %s categories.', 'wdm_instructor_role' ),
								esc_html( \LearnDash_Custom_Label::label_to_lower( 'course' ) )
							);
							?>
						</span>
					</div>
				</div>

				<div class="ir-subgroup-heading">
					<?php
					echo esc_html(
						sprintf(
							/* translators: 1.Course Label 2.Group Label */
							__( '%1$s and %2$s Pricing Options', 'wdm_instructor_role' ),
							\LearnDash_Custom_Label::get_label( 'course' ),
							\LearnDash_Custom_Label::get_label( 'group' )
						)
					);
					?>
				</div>
				<div class="ir-flex ir-inst-setting">
					<label for="enable_open_pricing" class="ir-switch ">
						<input type="checkbox" id="enable_open_pricing" name="enable_open_pricing" <?php checked( ir_get_settings( 'enable_open_pricing' ), 'on' ); ?>/>
						<span class="ir-slider round"></span>
					</label>
					<div>
						<div class="ir-setting-label"><?php esc_html_e( 'Open', 'wdm_instructor_role' ); ?></div>
						<span class="ir-section-setting-note">
							<?php echo esc_html( __( 'Enable or Disable the Open pricing option.', 'wdm_instructor_role' ) ); ?>
						</span>
					</div>
				</div>
				<div class="ir-flex ir-inst-setting">
					<label for="enable_free_pricing" class="ir-switch ">
						<input type="checkbox" id="enable_free_pricing" name="enable_free_pricing" <?php checked( ir_get_settings( 'enable_free_pricing' ), 'on' ); ?>/>
						<span class="ir-slider round"></span>
					</label>
					<div>
						<div class="ir-setting-label"><?php esc_html_e( 'Free', 'wdm_instructor_role' ); ?></div>
						<span class="ir-section-setting-note">
							<?php echo esc_html( __( 'Enable or Disable the Free pricing option.', 'wdm_instructor_role' ) ); ?>
						</span>
					</div>
				</div>
				<div class="ir-flex ir-inst-setting">
					<label for="enable_buy_pricing" class="ir-switch ">
						<input type="checkbox" id="enable_buy_pricing" name="enable_buy_pricing" <?php checked( ir_get_settings( 'enable_buy_pricing' ), 'on' ); ?>/>
						<span class="ir-slider round"></span>
					</label>
					<div>
						<div class="ir-setting-label"><?php esc_html_e( 'Buy', 'wdm_instructor_role' ); ?></div>
						<span class="ir-section-setting-note">
							<?php echo esc_html( __( 'Enable or Disable the Buy Now pricing option.', 'wdm_instructor_role' ) ); ?>
						</span>
					</div>
				</div>
				<div class="ir-flex ir-inst-setting">
					<label for="enable_recurring_pricing" class="ir-switch ">
						<input type="checkbox" id="enable_recurring_pricing" name="enable_recurring_pricing" <?php checked( ir_get_settings( 'enable_recurring_pricing' ), 'on' ); ?>/>
						<span class="ir-slider round"></span>
					</label>
					<div>
						<div class="ir-setting-label"><?php esc_html_e( 'Recurring', 'wdm_instructor_role' ); ?></div>
						<span class="ir-section-setting-note">
							<?php echo esc_html( __( 'Enable or Disable the Recurring pricing option.', 'wdm_instructor_role' ) ); ?>
						</span>
					</div>
				</div>
				<div class="ir-flex ir-inst-setting">
					<label for="enable_closed_pricing" class="ir-switch ">
						<input type="checkbox" id="enable_closed_pricing" name="enable_closed_pricing" <?php checked( ir_get_settings( 'enable_closed_pricing' ), 'on' ); ?>/>
						<span class="ir-slider round"></span>
					</label>
					<div>
						<div class="ir-setting-label"><?php esc_html_e( 'Closed', 'wdm_instructor_role' ); ?></div>
						<span class="ir-section-setting-note">
							<?php echo esc_html( __( 'Enable or Disable the Closed pricing option.', 'wdm_instructor_role' ) ); ?>
						</span>
					</div>
				</div>
				<p style="text-align:<?php echo ( is_rtl() ) ? 'left' : 'right'; ?>">
				<input id="ir_general_course_creation_save" type="submit" class="button button-primary ir-primary-btn" name="ir_general_course_creation_save" value="<?php esc_html_e( 'Save Settings', 'wdm_instructor_role' ); ?>">
				<?php wp_nonce_field( 'ir_general_course_creation_nonce', 'ir_nonce' ); ?>
			</p>
			</div>
		</div>
		</form>
		<div id="appearance_settings">
			<div class="ir-inline-flex align-center ir-primary-color-setting ir-back ir-hide-appearance">
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
			<div class="ir-heading-desc"></div>
			<form id="ir_frontend_dashboard_appearance_settings" method="post">
				<div class="ir-fcc-sync ir-fcc-section" style="<?php echo ( 'on' == $ir_enable_sync ) ? esc_html( 'pointer-events : none ; opacity: 0.4;' ) : ''; ?> " >
					<p class="ir-setting-title-2" style="<?php echo ( 'on' !== $ir_enable_frontend_dashboard ) ? esc_html( 'display: none;' ) : ''; ?>"><?php esc_html_e( 'Layout', 'wdm_instructor_role' ); ?></p>
					<div class="ir-custom-color-settings" style="<?php echo ( 'on' !== $ir_enable_frontend_dashboard ) ? esc_html( 'display: none;' ) : ''; ?>">
					<?php if ( false !== $onboarding_step ) : ?>
						<div class="ir-onboarding-container">
							<h3><?php esc_html_e( 'Course Creation', 'wdm_instructor_role' ); ?></h3>
							<span><?php esc_html_e( 'Select your theme color, font and click on save changes.', 'wdm_instructor_role' ); ?></span>
							<a class="setup-button ir-primary-btn" href="<?php echo admin_url( 'admin.php?page=instuctor&tab=setup' ); ?>" rel="noopener noreferrer">
							<?php esc_html_e( 'Resume Setup', 'wdm_instructor_role' ); ?>
						</a>
						</div>
					<?php endif; ?>
					<p class="ir-setting-title-3"><?php esc_html_e( 'Color Schemes', 'wdm_instructor_role' ); ?></p>
						<div class="ir-color-patterns">
							<div class="ir-color-scheme <?php echo ( 'calm_ocean' === $ir_frontend_course_creator_color_scheme ) ? 'ir-active-color' : ''; ?>">
								<div class="ir-color-scheme-name" name="calm_ocean">
									<?php esc_html_e( 'Calm ocean', 'wdm_instructor_role' ); ?>
										<span class="<?php echo ( 'calm_ocean' !== $ir_frontend_course_creator_color_scheme ) ? 'ir-hide' : ''; ?> dashicons dashicons-yes-alt" style="color: rgb(0, 127, 255);"></span>
								</div>
								<div class="ir-colors" name="calm_ocean">
									<span name="calm_ocean" style="background-color: #2E353C; border: 1px solid rgb(255, 255, 255);"></span>
									<span name="calm_ocean" style="background-color: #FFFFFF; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
									<span name="calm_ocean" style="background-color: #2067FA; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
								</div>
								<input style="display:none;" type="radio" name="ir_frontend_course_creator_color_scheme" class="ir_frontend_course_creator_color_scheme" value="calm_ocean" <?php checked( $ir_frontend_course_creator_color_scheme, 'calm_ocean' ); ?>>
							</div>
							<div class="ir-color-scheme <?php echo ( 'wise_pink' === $ir_frontend_course_creator_color_scheme ) ? 'ir-active-color' : ''; ?>">
								<div class="ir-color-scheme-name" name="wise_pink">
									<?php esc_html_e( 'Wise Pink', 'wdm_instructor_role' ); ?>
										<span class="<?php echo ( 'wise_pink' !== $ir_frontend_course_creator_color_scheme ) ? 'ir-hide' : ''; ?> dashicons dashicons-yes-alt" style="color: rgb(0, 127, 255);"></span>
								</div>
								<div class="ir-colors" name="wise_pink">
									<span name="wise_pink" style="background-color: #3B2E3B; border: 1px solid rgb(255, 255, 255);"></span>
									<span name="wise_pink" style="background-color: #FFFFFF; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
									<span name="wise_pink" style="background-color: #E339D8; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
								</div>
								<input style="display:none;" type="radio" name="ir_frontend_course_creator_color_scheme" class="ir_frontend_course_creator_color_scheme" value="wise_pink" <?php checked( $ir_frontend_course_creator_color_scheme, 'wise_pink' ); ?>>
							</div>
							<div class="ir-color-scheme <?php echo ( 'friendly_mustang' === $ir_frontend_course_creator_color_scheme ) ? 'ir-active-color' : ''; ?>">
								<div class="ir-color-scheme-name" name="friendly_mustang">
									<?php esc_html_e( 'Friendly Mustang', 'wdm_instructor_role' ); ?>
									<span class="<?php echo ( 'friendly_mustang' !== $ir_frontend_course_creator_color_scheme ) ? 'ir-hide' : ''; ?> dashicons dashicons-yes-alt" style="color: rgb(0, 127, 255);"></span>
								</div>
								<div class="ir-colors" name="friendly_mustang">
									<span name="friendly_mustang" style="background-color: #3C352E; border: 1px solid rgb(255, 255, 255);"></span>
									<span name="friendly_mustang" style="background-color: #FFFFFF; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
									<span name="friendly_mustang" style="background-color: #FC9618; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
								</div>
								<input style="display:none;" type="radio" name="ir_frontend_course_creator_color_scheme" class="ir_frontend_course_creator_color_scheme" value="friendly_mustang" <?php checked( $ir_frontend_course_creator_color_scheme, 'friendly_mustang' ); ?>>
							</div>
							<div class="ir-color-scheme <?php echo ( 'natural_green' === $ir_frontend_course_creator_color_scheme ) ? 'ir-active-color' : ''; ?>">
								<div class="ir-color-scheme-name" name="natural_green">
									<?php esc_html_e( 'Natural Green', 'wdm_instructor_role' ); ?>
										<span class="<?php echo ( 'natural_green' !== $ir_frontend_course_creator_color_scheme ) ? 'ir-hide' : ''; ?> dashicons dashicons-yes-alt" style="color: rgb(0, 127, 255);"></span>
								</div>
								<div class="ir-colors" name="natural_green">
									<span name="natural_green" style="background-color: #354538; border: 1px solid rgb(255, 255, 255);"></span>
									<span name="natural_green" style="background-color: #00533A; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
									<span name="natural_green" style="background-color: #21CF3D; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
								</div>
								<input style="display:none;" type="radio" name="ir_frontend_course_creator_color_scheme" class="ir_frontend_course_creator_color_scheme" value="natural_green" <?php checked( $ir_frontend_course_creator_color_scheme, 'natural_green' ); ?>>
							</div>
							<div class="ir-color-scheme <?php echo ( 'royal_purple' === $ir_frontend_course_creator_color_scheme ) ? 'ir-active-color' : ''; ?>">
								<div class="ir-color-scheme-name" name="royal_purple">
									<?php esc_html_e( 'Royal Purple', 'wdm_instructor_role' ); ?>
										<span class="<?php echo ( 'royal_purple' !== $ir_frontend_course_creator_color_scheme ) ? 'ir-hide' : ''; ?> dashicons dashicons-yes-alt" style="color: rgb(0, 127, 255);"></span>
								</div>
								<div class="ir-colors" name="royal_purple">
									<span name="royal_purple" style="background-color: #3F3444; border: 1px solid rgb(255, 255, 255);"></span>
									<span name="royal_purple" style="background-color: #20003F; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
									<span name="royal_purple" style="background-color: #954FB6; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
								</div>
								<input style="display:none;" type="radio" name="ir_frontend_course_creator_color_scheme" class="ir_frontend_course_creator_color_scheme" value="royal_purple" <?php checked( $ir_frontend_course_creator_color_scheme, 'royal_purple' ); ?>>
							</div>
							<div class="ir-color-scheme <?php echo ( 'custom' === $ir_frontend_course_creator_color_scheme ) ? 'ir-active-color' : ''; ?>">
								<div class="ir-color-scheme-name" name="custom">
									<?php esc_html_e( 'Custom', 'wdm_instructor_role' ); ?>
										<span class="<?php echo ( 'custom' !== $ir_frontend_course_creator_color_scheme ) ? 'ir-hide' : ''; ?> dashicons dashicons-yes-alt" style="color: rgb(0, 127, 255);"></span>
								</div>
								<div class="ir-colors" name="custom">
									<span name="custom" style="background-color: #364246; border: 1px solid rgb(255, 255, 255);"></span>
									<span name="custom" style="background-color: #96B4CC; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
									<span name="custom" style="background-color: #021768; margin-left: -8px; border: 1px solid rgb(255, 255, 255);"></span>
								</div>
								<input style="display:none;" type="radio" name="ir_frontend_course_creator_color_scheme" class="ir_frontend_course_creator_color_scheme" value="custom" <?php checked( $ir_frontend_course_creator_color_scheme, 'custom' ); ?>>
							</div>
						</div>
						<div class="ir-custom-color-pattern" <?php echo ( 'custom' !== $ir_frontend_course_creator_color_scheme ) ? 'style="display:none;"' : ''; ?>>
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
										<input type="color" name="ir_frontend_course_creator_custom_primary" id="ir_frontend_course_creator_custom_primary" value="<?php echo esc_attr( $ir_frontend_course_creator_custom_primary ); ?>">
									</div>
								</div>
								<div class="ir-custom-color">
									<div class="ir-custom-color-label">
										<?php esc_html_e( 'Accent Color', 'wdm_instructor_role' ); ?>
										<div class="tooltip">
											<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
											<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'Accents color is used in side bar CTA selection.', 'wdm_instructor_role' ); ?></span>
										</div>
									</div>
									<div class="ir-custom-color-value">
										<input type="color" name="ir_frontend_course_creator_custom_accent" id="ir_frontend_course_creator_custom_accent" value="<?php echo esc_attr( $ir_frontend_course_creator_custom_accent ); ?>">
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
										<input type="color" name="ir_frontend_course_creator_custom_background" id="ir_frontend_course_creator_custom_background" value="<?php echo esc_attr( $ir_frontend_course_creator_custom_background ); ?>">
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
										<input type="color" name="ir_frontend_course_creator_custom_headings" id="ir_frontend_course_creator_custom_headings" value="<?php echo esc_attr( $ir_frontend_course_creator_custom_headings ); ?>">
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
										<input type="color" name="ir_frontend_course_creator_custom_text" id="ir_frontend_course_creator_custom_text" value="<?php echo esc_attr( $ir_frontend_course_creator_custom_text ); ?>">
									</div>
								</div>
								<div class="ir-custom-color">
									<div class="ir-custom-color-label">
										<?php esc_html_e( 'Borders', 'wdm_instructor_role' ); ?>
										<div class="tooltip">
											<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
											<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'This color is used for borders.', 'wdm_instructor_role' ); ?></span>
										</div>
									</div>
									<div class="ir-custom-color-value">
										<input type="color" name="ir_frontend_course_creator_custom_border" id="ir_frontend_course_creator_custom_border" value="<?php echo esc_attr( $ir_frontend_course_creator_custom_border ); ?>">
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
											<?php esc_html_e( 'Text - Light', 'wdm_instructor_role' ); ?>
											<div class="tooltip">
											<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
											<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'This color is used for secondary text and icons.', 'wdm_instructor_role' ); ?></span>
										</div>
										</div>
										<div class="ir-custom-color-value">
											<input type="color" name="ir_frontend_course_creator_custom_text_light" id="ir_frontend_course_creator_custom_text_light" value="<?php echo esc_attr( $ir_frontend_course_creator_custom_text_light ); ?>">
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
											<input type="color" name="ir_frontend_course_creator_custom_text_ex_light" id="ir_frontend_course_creator_custom_text_ex_light" value="<?php echo esc_attr( $ir_frontend_course_creator_custom_text_ex_light ); ?>">
										</div>
									</div>
									<div class="ir-custom-color">
										<div class="ir-custom-color-label"><?php esc_html_e( 'Text - Primary Button', 'wdm_instructor_role' ); ?><div class="tooltip">
												<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
												<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'This color is used for text in Primary buttons.', 'wdm_instructor_role' ); ?></span>
											</div>
										</div>
										<div class="ir-custom-color-value">
											<input type="color" name="ir_frontend_course_creator_custom_text_primary_btn" id="ir_frontend_course_creator_custom_text_primary_btn" value="<?php echo esc_attr( $ir_frontend_course_creator_custom_text_primary_btn ); ?>">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="ir-additional-settings" style="<?php echo ( 'on' !== $ir_enable_frontend_dashboard ) ? esc_html( 'display: none;' ) : ''; ?>">
						<p class="ir-setting-title-2 ir-setting-font-title"><?php esc_html_e( 'Font', 'wdm_instructor_role' ); ?></p>
						<div>
							<div class="ir-setting-section">
								<span class="ir-setting-label"><?php esc_html_e( 'Font Family : ', 'wdm_instructor_role' ); ?></span>
								<div class="ir-dropdown">
									<select class="ir-setting-select" name="ir_frontend_course_creator_font_family" id="ir_frontend_course_creator_font_family">
										<?php foreach ( $fonts as $key => $value ) : ?>
											<option value='<?php echo esc_attr( $value ); ?>' <?php selected( $ir_frontend_course_creator_font_family, $value ); ?>><?php echo esc_attr( $value ); ?></option>
										<?php endforeach; ?>
									</select>
									<span class="dashicons dashicons-arrow-down-alt2"></span>
									<div class="ir-flex-row">
										<p class="ir-help-text">
										<?php esc_html_e( 'Recommended Font:', 'wdm_instructor_role' ); ?>
										</p>
										<p class="ir-help-font">&nbsp;
										<?php esc_html_e( 'Open Sans', 'wdm_instructor_role' ); ?>
										</p>
									</div>
								</div>
							</div>
							<div class="ir-setting-section">
								<span class="ir-setting-label"><?php esc_html_e( 'Font Size : ', 'wdm_instructor_role' ); ?></span>
								<div class="ir-dropdown">
									<select class="ir-setting-select" name="ir_frontend_course_creator_font_size" id="ir_frontend_course_creator_font_size">
										<option value="14px" <?php selected( $ir_frontend_course_creator_font_size, '14px' ); ?>>
											<?php esc_html_e( 'Small', 'wdm_instructor_role' ); ?>
										</option>
										<option value="16px" <?php selected( $ir_frontend_course_creator_font_size, '16px' ); ?>>
											<?php esc_html_e( 'Normal', 'wdm_instructor_role' ); ?>
										</option>
										<option value="18px" <?php selected( $ir_frontend_course_creator_font_size, '18px' ); ?>>
											<?php esc_html_e( 'Large', 'wdm_instructor_role' ); ?>
										</option>
										<option value="20px" <?php selected( $ir_frontend_course_creator_font_size, '20px' ); ?>>
											<?php esc_html_e( 'Larger', 'wdm_instructor_role' ); ?>
										</option>
									</select>
									<span class="dashicons dashicons-arrow-down-alt2"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php wp_nonce_field( 'ir_frontend_dashboard_settings', 'ir_nonce' ); ?>
			<p style="text-align:<?php echo ( is_rtl() ) ? 'left' : 'right'; ?>">
			<?php submit_button( __( 'Save Settings', 'wdm_instructor_role' ), 'primary', 'ir-right-align', false ); ?>
			</p>
		</div>
	</form>
</div>
