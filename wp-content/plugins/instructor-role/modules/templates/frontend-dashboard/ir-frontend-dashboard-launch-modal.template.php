<?php
/**
 * Frontend Dashboard Launch Modal Template.
 *
 * @since 5.0.0
 *
 * @package LearnDash\Instructor_Role
 *
 * cspell:ignore instuctor // ignoring misspelled words that we can't change now.
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="ir-welcome-modal" class="ir-welcome-popup-modal" style="display:none;">
	<div class="ir-modal-content-container">
		<div class="ir-content-section" style='background:url("<?php echo esc_attr( $background_img ); ?>")'>
			<div class="ir-content-head">
				<span>
					<?php esc_html_e( 'Dashboard is Created.', 'wdm_instructor_role' ); ?>
				</span>
			</div>
			<div class="ir-content-body free">
				<img src="<?php echo esc_attr( $center_img ); ?>" alt="Dashboard Created">
			</div>
			<div class="ir-content-footer">
				<button
					class="primary-bg  modal-button modal-button-view-dashboard"><?php esc_html_e( 'View Dashboard', 'wdm_instructor_role' ); ?>
					<i class="fa fa-chevron-right" aria-hidden="true"></i></button>

				<button
				class="primary-bg modal-button modal-button-resume-setup ir-primary-btn" onclick="window.location.href='<?php echo admin_url( 'admin.php?page=instuctor&tab=setup' ); ?>'"><?php esc_html_e( 'Resume Setup', 'wdm_instructor_role' ); ?>
				<i class="fa fa-chevron-right" aria-hidden="true"></i></button>
			</div>
		</div>
	</div>
</div>
