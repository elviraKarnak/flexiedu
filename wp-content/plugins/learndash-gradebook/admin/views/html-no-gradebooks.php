<?php
/**
 * HTMl for showing no Gradebooks.
 *
 * @since 1.2.0
 */

defined( 'ABSPATH' ) || die();
?>

<?php settings_errors(); ?>

<div class="ld-gb-no-gradebooks-page">
	<p class="ld-gb-no-gradebooks-page-header">
		<?php _e( "You don't have any Gradebooks created yet.", 'learndash-gradebook' ); ?>
	</p>

	<?php if ( current_user_can( 'edit_gradebooks' ) ) : ?>

		<p class="ld-gb-no-gradebooks-page-action">
			<a href="<?php echo admin_url( 'post-new.php?post_type=gradebook' ); ?>"
			class="button ld-gb-button ld-gb-button-marketing">
		<?php _e( 'Create a Gradebook', 'learndash-gradebook' ); ?>
			</a>
		</p>

		<p class="ld-gb-no-gradebooks-page-sub">
		<?php _e( 'Once you create one or more Gradebooks, you can view the student report for each one here.', 'learndash-gradebook' ); ?>
		</p>

	<?php else : ?>

		<p class="ld-gb-no-gradebooks-page-action">
			<?php _e( 'Please contact your site Administrator', 'learndash-gradebook' ); ?>
		</p>

	<?php endif; ?>

</div>