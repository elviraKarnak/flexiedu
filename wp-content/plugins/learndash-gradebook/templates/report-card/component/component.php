<?php
/**
 * Report card component row.
 *
 * @since 1.1.0
 * @updated 3.0.0
 * @version 4.3.2
 *
 * @var array $component
 * @var LD_GB_UserGrade $user_grade
 * @var int $gradebook_id
 * @var string $grade_format
 */

defined( 'ABSPATH' ) || die();

$component_handle = substr( md5( serialize( $component ) ), 0, 8 );
?>
<div id="ld-gb-report-card-<?php echo $gradebook_id; ?>-component-<?php echo $component_handle; ?>" class="ld-gb-report-card-component">

	<?php
	/**
	 * Displays the component toggle.
	 *
	 * @since 1.1.0
	 * @updated 1.6.0
 * @version 4.3.2
	 *
	 * @hooked LD_GB_SC_ReportCard::template_component_toggle() 10
	 */
	do_action( 'report-card-component-toggle', $component, $user_grade, $gradebook_id, $component_handle );
	?>

	<div class="ld-gb-report-card-component-container">

		<div class="ld-gb-report-card-section-info">
			<?php
			/**
			 * Displays the component title and grade.
			 *
			 * @since 1.1.0
			 * @updated 2.0.0
 * @version 4.3.2
			 *
			 * @hooked LD_GB_SC_ReportCard::template_component_title() 10
			 * @hooked LD_GB_SC_ReportCard::template_component_grade() 20
			 */
			do_action( 'report-card-component-info', $component, $user_grade, $gradebook_id, $component_handle, $grade_format );
			?>
		</div>

		<?php if ( $component['grades'] ) : ?>
			<div class="ld-gb-report-card-grades-container flip" style="display: none;">
				<table class="ld-gb-report-card-grades">
					<thead>
					<tr>
						<?php
						/**
						 * Outputs the table header for the component grades table.
						 *
						 * @since 1.1.0
						 * @updated 1.6.0
 * @version 4.3.2
						 *
						 * @hooked LD_GB_SC_ReportCard::template__grades_header() 10
						 */
						do_action( 'report-card-grades-header', $component, $user_grade, $gradebook_id );
						?>
					</tr>
					</thead>

					<tbody>
					<?php foreach ( $component['grades'] as $grade_index => $grade ) : ?>

						<?php
						/**
						 * Outputs a report card component grade.
						 *
						 * @since 1.1.0
						 * @updated 1.6.0
 * @version 4.3.2
						 *
						 * @hooked LD_GB_SC_ReportCard::template_grade() 10
						 */
						do_action( 'report-card-grade', $grade, $grade_index, $component, $user_grade, $gradebook_id );
						?>

					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
	</div>
</div>
