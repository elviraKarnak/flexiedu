<?php
/**
 * Group Code Settings Template.
 *
 * @since 4.1.0
 * @version 4.3.15
 *
 * @var string  $ldgr_group_code_enable_recaptcha
 * @var string  $ldgr_recaptcha_site_key
 * @var string  $ldgr_recaptcha_secret_key
 * @var string  $ldgr_group_code_enrollment_message
 * @var array   $ldgr_group_code_placeholders
 * @var string  $users_can_register
 * @var string  $ldgr_group_code_redirect
 * @var string  $ldgr_group_code_redirect_page
 * @var string  $ldgr_enable_gdpr
 * @var string  $ldgr_gdpr_checkbox_message
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;
?>
<h2><?php esc_html_e( 'Group Code', 'wdm_ld_group' ); ?></h2>
<?php if ( $users_can_register ) : ?>
	<div class="ldgr-settings">
		<form method="post" id="ldgr-group-code-settings-form">

			<div>
				<span>
					<?php esc_html_e( 'Enable group code', 'wdm_ld_group' ); ?>
				</span>
				<label class="wdm-switch">
					<input type="checkbox" name="ldgr_enable_group_code" <?php checked( $ldgr_enable_group_code, 'on' ); ?>>
					<span class="wdm-slider round"></span>
				</label>
				<p>
					<em><?php _e( sprintf( __( 'How does it work ? Learn more %s', 'wdm_ld_group' ), '<a target="_blank" href="https://www.learndash.com/support/docs/add-ons/group-registration-for-learndash/">here</a>' ) ); ?></em>
				</p>
			</div>

			<div>
				<span>
					<?php esc_html_e( 'Enable reCAPTCHA v2 Checkbox for group code registration and enrollment form', 'wdm_ld_group' ); ?>
				</span>
				<label class="wdm-switch">
					<input type="checkbox" name="ldgr_group_code_enable_recaptcha" <?php checked( $ldgr_group_code_enable_recaptcha, 'on' ); ?>>
					<span class="wdm-slider round"></span>
				</label>
				<p>
					<em><?php _e( sprintf( __( 'Learn more about reCAPTCHA and get your site keys from %s', 'wdm_ld_group' ), '<a target="_blank" href="https://www.google.com/recaptcha/admin">here</a>' ) ); ?></em>
				</p>
			</div>

			<div class="ldgr-recaptcha-settings" <?php echo ( 'on' != $ldgr_group_code_enable_recaptcha ) ? 'style="display:none;"' : ''; ?>>
				<p>
					<label for="ldgr_recaptcha_site_key">
						<?php esc_html_e( 'Enter your reCAPTCHA Site Key', 'wdm_ld_group' ); ?>
					</label>
					<input type="text" name="ldgr_recaptcha_site_key" id="ldgr_recaptcha_site_key" value="<?php echo $ldgr_recaptcha_site_key; ?>" size="50" />
				</p>
				<p>
					<label for="ldgr_recaptcha_secret_key">
						<?php esc_html_e( 'Enter your reCAPTCHA Secret Key', 'wdm_ld_group' ); ?>
					</label>
					<input type="text" name="ldgr_recaptcha_secret_key" id="ldgr_recaptcha_secret_key" value="<?php echo $ldgr_recaptcha_secret_key; ?>" size="50" />
				</p>
			</div>

			<div class="ldgr-group-code-enrollment-page">
				<label for="ldgr_group_code_enrollment_page">
					<?php esc_html_e( 'Select page for group code enrollments/registrations', 'wdm_ld_group' ); ?>
				</label>
				<select name="ldgr_group_code_enrollment_page" id="ldgr_group_code_enrollment_page">
					<option value="-1"><?php esc_html_e( 'Select a page', 'wdm_ld_group' ); ?></option>
					<?php foreach ( $pages as $page_id ) : ?>
						<option
							value="<?php echo esc_attr( $page_id ); ?>"
							<?php selected( $ldgr_group_code_enrollment_page, $page_id ); ?>>
							<?php echo get_the_title( $page_id ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<p>
					<em><?php _e( 'Page used to enroll and/or register users via group codes. Add <code>[ldgr-group-code-registration-form]</code> inside the page if not added already', 'wdm_ld_group' ); ?></em>
				</p>
			</div>

			<div>
				<span>
					<?php esc_html_e( 'Enable redirects on successful enrollment', 'wdm_ld_group' ); ?>
				</span>
				<label class="wdm-switch">
					<input type="checkbox" name="ldgr_group_code_redirect" <?php checked( $ldgr_group_code_redirect, 'on' ); ?>>
					<span class="wdm-slider round"></span>
				</label>
				<p class="ldgr-enrollment-redirect-tip <?php echo ( 'on' == $ldgr_group_code_redirect ) ? 'ldgr-hide' : ''; ?>">
					<em><?php esc_html_e( 'Enable to redirect users on successful enrollment to the selected page', 'wdm_ld_group' ); ?></em>
				</p>
				<p class="ldgr-enrollment-message-tip <?php echo ( 'on' != $ldgr_group_code_redirect ) ? 'ldgr-hide' : ''; ?>">
					<em><?php esc_html_e( 'Disable to display a custom message on successful enrollment', 'wdm_ld_group' ); ?></em>
				</p>
			</div>

			<div class="ldgr-enrollment-redirect-div <?php echo ( 'on' != $ldgr_group_code_redirect ) ? 'ldgr-hide' : ''; ?>">
				<div class="ldgr-group-code-redirect-page">
					<label for="ldgr_group_code_redirect_page">
						<?php esc_html_e( 'Select page for users to be redirect on successful enrollments/registrations', 'wdm_ld_group' ); ?>
					</label>
					<select name="ldgr_group_code_redirect_page" id="ldgr_group_code_redirect_page">
						<option value="-1"><?php esc_html_e( 'Select a page', 'wdm_ld_group' ); ?></option>
						<?php foreach ( $pages as $page_id ) : ?>
							<option
								value="<?php echo esc_attr( $page_id ); ?>"
								<?php selected( $ldgr_group_code_redirect_page, $page_id ); ?>>
								<?php echo get_the_title( $page_id ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<p>
						<em><?php _e( 'Page users will be redirected to on successful enrollment/registration via group codes. If not set, users will be redirected to home page.', 'wdm_ld_group' ); ?></em>
					</p>
				</div>
			</div>

			<div class="ldgr-enrollment-message-div <?php echo ( 'on' == $ldgr_group_code_redirect ) ? 'ldgr-hide' : ''; ?>">
				<div class="ldgr-enrollment-message-label">
					<span>
						<?php esc_html_e( 'Message on group code enrollment or registration', 'wdm_ld_group' ); ?>
					</span>
					<p>
						<strong><?php esc_html_e( 'Available Placeholders', 'wdm_ld_group' ); ?></strong> : <em><?php echo esc_attr( implode( ' , ', $ldgr_group_code_placeholders ) ); ?></em>
					</p>
				</div>
				<div class="ldgr-enrollment-message">
					<?php
						wp_editor(
							stripslashes( $ldgr_group_code_enrollment_message ),
							'ldgr_group_code_enrollment_message',
							[
								// 'wpautop'=>true,
								'media_buttons'    => false,
								'drag_drop_upload' => false,
								'textarea_rows'    => 5,
								'textarea_name'    => 'ldgr_group_code_enrollment_message',
							]
						);
					?>
				</div>
			</div>

			<div>
				<span>
					<?php esc_html_e( 'Enable GDPR check for group code registration/enrollment form', 'wdm_ld_group' ); ?>
				</span>
				<label class="wdm-switch">
					<input type="checkbox" name="ldgr_enable_gdpr" <?php checked( $ldgr_enable_gdpr, 'on' ); ?>>
					<span class="wdm-slider round"></span>
				</label>
			</div>

			<div class="ldgr-gdpr-checkbox-div <?php echo ( 'on' != $ldgr_enable_gdpr ) ? 'ldgr-hide' : ''; ?>">
				<div class="ldgr-gdpr-checkbox">
					<label for="ldgr_gdpr_checkbox_message">
						<?php esc_html_e( 'Enter the GDPR agreement message to be displayed on the group registration and enrollment form', 'wdm_ld_group' ); ?>
					</label>
					<p>
						<em><?php _e( 'You can make use of <code>{privacy_policy}</code> to display the privacy policy page link', 'wdm_ld_group' ); ?></em>
					</p>
					<div class="ldgr-gdpr-checkbox-message">
						<?php
							wp_editor(
								stripslashes( $ldgr_gdpr_checkbox_message ),
								'ldgr_gdpr_checkbox_message',
								[
									// 'wpautop'=>true,
									'media_buttons'    => false,
									'drag_drop_upload' => false,
									'textarea_rows'    => 5,
									'textarea_name'    => 'ldgr_gdpr_checkbox_message',
								]
							);
						?>
					</div>
				</div>
			</div>

			<?php wp_nonce_field( 'ldgr_save_group_code_settings', 'ldgr_nonce' ); ?>

			<?php submit_button( __( 'Save', 'wdm_ld_group' ) ); ?>

		</form>
	</div>
<?php else : ?>
	<div>
		<?php printf( __( 'Please enable user registrations for the website from %s to use Group Codes', 'wdm_ld_group' ), '<a href="' . admin_url( 'options-general.php' ) . '">General Settings</a>' ); ?>
	</div>
<?php endif; ?>
