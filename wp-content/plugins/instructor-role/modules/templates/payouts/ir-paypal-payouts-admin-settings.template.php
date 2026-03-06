<?php
/**
 * Paypal Payouts Admin Settings Template
 *
 * @since 3.4
 *
 * @var string $payout_test_environment             Whether Paypal Payout is in sandbox mode or not.
 * @var string $payout_client_id                    Paypal Payout Client ID
 * @var string $payout_client_secret_key            Paypal Payout Client Secret Key
 * @var string $payout_sandbox_client_id            Paypal Payout Sandbox Client ID
 * @var string $payout_sandbox_client_secret_key    Paypal Payout Sandbox Client Secret Key
 * @var array  $payout_currencies                   List of supported Paypal Payout currencies
 * @var string $payout_currency                     Selected payout currency
 * @var string $payout_pay_note                     Note to be added in the payout
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<div class="ir-instructor-settings-tab-content">
	<div class="ir-flex justify-apart align-center">
		<div class="ir-heading-wrap">
			<div class="ir-tab-heading"><?php echo __( 'Payment', 'wdm_instructor_role' ); ?></div> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
		</div>
	</div>
	<div class="ir-heading-desc"></div>
	<form class="ir-payments" method="post" action="">
		<div class="ir-flex justify-apart align-center">
			<div class="ir-heading-wrap">
				<div class="ir-tab-subheading"><?php echo __( 'PayPal', 'wdm_instructor_role' ); ?></div> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
			</div>
		</div>
		<div class="ir-subheading-desc"></div>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<?php esc_html_e( 'Test/Sandbox Mode', 'wdm_instructor_role' ); ?>
					</th>
					<td>
						<input type="checkbox" name="ir_payout_test_environment" id="ir_payout_test_environment" <?php checked( $payout_test_environment, 'on' ); ?>>
						<p class="ir-tooltip">
							<?php esc_html_e( 'Check this box if you would like to make test transactions using Paypal Payouts.', 'wdm_instructor_role' ); ?>
						</p>
					</td>
				</tr>
				<!-- Live Credentials -->
				<tr class="ir-live-payouts">
					<th scope="row">
						<?php esc_html_e( 'Client ID', 'wdm_instructor_role' ); ?>
					</th>
					<td>
						<input type="password" size="100" name="ir_payout_client_id" value="<?php echo $payout_client_id; ?>">
						<p class="ir-tooltip">
							<?php
							// translators: link.
							printf( esc_html__( 'Enter your PayPal Payout Application Client ID. Read more about creating Apps %s', 'wdm_instructor_role' ), '<a href="https://developer.paypal.com/developer/applications/create">here</a>' );
							?>
						</p>
					</td>
				</tr>
				<tr class="ir-live-payouts">
					<th scope="row">
						<?php esc_html_e( 'Client Secret', 'wdm_instructor_role' ); ?>
					</th>
					<td>
						<input type="password" size="100" name="ir_payout_client_secret_key" value="<?php echo $payout_client_secret_key; ?>" >
						<p class="ir-tooltip">
							<?php esc_html_e( 'Enter your PayPal Payout Application Client Secret.', 'wdm_instructor_role' ); ?>
						</p>
					</td>
				</tr>

				<!-- Sandbox Credentials -->
				<tr class="ir-sandbox-payouts">
					<th scope="row">
						<?php esc_html_e( 'Sandbox Client ID', 'wdm_instructor_role' ); ?>
					</th>
					<td>
						<input type="text" size="100" name="ir_payout_sandbox_client_id" value="<?php echo $payout_sandbox_client_id; ?>">
						<p class="ir-tooltip">
							<?php esc_html_e( 'Enter your PayPal Payout Application Sandbox Client ID.', 'wdm_instructor_role' ); ?>
						</p>
					</td>
				</tr>
				<tr class="ir-sandbox-payouts">
					<th scope="row">
						<?php esc_html_e( 'Sandbox Client Secret', 'wdm_instructor_role' ); ?>
					</th>
					<td>
						<input type="text" size="100" name="ir_payout_sandbox_client_secret_key" value="<?php echo $payout_sandbox_client_secret_key; ?>" >
						<p class="ir-tooltip">
							<?php esc_html_e( 'Enter your PayPal Payout Application Sandbox Client Secret.', 'wdm_instructor_role' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<?php esc_html_e( 'Currency', 'wdm_instructor_role' ); ?>
					</th>
					<td>
						<select name="ir_payout_currency" id="ir_payout_currency">
							<option value="-1"><?php esc_html_e( 'Select a currency', 'wdm_instructor_role' ); ?></option>
							<?php foreach ( $payout_currencies as $code => $currency ) : ?>
								<option value="<?php esc_html_e( $code ); ?>" <?php selected( $payout_currency, $code ); ?>>
									<?php esc_html_e( $currency ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<p class="ir-tooltip">
							<?php esc_html_e( 'Select the currency you wish to make the payouts in.', 'wdm_instructor_role' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Payout Note', 'wdm_instructor_role' ); ?></th>
					<td>
						<textarea name="ir_payout_pay_note" cols="40" rows="3"><?php echo $payout_pay_note; ?></textarea>
						<p class="ir-tooltip">
							<?php esc_html_e( 'Note to recipient (required for Venmo accounts, optional for PayPal accounts).', 'wdm_instructor_role' ); // cspell:disable-line . ?>
						</p>
					</td>
				</tr>
				<tr>
					<td colspan="2">
					<p style="text-align:<?php echo ( is_rtl() ) ? 'left' : 'right'; ?>">
						<input type="submit" class="button ir-primary-btn" name="ir_payout_settings_save" value="<?php esc_html_e( 'Save', 'wdm_instructor_role' ); ?>">
						<?php wp_nonce_field( 'ir_paypal_payout_admin_settings_nonce', 'ir_nonce' ); ?>
					</p>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
