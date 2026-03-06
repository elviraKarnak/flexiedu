<?php
/**
 * Commission Payment Popup Template supporting Paypal Payouts
 *
 * @since 3.4
 *
 * @var mixed   $instructor_id        User ID of the Instructor
 *
 * @since 3.4.0
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<!-- popup div starts -->
<div id="popUpDiv" style="display: none; top: 627px; left: 17%;">
	<div style="clear:both"></div>
	<table class="widefat" id="wdm_tbl_staff_mail"> <?php // cspell:disable-line . ?>
		<thead>
			<tr>
				<th colspan="2">
					<strong><?php esc_html_e( 'Transaction', 'wdm_instructor_role' ); ?></strong>
					<p id="wdm_close_pop" colspan="1" onclick="popup( 'popUpDiv' )"><span class="dashicons dashicons-no"></span></p>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<strong><?php esc_html_e( 'Paid Earnings', 'wdm_instructor_role' ); ?></strong>
				</td>
				<td>
					<input type="number" id="wdm_total_amount_paid_price" value="" readonly="readonly">
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php esc_html_e( 'Unpaid Earnings', 'wdm_instructor_role' ); ?></strong>
				</td>
				<td>
					<input type="number" id="wdm_amount_paid_price" value="" readonly="readonly">
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php esc_html_e( 'Enter amount', 'wdm_instructor_role' ); ?></strong>
				</td>
				<td>
					<input type="number" id="wdm_pay_amount_price" value="" >
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php esc_html_e( 'Payout Method', 'wdm_instructor_role' ); ?></strong>
				</td>
				<td>
					<label for="ir-commission-manual-payment-method">
						<input type="radio" name="ir-commission-payment-method" class="ir-commission-payment-method" id="ir-commission-manual-payment-method" value="manual" checked="checked" />
						<?php esc_html_e( 'Manual', 'wdm_instructor_role' ); ?>
					</label>
					<label for="ir-commission-payout-payment-method">
						<input type="radio" name="ir-commission-payment-method" class="ir-commission-payment-method" id="ir-commission-payout-payment-method" value="paypal-payout" />
						<?php esc_html_e( 'Paypal Payout', 'wdm_instructor_role' ); ?>
					</label>
				</td>
			</tr>
			<tr class="ir-payout-email" style="display: none;">
				<td>
					<strong><?php esc_html_e( 'Payout Email Address', 'wdm_instructor_role' ); ?></strong>
				</td>
				<td>
					<input type="text" value="<?php echo esc_attr( get_user_meta( $instructor_id, 'ir_paypal_payouts_email', true ) ); ?>" readonly />
				</td>
			</tr>
			<tr class="ir-commission-notes">
				<td>
					<strong><?php esc_html_e( 'Note', 'wdm_instructor_role' ); ?></strong>
				</td>
				<td>
					<textarea name="ir_payout_note" id="ir_payout_note" cols="30" rows="5"></textarea>
				</td>
			</tr>
			<?php do_action( 'wdm_commission_report_popup_table', $instructor_id ); ?>
			<tr>
				<td colspan="2">
					<input type="hidden" id="instructor_id" value="<?php echo esc_attr( $instructor_id ); ?>" />
					<input class="button-primary" type="button" value="<?php esc_html_e( 'Pay', 'wdm_instructor_role' ); ?>" id="ir_pay_click" />
					<?php wp_nonce_field( 'ir_commission_paypal_payout_payment', 'ir_nonce' ); ?>
					<img src="<?php echo plugins_url( '/modules/media/ajax-loader.gif', INSTRUCTOR_ROLE_BASE ); ?>" style="display: none" class="wdm_ajax_loader">
				</td>
			</tr>
		</tbody>
	</table>
</div>
<!-- popup div ends -->
