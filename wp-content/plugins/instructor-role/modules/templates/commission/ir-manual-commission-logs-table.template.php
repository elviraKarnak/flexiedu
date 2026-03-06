<?php
/**
 * Manual Commission Logs Table Template.
 *
 * @since 4.2.0
 *
 * @var int     $instructor_id      ID of the instructor.
 * @var array   $commission_logs    Array of manual commission logs for the instructor.
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<div class="ir-manual-commission-logs-container">
	<table class="row-border footable datatable" data-filter="#filter">
		<thead>
			<th><?php esc_html_e( 'Date', 'wdm_instructor_role' ); ?></th>
			<th><?php esc_html_e( 'Amount', 'wdm_instructor_role' ); ?></th>
			<th><?php esc_html_e( 'Remaining', 'wdm_instructor_role' ); ?></th>
			<th><?php esc_html_e( 'Note', 'wdm_instructor_role' ); ?></th>
			<th><?php esc_html_e( 'Actions', 'wdm_instructor_role' ); ?></th>
		</thead>
		<tbody>
			<?php foreach ( $commission_logs as $commission ) : ?>
			<tr id="ir_com_log_<?php echo esc_attr( $commission['id'] ); ?>" data-log-id="<?php echo esc_attr( $commission['id'] ); ?>" data-nonce=<?php echo esc_attr( wp_create_nonce( 'ir_commission_log_actions' ) ); ?>>
				<td class="ir-log-date" data-value="<?php echo esc_attr( $commission['date_time'] ); ?>"><?php echo esc_html( $commission['date_time'] ); ?></td>
				<td class="ir-log-amount" data-value="<?php echo esc_attr( number_format( $commission['amount'], 2 ) ); ?>"><?php echo esc_html( number_format( $commission['amount'], 2 ) ); ?></td>
				<td class="ir-log-remaining" data-value="<?php echo esc_attr( number_format( $commission['remaining'], 2 ) ); ?>"><?php echo esc_html( number_format( $commission['remaining'], 2 ) ); ?></td>
				<td class="ir-log-note" data-value="<?php echo esc_attr( $commission['notes'] ); ?>"><?php echo esc_html( $commission['notes'] ); ?></td>
				<td class="ir-log-actions">
					<span class="dashicons dashicons-edit"></span>
					<span class="dashicons dashicons-no"></span>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<div class="ir-loader-screen" style="display: none;">
		<span class="dashicons dashicons-update"></span>
	</div>
</div>
<div class="ir-modal-background" style="display: none;">
	<div id="ir-commission-log-update-modal">
		<div class="ir-modal-actions-top">
			<span class="dashicons dashicons-no"></span>
		</div>
		<div class="ir-modal-body">
			<div class="ir-modal-input">
				<label for=""><?php esc_html_e( 'Date', 'wdm_instructor_role' ); ?></label>
				<input type="text" name="ir_log_date" id="ir_log_date" readonly>
			</div>
			<div class="ir-modal-input">
				<label for=""><?php esc_html_e( 'Amount', 'wdm_instructor_role' ); ?></label>
				<input type="text" name="ir_log_amount" id="ir_log_amount">
			</div>
			<div class="ir-modal-input">
				<label for=""><?php esc_html_e( 'Note', 'wdm_instructor_role' ); ?></label>
				<textarea name="ir_log_note" id="ir_log_note" cols="22" rows="5"></textarea>
			</div>
		</div>
		<div class="ir-modal-actions-bottom">
			<button id="ir-modal-cancel"><?php esc_html_e( 'Cancel', 'wdm_instructor_role' ); ?></button>
			<button id="ir-modal-submit"><?php esc_html_e( 'Submit', 'wdm_instructor_role' ); ?></button>
		</div>
		<input type="hidden" name="ir_log_id" id="ir_log_id"/>
		<input type="hidden" name="ir_original_amount" id="ir_original_amount" value="0">
		<?php wp_nonce_field( 'ir_update_commission_log', 'ir_update_nonce' ); ?>
		<div class="ir-modal-loader-screen" style="display: none;">
			<span class="dashicons dashicons-update"></span>
			<span class="ir-loader-text"><?php esc_html_e( 'Updating . . .', 'wdm_instructor_role' ); ?></span>
		</div>
	</div>
</div>
