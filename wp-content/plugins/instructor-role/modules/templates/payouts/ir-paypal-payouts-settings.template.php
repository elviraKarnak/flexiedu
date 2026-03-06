<?php
/**
 * Paypal Payouts Settings Template
 *
 * @since 3.4
 *
 * @var string  $payout_email   Email of the user to send payouts.
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<h3>
	<?php esc_html_e( 'Instructor Paypal E-mail', 'wdm_instructor_role' ); ?>
</h3>

<table class="ir-paypal-payouts-settings-table">
	<tr>
		<th>
			<label for="ir_paypal_payouts_email">
				<?php esc_html_e( 'Paypal Payouts E-mail', 'wdm_instructor_role' ); ?>
			</label>
		</th>
		<td>
			<input type="text" name="ir_paypal_payouts_email" id="ir_paypal_payouts_email" value="<?php echo esc_attr( $payout_email ); ?>">
			<?php wp_nonce_field( 'ir_paypal_payout_settings_nonce', 'ir_payouts_nonce' ); ?>
		</td>
	</tr>
</table>
