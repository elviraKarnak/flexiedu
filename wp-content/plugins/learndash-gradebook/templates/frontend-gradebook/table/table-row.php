<?php
/**
 * Template for the Gradebook Table Row Contents
 *
 * @since 2.0.0
 * @updated 4.2.0
 * @version 4.3.2
 *
 * @var integer         $user_id
 * @var array           $user_columns
 * @var array           $user_grade
 * @var string          $grade_format
 * @var array           $components
 * @var integer         $gradebook_id
 * @var integer         $group_id
 */

defined( 'ABSPATH' ) || die();

$primary_column = 'display_name';

foreach ( $user_columns as $key => $name ) {
	$primary_column = $key;
	break;
}

?>

<td class="id" style="display: none;">
	<?php echo $user_id; ?>
</td>
<?php foreach ( $user_columns as $key => $name ) : ?>

	<td class="<?php echo $key; ?>">

		<?php if ( $key == $primary_column ) : ?>

			<a href="#user_view_grades" class="open-edit-panel" data-user_id="<?php echo esc_attr( $user_id ); ?>" data-gradebook_id="<?php echo esc_attr( $gradebook_id ); ?>" data-group_id="<?php echo esc_attr( $group_id ); ?>" data-grade_format="<?php echo esc_attr( $grade_format ); ?>">

		<?php endif; ?>

				<?php echo ( isset( $user_grade[ $key ] ) && $user_grade[ $key ] ) ? $user_grade[ $key ] : '&nbsp;'; ?>

		<?php if ( $key == $primary_column ) : ?>

			</a>

			<div class="hover-link">
				<a href="#user_view_grades" class="open-edit-panel" data-user_id="<?php echo esc_attr( $user_id ); ?>" data-gradebook_id="<?php echo esc_attr( $gradebook_id ); ?>" data-group_id="<?php echo esc_attr( $group_id ); ?>" data-grade_format="<?php echo esc_attr( $grade_format ); ?>">
					<?php _ex( 'View/Edit User Grades', 'Frontend Gradebook: View/Edit User Grades link text', 'learndash-gradebook' ); ?>
				</a>
			</div>

		<?php endif; ?>

	</td>

<?php endforeach; ?>

<?php do_action( 'ld_gb_frontend_gradebook_before_overall_grade_row', $user_id, $user_grade, $grade_format, $components, $gradebook_id, $group_id ); ?>

<td class="grade">
	<?php echo ( isset( $user_grade['grade'] ) ) ? learndash_gradebook_get_grade_display( $user_grade['grade'], $grade_format ) : ''; ?>
</td>

<?php do_action( 'ld_gb_frontend_gradebook_after_overall_grade_row', $user_id, $user_grade, $grade_format, $components, $gradebook_id, $group_id ); ?>

<?php
foreach ( $components as $component ) :

	$component_id = "component_{$component['id']}";

	?>

	<td class="<?php echo $component_id; ?>">
		<?php echo ( isset( $user_grade[ $component_id ] ) ) ? learndash_gradebook_get_grade_display( $user_grade[ $component_id ], $grade_format ) : ''; ?>
	</td>

<?php endforeach; ?>
