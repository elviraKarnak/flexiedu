<?php
/**
 * Instructor Setup Settings Template
 *
 * @package LearnDash\Instructor_Role
 *
 * cspell:ignore instuctor // ignoring misspelled words that we can't change now.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="ir-instructor-settings-tab-content">
	<div class="justified-top" style="margin-bottom:40px;">
		<div class="admin-setting-heading">
			<?php esc_html_e( 'Setup', 'wdm_instructor_role' ); ?>
		</div>
	</div>
	<div class="justified-top" style="gap:32px;">
		<div class="width-60">
			<div class="<?php echo ( 0 === $create_dashboard_status ) ? 'setup-container' : 'setup-container-complete'; ?>">
				<div class="flex-row">
					<div class="setup-title"><?php esc_html_e( 'Create a Dashboard', 'wdm_instructor_role' ); ?></div>
					<div class="<?php echo ( 0 === $create_dashboard_status ) ? 'setup-status-pending' : 'setup-status-complete'; ?>" >
					<?php if ( 0 === $create_dashboard_status ) : ?>
							<?php esc_html_e( 'Pending', 'wdm_instructor_role' ); ?>
						<?php else : ?>
							<?php esc_html_e( 'Complete', 'wdm_instructor_role' ); ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="setup-desc">
					<?php esc_html_e( 'You will be taken to a new page with a ready-made dashboard that you can customize. You can either save it as a draft for later edits or publish it to see it live.', 'wdm_instructor_role' ); ?>
				</div>
				<a class="setup-button" href="<?php echo admin_url( 'admin.php?page=instuctor&tab=dashboard_settings&onboarding=step_1' ); ?>" rel="noopener noreferrer">
					<?php esc_html_e( 'Create dashboard', 'wdm_instructor_role' ); ?>
				</a>
			</div>
			<div class="<?php echo ( 0 === $add_instructor_status ) ? 'setup-container' : 'setup-container-complete'; ?>">
				<div class="flex-row">
					<div class="setup-title"><?php esc_html_e( 'Add Instructors', 'wdm_instructor_role' ); ?></div>
					<div class="<?php echo ( 0 === $add_instructor_status ) ? 'setup-status-pending' : 'setup-status-complete'; ?>" >
					<?php if ( 0 === $add_instructor_status ) : ?>
						<?php esc_html_e( 'Pending', 'wdm_instructor_role' ); ?>
						<?php else : ?>
							<?php esc_html_e( 'Complete', 'wdm_instructor_role' ); ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="setup-desc">
					<?php esc_html_e( 'Grant Instructor Role status to new or existing users, and manage your existing Instructors.', 'wdm_instructor_role' ); ?>
				</div>
				<a class="setup-button" href="<?php echo admin_url( 'admin.php?page=instuctor&tab=instructor&onboarding=step_1' ); ?>" rel="noopener noreferrer">
					<?php esc_html_e( 'Add Instructors', 'wdm_instructor_role' ); ?>
				</a>
			</div>
			<div class="<?php echo ( 0 === $instructor_settings_status ) ? 'setup-container' : 'setup-container-complete'; ?>">
				<div class="flex-row">
					<div class="setup-title"><?php esc_html_e( 'Instructor Settings', 'wdm_instructor_role' ); ?></div>
					<div class="<?php echo ( 0 === $instructor_settings_status ) ? 'setup-status-pending' : 'setup-status-complete'; ?>" >
					<?php if ( 0 === $instructor_settings_status ) : ?>
						<?php esc_html_e( 'Pending', 'wdm_instructor_role' ); ?>
						<?php else : ?>
							<?php esc_html_e( 'Complete', 'wdm_instructor_role' ); ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="setup-desc">
					<?php esc_html_e( 'Configure Instructor settings for emails, student communication, profile, and more.', 'wdm_instructor_role' ); ?>
				</div>
				<a class="setup-button" href="<?php echo admin_url( 'admin.php?page=instuctor&tab=ir-profile&onboarding=step_1' ); ?>" rel="noopener noreferrer">
					<?php esc_html_e( 'Instructor Settings', 'wdm_instructor_role' ); ?>
				</a>
			</div>
			<div class="<?php echo ( 0 === $commission_status ) ? 'setup-container' : 'setup-container-complete'; ?>">
				<div class="flex-row">
					<div class="setup-title"><?php esc_html_e( 'Commissions', 'wdm_instructor_role' ); ?></div>
					<div class="<?php echo ( 0 === $commission_status ) ? 'setup-status-pending' : 'setup-status-complete'; ?>" >
					<?php if ( 0 === $commission_status ) : ?>
						<?php esc_html_e( 'Pending', 'wdm_instructor_role' ); ?>
						<?php else : ?>
							<?php esc_html_e( 'Complete', 'wdm_instructor_role' ); ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="setup-desc">
					<?php esc_html_e( 'You can setup commissions for your instructor in the commissions settings where you give all instructor a commission % or different', 'wdm_instructor_role' ); ?>
				</div>
				<a class="setup-button" href="<?php echo admin_url( 'admin.php?page=instuctor&tab=commission_report&onboarding=step_1' ); ?>" rel="noopener noreferrer">
					<?php esc_html_e( 'Commissions', 'wdm_instructor_role' ); ?>
				</a>
			</div>
			<div class="admin-setting-heading" style="padding-top:40px; padding-bottom:24px;">
				<?php esc_html_e( 'Advanced settings', 'wdm_instructor_role' ); ?>
			</div>
			<div class="<?php echo ( 0 === $course_creation_status ) ? 'setup-container' : 'setup-container-complete'; ?>">
				<div class="flex-row">
					<div class="setup-title"><?php esc_html_e( 'Course creation', 'wdm_instructor_role' ); ?></div>
					<div class="<?php echo ( 0 === $course_creation_status ) ? 'setup-status-pending' : 'setup-status-complete'; ?>" >
					<?php if ( 0 === $course_creation_status ) : ?>
						<?php esc_html_e( 'Pending', 'wdm_instructor_role' ); ?>
						<?php else : ?>
							<?php esc_html_e( 'Complete', 'wdm_instructor_role' ); ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="setup-desc">
				<?php esc_html_e( 'Configure your course creation settings to customize categories, purchase and selling options, and course reviews.', 'wdm_instructor_role' ); ?>
				</div>
				<a class="setup-button" href="<?php echo admin_url( 'admin.php?page=instuctor&tab=ir-frontend-dashboard&onboarding=step_1' ); ?>" rel="noopener noreferrer">
					<?php esc_html_e( 'Course creation', 'wdm_instructor_role' ); ?>
				</a>
			</div>
</div>
		<div class="sidebar-container">
			<div class="document-container">
				<div class="document-title"><?php esc_html_e( 'Documentations', 'wdm_instructor_role' ); ?></div>
				<ul id="documents">
					<li><a href="https://go.learndash.com/iroverview" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Installation, Activation, and Prerequisites', 'wdm_instructor_role' ); ?></a></li>
					<li><a href="https://learndash.com/support/docs/add-ons/frontend-dashboard-installation-guide/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Frontend Dashboard: Installation Guide', 'wdm_instructor_role' ); ?></a></li>
					<li><a href="https://learndash.com/support/docs/add-ons/how-to-customize-the-frontend-dashboard-gutenberg-editor-and-global-settings/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'How to Customize the Frontend Dashboard', 'wdm_instructor_role' ); ?></a></li>
					<li><a href="https://learndash.com/support/docs/add-ons/how-to-disable-the-backend-dashboard-for-instructors/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'How to disable the Backend dashboard(WP) for Instructors', 'wdm_instructor_role' ); ?></a></li>
				</ul>
				<ul id="documents" class="hidden" style="display:none;">
					<li><a href="#" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'How to make an instructor', 'wdm_instructor_role' ); ?></a></li>
					<li><a href="#" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'How to make an instructor', 'wdm_instructor_role' ); ?></a></li>
					<li><a href="#" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'How to make an instructor', 'wdm_instructor_role' ); ?></a></li>
					<li><a href="#" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'How to make an instructor', 'wdm_instructor_role' ); ?></a></li>
				</ul>
				<a class="documents-view-all-button" href="<?php echo admin_url( 'admin.php?page=instuctor&tab=docs_and_videos' ); ?>" onclick="toggleViewAll();">
					<?php esc_html_e( 'View all', 'wdm_instructor_role' ); ?>
				</a>
			</div>

			<div class="document-container">
				<div class="document-title"><?php esc_html_e( 'Tutorial videos', 'wdm_instructor_role' ); ?></div>
				<ul id="documents">
					<li><a href="https://www.youtube.com/watch?v=GUe4rHAMH9Q" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'How to onboard instructors?', 'wdm_instructor_role' ); ?></a></li>
					<li><a href="https://www.youtube.com/watch?v=NfqoMcsWGTw&t=1s" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'How to set an instructor Commission?', 'wdm_instructor_role' ); ?></a></li>
					<li><a href="https://www.youtube.com/watch?v=Bi7Tu2M-KEo&t=1s" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Frontend Course creator Settings', 'wdm_instructor_role' ); ?></a></li>
					<li><a href="https://www.youtube.com/watch?v=6MYhMjgb7sk" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Instructor List and Managing Instructors', 'wdm_instructor_role' ); ?></a></li>
				</ul>
				<ul id="documents" class="hidden" style="display:none;">
					<li><a href="#" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'How to make an instructor', 'wdm_instructor_role' ); ?></a></li>
					<li><a href="#" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'How to make an instructor', 'wdm_instructor_role' ); ?></a></li>
					<li><a href="#" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'How to make an instructor', 'wdm_instructor_role' ); ?></a></li>
					<li><a href="#" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'How to make an instructor', 'wdm_instructor_role' ); ?></a></li>
				</ul>
				<a class="documents-view-all-button" href="<?php echo admin_url( 'admin.php?page=instuctor&tab=docs_and_videos' ); ?>" onclick="toggleViewAll();">
					<?php esc_html_e( 'View all', 'wdm_instructor_role' ); ?>
				</a>
			</div>

			<div class="document-container">
				<div class="document-title"><?php esc_html_e( 'Help', 'wdm_instructor_role' ); ?></div>
				<div class="setup-desc">
					<?php
					echo wp_kses_post(
						sprintf(
							// translators: placeholders: HTML for a link to the support page.
							__( 'Got questions? We\'re here to help! Reach out to us anytime for assistance with our plugin. %1$sWe\'re always available to answer your queries and provide support.%2$s', 'wdm_instructor_role' ),
							'<a href="https://account.learndash.com/?tab=support" target="_blank">',
							'</a>'
						)
					);
					?>
				</div>
				<a class="documents-view-all-button" href="https://account.learndash.com/?tab=support" target="_blank" onclick="toggleViewAll();">
					<?php esc_html_e( 'Contact now', 'wdm_instructor_role' ); ?>
				</a>
			</div>

		</div>
	</div>
</div>

<script>
	function toggleViewAll() {
		var hiddenDocuments = document.querySelector('.hidden');
		var viewAllButton = document.querySelector('.documents-view-all-button');

		// Toggle the visibility of the hidden ul and update the view all button.
		if (hiddenDocuments.style.display === 'none' || hiddenDocuments.style.display === '') {
			hiddenDocuments.style.display = 'block';
			viewAllButton.style.display = 'none';
		} else {
			hiddenDocuments.style.display = 'none';
			viewAllButton.style.display = 'inline-block';
		}
	}
</script>
