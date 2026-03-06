<?php
/**
 * Course Reports Main Template
 *
 * @since 3.3.0
 *
 * @var $course_id  int     ID of the course.
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

do_action( 'ir_action_course_reports_start', $course_id );

/**
 * Filter course report chart template path for others to extend
 *
 * @since 3.3.0
 */
require apply_filters(
	'ir_filter_course_report_chart_template_path',
	INSTRUCTOR_ROLE_ABSPATH . 'modules/templates/course-report/ir-course-reports-chart.template.php',
	$course_id
);

/**
 * Filter course report user records template path for others to extend
 *
 * @since 3.3.0
 */
require apply_filters(
	'ir_filter_course_report_user_records_template_path',
	INSTRUCTOR_ROLE_ABSPATH . 'modules/templates/course-report/ir-course-reports-user-records.template.php',
	$course_id
);

do_action( 'ir_action_course_reports_end', $course_id );
