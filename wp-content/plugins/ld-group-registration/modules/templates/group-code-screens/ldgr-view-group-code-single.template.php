<?php
/**
 * View group codes tab single row template.
 *
 * @since 4.1.0
 * @version 4.3.15
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="ldgr-group-code-row-<?php echo esc_attr( $group_code['id'] ); ?>" class="ldgr-group-code-item">
	<div class="ldgr-group-code-info">
		<span class="ldgr-group-code"><?php echo esc_html( $group_code['title'] ); ?></span>
		<div class="ldgr-gr-code-status-wrap group-code-status">
			<span><?php esc_html_e( 'Code status', 'wdm_ld_group' ); ?></span>
			<div class="ldgr-toggle-wrap <?php echo ( 'publish' === $group_code['status'] ) ? 'enabled' : ''; ?>">
				<span class="empty-bg">
					<span class="filled-bg"></span>
				</span>
			</div>
			<span class="dashicons dashicons-update"></span>
			<input
				type="hidden"
				class="ldgr-code-status-toggle"
				name="group-code-<?php echo esc_attr( $group_code['id'] ); ?>"
				data-id="<?php echo esc_attr( $group_code['id'] ); ?>"
				data-nonce="<?php echo esc_attr( wp_create_nonce( 'ldgr-group-code-' . $group_code['id'] . '-' . get_current_user_id() ) ); ?>"
				value="<?php echo ( 'publish' === $group_code['status'] ) ? 'on' : 'off'; ?>"
			/>
		</div>
	</div>
	<div class="ldgr-group-code-actions">
		<div class="ldgr-cp-code" data-id="<?php echo esc_attr( $group_code['id'] ); ?>">
			<i class="ldgr-icon-Copy"></i>
			<span class="ldgr-color"><?php esc_html_e( 'Copy Code', 'wdm_ld_group' ); ?></span>
		</div>
		<div class="ldgr-cp-code-url" data-id="<?php echo esc_attr( get_page_link( get_option( 'ldgr_group_code_enrollment_page' ) ) ) . '?ldgr_gr_code=' . esc_html( $group_code['title'] ); ?>">
			<i class="ldgr-icon-Copy"></i>
			<span class="ldgr-color"><?php esc_html_e( 'Copy URL', 'wdm_ld_group' ); ?></span>
		</div>
		<div class="ldgr-edit-code"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'ldgr-group-code-edit-' . $group_code['id'] . '-' . get_current_user_id() ) ); ?>"
			data-id="<?php echo esc_attr( $group_code['id'] ); ?>">
			<i class="ldgr-icon-Edit"></i>
			<span class="ldgr-color"><?php esc_html_e( 'Edit', 'wdm_ld_group' ); ?></span>
		</div>
		<div class="ldgr-delete-code"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'ldgr-group-code-delete-' . $group_code['id'] . '-' . get_current_user_id() ) ); ?>"
			data-id="<?php echo esc_attr( $group_code['id'] ); ?>">
			<i class="ldgr-icon-Trash"></i>
			<span class="ldgr-color"><?php esc_html_e( 'Delete', 'wdm_ld_group' ); ?></span>
		</div>
	</div>
</div>
