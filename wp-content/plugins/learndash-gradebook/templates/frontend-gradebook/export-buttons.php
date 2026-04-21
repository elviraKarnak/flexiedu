<?php
/**
 * Template for the Dashboard
 *
 * @since 2.0.0
 * @updated 4.2.0
 * @version 4.3.2
 *
 * @var integer         $gradebook_id
 * @var integer         $group_id
 */

defined( 'ABSPATH' ) || die();

?>

<div class="grid-container full">
	<div class="grid-x grid-margin-x">

		<?php do_action( 'ld_gb_frontend_gradebook_before_export_buttons', $gradebook_id, ( $group_id ) ? $group_id : 0 ); ?>

		<div class="ld-gb-frontend-gradebook-export-gradebook-components-container medium-auto cell">

			<form method="get" class="export-gradebook-components">

				<?php do_action( 'ld_gb_frontend_gradebook_before_export_components_button', $gradebook_id, ( $group_id ) ? $group_id : 0 ); ?>

				<input type="submit" class="button primary" value="<?php _e( 'Export Gradebook Component Grades', 'learndash-gradebook' ); ?>" data-gradebook_id="<?php echo $gradebook_id; ?>" data-group_id="<?php echo ( $group_id ) ? $group_id : '0'; ?>" data-submitting_text="<?php _e( 'Generating .CSV...', 'learndash-gradebook' ); ?>" />

				<?php do_action( 'ld_gb_frontend_gradebook_after_export_components_button', $gradebook_id, ( $group_id ) ? $group_id : 0 ); ?>

			</form>

		</div>

		<div class="ld-gb-frontend-gradebook-export-gradebook-all-grades-container medium-auto cell">

			<form method="get" class="export-gradebook-all-grades">

				<?php do_action( 'ld_gb_frontend_gradebook_before_export_all_grades_button', $gradebook_id, ( $group_id ) ? $group_id : 0 ); ?>

				<input type="submit" class="button primary" value="<?php _e( 'Export All Gradebook Grades', 'learndash-gradebook' ); ?>" data-gradebook_id="<?php echo $gradebook_id; ?>" data-group_id="<?php echo ( $group_id ) ? $group_id : '0'; ?>" data-submitting_text="<?php _e( 'Generating .CSV...', 'learndash-gradebook' ); ?>" />

				<?php do_action( 'ld_gb_frontend_gradebook_after_export_all_grades_button', $gradebook_id, ( $group_id ) ? $group_id : 0 ); ?>

			</form>

		</div>

		<?php do_action( 'ld_gb_frontend_gradebook_after_export_buttons', $gradebook_id, ( $group_id ) ? $group_id : 0 ); ?>

	</div>
</div>
