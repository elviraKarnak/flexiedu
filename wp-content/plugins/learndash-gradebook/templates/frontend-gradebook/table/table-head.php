<?php
/**
 * Template for the Group Table Headings
 *
 * @since 2.0.0
 * @updated 4.2.0
 * @version 4.3.2
 *
 * @var array           $user_columns
 * @var array           $components
 * @var integer         $gradebook_id
 * @var integer         $group_id
 */

defined( 'ABSPATH' ) || die();
?>

<?php foreach ( $user_columns as $key => $name ) : ?>

	<th data-column_name="<?php echo esc_attr( $key ); ?>">
		<button class="sort" data-sort="<?php echo esc_attr( $key ); ?>">
			<span class="screen-reader-text">
				<?php _e( 'Sort by ', 'learndash-gradebook' ); ?>
			</span>
			<?php echo $name; ?>
		</a>
	</th>

<?php endforeach; ?>

<?php do_action( 'ld_gb_frontend_gradebook_before_overall_grade_header', $components, $gradebook_id, $group_id ); ?>

<th data-column_name="grade">
	<button class="sort" data-sort="grade">
		<span class="screen-reader-text">
			<?php _ex( 'Sort by ', 'Frontend Gradebook: Sort By Overall Grade Screen Reader label', 'learndash-gradebook' ); ?>
		</span>
		<?php esc_attr_e( 'Overall Grade', 'learndash-gradebook' ); ?>
	</a>
</th>

<?php do_action( 'ld_gb_frontend_gradebook_after_overall_grade_header', $components, $gradebook_id, $group_id ); ?>

<?php
foreach ( $components as $component ) :

	$component_id = "component_{$component['id']}";

	?>

	<th data-column_name="<?php echo esc_attr( $component_id ); ?>">
		<button class="sort" data-sort="<?php echo $component_id; ?>">
			<span class="screen-reader-text">
				<?php _ex( 'Sort by ', 'Frontend Gradebook: Sort By Component Screen Reader label', 'learndash-gradebook' ); ?>
			</span>
			<?php echo $component['name']; ?>
		</a>
	</th>

<?php endforeach; ?>
