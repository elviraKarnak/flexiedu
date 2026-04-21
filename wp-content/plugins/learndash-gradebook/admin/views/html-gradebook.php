<?php
/**
 * HTMl for the Gradebook reports page.
 *
 * @since 1.0.0
 * @updated 4.3.0
 *
 * @var LD_GB_GradebookListTable $gradebook The table object for the Gradebook report.
 * @var string|bool $hide_rows
 * @var array $group_options
 * @var string $active_gradebook
 * @var string $active_group_ID
 */

defined( 'ABSPATH' ) || die();
?>

<form method="get">

	<input type="hidden" name="page" value="learndash-gradebook"/>

	<?php if ( ! LD_GB_QuickStart::inside_quickstart() ) : ?>

		<?php $gradebook->search_box( _x( 'Search Users', 'Gradebook Search Box label', 'learndash-gradebook' ), 'gradebook-users' ); ?>

		<?php $gradebook->gradebook_select( $active_gradebook ); ?>


		<?php if ( ! empty( $group_IDs ) ) : ?>

			<?php $gradebook->group_select( $group_IDs, $active_group_ID ); ?>

		<?php endif; ?>

	<?php endif; ?>
	
</form>

<div class="ld-gb-gradebook-container">
	<?php $gradebook->display(); ?>
</div>

<?php do_action( 'ld_gb_admin_gradebook_before_export_buttons', $active_gradebook, ( $active_group_ID ) ? $active_group_ID : 0 ); ?>

<form method="get" id="export-gradebook-components">

	<?php do_action( 'ld_gb_admin_gradebook_before_export_components_button', $active_gradebook, ( $active_group_ID ) ? $active_group_ID : 0 ); ?>

	<input type="submit" class="button-primary" value="<?php _e( 'Export Gradebook Component Grades', 'learndash-gradebook' ); ?>" data-gradebook_id="<?php echo $active_gradebook; ?>" data-group_id="<?php echo ( $active_group_ID ) ? $active_group_ID : '0'; ?>" data-submitting_text="<?php _e( 'Generating .CSV...', 'learndash-gradebook' ); ?>" data-per_page="<?php echo esc_attr( apply_filters( 'ld_gb_backend_gradebook_export_per_page', 30, $active_gradebook, ( $active_group_ID ) ? $active_group_ID : 0 ) ); ?>" />

	<?php do_action( 'ld_gb_admin_gradebook_after_export_components_button', $active_gradebook, ( $active_group_ID ) ? $active_group_ID : 0 ); ?>

</form>

<form method="get" id="export-gradebook-all-grades">

	<?php do_action( 'ld_gb_admin_gradebook_before_export_all_grades_button', $active_gradebook, ( $active_group_ID ) ? $active_group_ID : 0 ); ?>

	<input type="submit" class="button-primary" value="<?php _e( 'Export All Gradebook Grades', 'learndash-gradebook' ); ?>" data-gradebook_id="<?php echo $active_gradebook; ?>" data-group_id="<?php echo ( $active_group_ID ) ? $active_group_ID : '0'; ?>" data-submitting_text="<?php _e( 'Generating .CSV...', 'learndash-gradebook' ); ?>" data-per_page="<?php echo esc_attr( apply_filters( 'ld_gb_backend_gradebook_export_per_page', 30, $active_gradebook, ( $active_group_ID ) ? $active_group_ID : 0 ) ); ?>" />

	<?php do_action( 'ld_gb_admin_gradebook_after_export_all_grades_button', $active_gradebook, ( $active_group_ID ) ? $active_group_ID : 0 ); ?>

</form>

<?php do_action( 'ld_gb_admin_gradebook_after_export_buttons', $active_gradebook, ( $active_group_ID ) ? $active_group_ID : 0 ); ?>