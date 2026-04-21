<?php
/**
 * Template for the Dashboard when no Gradebooks have been created
 *
 * @since 4.2.0
 * @version 4.3.2
 */

defined( 'ABSPATH' ) || die();

?>

<div class="ld-gb-frontend-gradebook-no-gradebooks">

	<p>
		<?php _e( "You don't have any Gradebooks created yet.", 'learndash-gradebook' ); ?>
	</p>

	<?php if ( current_user_can( 'edit_gradebooks' ) ) : ?>

		<p>
			<a href="<?php echo admin_url( 'post-new.php?post_type=gradebook' ); ?>"
			class="button primary">
				<?php _e( 'Create a Gradebook', 'learndash-gradebook' ); ?>
			</a>
		</p>

		<p>
		<?php _e( 'Once you create one or more Gradebooks, you can view the student report for each one here.', 'learndash-gradebook' ); ?>
		</p>

	<?php else : ?>

		<p>
			<?php _e( 'Please contact your site Administrator', 'learndash-gradebook' ); ?>
		</p>

	<?php endif; ?>

</div>
