<?php
/**
 * Template: LDGR Single Product Unlimited Members Template.
 *
 * @since 4.1.0
 * @version 4.3.15
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="ldgr-unlimited-member-options" <?php echo ( 'individual' == $default_option ) ? 'style=display:none' : ''; ?>>
	<input type="checkbox" name="ldgr_unlimited_member_check" id="ldgr-unlimited-member-check" value="yes"/>
	<input type="hidden" name="ldgr_unlimited_member_price" value="<?php echo esc_attr( $unlimited_price ); ?>">
	<label for="ldgr-unlimited-member-check"><?php echo esc_attr( $unlimited_label ); ?> : </label>
	<?php echo wc_price( $unlimited_price ); ?>
</div>
