<?php
/**
 * Template : LDGR edit group code screen template
 *
 * @since 4.1.0
 * @version 4.3.15
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="ldgr-group-code-setting ldgr-group-code-edit-section">
	<h2 class="ldgr-gcs-heading"><?php printf( esc_html__( 'Edit %s Code', 'wdm_ld_group' ), \LearnDash_Custom_Label::get_label( 'group' ) ); ?></h2>
	<div class="ldgr-group-code-messages">
		<span class="ldgr-message-close">&times;</span>
		<span class="ldgr-message-text"></span>
	</div>
	<form method="post" id="ldgr-group-code-edit-form" class="ldgr-form">
		<div class="ldgr-field">
			<div class="ldgr-toggle-wrap ldgr-dual-checkbox ldgr-gen-group-code">
				<span class="ldgr-left-val"><?php esc_html_e( 'Manual', 'wdm_ld_group' ); ?></span>
				<span class="empty-bg">
					<span class="filled-bg"></span>
				</span>
				<span class="ldgr-right-val"><?php esc_html_e( 'Auto generated', 'wdm_ld_group' ); ?></span>
			</div>
		</div>
		<div class="ldgr-field">
			<label><?php esc_html_e( 'Code', 'wdm_ld_group' ); ?></label>
			<input type="text" class="ldgr-textbox ldgr-w-300 ldgr-code-string" name="ldgr-code-string" autocomplete="off" required>
			<span class="dashicons dashicons-update"></span>
		</div>
		<?php if ( $is_unlimited ) : ?>
			<div class="ldgr-field">
				<label for="ldgr-code-limit">
					<?php esc_html_e( 'Number of Enrollments', 'wdm_ld_group' ); ?>
				</label>
				<input type="number" name="ldgr-code-limit" class="ldgr-textbox ldgr-w-300 ldgr-code-limit" min="1" required/>
			</div>
		<?php else : ?>
			<input type="hidden" name="ldgr-code-limit" value="<?php echo get_post_meta( $group_id, 'wdm_group_users_limit_' . $group_id, 1 ); ?>" />
		<?php endif; ?>
		<div class="ldgr-field">
			<label><?php esc_html_e( 'Number of Enrolled Users :', 'wdm_ld_group' ); ?></label>
			<strong><span class="ldgr-code-enrolled-users-count">0</span></strong>
		</div>
		<div class="ldgr-field ldgr-d-inline-flex ldgr-mr-40 ldgr-datepicker-icon">
			<label><?php esc_html_e( 'Valid from', 'wdm_ld_group' ); ?></label>
			<input type="text" class="ldgr-textbox ldgr-w-200 ldgr-code-date-range-from" name="ldgr-code-date-range-from" placeholder="dd-mm-yyyy" readonly>
		</div>
		<div class="ldgr-field ldgr-d-inline-flex ldgr-datepicker-icon">
			<label><?php esc_html_e( 'Valid till', 'wdm_ld_group' ); ?></label>
			<input type="text" class="ldgr-textbox ldgr-w-200 ldgr-code-date-range-to" name="ldgr-code-date-range-to" placeholder="dd-mm-yyyy" readonly>
		</div>
		<div class="ldgr-field">
			<div class="ldgr-toggle-wrap ldgr-validate-group-code">
				<span class="single-val"><?php esc_html_e( 'Validation Rules', 'wdm_ld_group' ); ?></span>
				<input type="hidden" name="ldgr-code-validation-check" class="ldgr-code-validation-check" />
				<span class="empty-bg">
					<span class="filled-bg"></span>
				</span>
			</div>
			<span class="ldgr-field-info">
				<?php esc_html_e( 'Validate enrollment based on IP address and email address domain name.', 'wdm_ld_group' ); ?>
			</span>
		</div>
		<div class="ldgr-validation-rules">
			<div class="ldgr-field ldgr-d-inline-flex ldgr-mr-40">
				<label><?php esc_html_e( 'Ip Address', 'wdm_ld_group' ); ?></label>
				<input type="text" class="ldgr-textbox ldgr-w-300 ldgr-code-ip-validation" name="ldgr-code-ip-validation">
			</div>
			<div class="ldgr-field ldgr-d-inline-flex">
				<label><?php esc_html_e( 'Domain Name', 'wdm_ld_group' ); ?></label>
				<input type="text" class="ldgr-textbox ldgr-w-300 ldgr-code-domain-validation" name="ldgr-code-domain-validation">
			</div>
		</div>
		<?php wp_nonce_field( 'ldgr-update-group-code-' . get_current_user_id(), 'ldgr_edit_nonce' ); ?>
		<input type="hidden" name="ldgr-code-status" value="off">
		<input type="hidden" name="ldgr-code-groups" value="<?php echo esc_attr( $group_id ); ?>" />
		<input type="hidden" name="ldgr-edit-group-code-id" id="ldgr-edit-group-code-id" value="" />

		<div class="ldgr-eg-actions">
			<span class="ldgr-btn gcs-update-cancel gcs-cancel"><?php esc_html_e( 'Cancel', 'wdm_ld_group' ); ?></span>
			<span class="ldgr-btn ldgr-bg-color solid ldgr-submit-form"><?php esc_html_e( 'Update', 'wdm_ld_group' ); ?></span>
		</div>
	</form>
</div>
