<?php
/**
 * Paypal Payouts Transactions Template
 *
 * @since 3.4
 *
 * @var array  $payout_transactions   List of all payout transactions for the instructor.
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<!-- <h3><?php esc_html_e( 'Paypal Payouts Transaction History', 'wdm_instructor_role' ); ?></h3> -->
<div class="ir-payout-transactions-container">
	<table class="ir-payout-transaction-table footable">
		<thead>
			<tr>
				<th>
					<?php esc_html_e( 'Batch ID', 'wdm_instructor_role' ); ?>
				</th>
				<th>
					<?php esc_html_e( 'Status', 'wdm_instructor_role' ); ?>
				</th>
				<th>
					<?php esc_html_e( 'Amount', 'wdm_instructor_role' ); ?>
				</th>
				<th>
					<?php esc_html_e( 'Type', 'wdm_instructor_role' ); ?>
				</th>
				<th data-hide="all"></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $payout_transactions ) ) : ?>
				<tr id="ir-no-payouts">
					<td colspan="5" class="ir-no-data-found">
						<?php esc_html_e( 'No payouts recorded yet', 'wdm_instructor_role' ); ?>
					</td>
				</tr>
			<?php else : ?>
				<?php foreach ( $payout_transactions as $payout ) : ?>
					<tr class="ir-payout-row">
						<td class="ir-payout-batch-id" data-batch-id="<?php echo esc_attr( $payout['batch_id'] ); ?>">
							<?php echo esc_attr( $payout['batch_id'] ); ?>
						</td>
						<td class="ir-payout-status">
							<?php echo esc_attr( $payout['status'] ); ?>
						</td>
						<td class="ir-payout-amount">
							<?php echo esc_attr( $payout['amount'] ); ?>
						</td>
						<td class="ir-payout-type">
							<?php echo esc_attr( $payout['type'] ); ?>
						</td>
						<td class="ir-payout-details">
							<div class="ir-black-screen ir-black-screen-<?php echo esc_attr( $payout['batch_id'] ); ?>">
								<span class="dashicons dashicons-update spin"></span>
							</div>
							<div class="ir-payout-transactions-details-<?php echo esc_attr( $payout['batch_id'] ); ?>">
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
	<?php wp_nonce_field( 'ir_fetch_payout_transaction_details', 'ir_get_payout_nonce' ); ?>
</div>
