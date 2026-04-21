<?php
/**
 * Template for error and success Notices
 *
 * @since 2.0.0
 * @version 4.3.2
 *
 * @var string          $message
 * @var string          $type
 */

defined( 'ABSPATH' ) || die();

?>

<div class="ld-gb-frontend-gradebook-notice callout <?php echo esc_attr( $type ); ?>" data-closable>

	<?php echo $message; ?>

	<button class="close-button" aria-label="<?php esc_attr_e( 'Close notice', 'learndash-gradebook' ); ?>" type="button" data-close>
		<span aria-hidden="true">&times;</span>
	</button>

</div>
