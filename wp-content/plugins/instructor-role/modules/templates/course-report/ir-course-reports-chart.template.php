<?php
/**
 * Course Reports Chart Template.
 *
 * @var $course_progress_details    array   Course progress details.
 * @var $course_statistics          array   Course statistics information.
 * @var $course_id                  int     ID of the course.
 * @var $course_title               string  Title of the course.
 *
 * @since 3.3.0
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<div id="wdm-report-graph">

	<div id="wdm_left_report">
		<div id="not_started">
			<span class="color-code" style="background-color: <?php echo esc_attr( $colors[0] ); ?>"></span>&nbsp;&nbsp;&nbsp;&nbsp;
			<?php
			esc_html_e( __( 'Not Started', 'wdm_instructor_role' ) . ': ' . ( $course_progress_details['not_started'] ) . '% ( ' . $course_statistics['not_started'] . '/' . $course_statistics['total'] . ' )' );
			?>
		</div>
		<div id="in_progress">
			<span class="color-code" style="background-color: <?php echo esc_attr( $colors[1] ); ?>"></span>&nbsp;&nbsp;&nbsp;&nbsp;
			<?php
			esc_html_e( __( 'In Progress', 'wdm_instructor_role' ) . ': ' . ( $course_progress_details['in_progress'] ) . '% ( ' . $course_statistics['in_progress'] . '/' . $course_statistics['total'] . ' )' );
			?>
		</div>
		<div id="completed">
			<span class="color-code" style="background-color: <?php echo esc_attr( $colors[2] ); ?>"></span>&nbsp;&nbsp;&nbsp;&nbsp;
			<?php
			esc_html_e( __( 'Completed', 'wdm_instructor_role' ) . ': ' . ( $course_progress_details['completed'] ) . '% ( ' . $course_statistics['completed'] . '/' . $course_statistics['total'] . ' )' );
			?>
		</div>
	</div>

	<div id="wdm_report_div" ></div><!-- high chart div -->
	<!--    added form for mail to all the users of that particular course -->
	<div id="mail_by_instructor">
		<form method="post" id="instructor_message_form">
			<h4 class="learndash_instructor_send_message_label">
			<?php
				/* translators: Course Title*/
				printf( esc_html__( 'Send message to all %s users', 'wdm_instructor_role' ), '<i>' . $course_title . '</i>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later.
			?>
			</h4>
			<label>
				<?php _e( 'Subject:', 'wdm_instructor_role' ); ?>
			</label>
			<span id="learndash_instructor_subject_err"></span>
			<br>
			<input type="text" size="40" id="learndash_instructor_subject" name="learndash_instructor_subject" style="margin-bottom: 15px;">
			<br>
			<div class="learndash_instructor_message_label">
				<label for="learndash_instructor_message_label">
					<?php _e( 'Body:', 'wdm_instructor_role' ); ?>
				</label>
				<span id="learndash_instructor_message_err"></span>
			</div>
			<textarea id="learndash_instructor_message" rows="10" cols="40" id="learndash_propanel_message" name="learndash_instructor_message"></textarea>
			<br>
			<input class="wdm-button" type="submit" name="submit_instructor_email" value="<?php _e( 'Send Email', 'wdm_instructor_role' ); ?>">
			<input type="hidden" name="course_id" value="<?php echo $course_id; ?>" />
		</form>
	</div>
	<div class="CL" ></div>
</div>
