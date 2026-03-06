<?php
/**
 * Paypal Payouts Transactions Details Template
 *
 * @since 3.4
 *
 * @var int     $status             Payout batch status code.
 * @var string  $time_created       Date and time the payout was created.
 * @var string  $transaction_status Payout item transaction status.
 * @var object  $errors             Errors in the payout, if any.
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<div class="ir-payout-transaction-details-container">
	<table>
		<tbody>
			<tr>
				<th>
					<?php esc_html_e( 'Amount', 'wdm_instructor_role' ); ?>
				</th>
				<td>
					<?php echo esc_attr( $amount['value'] ) . ' ' . esc_attr( $amount['currency'] ); ?>
				</td>
			</tr>
			<tr>
				<th>
					<?php esc_html_e( 'Status', 'wdm_instructor_role' ); ?>
				</th>
				<td>
					<?php echo esc_attr( $status ); ?>
				</td>
			</tr>
			<tr>
				<th>
					<?php esc_html_e( 'Time Created', 'wdm_instructor_role' ); ?>
				</th>
				<td>
					<?php echo esc_attr( $time_created ); ?>
				</td>
			</tr>
			<tr>
				<th>
					<?php esc_html_e( 'Transaction Status', 'wdm_instructor_role' ); ?>
				</th>
				<td>
					<?php echo esc_attr( $transaction_status ); ?>
				</td>
			</tr>
			<?php if ( ! empty( $errors ) ) : ?>
				<tr>
					<th>
						<?php esc_html_e( 'Error', 'wdm_instructor_role' ); ?>
					</th>
					<td>
						<?php echo esc_attr( $errors->name ); ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php esc_html_e( 'Description', 'wdm_instructor_role' ); ?>
					</th>
					<td>
						<?php echo esc_attr( $errors->message ); ?>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>
