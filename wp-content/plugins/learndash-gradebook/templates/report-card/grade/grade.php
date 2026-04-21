<?php
/**
 * Report card grade row.
 *
 * @since 1.1.0
 * @updated 3.0.0
 * @version 4.3.2
 *
 * @var string $grade
 * @var int $grade_index
 * @var array $component
 * @var LD_GB_UserGrade $user_grade
 * @var integer $gradebook_id
 */

defined( 'ABSPATH' ) || die();

$row_ID = "ld-gb-report-card-grade-{$component['id']}-";

switch ( $grade['type'] ) {
	case 'quiz':
	case 'assignment':
		$row_ID .= $grade['post_id'];
		break;

	case 'manual':
	default:
		$row_ID .= sanitize_html_class( $grade['name'] );
}
?>
<tr id="<?php echo $row_ID; ?>">
	<?php
	/**
	 * Outputs the grade content.
	 *
	 * @since 1.1.0
	 * @updated 1.6.0
 * @version 4.3.2
	 *
	 * @hooked LD_GB_SC_ReportCard::template_grade_type() 10
	 * @hooked LD_GB_SC_ReportCard::template_grade_name() 20
	 * @hooked LD_GB_SC_ReportCard::template_grade_score() 30
	 */
	do_action( 'report-card-grade-content', $grade, $grade_index, $component, $user_grade, $gradebook_id );
	?>
</tr>
