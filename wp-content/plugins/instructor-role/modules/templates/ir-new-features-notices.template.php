<?php
/**
 * New Features Notices Template
 *
 * @since 4.3.0
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter&family=Open+Sans&display=swap" rel="stylesheet">
<div class="wrap" style="margin-top: 50px;">
	<div class="ir-admin-notices-container">
		<div class="ir-admin-notice-logo">
			<img src="<?php echo esc_url_raw( $image_url ); ?>" alt="<?php esc_html_e( 'LearnDash Logo', 'wdm_instructor_role' ); ?>" style="width:100px; height: auto">
		</div>
		<div class="ir-admin-notice-content">
			<div class="ir-admin-notice-heading">
				<span style="color: #008AD8">
					<?php esc_html_e( 'Frontend Dashboard: Introducing Certificates Block', 'wdm_instructor_role' ); ?>
				</span>
			</div>
			<div class="ir-admin-notice-text">
				<?php esc_html_e( 'We have introduced a brand new Gutenberg block: Certificates block which will help instructors and administrators to manage their respective certificates from the frontend dashboard.', 'wdm_instructor_role' ); ?>
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
		<div class="ir-admin-notice-container-actions">
			<span class="dashicons dashicons-no-alt"></span>
		</div>
	</div>
</div>
