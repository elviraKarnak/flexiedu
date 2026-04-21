<?php
/**
 * Template for the report card.
 *
 * @since 1.0.0
 * @updated 3.0.0
 * @version 4.3.2
 *
 * @var LD_GB_UserGrade $user_grade
 * @var integer         $gradebook_id
 * @var string          $grade_format
 */

defined( 'ABSPATH' ) || die();

// Set the wrapper class using the block attributes if it's a block
if ( $is_block ) {
	$class = 'ld-gb-report-card-container wp-block-report-card-block';

	$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => $class ] );
} else {
	$wrapper_attributes = 'class="ld-gb-report-card-container"';
}

?>

<div id="ld-gb-report-card-<?php echo $gradebook_id; ?>" <?php echo $wrapper_attributes; ?>>

	<?php
	/**
	 * Expand/Collapse HTML.
	 *
	 * @since 1.1.0
	 * @updated 1.6.0
 * @version 4.3.2
	 *
	 * @hooked LD_GB_SC_ReportCard::template_expand_collapse() 10
	 */
	do_action( 'report-card-expand-collapse', $user_grade, $gradebook_id );
	?>

	<div class="ld-gb-report-card">
		<?php
		/**
		 * Outputs the title.
		 *
		 * @since 1.1.0
		 * @updated 1.6.0
 * @version 4.3.2
		 *
		 * @hooked LD_GB_SC_ReportCard::template_title() 10
		 */
		do_action( 'report-card-title', $user_grade, $gradebook_id );
		?>

		<?php if ( $user_grade->get_components() ) : ?>

			<div id="ld-gb-report-card-<?php echo $gradebook_id; ?>-component-list">

				<?php
				/**
				 * Outputs the overall grade.
				 *
				 * @since 1.1.0
				 * @updated 2.0.0
 * @version 4.3.2
				 *
				 * @hooked LD_GB_SC_ReportCard::template_overall_grade() 10
				 */
				do_action( 'report-card-overall-grade', $user_grade, $gradebook_id, $grade_format );
				?>

				<?php foreach ( $user_grade->get_components() as $component ) : ?>

					<?php
					/**
					 * Outputs a component row.
					 *
					 * @since 1.1.0
					 * @updated 2.0.0
 * @version 4.3.2
					 *
					 * @hooked LD_GB_SC_ReportCard::template_component() 10
					 */
					do_action( 'report-card-component', $component, $user_grade, $gradebook_id, $grade_format );
					?>

				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

</div>
