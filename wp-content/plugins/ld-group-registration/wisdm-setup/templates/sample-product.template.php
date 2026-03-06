<?php
/**
 * Setup Wizard: Sample Group Product View Template.
 *
 * @since 4.2.0
 * @version 4.3.15
 *
 * @var object  $setup_wizard         Setup Wizard class object.
 * @var string  $group_product_link   New sample group product link.
 * @var string  $group_settings_link  Group registration settings link.
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wisdm-setup-done">
	<?php echo $setup_wizard->get_checked_image_html(); ?>
	<h1><?php esc_html_e( 'All settings are done!', 'wdm_ld_group' ); ?></h1>
</div>

<div class="wisdm-setup-done-content">
	<p class="wc-setup-actions step">
		<a class="button button-primary" href="<?php echo esc_url( $group_product_link ); ?>"><?php esc_html_e( 'Setup Group Product', 'wdm_ld_group' ); ?></a>
		<a class="button" href="<?php echo esc_url( $group_settings_link ); ?>"><?php esc_html_e( 'More Settings', 'wdm_ld_group' ); ?></a>
	</p>
</div>
