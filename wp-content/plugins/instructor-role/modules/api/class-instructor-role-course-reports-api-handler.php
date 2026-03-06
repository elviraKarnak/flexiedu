<?php
/**
 * Course Reports Rest API Handler Module
 *
 * @since 5.5.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

namespace InstructorRole\Modules\Api;

use InstructorRole\Modules\Classes\Instructor_Role_Reports;
use WP_Rest_Server;
use WP_Error;
use WP_REST_Posts_Controller;
use WP_Post, WP_Query;
use WP_User;
use WP_User_Query;
use LDLMS_DB;
use LDLMS_Post_Types;
use LearnDash_Custom_Label;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Course_Reports_Api_Handler' ) ) {
	/**
	 * Class Instructor Role Course Reports Api Handler
	 */
	class Instructor_Role_Course_Reports_Api_Handler extends Instructor_Role_Dashboard_Block_Api_Handler {
		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
		 *
		 * @since 5.5.0
		 */
		protected static $instance = null;

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 5.5.0
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Register custom endpoints
		 *
		 * @since 5.5.0
		 */
		public function register_custom_endpoints() {
			// Course Reports Chart Data.
			register_rest_route(
				$this->namespace,
				'/course-report/(?P<id>[\d]+)/chart',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_course_reports_chart' ],
						'permission_callback' => [ $this, 'get_course_reports_chart_permissions_check' ],
					],
				]
			);

			// Course Reports User Data.
			register_rest_route(
				$this->namespace,
				'/course-report/(?P<id>[\d]+)/users',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_course_reports_users' ],
						'permission_callback' => [ $this, 'get_course_reports_users_permissions_check' ],
					],
				]
			);

			// Email Settings.
			register_rest_route(
				$this->namespace,
				'/settings/email/',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_email_settings' ],
						'permission_callback' => [ $this, 'get_email_settings_permissions_check' ],
					],
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'update_email_settings' ],
						'permission_callback' => [ $this, 'update_email_settings_permissions_check' ],
					],
				]
			);

			// Course Reports Chart Data.
			register_rest_route(
				$this->namespace,
				'/learner-report/(?P<id>[\d]+)/chart',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_learner_reports_chart' ],
						'permission_callback' => [ $this, 'get_learner_reports_chart_permissions_check' ],
					],
				]
			);

			// Learner Reports Course Data.
			register_rest_route(
				$this->namespace,
				'/learner-report/(?P<id>[\d]+)/courses',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_learner_reports_courses' ],
						'permission_callback' => [ $this, 'get_learner_reports_courses_permissions_check' ],
					],
				]
			);
		}

		/**
		 * Get course reports chart permissions check
		 *
		 * @since 5.5.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_course_reports_chart_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get course reports chart data
		 *
		 * @since 5.5.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_course_reports_chart( $request ) {
			$data            = [];
			$current_user_id = get_current_user_id();

			$course = get_post( $request['id'] );

			// Check if valid WP_Post object.
			if ( empty( $course ) || ! $course instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			if ( ! current_user_can( 'manage_options' ) && ! in_array( $course->ID, ir_get_instructor_complete_course_list( $current_user_id, 1 ), true ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			$data['progress_stats']  = $this->get_course_progress_statistics_data( $course->ID );
			$data['content_numbers'] = $this->get_course_content_count( $course->ID );
			$data['course_name']     = get_the_title( $course->ID );
			$data['export_link']     = add_query_arg(
				[
					'action'    => 'export_course_reports',
					'course_id' => $course->ID,
					'ir_nonce'  => wp_create_nonce( 'ir_export_course_report' ),
				],
				admin_url( 'admin-ajax.php' )
			);

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get course progress statistics data
		 *
		 * @since 5.5.0
		 *
		 * @param int $course_id    ID of the course.
		 * @return mixed
		 */
		public function get_course_progress_statistics_data( $course_id ) {
			$progress_stats = [
				'not_started' => 0,
				'in_progress' => 0,
				'completed'   => 0,
				'total'       => 0,
			];

			// Check if course id not empty.
			if ( empty( $course_id ) ) {
				return $progress_stats;
			}

			// Get list of all students who have access to the course enrolled either through group or directly in the course.
			$course_access_users = ir_get_users_with_course_access( $course_id, [ 'direct', 'group' ] );

			// Get total count of all the users.
			$total_users             = count( $course_access_users );
			$progress_stats['total'] = $total_users;

			if ( function_exists( 'learndash_course_get_steps_count' ) ) {
				$total_course_steps = learndash_course_get_steps_count( $course_id );
			} else {
				$total_course_steps = learndash_get_course_steps_count( $course_id );
			}

			$progress_stats['upto_20']             = 0;
			$progress_stats['upto_40']             = 0;
			$progress_stats['upto_60']             = 0;
			$progress_stats['upto_80']             = 0;
			$progress_stats['upto_100']            = 0;
			$progress_stats['completed']           = 0;
			$progress_stats['in_progress']         = 0;
			$progress_stats['not_started']         = 0;
			$progress_stats['avg_completion_rate'] = 0;
			$progress_stats['not_started_percent'] = 0;
			$progress_stats['in_progress_percent'] = 0;
			$progress_stats['completed_percent']   = 0;
			$total_completion                      = 0;

			if ( empty( $course_access_users ) ) {
				return $progress_stats;
			}

			foreach ( $course_access_users as $user_id ) {
				$progress = learndash_user_get_course_progress( $user_id, $course_id, 'summary' );

				if ( ! isset( $progress['total'] ) || empty( $progress['total'] ) ) {
					++$progress_stats['upto_20'];
					++$progress_stats['not_started'];
					continue;
				}

				$completion = floatval( number_format( 100 * $progress['completed'] / $progress['total'], 2, '.', '' ) );

				$total_completion += $completion;

				if ( $completion > 80 && $completion < 100 ) {
					++$progress_stats['upto_100'];
					++$progress_stats['in_progress'];
				} elseif ( $completion > 60 && $completion <= 80 ) {
					++$progress_stats['upto_80'];
					++$progress_stats['in_progress'];
				} elseif ( $completion > 40 && $completion <= 60 ) {
					++$progress_stats['upto_60'];
					++$progress_stats['in_progress'];
				} elseif ( $completion > 20 && $completion <= 40 ) {
					++$progress_stats['upto_40'];
					++$progress_stats['in_progress'];
				} elseif ( $completion > 0 && $completion <= 20 ) {
					++$progress_stats['upto_20'];
					++$progress_stats['in_progress'];
				}

				if ( 100 === intval( $completion ) ) {
					++$progress_stats['upto_100'];
					++$progress_stats['completed'];
				}

				if ( 0 === intval( $completion ) ) {
					++$progress_stats['upto_20'];
					++$progress_stats['not_started'];
				}
			}
			$progress_stats['avg_completion_rate'] = floatval( number_format( $total_completion / count( $course_access_users ), 2, '.', '' ) );

			$progress_stats['not_started_percent'] = round( ( $progress_stats['not_started'] / $progress_stats['total'] ) * 100, 2 );
			$progress_stats['in_progress_percent'] = round( ( $progress_stats['in_progress'] / $progress_stats['total'] ) * 100, 2 );
			$progress_stats['completed_percent']   = round( 100 - ( $progress_stats['not_started_percent'] + $progress_stats['in_progress_percent'] ), 2 );

			return $progress_stats;
		}

		/**
		 * Get count of course lesson, topic and quizzes
		 *
		 * @since 5.5.0
		 *
		 * @param int $course_id    ID of the course.
		 * @return array            Course content counts.
		 */
		public function get_course_content_count( $course_id ) {
			$content_numbers = [
				'lessons' => 0,
				'topics'  => 0,
				'quizzes' => 0,
			];

			if ( empty( $course_id ) ) {
				return $content_numbers;
			}

			// Get Content Numbers.
			$lessons = learndash_course_get_children_of_step(
				$course_id,
				0,
				LDLMS_Post_Types::get_post_type_slug( LDLMS_Post_Types::LESSON ),
				'ids',
				true
			);

			$topics = learndash_course_get_children_of_step(
				$course_id,
				0,
				LDLMS_Post_Types::get_post_type_slug( LDLMS_Post_Types::TOPIC ),
				'ids',
				true
			);

			$quizzes = learndash_course_get_children_of_step(
				$course_id,
				0,
				LDLMS_Post_Types::get_post_type_slug( LDLMS_Post_Types::QUIZ ),
				'ids',
				true
			);

			$content_numbers = [
				'lessons' => count( $lessons ),
				'topics'  => count( $topics ),
				'quizzes' => count( $quizzes ),
			];

			return $content_numbers;
		}

		/**
		 * Get user course progress data
		 *
		 * @since 5.5.0
		 *
		 * @param int $user_id      ID of the user.
		 * @param int $course_id    ID of the course.
		 * @return mixed
		 */
		public function get_user_course_progress_data( $user_id, $course_id ) {
			$course_progress = [
				'total_steps'         => 0,
				'completed_steps'     => 0,
				'percentage'          => 0,
				'course_completed_on' => 0,
			];

			// Check if empty user id or course id.
			if ( empty( $user_id ) || empty( $course_id ) ) {
				return false;
			}

			$total_course_steps = learndash_course_get_steps_count( $course_id );

			// No course progress if no steps.
			if ( ! empty( $total_course_steps ) ) {
				$course_completed_date = '-';
				$course_completed_meta = '';

				$completed_steps = learndash_course_get_completed_steps( $user_id, $course_id );

				if ( $completed_steps > 0 ) {
					if ( $completed_steps === $total_course_steps ) {
						$course_completed_meta = get_user_meta( $user_id, 'course_completed_' . $course_id, true );
						$course_completed_date = date_i18n( 'M d, Y g:i a', $course_completed_meta );
					}
				}

				$course_progress = [
					'total_steps'                => $total_course_steps,
					'completed_steps'            => $completed_steps,
					'percentage'                 => number_format( round( ( $completed_steps / $total_course_steps ) * 100, 2 ), 2 ),
					'course_completed_on'        => $course_completed_date,
					'course_completed_timestamp' => $course_completed_meta,
				];
			}

			// Get user data and enrollment info.
			$user_data       = get_userdata( $user_id );
			$enrollment_info = $this->get_user_course_enrollment_info( $user_id, $course_id, true );

			return [
				'user_id'         => $user_data->ID,
				'display_name'    => $user_data->display_name,
				'user_email'      => $user_data->user_email,
				'enrollment_info' => $enrollment_info,
				'progress'        => $course_progress,
			];
		}

		/**
		 * Get user course enrollment info
		 *
		 * @since 5.5.0
		 *
		 * @param int  $user_id      ID of the user.
		 * @param int  $course_id    ID of the course.
		 * @param bool $legacy      Whether to use legacy meta keys or not. Default false.
		 *
		 * @return mixed            Array with course enrollment info, else false.
		 */
		public function get_user_course_enrollment_info( $user_id, $course_id, $legacy = false ) {
			$course_enrollment_info = [
				'access_type' => '',
				'timestamp'   => '',
				'formatted'   => false,
			];

			// Check if empty user id or course id.
			if ( empty( $user_id ) || empty( $course_id ) ) {
				return false;
			}

			// Check for group access.
			$course_groups_ids = learndash_get_course_groups( $course_id );

			if ( ! empty( $course_groups_ids ) ) {
				foreach ( $course_groups_ids as $group_id ) {
					if ( $legacy ) {
						$group_access_from = get_user_meta( $user_id, "group_{$group_id}_access_from", 1 );
					} else {
						$group_access_from = get_user_meta( $user_id, "learndash_group_{$group_id}_enrolled_at", 1 );
					}

					if ( ! empty( $group_access_from ) ) {
						return [
							'access_type' => 'group',
							'timestamp'   => $group_access_from,
							'formatted'   => date_i18n( 'M d, Y g:i a', $group_access_from ),
						];
					}
				}
			}

			// Check for direct course access.
			if ( $legacy ) {
				$course_access_from = get_user_meta( $user_id, "course_{$course_id}_access_from", 1 );
			} else {
				$course_access_from = get_user_meta( $user_id, "learndash_course_{$course_id}_enrolled_at", 1 );
			}
			if ( ! empty( $course_access_from ) ) {
				return [
					'access_type' => 'course',
					'time_stamp'  => $course_access_from,
					'formatted'   => date_i18n( 'M d,Y g:i a', $course_access_from ),
				];
			}

			return $course_enrollment_info;
		}

		/**
		 * Get course reports users permissions check
		 *
		 * @since 5.5.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_course_reports_users_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get course reports users data
		 *
		 * @since 5.5.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_course_reports_users( $request ) {
			$data            = [];
			$current_user_id = get_current_user_id();

			$course = get_post( $request['id'] );

			// Check if valid WP_Post object.
			if ( empty( $course ) || ! $course instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			if ( ! current_user_can( 'manage_options' ) && ! in_array( $course->ID, ir_get_instructor_complete_course_list( $current_user_id, 1 ), true ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			$parameters = shortcode_atts(
				[
					'students'         => '',
					'search'           => '',
					'page'             => 1,
					'per_page'         => 10,
					'offset'           => 0,
					'order_by'         => 'enrollment_time DESC',
					'start_timestamp'  => '',
					'end_timestamp'    => '',
					'progress_status'  => 'all',
					'progress_percent' => -1,
					'groups'           => [],
					'total_steps'      => 0,
				],
				$request->get_params()
			);

			// Get list of associated groups.
			$groups = learndash_get_course_groups( $course->ID );
			if ( ! empty( $groups ) ) {
				$parameters['groups'] = $groups;
			}

			// Get total course steps.
			$total_course_steps = learndash_get_course_steps_count( $course->ID );
			if ( ! empty( $total_course_steps ) ) {
				$parameters['total_steps'] = $total_course_steps;
			}

			// Filter results to selected progress status.
			if ( ! empty( $parameters['progress_status'] ) ) {
				$valid_status = [ 'in_progress', 'not_started', 'completed' ];
				if ( ! in_array( $parameters['progress_status'], $valid_status, true ) ) {
					$parameters['progress_status'] = 'all';
				}
			}

			// Filter results to selected progress percentage.
			if ( ! empty( $parameters['progress_percent'] ) && -1 !== intval( $parameters['progress_percent'] ) ) {
				$valid_progress = [ 20, 40, 60, 80, 100 ];
				if ( ! in_array( intval( $parameters['progress_percent'] ), $valid_progress, true ) ) {
					$parameters['progress_percent'] = -1;
				}
			}

			// Filter results on start date.
			if ( ! empty( $parameters['start_timestamp'] ) ) {
				$parameters['start_timestamp'] = filter_var( $parameters['start_timestamp'], FILTER_SANITIZE_NUMBER_INT );
			}

			// Filter results on end date.
			if ( ! empty( $parameters['end_timestamp'] ) ) {
				$parameters['end_timestamp'] = filter_var( $parameters['end_timestamp'], FILTER_SANITIZE_NUMBER_INT );
			}

			// Filter results to search text.
			if ( ! empty( $parameters['search'] ) ) {
				$parameters['search'] = trim( filter_var( $parameters['search'] ) );
			}

			// Filter results to selected students.
			if ( ! empty( $parameters['students'] ) ) {
				$students = array_filter(
					array_map(
						function ( $student_id ) {
							return filter_var( trim( $student_id ), FILTER_VALIDATE_INT );
						},
						explode( ',', $parameters['students'] )
					)
				);

				$parameters['students'] = trim( implode( ',', $students ) );
			}

			// Paginate.
			if ( 1 < $parameters['page'] ) {
				$parameters['offset'] = $parameters['per_page'] * ( intval( $parameters['page'] ) - 1 );
			}

			// Sort.
			if ( 'enrollment_time' !== $parameters['order_by'] ) {
				$order_by_types = [
					'enrollment_time',
					'high_to_low',
					'low_to_high',
				];
				if ( ! in_array( $parameters['order_by'], $order_by_types, 1 ) ) {
					$parameters['order_by'] = 'enrollment_time';
				}
			}

			$course_users        = $this->fetch_course_users_list( $course->ID, $parameters, true );
			$data['total_pages'] = empty( $course_users ) ? null : current( $course_users )->total_pages;

			foreach ( $course_users as $user_data ) {
				$data['users'][] = $this->get_user_course_progress_data( $user_data->user_id, $course->ID );
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Send email notification to course users
		 *
		 * @since 5.5.0
		 */
		public function ajax_send_course_users_email_notification() {
			$response = [
				'message' => __( 'The notification email(s) could not be sent. Please contact the Admin or Instructor.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Verify Nonce.
			if ( ! check_ajax_referer( 'ir_send_course_email_notifications', 'ir_nonce', false ) ) {
				wp_send_json_error( $response );
			}

			// Sanitize data.
			$course_id       = ir_filter_input( 'course_id', INPUT_POST, 'number' );
			$current_user_id = get_current_user_id();

			// Check if user is admin or instructor.
			if ( ! current_user_can( 'manage_options' ) && ! wdm_is_instructor( $current_user_id ) ) {
				wp_send_json_error(
					[
						'message' => __( 'You do not have sufficient privileges to perform this action', 'wdm_instructor_role' ),
						'type'    => 'error',
					],
					403
				);
			}

			// Check if instructor has access to course.
			if ( ! current_user_can( 'manage_options' ) ) {
				if ( ! in_array( $course_id, ir_get_instructor_complete_course_list( $current_user_id ) ) ) {
					wp_send_json_error(
						[
							'message' => __( 'You do not have access to this post', 'wdm_instructor_role' ),
							'type'    => 'error',
						],
						403
					);
				}
			}

			$course_users = ir_get_users_with_course_access( $course_id, [ 'direct', 'group' ] );

			if ( empty( $course_users ) ) {
				wp_send_json_success(
					[
						'message' => __( 'No registered users enrolled in the course', 'wdm_instructor_role' ),
						'type'    => 'success',
					]
				);
			}

			// Check if the "from" input field is filled out.
			$subject = stripslashes( ir_filter_input( 'ir_message_subject' ) );
			$message = stripslashes( ir_filter_input( 'ir_message_body' ) );
			$subject = wp_strip_all_tags( $subject );
			$message = html_entity_decode( $message );

			// Message lines should not exceed 70 characters (PHP rule), so wrap it.
			$message = wordwrap( $message, 70 );

			// Send mail.
			$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
			$success = [];
			$failed  = [];

			foreach ( $course_users as $user_id ) {
				$user = get_user_by( 'ID', $user_id );

				if ( ! $user instanceof WP_User ) {
					continue;
				}

				// Store successfully email ids.
				if ( wp_mail( $user->user_email, $subject, $message, $headers ) ) {
					$success[] = $user_id;
				} else {
					$failed[] = $user_id;
				}
			}

			wp_send_json_success(
				[
					'message' => __( 'Successfully completed course users email notifications', 'wdm_instructor_role' ),
					'type'    => 'success',
					'success' => $success,
					'failed'  => $failed,
				]
			);
		}

		/**
		 * Send email notification to selected user in Learner reports.
		 *
		 * @since 5.5.0
		 */
		public function ajax_send_learner_email_notification() {
			$response = [
				'message' => __( 'The notification email(s) could not be sent. Please contact the Admin or Instructor.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Verify Nonce.
			if ( ! check_ajax_referer( 'ir_send_course_email_notifications', 'ir_nonce', false ) ) {
				wp_send_json_error( $response );
			}

			// Sanitize data.
			$learner_id = ir_filter_input( 'learner', INPUT_POST, 'number' );
			$learner    = get_userdata( $learner_id );

			// Check if user is valid.
			if ( ! $learner instanceof WP_User ) {
				wp_send_json_error(
					[
						'message' => __( 'Invalid user', 'wdm_instructor_role' ),
						'type'    => 'error',
					],
					401
				);
			}

			$current_user_id = get_current_user_id();

			// Check if user is admin or instructor.
			if ( ! current_user_can( 'manage_options' ) && ! wdm_is_instructor( $current_user_id ) ) {
				wp_send_json_error(
					[
						'message' => __( 'You do not have sufficient privileges to perform this action', 'wdm_instructor_role' ),
						'type'    => 'error',
					],
					403
				);
			}

			// Check if instructor has access to course.
			if ( ! current_user_can( 'manage_options' ) ) {
				if ( ! $this->check_if_user_data_accessible( $learner_id ) ) {
					wp_send_json_error(
						[
							'message' => __( 'You do not have access to this user', 'wdm_instructor_role' ),
							'type'    => 'error',
						],
						403
					);
				}
			}

			// Check if the "from" input field is filled out.
			$subject = stripslashes( ir_filter_input( 'ir_message_subject' ) );
			$message = stripslashes( ir_filter_input( 'ir_message_body' ) );
			$subject = wp_strip_all_tags( $subject );
			$message = html_entity_decode( $message );

			// Message lines should not exceed 70 characters (PHP rule), so wrap it.
			$message = wordwrap( $message, 70 );

			// Send mail.
			$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
			$success = [];
			$failed  = [];

			// Store successfully email ids.
			if ( wp_mail( $learner->user_email, $subject, $message, $headers ) ) {
				$success = $learner_id;
			} else {
				$failed = $learner_id;
			}

			wp_send_json_success(
				[
					'message' => __( 'Successfully completed user\'s email notifications', 'wdm_instructor_role' ),
					'type'    => 'success',
					'success' => $success,
					'failed'  => $failed,
				]
			);
		}

		/**
		 * Send email notification to selected course users
		 *
		 * @since 5.5.0
		 */
		public function ajax_send_selected_course_users_email_notification() {
			$response = [
				'message' => __( 'The notification email(s) could not be sent. Please contact the Admin or Instructor.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Verify Nonce.
			if ( ! check_ajax_referer( 'ir_send_course_email_notifications', 'ir_nonce', false ) ) {
				wp_send_json_error( $response );
			}

			// Sanitize data.
			$course_id       = ir_filter_input( 'course_id', INPUT_POST, 'number' );
			$current_user_id = get_current_user_id();

			// Check if user is admin or instructor.
			if ( ! current_user_can( 'manage_options' ) && ! wdm_is_instructor( $current_user_id ) ) {
				wp_send_json_error(
					[
						'message' => __( 'You do not have sufficient privileges to perform this action', 'wdm_instructor_role' ),
						'type'    => 'error',
					],
					403
				);
			}

			// Check if instructor has access to course.
			if ( ! current_user_can( 'manage_options' ) ) {
				if ( ! in_array( $course_id, ir_get_instructor_complete_course_list( $current_user_id ) ) ) {
					wp_send_json_error(
						[
							'message' => __( 'You do not have access to this post', 'wdm_instructor_role' ),
							'type'    => 'error',
						],
						403
					);
				}
			}

			$course_users = ir_get_users_with_course_access( $course_id, [ 'direct', 'group' ] );

			if ( empty( $course_users ) ) {
				wp_send_json_success(
					[
						'message' => __( 'No registered users enrolled in the course', 'wdm_instructor_role' ),
						'type'    => 'success',
					]
				);
			}

			// Get selected user ids.
			$selected_users = filter_input( INPUT_POST, 'selected_users', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );

			// Check if the "from" input field is filled out.
			$subject = stripslashes( ir_filter_input( 'ir_message_subject' ) );
			$message = stripslashes( ir_filter_input( 'ir_message_body' ) );
			$subject = wp_strip_all_tags( $subject );
			$message = html_entity_decode( $message );

			// Message lines should not exceed 70 characters (PHP rule), so wrap it.
			$message = wordwrap( $message, 70 );

			// Send mail.
			$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
			$success = [];
			$failed  = [];

			foreach ( $selected_users as $user_id ) {
				// If user not in course, continue.
				if ( ! in_array( $user_id, $course_users ) ) {
					continue;
				}

				$user = get_user_by( 'ID', $user_id );

				// Store successfully email ids.
				if ( wp_mail( $user->data->user_email, $subject, $message, $headers ) ) {
					$success[] = $user_id;
				} else {
					$failed[] = $user_id;
				}
			}

			wp_send_json_success(
				[
					'message' => __( 'Successfully completed course users email notifications', 'wdm_instructor_role' ),
					'type'    => 'success',
					'success' => $success,
					'failed'  => $failed,
				]
			);
		}

		/**
		 * Send email notification to filtered course users
		 *
		 * @since 5.5.0
		 */
		public function ajax_send_filtered_course_users_email_notification() {
			$response = [
				'message' => __( 'The notification email(s) could not be sent. Please contact the Admin or Instructor.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Verify Nonce.
			if ( ! check_ajax_referer( 'ir_send_course_email_notifications', 'ir_nonce', false ) ) {
				wp_send_json_error( $response );
			}

			// Sanitize data.
			$course_id       = ir_filter_input( 'course_id', INPUT_POST, 'number' );
			$current_user_id = get_current_user_id();

			// Check if user is admin or instructor.
			if ( ! current_user_can( 'manage_options' ) && ! wdm_is_instructor( $current_user_id ) ) {
				wp_send_json_error(
					[
						'message' => __( 'You do not have sufficient privileges to perform this action', 'wdm_instructor_role' ),
						'type'    => 'error',
					],
					403
				);
			}

			// Check if instructor has access to course.
			if ( ! current_user_can( 'manage_options' ) ) {
				if ( ! in_array( $course_id, ir_get_instructor_complete_course_list( $current_user_id ) ) ) {
					wp_send_json_error(
						[
							'message' => __( 'You do not have access to this post', 'wdm_instructor_role' ),
							'type'    => 'error',
						],
						403
					);
				}
			}

			$course_users = ir_get_users_with_course_access( $course_id, [ 'direct', 'group' ] );

			if ( empty( $course_users ) ) {
				wp_send_json_success(
					[
						'message' => __( 'No registered users enrolled in the course', 'wdm_instructor_role' ),
						'type'    => 'success',
					]
				);
			}

			// Get selected user ids.
			$filter_type = ir_filter_input( 'filter_type' );

			// Check if the "from" input field is filled out.
			$subject = stripslashes( ir_filter_input( 'ir_message_subject' ) );
			$message = stripslashes( ir_filter_input( 'ir_message_body' ) );
			$subject = wp_strip_all_tags( $subject );

			// Message lines should not exceed 70 characters (PHP rule), so wrap it.
			$message = wordwrap( $message, 70 );

			// Send mail.
			$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
			$success = [];
			$failed  = [];

			foreach ( $course_users as $user_id ) {
				// If user not in applied filter, continue.
				if ( ! $this->is_filtered_user( $user_id, $course_id, $course_users, $filter_type ) ) {
					continue;
				}

				$user = get_user_by( 'ID', $user_id );

				// Store successfully email ids.
				if ( wp_mail( $user->data->user_email, $subject, $message, $headers ) ) {
					$success[] = $user_id;
				} else {
					$failed[] = $user_id;
				}
			}

			wp_send_json_success(
				[
					'message' => __( 'Successfully completed course users email notifications', 'wdm_instructor_role' ),
					'type'    => 'success',
					'success' => $success,
					'failed'  => $failed,
				]
			);
		}

		/**
		 * Check if a user is falls under specific course filter.
		 *
		 * @since 5.5.0
		 *
		 * @param int    $user_id          ID of the user.
		 * @param int    $course_id        ID of the course.
		 * @param array  $course_users     Array of course users.
		 * @param string $filter_type      Type of applied filter.
		 *
		 * @return boolean
		 */
		public function is_filtered_user( $user_id, $course_id, $course_users, $filter_type ) {
			$is_filtered = false;
			// Check if user id not empty and user is a course user.
			if ( empty( $user_id ) || ! in_array( $user_id, $course_users ) ) {
				return false;
			}

			// Get course progress.
			$course_progress = learndash_user_get_course_progress( $user_id, $course_id, 'summary' );

			// Filter user based on progress status.
			if ( array_key_exists( 'status', $course_progress ) && $filter_type === $course_progress['status'] ) {
				$is_filtered = true;
			}

			return $is_filtered;
		}

		/**
		 * Export course report
		 *
		 * @since 5.5.0
		 */
		public function ajax_export_course_report() {
			$response = [
				'message' => __( 'The course report could not be exported. Please contact the Admin or Instructor.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Verify Nonce.
			if ( ! check_ajax_referer( 'ir_export_course_report', 'ir_nonce', false ) ) {
				wp_send_json_error( $response );
			}

			// Sanitize data.
			$course_id       = ir_filter_input( 'course_id', INPUT_GET, 'number' );
			$current_user_id = get_current_user_id();

			// Check if instructor has access to course.
			if ( ! current_user_can( 'manage_options' ) ) {
				if ( ! in_array( $course_id, ir_get_instructor_complete_course_list( $current_user_id ) ) ) {
					wp_send_json_error(
						[
							'message' => __( 'You do not have access to this post', 'wdm_instructor_role' ),
							'type'    => 'error',
						],
						403
					);
				}
			}

			$field_names   = [
				__( 'User ID', 'wdm_instructor_role' ),
				__( 'Name', 'wdm_instructor_role' ),
				__( 'Email', 'wdm_instructor_role' ),
				/* translators: Course label */
				sprintf( __( '%s ID', 'wdm_instructor_role' ), LearnDash_Custom_Label::get_label( 'course' ) ),
				/* translators: Course label */
				sprintf( __( '%s Title', 'wdm_instructor_role' ), LearnDash_Custom_Label::get_label( 'course' ) ),
				__( 'Completed Steps', 'wdm_instructor_role' ),
				/* translators: Course label */
				sprintf( __( '%s Completed', 'wdm_instructor_role' ), LearnDash_Custom_Label::get_label( 'course' ) ),
				__( 'Completion Date', 'wdm_instructor_role' ),
			];
			$exported_data = Instructor_Role_Reports::generate_csv_export_data( $course_id, $field_names );

			/**
			 * Refer method export_course_report_to_csv in class Instructor_Role_Reports for hook documentation.
			 */
			$field_names = apply_filters( 'ir_filter_csv_field_titles', $field_names, $exported_data );

			$file_name = sanitize_file_name( get_the_title( $course_id ) . '-' . gmdate( 'Y-m-d' ) );

			if ( empty( $exported_data ) ) {
				$exported_data[] = [ 'status' => __( 'No attempts', 'wdm_instructor_role' ) ];
			}

			/**
			 * Include parseCSV to write csv file.
			 */
			require_once LEARNDASH_LMS_LIBRARY_DIR . '/parsecsv.lib.php';

			$csv                  = new \lmsParseCSV();
			$csv->file            = $file_name;
			$csv->output_filename = $file_name;

			/**
			 * Filters csv object.
			 *
			 * @since 4.0
			 *
			 * @param \lmsParseCSV $csv CSV object.
			 * @param string       $context The context of the csv object.
			 */
			$csv = apply_filters( 'ir_filter_csv_object', $csv, 'ir_course_reports' );

			/**
			 * Filters the content will print onto the exported CSV
			 *
			 * @since 4.0
			 *
			 * @param void|array|mixed $content CSV content.
			 */
			$exported_data = apply_filters( 'ir_filter_course_export_data', $exported_data );

			$csv->output( $file_name . '.csv', $exported_data, array_keys( reset( $exported_data ) ) );
			wp_die();
		}

		/**
		 * Export course report
		 *
		 * @since 5.5.0
		 */
		public function ajax_export_learner_report() {
			$response = [
				'message' => __( 'The learner report could not be exported. Please contact the Admin or Instructor.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Verify Nonce.
			if ( ! check_ajax_referer( 'ir_export_learner_report', 'ir_nonce', false ) ) {
				wp_send_json_error( $response );
			}

			// Sanitize data.
			$learner     = ir_filter_input( 'learner', INPUT_GET, 'number' );
			$learner_obj = get_userdata( $learner );

			// Check if instructor has access to learner.
			if ( ! current_user_can( 'manage_options' ) ) {
				if ( ! $this->check_if_user_data_accessible( $learner ) ) {
					wp_send_json_error(
						[
							'message' => __( 'You do not have access to this post', 'wdm_instructor_role' ),
							'type'    => 'error',
						],
						403
					);
				}
			}

			$field_names   = [
				/* translators: Course label */
				sprintf( __( '%s ID', 'wdm_instructor_role' ), LearnDash_Custom_Label::get_label( 'course' ) ),
				/* translators: Course label */
				sprintf( __( '%s Title', 'wdm_instructor_role' ), LearnDash_Custom_Label::get_label( 'course' ) ),
				__( 'User ID', 'wdm_instructor_role' ),
				__( 'Name', 'wdm_instructor_role' ),
				__( 'Email', 'wdm_instructor_role' ),
				__( 'Status', 'wdm_instructor_role' ),
				__( 'Completed Steps', 'wdm_instructor_role' ),
				__( 'Completion Percentage', 'wdm_instructor_role' ),
				__( 'Enrollment Date', 'wdm_instructor_role' ),
				__( 'Completion Date', 'wdm_instructor_role' ),
			];
			$exported_data = Instructor_Role_Reports::generate_csv_export_data_for_learner( $learner_obj, $field_names );

			/**
			 * Refer method export_course_report_to_csv in class Instructor_Role_Reports for hook documentation.
			 */
			$field_names = apply_filters( 'ir_filter_learner_csv_field_titles', $field_names, $exported_data );

			$file_name = sanitize_file_name( $learner_obj->display_name . '-' . gmdate( 'Y-m-d' ) );

			if ( empty( $exported_data ) ) {
				$exported_data[] = [ 'status' => __( 'No attempts', 'wdm_instructor_role' ) ];
			}

			/**
			 * Include parseCSV to write csv file.
			 */
			require_once LEARNDASH_LMS_LIBRARY_DIR . '/parsecsv.lib.php';

			$csv                  = new \lmsParseCSV();
			$csv->file            = $file_name;
			$csv->output_filename = $file_name;

			/**
			 * Filters csv object.
			 *
			 * @since 4.0
			 *
			 * @param \lmsParseCSV $csv CSV object.
			 * @param string       $context The context of the csv object.
			 */
			$csv = apply_filters( 'ir_filter_csv_object', $csv, 'ir_course_reports' );

			/**
			 * Filters the content will print onto the exported CSV
			 *
			 * @since 4.0
			 *
			 * @param void|array|mixed $content CSV content.
			 */
			$exported_data = apply_filters( 'ir_filter_learner_export_data', $exported_data );

			$csv->output( $file_name . '.csv', $exported_data, array_keys( reset( $exported_data ) ) );
			wp_die();
		}

		/**
		 * Get email settings permissions check
		 *
		 * @since 5.5.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_email_settings_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get email settings data
		 *
		 * @since 5.5.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_email_settings( $request ) {
			$data            = [];
			$current_user_id = get_current_user_id();

			$data = [
				'quiz_completion' => $this->get_quiz_completion_email_settings( $current_user_id ),
				'course_purchase' => $this->get_course_purchase_email_settings( $current_user_id ),
			];

			// If admin user, return review email settings.
			if ( user_can( $current_user_id, 'manage_options' ) ) {
				$data['course_review']  = $this->get_course_review_email_settings();
				$data['product_review'] = $this->get_product_review_email_settings();
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get quiz completion email settings
		 *
		 * @since 5.5.0
		 *
		 * @param int $user_id  User ID of the instructor.
		 * @return mixed
		 */
		public function get_quiz_completion_email_settings( $user_id ) {
			// Check if global email settings enabled.
			$quiz_emails_enabled = ir_get_settings( 'instructor_mail' );

			if ( ! $quiz_emails_enabled ) {
				return [
					'status' => 'disabled',
				];
			}

			// Check if instructor email settings disabled.
			$is_instructor_emails_disabled = get_user_meta( $user_id, 'ir_quiz_emails_disabled', 1 );

			$subject = '';
			$body    = '';

			// Get instructor email settings with fallback global settings.
			$instructor_email_settings = get_user_meta( $user_id, 'instructor_email_template', true );
			$global_email_settings     = ir_get_email_settings();

			if ( ! empty( $instructor_email_settings ) && array_key_exists( 'mail_sub', $instructor_email_settings ) && ! empty( $instructor_email_settings['mail_sub'] ) ) {
				$subject = $instructor_email_settings['mail_sub'];
				$body    = $instructor_email_settings['mail_content'];
			} elseif ( ! empty( $global_email_settings ) && array_key_exists( 'qc_subject', $global_email_settings ) && ! empty( $global_email_settings['qc_subject'] ) ) {
				$subject = $global_email_settings['qc_subject'];
				$body    = $global_email_settings['qc_mail_content'];
			}

			// Reset to default if subject and/or body empty.
			if ( empty( $subject ) ) {
				$subject = $this->get_default_email_settings( 'quiz_completion', 'subject' );
			}

			if ( empty( $body ) ) {
				$body = $this->get_default_email_settings( 'quiz_completion', 'body', );
			}

			return [
				'subject'    => $subject,
				'body'       => $body,
				'status'     => ( $is_instructor_emails_disabled ) ? 'disabled' : 'enabled',
				'default'    => [
					'subject' => $this->get_default_email_settings( 'quiz_completion', 'subject' ),
					'body'    => $this->get_default_email_settings( 'quiz_completion', 'body' ),
				],
				'shortcodes' => $this->get_email_shortcodes( 'quiz_completion' ),
			];
		}

		/**
		 * Get course purchase email settings
		 *
		 * @since 5.5.0
		 *
		 * @param int $user_id  User ID of the instructor.
		 * @return mixed
		 */
		public function get_course_purchase_email_settings( $user_id ) {
			// Check if global email settings enabled.
			$enable_course_email = ir_get_settings( 'wdm_enable_instructor_course_mail' );
			if ( ! $enable_course_email ) {
				return [
					'status' => 'disabled',
				];
			}

			// Check if instructor email settings disabled.
			$is_instructor_emails_disabled = get_user_meta( $user_id, 'ir_course_emails_disabled', 1 );

			$subject = '';
			$body    = '';

			// Get instructor email settings with fallback global settings.
			$instructor_email_settings = [
				'subject' => get_user_meta( $user_id, 'ir-course-purchase-email-sub', true ),
				'body'    => get_user_meta( $user_id, 'ir-course-purchase-email-body', true ),
			];
			$global_email_settings     = get_option( '_wdmir_email_settings', [] );

			if ( ! empty( $instructor_email_settings ) && ! empty( $instructor_email_settings['subject'] ) && ! empty( $instructor_email_settings['body'] ) ) {
				$subject = $instructor_email_settings['subject'];
				$body    = $instructor_email_settings['body'];
			} elseif (
				! empty( $global_email_settings )
				&& array_key_exists( 'cp_subject', $global_email_settings )
				&& ! empty( $global_email_settings['cp_subject'] )
			) {
				$subject = $global_email_settings['cp_subject'];
				$body    = $global_email_settings['cp_mail_content'];
			}

			// Reset to default if subject and/or body empty.
			if ( empty( $subject ) ) {
				$subject = $this->get_default_email_settings( 'course_purchase', 'subject' );
			}

			if ( empty( $body ) ) {
				$body = $this->get_default_email_settings( 'course_purchase', 'body', );
			}

			return [
				'subject'    => $subject,
				'body'       => $body,
				'status'     => ( $is_instructor_emails_disabled ) ? 'disabled' : 'enabled',
				'default'    => [
					'subject' => $this->get_default_email_settings( 'course_purchase', 'subject' ),
					'body'    => $this->get_default_email_settings( 'course_purchase', 'body' ),
				],
				'shortcodes' => $this->get_email_shortcodes( 'course_purchase' ),
			];
		}

		/**
		 * Get default email settings
		 *
		 * @since 5.5.0
		 *
		 * @param string $email_type    The type of email setting.
		 * @param string $email_content The email content part. One of subject or body.
		 * @param string $user_role     Role of the user to fetch settings for. One of admin or instructor.
		 *
		 * @return mixed
		 */
		public function get_default_email_settings( $email_type, $email_content, $user_role = '' ) {
			$content = '';

			if ( empty( $email_type ) || empty( $email_content ) ) {
				return '';
			}

			switch ( $email_type ) {
				case 'quiz_completion':
					if ( 'subject' === $email_content ) {
						$content = IR_DEFAULT_QUIZ_COMP_EMAIL_SUB;
					}
					if ( 'body' === $email_content ) {
						$content = IR_DEFAULT_QUIZ_COMP_EMAIL_BODY;
					}
					break;

				case 'course_purchase':
					if ( 'subject' === $email_content ) {
						$content = IR_DEFAULT_COURSE_PURCHASE_EMAIL_SUB;
					}
					if ( 'body' === $email_content ) {
						$content = IR_DEFAULT_COURSE_PURCHASE_EMAIL_BODY;
					}
					break;

				case 'course_review':
					if ( 'admin' === $user_role ) {
						if ( 'subject' === $email_content ) {
							$content = IR_DEFAULT_COURSE_REVIEW_ADMIN_EMAIL_SUB;
						}
						if ( 'body' === $email_content ) {
							$content = IR_DEFAULT_COURSE_REVIEW_ADMIN_EMAIL_BODY;
						}
					} elseif ( 'instructor' === $user_role ) {
						if ( 'subject' === $email_content ) {
							$content = IR_DEFAULT_COURSE_REVIEW_INST_EMAIL_SUB;
						}
						if ( 'body' === $email_content ) {
							$content = IR_DEFAULT_COURSE_REVIEW_INST_EMAIL_BODY;
						}
					}
					break;

				case 'product_review':
					if ( 'admin' === $user_role ) {
						if ( 'subject' === $email_content ) {
							$content = IR_DEFAULT_PRODUCT_REVIEW_ADMIN_EMAIL_SUB;
						}
						if ( 'body' === $email_content ) {
							$content = IR_DEFAULT_PRODUCT_REVIEW_ADMIN_EMAIL_BODY;
						}
					} elseif ( 'instructor' === $user_role ) {
						if ( 'subject' === $email_content ) {
							$content = IR_DEFAULT_PRODUCT_REVIEW_INST_EMAIL_SUB;
						}
						if ( 'body' === $email_content ) {
							$content = IR_DEFAULT_PRODUCT_REVIEW_INST_EMAIL_BODY;
						}
					}
					break;
			}

			return $content;
		}

		/**
		 * Get course review email settings.
		 *
		 * @since 5.5.0
		 *
		 * @return mixed
		 */
		public function get_course_review_email_settings() {
			// Check if global email settings enabled.
			if ( ! WDMIR_REVIEW_COURSE ) {
				return [
					'status' => 'disabled',
				];
			}

			// Get course review email settings.
			$global_email_settings = get_option( '_wdmir_email_settings' );

			$email_settings = [
				// Admin settings.
				'admin'      => [
					'subject'    => $global_email_settings['cra_subject'] ?? '',
					'body'       => $global_email_settings['cra_mail_content'] ?? '',
					'emails'     => $global_email_settings['cra_emails'] ?? '',
					'default'    => [
						'subject' => $this->get_default_email_settings( 'course_review', 'subject', 'admin' ),
						'body'    => $this->get_default_email_settings( 'course_review', 'body', 'admin' ),
					],
					'shortcodes' => $this->get_email_shortcodes( 'course_review', 'admin' ),
				],
				// Instructor settings.
				'instructor' => [
					'subject'    => $global_email_settings['cri_subject'] ?? '',
					'body'       => $global_email_settings['cri_mail_content'] ?? '',
					'default'    => [
						'subject' => $this->get_default_email_settings( 'course_review', 'subject', 'instructor' ),
						'body'    => $this->get_default_email_settings( 'course_review', 'body', 'instructor' ),
					],
					'shortcodes' => $this->get_email_shortcodes( 'course_review', 'instructor' ),
				],
				'status'     => 'enabled',
			];

			// Reset to default if subject and/or body empty.
			if ( empty( $email_settings['admin']['subject'] ) ) {
				$email_settings['admin']['subject'] = $this->get_default_email_settings( 'course_review', 'subject', 'admin' );
			}
			if ( empty( $email_settings['admin']['body'] ) ) {
				$email_settings['admin']['body'] = $this->get_default_email_settings( 'course_review', 'body', 'admin' );
			}
			if ( empty( $email_settings['instructor']['subject'] ) ) {
				$email_settings['instructor']['subject'] = $this->get_default_email_settings( 'course_review', 'subject', 'instructor' );
			}
			if ( empty( $email_settings['instructor']['body'] ) ) {
				$email_settings['instructor']['body'] = $this->get_default_email_settings( 'course_review', 'body', 'instructor' );
			}

			return $email_settings;
		}

		/**
		 * Get product review email settings.
		 *
		 * @since 5.5.0
		 *
		 * @return mixed
		 */
		public function get_product_review_email_settings() {
			// Check if global email settings enabled.
			if ( ! WDMIR_REVIEW_PRODUCT ) {
				return [
					'status' => 'disabled',
				];
			}

			// Get product review email settings.
			$global_email_settings = get_option( '_wdmir_email_settings' );

			$email_settings = [
				// Admin settings.
				'admin'      => [
					'subject'    => $global_email_settings['pra_subject'] ?? '',
					'body'       => $global_email_settings['pra_mail_content'] ?? '',
					'emails'     => $global_email_settings['pra_emails'] ?? '',
					'default'    => [
						'subject' => $this->get_default_email_settings( 'product_review', 'subject', 'admin' ),
						'body'    => $this->get_default_email_settings( 'product_review', 'body', 'admin' ),
					],
					'shortcodes' => $this->get_email_shortcodes( 'product_review' ),
				],
				// Instructor settings.
				'instructor' => [
					'subject'    => $global_email_settings['pri_subject'] ?? '',
					'body'       => $global_email_settings['pri_mail_content'] ?? '',
					'default'    => [
						'subject' => $this->get_default_email_settings( 'product_review', 'subject', 'instructor' ),
						'body'    => $this->get_default_email_settings( 'product_review', 'body', 'instructor' ),
					],
					'shortcodes' => $this->get_email_shortcodes( 'product_review' ),
				],
				'status'     => 'enabled',
			];

			// Reset to default if subject and/or body empty.
			if ( empty( $email_settings['admin']['subject'] ) ) {
				$email_settings['admin']['subject'] = $this->get_default_email_settings( 'product_review', 'subject', 'admin' );
			}
			if ( empty( $email_settings['admin']['body'] ) ) {
				$email_settings['admin']['body'] = $this->get_default_email_settings( 'product_review', 'body', 'admin' );
			}
			if ( empty( $email_settings['instructor']['subject'] ) ) {
				$email_settings['instructor']['subject'] = $this->get_default_email_settings( 'product_review', 'subject', 'instructor' );
			}
			if ( empty( $email_settings['instructor']['body'] ) ) {
				$email_settings['instructor']['body'] = $this->get_default_email_settings( 'product_review', 'body', 'instructor' );
			}

			return $email_settings;
		}

		/**
		 * Update email settings permissions check
		 *
		 * @since 5.5.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_email_settings_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Update email settings data
		 *
		 * @since 5.5.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_email_settings( $request ) {
			$data            = [];
			$current_user_id = get_current_user_id();

			$form_data = $request->get_body_params();

			// If empty get all params.
			if ( empty( $form_data ) ) {
				$form_data = $request->get_params();
			}

			// Check if user is admin or instructor.
			if ( user_can( $current_user_id, 'manage_options' ) ) {
				// Update global quiz emails status.
				if ( array_key_exists( 'disable_quiz_completion_emails', $form_data ) ) {
					if ( ir_set_settings( 'instructor_mail', ! intval( $form_data['disable_quiz_completion_emails'] ) ) ) {
						$data['disable_quiz_completion_emails'] = $form_data['disable_quiz_completion_emails'];
					}
				}

				// Update global course purchase emails status.
				if ( array_key_exists( 'disable_course_completion_emails', $form_data ) ) {
					if ( ir_set_settings( 'wdm_enable_instructor_course_mail', ! intval( $form_data['disable_course_completion_emails'] ) ) ) {
						$data['disable_course_completion_emails'] = $form_data['disable_course_completion_emails'];
					}
				}

				// Update global quiz completion email subject.
				if ( array_key_exists( 'quiz_completion_subject', $form_data ) ) {
					if ( ir_set_email_settings( 'qc_subject', $form_data['quiz_completion_subject'] ) ) {
						$data['quiz_completion_subject'] = $form_data['quiz_completion_subject'];
					}
				}

				// Update global quiz completion email body.
				if ( array_key_exists( 'quiz_completion_body', $form_data ) ) {
					if ( ir_set_email_settings( 'qc_mail_content', $form_data['quiz_completion_body'] ) ) {
						$data['quiz_completion_body'] = $form_data['quiz_completion_body'];
					}
				}

				// Update global course purchase email subject.
				if ( array_key_exists( 'course_purchase_subject', $form_data ) ) {
					if ( ir_set_email_settings( 'cp_subject', $form_data['course_purchase_subject'] ) ) {
						$data['course_purchase_subject'] = $form_data['course_purchase_subject'];
					}
				}

				// Update global course purchase email body.
				if ( array_key_exists( 'course_purchase_body', $form_data ) ) {
					if ( ir_set_email_settings( 'cp_mail_content', $form_data['course_purchase_body'] ) ) {
						$data['course_purchase_body'] = $form_data['course_purchase_body'];
					}
				}

				// Update course review setting status.
				if ( array_key_exists( 'disable_course_review', $form_data ) ) {
					if ( ir_set_settings( 'review_course', ! intval( $form_data['disable_course_review'] ) ) ) {
						$data['disable_course_review'] = $form_data['disable_course_review'];
					}
				}

				// Update admin course review email list.
				if ( array_key_exists( 'course_review_emails', $form_data ) ) {
					$email_list = array_filter(
						array_map(
							function ( $email ) {
								return filter_var( trim( $email ), FILTER_VALIDATE_EMAIL );
							},
							explode( ',', $form_data['course_review_emails'] )
						)
					);

					if ( ir_set_email_settings( 'cra_emails', implode( ',', $email_list ) ) ) {
						$data['course_review_emails'] = implode( ',', $email_list );
					}
				}

				// Update admin course review email subject.
				if ( array_key_exists( 'admin_course_review_subject', $form_data ) ) {
					if ( ir_set_email_settings( 'cra_subject', $form_data['admin_course_review_subject'] ) ) {
						$data['admin_course_review_subject'] = $form_data['admin_course_review_subject'];
					}
				}

				// Update admin course review email body.
				if ( array_key_exists( 'admin_course_review_body', $form_data ) ) {
					if ( ir_set_email_settings( 'cra_mail_content', $form_data['admin_course_review_body'] ) ) {
						$data['admin_course_review_body'] = $form_data['admin_course_review_body'];
					}
				}

				// Update instructor course review email subject.
				if ( array_key_exists( 'inst_course_review_subject', $form_data ) ) {
					if ( ir_set_email_settings( 'cri_subject', $form_data['inst_course_review_subject'] ) ) {
						$data['inst_course_review_subject'] = $form_data['inst_course_review_subject'];
					}
				}

				// Update instructor course review email body.
				if ( array_key_exists( 'inst_course_review_body', $form_data ) ) {
					if ( ir_set_email_settings( 'cri_mail_content', $form_data['inst_course_review_body'] ) ) {
						$data['inst_course_review_body'] = $form_data['inst_course_review_body'];
					}
				}

				// Update product review setting status.
				if ( array_key_exists( 'disable_product_review', $form_data ) ) {
					if ( ir_set_settings( 'review_product', ! intval( $form_data['disable_product_review'] ) ) ) {
						$data['disable_product_review'] = $form_data['disable_product_review'];
					}
				}

				// Update admin product review email list.
				if ( array_key_exists( 'product_review_emails', $form_data ) ) {
					$email_list = array_filter(
						filter_var(
							explode( ',', $form_data['product_review_emails'] ),
							FILTER_VALIDATE_EMAIL,
							FILTER_REQUIRE_ARRAY
						)
					);
					if ( ir_set_email_settings( 'pra_emails', implode( ',', $email_list ) ) ) {
						$data['product_review_emails'] = implode( ',', $email_list );
					}
				}

				// Update admin product review email subject.
				if ( array_key_exists( 'admin_product_review_subject', $form_data ) ) {
					if ( ir_set_email_settings( 'pra_subject', $form_data['admin_product_review_subject'] ) ) {
						$data['admin_product_review_subject'] = $form_data['admin_product_review_subject'];
					}
				}

				// Update admin product review email body.
				if ( array_key_exists( 'admin_product_review_body', $form_data ) ) {
					if ( ir_set_email_settings( 'pra_mail_content', $form_data['admin_product_review_body'] ) ) {
						$data['admin_product_review_body'] = $form_data['admin_product_review_body'];
					}
				}

				// Update instructor product review email subject.
				if ( array_key_exists( 'inst_product_review_subject', $form_data ) ) {
					if ( ir_set_email_settings( 'pri_subject', $form_data['inst_product_review_subject'] ) ) {
						$data['inst_product_review_subject'] = $form_data['inst_product_review_subject'];
					}
				}

				// Update instructor product review email body.
				if ( array_key_exists( 'inst_product_review_body', $form_data ) ) {
					if ( ir_set_email_settings( 'pri_mail_content', $form_data['inst_product_review_body'] ) ) {
						$data['inst_product_review_body'] = $form_data['inst_product_review_body'];
					}
				}
			} else {
				// Update instructor quiz emails toggle.
				if ( array_key_exists( 'disable_quiz_completion_emails', $form_data ) ) {
					if ( update_user_meta( $current_user_id, 'ir_quiz_emails_disabled', intval( $form_data['disable_quiz_completion_emails'] ) ) ) {
						$data['disable_quiz_completion_emails'] = $form_data['disable_quiz_completion_emails'];
					}
				}

				// Update instructor course purchase emails toggle.
				if ( array_key_exists( 'disable_course_completion_emails', $form_data ) ) {
					if ( update_user_meta( $current_user_id, 'ir_course_emails_disabled', intval( $form_data['disable_course_completion_emails'] ) ) ) {
						$data['disable_course_completion_emails'] = $form_data['disable_course_completion_emails'];
					}
				}

				// Update instructor quiz completion email content.
				if ( array_key_exists( 'quiz_completion_subject', $form_data ) || array_key_exists( 'quiz_completion_body', $form_data ) ) {
					// Get quiz email settings.
					$instructor_quiz_email_settings = get_user_meta( $current_user_id, 'instructor_email_template', true );

					// Update instructor quiz completion email subject.
					if ( array_key_exists( 'quiz_completion_subject', $form_data ) ) {
						$instructor_quiz_email_settings['mail_sub'] = $form_data['quiz_completion_subject'];
					}

					// Update instructor quiz completion email body.
					if ( array_key_exists( 'quiz_completion_body', $form_data ) ) {
						$instructor_quiz_email_settings['mail_content'] = $form_data['quiz_completion_body'];
					}

					// Update settings.
					if ( update_user_meta( $current_user_id, 'instructor_email_template', $instructor_quiz_email_settings ) ) {
						$data['quiz_completion_subject'] = $instructor_quiz_email_settings['mail_sub'];
						$data['quiz_completion_body']    = $instructor_quiz_email_settings['mail_content'];
					}
				}

				// Update instructor course purchase email subject.
				if ( array_key_exists( 'course_purchase_subject', $form_data ) ) {
					if ( update_user_meta( $current_user_id, 'ir-course-purchase-email-sub', $form_data['course_purchase_subject'] ) ) {
						$data['course_purchase_subject'] = $form_data['course_purchase_subject'];
					}
				}

				// Update instructor course purchase email body.
				if ( array_key_exists( 'course_purchase_body', $form_data ) ) {
					if ( update_user_meta( $current_user_id, 'ir-course-purchase-email-body', $form_data['course_purchase_body'] ) ) {
						$data['course_purchase_body'] = $form_data['course_purchase_body'];
					}
				}
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get list of email shortcode placeholders
		 *
		 * @since 5.5.0
		 *
		 * @param string $email_type    The type of email setting.
		 * @param string $user_role     Role of the user to fetch shortcodes for. One of admin or instructor.
		 *
		 * @return mixed
		 */
		public function get_email_shortcodes( $email_type, $user_role = '' ) {
			$shortcodes = [];

			if ( empty( $email_type ) ) {
				return '';
			}

			switch ( $email_type ) {
				case 'quiz_completion':
					$shortcodes = [
						[
							'label'       => __( 'Student User ID', 'wdm_instructor_role' ),
							'placeholder' => '$userid',
						],
						[
							'label'       => __( 'Student Username', 'wdm_instructor_role' ),
							'placeholder' => '$username',
						],
						[
							'label'       => __( 'Student email', 'wdm_instructor_role' ),
							'placeholder' => '$useremail',
						],
						[
							'label'       => sprintf(
								/* translators: Quiz Label */
								__( '%s Name', 'wdm_instructor_role' ),
								LearnDash_Custom_Label::get_label( 'quiz' )
							),
							'placeholder' => '$quizname',
						],
						[
							'label'       => __( 'Result in Percentage', 'wdm_instructor_role' ),
							'placeholder' => '$result',
						],
						[
							'label'       => __( 'Points scored', 'wdm_instructor_role' ),
							'placeholder' => '$points',
						],
					];
					break;

				case 'course_purchase':
					$shortcodes = [
						[
							'label'       => __( 'Site Title', 'wdm_instructor_role' ),
							'placeholder' => '[site_name]',
						],
						[
							'label'       => sprintf(
								/* translators: Course Label */
								__( '%s Name', 'wdm_instructor_role' ),
								LearnDash_Custom_Label::get_label( 'course' )
							),
							'placeholder' => '[course_name]',
						],
						[
							'label'       => __( 'Instructor Name', 'wdm_instructor_role' ),
							'placeholder' => '[instructor_name]',
						],
						[
							'label'       => __( 'Customer Name', 'wdm_instructor_role' ),
							'placeholder' => '[customer_name]',
						],
					];
					break;

				case 'course_review':
					if ( 'admin' === $user_role ) {
						$shortcodes = [
							[
								'label'       => __( 'Instructor Profile Link', 'wdm_instructor_role' ),
								'placeholder' => '[ins_profile_link]',
							],
							[
								'label'       => __( 'Instructor First Name', 'wdm_instructor_role' ),
								'placeholder' => '[ins_first_name]',
							],
							[
								'label'       => __( 'Instructor Last Name', 'wdm_instructor_role' ),
								'placeholder' => '[ins_last_name]',
							],
							[
								'label'       => __( 'Instructor User ID', 'wdm_instructor_role' ),
								'placeholder' => '[ins_login]',
							],
							[
								'label'       => sprintf(
									/* translators: Course Label */
									__( '%s ID', 'wdm_instructor_role' ),
									LearnDash_Custom_Label::get_label( 'course' )
								),
								'placeholder' => '[course_id]',
							],
							[
								'label'       => sprintf(
									/* translators: Course Label */
									__( '%s Title', 'wdm_instructor_role' ),
									LearnDash_Custom_Label::get_label( 'course' )
								),
								'placeholder' => '[course_title]',
							],
							[
								'label'       => sprintf(
									/* translators: Course Label */
									__( 'Title of an edited %s content', 'wdm_instructor_role' ),
									LearnDash_Custom_Label::label_to_lower( 'course' )
								),
								'placeholder' => '[course_content_title]',
							],
							[
								'label'       => sprintf(
									/* translators: Course Label */
									__( 'Dashboard link of a edited %s content', 'wdm_instructor_role' ),
									LearnDash_Custom_Label::label_to_lower( 'course' )
								),
								'placeholder' => '[course_content_edit]',
							],
							[
								'label'       => sprintf(
									/* translators: Course Label */
									__( 'Updated date and time of the %s', 'wdm_instructor_role' ),
									LearnDash_Custom_Label::label_to_lower( 'course' )
								),
								'placeholder' => '[course_update_datetime]',
							],
							[
								'label'       => __( 'Updated date and time of a content', 'wdm_instructor_role' ),
								'placeholder' => '[content_update_datetime]',
							],
						];
					} elseif ( 'instructor' === $user_role ) {
						$shortcodes = [
							[
								'label'       => __( 'Instructor First Name', 'wdm_instructor_role' ),
								'placeholder' => '[ins_first_name]',
							],
							[
								'label'       => __( 'Instructor Last Name', 'wdm_instructor_role' ),
								'placeholder' => '[ins_last_name]',
							],
							[
								'label'       => __( 'Instructor User ID', 'wdm_instructor_role' ),
								'placeholder' => '[ins_login]',
							],
							[
								'label'       => sprintf(
									/* translators: Course Label */
									__( '%s ID', 'wdm_instructor_role' ),
									LearnDash_Custom_Label::get_label( 'course' )
								),
								'placeholder' => '[course_id]',
							],
							[
								'label'       => sprintf(
									/* translators: Course Label */
									__( '%s Title', 'wdm_instructor_role' ),
									LearnDash_Custom_Label::get_label( 'course' )
								),
								'placeholder' => '[course_title]',
							],
							[
								'label'       => sprintf(
									/* translators: Course Label */
									__( 'Title of an edited %s content', 'wdm_instructor_role' ),
									LearnDash_Custom_Label::label_to_lower( 'course' )
								),
								'placeholder' => '[course_content_title]',
							],
							[
								'label'       => sprintf(
									/* translators: Course Label */
									__( 'Permalink of the %s', 'wdm_instructor_role' ),
									LearnDash_Custom_Label::label_to_lower( 'course' )
								),
								'placeholder' => '[course_permalink]',
							],
							[
								'label'       => __( 'Content Permalink', 'wdm_instructor_role' ),
								'placeholder' => '[content_permalink]',
							],
							[
								'label'       => sprintf(
									/* translators: Course Label */
									__( 'Dashboard link of a edited %s content', 'wdm_instructor_role' ),
									LearnDash_Custom_Label::label_to_lower( 'course' )
								),
								'placeholder' => '[course_content_edit]',
							],
							[
								'label'       => __( 'Approved date and time of a content', 'wdm_instructor_role' ),
								'placeholder' => '[approved_datetime]',
							],
						];
					}
					break;

				case 'product_review':
					$shortcodes = [
						[
							'label'       => __( 'Instructor Profile Link', 'wdm_instructor_role' ),
							'placeholder' => '[ins_profile_link]',
						],
						[
							'label'       => __( 'Instructor First Name', 'wdm_instructor_role' ),
							'placeholder' => '[ins_first_name]',
						],
						[
							'label'       => __( 'Instructor Last Name', 'wdm_instructor_role' ),
							'placeholder' => '[ins_last_name]',
						],
						[
							'label'       => __( 'Instructor User ID', 'wdm_instructor_role' ),
							'placeholder' => '[ins_login]',
						],
						[
							'label'       => __( 'Product ID', 'wdm_instructor_role' ),
							'placeholder' => '[product_id]',
						],
						[
							'label'       => __( 'Product Title', 'wdm_instructor_role' ),
							'placeholder' => '[product_title]',
						],
						[
							'label'       => __( 'Product Permalink', 'wdm_instructor_role' ),
							'placeholder' => '[product_permalink]',
						],
						[
							'label'       => __( 'Updated date and time of the product', 'wdm_instructor_role' ),
							'placeholder' => '[product_update_datetime]',
						],
					];
					break;
			}

			return $shortcodes;
		}

		/**
		 * Fetch list of requested course users.
		 *
		 * @since 5.5.0
		 *
		 * @param int   $course_id  ID of the course.
		 * @param array $args       List of arguments.
		 * @param bool  $legacy     Whether to use legacy meta keys or not. Default false.
		 * @return mixed
		 */
		public function fetch_course_users_list( $course_id, $args, $legacy = false ) {
			global $wpdb;
			$results          = [];
			$where_queries    = '';
			$filter_queries   = '';
			$pre_query        = '';
			$post_query       = '';
			$pagination_query = ", CEIL(count(*) OVER() / {$args['per_page']}) as total_pages";

			// Setup main where query.
			if ( $legacy ) {
				$where_queries .= $wpdb->prepare(
					"( {$wpdb->usermeta}.meta_key = %s",
					'course_' . $course_id . '_access_from'
				);
			} else {
				$where_queries .= $wpdb->prepare(
					"( {$wpdb->usermeta}.meta_key = %s",
					'learndash_course_' . $course_id . '_enrolled_at'
				);
			}

			if ( ! empty( $args['groups'] ) ) {
				foreach ( $args['groups'] as $group_id ) {
					if ( $legacy ) {
						$where_queries .= sprintf( " OR ( {$wpdb->usermeta}.meta_key = 'group_%s_access_from' OR {$wpdb->usermeta}.meta_key = 'learndash_group_users_%s' )", $group_id, $group_id );
					} else {
						$where_queries .= sprintf( " OR ( {$wpdb->usermeta}.meta_key = 'learndash_group_%s_enrolled_at' OR {$wpdb->usermeta}.meta_key = 'learndash_group_users_%s' )", $group_id, $group_id );
					}
				}
			}
			$where_queries .= ' )';

			// Filter by progress status.
			if ( $args['progress_status'] !== 'all' ) {
				$filter_queries .= ' HAVING ';
				switch ( $args['progress_status'] ) {
					case 'in_progress':
						$filter_queries .= '( percentage > 0 && percentage < 100 )';
						break;
					case 'not_started':
						$filter_queries .= '( percentage = 0 )';
						break;
					case 'completed':
						$filter_queries .= '( percentage = 100 )';
						break;
				}
			}

			// Filter by progress percentage.
			if ( ! empty( $args['progress_percent'] ) && -1 != $args['progress_percent'] ) {
				if ( empty( $filter_queries ) ) {
					$filter_queries .= ' HAVING ';
				} else {
					$filter_queries .= ' AND ';
				}
				switch ( $args['progress_percent'] ) {
					case 20:
						$filter_queries .= '( percentage <= 20 )';
						break;
					case 40:
						$filter_queries .= '( percentage > 20 && percentage <= 40 )';
						break;
					case 60:
						$filter_queries .= '( percentage > 40 && percentage <= 60 )';
						break;
					case 80:
						$filter_queries .= '( percentage > 60 && percentage <= 80 )';
						break;
					case 100:
						$filter_queries .= '( percentage > 80 && percentage <= 100 )';
						break;
				}
			}

			// Filter by date.
			if ( ! empty( $args['start_timestamp'] ) && ! empty( $args['end_timestamp'] ) ) {
				if ( empty( $filter_queries ) ) {
					$filter_queries .= ' HAVING ';
				} else {
					$filter_queries .= ' AND ';
				}
				$filter_queries .= sprintf(
					'( enrollment_time >= %s AND enrollment_time <= %s )',
					$args['start_timestamp'],
					$args['end_timestamp']
				);
			}

			// Filter by students.
			if ( ! empty( $args['students'] ) ) {
				$where_queries .= " AND {$wpdb->usermeta}.user_id IN ( {$args['students']} )";
			}

			// Filter by students.
			if ( ! empty( $args['order_by'] ) ) {
				switch ( $args['order_by'] ) {
					case 'high_to_low':
						$orderby = 'percentage DESC, enrollment_time DESC';
						break;
					case 'low_to_high':
						$orderby = 'percentage ASC, enrollment_time DESC';
						break;
					case 'enrollment_time':
					default:
						$orderby = 'enrollment_time DESC';
						break;
				}
			}

			// Filter by search parameter. Will search for the word in ‘user_login‘ – Search by `user_login`, `user_nicename`, ‘user_email‘, ‘user_url‘ in users table & 'first_name' & 'last_name' meta in usermeta table.
			if ( ! empty( $args['search'] ) ) {
				$pre_query        = "SELECT course_reports.* {$pagination_query} FROM (";
				$pagination_query = '';

				$post_query = " ) as course_reports JOIN {$wpdb->users} ON course_reports.user_id = {$wpdb->users}.ID WHERE ( ( {$wpdb->users}.user_login LIKE \"%{$args['search']}%\" ) OR ( {$wpdb->users}.user_nicename LIKE \"%{$args['search']}%\" ) OR ( {$wpdb->users}.user_email LIKE \"%{$args['search']}%\" ) OR ( {$wpdb->users}.display_name LIKE \"%{$args['search']}%\" ) OR ( {$wpdb->users}.user_url LIKE \"%{$args['search']}%\" ) )";
			}

			$query = $wpdb->prepare(
				"{pre_query} SELECT {$wpdb->usermeta}.user_id, MAX( CAST( {$wpdb->usermeta}.meta_value AS UNSIGNED ) ) as enrollment_time, IFNULL( MAX( course_steps.steps_completed ), 0) / %d * 100 as percentage {pagination_query} FROM {$wpdb->usermeta} JOIN
				{$wpdb->users} ON {$wpdb->users}.ID = {$wpdb->usermeta}.user_id
				LEFT JOIN
				(
					SELECT activity.user_id, activity_meta.activity_meta_value as steps_completed
					FROM {$wpdb->prefix}learndash_user_activity as activity
					JOIN {$wpdb->prefix}learndash_user_activity_meta as activity_meta
					ON activity.activity_id = activity_meta.activity_id
					WHERE activity.course_id = %s AND activity.activity_type = 'course' AND activity_meta.activity_meta_key='steps_completed'
				) as course_steps
				ON {$wpdb->usermeta}.user_id = course_steps.user_id
				WHERE {where_queries}
				GROUP BY {$wpdb->usermeta}.user_id
				{filter_queries}
				{post_query}
				ORDER BY {order_by}
				LIMIT {offset}, {per_page}",
				$args['total_steps'],
				$course_id
			);

			$query = str_replace(
				[
					'{where_queries}',
					'{filter_queries}',
					'{order_by}',
					'{offset}',
					'{per_page}',
					'{pre_query}',
					'{post_query}',
					'{pagination_query}',
				],
				[
					$where_queries,
					$filter_queries,
					$orderby,
					$args['offset'],
					$args['per_page'],
					$pre_query,
					$post_query,
					$pagination_query,
				],
				$query
			);

			$results = $wpdb->get_results( $query );

			return $results;
		}

		/**
		 * Get learner reports chart permissions check
		 *
		 * @since 5.5.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_learner_reports_chart_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get learner reports chart data
		 *
		 * @since 5.5.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_learner_reports_chart( $request ) {
			$data = [];

			$learner = get_userdata( absint( $request['id'] ) );

			// Check if valid WP user.
			if ( empty( $learner ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid User ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Get student course progress.
			$data['progress_stats'] = $this->get_learner_progress_statistics_data( $learner );
			if ( is_wp_error( $data['progress_stats'] ) ) {
				return $data['progress_stats'];
			}
			$data['content_numbers'] = $this->get_learner_content_count( $learner );
			$data['profile_info']    = [
				'name'  => $learner->display_name,
				'email' => $learner->user_email,
				'image' => get_avatar_url( $learner->ID ),
			];
			$data['export_link']     = add_query_arg(
				[
					'action'   => 'export_learner_reports',
					'learner'  => $learner->ID,
					'ir_nonce' => wp_create_nonce( 'ir_export_learner_report' ),
				],
				admin_url( 'admin-ajax.php' )
			);

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get learner progress statistics data
		 *
		 * @since 5.5.0
		 *
		 * @param WP_User $learner  WP_User object.
		 * @return array
		 */
		public function get_learner_progress_statistics_data( $learner ) {
			$progress_stats = [
				'not_started' => 0,
				'in_progress' => 0,
				'completed'   => 0,
				'total'       => 0,
			];

			// Get list of all students who have access to the course enrolled either through group or directly in the course.
			$user_accessible = $this->check_if_user_data_accessible( $learner->ID );

			if ( ! $user_accessible ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this user.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Get user's progress data.
			$course_progress = $this->fetch_user_courses_progress( $learner->ID );

			if ( empty( $course_progress ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but no data found for this user.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			// Get total count of all the courses.
			$total_courses           = count( $course_progress );
			$progress_stats['total'] = $total_courses;

			$progress_stats['upto_20']             = 0;
			$progress_stats['upto_40']             = 0;
			$progress_stats['upto_60']             = 0;
			$progress_stats['upto_80']             = 0;
			$progress_stats['upto_100']            = 0;
			$progress_stats['completed']           = 0;
			$progress_stats['not_started']         = 0;
			$progress_stats['in_progress']         = 0;
			$progress_stats['avg_completion_rate'] = 0;
			$progress_stats['not_started_percent'] = 0;
			$progress_stats['in_progress_percent'] = 0;
			$progress_stats['completed_percent']   = 0;
			$total_completion                      = 0;

			if ( empty( $course_progress ) ) {
				return $progress_stats;
			}

			foreach ( $course_progress as $course_id => $progress ) {
				// Handle for deleted courses.
				if ( empty( get_post( $course_id ) ) ) {
					--$total_courses;
					--$progress_stats['total'];
					continue;
				}

				if ( ! isset( $progress['total'] ) || empty( $progress['total'] ) ) {
					++$progress_stats['upto_20'];
					++$progress_stats['not_started'];
					continue;
				}

				$completion = floatval( number_format( 100 * $progress['completed'] / $progress['total'], 2, '.', '' ) );

				$total_completion += $completion;

				if ( $completion > 80 && $completion < 100 ) {
					++$progress_stats['upto_100'];
					++$progress_stats['in_progress'];
				} elseif ( $completion > 60 && $completion <= 80 ) {
					++$progress_stats['upto_80'];
					++$progress_stats['in_progress'];
				} elseif ( $completion > 40 && $completion <= 60 ) {
					++$progress_stats['upto_60'];
					++$progress_stats['in_progress'];
				} elseif ( $completion > 20 && $completion <= 40 ) {
					++$progress_stats['upto_40'];
					++$progress_stats['in_progress'];
				} elseif ( $completion > 0 && $completion <= 20 ) {
					++$progress_stats['upto_20'];
					++$progress_stats['in_progress'];
				}

				if ( 100 === intval( $completion ) ) {
					++$progress_stats['upto_100'];
					++$progress_stats['completed'];
				}

				if ( 0 === intval( $completion ) ) {
					++$progress_stats['upto_20'];
					++$progress_stats['not_started'];
				}
			}
			$progress_stats['avg_completion_rate'] = floatval( number_format( $total_completion / $total_courses, 2, '.', '' ) );
			$progress_stats['not_started_percent'] = round( ( $progress_stats['not_started'] / $progress_stats['total'] ) * 100, 2 );
			$progress_stats['in_progress_percent'] = round( ( $progress_stats['in_progress'] / $progress_stats['total'] ) * 100, 2 );
			$progress_stats['completed_percent']   = round( 100 - ( $progress_stats['not_started_percent'] + $progress_stats['in_progress_percent'] ), 2 );

			return $progress_stats;
		}

		/**
		 * Check if user data is accessible.
		 *
		 * @since 5.5.0
		 *
		 * @param int $user_id  ID of the user.
		 * @return bool
		 */
		public function check_if_user_data_accessible( $user_id ) {
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}
			if ( wdm_is_instructor() ) {
				// Get course list.
				$unique_students_list = [];
				$current_user_id      = get_current_user_id();

				// Refresh shared courses.
				ir_refresh_shared_course_details( $current_user_id );

				// Final instructor course list.
				$course_list = ir_get_instructor_complete_course_list( $current_user_id );

				// No courses yet...
				if ( ! empty( $course_list ) && array_sum( $course_list ) > 0 ) {
					// Fetch the list of students in the courses.
					$all_students = [];
					foreach ( $course_list as $course_id ) {
						$students_list = ir_get_users_with_course_access( $course_id, [ 'direct', 'group' ] );

						if ( empty( $students_list ) ) {
							continue;
						}

						$all_students = array_merge( $all_students, $students_list );
					}

					$unique_students_list = array_unique( $all_students );
				}
				if ( empty( $unique_students_list ) ) {
					$unique_students_list = [ 0 ];
				}
				if ( ! in_array( $user_id, $unique_students_list ) ) {
					return false;
				}
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Get learner content stats.
		 *
		 * @since 5.5.0
		 *
		 * @param WP_User $learner  WP_User object.
		 * @return array
		 */
		public function get_learner_content_count( $learner ) {
			global $wpdb;
			$data                       = [];
			$data['quiz_attempts']      = 0;
			$data['last_active_on']     = 0;
			$data['last_activity']      = '';
			$data['last_activity_type'] = '';

			$quiz_table     = LDLMS_DB::get_table_name( 'quiz_statistic_ref', 'wpproquiz' );
			$activity_table = LDLMS_DB::get_table_name( 'user_activity', 'activity' );

			$quiz_query = $wpdb->prepare( "SELECT COUNT(*) FROM {$quiz_table} WHERE user_id = %d", $learner->ID );

			$data['quiz_attempts'] = $wpdb->get_var( $quiz_query );

			$activity_query = $wpdb->prepare( "SELECT activity_id, user_id, post_id, course_id, activity_updated, activity_started, activity_status, activity_type, activity_completed FROM {$activity_table} activity WHERE activity.activity_type IN ('course','access','lesson','topic','quiz') AND activity.user_id = %d ORDER BY GREATEST( activity_started, activity_updated, activity_completed ) DESC, activity_id DESC LIMIT 1", $learner->ID );

			$results = $wpdb->get_row( $activity_query );

			if ( empty( $results ) ) {
				return $data;
			}

			$data['last_active_on']     = date_i18n( 'M d, Y g:i a', max( $results->activity_started, $results->activity_completed, $results->activity_updated ) );
			$data['last_activity']      = get_the_title( $results->post_id );
			$data['last_activity_type'] = 'access' === $results->activity_type ? 'course' : $results->activity_type;

			return $data;
		}

		/**
		 * Get learner course reports data permissions check
		 *
		 * @since 5.5.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_learner_reports_courses_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get learner course reports data
		 *
		 * @since 5.5.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_learner_reports_courses( $request ) {
			$data            = [];
			$current_user_id = get_current_user_id();

			$learner = get_userdata( absint( $request['id'] ) );

			// Check if valid WP user.
			if ( empty( $learner ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid User ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			if ( ! $this->check_if_user_data_accessible( $learner->ID ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this user.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			$parameters = shortcode_atts(
				[
					'page'             => 1,
					'per_page'         => 10,
					'offset'           => 0,
					'order'            => 'DESC',
					'search'           => '',
					'order_by'         => 'latest', // latest, high_to_low, low_to_high.
					'progress_status'  => 'all',        // Possible values '', not_started, in_progress, completed.
					'progress_percent' => -1,       // Possible values -1, 100, 80, 60, 40, 20.
				],
				$request->get_params()
			);

			$args = [
				'post_type'      => learndash_get_post_type_slug( 'course' ),
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			];

			// For instructor user.
			if ( wdm_is_instructor( $current_user_id ) ) {
				$instructor_courses = ir_get_instructor_complete_course_list( $current_user_id, true );
				if ( empty( $instructor_courses ) ) {
					$instructor_courses = [ 0 ];
				}
				$args['post__in'] = $instructor_courses;
			}

			// Search courses.
			if ( isset( $parameters['search'] ) && ! empty( $parameters['search'] ) ) {
				$args['s'] = trim( $parameters['search'] );
			}

			$instructor_courses = get_posts( $args );

			// Get user's progress data.
			$progress_data = $this->fetch_user_courses_progress( $learner->ID );

			maybe_unserialize( get_user_meta( $learner->ID, '_sfwd-course_progress', true ) );

			if ( empty( $progress_data ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry no data found for this user.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			foreach ( $instructor_courses as $course ) {
				if ( ! array_key_exists( $course->ID, $progress_data ) ) {
					continue;
				}
				$enrollment_info                                 = $this->get_user_course_enrollment_info( $learner->ID, $course->ID, true );
				$progress_data[ $course->ID ]['enrollment_date'] = ! empty( $enrollment_info ) ? $enrollment_info['timestamp'] : '';
				$progress_data[ $course->ID ]['percentage']      = 0;
				if ( ! isset( $progress_data[ $course->ID ]['total'] ) || empty( $progress_data[ $course->ID ]['total'] ) ) {
					continue;
				}
				$progress_data[ $course->ID ]['percentage'] = floatval( number_format( 100 * $progress_data[ $course->ID ]['completed'] / $progress_data[ $course->ID ]['total'], 2, '.', '' ) );
			}

			// Remove results which don't satisfy search parameter.
			$progress_data = array_filter(
				$progress_data,
				function ( $course ) {
					return array_key_exists( 'enrollment_date', $course );
				}
			);
			// Sort courses as per sorting parameter.
			$progress_data = $this->sort_user_courses( $progress_data, $parameters );

			// Filter courses as per filtering parameter(s) i.e., progress_status, progress_percent.
			$progress_data = $this->filter_user_courses( $progress_data, $parameters );

			$data['total_pages'] = ceil( count( $progress_data ) / $parameters['per_page'] );

			// Paginate courses.
			$progress_data = $this->paginate_user_courses( $progress_data, $parameters );

			if ( empty( $progress_data ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry no data found for this user.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			foreach ( $progress_data as $course_id => $progress ) {
				$course_completed_meta       = get_user_meta( $learner->ID, 'course_completed_' . $course_id, true );
				$course_completed_date       = date_i18n( 'M d, Y g:i a', $course_completed_meta );
				$data['list_learner_data'][] = [
					'course'                => get_the_title( $course_id ),
					'image'                 => get_the_post_thumbnail_url( $course_id ),
					'enrollment_date'       => date_i18n( 'M d, Y g:i a', $progress['enrollment_date'] ),
					'steps_count'           => $this->get_course_content_count( $course_id ),
					'completed_on'          => $course_completed_date,
					'completion_percentage' => $progress['percentage'],
					'completion_status'     => $progress['status'],
				];
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Sort user courses
		 *
		 * @since 5.5.0
		 *
		 * @param array $progress_data  Data to sort.
		 * @param array $parameters     Sort parameter.
		 * @return array                Sorted data.
		 */
		public function sort_user_courses( $progress_data, $parameters ) {
			uasort(
				$progress_data,
				function ( $course_1, $course_2 ) use ( $parameters ) {
					if ( isset( $parameters['order_by'] ) && ! empty( $parameters['order_by'] ) ) {
						switch ( $parameters['order_by'] ) {
							case 'high_to_low':
								return $course_2['percentage'] <=> $course_1['percentage'];
							break;
							case 'low_to_high':
								return $course_1['percentage'] <=> $course_2['percentage'];
							break;
							case 'latest':
							default:
								return $course_2['enrollment_date'] <=> $course_1['enrollment_date'];
							break;
						}
					}
					return 0;
				}
			);
			return $progress_data;
		}

		/**
		 * Filter user courses
		 *
		 * @since 5.5.0
		 *
		 * @param array $progress_data  Progress data to filter.
		 * @param array $parameters     Filter parameters.
		 * @return array                Filtered data.
		 */
		public function filter_user_courses( $progress_data, $parameters ) {
			// Filter by status.
			$progress_data = array_filter(
				$progress_data,
				function ( $course ) use ( $parameters ) {
					if ( isset( $parameters['progress_status'] ) && 'all' !== $parameters['progress_status'] ) {
						$status = 'not_started';
						if ( 0 < $course['percentage'] && 100 > $course['percentage'] ) {
							$status = 'in_progress';
						} elseif ( 100 === intval( $course['percentage'] ) ) {
							$status = 'completed';
						}
						return $status === $parameters['progress_status'];
					}
					return true;
				}
			);
			// Filter by completion percentage.
			$progress_data = array_filter(
				$progress_data,
				function ( $course ) use ( $parameters ) {
					if ( isset( $parameters['progress_percent'] ) && -1 != $parameters['progress_percent'] ) {
						$progress = 20;
						if ( $course['percentage'] > 80 && $course['percentage'] <= 100 ) {
							$progress = 100;
						} elseif ( $course['percentage'] > 60 && $course['percentage'] <= 80 ) {
							$progress = 80;
						} elseif ( $course['percentage'] > 40 && $course['percentage'] <= 60 ) {
							$progress = 60;
						} elseif ( $course['percentage'] > 20 && $course['percentage'] <= 40 ) {
							$progress = 40;
						} elseif ( $course['percentage'] >= 0 && $course['percentage'] <= 20 ) {
							$progress = 20;
						}
						return $progress === (int) $parameters['progress_percent'];
					}
					return true;
				}
			);
			return $progress_data;
		}

		/**
		 * Paginate user course list.
		 *
		 * @since 5.5.0
		 *
		 * @param array $progress_data  Data to be paginated.
		 * @param array $parameters     Pagination parameters.
		 * @return array                Paginated data.
		 */
		public function paginate_user_courses( $progress_data, $parameters ) {
			return array_slice( $progress_data, ( $parameters['page'] - 1 ) * $parameters['per_page'], $parameters['per_page'], true );
		}

		/**
		 * Fetch user progress for all enrolled courses.
		 *
		 * @since 5.5.0
		 *
		 * @param int $user_id  ID of the User.
		 * @return array
		 */
		public function fetch_user_courses_progress( $user_id ) {
			// Get user course progress.
			$course_progress = get_user_meta( $user_id, '_sfwd-course_progress', true );

			// Get user registered courses.
			$course_progress = ! empty( $course_progress ) ? $course_progress : [];

			$registered_courses = ld_get_mycourses( $user_id ); // cspell:disable-line .
			$registered_courses = ! empty( $registered_courses ) ? $registered_courses : [];

			$all_course_list = array_merge( array_keys( $course_progress ), $registered_courses );
			foreach ( $all_course_list as $course_id ) {
				if ( ! array_key_exists( $course_id, $course_progress ) ) {
					$course_progress[ $course_id ] = [
						'status'    => 'not_started',
						'total'     => learndash_course_get_steps_count( $course_id ),
						'completed' => 0,
					];
				}
			}

			return $course_progress;
		}
	}
}
