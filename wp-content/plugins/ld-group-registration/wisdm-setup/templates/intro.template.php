<?php
/**
 * Setup Wizard: Intro View Template
 *
 * @since 4.2.0
 * @version 4.3.15
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;
?>
<h1>
	<?php esc_html_e( 'Welcome to the Group Registration Setup!', 'wdm_ld_group' ); ?>
</h1>
<p><?php esc_html_e( 'Thank you for trusting LearnDash! This quick setup wizard will help you configure the basic settings.', 'wdm_ld_group' ); ?> <strong><?php esc_html_e( 'It’s completely optional and shouldn’t take longer than three minutes.', 'wdm_ld_group' ); ?></strong></p>
<p><?php esc_html_e( 'No time right now? If you don’t want to go through the wizard, you can skip and return to the WordPress dashboard. Come back anytime if you change your mind!', 'wdm_ld_group' ); ?></p>
<p class="wc-setup-actions step">
	<a href="<?php echo esc_url( $wizard_handler->get_next_step_link() ); ?>" class="button-primary button button-large button-next"><?php esc_html_e( "Let's Go!", 'wdm_ld_group' ); ?></a>
	<a href="<?php echo esc_url( admin_url( 'index.php' ) ); ?>" class="button button-large"><?php esc_html_e( 'Not right now', 'wdm_ld_group' ); ?></a>
</p>
