<?php
/**
 * Pattern Update PopUp Message Template
 *
 * @since 5.1.0
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter&family=Open+Sans&display=swap" rel="stylesheet">
<div id="wrld-custom-modal" class="wrld-custom-popup-modal">
	<div class="wrld-modal-content">
		<div class="wrld-modal-content-container">
			<div class="wrld-modal-info-section">
				<span id="ir_close_modal" class="dashicons dashicons-no"></span>
				<div class="ir-modal-head ir-center">
					<span><p class="ir-modal-head-text"><?php esc_html_e( 'Frontend Dashboard: Introducing Certificates Block', 'wdm_instructor_role' ); ?></p></span>
				</div>
				<div>
					<span>
						<p class="ir-modal-text">
							<?php esc_html_e( 'We have introduced a brand new Gutenberg block: Certificates Block which will help instructors and administrators manage their respective certificates from the frontend dashboard.', 'wdm_instructor_role' ); ?>
						</p>
					</span>
				</div>
				<div class="ir-heading-3">
					<?php esc_html_e( 'Update your Instructor Dashboard now !', 'wdm_instructor_role' ); ?>
				</div>
				<div class="ir-pattern-update-actions-container">
					<div class="ir-pattern-update-section">
						<p>
							<?php
							echo wp_kses(
								__( '<strong>Manually Add</strong> the Certificates Blocks.</br>Go to the dashboard page &#10142; edit &#10142; search for Instructor Role: Certificates for LearnDash &#10142; insert. </br><a href="https://learndash.com/support/docs/add-ons/frontend-dashboard-for-instructors-gutenberg-blocks-list/#course-reports-for-instructors" target="_blank">Learn More</a>', 'wdm_instructor_role' ),
								[
									'strong' => [],
									'a'      => [
										'href'   => [],
										'target' => [],
									],
									'br'     => [],
								]
							);
							?>
						</p>
						<a href="<?php echo esc_url_raw( $manual_edit_link ); ?>" class="ir-action-link"><?php esc_html_e( 'Manually Edit Page', 'wdm_instructor_role' ); ?><span class="dashicons dashicons-arrow-right-alt2"></span></a>
					</div>
					<div class="ir-section-divider"><?php esc_html_e( 'OR', 'wdm_instructor_role' ); ?></div>
					<div class="ir-pattern-update-section">
						<p>
							<?php
							echo wp_kses(
								__( '<strong>Note:</strong> While auto updating we will delete the current block pattern and replace with the new one including the certificates blocks. <strong>If any custom changes were made to the Dashboard page, they will be lost.</strong>', 'wdm_instructor_role' ),
								[
									'strong' => [],
								]
							);
							?>
						</p>
						<a href="<?php echo esc_url_raw( $auto_edit_link ); ?>" class="ir-action-link ir-primary-link"><?php esc_html_e( 'Auto Update Page', 'wdm_instructor_role' ); ?><span class="dashicons dashicons-arrow-right-alt2"></span></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
