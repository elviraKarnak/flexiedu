<?php
/**
 * Additional instructor settings template
 *
 * @since 5.0.0
 *
 * @param string $ir_disable_backend_dashboard
 * @param string $ir_student_communication_check
 * @param string $ir_ld_category_check
 * @param string $review_product
 * @param string $review_course
 * @param string $review_download
 * @param string $wl8_en_inst_mail
 * @param string $wdm_enable_instructor_course_mail
 * @param string $wl8_en_inst_commi // cspell:disable-line .
 * @param string $wdm_login_redirect
 * @param array  $page_args
 * @param string $login_redirect_tooltip
 * @param string $enable_ld_category
 * @param string $enable_wp_category
 * @param string $enable_permalinks
 * @param string $enable_elu_header
 * @param string $enable_elu_layout
 * @param string $enable_elu_cover
 * @param string $enable_bb_cover
 * @param string $enable_open_pricing
 * @param string $enable_free_pricing
 * @param string $enable_buy_pricing
 * @param string $enable_recurring_pricing
 * @param string $enable_closed_pricing
 * @param string $is_ld_category
 * @param string $is_wp_category
 * @param string $active_theme
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="ir-general-settings">
	<h2>
		<?php esc_html_e( 'General Settings', 'wdm_instructor_role' ); ?>
	</h2>
	<form method="post" action="">
		<?php wp_nonce_field( 'instructor_setting_nonce_action', 'instructor_setting_nonce', true, true ); ?>

		<?php
		/**
		 * Fires before settings table
		 *
		 * @since 3.5.0
		 */
		do_action( 'wdmir_settings_before_table' );
		?>

		<div class="ir-dashboard-builder-settings-section ir-settings-section">
			<div class="ir-settings-section-title">
				<span><?php esc_html_e( 'Instructor Dashboard Builder', 'wdm_instructor_role' ); ?></span>
				<p class="ir-section-note">
					<?php esc_html_e( 'Allow or restrict access to the "Instructor Dashboard Tabs" builder block for all users.', 'wdm_instructor_role' ); ?>
				</p>
			</div>
			<div class="ir-separator"></div>
			<div class="ir-settings-section-container">
				<div class="ir-section-setting">
					<label for="enable_tabs_access">
						<input
							name="enable_tabs_access"
							type="checkbox"
							id="enable_tabs_access"
							<?php echo esc_attr( $enable_tabs_access ); ?>
						/>
						<?php esc_html_e( 'Enable Student Dashboard', 'wdm_instructor_role' ); ?>
					</label>
					<p class="ir-section-setting-note">
						<?php esc_html_e( 'Enable this setting to create frontend dashboard for any logged-in user.', 'wdm_instructor_role' ); ?>
					</p>
					<p class="ir-section-setting-note">
						<?php
						echo wp_kses(
							__( '<strong>Note:</strong> By default, only administrators and instructors can view the page with the Instructor Dashboard tabs block and other users are redirected to the home page. If the above setting is enabled, any logged-in user can access a page with the block and view the dashboard contents if they have access to it.', 'wdm_instructor_role' ),
							[
								'strong' => [],
							]
						);
						?>
					</p>
				</div>
			</div>
		</div>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'wdm_instructor_role' ); ?>">
		</p>
	</form>
</div>
