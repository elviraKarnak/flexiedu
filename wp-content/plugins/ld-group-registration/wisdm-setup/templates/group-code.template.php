<?php
/**
 * Setup Wizard: Group Code Settings View Template.
 *
 * @since 4.2.0
 * @version 4.3.15
 *
 * @var object  $wizard_handler         Setup Wizard handler class object.
 * @var mixed   $ldgr_enable_group_code
 * @var mixed   $ldgr_group_code_enrollment_page
 * @var mixed   $ldgr_enable_gdpr
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;
?>
<form method="post" action="<?php echo esc_url( $wizard_handler->get_next_step_link() ); ?>">
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="ldgr_enable_group_code">
						<?php esc_html_e( 'Group Code', 'wdm_ld_group' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="ldgr_enable_group_code" id="ldgr_enable_group_code" class="switch-input" <?php checked( $ldgr_enable_group_code, 'on' ); ?>>
					<label for="ldgr_enable_group_code" class="switch-label">
						<span class="toggle--on"><?php esc_html_e( 'On', 'wdm_ld_group' ); ?></span>
						<span class="toggle--off"><?php esc_html_e( 'Off', 'wdm_ld_group' ); ?></span>
					</label>
					<span class="description">
						<?php
						printf(
							// translators: Link to group code documentation.
							esc_html__( 'Enable to allow group leaders to create group codes for easier group enrollments. Learn more about it %s', 'wdm_ld_group' ),
							'<a target="_blank" href="https://www.learndash.com/support/docs/add-ons/group-registration-for-learndash/">here</a>'
						);
						?>
						</span>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="ldgr_group_code_enrollment_page">
						<?php esc_html_e( 'Group Code Enrollments/Registrations', 'wdm_ld_group' ); ?>
					</label>
				</th>
				<td>
					<?php
					echo wp_dropdown_pages(
						[
							'name'              => 'ldgr_group_code_enrollment_page',
							'echo'              => 0,
							'show_option_none'  => __( '&mdash; Select &mdash;' ),
							'option_none_value' => '0',
							'selected'          => $ldgr_group_code_enrollment_page,
						]
					);
					?>
					<span class="description">
						<?php
						printf(
							// translators: group code reg form shortcode.
							esc_html__( 'Page used to enroll and/or register users via group codes. Add %s inside the page if not added already.', 'wdm_ld_group' ),
							'<code>[ldgr-group-code-registration-form]</code>'
						);
						?>
					</span>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="ldgr_enable_gdpr">
						<?php esc_html_e( 'GDPR', 'wdm_ld_group' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="ldgr_enable_gdpr" id="ldgr_enable_gdpr" class="switch-input" <?php checked( $ldgr_enable_gdpr, 'on' ); ?>>
					<label for="ldgr_enable_gdpr" class="switch-label">
						<span class="toggle--on"><?php esc_html_e( 'On', 'wdm_ld_group' ); ?></span>
						<span class="toggle--off"><?php esc_html_e( 'Off', 'wdm_ld_group' ); ?></span>
					</label>
					<span class="description">
						<?php esc_html_e( 'Enable GDPR check for Group Code Registration/Enrollment Form', 'wdm_ld_group' ); ?>
						</span>
				</td>
			</tr>
		</tbody>
	</table>
	<p class="wc-setup-actions step">
		<input type="submit" class="button-primary button button-large button-next" value="<?php esc_html_e( 'Continue', 'wdm_ld_group' ); ?>" name="save_step">
		<input type="hidden" name="wisdm_setup_step" value="<?php echo esc_attr( $wizard_handler->get_current_step_slug() ); ?>" />
		<a href="<?php echo esc_url( $wizard_handler->get_next_step_link() ); ?>" class="button button-large button-next"><?php esc_html_e( 'Skip this step', 'wdm_ld_group' ); ?></a>
	<?php wp_nonce_field( 'setup_group_code_settings', 'wisdm_setup_nonce' ); ?>
	</p>
</form>
