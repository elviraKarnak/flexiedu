<?php
/**
 * Instructor Commission Settings Template
 *
 * @since 3.5.5
 *
 * @var array $instructors      List of all instructors data.
 *
 * @package LearnDash\Instructor_Role
 *
 * cspell:ignore instuctor // ignoring misspelled words that we can't change now.
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="ir-instructor-settings-tab-content">
	<!-- Manage instructors screen -->
	<?php
		echo do_blocks(
			'<!-- wp:instructor-role/wisdm-manage-instructor -->
			<div class="wp-block-instructor-role-wisdm-manage-instructor"><div class="wisdm-manage-instructor"></div></div>
			<!-- /wp:instructor-role/wisdm-manage-instructor -->'
		);
		?>
	<?php if ( 'step_1' === $onboarding ) : ?>
	<div class="ir-onboarding-container">
		<h3><?php esc_html_e( 'Instructor setup', 'wdm_instructor_role' ); ?></h3>
		<span><?php esc_html_e( 'Click on add Instructor to add/assign new Instructor', 'wdm_instructor_role' ); ?></span>
		<span target="_blank" rel="noopener noreferrer">
	</div>

	<div class="ir-onboarding-container step_existing_user">
		<h3><?php esc_html_e( 'Instructor setup', 'wdm_instructor_role' ); ?></h3>
		<span><?php esc_html_e( 'Select existing user to assign as a instructor and click on add instructor button to save', 'wdm_instructor_role' ); ?></span>
		<span target="_blank" rel="noopener noreferrer">
	</div>

	<div class="ir-onboarding-container step_new_user">
		<h3><?php esc_html_e( 'Instructor setup', 'wdm_instructor_role' ); ?></h3>
		<span><?php esc_html_e( 'Fill all the details below to create a new user and assign as a instructor', 'wdm_instructor_role' ); ?></span>
		<span target="_blank" rel="noopener noreferrer">
	</div>

	<div class="ir-onboarding-container step_last">
		<h3><?php esc_html_e( 'Instructor setup', 'wdm_instructor_role' ); ?></h3>
		<span><?php esc_html_e( 'Instructor is successfully added, add more by clicking on add instructor button or click on Resume Setup to continue setup', 'wdm_instructor_role' ); ?></span>
		<a class="setup-button ir-primary-btn" href="<?php echo admin_url( 'admin.php?page=instuctor&tab=setup' ); ?>" rel="noopener noreferrer">
			<?php esc_html_e( 'Resume Setup', 'wdm_instructor_role' ); ?>
		</a>
	</div>
	<?php endif; ?>

	<?php if ( 'commission_step' === $onboarding ) : ?>
	<div class="ir-onboarding-container ir_edit_step ir_commission_step">
		<h3><?php esc_html_e( 'Instructor setup', 'wdm_instructor_role' ); ?></h3>
		<span><?php esc_html_e( 'To assign distinct commission percentages click Edit against each Instructor name and save it.', 'wdm_instructor_role' ); ?></span>
		<span target="_blank" rel="noopener noreferrer">
	</div>

	<div class="ir-onboarding-container ir_assign_step ir_commission_step">
		<h3><?php esc_html_e( 'Instructor setup', 'wdm_instructor_role' ); ?></h3>
		<span><?php esc_html_e( 'Assign Commissions to more Instructors else Resume Setup', 'wdm_instructor_role' ); ?></span>
		<a class="setup-button ir-primary-btn" href="<?php echo admin_url( 'admin.php?page=instuctor&tab=setup' ); ?>" rel="noopener noreferrer">
			<?php esc_html_e( 'Resume Setup', 'wdm_instructor_role' ); ?>
		</a>
	</div>
	<?php endif; ?>
	<!-- add new instructor -->


<!-- old code -->
<br/>
<div id="update_commission_message"></div>
		</div>
