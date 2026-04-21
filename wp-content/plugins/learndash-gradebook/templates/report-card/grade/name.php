<?php
/**
 * Report card grade name.
 *
 * @package LearnDash\Gradebook
 *
 * @since 1.1.0
 * @version 4.3.3
 *
 * @var array<string, mixed> $grade        Grade data.
 * @var int                  $grade_index  Grade index.
 * @var array<string, mixed> $component    Component data.
 * @var LD_GB_UserGrade      $user_grade   User grade object.
 * @var int                  $gradebook_id Gradebook ID.
 */

use LearnDash\Core\Utilities\Cast;

defined( 'ABSPATH' ) || die();

$has_link = isset( $grade['post_id'] ) && $grade['post_id'];

// Get the course ID for proper navigation context.
$course_id    = 0;
$quiz_post_id = $has_link ? Cast::to_int( $grade['post_id'] ) : 0;

if ( ! empty( $grade['course_id'] ) ) {
	$course_id = Cast::to_int( $grade['course_id'] );
} elseif ( ! empty( $grade['post_id'] ) ) {
	$course_id    = Cast::to_int( learndash_get_course_id( $quiz_post_id ) );
}
?>
<td class="ld-gb-report-card-grades-column-name">
	<?php if ( $has_link ) : ?>
		<a href="<?php echo esc_attr( learndash_get_step_permalink( $quiz_post_id, $course_id ) ); ?>" target="_blank">
	<?php endif; ?>
			<?php echo esc_html( Cast::to_string( $grade['name'] ) ); ?>
	<?php if ( $has_link ) : ?>
		</a>
	<?php endif; ?>
</td>
