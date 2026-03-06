<?php
/**
 * Group code user registration form
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
 *
 * cspell:ignore cabgc
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<?php if ( 'on' == $enable_recaptcha ) : ?>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>

<div class="ldgr-group-code-registration-form-container ldgr-cabgc">

	<?php do_action( 'ldgr_action_before_group_code_registration_form' ); ?>

	<div class="ldgr-group-code-messages">
		<span class="ldgr-message-text"></span>
	</div>

	<form id="ldgr-group-code-registration-form" class="ldgr-form" method="post">

		<div class="ldgr-field ldgr-d-inline-flex ldgr-mr-40">
			<label for="ldgr-user-first-name">
				<?php esc_html_e( 'First Name', 'wdm_ld_group' ); ?>
			</label>
			<input type="text" name="ldgr-user-first-name" id="ldgr-user-first-name" class="ldgr-textbox ldgr-w-300" required />
		</div>

		<div class="ldgr-field ldgr-d-inline-flex ldgr-mr-40">
			<label for="ldgr-user-last-name">
				<?php esc_html_e( 'Last Name', 'wdm_ld_group' ); ?>
			</label>
			<input type="text" name="ldgr-user-last-name" id="ldgr-user-last-name" class="ldgr-textbox ldgr-w-300" required />
		</div>

		<div class="ldgr-field ldgr-d-inline-flex ldgr-mr-40">
			<label for="ldgr-user-username">
				<?php esc_html_e( 'Username', 'wdm_ld_group' ); ?>
			</label>
			<input type="text" name="ldgr-user-username" id="ldgr-user-username" class="ldgr-textbox ldgr-w-300" required />
		</div>

		<div class="ldgr-field ldgr-d-inline-flex ldgr-mr-40">
			<label for="ldgr-user-email">
				<?php esc_html_e( 'User Email', 'wdm_ld_group' ); ?>
			</label>
			<input type="email" name="ldgr-user-email" id="ldgr-user-email" class="ldgr-textbox ldgr-w-300" required />
		</div>

		<div class="ldgr-field ldgr-d-inline-flex ldgr-mr-40">
			<label for="ldgr-user-password">
				<?php esc_html_e( 'User Password', 'wdm_ld_group' ); ?>
			</label>
			<input type="password" name="ldgr-user-password" id="ldgr-user-password" class="ldgr-textbox ldgr-w-300" required />
		</div>

		<div class="ldgr-field ldgr-d-inline-flex ldgr-mr-40">
			<label for="ldgr-user-confirm-password">
				<?php esc_html_e( 'Confirm Password', 'wdm_ld_group' ); ?>
			</label>
			<input type="password" name="ldgr-user-confirm-password" id="ldgr-user-confirm-password" class="ldgr-textbox ldgr-w-300" required />
		</div>

		<?php
			/**
			 * Allow 3rd party addons to add custom fields before group code field.
			 *
			 * @since 4.1.2
			 */
			do_action( 'ldgr_action_registration_form_before_group_code_field' );
		?>

		<div class="ldgr-field ldgr-d-inline-flex ldgr-mr-40">
			<label for="ldgr-user-group-code">
				<?php esc_html_e( 'Group Code', 'wdm_ld_group' ); ?>
			</label>
			<?php
			$ldgr_gr_code = '';
			if ( null != isset( $_GET['ldgr_gr_code'] ) ) {
				$ldgr_gr_code = wp_unslash( $_GET['ldgr_gr_code'] );
			}
			?>
			<input type="text" name="ldgr-user-group-code" id="ldgr-user-group-code" class="ldgr-textbox ldgr-w-300" value='<?php echo esc_attr( $ldgr_gr_code ); ?>' autocomplete="off" required />
		</div>

		<?php wp_nonce_field( 'ldgr-group-code-registration-form', 'ldgr_nonce' ); ?>

		<?php if ( 'on' == $enable_recaptcha ) : ?>
			<div class="g-recaptcha ldgr-form-field" data-sitekey="<?php echo esc_attr( $ldgr_recaptcha_site_key ); ?>"></div>
		<?php endif; ?>

		<?php if ( 'on' === $ldgr_enable_gdpr ) : ?>
			<p>
				<label for="ldgr-user-gdpr-check">
					<input type="checkbox" name="ldgr-user-gdpr-check" id="ldgr-user-gdpr-check" required />
					<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $ldgr_gdpr_checkbox_message;
					?>
				</label>
			</p>
		<?php endif; ?>

		<div class="ldgr-eg-actions">
			<span class="ldgr-btn"><?php esc_html_e( 'Cancel', 'wdm_ld_group' ); ?></span>
			<span id="ldgr-user-reg-form-submit" class="ldgr-btn ldgr-bg-color solid"><?php esc_html_e( 'Submit', 'wdm_ld_group' ); ?></span>
		</div>

	</form>
	<div class="ldgr-black-screen" style="display:none">
		<span style="margin-bottom:10px;"><?php esc_html_e( 'Please wait...', 'wdm_ld_group' ); ?></span>
		<span class="dashicons dashicons-update spin"></span>
	</div>
	<?php do_action( 'ldgr_action_after_group_code_registration_form' ); ?>

</div>
