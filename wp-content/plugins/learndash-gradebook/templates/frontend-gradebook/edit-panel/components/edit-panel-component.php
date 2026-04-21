<?php
/**
 * Template for the Edit Panel Components
 *
 * @since 2.0.0
 * @updated 4.2.0
 * @version 4.3.2
 *
 * @var integer         $user_id
 * @var integer         $gradebook_id
 * @var integer         $group_id
 * @var LD_GB_UserGrade $user_grade
 * @var array           $component
 * @var string          $grade_format
 */

defined( 'ABSPATH' ) || die();
?>

<div class="ld-gb-frontend-gradebook-component" data-component="<?php echo esc_attr( $component['id'] ); ?>">

	<?php do_action( 'ld_gb_frontend_gradebook_edit_panel_component_grade', $component, $grade_format, $user_id, $gradebook_id, $group_id ); ?>

	<table>
		<thead>
			<tr>
				<th colspan="3">
					<?php _ex( 'Name', 'Frontend Gradebook: Component Grade Name label', 'learndash-gradebook' ); ?>
				</th>
				<th>
					<?php _ex( 'Score', 'Frontend Gradebook: Component Grade Score label', 'learndash-gradebook' ); ?>
				</th>
			</tr>
		</thead>
		<tbody>

			<?php if ( ! empty( $component['grades'] ) ) : ?>

				<?php foreach ( $component['grades'] as $grade ) : ?>

					<?php do_action( 'ld_gb_frontend_gradebook_edit_panel_grade_row', $grade, $user_grade, $component, $user_id, $gradebook_id, $group_id, $grade_format ); ?>

				<?php endforeach; ?>

			<?php else : ?>

				<?php do_action( 'ld_gb_frontend_gradebook_edit_panel_no_grades', $user_grade, $component, $user_id, $gradebook_id ); ?>

			<?php endif; ?>

		</tbody>
	</table>

	<?php

	if ( ! ld_gb_get_option_field( 'disable_manual_grades', false ) ) :

		do_action( 'ld_gb_frontend_gradebook_edit_panel_grade_add', $user_id, $gradebook_id, $group_id, $component, $user_grade, $grade_format );

	endif;

	do_action( 'ld_gb_frontend_gradebook_edit_panel_grade_edit', $user_id, $gradebook_id, $group_id, $component, $grade_format );
	?>

</div>
