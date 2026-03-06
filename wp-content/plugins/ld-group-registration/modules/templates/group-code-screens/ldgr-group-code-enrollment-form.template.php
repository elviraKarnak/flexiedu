<?php
/**
 * Group code user enrollment form
 *
 * @since 4.1.0
 * @version 4.3.15
 *
 * @var string  $enable_recaptcha
 * @var string  $ldgr_recaptcha_site_key
 * @var string  $ldgr_enable_gdpr
 * @var string  $ldgr_gdpr_checkbox_message
 *
 * @package LearnDash\Seats_Plus
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<?php if ( 'on' == $enable_recaptcha ) : ?>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>
<div class="ldgr-group-code-enrollment-form-container">

	<?php do_action( 'ldgr_action_before_group_code_enrollment_form' ); ?>

	<div class="ldgr-group-code-messages">
		<span class="ldgr-message-text"></span>
	</div>

	<form id="ldgr-group-code-enrollment-form" class="ldgr-form" method="post">

		<div class="ldgr-form-field">
			<label for="ldgr-user-group-code">
				<?php esc_html_e( 'Group Code', 'wdm_ld_group' ); ?>
			</label>
			<?php
			$ldgr_gr_code = '';
			if ( null !== isset( $_GET['ldgr_gr_code'] ) ) {
				$ldgr_gr_code = wp_unslash( $_GET['ldgr_gr_code'] );
			}
			?>
			<input type="text" name="ldgr-user-group-code" id="ldgr-user-group-code" autocomplete="off" value='<?php echo esc_attr( $ldgr_gr_code ); ?>' required />
		</div>

		<?php wp_nonce_field( 'ldgr-group-code-enrollment-form', 'ldgr_nonce' ); ?>

		<?php if ( 'on' == $enable_recaptcha ) : ?>
			<div class="g-recaptcha ldgr-form-field" data-sitekey="<?php echo esc_attr( $ldgr_recaptcha_site_key ); ?>"></div>
		<?php endif; ?>

		<?php if ( 'on' === $ldgr_enable_gdpr ) : ?>
			<p>
				<label for="ldgr-user-gdpr-check">
					<input type="checkbox" name="ldgr-user-gdpr-check" id="ldgr-user-gdpr-check" required />
					<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo stripslashes( $ldgr_gdpr_checkbox_message );
					?>
				</label>
			</p>
		<?php endif; ?>

		<button type="submit" id="ldgr-user-enroll-form-submit"><?php esc_html_e( 'Submit', 'wdm_ld_group' ); ?></button>

	</form>


	<div class="ldgr-black-screen" style="display:none">
		<span style="margin-bottom:10px;"><?php esc_html_e( 'Please wait...', 'wdm_ld_group' ); ?></span>
		<span class="dashicons dashicons-update spin"></span>
	</div>

	<?php do_action( 'ldgr_action_after_group_code_enrollment_form' ); ?>

</div>
