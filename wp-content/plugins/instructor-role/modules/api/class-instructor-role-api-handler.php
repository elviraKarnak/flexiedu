<?php
/**
 * Rest API Handler Module
 *
 * @since 4.4.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 *
 * cspell:ignore prereq // ignoring misspelled words that we can't change now.
 */

namespace InstructorRole\Modules\Api;

use WP_Error;
use WP_REST_Posts_Controller;
use WP_Rest_Server;
use WP_Post, WP_Query;
use WP_User;
use WP_User_Query;
use WP_REST_Request;
use WP_REST_Response;
use InstructorRole\Modules\Classes\Instructor_Role_Review;
use LDLMS_DB;
use LearnDash\Core\Utilities\Cast;
use WpProQuiz_Model_Question;
use WpProQuiz_Model_Quiz;
use WpProQuiz_Model_QuizMapper;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Api_Handler' ) ) {
	/**
	 * Class Instructor Role Api Handler
	 */
	class Instructor_Role_Api_Handler extends WP_REST_Posts_Controller {
		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
		 *
		 * @since 4.4.0
		 */
		protected static $instance = null;

		/**
		 * Plugin Slug
		 *
		 * @var string  $plugin_slug
		 *
		 * @since 4.4.0
		 */
		protected $plugin_slug = '';

		/**
		 * API Route Namespace
		 *
		 * @var string  $namespace
		 *
		 * @since 4.4.0
		 */
		protected $namespace = '';
		/**
		 * Constructor
		 */
		public function __construct() {
			$this->plugin_slug = INSTRUCTOR_ROLE_TXT_DOMAIN;
			$this->namespace   = 'ir/v1';
		}

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 4.4.0
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
		 * @since 4.4.0
		 */
		public function register_custom_endpoints() {
			// Get course details.
			register_rest_route(
				$this->namespace,
				'/sfwd-courses/(?P<id>[\d]+)/steps',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_course_steps' ],
						'permission_callback' => [ $this, 'get_course_steps_permissions_check' ],
					],
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'update_course_steps' ],
						'permission_callback' => [ $this, 'update_course_steps_permissions_check' ],
					],
					'schema' => [ $this, 'get_schema' ],
				]
			);

			// Get all course data.
			register_rest_route(
				$this->namespace,
				'/sfwd-courses/(?P<id>[\d]+)/steps_data',
				[
					[
						'methods'             => WP_Rest_Server::READABLE,
						'callback'            => [ $this, 'get_course_steps_data' ],
						'permission_callback' => [ $this, 'get_course_steps_data_permissions_check' ],
					],
				]
			);

			// Get all lessons list.
			register_rest_route(
				$this->namespace,
				'/other-sfwd-lessons/',
				[
					[
						'methods'             => WP_Rest_Server::READABLE,
						'callback'            => [ $this, 'get_other_lessons' ],
						'permission_callback' => [ $this, 'get_other_lessons_permissions_check' ],
					],
				]
			);

			// Get all topic list.
			register_rest_route(
				$this->namespace,
				'/other-sfwd-topics/',
				[
					[
						'methods'             => WP_Rest_Server::READABLE,
						'callback'            => [ $this, 'get_other_topics' ],
						'permission_callback' => [ $this, 'get_other_topics_permissions_check' ],
					],
				]
			);

			// Get all quiz list.
			register_rest_route(
				$this->namespace,
				'/other-sfwd-quizzes/',
				[
					[
						'methods'             => WP_Rest_Server::READABLE,
						'callback'            => [ $this, 'get_other_quizzes' ],
						'permission_callback' => [ $this, 'get_other_quizzes_permissions_check' ],
					],
				]
			);

			// Get course users.
			register_rest_route(
				$this->namespace,
				'/sfwd-courses/(?P<id>[\d]+)/users',
				[
					[
						'methods'             => WP_Rest_Server::READABLE,
						'callback'            => [ $this, 'get_course_users' ],
						'permission_callback' => [ $this, 'get_course_users_permissions_check' ],
					],
					[
						'methods'             => WP_Rest_Server::EDITABLE,
						'callback'            => [ $this, 'update_course_users' ],
						'permission_callback' => [ $this, 'update_course_users_permissions_check' ],
					],
				]
			);

			// Get certificates.
			register_rest_route(
				$this->namespace,
				'/sfwd-certificates/',
				[
					[
						'methods'             => WP_Rest_Server::READABLE,
						'callback'            => [ $this, 'get_certificates' ],
						'permission_callback' => [ $this, 'get_certificates_permissions_check' ],
					],
				]
			);

			// Get challenge exams.
			register_rest_route(
				$this->namespace,
				'/ld-exams/',
				[
					[
						'methods'             => WP_Rest_Server::READABLE,
						'callback'            => [ $this, 'get_exams' ],
						'permission_callback' => [ $this, 'get_exams_permissions_check' ],
					],
				]
			);

			// Get all instructors.
			register_rest_route(
				$this->namespace,
				'/instructors/',
				[
					[
						'methods'             => WP_Rest_Server::READABLE,
						'callback'            => [ $this, 'get_instructors' ],
						'permission_callback' => [ $this, 'get_instructors_permissions_check' ],
					],
				]
			);

			// Get all ld-course-category.
			register_rest_route(
				$this->namespace,
				'/ld-course-category/',
				[
					[
						'methods'             => WP_Rest_Server::READABLE,
						'callback'            => [ $this, 'get_category' ],
						'permission_callback' => [ $this, 'get_category_permissions_check' ],
					],
				]
			);

			// Get all ld-lesson-category.
			register_rest_route(
				$this->namespace,
				'/ld-lesson-category/',
				[
					[
						'methods'             => WP_Rest_Server::READABLE,
						'callback'            => [ $this, 'get_category' ],
						'permission_callback' => [ $this, 'get_category_permissions_check' ],
					],
				]
			);

			// Get all ld-topic-category.
			register_rest_route(
				$this->namespace,
				'/ld-topic-category/',
				[
					[
						'methods'             => WP_Rest_Server::READABLE,
						'callback'            => [ $this, 'get_category' ],
						'permission_callback' => [ $this, 'get_category_permissions_check' ],
					],
				]
			);

			// Get all ld-quiz-category.
			register_rest_route(
				$this->namespace,
				'/ld-quiz-category/',
				[
					[
						'methods'             => WP_Rest_Server::READABLE,
						'callback'            => [ $this, 'get_category' ],
						'permission_callback' => [ $this, 'get_category_permissions_check' ],
					],
				]
			);

			// Get course instructors.
			register_rest_route(
				$this->namespace,
				'/instructors/(?P<id>[\d]+)',
				[
					[
						'methods'             => WP_Rest_Server::READABLE,
						'callback'            => [ $this, 'get_course_instructors' ],
						'permission_callback' => [ $this, 'get_course_instructors_permissions_check' ],
					],
					[
						'methods'             => WP_Rest_Server::EDITABLE,
						'callback'            => [ $this, 'update_course_instructors' ],
						'permission_callback' => [ $this, 'update_course_instructors_permissions_check' ],
					],
				]
			);

			// Update course details not saved by LD.
			register_rest_route(
				$this->namespace,
				'/sfwd-courses/(?P<id>[\d]+)/remaining',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'fetch_remaining_course_details' ],
						'permission_callback' => [ $this, 'fetch_remaining_course_details_permissions_check' ],
					],
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'update_remaining_course_details' ],
						'permission_callback' => [ $this, 'update_remaining_course_details_permissions_check' ],
					],
				]
			);

			// Update course group details.
			register_rest_route(
				$this->namespace,
				'/sfwd-courses/(?P<id>[\d]+)/groups',
				[
					'args'   => [
						'id' => [
							'description' => sprintf(
								// translators: placeholder: Course.
								esc_html_x(
									'%s ID',
									'placeholder: Course',
									'learndash'
								),
								\LearnDash_Custom_Label::get_label( 'course' )
							),
							'required'    => true,
							'type'        => 'integer',
						],
					],
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_group_course' ],
						'permission_callback' => [ $this, 'fetch_remaining_course_details_permissions_check' ],
					],
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'update_group_course' ],
						'permission_callback' => [ $this, 'update_group_course_permissions_check' ],
					],
					'schema' => [ $this, 'get_public_item_schema' ],
				]
			);

			// Fetch and Update quiz settings not saved by LD.
			register_rest_route(
				$this->namespace,
				'/quiz-settings/(?P<id>[\d]+)',
				[
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'update_quiz_settings' ],
						'permission_callback' => [ $this, 'set_quiz_settings_permissions_handle' ],
					],
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'fetch_quiz_settings' ],
						'permission_callback' => [ $this, 'set_quiz_settings_permissions_handle' ],
					],
				],
			);

			// Fetch question settings not saved by LD.
			register_rest_route(
				$this->namespace,
				'/sfwd-question/(?P<id>[\d]+)',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'fetch_question_by_id' ],
						'permission_callback' => [ $this, 'fetch_question_by_id_permissions_handle' ],
					],
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'update_question_settings' ],
						'permission_callback' => [ $this, 'set_question_settings_permissions_handle' ],
					],
				],
			);

			// Create question not saved by LD.
			register_rest_route(
				$this->namespace,
				'/sfwd-question/',
				[
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'create_question' ],
						'permission_callback' => [ $this, 'set_question_settings_permissions_handle' ],
					],
				],
			);

			// Fetch question library.
			register_rest_route(
				$this->namespace,
				'/question-library/(?P<id>[\d]+)',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'fetch_question_library' ],
						'permission_callback' => [ $this, 'get_question_library_permissions_handle' ],
					],
				],
			);

			// Fetch site users.
			register_rest_route(
				$this->namespace,
				'/users',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'fetch_users' ],
						'permission_callback' => [ $this, 'fetch_users_permissions_check' ],
					],
				],
			);

			// Fetch user groups.
			register_rest_route(
				$this->namespace,
				'/groups',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'fetch_groups' ],
						'permission_callback' => [ $this, 'fetch_groups_permissions_check' ],
					],
				],
			);

			// Fetch course review data.
			register_rest_route(
				$this->namespace,
				'/sfwd-courses/(?P<id>[\d]+)/review',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'fetch_review_data' ],
						'permission_callback' => [ $this, 'fetch_review_data_permissions_check' ],
					],
				],
			);

			// Fetch lesson review data.
			register_rest_route(
				$this->namespace,
				'/sfwd-lessons/(?P<id>[\d]+)/review',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'fetch_review_data' ],
						'permission_callback' => [ $this, 'fetch_review_data_permissions_check' ],
					],
				],
			);

			// Fetch topic review data.
			register_rest_route(
				$this->namespace,
				'/sfwd-topic/(?P<id>[\d]+)/review',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'fetch_review_data' ],
						'permission_callback' => [ $this, 'fetch_review_data_permissions_check' ],
					],
				],
			);

			// Fetch quiz review data.
			register_rest_route(
				$this->namespace,
				'/sfwd-quiz/(?P<id>[\d]+)/review',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'fetch_review_data' ],
						'permission_callback' => [ $this, 'fetch_review_data_permissions_check' ],
					],
				],
			);
		}

		/**
		 * Get course steps
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_course_steps( $request ) {
			$data = [];

			$course = get_post( $request['id'] );

			if ( empty( $course ) || ! $course instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			$ld_course_steps_object = \LDLMS_Factory_Post::course_steps( intval( $course->ID ) );

			if ( $ld_course_steps_object ) {
				$steps            = $ld_course_steps_object->get_steps();
				$data['sections'] = $ld_course_steps_object->get_steps( 'sections' );
				$data['tree']     = ir_get_formatted_course_steps( $steps );
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Course steps permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_course_steps_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();
			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if instructor course.
			$course_id              = absint( $request['id'] );
			$instructor_course_list = ir_get_instructor_complete_course_list( $current_user_id, 1 );

			if ( ! in_array( $course_id, $instructor_course_list ) ) {
				return new WP_Error( 'ir_rest_not_allowed', esc_html__( 'You do not have access to this post.', 'wdm_instructor_role' ), [ 'status' => 403 ] );
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Gets the course steps schema.
		 *
		 * @return array
		 */
		public function get_schema() {
			$schema = [
				'$schema'    => 'http://json-schema.org/draft-04/schema#',
				'title'      => 'course-step',
				'type'       => 'object',
				'properties' => [
					'id'   => [
						'description' => __( 'Unique identifier for the object.', 'learndash' ),
						'type'        => 'integer',
						'context'     => [ 'view', 'edit', 'embed' ],
						'readonly'    => true,
					],
					'type' => [
						// translators: placeholder: course.
						'description' => sprintf( esc_html_x( 'The %s step type.', 'placeholder: course', 'learndash' ), learndash_get_custom_label_lower( 'course' ) ),
						'type'        => 'string',
						'enum'        => [
							'all',
							'h',
							'l',
							't',
							'r',
						],
						'context'     => [ 'view', 'edit' ],
					],
				],
			];

			return $schema;
		}

		/**
		 * Update course steps
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 *
		 * @return WP_REST_REQUEST
		 */
		public function update_course_steps( $request ) {
			$course = get_post( absint( $request['id'] ) );
			if ( empty( $course ) || ! $course instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			$ld_course_steps_object = \LDLMS_Factory_Post::course_steps( intval( $course->ID ) );
			$course_data            = $request->get_params();
			$steps                  = [];
			$steps['sfwd-lessons']  = [];
			$steps['sfwd-quiz']     = [];

			if ( ! empty( $course_data['sfwd-lessons'] ) ) {
				uasort(
					$course_data['sfwd-lessons'],
					function ( $ele1, $ele2 ) {
						return ( $ele1['order'] >= $ele2['order'] ) ? 1 : -1;
					}
				);

				foreach ( $course_data['sfwd-lessons'] as $lesson_id => $lesson_set ) {
					$steps['sfwd-lessons'][ $lesson_id ]               = [];
					$steps['sfwd-lessons'][ $lesson_id ]['sfwd-topic'] = [];
					$steps['sfwd-lessons'][ $lesson_id ]['sfwd-quiz']  = [];

					if ( ! empty( $lesson_set['sfwd-topic'] ) ) {
						uasort(
							$lesson_set['sfwd-topic'],
							function ( $ele1, $ele2 ) {
								return ( $ele1['order'] >= $ele2['order'] ) ? 1 : -1;
							}
						);

						foreach ( $lesson_set['sfwd-topic'] as $topic_id => $topic_set ) {
							$steps['sfwd-lessons'][ $lesson_id ]['sfwd-topic'][ $topic_id ]              = [];
							$steps['sfwd-lessons'][ $lesson_id ]['sfwd-topic'][ $topic_id ]['sfwd-quiz'] = [];

							if ( ! empty( $topic_set['sfwd-quiz'] ) ) {
								uasort(
									$topic_set['sfwd-quiz'],
									function ( $ele1, $ele2 ) {
										return ( $ele1['order'] >= $ele2['order'] ) ? 1 : -1;
									}
								);

								foreach ( $topic_set['sfwd-quiz'] as $quiz_id => $quiz_set ) {
									$steps['sfwd-lessons'][ $lesson_id ]['sfwd-topic'][ $topic_id ]['sfwd-quiz'][ $quiz_id ] = [];
								}
							}
						}
					}

					if ( ! empty( $lesson_set['sfwd-quiz'] ) ) {
						uasort(
							$lesson_set['sfwd-quiz'],
							function ( $ele1, $ele2 ) {
								return ( $ele1['order'] >= $ele2['order'] ) ? 1 : -1;
							}
						);
						foreach ( $lesson_set['sfwd-quiz'] as $quiz_id => $quiz_set ) {
							$steps['sfwd-lessons'][ $lesson_id ]['sfwd-quiz'][ $quiz_id ] = [];
						}
					}
				}
			}

			if ( ! empty( $course_data['sfwd-quiz'] ) ) {
				uasort(
					$course_data['sfwd-quiz'],
					function ( $ele1, $ele2 ) {
						return ( $ele1['order'] >= $ele2['order'] ) ? 1 : -1;
					}
				);
				$steps['sfwd-quiz'] = $course_data['sfwd-quiz'];
			}

			if ( ! empty( $course_data['section-heading'] ) ) {
				$steps['section-heading'] = $course_data['section-heading'];
			}

			$ld_course_steps_object->set_steps( $steps );
			$ld_course_steps_object->load_steps();
			$course_steps = $ld_course_steps_object->get_steps( 'h' );

			// Create the response object.
			$response = rest_ensure_response( $course_steps );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Update course steps permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_course_steps_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();
			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if instructor course.
			$course_id              = absint( $request['id'] );
			$instructor_course_list = ir_get_instructor_complete_course_list( $current_user_id, 1 );

			if ( ! in_array( $course_id, $instructor_course_list ) ) {
				return new WP_Error( 'ir_rest_not_allowed', esc_html__( 'You do not have access to this post.', 'wdm_instructor_role' ), [ 'status' => 403 ] );
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Get course steps data
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_course_steps_data( $request ) {
			$data  = [];
			$steps = [];

			$course = get_post( absint( $request['id'] ) );

			if ( empty( $course ) || ! $course instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			$ld_course_steps_object = \LDLMS_Factory_Post::course_steps( intval( $course->ID ) );

			if ( $ld_course_steps_object ) {
				$steps = $ld_course_steps_object->get_steps( 't' );
			}

			// Get lesson objects.
			if ( array_key_exists( 'sfwd-lessons', $steps ) ) {
				foreach ( $steps['sfwd-lessons'] as $lesson_id ) {
					$data[ absint( $lesson_id ) ] = (array) get_post( absint( $lesson_id ) );
					if ( learndash_is_sample( $lesson_id ) ) {
						$data[ absint( $lesson_id ) ]['is_sample'] = true;
					}
				}
			}
			// Get topic objects.
			if ( array_key_exists( 'sfwd-topic', $steps ) ) {
				foreach ( $steps['sfwd-topic'] as $topic_id ) {
					$data[ absint( $topic_id ) ] = get_post( absint( $topic_id ) );
				}
			}
			// Get quiz objects.
			if ( array_key_exists( 'sfwd-quiz', $steps ) ) {
				foreach ( $steps['sfwd-quiz'] as $quiz_id ) {
					$data[ absint( $quiz_id ) ] = get_post( absint( $quiz_id ) );
				}
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Course steps data permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_course_steps_data_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor here.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if instructor course.
			$course_id              = absint( $request['id'] );
			$instructor_course_list = ir_get_instructor_complete_course_list( $current_user_id, 1 );

			if ( ! in_array( $course_id, $instructor_course_list ) ) {
				return new WP_Error( 'ir_rest_not_allowed', esc_html__( 'You do not have access to this post, returning.', 'wdm_instructor_role' ), [ 'status' => 403 ] );
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Get list of other lessons.
		 *
		 * @param WP_REST_Request $request  WP_REST_Request instance.
		 */
		public function get_other_lessons( $request ) {
			$data                      = [];
			$lessons_list              = [];
			$course_lessons            = [];
			$instructor_course_lessons = [];
			$orphan_lessons            = [];
			$course                    = get_post( absint( $request['course'] ) );

			if ( empty( $course ) || ! $course instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid Course ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			$course_lessons = learndash_get_course_steps( $course->ID, [ 'sfwd-lessons' ] );

			if ( current_user_can( 'manage_options' ) ) {
				// Fetch orphan lessons.
				$args = [
					'post_type'      => learndash_get_post_type_slug( 'lesson' ),
					'posts_per_page' => -1,
					'post_status'    => [ 'publish', 'draft' ],
					'fields'         => 'ids',
				];

				$all_lessons = new WP_Query( $args );
				if ( ! empty( $all_lessons->posts ) ) {
					$lessons_list = array_unique( array_merge( $lessons_list, $all_lessons->posts ) );
				}

				if ( ! empty( $course_lessons ) ) {
					$lessons_list = array_diff( $lessons_list, $course_lessons );
				}
			} else {
				$current_user_id    = get_current_user_id();
				$instructor_courses = ir_get_instructor_complete_course_list( $current_user_id, 1 );

				foreach ( $instructor_courses as $course_id ) {
					// Skip current course.
					if ( absint( $course_id ) === absint( $course->ID ) ) {
						continue;
					}
					$instructor_course_lessons = learndash_get_lesson_list( $course_id );

					if ( ! empty( $instructor_course_lessons ) ) {
						$lessons_list = array_merge( $lessons_list, wp_list_pluck( $instructor_course_lessons, 'ID' ) );
					}
				}

				// Fetch orphan lessons.
				$args = [
					'post_type'      => learndash_get_post_type_slug( 'lesson' ),
					'posts_per_page' => -1,
					'post_status'    => [ 'publish', 'draft' ],
					'author'         => $current_user_id,
					'fields'         => 'ids',
				];

				$orphan_lessons = new WP_Query( $args );

				if ( ! empty( $orphan_lessons->posts ) ) {
					$lessons_list = array_unique( array_merge( $lessons_list, $orphan_lessons->posts ) );
				}

				if ( ! empty( $course_lessons ) ) {
					// Check if object array or simple array.
					if ( is_object( $course_lessons[0] ) ) {
						$lessons_list = array_diff( $lessons_list, wp_list_pluck( $course_lessons, 'ID' ) );
					} else {
						$lessons_list = array_diff( $lessons_list, $course_lessons );
					}
				}
			}

			foreach ( $lessons_list as $lesson_id ) {
				$lesson_details = get_post( $lesson_id );

				if ( null !== $lesson_details ) {
					$data[ $lesson_id ] = [
						'title'     => $lesson_details->post_title,
						'timestamp' => strtotime( $lesson_details->post_modified ),
						'status'    => $lesson_details->post_status,
						'is_sample' => learndash_is_sample( $lesson_id ),
					];

					if ( ! learndash_is_course_shared_steps_enabled() && ! is_null( get_post( (int) learndash_get_setting( $lesson_id, 'course' ) ) ) ) {
						unset( $data[ $lesson_id ] );
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
		 * Get other lessons permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_other_lessons_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if instructor course.
			$course_id              = absint( $request['course'] );
			$instructor_course_list = ir_get_instructor_complete_course_list( $current_user_id, 1 );

			if ( ! in_array( $course_id, $instructor_course_list ) ) {
				return new WP_Error( 'ir_rest_not_allowed', esc_html__( 'You do not have access to this post.', 'wdm_instructor_role' ), [ 'status' => 403 ] );
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Get list of other topics.
		 *
		 * @param WP_REST_Request $request  WP_REST_Request instance.
		 */
		public function get_other_topics( $request ) {
			$data            = [];
			$topics_list     = [];
			$course_topics   = [];
			$orphan_topics   = [];
			$course          = get_post( absint( $request['course'] ) );
			$current_user_id = get_current_user_id();

			if ( empty( $course ) || ! $course instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid Course ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			$topic_steps = learndash_get_course_steps( $course->ID, [ 'sfwd-topic' ] );

			if ( current_user_can( 'manage_options' ) ) {
				// Fetch all topics.
				$args = [
					'post_type'      => learndash_get_post_type_slug( 'topic' ),
					'posts_per_page' => -1,
					'post_status'    => [ 'publish', 'draft' ],
					'fields'         => 'ids',
				];

				$all_topics = new WP_Query( $args );
				if ( ! empty( $all_topics->posts ) ) {
					$topics_list = array_unique( array_merge( $topics_list, $all_topics->posts ) );
				}

				if ( ! empty( $topic_steps ) ) {
					$topics_list = array_diff( $topics_list, $topic_steps );
				}
			} else {
				// First get topics associated with some instructor courses.
				$instructor_courses = ir_get_instructor_complete_course_list( $current_user_id );

				foreach ( $instructor_courses as $course_id ) {
					// Skip current course.
					if ( absint( $course_id ) === absint( $course->ID ) ) {
						continue;
					}
					$course_topics = learndash_get_topic_list( null, $course_id );
					if ( ! empty( $course_topics ) ) {
						$topics_list = array_merge( $topics_list, wp_list_pluck( $course_topics, 'ID' ) );
					}
				}

				// Fetch orphan topics.
				$args = [
					'post_type'      => learndash_get_post_type_slug( 'topic' ),
					'posts_per_page' => -1,
					'post_status'    => [ 'publish', 'draft' ],
					'author'         => $current_user_id,
					'fields'         => 'ids',
				];

				$orphan_topics = new WP_Query( $args );

				if ( ! empty( $orphan_topics->posts ) ) {
					$topics_list = array_unique( array_merge( $topics_list, $orphan_topics->posts ) );
				}

				if ( ! empty( $topic_steps ) ) {
					$topics_list = array_diff( $topics_list, $topic_steps );
				}
			}

			foreach ( $topics_list as $topic_id ) {
				$topic_details = get_post( $topic_id );

				if ( null !== $topic_details ) {
					$data[ $topic_id ] = [
						'title'     => $topic_details->post_title,
						'timestamp' => strtotime( $topic_details->post_modified ),
						'status'    => $topic_details->post_status,
					];

					if ( ! learndash_is_course_shared_steps_enabled() && ! is_null( get_post( (int) learndash_get_setting( $topic_id, 'course' ) ) ) ) {
						unset( $data[ $topic_id ] );
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
		 * Get other topics permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_other_topics_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if instructor course.
			$course_id              = absint( $request['course'] );
			$instructor_course_list = ir_get_instructor_complete_course_list( $current_user_id, 1 );

			if ( ! in_array( $course_id, $instructor_course_list ) ) {
				return new WP_Error( 'ir_rest_not_allowed', esc_html__( 'You do not have access to this post.', 'wdm_instructor_role' ), [ 'status' => 403 ] );
			}

			// Check if LearnDash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Get list of other quizzes.
		 *
		 * @param WP_REST_Request $request  WP_REST_Request instance.
		 */
		public function get_other_quizzes( $request ) {
			$data            = [];
			$quiz_list       = [];
			$course_quiz     = [];
			$orphan_quiz     = [];
			$course          = get_post( absint( $request['course'] ) );
			$current_user_id = get_current_user_id();

			if ( empty( $course ) || ! $course instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid Course ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			$course_quizzes = learndash_get_course_steps( $course->ID, [ 'sfwd-quiz' ] );

			if ( current_user_can( 'manage_options' ) ) {
				// Fetch all quizzes.
				$args = [
					'post_type'      => learndash_get_post_type_slug( 'quiz' ),
					'posts_per_page' => -1,
					'post_status'    => [ 'publish', 'draft' ],
					'fields'         => 'ids',
				];

				$all_quizzes = new WP_Query( $args );
				if ( ! empty( $all_quizzes->posts ) ) {
					$quiz_list = array_unique( array_merge( $quiz_list, $all_quizzes->posts ) );
				}

				if ( ! empty( $course_quizzes ) ) {
					$quiz_list = array_diff( $quiz_list, $course_quizzes );
				}
			} else {
				// First get quiz associated with some instructor courses.
				$instructor_courses = ir_get_instructor_complete_course_list( $current_user_id );

				foreach ( $instructor_courses as $course_id ) {
					// Skip current course.
					if ( absint( $course_id ) === absint( $course->ID ) ) {
						continue;
					}
					$course_quiz = learndash_get_course_quiz_list( $course_id );
					if ( ! empty( $course_quiz ) ) {
						$quiz_list = array_merge( $quiz_list, wp_list_pluck( $course_quiz, 'id' ) );
					}
				}

				// Fetch orphan quiz.
				$args = [
					'post_type'      => learndash_get_post_type_slug( 'quiz' ),
					'posts_per_page' => -1,
					'post_status'    => [ 'publish', 'draft' ],
					'author'         => $current_user_id,
					'fields'         => 'ids',
				];

				$orphan_quiz = new WP_Query( $args );

				if ( ! empty( $orphan_quiz->posts ) ) {
					$quiz_list = array_unique( array_merge( $quiz_list, $orphan_quiz->posts ) );
				}

				if ( ! empty( $course_quizzes ) ) {
					$quiz_list = array_diff( $quiz_list, $course_quizzes );
				}
			}

			foreach ( $quiz_list as $quiz_id ) {
				$quiz_details = get_post( $quiz_id );

				if ( null !== $quiz_details ) {
					$data[ $quiz_id ] = [
						'title'     => $quiz_details->post_title,
						'timestamp' => strtotime( $quiz_details->post_modified ),
						'status'    => $quiz_details->post_status,
					];

					if ( ! learndash_is_course_shared_steps_enabled() && ! is_null( get_post( (int) learndash_get_setting( $quiz_id, 'course' ) ) ) ) {
						unset( $data[ $quiz_id ] );
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
		 * Get other quizzes permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_other_quizzes_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if instructor course.
			$course_id              = absint( $request['course'] );
			$instructor_course_list = ir_get_instructor_complete_course_list( $current_user_id, 1 );

			if ( ! in_array( $course_id, $instructor_course_list ) ) {
				return new WP_Error( 'ir_rest_not_allowed', esc_html__( 'You do not have access to this post.', 'wdm_instructor_role' ), [ 'status' => 403 ] );
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Get other quizzes permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function set_quiz_settings_permissions_handle( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			// Shared quiz permissions.
			$course_id = learndash_get_course_id( $request['id'] );
			if ( $course_id ) {
				$shared_instructor_list = get_post_meta( $course_id, 'ir_shared_instructor_ids', 1 );
				// @phpstan-ignore-next-line
				$shared_instructor_ids = explode( ',', $shared_instructor_list );
				if ( in_array( $current_user_id, $shared_instructor_ids ) ) {
					return true;
				}
			}

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if the quiz belongs to the current user.
			if ( (int) get_post_field( 'post_author', $request['id'] ) !== (int) $current_user_id ) {
				return new WP_Error( 'ir_rest_quiz_invalid', esc_html__( 'Invalid quiz ID or permission.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Get other quizzes permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function set_question_settings_permissions_handle( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Shared quiz permissions.
			$related_quiz = get_post_meta( $request['id'], '_sfwd-question', true );
			foreach ( $related_quiz as $quiz_id ) {
				$course_id = learndash_get_course_id( $quiz_id );
				if ( $course_id ) {
					$shared_instructor_list = get_post_meta( $course_id, 'ir_shared_instructor_ids', 1 );
					// @phpstan-ignore-next-line
					$shared_instructor_ids = explode( ',', $shared_instructor_list );
					if ( in_array( $current_user_id, $shared_instructor_ids ) ) {
						return true;
					}
				}
			}

			// Shared quiz permissions.
			if ( isset( $request['quiz'] ) ) {
				$course_id = learndash_get_course_id( $request['quiz'] );
				if ( $course_id ) {
					$shared_instructor_list = get_post_meta( $course_id, 'ir_shared_instructor_ids', 1 );
					// @phpstan-ignore-next-line
					$shared_instructor_ids = explode( ',', $shared_instructor_list );
					if ( in_array( $current_user_id, $shared_instructor_ids ) ) {
						return true;
					}
				}
			}

			if ( isset( $request['id'] ) ) {
				$question_post_id = $request['id'];
				// Check if the quiz belongs to the current user.
				if ( empty( $question_post_id ) || ( (int) get_post_field( 'post_author', $question_post_id ) !== $current_user_id ) ) {
					return new WP_Error( 'ir_rest_question_permission', esc_html__( 'You don\'t have permission to edit this question.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
				}
			}

			if ( isset( $request['quiz'] ) ) {
				$quiz_post_id = $request['quiz'];
				// Check if the quiz belongs to the current user.
				if ( empty( $quiz_post_id ) || ( (int) get_post_field( 'post_author', $quiz_post_id ) !== (int) $current_user_id ) ) {
					return new WP_Error( 'ir_rest_quiz_permission', esc_html__( 'You don\'t have permission to edit this quiz.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
				}
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Get other quizzes permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function fetch_question_by_id_permissions_handle( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}
			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Shared quiz permissions.
			if ( isset( $request['quiz'] ) ) {
				$course_id = learndash_get_course_id( $request['quiz'] );
				if ( $course_id ) {
					$shared_instructor_list = get_post_meta( $course_id, 'ir_shared_instructor_ids', 1 );
					// @phpstan-ignore-next-line
					$shared_instructor_ids = explode( ',', $shared_instructor_list );
					if ( in_array( $current_user_id, $shared_instructor_ids ) ) {
						return true;
					}
				}
			}

			$related_quiz = get_post_meta( $request['id'], '_sfwd-question', true );
			foreach ( $related_quiz as $quiz_id ) {
				$course_id = learndash_get_course_id( $quiz_id );
				if ( $course_id ) {
					$shared_instructor_list = get_post_meta( $course_id, 'ir_shared_instructor_ids', 1 );
					// @phpstan-ignore-next-line
					$shared_instructor_ids = explode( ',', $shared_instructor_list );
					if ( in_array( $current_user_id, $shared_instructor_ids ) ) {
						return true;
					}
				}
			}

			if ( isset( $request['id'] ) ) {
				$question_post_id = $request['id'];
				// Check if the quiz belongs to the current user.
				if ( empty( $question_post_id ) || ( (int) get_post_field( 'post_author', $question_post_id ) !== (int) $current_user_id ) ) {
					return new WP_Error( 'ir_rest_question_permission', esc_html__( 'You don\'t have permission to edit this question.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
				}
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Get other quizzes permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_question_library_permissions_handle( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Get course users
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_course_users( $request ) {
			$data = [];

			$course = get_post( absint( $request['id'] ) );

			if ( empty( $course ) || ! $course instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			$users = learndash_get_users_for_course( $course->ID );
			if ( ! empty( $users ) && ! is_array( $users ) ) {
				$users = $users->get_results();
			}
			$user = false;
			foreach ( $users as $user_id ) {
				$user = get_userdata( $user_id );
				if ( false !== $user ) {
					array_push(
						$data,
						[
							'id'   => $user_id,
							'name' => $user->display_name,
						]
					);
				}
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get course users permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_course_users_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if instructor course.
			$course_id              = absint( $request['id'] );
			$instructor_course_list = ir_get_instructor_complete_course_list( $current_user_id, 1 );

			if ( ! in_array( $course_id, $instructor_course_list ) ) {
				return new WP_Error( 'ir_rest_not_allowed', esc_html__( 'You do not have access to this post.', 'wdm_instructor_role' ), [ 'status' => 403 ] );
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Update course users
		 *
		 * @since 4.4.0
		 *
		 * @param WP_REST_Request<array{id: int, user_ids: int[]}> $request WP_REST_Request instance.
		 *
		 * @return WP_REST_Response|WP_Error
		 */
		public function update_course_users( $request ) {
			$data = [];

			$course = get_post( absint( $request['id'] ) );

			if ( empty( $course ) || ! $course instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			$user_ids = $request->get_param( 'user_ids' );

			if ( ! is_array( $user_ids ) ) {
				$user_ids = [];
			}

			$user_ids = array_map( 'absint', $user_ids );

			learndash_set_users_for_course( $course->ID, $user_ids );

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Update course users permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_course_users_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if instructor course.
			$course_id              = absint( $request['id'] );
			$instructor_course_list = ir_get_instructor_complete_course_list( $current_user_id, 1 );

			if ( ! in_array( $course_id, $instructor_course_list ) ) {
				return new WP_Error( 'ir_rest_not_allowed', esc_html__( 'You do not have access to this post.', 'wdm_instructor_role' ), [ 'status' => 403 ] );
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Get exams.
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_exams( $request ) {
			$data = [];

			$args = [
				'post_type'      => learndash_get_post_type_slug( 'exam' ),
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			];

			if ( ! current_user_can( 'manage_options' ) ) {
				$args['author'] = get_current_user_id();
			}

			$query = new WP_Query( $args );
			if ( $query->have_posts() ) {
				foreach ( $query->posts as $post ) {
					array_push(
						$data,
						[
							'value' => $post->ID,
							'label' => $post->post_title,
						]
					);
				}
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get exams permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_exams_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Get certificates.
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_certificates( $request ) {
			$data = [];

			$data = $this->get_ld_certificates();

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get certificates permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_certificates_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( learndash_is_group_leader_user( $current_user_id ) ) {
				return true;
			}

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Update API User Query to send all users
		 *
		 * @since 4.4.0
		 *
		 * @param array  $prepared_args     Prepared Arguments for the query.
		 * @param object $request           The Rest API Request object.
		 * @return array
		 */
		public function update_api_user_query( $prepared_args, $request ) {
			if ( array_key_exists( 'has_published_posts', $prepared_args ) ) {
				unset( $prepared_args['has_published_posts'] );
			}
			return $prepared_args;
		}

		/**
		 * Get site instructors.
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructors( $request ) {
			$data = [];

			$user_query = new WP_User_Query(
				[
					'role'    => 'wdm_instructor',
					'exclude' => [ get_current_user_id() ],
				]
			);

			if ( ! empty( $user_query->get_results() ) ) {
				foreach ( $user_query->get_results() as $user ) {
					array_push(
						$data,
						[
							'value' => strval( $user->ID ),
							'label' => $user->data->display_name,
						]
					);
				}
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get site instructors permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructors_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Get site instructors permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_category_permissions_check( $request ) {
			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Get course instructors.
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_course_instructors( $request ) {
			$data = [];

			$course = get_post( absint( $request['id'] ) );

			if ( empty( $course ) || ! $course instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			$shared_instructor_list = get_post_meta( $course->ID, 'ir_shared_instructor_ids', 1 );
			// @phpstan-ignore-next-line
			$shared_instructor_ids = explode( ',', $shared_instructor_list );

			if ( ! empty( $shared_instructor_ids ) ) {
				foreach ( $shared_instructor_ids as $user_id ) {
					$user_data = get_userdata( $user_id );

					// Skip user if not found.
					if ( false === $user_data ) {
						continue;
					}

					array_push(
						$data,
						[
							'value' => $user_id,
							'label' => $user_data->display_name,
						]
					);
				}
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get course instructors permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_course_instructors_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Update course instructors.
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_course_instructors( $request ) {
			$course = get_post( absint( $request['id'] ) );

			if ( empty( $course ) || ! $course instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			$shared_instructor_ids = $request['shared_instructors'];

			// Delete old meta.
			// Get the instructors with shared course access.
			$shared_instructors_list = get_post_meta( $course->ID, 'ir_shared_instructor_ids', 1 );
			// @phpstan-ignore-next-line
			$shared_instructors = explode( ',', $shared_instructors_list );

			// Remove shared course access from instructor meta.
			foreach ( $shared_instructors as $co_instructor_id ) {
				// Get shared course list for the instructor.
				$instructor_shared_courses = ir_get_instructor_shared_course_list( $co_instructor_id );

				// Check if current course is shared and get its key index.
				$key = array_search( $course->ID, $instructor_shared_courses );

				// Remove the course id from the list.
				if ( false === $key ) {
					continue;
				}

				// Remove course id from instructor course share list.
				unset( $instructor_shared_courses[ $key ] );

				// Update instructor shared course meta.
				$shared_course_list = implode( ',', $instructor_shared_courses );
				update_user_meta( $co_instructor_id, 'ir_shared_courses', $shared_course_list );
			}

			// Remove shared course access from course meta.
			delete_post_meta( $course->ID, 'ir_shared_instructor_ids' );

			// Save list of co-instructors in course meta.
			$shared_instructor_list = implode( ',', $shared_instructor_ids );
			$status                 = update_post_meta( $course->ID, 'ir_shared_instructor_ids', $shared_instructor_list );

			// Save course id in co-instructor's usermeta.
			foreach ( $shared_instructor_ids as $co_instructor_id ) {
				$shared_courses = ir_get_instructor_shared_course_list( $co_instructor_id );
				if ( in_array( $course->ID, $shared_courses ) ) {
					continue;
				}

				array_push( $shared_courses, $course->ID );
				$shared_course_list = '';
				$shared_course_list = implode( ',', $shared_courses );

				update_user_meta( $co_instructor_id, 'ir_shared_courses', $shared_course_list );
				ir_refresh_shared_course_details( $co_instructor_id );
			}

			if ( $status ) {
				$data = $shared_instructor_ids;
			} else {
				$data = [];
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Update course instructors permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_course_instructors_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Update remaining course details permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_remaining_course_details_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Update group course details permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_group_course_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Update remaining course details.
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_remaining_course_details( $request ) {
			$data   = [];
			$course = get_post( absint( $request['id'] ) );
			$format = 'Y-m-d\\TH:i:sP';

			if ( empty( $course ) || ! $course instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			$course_settings = learndash_get_course_meta_setting( $course->ID );
			if ( 'subscribe' === $request['type'] ) {
				$data['price_type_subscribe_price_billing_cycle']    = learndash_update_setting( $course->ID, 'course_price_billing_p3', sanitize_text_field( $request['price_type_subscribe_price_billing_cycle'] ) );
				$data['price_type_subscribe_price_billing_interval'] = learndash_update_setting( $course->ID, 'course_price_billing_t3', sanitize_text_field( $request['price_type_subscribe_price_billing_interval'] ) );
				$data['trial_duration']                              = learndash_update_setting( $course->ID, 'course_trial_duration_p1', sanitize_text_field( $request['trial_duration'] ) );
				$data['trial_duration_interval']                     = learndash_update_setting( $course->ID, 'course_trial_duration_t1', sanitize_text_field( $request['trial_duration_interval'] ) );

				$course_settings[ learndash_get_post_type_slug( 'course' ) . '_course_price_type_subscribe_enrollment_url' ] = $request['price_type_subscribe_enrollment_url'];

				$course_settings[ learndash_get_post_type_slug( 'course' ) . '_course_no_of_cycles' ] = $request['price_type_subscribe_billing_recurring_times'];

				$data['subscribe_enroll_url_and_recurring_times'] = update_post_meta( $course->ID, '_sfwd-courses', $course_settings );

				// Check if course published for first time.
				if ( $request['is_published'] ) {
					update_post_meta( $course->ID, 'ir_fcc_is_published', true );
				}
			}

			if ( 'pagination' === $request['type'] ) {
				$course_settings[ learndash_get_post_type_slug( 'course' ) . '_course_lesson_per_page' ] = ( $request['lessons_per_page'] ) ? true : false;

				$data['lessons_per_page'] = update_post_meta( $course->ID, '_sfwd-courses', $course_settings );
			}

			if ( 'release_schedule' == $request['type'] ) {
				$lesson_id = intval( $request['lesson_id'] );
				$meta_key  = '_sfwd-lessons';
				$pre_fix   = learndash_get_post_type_slug( 'lesson' );
				if ( 'topic' === $request['post_type'] ) {
					$meta_key = '_sfwd-topic';
					$pre_fix  = learndash_get_post_type_slug( 'topic' );
				}
				$lesson_settings = get_post_meta( $lesson_id, $meta_key, true );
				$lesson_settings[ $pre_fix . '_visible_after_specific_date' ] = $request['specific_date'];

				$data['visible_after_specific_date'] = update_post_meta( $lesson_id, $meta_key, $lesson_settings );
			}

			// Update Course Seats limit.
			if ( ! empty( $request['course_seats_limit'] ) ) {
				$data['course_seats_limit'] = learndash_update_setting( $course->ID, 'course_seats_limit', sanitize_text_field( $request['course_seats_limit'] ) );
			}

			// Update Course Start date.
			if ( ! empty( $request['course_start_date'] ) ) {
				$start_date                = ir_get_date_in_site_timezone( $request['course_start_date'], $format );
				$data['course_start_date'] = learndash_update_setting( $course->ID, 'course_start_date', sanitize_text_field( strtotime( $start_date ) ) );
			}

			// Update Course end date.
			if ( ! empty( $request['course_end_date'] ) ) {
				$end_date                = ir_get_date_in_site_timezone( $request['course_end_date'], $format );
				$data['course_end_date'] = learndash_update_setting( $course->ID, 'course_end_date', sanitize_text_field( strtotime( $end_date ) ) );
			}

			// Update group extend access settings.
			if ( ! empty( $request['new_expiration_date'] ) ) {
				if ( ! empty( $request['group_users_to_extend_access'] ) ) {
					$extend_user_ids = array_filter(
						array_map(
							function ( $user_id ) {
								return filter_var( trim( $user_id ), FILTER_VALIDATE_INT );
							},
							$request['group_users_to_extend_access'],
						)
					);

					$new_expiration_date = ir_get_date_in_site_timezone( $request['new_expiration_date'], $format );

					if ( ! empty( $extend_user_ids ) && ! empty( $new_expiration_date ) && function_exists( 'learndash_course_extend_user_access' ) ) {
							learndash_course_extend_user_access( $course->ID, $extend_user_ids, strtotime( $new_expiration_date ) );
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
		 * Update group course details.
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_group_course( $request ) {
			$course_id = $request['id'];
			if ( empty( $course_id ) ) {
				return new WP_Error(
					'rest_post_invalid_id',
					sprintf(
						// translators: placeholder: Course.
						esc_html_x(
							'Invalid %s ID.',
							'placeholder: Course',
							'learndash'
						),
						\LearnDash_Custom_Label::get_label( 'course' )
					),
					[ 'status' => 404 ]
				);
			}

			$course_post = get_post( $course_id );
			if ( ( ! $course_post ) || ( ! is_a( $course_post, 'WP_Post' ) ) || ( learndash_get_post_type_slug( 'course' ) !== $course_post->post_type ) ) {
				return new WP_Error(
					'rest_post_invalid_id',
					sprintf(
						// translators: placeholder: Course.
						esc_html_x(
							'Invalid %s ID.',
							'placeholder: Course',
							'learndash'
						),
						\LearnDash_Custom_Label::get_label( 'course' )
					),
					[ 'status' => 404 ]
				);
			}

			$group_ids = $request['group_ids'];
			if ( ( ! is_array( $group_ids ) ) || ( empty( $group_ids ) ) ) {
				return new WP_Error(
					'rest_post_invalid_id',
					sprintf(
						// translators: placeholder: Group.
						esc_html_x(
							'Missing %s IDs.',
							'placeholder: Group',
							'learndash'
						),
						\LearnDash_Custom_Label::get_label( 'group' )
					),
					[
						'status' => 404,
					]
				);
			}
			$group_ids = array_map( 'absint', $group_ids );
			$data      = [];

			foreach ( $group_ids as $group_id ) {
				if ( empty( $group_id ) ) {
					continue;
				}

				$group_post = get_post( $group_id );
				if ( ( ! $group_post ) || ( ! is_a( $group_post, 'WP_Post' ) ) || ( learndash_get_post_type_slug( 'group' ) !== $group_post->post_type ) ) {
					$data_item['group_id'] = $group_id;
					$data_item['status']   = 'failed';
					$data_item['code']     = 'learndash_rest_invalid_id';
					$data_item['message']  = sprintf(
						// translators: placeholder: Group.
						esc_html_x(
							'Invalid %s ID.',
							'placeholder: Group',
							'learndash'
						),
						\LearnDash_Custom_Label::get_label( 'group' )
					);
					$data[] = $data_item;

					continue;
				}

				$ret = ld_update_course_group_access( $course_id, $group_id, false );
				if ( true === $ret ) {
					$data_item['group_id'] = $group_id;
					$data_item['status']   = 'success';
					$data_item['code']     = 'learndash_rest_enroll_success';
					$data_item['message']  = sprintf(
						// translators: placeholder: Course, Group.
						esc_html_x(
							'%1$s enrolled in %2$s success.',
							'placeholder: Course, Group',
							'learndash'
						),
						\LearnDash_Custom_Label::get_label( 'course' ),
						\LearnDash_Custom_Label::get_label( 'group' )
					);
				} else {
					$data_item['group_id'] = $group_id;
					$data_item['status']   = 'failed';
					$data_item['code']     = 'learndash_rest_enroll_failed';
					$data_item['message']  = sprintf(
						// translators: placeholder: Course, Group.
						esc_html_x(
							'%1$s already enrolled in %2$s.',
							'placeholder: Course, Group',
							'learndash'
						),
						\LearnDash_Custom_Label::get_label( 'course' ),
						\LearnDash_Custom_Label::get_label( 'group' )
					);
				}
				$data[] = $data_item;
			}

			// Remove groups not passed.
			$groups = learndash_get_course_groups( $course_id );
			$groups = array_map( 'absint', $groups );
			asort( $groups );
			$groups = array_values( $groups );
			asort( $group_ids );
			$group_ids = array_values( $group_ids );

			// Get the elements that are in array two but not in array one.
			$group_ids_diff = array_diff( $groups, $group_ids );

			foreach ( $group_ids_diff as $group_id ) {
				if ( empty( $group_id ) ) {
					continue;
				}

				$ret = ld_update_course_group_access( $course_id, $group_id, true );
				if ( true === $ret ) {
					$data_item['group_id'] = $group_id;
					$data_item['status']   = 'success';
					$data_item['code']     = 'learndash_rest_unenroll_success';
					$data_item['message']  = sprintf(
						// translators: placeholder: Course, Group.
						esc_html_x(
							'%1$s unenrolled in %2$s success.',
							'placeholder: Course, Group',
							'learndash'
						),
						\LearnDash_Custom_Label::get_label( 'course' ),
						\LearnDash_Custom_Label::get_label( 'group' )
					);
				} else {
					$data_item['group_id'] = $group_id;
					$data_item['status']   = 'failed';
					$data_item['code']     = 'learndash_rest_unenroll_failed';
					$data_item['message']  = sprintf(
						// translators: placeholder: Course, Group.
						esc_html_x(
							'%1$s already unenrolled in %2$s.',
							'placeholder: Course, Group',
							'learndash'
						),
						\LearnDash_Custom_Label::get_label( 'course' ),
						\LearnDash_Custom_Label::get_label( 'group' )
					);
				}
				$data[] = $data_item;
			}

			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}


		/**
		 * Retrieves a course users.
		 *
		 * @since 3.3.0
		 *
		 * @param WP_REST_Request $request Full details about the request.
		 *
		 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
		 */
		public function get_group_course( $request ) {
			$data      = [];
			$course_id = $request['id'];
			$groups    = learndash_get_course_groups( $course_id );
			$post      = null;

			foreach ( $groups as $group ) {
				$post = get_post( $group );
				if ( ! $post instanceof WP_Post ) {
					continue;
				}
				$data[] = [
					'id'    => $post->ID,
					'title' => [
						'rendered' => $post->post_title,
					],
				];
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Fetch remaining course details.
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function fetch_remaining_course_details( $request ) {
			$data   = [];
			$course = get_post( absint( $request['id'] ) );

			if ( empty( $course ) || ! $course instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			$data['price_type_subscribe_price_billing_cycle']    = learndash_get_setting( $course->ID, 'course_price_billing_p3' );
			$data['price_type_subscribe_price_billing_interval'] = learndash_get_setting( $course->ID, 'course_price_billing_t3' );
			$data['trial_duration']                              = learndash_get_setting( $course->ID, 'course_trial_duration_p1' );
			$data['trial_duration_interval']                     = learndash_get_setting( $course->ID, 'course_trial_duration_t1' );
			$data['course_seats_limit']                          = learndash_get_setting( $course->ID, 'course_seats_limit' );
			$data['category_restrict_check']                     = ir_admin_settings_check( 'ir_ld_category_check' );
			$ir_admin_settings                                   = get_option( '_wdmir_admin_settings', [] );
			$data['category_hide_check']                         = isset( $ir_admin_settings['enable_ld_category'] ) ? $ir_admin_settings['enable_ld_category'] : '';
			$data['enable_open_pricing']                         = isset( $ir_admin_settings['enable_open_pricing'] ) ? $ir_admin_settings['enable_open_pricing'] : '';
			$data['enable_free_pricing']                         = isset( $ir_admin_settings['enable_free_pricing'] ) ? $ir_admin_settings['enable_free_pricing'] : '';
			$data['enable_buy_pricing']                          = isset( $ir_admin_settings['enable_buy_pricing'] ) ? $ir_admin_settings['enable_buy_pricing'] : '';
			$data['enable_recurring_pricing']                    = isset( $ir_admin_settings['enable_recurring_pricing'] ) ? $ir_admin_settings['enable_recurring_pricing'] : '';
			$data['enable_closed_pricing']                       = isset( $ir_admin_settings['enable_closed_pricing'] ) ? $ir_admin_settings['enable_closed_pricing'] : '';
			$data['enable_permalinks']                           = isset( $ir_admin_settings['enable_permalinks'] ) ? $ir_admin_settings['enable_permalinks'] : '';

			$course_settings = learndash_get_course_meta_setting( $course->ID );

			// Custom Pagination.
			// @phpstan-ignore-next-line.
			if ( ! empty( $course_settings ) && array_key_exists( learndash_get_post_type_slug( 'course' ) . '_course_lesson_per_page', $course_settings ) ) {
				$data['lessons_per_page'] = $course_settings[ learndash_get_post_type_slug( 'course' ) . '_course_lesson_per_page' ];
			}

			// Recurring cycles.
			// @phpstan-ignore-next-line.
			if ( ! empty( $course_settings ) && array_key_exists( learndash_get_post_type_slug( 'course' ) . '_course_no_of_cycles', $course_settings ) ) {
				$data['price_type_subscribe_billing_recurring_times'] = $course_settings[ learndash_get_post_type_slug( 'course' ) . '_course_no_of_cycles' ];
			}

			// Start Date.
			$data['course_start_date'] = '';
			if ( false != learndash_get_setting( $course->ID, 'course_start_date' ) && ! empty( learndash_get_setting( $course->ID, 'course_start_date' ) ) && 0 != learndash_get_setting( $course->ID, 'course_start_date' ) ) {
				// @phpstan-ignore-next-line
				$data['course_start_date'] = strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s', learndash_get_setting( $course->ID, 'course_start_date' ) ) ) );
			}

			// End Date.
			$data['course_end_date'] = '';
			if ( false != learndash_get_setting( $course->ID, 'course_end_date' ) && ! empty( learndash_get_setting( $course->ID, 'course_end_date' ) ) && 0 != learndash_get_setting( $course->ID, 'course_end_date' ) ) {
				// @phpstan-ignore-next-line
				$data['course_end_date'] = strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s', learndash_get_setting( $course->ID, 'course_end_date' ) ) ) );
			}

			// Check if course already published.
			$data['is_published'] = get_post_meta( $course->ID, 'ir_fcc_is_published', 1 );

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Update quiz settings.
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_quiz_settings( $request ) {
			$quiz = get_post( $request['id'] );

			if ( empty( $quiz ) || ! $quiz instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			$quiz_id  = $quiz->ID;
			$data     = [];
			$quiz_pro = (int) learndash_get_setting( $quiz_id, 'quiz_pro' );
			// Database Processing.
			global $wpdb;

			// Get the Custom data from database.
			$table_name = LDLMS_DB::get_table_name( 'quiz_master' );
			$row        = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT
						*
					FROM
						' . $table_name . '
					WHERE
						id = %d',
					$quiz_pro
				),
				ARRAY_A
			);
			// result_text data update.
			$quiz_model = new WpProQuiz_Model_Quiz( $row );

			// Save result text as is.
			$quiz_model->setResultText( maybe_unserialize( $row['result_text'] ) );

			$quiz_mapper = new WpProQuiz_Model_QuizMapper();

			// Prevent settings.
			$quiz_model->setViewProfileStatistics( $quiz_model->getViewProfileStatistics() );

			// Get and set the data.
			if ( isset( $request['retries_applicable_to'] ) ) {
				$retries_applicable_to = (string) $request['retries_applicable_to'];

				learndash_update_setting( $quiz_id, 'quizRunOnce', $retries_applicable_to );
				learndash_update_setting( $quiz_id, 'quizRunOnceType', $retries_applicable_to );
				$quiz_model->setQuizRunOnce( $retries_applicable_to );
				$quiz_model->setQuizRunOnceType( $retries_applicable_to );
			}

			$data['email_notification'] = $row['email_notification'];
			$email_notification         = $row['email_notification'];
			if ( isset( $request['email_admin_enabled'] ) ) {
				$email_notification         = filter_var( $request['email_admin_enabled'], FILTER_VALIDATE_INT );
				$data['email_notification'] = $email_notification;// Admin email notification eg 2 (ADMIN).
			}

			$user_email_notification = null;
			if ( isset( $request['email_user_enabled'] ) ) {
				$user_email_notification = $request['email_user_enabled'];
				$user_email_notification = filter_var( $request['email_user_enabled'], FILTER_VALIDATE_BOOLEAN );
				if ( $user_email_notification ) {
					$user_email_notification = (int) 1;
				} else {
					$user_email_notification = (int) 0;
				}
				$data['user_email_notification'] = $user_email_notification;// user email eg 1 notification (USER).
			}

			$statistics_ip_lock = 0;
			if ( isset( $request['statistics_ip_lock_enabled_value'] ) ) {
				$statistics_ip_lock = filter_var( $request['statistics_ip_lock_enabled_value'], FILTER_VALIDATE_INT );
				if ( 0 === (int) $request['statistics_ip_lock_enabled'] ) {
					$statistics_ip_lock = 0;
				}
				$data['statistics_ip_lock'] = $statistics_ip_lock;// time in minutes sec converted 0 means inactive eg 60.
				$quiz_model->setStatisticsIpLock( $statistics_ip_lock );
			}

			$question_random = $quiz_model->getQuestionRandom();
			if ( isset( $request['question_random'] ) ) {
				$question_random = $request['question_random'];
				$question_random = filter_var( $request['question_random'], FILTER_VALIDATE_BOOLEAN );
				if ( $question_random ) {
					$question_random = (int) 1;
				} else {
					$question_random = (int) 0;
				}
				$quiz_model->setQuestionRandom( $question_random );
			}

			$show_max_question = (bool) $row['show_max_question'];
			if ( isset( $request['show_max_question'] ) ) {
				$show_max_question = $request['show_max_question'];
				$show_max_question = filter_var( $request['show_max_question'], FILTER_VALIDATE_BOOLEAN );
				if ( $show_max_question ) {
					$show_max_question       = (int) 1;
					$data['question_random'] = 1;
				} else {
					$show_max_question = (int) 0;
				}
				$quiz_model->setShowMaxQuestion( $show_max_question );
			}

			$show_max_question_value = $quiz_model->getShowMaxQuestionValue();
			if ( isset( $request['show_max_question_value'] ) ) {
				$show_max_question_value = filter_var( $request['show_max_question_value'], FILTER_VALIDATE_INT );
				$quiz_model->setShowMaxQuestionValue( $show_max_question_value );
			}

			if ( isset( $request['questions_per_page'] ) ) {
				$questions_per_page         = filter_var( $request['questions_per_page'], FILTER_VALIDATE_INT );
				$data['questions_per_page'] = $questions_per_page;
			}

			if ( isset( $request['toplist_data_time'] ) ) {
				$toplist_data_time = filter_var( $request['toplist_data_time'], FILTER_VALIDATE_INT );
				$quiz_model->setToplistDataAddBlock( $toplist_data_time );
			}

			if ( isset( $request['toplist_data_showin'] ) ) {
				$toplist_data_showin = filter_var( $request['toplist_data_showin'], FILTER_VALIDATE_INT );
				if ( 1 === $toplist_data_showin || 2 === $toplist_data_showin ) {
					$quiz_model->setToplistDataShowIn( $toplist_data_showin );
				} else {
					$quiz_model->setToplistDataShowIn( 0 );
				}
			}

			if ( isset( $request['really_simple_captcha'] ) ) {
				$really_simple_captcha = filter_var( $request['really_simple_captcha'], FILTER_VALIDATE_BOOLEAN );
				$quiz_model->setToplistDataCaptcha( $really_simple_captcha );
			}

			// Get quiz using default LD API.
			$route_url     = '/ldlms/v2/sfwd-quiz/' . $quiz_id;
			$quiz_request  = new \WP_REST_Request( 'GET', $route_url );
			$quiz_response = rest_do_request( $quiz_request );
			$server        = rest_get_server();
			$rest_data     = $server->response_to_data( $quiz_response, false );

			if ( is_wp_error( $rest_data ) || empty( $rest_data ) ) { // Returns error response.
				return new WP_Error( 'ir_rest_quiz_response_error', esc_html__( 'Quiz not found.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			$persist_result_text = $row['result_text'];
			if ( isset( $request['result_text'] ) ) {
				$persist_result_text = maybe_serialize( $request['result_text'] );
			}

			// Save additional data.

			// Save release visible after specific date value.
			if ( isset( $request['visible_after_specific_date'] ) ) {
				$visible_after_specific_date = $request['visible_after_specific_date'];

				learndash_update_setting( $quiz_id, 'visible_after_specific_date', $visible_after_specific_date );
			} else {
				$visible_after_specific_date = learndash_get_setting( $quiz_id, 'visible_after_specific_date' );
			}

			$where               = [ 'ID' => $quiz_pro ];
			$data['result_text'] = $persist_result_text;
			if ( ! empty( $data ) ) {
				$wpdb->update( $table_name, $data, $where );
			}

			if ( isset( $request['result_text_enabled'] ) ) {
				learndash_update_setting( $quiz_id, 'resultGradeEnabled', Cast::to_bool( $request['result_text_enabled'] ) );
			}

			// Check if quiz published for first time.
			if ( $request['is_published'] ) {
				update_post_meta( $quiz_id, 'ir_fqb_is_published', true );
			}

			// Check if browser cookie limit set.
			if ( isset( $request['time_limit_cookie'] ) ) {
				update_post_meta( $quiz_id, '_timeLimitCookie', $request['time_limit_cookie'] );
			}

			// Save quiz custom fields.
			if ( isset( $request['custom_fields'] ) ) {
				$this->set_quiz_custom_fields( $quiz_pro, $request['custom_fields'] );
			}

			if ( isset( $request['custom_fields_enabled'] ) ) {
				$custom_fields_enabled = Cast::to_bool( $request['custom_fields_enabled'] );

				learndash_update_setting( $quiz_id, 'formActivated', $custom_fields_enabled );
				$quiz_model->setFormActivated( $custom_fields_enabled );
			}

			if ( isset( $request['custom_field_display_position'] ) ) {
				$custom_field_display_position = Cast::to_int( $request['custom_field_display_position'] );

				learndash_update_setting( $quiz_id, 'formShowPosition', $custom_field_display_position );
				$quiz_model->setFormShowPosition( $custom_field_display_position );
			}

			// Save quiz as template.
			if ( isset( $request['save_as_template'] ) && ! empty( $request['save_as_template'] ) ) {
				if ( 'new' === $request['save_as_template'] ) {
					$this->save_quiz_as_template( $quiz_model, $request['new_template_name'], true );
				} else {
					$this->save_quiz_as_template( $quiz_model, $request['save_as_template'], false );
				}
			}

			$quiz_model = $quiz_mapper->save( $quiz_model );

			// Custom data.
			$rest_data['show_max_question']                = (bool) $show_max_question;
			$rest_data['show_max_question_value']          = $show_max_question_value;
			$rest_data['retries_applicable_to']            = (int) learndash_get_setting( $quiz_id, 'quizRunOnceType' );
			$rest_data['result_text_enabled']              = Cast::to_bool( learndash_get_setting( $quiz_id, 'resultGradeEnabled' ) );
			$rest_data['single_feedback']                  = learndash_get_setting( $quiz_id, 'quiz_quizModus_single_feedback' );
			$rest_data['single_back_button']               = learndash_get_setting( $quiz_id, 'quizModus_single_back_button' );
			$rest_data['multiple_questions_per_page']      = learndash_get_setting( $quiz_id, 'quizModus_multiple_questionsPerPage' );
			$rest_data['question_random']                  = (bool) $question_random;
			$rest_data['statistics_ip_lock_enabled']       = (bool) $statistics_ip_lock;
			$rest_data['statistics_ip_lock_enabled_value'] = (int) $statistics_ip_lock;
			$rest_data['email_admin_enabled']              = (int) $email_notification;
			$rest_data['email_user_enabled']               = (bool) $user_email_notification;
			$rest_data['toplist_data_time']                = $quiz_model->getToplistDataAddBlock();
			$rest_data['toplist_data_showin']              = $quiz_model->getToplistDataShowIn();
			$rest_data['really_simple_captcha']            = $quiz_model->isToplistDataCaptcha();
			if ( (bool) $email_notification || (bool) $user_email_notification ) {
				$rest_data['email_enabled'] = true;
			} else {
				$rest_data['email_enabled'] = false;
			}

			$response = rest_ensure_response( $rest_data );

			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Fetch quiz settings.
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function fetch_quiz_settings( $request ) {
			$quiz = get_post( $request['id'] );

			if ( empty( $quiz ) || ! $quiz instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			$quiz_id = $quiz->ID;

			// Get quiz using default LD API.
			$route_url     = '/ldlms/v2/sfwd-quiz/' . $quiz_id;
			$quiz_request  = new \WP_REST_Request( 'GET', $route_url );
			$quiz_response = rest_do_request( $quiz_request );
			$server        = rest_get_server();
			$rest_data     = $server->response_to_data( $quiz_response, false );

			if ( is_wp_error( $rest_data ) || empty( $rest_data ) ) { // Returns error response.
				return new WP_Error( 'ir_rest_quiz_response_error', esc_html__( 'Response error.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Checks if quiz is present and then updates the data.
			$quiz_pro = learndash_get_setting( $quiz_id, 'quiz_pro' );

			// Get the Custom data from database.
			global $wpdb;
			$table_name = LDLMS_DB::get_table_name( 'quiz_master' );
			$row        = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT
						*
					FROM
						' . $table_name . '
					WHERE
						id = %d',
					$quiz_pro
				),
				ARRAY_A
			);

			// result_text data update.
			$quiz_model      = new \WpProQuiz_Model_Quiz( $row );
			$question_mapper = new \WpProQuiz_Model_QuestionMapper();

			$questions       = $question_mapper->fetchAll( $quiz_model );
			$questions_count = 0;
			if ( ( is_array( $questions ) ) && ( ! empty( $questions ) ) ) {
				$questions_count = count( $questions );
			}

			$show_max_question_value                       = (int) $row['show_max_question_value'];
			$show_max_question                             = (bool) $row['show_max_question'];
			$rest_data['show_max_question']                = $show_max_question;
			$rest_data['statistics_ip_lock_enabled_value'] = (int) $row['statistics_ip_lock'];
			$rest_data['custom_fields_enabled']            = Cast::to_bool( learndash_get_setting( $quiz_id, 'formActivated' ) );
			$rest_data['show_max_question_value']          = $show_max_question_value;
			$rest_data['retries_applicable_to']            = (int) learndash_get_setting( $quiz_id, 'quizRunOnceType' );
			$rest_data['single_feedback']                  = learndash_get_setting( $quiz_id, 'quiz_quizModus_single_feedback' );
			$rest_data['single_back_button']               = learndash_get_setting( $quiz_id, 'quizModus_single_back_button' );
			$rest_data['multiple_questions_per_page']      = learndash_get_setting( $quiz_id, 'quizModus_multiple_questionsPerPage' );
			$rest_data['question_count']                   = (int) $questions_count;
			$rest_data['email_admin_enabled']              = (int) $row['email_notification'];
			$rest_data['email_user_enabled']               = (bool) $row['user_email_notification'];
			$rest_data['result_text_enabled']              = Cast::to_bool( learndash_get_setting( $quiz_id, 'resultGradeEnabled' ) );
			$rest_data['result_text']                      = $this->get_result_text( $row['result_text'] );
			$rest_data['toplist_data_time']                = $quiz_model->getToplistDataAddBlock();
			$rest_data['toplist_data_showin']              = $quiz_model->getToplistDataShowIn();
			$rest_data['really_simple_captcha']            = $quiz_model->isToplistDataCaptcha();

			// Check if quiz already published.
			$rest_data['is_published'] = get_post_meta( $quiz_id, 'ir_fqb_is_published', 1 );

			switch ( $quiz_model->getQuizModus() ) {
				case 0:
					$rest_data['quiz_modus']         = 'single';
					$rest_data['single_feedback']    = 'end';
					$rest_data['single_back_button'] = 'off';
					break;
				case 1:
					$rest_data['quiz_modus']         = 'single';
					$rest_data['single_feedback']    = 'end';
					$rest_data['single_back_button'] = 'on';
					break;
				case 2:
					$rest_data['quiz_modus']      = 'single';
					$rest_data['single_feedback'] = 'each';
					break;
				case 3:
					$rest_data['quiz_modus']                  = 'multiple';
					$rest_data['multiple_questions_per_page'] = $quiz_model->getQuestionsPerPage();
					break;
			}

			// Quiz Prerequisites.
			$rest_data['quiz_pre_requisites'] = $this->get_prereq_quizzes( $quiz );

			// Quiz certificates.
			$rest_data['quiz_certificates'] = $this->get_ld_certificates();

			// Browser Authentication Cookie.
			$rest_data['time_limit_cookie'] = get_post_meta( $quiz_id, '_timeLimitCookie', true );

			// Custom Fields.
			$rest_data['custom_fields'] = $this->get_quiz_custom_fields( $quiz_pro );

			$rest_data['custom_field_display_position'] = learndash_get_setting( $quiz_id, 'formShowPosition' );

			// Quiz Templates.
			$rest_data['quiz_templates'] = $this->get_quiz_templates( $quiz_pro );

			/**
			 * Filter the data returned on fetch quiz settings API.
			 *
			 * @since 4.5.1
			 *
			 * @param array  $rest_data    Response data returned.
			 * @param array  $request      Request data.
			 * @param object $quiz         LearnDash Quiz object.
			 */
			$rest_data = apply_filters( 'ir_filter_api_fetch_quiz_settings', $rest_data, $request, $quiz );

			$response = rest_ensure_response( $rest_data );
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Update quiz settings.
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_question_settings( $request ) {
			// Create an instance of the class.
			$question              = new \WpProQuiz_Model_Question();
			$question_mapper       = new \WpProQuiz_Model_QuestionMapper();
			$question_pro_id_array = get_post_meta( $request['id'], 'question_pro_id' );
			if (
				is_array( $question_pro_id_array )
				&& ! empty( $question_pro_id_array[0] )
			) {
				$question_pro_id = (int) $question_pro_id_array[0];
			} else {
				return new WP_Error( 'ir_rest_invalid_question_id', esc_html__( 'Please provide valid question id', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			$question_post_id = $request['id'];

			global $wpdb;

			if ( isset( $request['id'] ) ) {
				$question->setId( $question_pro_id );
			}

			$fields_map = [
				'_id'                             => 'id',
				'_quizId'                         => 'quiz',
				'_answerType'                     => 'question_type',
				'_points'                         => 'points_total',
				'_answerPointsActivated'          => 'points_per_answer',
				'_showPointsInBox'                => 'points_show_in_message',
				'_answerPointsDiffModusActivated' => 'points_diff_modus',
				'_disableCorrect'                 => 'disable_correct',
				'_correctMsg'                     => 'correct_message',
				'_incorrectMsg'                   => 'incorrect_message',
				'_correctSameText'                => 'correct_same',
				'_tipEnabled'                     => 'hints_enabled',
				'_tipMsg'                         => 'hints_message',
				'_answer_data'                    => 'answer_data',
				'_previousId'                     => 'previous_id',
				'_online'                         => 'online',
				'_sort'                           => 'menu_order',
				'_title'                          => 'title',
				'_question'                       => 'question',
				'_answerData'                     => 'answer_data',
				'_matrixSortAnswerCriteriaWidth'  => 'answer_criteria_width',
				'_questionPostId'                 => 'question_post_id',
			];
			if ( isset( $request['id'] ) ) {
				$question = fetchQuestionModel( $question_pro_id );
				$question_mapper->save( $question );
			}

			if ( null === $question || is_wp_error( $question ) ) {
				return new WP_Error(
					'ir_rest_invalid_question_id',
					esc_html__(
						'Please provide valid question id',
						'wdm_instructor_role'
					),
					[ 'status' => 401 ]
				);
			}

			$response_data = $question->get_object_as_array();
			foreach ( $fields_map as $key => $value ) {
				$new_response_data[ $fields_map[ $key ] ] = $value;
				if ( ! isset( $request['id'] ) || array_key_exists( $key, $response_data ) ) {
					switch ( $fields_map[ $key ] ) {
						case 'id':
							if ( isset( $request['id'] ) ) {
								$question->setId( $question_pro_id );
							}
							break;
						case 'menu_order':
							if ( isset( $request['menu_order'] ) ) {
								$question->setSort( $request['menu_order'] );
							}
							break;
						case 'title':
							if ( isset( $request['title'] ) ) {
								$question->setTitle( $request['title'] );
							}
							break;
						case 'points_total':
							if ( isset( $request['points_total'] ) ) {
								$question->setPoints( $request['points_total'] );
							}

							if ( isset( $request['question_type'] ) && 'assessment_answer' === $request['question_type'] && $request['answer_data'] ) {
								$answer_data = $request['answer_data'][0]['_answer'];
								$answer_data = learndash_question_assessment_fetch_data( $answer_data );
								$question->setPoints( max( $answer_data['points'] ) );
							}

							if ( isset( $request['question_type'] ) && 'cloze_answer' === $request['question_type'] && $request['answer_data'] && $request['points_per_answer'] ) {
								$answer_data = $request['answer_data'][0]['_answer'];
								$answer_data = learndash_question_cloze_fetch_data( $answer_data );

								$points     = 0;
								$max_points = 0;

								foreach ( $answer_data['points'] as $points_set ) {
									$item_points = 1;
									if ( ( is_array( $points_set ) ) && ( ! empty( $points_set ) ) ) {
										$item_points = max( $points_set );
									}

									$points    += $item_points;
									$max_points = max( $max_points, $item_points );
								}

								$question->setPoints( $max_points );
							}

							if ( isset( $request['question_type'] ) && 'free_answer' === $request['question_type'] && $request['answer_data'] && $request['points_per_answer'] ) {
								$points_answer_data = $request['answer_data'][0];
								$answer_type        = new \WpProQuiz_Model_AnswerTypes( $points_answer_data );
								$points_answer_data = learndash_question_free_get_answer_data( $answer_type );
								$max_points         = 0;
								$max_points         = max( $points_answer_data['points'] );
								$question->setPoints( $max_points );
							}

							// if points_per_answer is true sum all the answers.
							if ( ( 'matrix_sort_answer' === $request['question_type'] || 'sort_answer' === $request['question_type'] ) && $request['answer_data'] && $request['points_per_answer'] ) {
								$value       = $request['answer_data'];
								$total_point = 0;
								foreach ( $value as $answer_item ) {
										$total_point = $total_point + (int) $answer_item['_points'];
								}
								$question->setPoints( $total_point );
							}

							break;
						case 'quiz':
							if ( isset( $request['quiz'] ) ) {
								$quiz_pro_id = $question->getQuizId();
								$quiz_pro_id = absint( $quiz_pro_id );
								$question->setQuizId( $quiz_pro_id );
								$quiz_post_id = learndash_get_quiz_id_by_pro_quiz_id( $quiz_pro_id );
								learndash_update_setting( $question_post_id, 'quiz', $quiz_post_id );
								learndash_proquiz_sync_question_fields( $question_post_id, $question_pro );
								learndash_set_question_quizzes_dirty( $question_post_id );
							}
							break;
						case 'hints_message':
							if ( isset( $request['hints_message'] ) ) {
								$question->setTipMsg( $request['hints_message'] );
							}
							break;
						case 'question':
							if ( isset( $request['question'] ) ) {
								$question->setQuestion( $request['question'] );
							}
							break;
						case 'answer_data':
							$value               = $request['answer_data'];
							$answer_import_array = null;
							$total_point         = 0;
							if ( is_array( $value ) && isset( $request['answer_data'] ) ) {
								$answer_import_array = [];
								foreach ( $value as $answer_item ) {
									if ( is_array( $answer_item ) ) {
										$answer_import = new \WpProQuiz_Model_AnswerTypes();
										$answer_import->set_array_to_object( $answer_item );
										$answer_import_array[] = $answer_import;
									} elseif ( is_a( $answer_item, 'WpProQuiz_Model_AnswerTypes' ) ) {
										$answer_import_array[] = $answer_item;
									}
								}
								$question->setAnswerData( $answer_import_array );
							}
							break;
						case 'question_type':
							if ( isset( $request['question_type'] ) ) {
								$question->setAnswerType( isset( $request['question_type'] ) ? $request['question_type'] : 'single' );
							}
							break;
						case 'previous_id':
							if ( isset( $request['previous_id'] ) ) {
								$question->setPreviousId( $request['previous_id'] );
							}
							break;
						case 'online':
							if ( isset( $request['online'] ) ) {
								$question->setOnline( $request['online'] );
							}
							break;
						case 'points_show_in_message':
							if ( isset( $request['points_show_in_message'] ) ) {
								$question->setShowPointsInBox( $request['points_show_in_message'] );
							}
							break;
						case 'points_per_answer':
							if ( isset( $request['points_per_answer'] ) ) {
								$question->setAnswerPointsActivated( $request['points_per_answer'] );
							}

							if ( 'assessment_answer' === $request['question_type'] ) {
								$question->setAnswerPointsActivated( true );
							}

							// Total points calculations.
							if ( isset( $request['answer_data'] ) ) {
								$value = $request['answer_data'];
							} else {
								$value = $question->getAnswerData( true );
							}
							// if points_per_answer is true sum all the correct answers.
							if ( 'multiple' === $request['question_type'] ) {
								$total_point = 0;
								foreach ( $value as $answer_item ) {
									if ( $answer_item['_correct'] ) {
										$total_point = $total_point + (int) $answer_item['_points'];
									}
								}
								if ( $question->isAnswerPointsActivated() ) {
									$question->setPoints( $total_point );
								}
							}

							break;
						case 'points_diff_modus':
							if ( isset( $request['points_diff_modus'] ) ) {
								$question->setAnswerPointsDiffModusActivated( $request['points_diff_modus'] );
							}
							break;
						case 'disable_correct':
							if ( isset( $request['disable_correct'] ) ) {
								$question->setDisableCorrect( $request['disable_correct'] );
							}
							break;
						case 'answer_criteria_width':
							if ( isset( $request['answer_criteria_width'] ) && absint( $request['answer_criteria_width'] ) < 100 ) {
								$question->setMatrixSortAnswerCriteriaWidth( $request['answer_criteria_width'] );
							}
							break;
						case 'correct_message':
							if ( isset( $request['correct_message'] ) ) {
								$question->setCorrectMsg( $request['correct_message'] );
							}
							break;
						case 'incorrect_message':
							if ( isset( $request['incorrect_message'] ) ) {
								$question->setIncorrectMsg( $request['incorrect_message'] );
							}
							break;
						case 'correct_same':
							if ( isset( $request['correct_same'] ) ) {
								$question->setCorrectSameText( (int) $request['correct_same'] );
							}
							break;
						case 'hints_enabled':
							if ( isset( $request['hints_enabled'] ) ) {
								$question->setTipEnabled( (int) $request['hints_enabled'] );
							}
							break;
						default:
							// code to be executed if expression does not match any of the values.
							break;
					}
				}
			}

			$question_mapper->save( $question );

			$table_name = LDLMS_DB::get_table_name( 'quiz_question' );
			if ( (int) $question->getId() !== 0 ) {
				$wpdb->update(
					$table_name,
					[
						'quiz_id'                 => $question->getQuizId(),
						'title'                   => $question->getTitle(),
						'points'                  => $question->getPoints(),
						'question'                => $question->getQuestion(),
						'correct_msg'             => $question->getCorrectMsg(),
						'incorrect_msg'           => $question->getIncorrectMsg(),
						'correct_same_text'       => (int) $question->isCorrectSameText(),
						'tip_enabled'             => (int) $question->isTipEnabled(),
						'tip_msg'                 => $question->getTipMsg(),
						'answer_type'             => $question->getAnswerType(),
						'show_points_in_box'      => (int) $question->isShowPointsInBox(),
						'answer_points_activated' => (int) $question->isAnswerPointsActivated(),
						'answer_data'             => $question->getAnswerData( true ),
						'category_id'             => $question->getCategoryId(),
						'answer_points_diff_modus_activated' => (int) $question->isAnswerPointsDiffModusActivated(),
						'disable_correct'         => (int) $question->isDisableCorrect(),
						'matrix_sort_answer_criteria_width' => $question->getMatrixSortAnswerCriteriaWidth(),
					],
					[ 'id' => $question_pro_id ],
					[ '%s', '%s', '%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%d', '%d', '%s', '%d', '%d', '%d', '%d' ],
					[ '%d' ]
				);
				$response_data = $question->get_object_as_array();
				foreach ( $response_data as $key => $value ) {
					if ( '_answerData' === $key ) {
						foreach ( $question->getAnswerData() as $key => $answer ) {
							$answer_data[] = $answer->get_object_as_array();
						}
					} else {
						$new_response_data[ $fields_map[ $key ] ] = $value;
					}
				}

				$new_response_data['answer_data'] = $answer_data;

				// Set the featured image for the post.
				$new_response_data['question_post_id'] = (int) learndash_get_question_post_by_pro_id( $question->getId() );
				set_post_thumbnail( $new_response_data['question_post_id'], (int) $request['featured_media'] );

				$new_response_data['featured_media'] = (int) get_post_thumbnail_id( $new_response_data['question_post_id'] );

				// Update Question Post title [WORKS].
				if ( isset( $request['title'] ) ) {
					wp_update_post(
						[
							'ID'         => $question_post_id,
							'post_title' => $request['title'],
							'title'      => $request['title'],
						]
					);
				}

				// Update question post content.
				if ( isset( $request['question'] ) ) {
					wp_update_post(
						[
							'ID'           => $question_post_id,
							'post_content' => $request['question'],
						]
					);
				}

				// Update question status.
				if ( isset( $request['post_status'] ) && ( 'publish' === $request['post_status'] || 'draft' === $request['post_status'] || 'trash' === $request['post_status'] ) ) {
					wp_update_post(
						[
							'ID'          => $question_post_id,
							'post_status' => $request['post_status'],
						]
					);
				}

				$new_response_data['post_data'] = get_post( $new_response_data['question_post_id'] );
				$response                       = rest_ensure_response( $new_response_data );
				$response->set_status( 200 );
				return $response;
			}
		}

		/**
		 * Update quiz settings.
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function fetch_question_by_id( $request ) {
			// Create an instance of the class.
			$question   = new \WpProQuiz_Model_Question();
			$fields_map = [
				'_id'                             => 'id',
				'_quizId'                         => 'quiz',
				'_answerType'                     => 'question_type',
				'_points'                         => 'points_total',
				'_answerPointsActivated'          => 'points_per_answer',
				'_showPointsInBox'                => 'points_show_in_message',
				'_answerPointsDiffModusActivated' => 'points_diff_modus',
				'_disableCorrect'                 => 'disable_correct',
				'_correctMsg'                     => 'correct_message',
				'_incorrectMsg'                   => 'incorrect_message',
				'_correctSameText'                => 'correct_same',
				'_tipEnabled'                     => 'hints_enabled',
				'_tipMsg'                         => 'hints_message',
				'_answer_data'                    => 'answer_data',
				'_previousId'                     => 'previous_id',
				'_online'                         => 'online',
				'_sort'                           => 'menu_order',
				'_title'                          => 'title',
				'_question'                       => 'question',
				'_answerData'                     => 'answer_data',
				'_matrixSortAnswerCriteriaWidth'  => 'answer_criteria_width',
				'_questionPostId'                 => 'question_post_id',
			];
			if ( isset( $request['id'] ) ) {
				$question = fetchQuestionModel( (int) get_post_meta( $request['id'], 'question_pro_id', true ) );
			}

			if ( null === $question || is_wp_error( $question ) ) {
				return new WP_Error(
					'ir_rest_invalid_question_id',
					esc_html__(
						'Please provide valid question id',
						'wdm_instructor_role'
					),
					[ 'status' => 401 ]
				);
			}

			$response_data = $question->get_object_as_array();

			foreach ( $response_data as $key => $value ) {
				if ( '_answerData' === $key ) {
					foreach ( $question->getAnswerData() as $key => $answer ) {
						$answer_data[] = $answer->get_object_as_array();
					}
				} else {
					$new_response_data[ $fields_map[ $key ] ] = $value;
				}
			}

				$new_response_data['question_post_id'] = $request['id'];

				$new_response_data['answer_data'] = $answer_data;

				$new_response_data['featured_media'] = (int) get_post_thumbnail_id( $new_response_data['question_post_id'] );
				$new_response_data['post_data']      = get_post( $new_response_data['question_post_id'] );
				$response                            = rest_ensure_response( $new_response_data );
				$response->set_status( 200 );
				return $response;
		}

		/**
		 * Fetch question library settings.
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function fetch_question_library( $request ) {
			// Shared questions.
			$quiz_id         = absint( $request['id'] );
			$data            = [];
			$question_data[] = '';
			$args            = [
				'post_type'      => 'sfwd-question',
				'author'         => get_current_user_id(),
				'posts_per_page' => -1,
				'post_status'    => [ 'publish', 'draft' ],
				'meta_query'     => [
					[
						'key'   => 'quiz_id',
						'value' => $quiz_id,
					],
				],
			];
			$query           = new WP_Query( $args );
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					if ( 0 !== $quiz_id ) {
						$question_data[] = get_the_ID(); // add post id to array.
						if ( 'draft' === get_post_status() ) {
							$question_mapper = new \WpProQuiz_Model_QuestionMapper();
							$question_model  = $question_mapper->fetch( (int) get_post_meta( get_the_ID(), 'question_pro_id', true ) );
							$question        = [
								'id'          => get_the_ID(),
								'pro_id'      => (int) get_post_meta( get_the_ID(), 'question_pro_id', true ),
								'title'       => get_the_title(),
								'points'      => $question_model->getPoints(),
								'type'        => $question_model->getAnswerType(),
								'timestamp'   => get_post_timestamp(),
								'post_status' => get_post_status(),
							];
							array_push( $data, $question );
						}
					}
				}
			}
			$args2 = [
				'post__not_in'   => $question_data,
				'post_type'      => 'sfwd-question',
				'post_status'    => [ 'publish', 'draft' ],
				'posts_per_page' => -1,
				'author'         => get_current_user_id(),
			];
			$q2    = new WP_query( $args2 );
			if ( $q2->have_posts() ) :
				while ( $q2->have_posts() ) :
					$q2->the_post();
					$question_mapper = new \WpProQuiz_Model_QuestionMapper();
					$question_model  = $question_mapper->fetch( (int) get_post_meta( get_the_ID(), 'question_pro_id', true ) );

					$question = [
						'id'          => get_the_ID(),
						'pro_id'      => (int) get_post_meta( get_the_ID(), 'question_pro_id', true ),
						'title'       => get_the_title(),
						'points'      => $question_model->getPoints(),
						'type'        => $question_model->getAnswerType(),
						'timestamp'   => get_post_timestamp(),
						'post_status' => get_post_status(),
					];

					// Unshared question library.
					if ( \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Quizzes_Builder', 'shared_questions' ) === 'yes' ) {
						array_push( $data, $question );
					} elseif ( 0 === (int) get_post_meta( get_the_ID(), 'quiz_id', true ) || null === get_post( (int) get_post_meta( get_the_ID(), 'quiz_id', true ) ) ) {
						array_push( $data, $question );
					}

				endwhile;
			endif;
			wp_reset_postdata();
			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );
			return $response;
		}

		/**
		 *  Create or update Question
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function create_question( $request ) {
			// Create an instance of the class.
			$question        = new \WpProQuiz_Model_Question();
			$question_mapper = new \WpProQuiz_Model_QuestionMapper();
			global $wpdb;
			$quiz_pro_id = (int) get_post_meta( $request['quiz'], 'quiz_pro_id', true );
			if ( ! isset( $request['quiz'] ) || (bool) ! $quiz_pro_id ) {
				return new WP_Error( 'ir_rest_question_quiz_id', esc_html__( 'Please provide valid quiz id', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			$fields_map = [
				'_id'                             => 'id',
				'_quizId'                         => 'quiz',
				'_answerType'                     => 'question_type',
				'_points'                         => 'points_total',
				'_answerPointsActivated'          => 'points_per_answer',
				'_showPointsInBox'                => 'points_show_in_message',
				'_answerPointsDiffModusActivated' => 'points_diff_modus',
				'_disableCorrect'                 => 'disable_correct',
				'_correctMsg'                     => 'correct_message',
				'_incorrectMsg'                   => 'incorrect_message',
				'_correctSameText'                => 'correct_same',
				'_tipEnabled'                     => 'hints_enabled',
				'_tipMsg'                         => 'hints_message',
				'_answer_data'                    => 'answer_data',
				'_previousId'                     => 'previous_id',
				'_online'                         => 'online',
				'_sort'                           => 'menu_order',
				'_title'                          => 'title',
				'_question'                       => 'question',
				'_answerData'                     => 'answer_data',
				'_matrixSortAnswerCriteriaWidth'  => 'answer_criteria_width',
				'_questionPostId'                 => 'question_post_id',
			];
			if ( isset( $request['id'] ) ) {
				$question = fetchQuestionModel( $request['id'] );
			}
			$response_data = $question->get_object_as_array();
			foreach ( $fields_map as $key => $value ) {
				$new_response_data[ $fields_map[ $key ] ] = $value;
				if ( ! isset( $request['id'] ) || array_key_exists( $key, $response_data ) ) {
					switch ( $fields_map[ $key ] ) {
						case 'id':
							if ( isset( $request['id'] ) ) {
								$question->setId( $request['id'] );
							}
							break;
						case 'menu_order':
							if ( isset( $request['menu_order'] ) ) {
								$question->setSort( $request['menu_order'] );
							}
							break;
						case 'title':
							if ( isset( $request['title'] ) ) {
								$question->setTitle( $request['title'] );
							}
							break;
						case 'points_total':
							if ( isset( $request['points_total'] ) ) {
								$question->setPoints( $request['points_total'] );
							}
							if ( isset( $request['question_type'] ) && 'assessment_answer' === $request['question_type'] && $request['answer_data'] ) {
								$answer_data = $request['answer_data'][0]['_answer'];
								$answer_data = learndash_question_assessment_fetch_data( $answer_data );
								$question->setPoints( max( $answer_data['points'] ) );
							}

							if ( isset( $request['question_type'] ) && 'cloze_answer' === $request['question_type'] && $request['answer_data'] && $request['points_per_answer'] ) {
								$answer_data = $request['answer_data'][0]['_answer'];
								$answer_data = learndash_question_cloze_fetch_data( $answer_data );

								$points     = 0;
								$max_points = 0;

								foreach ( $answer_data['points'] as $points_set ) {
									$item_points = 1;
									if ( ( is_array( $points_set ) ) && ( ! empty( $points_set ) ) ) {
										$item_points = max( $points_set );
									}

									$points    += $item_points;
									$max_points = max( $max_points, $item_points );
								}
								$question->setPoints( $max_points );
							}
							if ( isset( $request['question_type'] ) && 'assessment_answer' === $request['question_type'] && $request['answer_data'] ) {
								$answer_data = $request['answer_data'][0]['_answer'];
								$answer_data = learndash_question_assessment_fetch_data( $answer_data );
								$question->setPoints( max( $answer_data['points'] ) );
							}

							if ( isset( $request['question_type'] ) && 'free_answer' === $request['question_type'] && $request['answer_data'] && $request['points_per_answer'] ) {
								$points_answer_data = $request['answer_data'][0];
								$answer_type        = new \WpProQuiz_Model_AnswerTypes( $points_answer_data );
								$points_answer_data = learndash_question_free_get_answer_data( $answer_type );
								$max_points         = 0;
								$max_points         = max( $points_answer_data['points'] );
								$question->setPoints( $max_points );
							}

							// if points_per_answer is true sum all the answers.
							if ( ( 'matrix_sort_answer' === $request['question_type'] || 'sort_answer' === $request['question_type'] ) && $request['answer_data'] && $request['points_per_answer'] ) {
								$value       = $request['answer_data'];
								$total_point = 0;
								foreach ( $value as $answer_item ) {
										$total_point = $total_point + (int) $answer_item['_points'];
								}
								$question->setPoints( $total_point );
							}
							break;
						case 'quiz':
							if ( isset( $request['quiz'] ) ) {
								$question->setQuizId( $quiz_pro_id );
							}
							$question_mapper = new \WpProQuiz_Model_QuestionMapper();
							$questions       = $question_mapper->fetchAll( $request['quiz'] );
							if ( ( is_array( $questions ) ) && ( ! empty( $questions ) ) ) {
								$questions_count = count( $questions );
							}
							break;
						case 'hints_message':
							if ( isset( $request['hints_message'] ) ) {
								$question->setTipMsg( $request['hints_message'] );
							}
							break;
						case 'question':
							if ( isset( $request['question'] ) ) {
								$question->setQuestion( $request['question'] );
							}
							break;
						case 'answer_data':
							$value               = $request['answer_data'];
							$answer_import_array = null;
							$total_point         = 0;
							if ( is_array( $value ) ) {
								$answer_import_array = [];
								foreach ( $value as $answer_item ) {
									if ( is_array( $answer_item ) ) {
										$answer_import = new \WpProQuiz_Model_AnswerTypes();
										$answer_import->set_array_to_object( $answer_item );
										$answer_import_array[] = $answer_import;
									} elseif ( is_a( $answer_item, 'WpProQuiz_Model_AnswerTypes' ) ) {
										$answer_import_array[] = $answer_item;
									}
								}
							}
							$question->setAnswerData( $answer_import_array );
							break;
						case 'question_type':
							if ( isset( $request['question_type'] ) ) {
								$question->setAnswerType( isset( $request['question_type'] ) ? $request['question_type'] : 'single' );
							}
							break;
						case 'previous_id':
							if ( isset( $request['previous_id'] ) ) {
								$question->setPreviousId( $request['previous_id'] );
							}
							break;
						case 'online':
							if ( isset( $request['online'] ) ) {
								$question->setOnline( $request['online'] );
							}
							break;
						case 'points_show_in_message':
							if ( isset( $request['points_show_in_message'] ) ) {
								$question->setShowPointsInBox( $request['points_show_in_message'] );
							}
							break;
						case 'points_per_answer':
							if ( isset( $request['points_per_answer'] ) ) {
								$question->setAnswerPointsActivated( $request['points_per_answer'] );
							}

							if ( 'assessment_answer' === $request['question_type'] ) {
								$question->setAnswerPointsActivated( true );
							}

							// Total points calculations.
							if ( isset( $request['answer_data'] ) ) {
								$value = $request['answer_data'];
							} else {
								$value = $question->getAnswerData( true );
							}

							if ( 'multiple' === $request['question_type'] ) {
								// if points_per_answer is true sum all the correct answers.
								$total_point = 0;
								foreach ( $value as $answer_item ) {
									if ( $answer_item['_correct'] ) {
										$total_point = $total_point + (int) $answer_item['_points'];
									}
								}
								if ( $question->isAnswerPointsActivated() ) {
									$question->setPoints( $total_point );
								}
							}
							break;
						case 'points_diff_modus':
							if ( isset( $request['points_diff_modus'] ) ) {
								$question->setAnswerPointsDiffModusActivated( $request['points_diff_modus'] );
							}
							break;
						case 'disable_correct':
							if ( isset( $request['disable_correct'] ) ) {
								$question->setDisableCorrect( $request['disable_correct'] );
							}
							break;
						case 'answer_criteria_width':
							if ( isset( $request['answer_criteria_width'] ) && absint( $request['answer_criteria_width'] ) < 100 ) {
								$question->setMatrixSortAnswerCriteriaWidth( $request['answer_criteria_width'] );
							}
							break;
						case 'correct_message':
							if ( isset( $request['correct_message'] ) ) {
								$question->setCorrectMsg( $request['correct_message'] );
							}
							break;
						case 'incorrect_message':
							if ( isset( $request['incorrect_message'] ) ) {
								$question->setIncorrectMsg( $request['incorrect_message'] );
							}
							break;
						case 'correct_same':
							if ( isset( $request['correct_same'] ) ) {
								$question->setCorrectSameText( (int) $request['correct_same'] );
							}
							break;
						case 'hints_enabled':
							if ( isset( $request['hints_enabled'] ) ) {
								$question->setTipEnabled( (int) $request['hints_enabled'] );
							}
							break;
						default:
							// code to be executed if expression does not match any of the values.
							break;
					}
				}
			}

			$table_name = LDLMS_DB::get_table_name( 'quiz_question' );
			if ( (int) $question->getId() !== 0 ) {
				$wpdb->update(
					$table_name,
					[
						'quiz_id'                 => $question->getQuizId(),
						'title'                   => $question->getTitle(),
						'points'                  => $question->getPoints(),
						'question'                => $question->getQuestion(),
						'correct_msg'             => $question->getCorrectMsg(),
						'incorrect_msg'           => $question->getIncorrectMsg(),
						'correct_same_text'       => (int) $question->isCorrectSameText(),
						'tip_enabled'             => (int) $question->isTipEnabled(),
						'tip_msg'                 => $question->getTipMsg(),
						'answer_type'             => $question->getAnswerType(),
						'show_points_in_box'      => (int) $question->isShowPointsInBox(),
						'answer_points_activated' => (int) $question->isAnswerPointsActivated(),
						'answer_data'             => $question->getAnswerData( true ),
						'category_id'             => $question->getCategoryId(),
						'answer_points_diff_modus_activated' => (int) $question->isAnswerPointsDiffModusActivated(),
						'disable_correct'         => (int) $question->isDisableCorrect(),
						'matrix_sort_answer_criteria_width' => $question->getMatrixSortAnswerCriteriaWidth(),
					],
					[ 'id' => $question->getId() ],
					[ '%s', '%s', '%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%d', '%d', '%s', '%d', '%d', '%d', '%d' ],
					[ '%d' ]
				);
				// Set the featured image for the post.
				set_post_thumbnail( $question->getId(), $request['featured_media'] );

				$response_data = $question->get_object_as_array();
				foreach ( $response_data as $key => $value ) {
					if ( '_answerData' === $key ) {
						foreach ( $question->getAnswerData() as $key => $answer ) {
							$answer_data[] = $answer->get_object_as_array();
						}
					} else {
						$new_response_data[ $fields_map[ $key ] ] = $value;
					}
				}

				$new_response_data['answer_data'] = $answer_data;

				// Set the featured image for the post.
				$new_response_data['question_post_id'] = (int) learndash_get_question_post_by_pro_id( $question->getId() );

				set_post_thumbnail( $new_response_data['question_post_id'], $request['featured_media'] );

				// Update Question Post title.
				if ( isset( $request['title'] ) ) {
					wp_update_post(
						[
							'ID'         => $new_response_data['question_post_id'],
							'post_title' => $request['title'],
							'title'      => $request['title'],
						]
					);
				}

				// Update Question Post status.
				if ( isset( $request['post_status'] ) && ( 'publish' === $request['post_status'] || 'draft' === $request['post_status'] || 'trash' === $request['post_status'] ) ) {
					wp_update_post(
						[
							'ID'          => $new_response_data['question_post_id'],
							'post_status' => $request['post_status'],
						]
					);
				}

				$new_response_data['featured_media'] = (int) get_post_thumbnail_id( $new_response_data['question_post_id'] );
				$new_response_data['post_data']      = get_post( $new_response_data['question_post_id'] );
				$response                            = rest_ensure_response( $new_response_data );
				$response->set_status( 200 );
				return $response;
			} else {
				$question_mapper->save( $question );

				$question_post_id = learndash_get_question_post_by_pro_id( $question->getId() );
				$question->setSort( $request['menu_order'] );
				if ( empty( $question_post_id ) ) {
					// We load fresh from DB. Don't use the $question object as it is not up to date.
					$question_pro = $question_mapper->fetchById( $question->getId() );
					if (
						$question_pro
						&& $question_pro instanceof WpProQuiz_Model_Question
					) {
						$question_insert_post                 = [];
						$question_insert_post['post_type']    = learndash_get_post_type_slug( 'question' );
						$question_insert_post['post_status']  = 'publish';
						$question_insert_post['post_title']   = $question_pro->getTitle();
						$question_insert_post['post_content'] = $question_pro->getQuestion();
						$question_insert_post['menu_order']   = absint( $question_pro->getSort() );

						$question_insert_post    = wp_slash( $question_insert_post );
						$question_insert_post_id = wp_insert_post( $question_insert_post );
						if ( false !== $question_insert_post_id ) {
							$quiz_pro_id  = $question_pro->getQuizId();
							$quiz_pro_id  = absint( $quiz_pro_id );
							$quiz_post_id = learndash_get_quiz_id_by_pro_quiz_id( $quiz_pro_id );
							learndash_update_setting( $question_insert_post_id, 'quiz', $quiz_post_id );
							learndash_proquiz_sync_question_fields( $question_insert_post_id, $question_pro );
							learndash_set_question_quizzes_dirty( $question_insert_post_id );
							// Update the postmeta table.
							update_post_meta( $question_insert_post_id, 'question_pro_id', $question_pro->getId() );
							update_post_meta( $question_insert_post_id, 'question_type', 'single' );
							update_post_meta( $question_insert_post_id, '_sfwd-question', 'a:1:{s:18:"sfwd-question_quiz";s:1:"0";}' );

							$response_data = $question->get_object_as_array();
							foreach ( $response_data as $key => $value ) {
								if ( '_answerData' === $key ) {
									foreach ( $question->getAnswerData() as $key => $answer ) {
										$answer_data[] = $answer->get_object_as_array();
									}
								} else {
									$new_response_data[ $fields_map[ $key ] ] = $value;
								}
							}

							// Update Question post status.
							if ( isset( $request['post_status'] ) && ( 'publish' === $request['post_status'] || 'draft' === $request['post_status'] || 'trash' === $request['post_status'] ) ) {
								wp_update_post(
									[
										'ID'          => $question_insert_post_id,
										'post_status' => $request['post_status'],
									]
								);
							}

							$new_response_data['answer_data']      = $answer_data;
							$new_response_data['question_post_id'] = (int) $question_insert_post_id;
							// Set the featured image for the post.
							set_post_thumbnail( $question_insert_post_id, $request['featured_media'] );
							$new_response_data['featured_media'] = (int) get_post_thumbnail_id( $question_insert_post_id );
							$new_response_data['post_data']      = get_post( $new_response_data['question_post_id'] );
							$response                            = rest_ensure_response( $new_response_data );
							$response->set_status( 200 );
							return $response;
						}
					}
				} else {
					// Error Response.
					return new WP_Error( 'ir_rest_question_error', esc_html__( 'Question post creation failed.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
				}
			}
		}

		/**
		 * Fetch remaining course details permissions check
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function fetch_remaining_course_details_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Remove user api endpoint limit
		 *
		 * @since 4.4.0
		 *
		 * @param array $endpoints      Array of available endpoints.
		 * @return array
		 */
		public function remove_user_endpoint_limits( $endpoints ) {
			if ( ! isset( $endpoints['/wp/v2/users'] ) ) {
				return $endpoints;
			}
			unset( $endpoints['/wp/v2/users'][0]['args']['per_page']['maximum'] );

			return $endpoints;
		}

		/**
		 * Update remaining quiz details.
		 *
		 * @param WP_REST_Response $result  Result to send to the client.
		 * @param WP_REST_Server   $server  WP_REST_Server instance.
		 * @param WP_REST_Request  $request WP_REST_Request instance.
		 *
		 * @since 4.5.0
		 */
		public function update_remaining_quiz_details( $result, $server, $request ) {
			// Check if quiz update rest request.
			if ( ! preg_match( '/\/ldlms\/v2\/sfwd-quiz\/[\d]+/', $request->get_route() ) ) {
				return $result;
			}

			// Get quiz object.
			$quiz = get_post( absint( $request['id'] ) );

			// Check if valid quiz.
			if ( empty( $quiz ) || ! $quiz instanceof WP_Post ) {
				return $result;
			}
			if ( 'POST' === $request->get_method() && '/ldlms/v2/sfwd-quiz/' . $quiz->ID === $request->get_route() ) {
				// Get pro quiz ID.
				$quiz_pro_id    = learndash_get_setting( $quiz->ID, 'quiz_pro' );
				$body           = $request->get_json_params();
				$questions_sort = [];

				if ( ! empty( $body ) && array_key_exists( 'questions', $body ) && null !== $body['questions'] ) {
					$questions_list = $body['questions'];

					// Sort questions according to menu order.
					array_multisort( array_column( $questions_list, 'menu_order' ), SORT_ASC, $questions_list );

					foreach ( $questions_list as $question ) {
						// Get question pro ID.
						$question_pro_id = get_post_meta( $question['id'], 'question_pro_id', true );
						if ( ! empty( $question_pro_id ) ) {
							$question_pro_id = absint( $question_pro_id );
						} else {
							$question_pro_id = 0;
						}
						$questions_sort[ $question['id'] ] = $question_pro_id;

						$question_model  = null;
						$question_mapper = null;

						$question_mapper = new \WpProQuiz_Model_QuestionMapper();
						$question_model  = $question_mapper->fetch( $question_pro_id );

						// Update quiz ID.
						if ( ! empty( $quiz_pro_id ) ) {
							$question_model->setQuizId( $quiz_pro_id );
							update_post_meta( $question['id'], 'quiz_id', $quiz->ID );
							update_post_meta(
								$question['id'],
								'_sfwd-question',
								[ 'sfwd-question_quiz' => $quiz->ID ]
							);
						}

						// Update question menu order.
						if ( array_key_exists( 'menu_order', $question ) ) {
							$question_model->setSort( $question['menu_order'] );
							wp_update_post(
								[
									'ID'         => $question['id'],
									'menu_order' => $question['menu_order'],
								]
							);
						}

						$question_mapper->save( $question_model );
					}

					// Update quiz questions sorting order.
					$ld_quiz_questions_object = \LDLMS_Factory_Post::quiz_questions( $quiz->ID );
					$ld_quiz_questions_object->set_questions( $questions_sort );

					global $wpdb;

					// Save quiz modus settings ( i.e. Question Display ).
					$table_name = LDLMS_DB::get_table_name( 'quiz_master' );
					$row        = $wpdb->get_row(
						$wpdb->prepare(
							'SELECT
								*
							FROM
								' . $table_name . '
							WHERE
								id = %d',
							$quiz_pro_id
						),
						ARRAY_A
					);

					$quiz_model = new \WpProQuiz_Model_Quiz( $row );
					// Save result text as is .
					$quiz_model->setResultText( maybe_unserialize( $row['result_text'] ) );

					// Update result text to value fetched from DB.
					if ( isset( $body['result_text'] ) ) {
						$result_text = maybe_unserialize( $body['result_text'] );
						learndash_update_setting( $quiz->ID, 'resultText', $result_text );
						$quiz_model->setResultText( $result_text );
					}

					$quiz_mapper = new \WpProQuiz_Model_QuizMapper();

					$single_feedback = false;
					// Save question display single values.
					if ( isset( $body['single_feedback'] ) ) {
						// This would either be end or each.
						$single_feedback = $body['single_feedback'];
						learndash_update_setting( $quiz->ID, 'quiz_quizModus_single_feedback', $single_feedback );
					}

					$single_back_button = false;
					if ( isset( $body['single_back_button'] ) ) {
						// This would either be on or off/empty.
						$single_back_button = $body['single_back_button'];
						learndash_update_setting( $quiz->ID, 'quizModus_single_back_button', $single_back_button );
					}

					$multiple_questions_per_page = false;
					// Save question display multiple values.
					if ( isset( $body['multiple_questions_per_page'] ) ) {
						// This would either be on or off/empty.
						$multiple_questions_per_page = $body['multiple_questions_per_page'];
						learndash_update_setting( $quiz->ID, 'quizModus_multiple_questionsPerPage', $multiple_questions_per_page );
					}

					if ( 'single' === $body['quiz_modus'] ) {
						if ( 'end' === $single_feedback ) {
							if ( 'on' === $single_back_button ) {
								$quiz_model->setQuizModus( 1 );
							} else {
								$quiz_model->setQuizModus( 0 );
							}
						} else {
							$quiz_model->setQuizModus( 2 );
						}
					} else {
						$quiz_model->setQuizModus( 3 );
						$quiz_model->setQuestionsPerPage( $multiple_questions_per_page );
					}

					// Prevent settings.
					$quiz_model->setViewProfileStatistics( $quiz_model->getViewProfileStatistics() );
					$quiz_mapper->save( $quiz_model );
				}
			}

			return $result;
		}

		/**
		 * Get pre-requisite quizzes
		 *
		 * @since 4.5.1
		 *
		 * @param object $quiz    LearnDash Quiz.
		 **/
		public function get_prereq_quizzes( $quiz ) {
			$pre_req_quizzes = [];
			$quiz_list       = [];
			if ( empty( $quiz ) || ! $quiz instanceof WP_Post ) {
				return $quiz_list;
			}
			$orphan_quiz     = [];
			$current_user_id = get_current_user_id();

			if ( current_user_can( 'manage_options' ) ) {
				// Fetch all quizzes.
				$args = [
					'post_type'      => learndash_get_post_type_slug( 'quiz' ),
					'posts_per_page' => -1,
					'status'         => 'publish',
					'fields'         => 'ids',
				];

				$all_quizzes = new WP_Query( $args );
				if ( ! empty( $all_quizzes->posts ) ) {
					$pre_req_quizzes = array_unique( array_merge( $pre_req_quizzes, $all_quizzes->posts ) );
				}
			} else {
				// First get quiz associated with some instructor courses.
				$instructor_courses = ir_get_instructor_complete_course_list( $current_user_id );

				foreach ( $instructor_courses as $course_id ) {
					$course_quiz = learndash_get_course_steps( $course_id, [ 'sfwd-quiz' ] );
					if ( ! empty( $course_quiz ) ) {
						$pre_req_quizzes = array_merge( $pre_req_quizzes, $course_quiz );
					}
				}

				// Fetch orphan quiz.
				$args = [
					'post_type'      => learndash_get_post_type_slug( 'quiz' ),
					'posts_per_page' => -1,
					'status'         => 'publish',
					'author'         => $current_user_id,
					'fields'         => 'ids',
				];

				$orphan_quiz = new WP_Query( $args );

				if ( ! empty( $orphan_quiz->posts ) ) {
					$pre_req_quizzes = array_unique( array_merge( $pre_req_quizzes, $orphan_quiz->posts ) );
				}

				if ( ! empty( $course_quizzes ) ) {
					$pre_req_quizzes = array_diff( $pre_req_quizzes, $course_quizzes );
				}
			}

			foreach ( $pre_req_quizzes as $value ) {
				if ( intval( $value ) === intval( $quiz->ID ) ) {
					continue;
				}

				// Return quiz WP post ID for LD v5.0.0+ (LD Core REST API v2 release), otherwise return pro quiz ID.
				$quiz_id = defined( 'LEARNDASH_VERSION' ) && version_compare( LEARNDASH_VERSION, '5.0.0-dev', '>=' ) // @phpstan-ignore-line -- phpstan is not aware of the LEARNDASH_VERSION constant actual value.
					? Cast::to_int( $value )
					: Cast::to_int( learndash_get_setting( $value, 'quiz_pro' ) );

				array_push(
					$quiz_list,
					[
						'value' => $quiz_id,
						'label' => get_the_title( $value ),
					]
				);
			}

			return $quiz_list;
		}

		/**
		 * Get LearnDash Certificates
		 *
		 * @since 4.5.1
		 *
		 * @return array    Array of certificates accessible to current user.
		 */
		public function get_ld_certificates() {
			$data = [];
			$args = [
				'post_type'      => learndash_get_post_type_slug( 'certificate' ),
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			];

			if ( ! current_user_can( 'manage_options' ) ) {
				$args['author'] = get_current_user_id();
			}

			$query = new WP_Query( $args );

			if ( $query->have_posts() ) {
				foreach ( $query->posts as $post ) {
					array_push(
						$data,
						[
							'value' => $post->ID,
							'label' => $post->post_title,
						]
					);
				}
			}

			return $data;
		}

		/**
		 * It takes the data from the default LD API and builds a nested array
		 *
		 * @since 4.5.1
		 *
		 * @param object $request The request object.
		 *
		 * @return string $response response object.
		 */
		public function get_category( $request ) {
			if ( '/ir/v1/ld-course-category' === $request->get_route() ) {
				$type = 'course';
				if ( \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Taxonomies', 'ld_course_category' ) != 'yes' ) {
					return new WP_Error( 'ir_course_category_disabled', esc_html__( 'Course category disabled.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
				}
			} elseif ( '/ir/v1/ld-lesson-category' === $request->get_route() ) {
				$type = 'lesson';
				if ( \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Lessons_Taxonomies', 'ld_lesson_category' ) != 'yes' ) {
					return new WP_Error( 'ir_lesson_category_disabled', esc_html__( 'Lesson category disabled.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
				}
			} elseif ( '/ir/v1/ld-topic-category' === $request->get_route() ) {
				$type = 'topic';
				if ( \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Topics_Taxonomies', 'ld_topic_category' ) != 'yes' ) {
					return new WP_Error( 'ir_topic_category_disabled', esc_html__( 'Topic category disabled.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
				}
			} elseif ( '/ir/v1/ld-quiz-category' === $request->get_route() ) {
				$type = 'quiz';
				if ( \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Quizzes_Taxonomies', 'ld_quiz_category' ) != 'yes' ) {
					return new WP_Error( 'ir_quiz_category_disabled', esc_html__( 'Quiz category disabled.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
				}
			}

			// Get quiz using default LD API.
			$route_url = '/wp/v2/ld_' . $type . '_category';
			$args      = [
				'per_page' => 100,
				'orderby'  => 'name',
				'order'    => 'asc',
				'_fields'  => 'id,name,parent,taxonomy',
			];

			$category_request = new \WP_REST_Request( 'GET', $route_url );
			$category_request->set_query_params( $args );
			$category_response = rest_do_request( $category_request );
			$nested_data       = [];

			if ( ! $category_response->is_error() ) {
				$server = rest_get_server();
				$data   = $server->response_to_data( $category_response, false );

				// Build nested array.
				if ( $data ) {
					$nested_data = ir_category_build_tree( $data );
				}
			}

			$response = rest_ensure_response( $nested_data );
			$response->set_status( 200 );
			return $response;
		}

		/**
		 * Fetch users permissions check
		 *
		 * @since 4.5.2
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function fetch_users_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			return true;
		}

		/**
		 * Get users.
		 *
		 * @since 4.5.2
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function fetch_users( $request ) {
			$data = [];

			$params = $request->get_params();

			$atts = [];

			if ( array_key_exists( 'roles', $params ) && ! empty( $params['roles'] ) ) {
				$atts['role__in'] = explode( ',', $params['roles'] );
			}

			$atts = shortcode_atts(
				[
					'role__in' => [
						'administrator',
						'wdm_instructor',
					],
				],
				$atts
			);

			// Get admin and instructor users.
			$query = new \WP_User_Query( $atts );

			foreach ( $query->results as $user ) {
				if ( array_key_exists( 'fields', $params ) && 'all' === $params['fields'] ) {
					array_push(
						$data,
						[
							'id'           => $user->ID,
							'name'         => $user->display_name,
							'image'        => get_avatar_url( $user->ID ),
							'email'        => $user->user_email,
							'username'     => $user->user_login,
							'paypal_email' => get_user_meta( $user->ID, 'ir_paypal_payouts_email', true ),
						]
					);
				} else {
					array_push(
						$data,
						[
							'id'   => $user->ID,
							'name' => $user->display_name,
						]
					);
				}
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Fetch user groups permissions check
		 *
		 * @since 4.5.2
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function fetch_groups_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			if ( empty( $current_user_id ) || ! wdm_is_instructor( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			return true;
		}

		/**
		 * Get user groups.
		 *
		 * @since 4.5.2
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function fetch_groups( $request ) {
			$data = [];

			// Get groups.
			$groups = get_posts(
				[
					'post_type'      => learndash_get_post_type_slug( 'group' ),
					'post_status'    => 'publish',
					'author'         => get_current_user_id(),
					'posts_per_page' => -1,
				]
			);

			foreach ( $groups as $group ) {
				array_push(
					$data,
					[
						'id'    => $group->ID,
						'title' => [
							'rendered' => $group->post_title,
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
		 * Update remaining lesson details.
		 *
		 * @param WP_REST_Response $result  Result to send to the client.
		 * @param WP_REST_Server   $server  WP_REST_Server instance.
		 * @param WP_REST_Request  $request WP_REST_Request instance.
		 *
		 * @since 4.5.2
		 */
		public function update_remaining_lesson_details( $result, $server, $request ) {
			// Check if lesson update rest request.
			if ( ! ( preg_match( '/\/ldlms\/v2\/sfwd-lessons\/[\d]+/', $request->get_route() ) || '/ldlms/v2/sfwd-lessons' === $request->get_route() ) ) {
				return $result;
			}

			// Get lesson object.
			$lesson = get_post( absint( $request['id'] ) );

			// Check if valid lesson.
			if ( empty( $lesson ) || ! $lesson instanceof WP_Post ) {
				return $result;
			}

			// Check if create or update post.
			if ( 'POST' === $request->get_method() ) {
				$body = $request->get_json_params();

				if ( ! empty( $body ) && array_key_exists( 'video_shown', $body ) && null !== $body['video_shown'] ) {
					learndash_update_setting( $lesson->ID, 'lesson_video_shown', $body['video_shown'] );
				}
			}

			return $result;
		}

		/**
		 * Update remaining topic details.
		 *
		 * @param WP_REST_Response $result  Result to send to the client.
		 * @param WP_REST_Server   $server  WP_REST_Server instance.
		 * @param WP_REST_Request  $request WP_REST_Request instance.
		 *
		 * @since 4.5.2
		 */
		public function update_remaining_topic_details( $result, $server, $request ) {
			// Check if topic update rest request.
			if ( ! ( preg_match( '/\/ldlms\/v2\/sfwd-topic\/[\d]+/', $request->get_route() ) || '/ldlms/v2/sfwd-topic' === $request->get_route() ) ) {
				return $result;
			}

			// Get topic object.
			$topic = get_post( absint( $request['id'] ) );

			// Check if valid topic.
			if ( empty( $topic ) || ! $topic instanceof WP_Post ) {
				return $result;
			}

			// Check if create or update post.
			if ( 'POST' === $request->get_method() ) {
				$body = $request->get_json_params();

				if ( ! empty( $body ) && array_key_exists( 'video_shown', $body ) && null !== $body['video_shown'] ) {
					learndash_update_setting( $topic->ID, 'lesson_video_shown', $body['video_shown'] );
				}
			}

			return $result;
		}

		/**
		 * Get quiz custom fields data
		 *
		 * @param int $quiz_pro_id LearnDash Pro Quiz ID.
		 *
		 * @return array Quiz custom fields data.
		 *
		 * @since 4.5.3
		 */
		public function get_quiz_custom_fields( $quiz_pro_id ) {
			$custom_fields = [];

			// If Quiz pro ID not set, return.
			if ( empty( $quiz_pro_id ) ) {
				return $custom_fields;
			}

			$quiz_form_mapper = new \WpProQuiz_Model_FormMapper();
			$forms            = $quiz_form_mapper->fetch( $quiz_pro_id );

			$field_data = [];
			foreach ( $forms as $field ) {
				if ( $field instanceof \WpProQuiz_Model_Form ) {
					$field_data = $field->getData();
					if ( ! empty( $field_data ) && is_array( $field_data ) ) {
						$field_data = implode( "\n", $field->getData() );
					}

					array_push(
						$custom_fields,
						[
							'fieldName' => $field->getFieldname(),
							'fieldType' => strval( $field->getType() ),
							'required'  => $field->isRequired(),
							'formId'    => $field->getFormId(),
							'fieldSort' => $field->getSort(),
							'fieldData' => $field_data,
						]
					);
				}
			}

			return $custom_fields;
		}

		/**
		 * Set quiz custom fields data.
		 *
		 * @param int   $quiz_pro_id            LearnDash Pro Quiz ID.
		 * @param array $custom_fields_data     Quiz custom fields data.
		 *
		 * @since 4.5.3
		 */
		public function set_quiz_custom_fields( $quiz_pro_id, $custom_fields_data ) {
			$fields        = [];
			$sort          = 0;
			$field_ids     = [];
			$delete_fields = [];

			$quiz_form_mapper = new \WpProQuiz_Model_FormMapper();

			foreach ( $custom_fields_data as $field ) {
				if ( ( ! isset( $field['fieldName'] ) ) || ( empty( $field['fieldName'] ) ) ) {
					continue;
				}
				if ( ! empty( $field['fieldData'] ) ) {
					$items              = explode( "\n", $field['fieldData'] );
					$field['fieldData'] = [];

					foreach ( $items as $item ) {
						$item = trim( $item );

						if ( ! empty( $item ) ) {
							$field['fieldData'][] = $item;
						}
					}
				}
				$fields[] = new \WpProQuiz_Model_Form(
					[
						'fieldname' => $field['fieldName'],
						'formId'    => isset( $field['formId'] ) ? $field['formId'] : 0,
						'sort'      => $sort++,
						'quizId'    => $quiz_pro_id,
						'type'      => $field['fieldType'],
						'required'  => $field['required'],
						'data'      => $field['fieldData'],
					]
				);
				if ( isset( $field['formId'] ) ) {
					array_push( $field_ids, $field['formId'] );
				}
			}

			// Delete any old fields first.
			$existing_fields = $this->get_quiz_custom_fields( $quiz_pro_id );

			foreach ( $existing_fields as $field ) {
				if ( ! in_array( $field['formId'], $field_ids ) ) {
					array_push( $delete_fields, (int) $field['formId'] );
				}
			}

			if ( ! empty( $delete_fields ) ) {
				$quiz_form_mapper->deleteForm( $delete_fields, $quiz_pro_id );
			}

			// Add/Update fields.
			$quiz_form_mapper->update( $fields );
		}

		/**
		 * Handle instructor course review updates
		 *
		 * @param WP_REST_Response $result  Result to send to the client.
		 * @param WP_REST_Server   $server  WP_REST_Server instance.
		 * @param WP_REST_Request  $request WP_REST_Request instance.
		 *
		 * @since 4.5.3
		 */
		public function handle_instructor_course_review( $result, $server, $request ) {
			// Check if courses update rest request.
			if ( ( preg_match( '/\/ldlms\/v2\/sfwd-courses\/[\d]+/', $request->get_route() ) ) ) {
				// Get course object.
				$course = get_post( absint( $request['id'] ) );

				// Check if valid course.
				if ( ! empty( $course ) && $course instanceof WP_Post ) {
					// Check if course publish request.
					if ( 'POST' === $request->get_method() ) {
						$body = $request->get_json_params();
						// Check if course review enabled and post is being published by instructor.
						if ( WDMIR_REVIEW_COURSE && wdm_is_instructor() && 'publish' === $body['original_status'] ) {
							$module_review = Instructor_Role_Review::get_instance();
							$module_review->add_course_for_review( $course->ID, $course->ID );
						}
					}
				}
			}

			// Check if lesson update rest request.
			if ( ( preg_match( '/\/ldlms\/v2\/sfwd-lessons\/[\d]+/', $request->get_route() ) ) ) {
				// Get lesson object.
				$lesson = get_post( absint( $request['id'] ) );

				// Check if valid lesson.
				if ( ! empty( $lesson ) && $lesson instanceof WP_Post ) {
					// Check if lesson publish request.
					if ( 'POST' === $request->get_method() ) {
						$body = $request->get_json_params();

						// Check if lesson review enabled and post is being published by instructor.
						if ( WDMIR_REVIEW_COURSE && wdm_is_instructor() && 'publish' === $body['status'] ) {
							$module_review = Instructor_Role_Review::get_instance();
							$module_review->add_course_for_review( $lesson->ID, learndash_get_course_id( $lesson->ID ) );
						}
					}
				}
			}

			// Check if topic update rest request.
			if ( ( preg_match( '/\/ldlms\/v2\/sfwd-topic\/[\d]+/', $request->get_route() ) ) ) {
				// Get topic object.
				$topic = get_post( absint( $request['id'] ) );

				// Check if valid topic.
				if ( ! empty( $topic ) && $topic instanceof WP_Post ) {
					// Check if topic publish request.
					if ( 'POST' === $request->get_method() ) {
						$body = $request->get_json_params();

						// Check if topic review enabled and post is being published by instructor.
						if ( WDMIR_REVIEW_COURSE && wdm_is_instructor() && 'publish' === $body['status'] ) {
							$module_review = Instructor_Role_Review::get_instance();
							$module_review->add_course_for_review( $topic->ID, learndash_get_course_id( $topic->ID ) );
						}
					}
				}
			}

			// Check if quiz update rest request.
			if ( ( preg_match( '/\/ldlms\/v2\/sfwd-quiz\/[\d]+/', $request->get_route() ) ) ) {
				// Get quiz object.
				$quiz = get_post( absint( $request['id'] ) );

				// Check if valid quiz.
				if ( ! empty( $quiz ) && $quiz instanceof WP_Post ) {
					// Check if quiz publish request.
					if ( 'POST' === $request->get_method() ) {
						$body = $request->get_json_params();

						// Check if quiz review enabled and post is being published by instructor.
						if ( WDMIR_REVIEW_COURSE && wdm_is_instructor() && 'publish' === $body['status'] ) {
							$module_review = Instructor_Role_Review::get_instance();
							$module_review->add_course_for_review( $quiz->ID, learndash_get_course_id( $quiz->ID ) );
						}
					}
				}
			}
			return $result;
		}

		/**
		 * Get result text from data
		 *
		 * @param mixed $data  Result data in serialized format.
		 * @return mixed        Result data array.
		 *
		 * @since 4.5.3
		 */
		public function get_result_text( $data ) {
			$data = maybe_unserialize( $data );

			// Check if the key 'text' exists, 'text' is array and of length 1.
			if (
				is_array( $data )
				&& ! empty( $data['text'] )
				&& 1 === count( $data['text'] )
			) {
				$text = $data['text'][0];
				if ( empty( trim( $text ) ) ) {
					$data['text']    = [];
					$data['prozent'] = []; // cspell:disable-line .
					$data['activ']   = []; // cspell:disable-line .
				}
			}

			return $data;
		}

		/**
		 * Fetch review data permissions check
		 *
		 * @since 4.5.3
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function fetch_review_data_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			return true;
		}

		/**
		 * Get course review data.
		 *
		 * @since 4.5.3
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function fetch_review_data( $request ) {
			$data = [];

			$post = get_post( $request['id'] );

			if ( empty( $post ) || ! $post instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
			}

			switch ( $post->post_type ) {
				case learndash_get_post_type_slug( 'course' ):
					// Get approval meta.
					$approval_data = wdmir_get_approval_meta( $post->ID );

					foreach ( $approval_data as $post_id => $details ) {
						// Check if approval pending.
						if ( 'pending' !== $details['status'] ) {
							continue;
						}

						// Convert time to site timezone.
						$site_timezone = get_option( 'timezone_string' );
						$site_timezone = empty( $site_timezone ) ? 'UTC' : $site_timezone;

						$date = new \DateTime();
						$date->setTimezone( new \DateTimeZone( $site_timezone ) );
						$date->setTimestamp( strtotime( $details['update_time'] ) );
						$date_time = $date->format( 'd M Y h:i a' );

						$data[] = [
							'post_id' => $post_id,
							'title'   => get_the_title( $post_id ),
							'time'    => $date_time,
						];
					}
					break;

				case learndash_get_post_type_slug( 'lesson' ):
				case learndash_get_post_type_slug( 'topic' ):
				case learndash_get_post_type_slug( 'quiz' ):
					$data = wdmir_am_i_pending_post( $post->ID );
					break;
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Save quiz as a template
		 *
		 * @param WpProQuiz_Model_Quiz $quiz_model    WpProQuiz_Model_Quiz instance of the quiz.
		 * @param mixed                $save_template Name of the template if new template and
		 *                                            ID of existing template if override.
		 * @param bool                 $new_template  True if new template, false if existing template.
		 *
		 * @since 4.5.3
		 */
		public function save_quiz_as_template( $quiz_model, $save_template, $new_template ) {
			$template_mapper = new \WpProQuiz_Model_TemplateMapper();

			$data         = [];
			$data['quiz'] = $quiz_model;

			$quiz_form_mapper = new \WpProQuiz_Model_FormMapper();
			$data['forms']    = $quiz_form_mapper->fetch( $quiz_model->getId() );

			$quiz_post_id = $quiz_model->getPostId();
			if ( ! empty( $quiz_post_id ) ) {
				$data[ '_' . learndash_get_post_type_slug( 'quiz' ) ] = learndash_get_setting( $quiz_post_id );
			}

			$quiz_prereq = $this->get_prereq_quizzes( get_post( $quiz_post_id ) );

			$data['prerequisiteQuizList'] = [];
			foreach ( $quiz_prereq as $quiz ) {
				array_push( $data['prerequisiteQuizList'], $quiz['value'] );
			}

			// Zero out the ProQuiz Post ID and the reference for the associated settings.
			$data['quiz']->setPostId( 0 );
			$data[ '_' . learndash_get_post_type_slug( 'quiz' ) ]['quiz_pro'] = 0;

			$template = new \WpProQuiz_Model_Template();

			if ( $new_template ) {
				// @phpstan-ignore-next-line
				$template->setName( trim( $save_template ) );
			} else {
				$template = $template_mapper->fetchById( $save_template, false );
			}

			$template->setType( \WpProQuiz_Model_Template::TEMPLATE_TYPE_QUIZ );

			$template->setData( $data );

			$template_mapper->save( $template );
		}

		/**
		 * Get all quiz templates
		 *
		 * @param int $pro_quiz_id  Pro Quiz ID.
		 *
		 * @since 4.5.3
		 */
		public function get_quiz_templates( $pro_quiz_id ) {
			$template_data   = [];
			$template_mapper = new \WpProQuiz_Model_TemplateMapper();
			$all_templates   = $template_mapper->fetchAll( \WpProQuiz_Model_Template::TEMPLATE_TYPE_QUIZ, false );

			foreach ( $all_templates as $template ) {
				$template_data[] = [
					'value' => $template->getTemplateId(),
					'label' => $template->getName(),
				];
			}

			return $template_data;
		}

		/**
		 * Update response to send post passwords.
		 *
		 * @since 4.5.3
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WP_Post          $post     The post object.
		 * @param WP_REST_Request  $request  The request object.
		 */
		public function handle_post_passwords_in_request( $response, $post, $request ) {
			// @todo Get LD endpoint namespace and path via settings.
			if ( 'GET' === $request->get_method() && (
				preg_match( '/\/ldlms\/v2\/sfwd-courses\/[\d]+/', $request->get_route() ) ||
				preg_match( '/\/ldlms\/v2\/sfwd-lessons\/[\d]+/', $request->get_route() ) ||
				preg_match( '/\/ldlms\/v2\/sfwd-topic\/[\d]+/', $request->get_route() ) ||
				preg_match( '/\/ldlms\/v2\/groups\/[\d]+/', $request->get_route() ) ||
				preg_match( '/\/ldlms\/v2\/sfwd-quiz\/[\d]+/', $request->get_route() ) ) ) {
				$data = $response->get_data();

				if ( ! is_array( $data ) ) {
					$data = [];
				}

				if ( isset( $post->post_password ) ) {
					$data['password'] = $post->post_password;
					$response->set_data( $data );
				}
			}

			return $response;
		}
	}
}
