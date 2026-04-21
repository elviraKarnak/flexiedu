<?php
/**
 * Report card overall grade.
 *
 * @since 1.1.0
 * @updated 3.3.0
 * @version 4.3.2

 *
 * @var LD_GB_UserGrade $user_grade
 * @var int $gradebook_id
 * @var string $grade_format
 */

defined( 'ABSPATH' ) || die();
?>

<div id="ld-gb-report-card-overall" class="ld-gb-report-card-overall">
	<div class="ld-gb-report-card-section-info">
		<span class="ld-gb-report-card-section-title">
			<?php _ex( 'Overall Grade', 'Report Card: Overall Grade Label', 'learndash-gradebook' ); ?>
		</span>

		<span class="ld-gb-report-card-section-grade"
				style="background-color: <?php $user_grade->display_user_grade_color(); ?>;">
			<?php echo $user_grade->display_user_grade( $grade_format ); ?>
		</span>
	</div>
</div>
