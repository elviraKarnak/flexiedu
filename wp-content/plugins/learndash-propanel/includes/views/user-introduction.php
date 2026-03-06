<?php
/**
 * Template is used to show user introduction on the dashboard.
 *
 * @package LearnDash\Reports
 *
 * @since 3.0.0
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="user-info-section">
	<?php
	$display_name = get_userdata( $user_id )->display_name;
	?>
	<div class="thumbnail"><?php echo get_avatar( $user_id ); ?></div>
	<div class="information">
		<div class="label">
			<span><?php echo esc_html__( 'Student Name', 'learndash-reports-pro' ); ?></span>
		</div>
		<div class="name">
			<span><?php echo esc_html( $display_name ); ?></span>
		</div>
	</div>
</div>
