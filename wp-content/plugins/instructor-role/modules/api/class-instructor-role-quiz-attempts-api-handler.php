<?php
/**
 * Quiz Attempts Rest API Handler Module
 *
 * @since 5.4.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

namespace InstructorRole\Modules\Api;

use WP_Rest_Server;
use WP_Query;
use WP_Error;
use WP_Post;
use LDLMS_DB;
use LDLMS_Factory_Post;
use InstructorRole\Modules\Classes\Instructor_Role_Single_Question_Data;
use InstructorRole\Modules\Classes\Instructor_Role_Free_Question_Data;
use InstructorRole\Modules\Classes\Instructor_Role_Sort_Question_Data;
use InstructorRole\Modules\Classes\Instructor_Role_Matrix_Sort_Question_Data;
use InstructorRole\Modules\Classes\Instructor_Role_Cloze_Question_Data;
use InstructorRole\Modules\Classes\Instructor_Role_Assessment_Question_Data;
use InstructorRole\Modules\Classes\Instructor_Role_Essay_Question_Data;
use LearnDash\Core\Utilities\Str;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Quiz_Attempts_Api_Handler' ) ) {
	/**
	 * Class Instructor Role Quiz Attempts Api Handler
	 */
	class Instructor_Role_Quiz_Attempts_Api_Handler extends Instructor_Role_Dashboard_Block_Api_Handler {
		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
		 *
		 * @since 5.4.0
		 */
		protected static $instance = null;

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 5.4.0
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
		 * @since 5.4.0
		 */
		public function register_custom_endpoints() {
			// List Quiz Attempts.
			register_rest_route(
				$this->namespace,
				'/quiz-attempts-list',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_quiz_attempts' ],
						'permission_callback' => [ $this, 'get_quiz_attempts_permissions_check' ],
						'args'                => [
							'group'          => [
								'description' => esc_html__( 'Fetch attempts for selected group.', 'wdm_instructor_role' ),
								'type'        => 'integer',
							],
							'course'         => [
								'description' => esc_html__( 'Fetch attempts for selected course.', 'wdm_instructor_role' ),
								'type'        => 'integer',
							],
							'quiz'           => [
								'description' => esc_html__( 'Fetch attempts for selected quiz.', 'wdm_instructor_role' ),
								'type'        => 'integer',
							],
							'learner'        => [
								'description' => esc_html__( 'Fetch attempts for selected learner.', 'wdm_instructor_role' ),
								'type'        => 'integer',
							],
							'start_date'     => [
								'description' => esc_html__( 'Timestamp for the attempt start date.', 'wdm_instructor_role' ),
								'type'        => 'string',
							],
							'end_date'       => [
								'description' => esc_html__( 'Timestamp for the attempt end date.', 'wdm_instructor_role' ),
								'type'        => 'string',
							],
							'duration'       => [
								'description' => esc_html__( 'String for the attempt duration.', 'wdm_instructor_role' ),
								'type'        => 'string',
								'default'     => '3 months',
							],
							'posts_per_page' => [
								'description' => esc_html__( 'Number of entries per page of the collection.', 'wdm_instructor_role' ),
								'type'        => 'integer',
								'default'     => 10,
							],
							'page'           => [
								'description' => esc_html__( 'Current page of the collection.', 'wdm_instructor_role' ),
								'type'        => 'integer',
								'default'     => 1,
							],
							'sort'           => [
								'description' => esc_html__( 'Sort results by selected parameter.', 'wdm_instructor_role' ),
								'type'        => 'string',
								'default'     => 'recent_desc',
							],
							'user_type'      => [
								'description' => esc_html__( 'Show all attempts or only registered attempts or only anonymous attempts', 'wdm_instructor_role' ),
								'type'        => 'string',
								'default'     => 'false',
							],
							'attempt_type'   => [
								'description' => esc_html__( 'show all attempts or only recent most attempt', 'wdm_instructor_role' ),
								'type'        => 'string',
								'default'     => 'all',
							],
						],
					],
				]
			);

			// Get Quiz Statistics Overview data.
			register_rest_route(
				$this->namespace,
				'/quiz-overview-data',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_quiz_overview_data' ],
						'permission_callback' => [ $this, 'get_quiz_overview_data_permissions_check' ],
					],
				]
			);

			// Get quiz attempt details.
			register_rest_route(
				$this->namespace,
				'/quiz-attempt-data',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_quiz_attempt_data' ],
						'permission_callback' => [ $this, 'get_quiz_attempt_data_permissions_check' ],
					],
				]
			);

			// Get course quizzes.
			register_rest_route(
				$this->namespace,
				'/sfwd-courses/(?P<id>[\d]+)/quizzes',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_course_quizzes' ],
						'permission_callback' => [ $this, 'get_course_quizzes_permissions_check' ],
					],
				]
			);

			// Fetch instructor groups.
			register_rest_route(
				$this->namespace,
				'/accessible-groups',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'fetch_groups' ],
						'permission_callback' => [ $this, 'fetch_groups_permissions_check' ],
					],
				],
			);

			// Fetch instructor quizzes.
			register_rest_route(
				$this->namespace,
				'/accessible-quizzes',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'fetch_quizzes' ],
						'permission_callback' => [ $this, 'fetch_quizzes_permissions_check' ],
					],
				],
			);

			// Fetch group courses.
			register_rest_route(
				$this->namespace,
				'/group/(?P<id>[\d]+)/courses',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'fetch_group_details' ],
						'permission_callback' => [ $this, 'fetch_group_details_permissions_check' ],
					],
				],
			);
		}

		/**
		 * Get Group Courses permissions check
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function fetch_group_details_permissions_check( $request ) {
			return $this->group_leader_request_permission_check( $request );
		}

		/**
		 * Get Group Courses API endpoint Handler.
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function fetch_group_details( $request ) {
			$data = [];

			$group = get_post( $request['id'] );

			// Check for valid group id.
			if ( empty( $group ) || ! $group instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			$data = $this->get_group_courses( $group );

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get Group Courses.
		 *
		 * @since 5.4.0
		 *
		 * @param WP_Post $group WP_Post instance.
		 */
		public function get_group_courses( $group ) {
			// Return Course ID array.
			$group_courses = learndash_group_enrolled_courses( $group->ID );
			$data          = [];
			if ( ! empty( $group_courses ) ) {
				foreach ( $group_courses as $course_id ) {
					// Added rendered to make it uniform as this format was used in other existing API endpoints.
					array_push(
						$data,
						[
							'id'    => $course_id,
							'title' => [
								'rendered' => get_the_title( $course_id ),
							],
						]
					);
				}
			}

			return $data;
		}

		/**
		 * Get Accessible Groups permissions check
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function fetch_groups_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get groups Accessible by instructors.
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function fetch_groups( $request ) {
			$data    = [];
			$user_id = get_current_user_id();

			// Checks if the current user is instructor.
			if ( wdm_is_instructor() ) {
				// Get groups where instructor is the author.
				$groups = get_posts(
					[
						'post_type'      => learndash_get_post_type_slug( 'group' ),
						'post_status'    => 'publish',
						'author'         => get_current_user_id(),
						'posts_per_page' => -1,
						'fields'         => 'ids',
					]
				);
				// Get groups where instructor is added as a group leader.
				$group_leader_access_to = learndash_get_administrators_group_ids( $user_id );

				$groups = array_unique( array_merge( $groups, $group_leader_access_to ) );
			} else {
				// Get all groups.
				$groups = get_posts(
					[
						'post_type'      => learndash_get_post_type_slug( 'group' ),
						'post_status'    => 'publish',
						'posts_per_page' => -1,
						'fields'         => 'ids',
					]
				);
			}
			// Added rendered to make it uniform as this format was used in other existing API endpoints.
			foreach ( $groups as $group_id ) {
				array_push(
					$data,
					[
						'id'    => $group_id,
						'title' => [
							'rendered' => get_the_title( $group_id ),
						],
					]
				);
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get Accessible Quizzes permissions check
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function fetch_quizzes_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get Quizzes Accessible by instructors.
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function fetch_quizzes( $request ) {
			$data    = [];
			$user_id = get_current_user_id();

			// Checks if the current user is instructor.
			if ( wdm_is_instructor() ) {
				$instructor_courses = ir_get_instructor_complete_course_list( $user_id, true, true );
				$instructor_quizzes = [];

				// Get instructor quiz list.
				foreach ( $instructor_courses as $course_id ) {
					$course_quiz = learndash_get_course_steps( $course_id, [ 'sfwd-quiz' ] );

					if ( ! empty( $course_quiz ) ) {
						$instructor_quizzes = array_merge( $instructor_quizzes, $course_quiz );
					}
				}

				// Fetch orphan quiz.
				$orphan_quiz = new WP_Query(
					[
						'post_type'      => learndash_get_post_type_slug( 'quiz' ),
						'posts_per_page' => -1,
						'post_status'    => 'publish',
						'author'         => $user_id,
						'fields'         => 'ids',
					]
				);

				if ( ! empty( $orphan_quiz->posts ) ) {
					$instructor_quizzes = array_unique( array_merge( $instructor_quizzes, $orphan_quiz->posts ) );
				}
			} else {
				// Get all quizzes.
				$instructor_quizzes = get_posts(
					[
						'post_type'      => learndash_get_post_type_slug( 'quiz' ),
						'post_status'    => 'publish',
						'posts_per_page' => -1,
						'fields'         => 'ids',
					]
				);
			}
			// Added rendered to make it uniform as this format was used in other existing API endpoints.
			foreach ( $instructor_quizzes as $quiz_id ) {
				array_push(
					$data,
					[
						'id'    => $quiz_id,
						'title' => [
							'rendered' => get_the_title( $quiz_id ),
						],
					]
				);
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get course quizzes permissions check
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_course_quizzes_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get course quizzes API endpoint handler
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_course_quizzes( $request ) {
			$data = [];

			$course = get_post( $request['id'] );
			// Check for valid course id.
			if ( empty( $course ) || ! $course instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			$ld_course_steps_object = LDLMS_Factory_Post::course_steps( intval( $course->ID ) );

			if ( $ld_course_steps_object ) {
				// Get steps by type.
				$steps = $ld_course_steps_object->get_steps( 't' );
				// Added rendered to make it uniform as this format was used in other existing API endpoints.
				$data = array_map(
					function ( $quiz_id ) {
						return [
							'id'    => $quiz_id,
							'title' => [
								'rendered' => get_the_title( $quiz_id ),
							],
						];
					},
					$steps['sfwd-quiz']
				);
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get assignment lessons permissions check
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_quiz_attempts_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get quiz attempts list API endpoint handler.
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_quiz_attempts( $request ) {
			$data = [];
			// Default parameters.
			$params = shortcode_atts(
				[
					'group'          => '',
					'course'         => '',
					'quiz'           => '',
					'learner'        => -1,
					'start_date'     => '',
					'end_date'       => '',
					'duration'       => '3 months',
					'posts_per_page' => 10,
					'page'           => 1,
					'sort'           => 'recent_desc',
					'user_type'      => 'all',
					'attempt_type'   => 'all',
				],
				$request->get_params()
			);

			// One of the parameters(group/course/learner/quiz) is required to be present.
			if ( ! $this->validate_required_fields( $params, 'group' ) && ! $this->validate_required_fields( $params, 'course' ) && ! $this->validate_required_fields( $params, 'quiz' ) && ! $this->validate_required_fields( $params, 'learner' ) ) {
				return new WP_Error( 'ir_rest_parameters_missing', esc_html__( 'You need to select at least one of the filters among group/course/quiz/learner.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			// Both of these parameters should either be absent or present.
			if ( $this->validate_required_fields( $params, 'start_date' ) !== $this->validate_required_fields( $params, 'end_date' ) ) {
				return new WP_Error( 'ir_rest_parameters_missing', esc_html__( 'You need to select both start date and end date for custom date range.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			// Checks for accessibility of the involved posts and returns accessible courses/learners etc.
			$query = $this->validate_input_data( $params );
			if ( is_wp_error( $query ) ) {
				return $query;
			}

			// Get timestamps for duration field and/or start_date/end date field.
			$dates = $this->get_duration_timestamps( $params );

			// Fetch relevant statistics and their data from DB.
			$statistics = $this->get_statistics( $params, $query, $dates );

			// Post processing of the data.
			$data = array_map(
				function ( $statistic ) {
					if ( 0 == $statistic['user_id'] ) {
						$statistic['learner'] = __( 'Anonymous', 'wdm_instructor_role' );
					} else {
						$statistic['learner'] = get_userdata( $statistic['user_id'] )->display_name;
					}
					$statistic['time']    = date_i18n( 'Y-m-d h:i A', $statistic['create_time'] );
					$statistic['attempt'] = $this->ordinal( (int) $statistic['attempt'] );
					$quiz_post_settings   = learndash_get_setting( $statistic['quiz_post_id'] );
					if ( ! is_array( $quiz_post_settings ) ) {
						$quiz_post_settings = [];
					}
					if ( ! isset( $quiz_post_settings['passingpercentage'] ) ) {
						$quiz_post_settings['passingpercentage'] = 0;
					}
					$passingpercentage = absint( $quiz_post_settings['passingpercentage'] );

					$pass                    = ( $statistic['percentage'] >= $passingpercentage ) ? 1 : 0;
					$statistic['status']     = $pass ? __( 'Pass', 'wdm_instructor_role' ) : __( 'Failed', 'wdm_instructor_role' );
					$statistic['pass']       = $pass;
					$statistic['export_csv'] = admin_url( 'admin-ajax.php' ) . '?action=export_quiz_statistics&_nonce=' . wp_create_nonce( 'ir_quiz_export' ) . '&ref_id=' . $statistic['statistic_ref_id'] . '&file_format=csv&quiz=' . $statistic['quiz_post_id'] . '&learner=' . $statistic['user_id'];
					return $statistic;
				},
				$statistics
			);

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * This method is used to check if the input key is passed in the parameters.
		 *
		 * @since 5.4.0
		 *
		 * @param array  $params Input params.
		 * @param string $key    key to check.
		 */
		public function validate_required_fields( $params, $key ) {
			// 0 or empty doesn't work for learner as anonymous learners have 0 user_id so special handling for this parameter is needed.
			if ( 'learner' === $key ) {
				return array_key_exists( $key, $params ) && -1 != $params[ $key ];
			}
			return array_key_exists( $key, $params ) && ( ! empty( $params[ $key ] ) && -1 != $params[ $key ] );
		}

		/**
		 * This method is used to check if the input parameters are valid and accessible.
		 *
		 * @since 5.4.0
		 *
		 * @param array $params Input params.
		 */
		public function validate_input_data( $params ) {
			$query = [
				'courses'  => [],
				'learners' => [],
				'quizzes'  => [],
			];
			// First check if group is selected.
			if ( $this->validate_required_fields( $params, 'group' ) ) {
				$group = get_post( $params['group'] );
				// Check if valid group ID.
				if ( empty( $group ) || ! $group instanceof WP_Post ) {
					return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
				}
				// Check if the current instructor has access to the group.
				if ( wdm_is_instructor() ) {
					$user_id                = get_current_user_id();
					$group_leader_access_to = learndash_get_administrators_group_ids( $user_id );
					if ( $user_id !== $group->post_author && ( empty( $group_leader_access_to ) || ! in_array( $group->ID, $group_leader_access_to ) ) ) {
						return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Sorry but you are not an administrator of the selected group.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
					}
				}

				$courses = learndash_group_enrolled_courses( $group->ID );
				$quizzes = [];
				// Now check if a specific course is also selected.
				if ( $this->validate_required_fields( $params, 'course' ) ) {
					$course = get_post( $params['course'] );

					// Check if valid course ID.
					if ( empty( $course ) || ! $course instanceof WP_Post ) {
						return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
					}
					// Check if the course is present in the selected group.
					if ( ! in_array( $course->ID, $courses ) ) {
						return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid request. Selected course is not part of the selected group.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
					}

					$courses = [ $course->ID ];
					// Now check if the quiz is also selected.
					if ( $this->validate_required_fields( $params, 'quiz' ) ) {
						$quiz = get_post( $params['quiz'] );

						// Check if valid quiz ID.
						if ( empty( $quiz ) || ! $quiz instanceof WP_Post ) {
							return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
						}

						$ld_course_steps_object = LDLMS_Factory_Post::course_steps( intval( $course->ID ) );

						if ( $ld_course_steps_object ) {
							$steps = $ld_course_steps_object->get_steps( 't' );

							// Check if quiz is present in the selected course.
							if ( ! in_array( $quiz->ID, $steps['sfwd-quiz'] ) ) {
								return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid request. Selected quiz is not part of the selected course.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
							}
						}
					}
				}
				// Check if directly quiz selected from group.
				if ( $this->validate_required_fields( $params, 'quiz' ) ) {
					// BUG: Check missing for whether quiz is part of the group.
					$quizzes = [ $params['quiz'] ];
				}

				$learners = learndash_get_groups_user_ids( $group->ID );

				// Check if a learner is selected with group.
				if ( $this->validate_required_fields( $params, 'learner' ) ) {
					// Check if learner is enrolled in the group.
					if ( ! in_array( $params['learner'], $learners ) ) {
						return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid request. Selected learner is not part of the selected group.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
					}
					if ( $this->validate_required_fields( $params, 'course' ) ) {
						// Check if learner is part of the course if course also selected.
						if ( ! sfwd_lms_has_access( $params['course'], $params['learner'] ) ) {
							return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid request. Selected learner is not enrolled in the selected course.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
						}
					}
					$learners = [ $params['learner'] ];
				}

				$query['courses']  = $courses;
				$query['learners'] = $learners;
				$query['quizzes']  = $quizzes;
				// Else check if course is selected.
			} elseif ( $this->validate_required_fields( $params, 'course' ) ) {
				$course = get_post( $params['course'] );

				// Check if valid course ID.
				if ( empty( $course ) || ! $course instanceof WP_Post ) {
					return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
				}
				$courses = [ $course->ID ];
				$quizzes = [];

				// Check if a quiz is also selected.
				if ( $this->validate_required_fields( $params, 'quiz' ) ) {
					$quiz = get_post( $params['quiz'] );

					// Check if valid quiz ID.
					if ( empty( $quiz ) || ! $quiz instanceof WP_Post ) {
						return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
					}

					$ld_course_steps_object = LDLMS_Factory_Post::course_steps( intval( $course->ID ) );

					if ( $ld_course_steps_object ) {
						$steps = $ld_course_steps_object->get_steps( 't' );

						// Check if selected quiz is part of the course.
						if ( ! in_array( $quiz->ID, $steps['sfwd-quiz'] ) ) {
							return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid request. Selected quiz is not part of the selected course.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
						}
					}

					$quizzes = [ $quiz->ID ];
				}

				$learners = learndash_get_users_for_course( $course->ID );
				if ( ! empty( $learners ) && ! is_array( $learners ) ) {
					$learners = $learners->get_results();
				}

				// Check if learner is selected.
				if ( $this->validate_required_fields( $params, 'learner' ) ) {
					// Check if learner has access to the course.
					if ( ! sfwd_lms_has_access( $course->ID, $params['learner'] ) ) {
						return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid request. Selected learner is not enrolled in the selected course.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
					}
					$learners = [ $params['learner'] ];
				}

				$query['courses']  = $courses;
				$query['learners'] = $learners;
				$query['quizzes']  = $quizzes;
				// Else check if quiz is selected.
			} elseif ( $this->validate_required_fields( $params, 'quiz' ) ) {
				$quiz     = get_post( $params['quiz'] );
				$learners = [];

				// Check if valid quiz ID.
				if ( empty( $quiz ) || ! $quiz instanceof WP_Post ) {
					return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
				}

				// Check if learner is selected.
				if ( $this->validate_required_fields( $params, 'learner' ) ) {
					$learners = [ $params['learner'] ];
				}

				$query['courses']  = [];
				$query['learners'] = $learners;
				$query['quizzes']  = [ $quiz->ID ];
				// Else check if learner is selected.
			} elseif ( $this->validate_required_fields( $params, 'learner' ) ) {
				$query['courses']  = [];
				$query['learners'] = [ $params['learner'] ];
				$query['quizzes']  = [];
			}
			return $query;
		}

		/**
		 * This method is used to get start and end date timestamps.
		 *
		 * @since 5.4.0
		 *
		 * @param array $params Input params.
		 */
		public function get_duration_timestamps( $params ) {
			$dates = [];
			// If start_date and end_date parameters are passed directly take the timestamp.
			if ( $this->validate_required_fields( $params, 'start_date' ) ) {
				$dates['start_date'] = $params['start_date'];
				$dates['end_date']   = $params['end_date'];
				return $dates;
			}
			// If duration field is passed get start date from string.
			$dates['start_date'] = strtotime( date_i18n( 'Y-m-d', strtotime( '-' . $params['duration'] ) ) );
			$dates['end_date']   = current_time( 'timestamp' );
			return $dates;
		}

		/**
		 * This method is used to get statistics from the database based on the input parameters, accessible content & duration.
		 *
		 * @since 5.4.0
		 *
		 * @param array $params Input params.
		 * @param array $query  Contains accessible posts/learners information.
		 * @param array $dates  Contains start and end date timestamp.
		 */
		public function get_statistics( $params, $query, $dates ) {
			global $wpdb;
			$statistics_ref_table = LDLMS_DB::get_table_name( 'quiz_statistic_ref', 'wpproquiz' );
			$statistic_table      = LDLMS_DB::get_table_name( 'quiz_statistic', 'wpproquiz' );
			$question_table       = LDLMS_DB::get_table_name( 'quiz_question', 'wpproquiz' );

			$where = '1=1';
			// Default sort by percentage ( Top Score First ).
			$orderby = 'percentage';
			// Check if sort by date.
			if ( Str::contains( $params['sort'], 'recent' ) ) {
				$orderby = 'qsr.statistic_ref_id';
			}
			$order = 'DESC';
			// Check if ascending order.
			if ( Str::contains( $params['sort'], 'asc' ) ) {
				$order = 'ASC';
			}
			// Set pagination values.
			$limit  = $params['posts_per_page'];
			$offset = ( (int) $params['page'] - 1 ) * (int) $limit;

			// Check if start_date and end_date are not all (for all preset).
			if ( 'all' !== $params['start_date'] && 'all' !== $params['end_date'] ) {
				$where .= ' AND qsr.create_time >= ' . $dates['start_date'];
				$where .= ' AND qsr.create_time <= ' . $dates['end_date'];
			}
			// Check for selected courses array.
			if ( ! empty( $query['courses'] ) ) {
				$where .= ' AND qsr.course_post_id IN (' . implode( ',', $query['courses'] ) . ')';
			}
			// Check for selected learners array.
			if ( ! empty( $query['learners'] ) ) {
				$where .= ' AND qsr.user_id IN (' . implode( ',', $query['learners'] ) . ')';
			}
			// Check for selected quizzes array.
			if ( ! empty( $query['quizzes'] ) ) {
				$where .= ' AND qsr.quiz_post_id IN (' . implode( ',', $query['quizzes'] ) . ')';
			}

			// Check if filter selected to only show the most recent attempts.
			if ( 'latest' === $params['attempt_type'] ) {
				$where .= ' AND qsr.reverse_attempts = 1';
			}

			// Check if anonymous only filter is selected.
			if ( 'anonymous' === $params['user_type'] ) {
				$where .= ' AND qsr.user_id = 0';
				// Check if registered only filter is selected.
			} elseif ( 'registered' === $params['user_type'] ) {
				$where .= ' AND qsr.user_id <> 0';
			}
			// Cannot use $wpdb->prepare as it escapes the percentage sign which we need to get percentage string output.
			$sql = sprintf( "SELECT qsr.user_id, qsr.create_time, qsr.quiz_post_id, posts.post_title AS quiz, qsr.attempts as attempt, SUM(qs.points) AS points, SUM(qq.points) AS total_points, SUM(qs.points) / SUM(qq.points) * 100 as percentage, CONCAT(ROUND(SUM(qs.points) * 100 / SUM(qq.points), 2),'%s') as score, qsr.statistic_ref_id, CEIL(count(*) OVER() / {$limit}) as total_pages, {$params['page']} as current_page,SUM(qs.question_time) AS question_time, SUM(qs.correct_count) AS correct_count, SUM(qs.incorrect_count) AS incorrect_count FROM ( SELECT *, ROW_NUMBER() OVER (PARTITION BY user_id, quiz_post_id, course_post_id ORDER BY create_time ASC) AS attempts, ROW_NUMBER() OVER (PARTITION BY user_id, quiz_post_id, course_post_id ORDER BY create_time DESC) AS reverse_attempts FROM {$statistics_ref_table}) AS qsr INNER JOIN {$statistic_table} qs ON qsr.statistic_ref_id = qs.statistic_ref_id INNER JOIN {$question_table} qq ON qq.id = qs.question_id INNER JOIN {$wpdb->posts} posts ON posts.ID = qsr.quiz_post_id WHERE {$where} GROUP BY qsr.statistic_ref_id ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d", '%', $limit, $offset );
			// Check if there's an SQL injection.
			if ( preg_match( '[update|delete|drop|alter]', strtolower( $sql ) ) === true ) {
				throw new \Exception( 'No cheating' );
			}

			/**
			 * Filter the data returned on fetch quiz statistics list.
			 *
			 * @since 5.4.0
			 *
			 * @param array                Response data returned.
			 * @param array  $params       Request parameters.
			 * @param array  $query        Processed parameters.
			 * @param array  $dates        Start and end date timestamps.
			 */
			return apply_filters( 'ir_filter_quiz_statistics_list', $wpdb->get_results( $sql, ARRAY_A ), $params, $query, $dates );
		}

		/**
		 * This method is used to add ordinal strings to number so for e.g., to change 1 to 1st, 2 to 2nd etc.
		 *
		 * @since 5.4.0
		 *
		 * @param integer $number Contains an integer number
		 */
		public function ordinal( $number ) {
			$ends = [ 'th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th' ];
			if ( ( ( $number % 100 ) >= 11 ) && ( ( $number % 100 ) <= 13 ) ) {
				return $number . 'th';
			} else {
				return $number . $ends[ $number % 10 ];
			}
		}

		/**
		 * Get quiz statistics summarized/overview level data permissions check
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_quiz_overview_data_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get quiz statistics summarized/overview level data API endpoint handler.
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_quiz_overview_data( $request ) {
			$data = [];

			// Default parameters.
			$params = shortcode_atts(
				[
					'quiz'       => '',
					'duration'   => '3 months',
					'start_date' => '',
					'end_date'   => '',
					'course'     => '', // optional.
					'group'      => '', // optional.
					'learner'    => '', // optional.
				],
				$request->get_params()
			);

			// Quiz is a required field.
			if ( ! $this->validate_required_fields( $params, 'quiz' ) ) {
				return new WP_Error( 'ir_rest_parameters_missing', esc_html__( 'You need to select a Quiz to view this data.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			$quiz = get_post( $params['quiz'] );

			// Check if valid quiz ID.
			if ( empty( $quiz ) || ! $quiz instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			// Both of these parameters should either be absent or present.
			if ( $this->validate_required_fields( $params, 'start_date' ) !== $this->validate_required_fields( $params, 'end_date' ) ) {
				return new WP_Error( 'ir_rest_parameters_missing', esc_html__( 'You need to select both start date and end date for custom date range.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			// Checks for accessibility of the involved posts and returns accessible courses/learners etc.
			$query = $this->validate_input_data( $params );
			if ( is_wp_error( $query ) ) {
				return $query;
			}

			// Get timestamps for duration field and/or start_date/end date field.
			$dates = $this->get_duration_timestamps( $params );

			// Get quiz overview data above the statistics table.
			$overview_data = $this->fetch_overview_data( $params, $query, $dates );

			// Process the obtained data into presentable form.
			$data = $this->process_overview_data( $overview_data, $params );

			// Return error if no data found.
			if ( empty( $data ) ) {
				return new WP_Error( 'ir_rest_no_data', esc_html__( 'No data found.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * This method is used to fetch quiz statistics summarized/overview data from the database.
		 *
		 * @since 5.4.0
		 *
		 * @param array $params Input params.
		 * @param array $query  Contains accessible posts/learners information.
		 * @param array $dates  Contains start and end date timestamp.
		 */
		public function fetch_overview_data( $params, $query, $dates ) {
			global $wpdb;
			$statistics_ref_table = LDLMS_DB::get_table_name( 'quiz_statistic_ref', 'wpproquiz' );
			$statistic_table      = LDLMS_DB::get_table_name( 'quiz_statistic', 'wpproquiz' );
			$question_table       = LDLMS_DB::get_table_name( 'quiz_question', 'wpproquiz' );

			$where = '1=1';

			// Check if start_date and end_date are not all (for all preset).
			if ( 'all' !== $params['start_date'] && 'all' !== $params['end_date'] ) {
				$where .= ' AND qsr.create_time >= ' . $dates['start_date'];
				$where .= ' AND qsr.create_time <= ' . $dates['end_date'];
			}

			// Check for selected courses array.
			if ( ! empty( $query['courses'] ) ) {
				$where .= ' AND qsr.course_post_id IN (' . implode( ',', $query['courses'] ) . ')';
			}
			// Check for selected learners array.
			if ( ! empty( $query['learners'] ) ) {
				$where .= ' AND qsr.user_id IN (' . implode( ',', $query['learners'] ) . ')';
			}

			if ( ! empty( $params['quiz'] ) ) {
				$where .= ' AND qsr.quiz_post_id = ' . $params['quiz'];
			}
			// Cannot use $wpdb->prepare as $where variable will get quotes using that method.
			$sql = sprintf( "SELECT qsr.quiz_post_id, posts.post_title AS quiz, posttable.post_title AS course, SUM(qs.points) AS points,SUM(qq.points) AS total_points,SUM(qs.points) / SUM(qq.points) * 100 as percentage,SUM(qs.question_time) AS quiz_time, qsr.statistic_ref_id, qsr.user_id,COUNT(*) OVER (partition by qsr.quiz_post_id) as total_attempts,COUNT(*) OVER (partition by qsr.user_id) as user_attempts FROM {$statistics_ref_table} AS qsr INNER JOIN {$statistic_table} qs ON qsr.statistic_ref_id = qs.statistic_ref_id INNER JOIN {$question_table} qq ON qq.id = qs.question_id INNER JOIN {$wpdb->posts} posts ON posts.ID = qsr.quiz_post_id INNER JOIN {$wpdb->posts} posttable ON posttable.ID = qsr.course_post_id WHERE {$where} GROUP BY qsr.statistic_ref_id ORDER BY qsr.quiz_post_id" );

			// Check for SQL injection.
			if ( preg_match( '[update|delete|drop|alter]', strtolower( $sql ) ) === true ) {
				throw new \Exception( 'No cheating' );
			}

			/**
			 * Filter the data returned for the overview section of the quiz statistics page.
			 *
			 * @since 5.4.0
			 *
			 * @param array                Response data returned.
			 * @param array  $params       Request parameters.
			 */
			return apply_filters( 'ir_filter_quiz_statistics_overview', $wpdb->get_results( $sql, ARRAY_A ), $params );
		}

		/**
		 * This method is used to further process the summarized/overview data obtained from the database in an appropriate format.
		 *
		 * @since 5.4.0
		 *
		 * @param array $overview_data Overview data.
		 * @param array $params        Input params.
		 */
		public function process_overview_data( $overview_data, $params ) {
			$data = [];
			if ( empty( $overview_data ) ) {
				return $data;
			}
			// Used 0th index because aggregate data is same in each row.
			$total_attempts = $overview_data[0]['total_attempts'];
			$users          = array_unique( wp_list_pluck( $overview_data, 'user_id' ) );
			$total_users    = count( $users );

			$data['quiz']         = get_the_title( $params['quiz'] );
			$data['avg_attempts'] = floatval( number_format( $total_attempts / $total_users, 2, '.', '' ) );// Cast to integer if no decimals.
			$data['avg_score']    = floatval( number_format( array_sum( wp_list_pluck( $overview_data, 'percentage' ) ) / $total_attempts, 2, '.', '' ) ) . '%';// Cast to integer if no decimals.
			$data['avg_points']   = floatval( number_format( array_sum( wp_list_pluck( $overview_data, 'points' ) ) / $total_attempts, 2, '.', '' ) );// Cast to integer if no decimals.
			$data['avg_time']     = date_i18n( 'H:i:s', floatval( number_format( array_sum( wp_list_pluck( $overview_data, 'quiz_time' ) ) / $total_attempts, 2, '.', '' ) ) );// Cast to integer if no decimals.

			// Check for pass/fail based on percentage obtained.
			$quiz_post_settings = learndash_get_setting( $params['quiz'] );
			if ( ! is_array( $quiz_post_settings ) ) {
				$quiz_post_settings = [];
			}
			if ( ! isset( $quiz_post_settings['passingpercentage'] ) ) {
				$quiz_post_settings['passingpercentage'] = 0;
			}
			$passingpercentage = absint( $quiz_post_settings['passingpercentage'] );

			$data['total_passed'] = 0;
			$data['total_failed'] = 0;

			foreach ( $overview_data as $attempt ) {
				if ( $attempt['percentage'] >= $passingpercentage ) {
					++$data['total_passed'];
					continue;
				}
				++$data['total_failed'];
			}

			$data['course'] = array_values( array_unique( wp_list_pluck( $overview_data, 'course' ) ) );
			$data['lesson'] = '';
			$data['topic']  = '';

			if ( ! empty( $params['course'] ) ) {
				// Get lesson/topic titles by climbing up the hierarchy.
				$data = $this->get_quiz_parents( $data, $params );
			}

			/**
			 * Filter the processed data returned for the overview section of the quiz statistics page.
			 *
			 * @since 5.4.0
			 *
			 * @param array  $data         Response data returned.
			 * @param array  $params       Request parameters.
			 */
			return apply_filters( 'ir_filter_quiz_statistics_overview_processed', $data, $params );
		}

		/**
		 * This method is used to fetch a quiz's parent lessons & topics.
		 *
		 * @since 5.4.0
		 *
		 * @param array $data   Processed overview data.
		 * @param array $params Input params.
		 */
		public function get_quiz_parents( $data, $params ) {
			$ld_course_steps_object = LDLMS_Factory_Post::course_steps( intval( $params['course'] ) );

			if ( $ld_course_steps_object ) {
				$steps = $ld_course_steps_object->get_steps( 'h' );
				// First check in global quizzes.
				if ( array_key_exists( 'sfwd-quiz', $steps ) && in_array( $params['quiz'], array_keys( $steps['sfwd-quiz'] ) ) ) {
					return $data;
				}
				if ( ! array_key_exists( 'sfwd-quiz', $steps ) ) {
					return $data;
				}
				foreach ( $steps['sfwd-lessons'] as $lesson_id => $lesson_tree ) {
					if ( array_key_exists( 'sfwd-quiz', $lesson_tree ) && in_array( $params['quiz'], array_keys( $lesson_tree['sfwd-quiz'] ) ) ) {
						$data['lesson'] = get_the_title( $lesson_id );
						return $data;
					}
					if ( ! array_key_exists( 'sfwd-topic', $lesson_tree ) || empty( $lesson_tree['sfwd-topic'] ) ) {
						continue;
					}
					foreach ( $lesson_tree['sfwd-topic'] as $topic_id => $topic_tree ) {
						if ( array_key_exists( 'sfwd-quiz', $topic_tree ) && in_array( $params['quiz'], array_keys( $topic_tree['sfwd-quiz'] ) ) ) {
							$data['lesson'] = get_the_title( $lesson_id );
							$data['topic']  = get_the_title( $topic_id );
							return $data;
						}
					}
				}
			}
			return $data;
		}

		/**
		 * Get quiz attempt details data permissions check
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_quiz_attempt_data_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get quiz attempt details data API endpoint handler
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_quiz_attempt_data( $request ) {
			$data = [];

			// Default params.
			$params = shortcode_atts(
				[
					'quiz'             => '',
					'statistic_ref_id' => '',
					'learner'          => '',
				],
				$request->get_params()
			);

			// All three fields are required.
			if ( ! $this->validate_required_fields( $params, 'quiz' ) || ! $this->validate_required_fields( $params, 'statistic_ref_id' ) || ! $this->validate_required_fields( $params, 'learner' ) ) {
				return new WP_Error( 'ir_rest_parameters_missing', esc_html__( 'You need to provide a Quiz ID, User ID & Statistic Ref ID to view this data.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			$quiz = get_post( $params['quiz'] );

			// Check if valid quiz.
			if ( empty( $quiz ) || ! $quiz instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			// Profile related info used in the first fold of the screen.
			$data['profile_info'] = $this->fetch_profile_info( $params );
			// Quiz overview related info used in the second half of the screen.
			$data['quiz_info'] = $this->fetch_quiz_info( $params );
			// Quiz Attempt info basically all questions, answers, points, etc.
			$data['attempt_info'] = $this->fetch_attempt_info( $params );

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * This method is used to fetch profile level info regarding the learner, each of their attempts and export link for the attempt.
		 *
		 * @since 5.4.0
		 *
		 * @param array $params Input params.
		 */
		public function fetch_profile_info( $params ) {
			global $wpdb;
			$statistics_ref_table = LDLMS_DB::get_table_name( 'quiz_statistic_ref', 'wpproquiz' );

			$where = '1=1';

			// Build query.
			if ( $this->validate_required_fields( $params, 'learner' ) ) {
				$where .= ' AND qsr.user_id =' . $params['learner'];
			}
			if ( ! empty( $params['quiz'] ) ) {
				$where .= ' AND qsr.quiz_post_id =' . $params['quiz'];
			}

			// Cannot use $wpdb->prepare as it will add quotes to the $where variable and the query will give syntax error.
			$sql = sprintf( "SELECT qsr.quiz_post_id as quiz_id, qsr.statistic_ref_id as statistic_ref_id, qsr.create_time as attempt_time, qsr.course_post_id as course_id, posts.post_title as course, posttable.post_title as quiz FROM {$statistics_ref_table} AS qsr INNER JOIN {$wpdb->posts} AS posts ON qsr.course_post_id=posts.ID INNER JOIN {$wpdb->posts} AS posttable ON qsr.quiz_post_id=posttable.ID WHERE {$where} GROUP BY qsr.statistic_ref_id ORDER BY qsr.create_time ASC" );

			// Check for SQL injection.
			if ( preg_match( '[update|delete|drop|alter]', strtolower( $sql ) ) === true ) {
				throw new \Exception( 'No cheating' );
			}

			$all_attempts = $wpdb->get_results( $sql, ARRAY_A );

			$statistics         = wp_list_pluck( $all_attempts, 'statistic_ref_id' );
			$selected_statistic = array_search( $params['statistic_ref_id'], $statistics );

			$data              = [];
			$data['quiz']      = $all_attempts[0]['quiz'];
			$data['course']    = $all_attempts[ $selected_statistic ]['course'];
			$data['course_id'] = $params['course'] = $all_attempts[ $selected_statistic ]['course_id'];
			$data['lesson']    = '';
			$data['topic']     = '';
			$data              = $this->get_quiz_parents( $data, $params );

			// Filter out statistics from other courses. (Shared steps).
			$all_attempts = array_filter(
				$all_attempts,
				function ( $attempt ) use ( $all_attempts, $selected_statistic ) {
					if ( $attempt['course_id'] !== $all_attempts[ $selected_statistic ]['course_id'] ) {
						return false;
					}
					return true;
				}
			);

			// Recalculate index for current attempt and attempts list after other course results are filtered out.
			$statistics         = wp_list_pluck( $all_attempts, 'statistic_ref_id' );
			$selected_statistic = array_search( $params['statistic_ref_id'], $statistics );

			$data['total_attempts'] = count( $all_attempts );

			// Get all similar attempts list for the attempt dropdown.
			if ( class_exists( 'NumberFormatter' ) ) {
				$formatter = new \NumberFormatter( 'en_US', \NumberFormatter::SPELLOUT );
				$formatter->setTextAttribute( \NumberFormatter::DEFAULT_RULESET, '%spellout-ordinal' );
				$index = 0;
				foreach ( $statistics as $statistic_ref_id ) {
					$data['statistics'][] = [
						'value' => $statistic_ref_id,
						'label' => sprintf( __( '%s Attempt', 'wdm_instructor_role' ), ucfirst( $formatter->format( 1 + $index ) ) ),
					];
					++$index;
				}
			} else {
				$index = 0;
				foreach ( $statistics as $statistic_ref_id ) {
					$data['statistics'][] = [
						'value' => $statistic_ref_id,
						'label' => sprintf( __( '%s Attempt', 'wdm_instructor_role' ), $this->ordinal( 1 + $index ) ),
					];
					++$index;
				}
			}
			$data['export_csv'] = admin_url( 'admin-ajax.php' ) . '?action=export_quiz_statistics&_nonce=' . wp_create_nonce( 'ir_quiz_export' ) . '&ref_id=' . $params['statistic_ref_id'] . '&file_format=csv&quiz=' . $params['quiz'] . '&learner=' . $params['learner'];

			$data['time'] = date_i18n( 'Y-m-d h:i A', $all_attempts[ $selected_statistic ]['attempt_time'] );

			if ( $params['learner'] > 0 ) {
				$user            = get_userdata( $params['learner'] );
				$data['learner'] = [
					'id'    => $user->ID,
					'name'  => $user->display_name,
					'image' => get_avatar_url( $user->ID ),
					'email' => $user->user_email,
				];
			} else {
				$data['learner'] = [
					'id'    => 0,
					'name'  => __( 'Anonymous', 'wdm_instructor_role' ),
					'image' => '',
					'email' => '',
				];
			}
			return $data;
		}

		/**
		 * This method is used to fetch quiz attempt details such as points, score, percentage, correct count etc for a specific attempt.
		 *
		 * @since 5.4.0
		 *
		 * @param array $params Input params.
		 */
		public function fetch_quiz_info( $params ) {
			global $wpdb;
			$statistics_ref_table = LDLMS_DB::get_table_name( 'quiz_statistic_ref', 'wpproquiz' );
			$statistic_table      = LDLMS_DB::get_table_name( 'quiz_statistic', 'wpproquiz' );
			$question_table       = LDLMS_DB::get_table_name( 'quiz_question', 'wpproquiz' );

			$where = '1=1';

			if ( ! empty( $params['statistic_ref_id'] ) ) {
				$where .= ' AND qsr.statistic_ref_id =' . $params['statistic_ref_id'];
			}

			// Cannot use $wpdb->prepare as it will add quotes around $where which will give SQL syntax error.
			$sql = sprintf( "SELECT SUM(qs.points) AS points, SUM(qq.points) AS total_points, SUM(qs.points) / SUM(qq.points) * 100 as percentage, SUM(qs.question_time) AS quiz_time, SUM(qs.correct_count) AS correct_count, SUM(qs.incorrect_count) AS incorrect_count FROM {$statistics_ref_table} as qsr INNER JOIN {$statistic_table} as qs ON qsr.statistic_ref_id = qs.statistic_ref_id INNER JOIN {$question_table} as qq ON qq.id = qs.question_id WHERE {$where}" );

			// Check for SQL injection.
			if ( preg_match( '[update|delete|drop|alter]', strtolower( $sql ) ) === true ) {
				throw new \Exception( 'No cheating' );
			}

			$attempt_details = $wpdb->get_results( $sql, ARRAY_A );

			// Get passing criteria.
			$quiz_post_settings = learndash_get_setting( $params['quiz'] );
			if ( ! is_array( $quiz_post_settings ) ) {
				$quiz_post_settings = [];
			}
			if ( ! isset( $quiz_post_settings['passingpercentage'] ) ) {
				$quiz_post_settings['passingpercentage'] = 0;
			}
			$passingpercentage = absint( $quiz_post_settings['passingpercentage'] );

			$data                    = [];
			$data['percentage']      = floatval( number_format( $attempt_details[0]['percentage'], 2, '.', '' ) );// Cast to integer if no decimals.
			$data['status']          = ( $passingpercentage <= $attempt_details[0]['percentage'] ) ? __( 'Passed', 'wdm_instructor_role' ) : __( 'Failed', 'wdm_instructor_role' );
			$data['pass']            = $passingpercentage <= $attempt_details[0]['percentage'];
			$data['correct_count']   = $attempt_details[0]['correct_count'];
			$data['incorrect_count'] = $attempt_details[0]['incorrect_count'];
			$data['time_taken']      = date_i18n( 'H:i:s', $attempt_details[0]['quiz_time'] );
			$data['points']          = $attempt_details[0]['points'];
			$data['total_points']    = $attempt_details[0]['total_points'];

			return $data;
		}

		/**
		 * This method is used to fetch details of each question for the attempt such as question, points, options, correct answer, user response etc.
		 *
		 * @since 5.4.0
		 *
		 * @param array $params Input params.
		 */
		public function fetch_attempt_info( $params ) {
			global $wpdb;
			$statistics_ref_table = LDLMS_DB::get_table_name( 'quiz_statistic_ref', 'wpproquiz' );
			$statistic_table      = LDLMS_DB::get_table_name( 'quiz_statistic', 'wpproquiz' );
			$question_table       = LDLMS_DB::get_table_name( 'quiz_question', 'wpproquiz' );

			$where = '1=1';

			if ( ! empty( $params['statistic_ref_id'] ) ) {
				$where .= ' AND qsr.statistic_ref_id = ' . $params['statistic_ref_id'];
			}

			// Cannot use $wpdb->prepare as it will add quotes around $where which will give SQL syntax error.
			$sql = sprintf( "SELECT qsr.quiz_post_id, qsr.quiz_id, qsr.user_id as user_id, qs.correct_count, qs.answer_data as user_answer, qq.id as question_pro_id, qq.question, qq.answer_type, qq.answer_data as question_options, qq.points, qq.answer_points_activated,qq.sort col_sort, qs.points qspoints, qs.question_time FROM {$statistics_ref_table} as qsr INNER JOIN {$statistic_table} as qs ON qsr.statistic_ref_id = qs.statistic_ref_id INNER JOIN {$question_table} as qq ON qq.id = qs.question_id WHERE {$where} ORDER BY qq.sort ASC" );

			// Check for SQL injection.
			if ( preg_match( '[update|delete|drop|alter]', strtolower( $sql ) ) === true ) {
				throw new \Exception( 'No cheating' );
			}

			$attempt_details = $wpdb->get_results( $sql, ARRAY_A );
			$data            = [];
			$counter         = 0;

			// Loop through each question.
			foreach ( $attempt_details as $question ) {
				$question_type       = $question['answer_type'];
				$arr_user_response   = [];
				$arr_answers         = [];
				$arr_correct_answers = [];
				$points_array        = [];
				$correct_array       = [];
				$is_attach_question  = false; // if want to attache answer to question. For cloze questions.
				$answer_obj          = '';
				$question_options    = maybe_unserialize( $question['question_options'] );
				$user_answer         = json_decode( $question['user_answer'], 1 );
				$points_per_answer   = $question['answer_points_activated'];
				// Process each questions data based on question type.
				switch ( $question_type ) {
					// if $question_type is single OR multiple.
					case 'single':
					case 'multiple':
						$res_object          = new Instructor_Role_Single_Question_Data( $question_options, $user_answer, $points_per_answer );
						$arr_correct_answers = $res_object->get_correct_answers();
						$arr_user_response   = $res_object->get_user_answers();
						$arr_answers         = $res_object->get_all_answers();
						$points_array        = $res_object->get_answer_points();
						$correct_array       = $res_object->get_correct_options();
						break;

					case 'free_answer':
						// if $question_type is free choice.
						$res_object          = new Instructor_Role_Free_Question_Data( $question_options, $user_answer, $points_per_answer );
						$arr_correct_answers = $res_object->get_correct_answers();
						$arr_user_response   = $res_object->get_user_answers();
						$arr_answers         = $res_object->get_all_answers();
						$points_array        = $res_object->get_answer_points();
						$correct_array       = $res_object->get_correct_options();
						break;

					case 'sort_answer':
						// if $question_type is sorting.
						$res_object          = new Instructor_Role_Sort_Question_Data( $question_options, $question['question_pro_id'], $user_answer, $question['user_id'], $points_per_answer );
						$arr_correct_answers = $res_object->get_correct_answers();
						$arr_user_response   = $res_object->get_user_answers();
						$arr_answers         = $res_object->get_all_answers();
						$points_array        = $res_object->get_answer_points();
						$correct_array       = $res_object->get_correct_options();
						break;

					case 'matrix_sort_answer':
						// if $question_type is matrix sort.
						$res_object          = new Instructor_Role_Matrix_Sort_Question_Data( $question_options, $question['question_pro_id'], $user_answer, $question['user_id'], $points_per_answer );
						$arr_correct_answers = $res_object->get_correct_answers();
						$arr_user_response   = $res_object->get_user_answers();
						$arr_answers         = $res_object->get_all_answers();
						$answer_obj          = $res_object->get_answer_obj();
						$points_array        = $res_object->get_answer_points();
						$correct_array       = $res_object->get_correct_options();
						break;

					case 'cloze_answer':
						// if $question_type is fill in the blank.
						$is_attach_question  = true;
						$res_object          = new Instructor_Role_Cloze_Question_Data( $question_options, $question, $user_answer, $points_per_answer );
						$arr_correct_answers = $res_object->get_correct_answers();
						$arr_user_response   = $res_object->get_user_answers();
						$arr_answers         = $res_object->get_all_answers();
						$answer_obj          = $res_object->get_answer_obj();
						$points_array        = $res_object->get_answer_points();
						$correct_array       = $res_object->get_correct_options();
						break;

					case 'assessment_answer':
						// if $question_type is assessment ( post reviews type ).
						$res_object          = new Instructor_Role_Assessment_Question_Data( $question_options, $user_answer, $points_per_answer );
						$arr_correct_answers = $res_object->get_correct_answers();
						$arr_user_response   = $res_object->get_user_answers();
						$arr_answers         = explode( ',', $res_object->get_all_answers()[0] );
						$answer_obj          = $res_object->get_answer_obj();
						$points_array        = $res_object->get_answer_points();
						$correct_array       = $res_object->get_correct_options();
						break;

					case 'essay':
						// if $question_type is essay.
						$res_object                                       = new Instructor_Role_Essay_Question_Data( $user_answer );
						$arr_user_response                                = $res_object->get_user_answers();
						$data['question_meta'][ $counter ]['graded_type'] = ir_get_protected_value( $question_options[0], '_gradedType' );
						$is_graded                                        = ir_get_protected_value( $question_options[0], '_gradingProgression' );
						$data['question_meta'][ $counter ]['is_graded']   = ! Str::contains( $is_graded, 'not-graded' );
						if ( empty( $arr_user_response[0] ) ) {
							$data['question_meta'][ $counter ]['is_graded'] = true;
						}
						break;
				}
				// Remove HTML from question text.
				$data['question_meta'][ $counter ]['question'] = strip_tags( $question['question'] );
				// For cloze/fill in the blank following processing is needed.
				if ( $is_attach_question && '' !== $answer_obj ) {
					foreach ( $arr_correct_answers as $key => $answer ) {
						$points_earned_index = array_search( $arr_user_response[ $key ], $answer );

						if ( false === $points_earned_index ) {
							$points_str = '<strong class="ir-heading-color">0</strong>/' . ( isset( $points_array[ $key ] ) ? max( $points_array[ $key ] ) : $question['points'] ) . __( ' points', 'wdm_instructor_role' );
							if ( ! $points_per_answer ) {
								$points_str = '';
							}
							$is_correct = false;
						} else {
							$points_str = '<strong class="ir-heading-color">' . $points_array[ $key ][ $points_earned_index ] . '</strong>/' . ( isset( $points_array[ $key ][ $points_earned_index ] ) ? max( $points_array[ $key ] ) : $question['points'] ) . __( ' points', 'wdm_instructor_role' );
							if ( ! $points_per_answer ) {
								$points_str = '';
							}
							$is_correct = true;
						}
						$arr_user_response[ $key ] = '<div><span class="ir-answer-text ' . ( $is_correct ? 'correct' : 'incorrect' ) . '">' . $arr_user_response[ $key ] . '</span><span class="ir-answer-points">' . $points_str . '</span></div>';
						// Replace each {} with user's answer.
						$answer_obj = preg_replace( '#\{(.*?)\}#', $arr_user_response[ $key ], $answer_obj, 1 );
					}
					// to attach answer in question.
					$data['question_meta'][ $counter ]['answer'] = $answer_obj;
				}
				$data['question_meta'][ $counter ]['sorting_options']          = ( '' !== $answer_obj && 'matrix_sort_answer' === $question_type ) ? $answer_obj : [];
				$data['question_meta'][ $counter ]['points']                   = $question['points'];
				$data['question_meta'][ $counter ]['points_scored']            = $question['qspoints'];
				$data['question_meta'][ $counter ]['is_correct']               = $question['correct_count'];
				$data['question_meta'][ $counter ]['time_taken']               = date_i18n( 'H:i:s', $question['question_time'] );
				$data['question_meta'][ $counter ]['answers']                  = $arr_answers;
				$data['question_meta'][ $counter ]['correct_answers']          = $arr_correct_answers;
				$data['question_meta'][ $counter ]['user_response']            = $arr_user_response;
				$data['question_meta'][ $counter ]['question_type']            = $question_type;
				$data['question_meta'][ $counter ]['question_id']              = $question['question_pro_id'];
				$data['question_meta'][ $counter ]['different_points_setting'] = $points_per_answer;
				$data['question_meta'][ $counter ]['correct_array']            = $correct_array;
				$data['question_meta'][ $counter ]['answer_points']            = $points_array;
				++$counter;
			}
			return $data;
		}
	}
}
