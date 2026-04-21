<?php
/**
 * Report card component grade.
 *
 * @since 1.1.0
 * @updated 2.0.0
 * @version 4.3.2
 *
 * @var array $component
 * @var LD_GB_UserGrade $user_grade
 * @var int $gradebook_id
 * @var string $component_handle
 * @var string $grade_format
 */

defined( 'ABSPATH' ) || die();
?>

<span class="ld-gb-report-card-section-grade"
		style="background-color: <?php $user_grade->display_grade_color( $component['averaged_score'] ); ?>;">
	<?php echo $user_grade->display_grade( $component['averaged_score'], $grade_format ); ?>
</span>
