<?php
/**
 * Group Image Template.
 *
 * @since 4.2.0
 * @version 4.3.15
 *
 * @var string $src         Group Image URL.
 * @var int    $width       Image width in pixels.
 * @var int    $group_id    ID of the Group.
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="ldgr-group-image">
	<img style="width: <?php echo esc_attr( $width ); ?>px;" src="<?php echo esc_attr( $src ); ?>"/>
</div>
