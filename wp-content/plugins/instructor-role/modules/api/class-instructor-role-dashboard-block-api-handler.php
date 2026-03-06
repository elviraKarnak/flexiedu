<?php
/**
 * Dashboard Block Rest API Handler Module
 *
 * @since 5.0.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

namespace InstructorRole\Modules\Api;

use InstructorRole\Modules\Classes\Instructor_Role_Overview;
use InstructorRole\Modules\Classes\Instructor_Role_Payouts;
use InstructorRole\Modules\Classes\Instructor_Role_Profile;
use LearnDash_Settings_Section;
use WP_Error;
use WP_REST_Posts_Controller;
use WP_Rest_Server;
use WP_Post, WP_Query;
use WP_Term;
use LearnDash\Instructor_Role\StellarWP\DB\DB;
use LearnDash\Instructor_Role\StellarWP\DB\QueryBuilder\JoinQueryBuilder;
use LearnDash\Core\Utilities\Cast;
use WP_REST_Request;
use WP_REST_Response;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Dashboard_Block_Api_Handler' ) ) {
	/**
	 * Class Instructor Role Api Handler
	 */
	class Instructor_Role_Dashboard_Block_Api_Handler extends WP_REST_Posts_Controller {
		/**
		 * Singleton instance of this class
		 *
		 * @var self $instance
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
		 * @since 4.4.0
		 *
		 * @return self
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
			// Get overview details.
			register_rest_route(
				$this->namespace,
				'/overview/',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_overview_data' ],
						'permission_callback' => [ $this, 'get_overview_data_permissions_check' ],
					],
				]
			);

			// Get course list details.
			register_rest_route(
				$this->namespace,
				'/course-list/',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_course_list' ],
						'permission_callback' => [ $this, 'get_course_list_permissions_check' ],
					],
				]
			);

			// Get quiz list details.
			register_rest_route(
				$this->namespace,
				'/quiz-list/',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_quiz_list' ],
						'permission_callback' => [ $this, 'get_quiz_list_permissions_check' ],
					],
				]
			);

			// Get course filter details.
			register_rest_route(
				$this->namespace,
				'course-list/filters/',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_course_filters_data' ],
						'permission_callback' => [ $this, 'get_course_filters_data_permissions_check' ],
					],
				]
			);

			// Get quiz filter details.
			register_rest_route(
				$this->namespace,
				'quiz-list/filters/',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_quiz_filters_data' ],
						'permission_callback' => [ $this, 'get_quiz_filters_data_permissions_check' ],
					],
				]
			);

			// Get quiz filtered lessons and topics.
			register_rest_route(
				$this->namespace,
				'quiz-list/filters/lessons',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_quiz_filtered_lessons_data' ],
						'permission_callback' => [ $this, 'get_quiz_filtered_lessons_data_permissions_check' ],
					],
				]
			);

			// Fetch and update settings.
			register_rest_route(
				$this->namespace,
				'/settings/',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_settings' ],
						'permission_callback' => [ $this, 'get_settings_permissions_check' ],
					],
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'update_settings' ],
						'permission_callback' => [ $this, 'update_settings_permissions_check' ],
						'args'                => [
							'first_name'          => [
								'description' => esc_html__( 'The first_name parameter is used to update first name of the user.', 'wdm_instructor_role' ),
								'type'        => 'string',
							],
							'last_name'           => [
								'description' => esc_html__( 'The last_name parameter is used to update last name of the user.', 'wdm_instructor_role' ),
								'type'        => 'string',
							],
							'nickname'            => [
								'description' => esc_html__( 'The nickname parameter is used to update nick name of the user.', 'wdm_instructor_role' ),
								'type'        => 'string',
							],
							'display_name'        => [
								'description' => esc_html__( 'The display_name parameter is used to update display name of the user.', 'wdm_instructor_role' ),
								'type'        => 'string',
							],
							'email'               => [
								'description'       => esc_html__( 'The email parameter is used to update the email address of the user.', 'wdm_instructor_role' ),
								'type'              => 'string',
								'validate_callback' => [ $this, 'validate_user_email' ],
							],
							'website'             => [
								'description' => esc_html__( 'The website parameter is used to update the website address of the user.', 'wdm_instructor_role' ),
								'type'        => 'string',
							],
							'bio'                 => [
								'description' => esc_html__( 'The bio parameter is used to update the biography details of the user.', 'wdm_instructor_role' ),
								'type'        => 'string',
							],
							'paypal_payout_email' => [
								'description' => esc_html__( 'The paypal_payout_email parameter is used to update the paypal payout email of the user.', 'wdm_instructor_role' ),
								'type'        => 'string',
							],
						],
					],
				]
			);

			// Trash courses.
			register_rest_route(
				$this->namespace,
				'/course-list/trash',
				[
					[
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => [ $this, 'trash_courses' ],
						'permission_callback' => [ $this, 'trash_courses_permissions_check' ],
					],
				]
			);

			// Restore courses.
			register_rest_route(
				$this->namespace,
				'/course-list/restore',
				[
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'restore_courses' ],
						'permission_callback' => [ $this, 'restore_courses_permissions_check' ],
					],
				]
			);

			// Trash quizzes.
			register_rest_route(
				$this->namespace,
				'/quiz-list/trash',
				[
					[
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => [ $this, 'trash_quizzes' ],
						'permission_callback' => [ $this, 'trash_quizzes_permissions_check' ],
					],
				]
			);

			// Restore quizzes.
			register_rest_route(
				$this->namespace,
				'/quiz-list/restore',
				[
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'restore_quizzes' ],
						'permission_callback' => [ $this, 'restore_quizzes_permissions_check' ],
					],
				]
			);

			// Get Commission Order Details.
			register_rest_route(
				$this->namespace,
				'/commission/order_details',
				[
					[
						'methods'             => WP_Rest_Server::READABLE,
						'callback'            => [ $this, 'get_commission_order_details' ],
						'permission_callback' => [ $this, 'get_commission_order_details_permissions_check' ],
						'args'                => [
							'page'          => [
								'type'    => 'integer',
								'default' => 1,
							],
							'instructor_id' => [
								'type' => 'integer',
							],
							'search'        => [
								'type' => 'string',
							],
							'per_page'      => [
								'type'    => 'integer',
								'default' => 5,
							],
							'start_date'    => [
								'type'   => 'string',
								'format' => 'date',
							],
							'end_date'      => [
								'type'   => 'string',
								'format' => 'date',
							],
						],
					],
				]
			);

			// Get Commission Manual transaction Details.
			register_rest_route(
				$this->namespace,
				'/commission/manual_transaction',
				[
					[
						'methods'             => WP_Rest_Server::READABLE,
						'callback'            => [ $this, 'get_commission_manual_transaction' ],
						'permission_callback' => [ $this, 'get_commission_manual_transaction_permissions_check' ],
						'args'                => [
							'page'          => [
								'type'    => 'integer',
								'default' => 1,
							],
							'instructor_id' => [
								'type' => 'integer',
							],
							'per_page'      => [
								'type'    => 'integer',
								'default' => 5,
							],
							'search'        => [
								'type'    => 'string',
								'default' => '',
							],
						],
					],
				]
			);

			// Get Commission Paypal transaction Details.
			register_rest_route(
				$this->namespace,
				'/commission/paypal_transaction',
				[
					[
						'methods'             => WP_Rest_Server::READABLE,
						'callback'            => [ $this, 'get_commission_paypal_transaction' ],
						'permission_callback' => [ $this, 'get_commission_paypal_transaction_permissions_check' ],
						'args'                => [
							'page'     => [
								'type'    => 'integer',
								'default' => 1,
							],
							'per_page' => [
								'type'    => 'integer',
								'default' => 5,
							],
						],
					],
				]
			);

			// Get Commission Paypal transaction Details.
			register_rest_route(
				$this->namespace,
				'/commission/paypal_transaction/single_payout',
				[
					[
						'methods'             => WP_Rest_Server::READABLE,
						'callback'            => [ $this, 'get_commission_paypal_single_payout_transaction' ],
						'permission_callback' => [ $this, 'get_commission_paypal_single_payout_transaction_permissions_check' ],
						'args'                => [
							'batch_id' => [
								'type'    => 'string',
								'default' => '',
							],
						],
					],
				]
			);

			// Get instructor earnings data.
			register_rest_route(
				$this->namespace,
				'/earnings/',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_earnings_graph_data' ],
						'permission_callback' => [ $this, 'get_earnings_graph_data_permissions_check' ],
						'args'                => [
							'instructor_id' => [
								'type' => 'integer',
							],
						],
					],
				]
			);

			// Get instructor list.
			register_rest_route(
				$this->namespace,
				'/instructor-list/',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_instructor_list' ],
						'permission_callback' => [ $this, 'get_instructor_list_permissions_check' ],
						'args'                => [
							'page' => [
								'type'    => 'integer',
								'default' => '1',
							],
						],
					],
				]
			);

			// Delete Instructor.
			register_rest_route(
				$this->namespace,
				'/instructor-list/trash/(?P<id>[\d]+)',
				[
					[
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => [ $this, 'delete_instructor' ],
						'permission_callback' => [ $this, 'delete_instructor_permissions_check' ],
					],
				]
			);
		}

		/**
		 * Get instructor overview page data
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_overview_data( $request ) {
			$data                 = [];
			$course_list          = [];
			$unique_students_list = [];
			$course_count         = 0;
			$student_count        = 0;

			// Get course list.
			$user_id = get_current_user_id();

			// Refresh shared courses.
			ir_refresh_shared_course_details( $user_id );

			// Final instructor course list.
			$course_list = ir_get_instructor_complete_course_list( $user_id );

			// No courses yet...
			if ( ! empty( $course_list ) && array_sum( $course_list ) > 0 ) {
				$course_count = count( $course_list );

				// Fetch the list of students in the courses.
				$all_students = [];
				foreach ( $course_list as $course_id ) {
					// Check if trashed course.
					if ( 'trash' == get_post_status( $course_id ) ) {
						--$course_count;
					}

					$students_list = ir_get_users_with_course_access( $course_id, [ 'direct', 'group' ] );

					if ( empty( $students_list ) ) {
						continue;
					}
					$all_students = array_merge( $all_students, $students_list );
				}

				$unique_students_list = array_unique( $all_students );
				$student_count        = count( $unique_students_list );
			}

			// Calculate quiz attempts.
			$quiz_attempts = $this->get_quiz_attempts_count( $unique_students_list, $course_list );

			// Get top courses.
			$top_courses = $this->get_top_courses( $course_list );

			// Get earnings data.
			$earnings_data = $this->get_earnings_data();

			// Get submissions data.
			$submissions = $this->get_submissions_data();

			// User Information data.
			$user_info = $this->get_user_details( $user_id );

			$data = [
				'student_count'     => $student_count,
				'course_count'      => $course_count,
				'submissions_count' => count( $submissions ),
				'submissions'       => $submissions,
				'quiz_attempts'     => $quiz_attempts,
				'top_courses'       => $top_courses,
				'earnings_data'     => $earnings_data,
				'user_info'         => $user_info,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get overview data permissions check
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_overview_data_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get submission reports data
		 *
		 * @since 5.0.0
		 *
		 * @return mixed
		 */
		public function get_submissions_data() {
			$submissions = [];

			$no_of_records = 10;
			$page_no       = 1;

			if ( ! class_exists( 'Instructor_Role_Overview' ) ) {
				require_once plugin_dir_path( __DIR__ ) . 'classes/class-instructor-role-overview.php';
			}
			$module_overview = Instructor_Role_Overview::get_instance();
			/**
			 * Allow 3rd party plugins to filter through the submissions array.
			 *
			 * @since 3.1.0
			 */
			$submissions = apply_filters( 'ir_overview_submissions', $module_overview->getSubmissionReportData( $page_no, $no_of_records ) );

			return $submissions;
		}

		/**
		 * Get quiz attempts count
		 *
		 * @since 5.0.0
		 *
		 * @param array $students_list      Array of students.
		 * @param array $course_list        Array of courses.
		 * @return int
		 */
		public function get_quiz_attempts_count( $students_list, $course_list ) {
			$quiz_attempts_count = 0;
			if ( empty( $students_list ) ) {
				return $quiz_attempts_count;
			}
			$course_quizzes = [];

			foreach ( $course_list as $course_id ) {
				$course_quizzes = array_merge( $course_quizzes, learndash_get_course_steps( $course_id, [ 'sfwd-quiz' ] ) );
			}

			foreach ( $students_list as $student_id ) {
				$student_attempts = 0;
				foreach ( $course_quizzes as $quiz_id ) {
					$student_attempts += count( learndash_get_user_quiz_attempts( $student_id, $quiz_id ) );
				}
				$quiz_attempts_count += $student_attempts;
			}

			return $quiz_attempts_count;
		}

		/**
		 * Get top courses based on no of students enrolled.
		 *
		 * @since 5.0.0
		 *
		 * @param array $course_list    Array of courses.
		 * @return array                Array of top courses.
		 */
		public function get_top_courses( $course_list ) {
			$data        = [];
			$top_courses = [];
			if ( empty( $course_list ) ) {
				return $data;
			}

			foreach ( $course_list as $course_id ) {
				$students_list             = ir_get_users_with_course_access( $course_id, [ 'direct', 'group' ] );
				$top_courses[ $course_id ] = count( $students_list );
			}

			arsort( $top_courses );

			foreach ( $top_courses as $course_id => $student_count ) {
				$course = get_post( $course_id );
				array_push(
					$data,
					[
						'id'             => $course_id,
						'title'          => $course->post_title,
						'total_students' => $student_count,
						'featured_image' => wp_get_attachment_url( get_post_thumbnail_id( $course->ID ) ),
						'price_type'     => learndash_get_setting( $course_id, 'course_price_type' ),
						'price_value'    => learndash_get_setting( $course_id, 'course_price' ),
					]
				);
			}

			return $data;
		}

		/**
		 * Get earnings commission data for the instructor
		 *
		 * @since 5.0.0
		 *
		 * @return array    Array of Earnings data.
		 */
		public function get_earnings_data() {
			$earnings_data      = [];
			$commission_records = ir_get_instructor_commission_records();

			$graph_data = $this->get_formatted_earnings( $commission_records );

			// Total commission.
			$total_commission = 0;
			foreach ( $commission_records as $record ) {
				$total_commission += floatval( $record->commission_price );
			}

			// Calculate paid and unpaid amount.
			$paid_amount   = floatval( get_user_meta( get_current_user_id(), 'wdm_total_amount_paid', 1 ) );
			$unpaid_amount = $total_commission - $paid_amount;

			$currency_code = function_exists( 'get_woocommerce_currency' )
				? get_woocommerce_currency()
				: learndash_get_currency_code();

			$earnings_data = [
				'total_commission'           => learndash_instructor_role_normalize_float_value( $total_commission ),
				'paid'                       => learndash_instructor_role_normalize_float_value( $paid_amount ),
				'unpaid'                     => learndash_instructor_role_normalize_float_value( $unpaid_amount ),
				'total_commission_formatted' => learndash_get_price_formatted( $total_commission, $currency_code ),
				'paid_formatted'             => learndash_get_price_formatted( $paid_amount, $currency_code ),
				'unpaid_formatted'           => learndash_get_price_formatted( $unpaid_amount, $currency_code ),
				'commission_data'            => $graph_data,
			];

			return $earnings_data;
		}

		/**
		 * Get course list permissions check
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_course_list_permissions_check( $request ) {
			return $this->group_leader_request_permission_check( $request );
		}

		/**
		 * Get instructor course list page data
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_course_list( $request ) {
			$data               = [];
			$found_courses      = [];
			$users              = [];
			$current_user_id    = get_current_user_id();
			$instructor_courses = [];

			$parameters = shortcode_atts(
				[
					'search'      => '',
					'page'        => 1,
					'no_of_posts' => 9,
					'status'      => 'any',
					'month'       => '',
					'price_type'  => '',
					'categories'  => [],
					'tags'        => [],
				],
				$request->get_params()
			);

			// Default query parameters.
			$args = [
				'post_type'      => learndash_get_post_type_slug( 'course' ),
				'posts_per_page' => $parameters['no_of_posts'],
				'post_status'    => 'any',
				'paged'          => $parameters['page'],
				'order_by'       => 'ID',
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

			// Filter by status.
			if ( 'any' !== $parameters['status'] ) {
				switch ( $parameters['status'] ) {
					case 'mine':
						$args['author'] = $current_user_id;
						break;

					case 'pending':
					case 'publish':
					case 'draft':
						$args['post_status'] = $parameters['status'];
						break;

					case 'trash':
						$args['post_status'] = $parameters['status'];
						if ( wdm_is_instructor( $current_user_id ) ) {
							$args['author'] = $current_user_id;
							unset( $args['post__in'] );
						}
						break;

					case 'shared':
						$shared_courses = ir_get_instructor_shared_course_list();
						if ( empty( $shared_courses ) ) {
							$shared_courses = [ 0 ];
						}
						$args['post__in'] = $shared_courses;
						break;
				}
			}

			// Filter by month.
			if ( ! empty( $parameters['month'] ) ) {
				$args['m'] = trim( $parameters['month'] );
			}

			// Filter by price type.
			if ( ! empty( $parameters['price_type'] ) ) {
				$args['meta_key']   = '_ld_price_type';
				$args['meta_value'] = trim( $parameters['price_type'] );
			}

			// Filter by LD Course Categories.
			if ( ! empty( $parameters['categories'] ) ) {
				$categories = explode( ',', $parameters['categories'] );

				if ( ! is_array( $categories ) ) {
					$categories = [ $categories ];
				}

				// Check if LD Categories enabled.
				if ( 'yes' === \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Taxonomies', 'ld_course_category' ) ) {
					$args['tax_query'] = [
						[
							'taxonomy' => 'ld_course_category',
							'field'    => 'term_id',
							'terms'    => $categories,
						],
					];
				}
			}

			// Filter by LD Course Tags.
			if ( ! empty( $parameters['tags'] ) ) {
				$tags = explode( ',', $parameters['tags'] );
				if ( ! is_array( $tags ) ) {
					$tags = [ trim( $tags ) ];
				}

				// Check if LD Tags enabled.
				if ( 'yes' === \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Taxonomies', 'ld_course_tag' ) ) {
					$args['tax_query'] = [
						[
							'taxonomy' => 'ld_course_tag',
							'field'    => 'term_id',
							'terms'    => $tags,
						],
					];
				}
			}

			// Find requested courses.
			$course_list_query = new WP_Query( $args );

			foreach ( $course_list_query->posts as $course ) {
				if ( ! array_key_exists( $course->post_author, $users ) ) {
					$users[ $course->post_author ] = get_userdata( $course->post_author );
				}

				$found_courses[] = $this->get_list_single( 'course', $course, $users );
			}

			// Final data.
			$data = [
				'posts'        => $found_courses,
				'posts_count'  => $course_list_query->post_count,
				'total_posts'  => $course_list_query->found_posts,
				'max_page_num' => $course_list_query->max_num_pages,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get quiz list permissions check
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_quiz_list_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get instructor quiz list page data
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_quiz_list( $request ) {
			$data               = [];
			$instructor_quizzes = [];
			$users              = [];
			$current_user_id    = get_current_user_id();
			$found_quizzes      = [];

			// Set Query parameters.
			$parameters = shortcode_atts(
				[
					'search'      => '',
					'page'        => 1,
					'no_of_posts' => 9,
					'status'      => 'any',
					'month'       => '',
					'categories'  => [],
					'tags'        => [],
					'course'      => '',
					'lesson'      => '',
					'topic'       => '',
				],
				$request->get_params()
			);

			// Default query parameters.
			$args = [
				'post_type'      => learndash_get_post_type_slug( 'quiz' ),
				'posts_per_page' => $parameters['no_of_posts'],
				'post_status'    => 'any',
				'paged'          => $parameters['page'],
				'order_by'       => 'ID',
			];

			// For instructor user.
			if ( wdm_is_instructor( $current_user_id ) ) {
				// Get instructor quiz list.
				$instructor_courses = ir_get_instructor_complete_course_list( $current_user_id, true, true );

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
						'post_status'    => 'any',
						'author'         => $current_user_id,
						'fields'         => 'ids',
					]
				);

				if ( ! empty( $orphan_quiz->posts ) ) {
					$instructor_quizzes = array_unique( array_merge( $instructor_quizzes, $orphan_quiz->posts ) );
				}

				if ( empty( $instructor_quizzes ) ) {
					$instructor_quizzes = [ 0 ];
				}

				$args['post__in'] = $instructor_quizzes;
			}

			// Search quizzes.
			if ( isset( $parameters['search'] ) && ! empty( $parameters['search'] ) ) {
				$args['s'] = trim( $parameters['search'] );
			}

			// Filter by status.
			if ( 'any' !== $parameters['status'] ) {
				switch ( $parameters['status'] ) {
					case 'mine':
						$args['author'] = $current_user_id;
						break;

					case 'publish':
						$args['post_status'] = $parameters['status'];
						break;

					case 'draft':
						$args['post_status'] = $parameters['status'];
						break;

					case 'trash':
						$args['post_status'] = $parameters['status'];
						if ( wdm_is_instructor( $current_user_id ) ) {
							$args['author'] = $current_user_id;
							unset( $args['post__in'] );
						}
						break;
				}
			}

			// Filter by month.
			if ( ! empty( $parameters['month'] ) ) {
				$args['m'] = trim( $parameters['month'] );
			}

			// Filter by course.
			if ( ! empty( $parameters['course'] ) ) {
				// Check if shared steps are enabled.
				if ( 'yes' === \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Builder', 'shared_steps' ) ) {
					$args['post__in'] = $this->get_filter_by_shared_steps( $parameters );
				} else {
					$args['meta_query'] = $this->get_filter_by_meta_query( $parameters );
				}
			}

			// Filter by LD Quiz Categories.
			if ( ! empty( $parameters['categories'] ) ) {
				$categories = explode( ',', $parameters['categories'] );

				if ( ! is_array( $categories ) ) {
					$categories = [ trim( $categories ) ];
				}

				// Check if LD Categories enabled.
				if ( 'yes' === \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Quizzes_Taxonomies', 'ld_quiz_category' ) ) {
					$args['tax_query'] = [
						[
							'taxonomy' => 'ld_quiz_category',
							'field'    => 'term_id',
							'terms'    => $categories,
						],
					];
				}
			}

			// Filter by LD Quiz Tags.
			if ( ! empty( $parameters['tags'] ) ) {
				$tags = explode( ',', $parameters['tags'] );
				if ( ! is_array( $tags ) ) {
					$tags = [ trim( $tags ) ];
				}

				// Check if LD Tags enabled.
				if ( 'yes' === \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Quizzes_Taxonomies', 'ld_quiz_tag' ) ) {
					$args['tax_query'] = [
						[
							'taxonomy' => 'ld_quiz_tag',
							'field'    => 'term_id',
							'terms'    => $tags,
						],
					];
				}
			}

			// Find requested quizzes.
			$quiz_list_query = new WP_Query( $args );

			foreach ( $quiz_list_query->posts as $quiz ) {
				if ( ! array_key_exists( $quiz->post_author, $users ) ) {
					$users[ $quiz->post_author ] = get_userdata( $quiz->post_author );
				}

				$found_quizzes[] = $this->get_list_single( 'quiz', $quiz, $users );
			}

			// Final data.
			$data = [
				'posts'        => $found_quizzes,
				'posts_count'  => $quiz_list_query->post_count,
				'total_posts'  => $quiz_list_query->found_posts,
				'max_page_num' => $quiz_list_query->max_num_pages,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get course list filters data permissions check
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_course_filters_data_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get data for course filters for the frontend dashboard
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_course_filters_data( $request ) {
			$data                 = [];
			$formatted_categories = [
				[
					'value' => '',
					'label' => __( 'All Categories', 'wdm_instructor_role' ),
				],
			];
			$formatted_tags       = [
				[
					'value' => '',
					'label' => __( 'All Tags', 'wdm_instructor_role' ),
				],
			];

			$user_id = get_current_user_id();

			// Get date filters data.
			$args = [
				'post_type'      => learndash_get_post_type_slug( 'course' ),
				'post_status'    => 'any',
				'posts_per_page' => -1,
			];

			if ( wdm_is_instructor( $user_id ) ) {
				$course_ids = ir_get_instructor_complete_course_list( get_current_user_id(), true );
				if ( empty( $course_ids ) ) {
					$course_ids = [ 0 ];
				}
				$args['post__in'] = $course_ids;
			}

			$course_list = new WP_Query( $args );

			$date_filter   = [];
			$date_keys     = [];
			$date_filter[] = [
				'value' => '',
				'label' => __( 'All dates', 'wdm_instructor_role' ),
			];

			foreach ( $course_list->posts as $single_course ) {
				$course_date = strtotime( $single_course->post_date );
				$key         = gmdate( 'Ym', $course_date );
				if ( ! in_array( $key, $date_keys ) ) {
					$date_filter[] = [
						'value' => gmdate( 'Ym', $course_date ),
						'label' => gmdate( 'F Y', $course_date ),
					];
					$date_keys[]   = gmdate( 'Ym', $course_date );
				}
			}

			// Price Type values.
			$price_filter = [];
			$price_filter = [
				[
					'value' => '',
					'label' => __( 'All price types', 'wdm_instructor_role' ),
				],
				[
					'value' => 'open',
					'label' => __( 'Open', 'wdm_instructor_role' ),
				],
				[
					'value' => 'free',
					'label' => __( 'Free', 'wdm_instructor_role' ),
				],
				[
					'value' => 'subscribe',
					'label' => __( 'Recurring', 'wdm_instructor_role' ),
				],
				[
					'value' => 'paynow',
					'label' => __( 'Buy Now', 'wdm_instructor_role' ),
				],
				[
					'value' => 'closed',
					'label' => __( 'Closed', 'wdm_instructor_role' ),
				],
			];

			// Course Categories.
			$course_categories = [];
			if ( \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Taxonomies', 'ld_course_category' ) === 'yes' ) {
				$course_categories = get_terms(
					[
						'taxonomy'   => 'ld_course_category',
						'hide_empty' => false,
					]
				);

				if ( is_array( $course_categories ) ) {
					foreach ( $course_categories as $category ) {
						if ( $category instanceof WP_Term ) {
							$formatted_categories[] = [
								'value' => $category->term_id,
								'label' => $category->name,
							];
						}
					}
				}
			}

			// Course Tags.
			$course_tags = [];
			if ( \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Taxonomies', 'ld_course_tag' ) === 'yes' ) {
				$course_tags = get_terms(
					[
						'taxonomy'   => 'ld_course_tag',
						'hide_empty' => false,
					]
				);
				foreach ( $course_tags as $tag ) {
					$formatted_tags[] = [
						'value' => $tag->term_id,
						'label' => $tag->name,
					];
				}
			}

			$data = [
				'date_filter'       => $date_filter,
				'price_filter'      => $price_filter,
				'course_categories' => $formatted_categories,
				'course_tags'       => $formatted_tags,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get quiz list filters data permissions check
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_quiz_filters_data_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get data for quiz filters for the frontend dashboard
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_quiz_filters_data( $request ) {
			$data                 = [];
			$formatted_categories = [
				[
					'value' => '',
					'label' => __( 'All Categories', 'wdm_instructor_role' ),
				],
			];
			$formatted_tags       = [
				[
					'value' => '',
					'label' => __( 'All Tags', 'wdm_instructor_role' ),
				],
			];

			$user_id = get_current_user_id();

			// Get Course Filter Args.
			$course_filter_args = [
				'post_type'      => learndash_get_post_type_slug( 'course' ),
				'post_status'    => 'any',
				'posts_per_page' => -1,
			];

			// Get Lesson Filter Args.
			$lesson_filter_args = [
				'post_type'      => learndash_get_post_type_slug( 'lesson' ),
				'post_status'    => 'any',
				'posts_per_page' => -1,
			];

			// Get Topic Filter Args.
			$topic_filter_args = [
				'post_type'      => learndash_get_post_type_slug( 'topic' ),
				'post_status'    => 'any',
				'posts_per_page' => -1,
			];

			// Get date filters data.
			$args = [
				'post_type'      => learndash_get_post_type_slug( 'quiz' ),
				'post_status'    => 'any',
				'posts_per_page' => -1,
			];

			if ( wdm_is_instructor( $user_id ) ) {
				$course_ids = ir_get_instructor_complete_course_list( get_current_user_id(), true );
				$lesson_ids = [];
				$topic_ids  = [];
				$quiz_ids   = [];

				foreach ( $course_ids as $course_id ) {
					$course_quiz = learndash_get_course_steps( $course_id, [ 'sfwd-quiz' ] );
					if ( ! empty( $course_quiz ) ) {
						$quiz_ids = array_merge( $quiz_ids, $course_quiz );
					}

					$course_lessons = learndash_get_course_steps( $course_id, [ 'sfwd-lessons' ] );
					$course_topics  = learndash_get_course_steps( $course_id, [ 'sfwd-topic' ] );
					if ( ! empty( $course_lessons ) ) {
						$lesson_ids = array_diff( $lesson_ids, $course_lessons );
					}
					if ( ! empty( $course_topics ) ) {
						$topic_ids = array_diff( $topic_ids, $course_topics );
					}
				}
				$quiz_ids         = array_unique( $quiz_ids );
				$args['post__in'] = empty( $quiz_ids ) ? [ 0 ] : $quiz_ids;

				$course_filter_args['post__in'] = empty( $course_ids ) ? [ 0 ] : $course_ids;
				$lesson_filter_args['post__in'] = empty( $lesson_ids ) ? [ 0 ] : $lesson_ids;
				$topic_filter_args['post__in']  = empty( $topic_ids ) ? [ 0 ] : $topic_ids;
			}

			$quiz_list = new WP_Query( $args );

			$date_filter[] = [
				'value' => '',
				'label' => __( 'All dates', 'wdm_instructor_role' ),
			];
			$date_keys     = [];

			foreach ( $quiz_list->posts as $single_quiz ) {
				$quiz_date = strtotime( $single_quiz->post_date );
				$key       = gmdate( 'Ym', $quiz_date );
				if ( ! in_array( $key, $date_keys ) ) {
					$date_filter[] = [
						'value' => gmdate( 'Ym', $quiz_date ),
						'label' => gmdate( 'F Y', $quiz_date ),
					];
					$date_keys[]   = gmdate( 'Ym', $quiz_date );
				}
			}

			// Quiz Categories.
			$quiz_categories = [];
			if ( \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Quizzes_Taxonomies', 'ld_quiz_category' ) === 'yes' ) {
				$quiz_categories = get_terms(
					[
						'taxonomy'   => 'ld_quiz_category',
						'hide_empty' => false,
					]
				);
				foreach ( $quiz_categories as $category ) {
					$formatted_categories[] = [
						'value' => $category->term_id,
						'label' => $category->name,
					];
				}
			}

			// Quiz Tags.
			$quiz_tags = [];
			if ( \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Quizzes_Taxonomies', 'ld_quiz_tag' ) === 'yes' ) {
				$quiz_tags = get_terms(
					[
						'taxonomy'   => 'ld_quiz_tag',
						'hide_empty' => false,
					]
				);
				foreach ( $quiz_tags as $tag ) {
					$formatted_tags[] = [
						'value' => $tag->term_id,
						'label' => $tag->name,
					];
				}
			}

			// Course Filter.
			$course_filter = $this->get_quiz_filter_options( 'courses', $course_filter_args );

			// Lesson Filter.
			$lesson_filter = $this->get_quiz_filter_options( 'lessons', $lesson_filter_args );

			// Topic Filter.
			$topic_filter = $this->get_quiz_filter_options( 'topics', $topic_filter_args );

			$data = [
				'date_filter'     => $date_filter,
				'quiz_categories' => $formatted_categories,
				'quiz_tags'       => $formatted_tags,
				'course_filter'   => $course_filter,
				'lesson_filter'   => $lesson_filter,
				'topic_filter'    => $topic_filter,
			];
			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get quiz list filtered lessons and topics data permissions check
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_quiz_filtered_lessons_data_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get data for quiz filtered lessons and topics for the frontend dashboard
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_quiz_filtered_lessons_data( $request ) {
			$data              = [];
			$filter_args       = [];
			$filtered_posts    = [];
			$user_id           = get_current_user_id();
			$has_course_access = false;

			// Get parameters.
			$parameters = shortcode_atts(
				[
					'course' => 0,
					'lesson' => 0,
					'type'   => 'lesson',
				],
				$request->get_params()
			);

			if ( ! empty( $parameters['course'] ) ) {
				$course_id = intval( $parameters['course'] );

				if ( wdm_is_instructor( $user_id ) ) {
					$course_ids = ir_get_instructor_complete_course_list( $user_id, true );
					if ( in_array( $course_id, $course_ids ) ) {
						$has_course_access = true;
					}
				}

				// Check if user has course access.
				if ( current_user_can( 'manage_options' ) || $has_course_access ) {
					// Return Lessons.
					if ( 'lesson' === $parameters['type'] ) {
						$slug = 'lessons';

						// Lesson Filter Args.
						$filter_args = [
							'post_type'      => learndash_get_post_type_slug( 'lesson' ),
							'post_status'    => 'any',
							'posts_per_page' => -1,
						];

						// Check if shared steps are enabled.
						if ( 'yes' === \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Builder', 'shared_steps' ) ) {
							$filtered_posts          = learndash_course_get_children_of_step( $course_id, 0, 'sfwd-lessons', 'ids', true );
							$filter_args['post__in'] = empty( $filtered_posts ) ? [ 0 ] : $filtered_posts;
						} else {
							$filter_args['meta_query'] = [
								[
									'key'   => 'course_id',
									'value' => $course_id,
								],
							];
						}
					} else {
						// Return Topics.
						$lesson_id = intval( $parameters['lesson'] );
						$slug      = 'topics';

						// Topic Filter Args.
						$filter_args = [
							'post_type'      => learndash_get_post_type_slug( 'topic' ),
							'post_status'    => 'any',
							'posts_per_page' => -1,
						];

						// Check if shared steps are enabled.
						if ( 'yes' === \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Builder', 'shared_steps' ) ) {
							$filtered_posts          = learndash_course_get_children_of_step( $course_id, $lesson_id, 'sfwd-topic', 'ids', true );
							$filter_args['post__in'] = empty( $filtered_posts ) ? [ 0 ] : $filtered_posts;
						} else {
							$filter_args['meta_query'] = [
								[
									'key'   => 'course_id',
									'value' => $course_id,
								],
								[
									'key'   => 'lesson_id',
									'value' => $lesson_id,
								],
							];
						}
					}

					$data = $this->get_quiz_filter_options( $slug, $filter_args );
				}
			} else {
				$data = [
					[
						'value' => '',
						'label' => sprintf(
							/* translators: Course, Lesson or Topic Label */
							__( 'All %s', 'wdm_instructor_role' ),
							\LearnDash_Custom_Label::get_label( $parameters['type'] . 's' )
						),
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
		 * Get user details used on the overview page.
		 *
		 * @since 5.0.0
		 *
		 * @param int $user_id    ID of the user.
		 * @return array          Array of user details.
		 */
		public function get_user_details( $user_id ) {
			$user_info = [];
			if ( empty( $user_id ) ) {
				$user_id = get_current_user_id();
			}
			$user_data = get_userdata( $user_id );

			if ( ! empty( $user_data ) ) {
				$user_info = [
					'ID'        => $user_data->ID,
					'email'     => $user_data->data->user_email,
					'name'      => $user_data->data->display_name,
					'image_url' => get_avatar_url( $user_id ),
				];
			}

			return $user_info;
		}

		/**
		 * Get settings permissions check
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_settings_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get settings data
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_settings( $request ) {
			$data = [];

			$user_id = get_current_user_id();

			$data = [
				'basic'               => $this->get_basic_section( $user_id ),
				'profile'             => $this->get_profile_section( $user_id ),
				'paypal_payout_email' => get_user_meta( $user_id, 'ir_paypal_payouts_email', true ),
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Update settings data
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function update_settings( $request ) {
			$data         = [];
			$social_links = [];

			$user_id = get_current_user_id();

			$update_userdata = [
				'ID' => $user_id,
			];

			$form_data = $request->get_body_params();

			// If empty get all params.
			if ( empty( $form_data ) ) {
				$form_data = $request->get_params();
			}

			// Update first name.
			if ( array_key_exists( 'first_name', $form_data ) ) {
				if ( update_user_meta( $user_id, 'first_name', $form_data['first_name'] ) ) {
					$data['first_name'] = $form_data['first_name'];
				}
			}

			// Update last name.
			if ( array_key_exists( 'last_name', $form_data ) ) {
				if ( update_user_meta( $user_id, 'last_name', $form_data['last_name'] ) ) {
					$data['last_name'] = $form_data['last_name'];
				}
			}

			// Update nick name.
			if ( array_key_exists( 'nickname', $form_data ) ) {
				if ( update_user_meta( $user_id, 'nickname', $form_data['nickname'] ) ) {
					$data['nickname'] = $form_data['nickname'];
				}
			}

			// Update bio.
			if ( array_key_exists( 'bio', $form_data ) ) {
				if ( update_user_meta( $user_id, 'description', $form_data['bio'] ) ) {
					$data['description'] = $form_data['bio'];
				}
			}

			// Update paypal email.
			if ( array_key_exists( 'paypal_payout_email', $form_data ) ) {
				if ( update_user_meta( $user_id, 'ir_paypal_payouts_email', $form_data['paypal_payout_email'] ) ) {
					$data['paypal_payout_email'] = $form_data['paypal_payout_email'];
				}
			}

			// Update website.
			if ( array_key_exists( 'website', $form_data ) ) {
				$update_userdata['user_url'] = $form_data['website'];
			}

			// Update display name.
			if ( array_key_exists( 'display_name', $form_data ) ) {
				$update_userdata['display_name'] = $form_data['display_name'];
			}

			// Update user email.
			if ( array_key_exists( 'email', $form_data ) ) {
				$update_userdata['user_email'] = $form_data['email'];
			}

			// Update user password.
			if ( array_key_exists( 'password', $form_data ) && array_key_exists( 'update_pass_nonce', $form_data ) && wp_verify_nonce( $form_data['update_pass_nonce'], 'ir-update-pass-' . $user_id ) ) {
				$update_userdata['user_pass'] = $form_data['password'];
			}

			// Check for social links update.
			if ( array_key_exists( 'facebook', $form_data ) || array_key_exists( 'twitter', $form_data ) || array_key_exists( 'youtube', $form_data ) ) {
				$social_links = get_user_meta( $user_id, 'ir_profile_social_links', 1 );

				// Update facebook link.
				if ( array_key_exists( 'facebook', $form_data ) ) {
					$social_links['facebook'] = $form_data['facebook'];
				}

				// Update twitter link.
				if ( array_key_exists( 'twitter', $form_data ) ) {
					$social_links['twitter'] = $form_data['twitter'];
				}

				// Update youtube link.
				if ( array_key_exists( 'youtube', $form_data ) ) {
					$social_links['youtube'] = $form_data['youtube'];
				}
			}

			// Update userdata.
			if ( ! empty( $update_userdata ) ) {
				if ( wp_update_user( $update_userdata ) ) {
					$data = $update_userdata + $data;
				}
			}

			// Update Introduction Sections Data.
			$data['intro_sections'] = $this->update_intro_sections_data( $user_id, $form_data );

			// Update social links.
			if ( ! empty( $social_links ) ) {
				update_user_meta( $user_id, 'ir_profile_social_links', $social_links );
			}
			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Update settings permissions check
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function update_settings_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Common REST permission checks for instructor requests.
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function instructor_request_permission_check( $request ) {
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
		 * Trash courses
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function trash_courses( $request ) {
			$data             = [];
			$user_id          = get_current_user_id();
			$delete           = false;
			$query_parameters = $request->get_params();

			// Get the course(s) to be trashed.
			$trash_ids = [];

			if ( isset( $query_parameters['courses'] ) ) {
				$trash_ids = explode( ',', $query_parameters['courses'] );
			}

			// Check whether to trash or permanently delete.
			if ( isset( $query_parameters['action'] ) && 'delete' === $query_parameters['action'] ) {
				$delete = true;
			}

			foreach ( $trash_ids as $course_id ) {
				$course = get_post( $course_id );

				// Check if valid course.
				if ( empty( $course ) || ! $course instanceof WP_Post || learndash_get_post_type_slug( 'course' ) !== $course->post_type ) {
					continue;
				}

				// Verify if user is course author or admin.
				if ( current_user_can( 'manage_options' ) || ( intval( $course->post_author ) === $user_id ) ) {
					// Trash or delete course.
					if ( ! $delete ) {
						$trashed_course    = wp_trash_post( $course_id );
						$data['trashed'][] = $trashed_course;
					} else {
						$deleted_course    = wp_delete_post( $course_id, $delete );
						$data['deleted'][] = $deleted_course;
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
		 * Trash courses permissions check
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function trash_courses_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Restore trashed courses.
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request  WP_REST_Request instance.
		 */
		public function restore_courses( $request ) {
			$data    = [];
			$user_id = get_current_user_id();

			$parameters = $request->get_body_params();

			// If empty get all params.
			if ( empty( $parameters ) ) {
				$parameters = $request->get_params();
			}

			// Get the course(s) to be restored.
			$restore_ids = [];

			if ( isset( $parameters['courses'] ) ) {
				$restore_ids = explode( ',', $parameters['courses'] );
			}

			foreach ( $restore_ids as $course_id ) {
				$course = get_post( $course_id );

				// Check if valid trashed course.
				if ( empty( $course ) || ! $course instanceof WP_Post || learndash_get_post_type_slug( 'course' ) !== $course->post_type || 'trash' !== $course->post_status ) {
					continue;
				}

				// Verify if user is course author or admin.
				if ( current_user_can( 'manage_options' ) || ( intval( $course->post_author ) === $user_id ) ) {
					// Restore course.
					$restored_course = wp_untrash_post( $course_id );
					if ( ! empty( $restored_course ) ) {
						$data['restored'][] = $restored_course;
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
		 * Restore courses permissions check
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function restore_courses_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Trash quizzes
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function trash_quizzes( $request ) {
			$data             = [];
			$user_id          = get_current_user_id();
			$query_parameters = $request->get_params();
			$delete           = false;

			// Get the quiz(zes) to be trashed.
			$trash_ids = [];
			if ( isset( $query_parameters['quizzes'] ) ) {
				$trash_ids = explode( ',', $query_parameters['quizzes'] );
			}

			// Check whether to trash or permanently delete.
			if ( isset( $query_parameters['action'] ) && 'delete' === $query_parameters['action'] ) {
				$delete = true;
			}

			foreach ( $trash_ids as $quiz_id ) {
				$quiz = get_post( $quiz_id );
				// Check if valid quiz.
				if ( empty( $quiz ) || ! $quiz instanceof WP_Post || learndash_get_post_type_slug( 'quiz' ) !== $quiz->post_type ) {
					continue;
				}

				// Verify if user is quiz author or admin.
				if ( current_user_can( 'manage_options' ) || intval( $quiz->post_author ) === $user_id ) {
					if ( ! $delete ) {
						$trashed_quiz      = wp_trash_post( $quiz_id );
						$data['trashed'][] = $trashed_quiz;
					} else {
						$deleted_quiz      = wp_delete_post( $quiz_id, $delete );
						$data['deleted'][] = $deleted_quiz;
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
		 * Trash courses permissions check
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function trash_quizzes_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Restore trashed quizzes.
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request  WP_REST_Request instance.
		 */
		public function restore_quizzes( $request ) {
			$data    = [];
			$user_id = get_current_user_id();

			$parameters = $request->get_body_params();

			// If empty get all params.
			if ( empty( $parameters ) ) {
				$parameters = $request->get_params();
			}

			// Get the quiz(zes) to be restored.
			$restore_ids = [];

			if ( isset( $parameters['quizzes'] ) ) {
				$restore_ids = explode( ',', $parameters['quizzes'] );
			}

			foreach ( $restore_ids as $quiz_id ) {
				$quiz = get_post( $quiz_id );

				// Check if valid trashed course.
				if ( empty( $quiz ) || ! $quiz instanceof WP_Post || learndash_get_post_type_slug( 'quiz' ) !== $quiz->post_type || 'trash' !== $quiz->post_status ) {
					continue;
				}

				// Verify if user is quiz author or admin.
				if ( current_user_can( 'manage_options' ) || ( intval( $quiz->post_author ) === $user_id ) ) {
					// Restore quiz.
					$restored_quiz = wp_untrash_post( $quiz_id );
					if ( ! empty( $restored_quiz ) ) {
						$data['restored'][] = $restored_quiz;
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
		 * Restore quizzes permissions check
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function restore_quizzes_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get course or quiz list single entry
		 *
		 * @since 5.0.0
		 *
		 * @param string  $type     One of 'course' or 'quiz'.
		 * @param WP_Post $post     WP_Post object.
		 * @param array   $users    Array of users.
		 * @return array            Formatted array of single entry data.
		 */
		public function get_list_single( $type, $post, $users ) {
			$supported_post_types = [
				'course',
				'quiz',
				'product',
				'group',
				'certificate',
			];

			// Check if valid type value.
			if ( ! in_array( $type, $supported_post_types, true ) ) {
				return [];
			}

			$author_name = $users[ $post->post_author ]->display_name;

			$category_taxonomy = 'category';
			$tag_taxonomy      = 'tag';
			$edit_url          = add_query_arg(
				[
					'post'      => $post->ID,
					'post_type' => $post->post_type,
				]
			);

			switch ( $type ) {
				case 'course':
					$category_taxonomy = 'ld_course_category';
					$tag_taxonomy      = 'ld_course_tag';
					$edit_url          = get_site_url() . '/course-builder/' . $post->ID;
					break;

				case 'quiz':
					$category_taxonomy = 'ld_quiz_category';
					$tag_taxonomy      = 'ld_quiz_tag';
					$edit_url          = get_site_url() . '/quiz-builder/' . $post->ID;
					break;

				case 'product':
					$category_taxonomy = 'product_cat';
					$tag_taxonomy      = 'product_tag';
					break;

				case 'group':
					$category_taxonomy = 'ld_group_category';
					$tag_taxonomy      = 'ld_group_tag';
					break;

				default:
					break;
			}

			$categories = '';
			$tags       = '';
			if ( 'group' !== $type ) {
				$categories = $this->get_formatted_terms( $post, $category_taxonomy );
				$tags       = $this->get_formatted_terms( $post, $tag_taxonomy );
			}

			// Clone URL.
			$clone_url = add_query_arg(
				[
					'action'    => 'learndash_cloning_action_course',
					'object_id' => $post->ID,
					'nonce'     => wp_create_nonce( 'learndash_cloning_action_course' . $post->ID ),
				],
				admin_url( 'admin-post.php' )
			);

			$data = [
				'id'             => $post->ID,
				'title'          => html_entity_decode( $post->post_title ),
				'author_url'     => get_avatar_url( $post->post_author ),
				'author'         => $author_name,
				'date'           => $post->post_date,
				'status'         => $post->post_status,
				'view_url'       => get_the_permalink( $post ),
				'edit_url'       => $edit_url,
				'categories'     => $categories,
				'tags'           => $tags,
				'featured_image' => wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) ),
				'clone_url'      => $clone_url,
			];

			if ( 'course' === $type || 'group' === $type ) {
				$data['price_type']  = learndash_get_setting( $post->ID, "{$type}_price_type" );
				$data['price_type']  = empty( $data['price_type'] ) ? 'open' : $data['price_type'];
				$data['price_value'] = learndash_get_price_formatted( floatval( learndash_get_setting( $post->ID, "{$type}_price" ) ) );

				$data = apply_filters( "ir_filter_{$type}_list_single", $data, $post, $users );
			}

			/**
			 * Filter API response for a single post entry on listing pages.
			 *
			 * @since 5.0.1
			 *
			 * @param array   $data     Array of returned post data.
			 * @param WP_Post $post     Post Object.
			 * @param string  $type     Type of the post.
			 * @param array   $users    Array of user details.
			 */
			return apply_filters( 'ir_filter_api_post_list_single', $data, $post, $type, $users );
		}

		/**
		 * Get taxonomy terms in a formatted manner.
		 *
		 * Example: If 4 terms are returned then they will be returned in following format
		 *          Red, Blue +2 more.
		 *
		 * @since 5.0.0
		 *
		 * @param WP_Post $post         WP_Post object.
		 * @param string  $taxonomy     Taxonomy slug.
		 * @return string               Formatted list of taxonomy terms.
		 */
		public function get_formatted_terms( $post, $taxonomy ) {
			$formatted_terms = '';
			$terms           = get_the_terms( $post, $taxonomy );

			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				return $formatted_terms;
			}

			$count = count( $terms );

			$term_names = array_map(
				function ( $element ) {
					return $element->name;
				},
				array_slice( $terms, 0, 2 )
			);

			$formatted_terms = implode( ',', $term_names );
			if ( $count > 2 ) {
				$formatted_terms .= sprintf(
					/* translators: Count of additional terms. */
					__( ' + %d more', 'wdm_instructor_role' ),
					$count - 2
				);
			}

			return $formatted_terms;
		}

		/**
		 * Get basic user information section details
		 *
		 * @since 5.0.0
		 *
		 * @param int $user_id  ID of the user.
		 * @return array        Array of user information.
		 */
		public function get_basic_section( $user_id ) {
			$profile_data = [];

			if ( empty( $user_id ) ) {
				$user_id = get_current_user_id();
			}

			$user_data = get_userdata( $user_id );

			$profile_data = [
				'first_name'   => get_user_meta( $user_id, 'first_name', 1 ),
				'last_name'    => get_user_meta( $user_id, 'last_name', 1 ),
				'nickname'     => get_user_meta( $user_id, 'nickname', 1 ),
				'display_name' => $user_data->data->display_name,
				'username'     => $user_data->data->user_login,
				'email'        => $user_data->data->user_email,
				'website'      => $user_data->data->user_url,
				'image'        => get_avatar_url( $user_id ),
			];

			$display_name_options = [
				$profile_data['nickname'],
				$profile_data['username'],
			];

			if ( ! empty( $profile_data['first_name'] ) ) {
				$display_name_options[] = $profile_data['first_name'];
			}

			if ( ! empty( $profile_data['last_name'] ) ) {
				$display_name_options[] = $profile_data['last_name'];
			}

			if ( ! empty( $profile_data['first_name'] ) && ! empty( $profile_data['last_name'] ) ) {
				$display_name_options[] = $profile_data['first_name'] . ' ' . $profile_data['last_name'];
				$display_name_options[] = $profile_data['last_name'] . ' ' . $profile_data['first_name'];
			}

			if ( ! in_array( $profile_data['display_name'], $display_name_options, true ) ) {
				// Only add this if it isn't duplicated elsewhere.
				$display_name_options[] = $profile_data['display_name'];
			}

			$display_name_options = array_map( 'trim', $display_name_options );
			$display_name_options = array_unique( $display_name_options );

			$profile_data['display_name_options'] = $display_name_options;

			return $profile_data;
		}

		/**
		 * Get user profile section information details.
		 *
		 * @since 5.0.0
		 *
		 * @param int $user_id  ID of the user.
		 * @return array        Array of instructor profile information.
		 */
		public function get_profile_section( $user_id ) {
			$instructor_profile_details = [];

			// Get social links data.
			$social_links = get_user_meta( $user_id, 'ir_profile_social_links', true );
			$social_links = shortcode_atts(
				[
					'facebook' => '',
					'twitter'  => '',
					'youtube'  => '',
				],
				$social_links
			);

			$instructor_profile_details = [
				'link'                  => ir_get_instructor_profile_link( $user_id ),
				'introduction_sections' => $this->get_intro_sections_data( $user_id ),
				'bio'                   => get_user_meta( $user_id, 'description', 1 ),
				'social'                => $social_links,
			];

			// Profile Page Link.
			return $instructor_profile_details;
		}

		/**
		 * Get introduction sections data for instructor profile
		 *
		 * @since 5.0.0
		 *
		 * @param int $user_id  ID of the User.
		 * @return array        Array of intro sections data for the instructor.
		 */
		public function get_intro_sections_data( $user_id ) {
			$sections_data  = [];
			$module_profile = Instructor_Role_Profile::get_instance();
			$sections_meta  = $module_profile->fetch_introduction_settings_data();

			foreach ( $sections_meta as $section_meta ) {
				$sections_data[] = [
					'title'     => $section_meta['title'],
					'image'     => $section_meta['image'],
					'data_type' => $section_meta['data_type'],
					'icon'      => $section_meta['icon'],
					'meta_key'  => $section_meta['meta_key'],
					'data'      => get_user_meta( $user_id, $section_meta['meta_key'], 1 ),
				];
			}

			return $sections_data;
		}

		/**
		 * Validate user email.
		 *
		 * @param mixed                         $value   Value of the 'filter' argument.
		 * @param WP_REST_Request<array{mixed}> $request The current request object.
		 * @param string                        $param   Key of the parameter. In this case it is 'filter'.
		 * @return WP_Error|boolean
		 */
		public function validate_user_email( $value, $request, $param ) {
			if ( ! is_email( $value ) ) {
				return new WP_Error( 'rest_invalid_param', esc_html__( 'The email argument must be a valid email.', 'wdm_instructor_role' ), [ 'status' => 400 ] );
			}
		}

		/**
		 * Update introduction sections data for instructor profile
		 *
		 * @since 5.0.0
		 *
		 * @param int   $user_id    ID of the User.
		 * @param array $form_data  Form data to be saved.
		 * @return array            Array of intro sections data for the instructor.
		 */
		public function update_intro_sections_data( $user_id, $form_data ) {
			$data = [];
			// Get introduction sections data.
			$module_profile             = Instructor_Role_Profile::get_instance();
			$introduction_settings_data = $module_profile->fetch_introduction_settings_data();

			foreach ( $introduction_settings_data as $section_details ) {
				// Check if section data set.
				if ( array_key_exists( $section_details['meta_key'], $form_data ) && ! empty( $form_data[ $section_details['meta_key'] ] ) ) {
					$section_value = empty( $form_data[ $section_details['meta_key'] ] ) ? '' : $form_data[ $section_details['meta_key'] ];

					/**
					 * Filter introduction section value before it is saved in user meta
					 *
					 * @since 3.5.0
					 *
					 * @param mixed $section_value      Section value to be saved.
					 * @param int   $user_id            User ID of the instructor.
					 * @param array $section_details    Details of the section.
					 */
					$section_value = apply_filters( 'ir_filter_save_introduction_section_value', $section_value, $user_id, $section_details );

					// Update.
					if ( update_user_meta(
						$user_id,
						$section_details['meta_key'],
						$section_value
					)
					) {
						$data[ $section_details['meta_key'] ] = $section_value;
					}
				}
			}

			return $data;
		}

		/**
		 * Get quiz filtering options for course, lesson or topic.
		 *
		 * @since 5.0.0
		 *
		 * @param string $post_type_slug   Post type slug.
		 * @param array  $args             WP_Query arguments.
		 * @return array                   Array of formatted filtering options.
		 */
		public function get_quiz_filter_options( $post_type_slug, $args ) {
			$filter_options = [];
			if ( empty( $post_type_slug ) ) {
				return $filter_options;
			}
			$filter_options = [
				[
					'value' => '',
					'label' => sprintf(
						/* translators: Course, Lesson or Topic Label */
						__( 'All %s', 'wdm_instructor_role' ),
						\LearnDash_Custom_Label::get_label( $post_type_slug )
					),
				],
			];

			$filter_query = new WP_Query( $args );

			foreach ( $filter_query->posts as $post ) {
				$filter_options[] = [
					'value' => $post->ID,
					'label' => html_entity_decode( $post->post_title ),
				];
			}

			return $filter_options;
		}

		/**
		 * Get quiz filter meta query
		 *
		 * @since 5.0.0
		 *
		 * @param array $parameters     Array of parameters.
		 * @return array
		 */
		public function get_filter_by_meta_query( $parameters ) {
			$meta_query = [];

			$meta_query[] = [
				'key'   => 'course_id',
				'value' => $parameters['course'],
			];

			if ( ! empty( $parameters['lesson'] ) ) {
				// Filter by lesson.
				if ( empty( $parameters['topic'] ) ) {
					$lesson_list   = array_column( learndash_course_get_topics( $parameters['course'], $parameters['lesson'] ), 'ID' );
					$lesson_list[] = $parameters['lesson'];

					$meta_query[] = [
						'key'     => 'lesson_id',
						'value'   => $lesson_list,
						'compare' => 'IN',
					];
				} else {
					// Filter by topic.
					$meta_query[] = [
						'key'   => 'lesson_id',
						'value' => $parameters['topic'],
					];
				}
			}

			return $meta_query;
		}

		/**
		 * Get filtered posts when shared steps are enabled.
		 *
		 * @since 5.0.0
		 *
		 * @param array $parameters    Array of query parameters.
		 * @return array                Array of filtered posts.
		 */
		public function get_filter_by_shared_steps( $parameters ) {
			$filtered_posts = [];

			$course = intval( $parameters['course'] );
			$lesson = intval( $parameters['lesson'] );
			$topic  = intval( $parameters['topic'] );

			if ( ! empty( $lesson ) ) {
				// Filter by course, lesson and topic.
				if ( ! empty( $topic ) ) {
					$filtered_posts = learndash_course_get_children_of_step( $course, $topic, 'sfwd-quiz' );
				} else {
					// Filter by course and lesson.
					$filtered_posts = learndash_course_get_children_of_step( $course, $lesson, 'sfwd-quiz', 'ids', true );
				}
			} else {
				// Filter by course.
				$filtered_posts = learndash_course_get_children_of_step( $course, 0, 'sfwd-quiz', 'ids', true );
			}

			if ( empty( $filtered_posts ) ) {
				$filtered_posts = [ 0 ];
			}

			return $filtered_posts;
		}

		/**
		 * Fetch commission order details.
		 *
		 * @param WP_REST_Request<array{mixed}> $request  The request object.
		 */
		public function get_commission_order_details( $request ) {
			// Add a $instructor_id to receive through the request.
			$instructor_id     = Cast::to_int( $request->get_param( 'instructor_id' ) );
			$page              = $request->get_param( 'page' );
			$per_page          = $request->get_param( 'per_page' );
			$search            = $request->get_param( 'search' );
			$start_date        = Cast::to_string( $request->get_param( 'start_date' ) );
			$end_date          = Cast::to_string( $request->get_param( 'end_date' ) );
			$offset            = ( $page - 1 ) * $per_page;
			$search_conditions = '';

			if ( empty( $instructor_id ) ) {
				$instructor_id = get_current_user_id();
			}

			global $wpdb;
			$instructor_order_details = [];

			// Build the search conditions for each column.
			if ( ! empty( $search ) ) {
				$search_conditions = $wpdb->prepare(
					" AND ( order_id LIKE %s
					OR {$wpdb->prefix}posts.post_title LIKE %s
					OR actual_price LIKE %s
					OR commission_price LIKE %s
					)",
					'%' . $wpdb->esc_like( $search ) . '%',
					'%' . $wpdb->esc_like( $search ) . '%',
					'%' . $wpdb->esc_like( $search ) . '%',
					'%' . $wpdb->esc_like( $search ) . '%'
				);
			}

			// Add additional conditions for start and end date.
			if ( ! empty( $start_date ) ) {
				$start_date         = gmdate(
					'Y-m-d',
					Cast::to_int( strtotime( $start_date ) )
				);
				$search_conditions .= " AND transaction_time >= '$start_date 00:00:00'";
			}

			if ( ! empty( $end_date ) ) {
				$end_date           = gmdate(
					'Y-m-d',
					Cast::to_int( strtotime( $end_date ) )
				);
				$search_conditions .= " AND transaction_time <= '$end_date 23:59:59'";
			}

			// Retrieve the total count of items.
			$total_count_sql = "SELECT COUNT(*)
				FROM {$wpdb->prefix}wdm_instructor_commission
				LEFT JOIN {$wpdb->prefix}posts
				ON {$wpdb->prefix}wdm_instructor_commission.product_id = {$wpdb->prefix}posts.ID
				WHERE user_id = %d" . $search_conditions;

			$total_count = $wpdb->get_var( $wpdb->remove_placeholder_escape( $wpdb->prepare( $total_count_sql, $instructor_id ) ) );

			// Retrieve the paginated results.
			$sql = "SELECT *, {$wpdb->prefix}posts.post_title AS course_name
			FROM {$wpdb->prefix}wdm_instructor_commission
			LEFT JOIN {$wpdb->prefix}posts
			ON {$wpdb->prefix}wdm_instructor_commission.product_id = {$wpdb->prefix}posts.ID
			WHERE user_id = %d{$search_conditions}
			ORDER BY transaction_time DESC
			LIMIT %d OFFSET %d";

			$results = $wpdb->get_results( $wpdb->remove_placeholder_escape( $wpdb->prepare( $sql, $instructor_id, $per_page, $offset ) ) );

			if ( ! empty( $results ) ) {
				$amount_paid = 0;
				foreach ( $results as $value ) {
					$amount_paid += $value->commission_price;

					$currency_code = $value->product_type === 'WC' && function_exists( 'get_woocommerce_currency' )
						? get_woocommerce_currency()
						: learndash_get_currency_code();

					$instructor_order_details[] = [
						'order_id'                   => $value->order_id,
						'date'                       => $value->transaction_time,
						'course_edit_link'           => $this->get_post_edit_link( $value->product_id ),
						'course_title'               => $this->get_post_title( $value->product_id ),
						'actual_price'               => learndash_instructor_role_normalize_float_value( $value->actual_price ),
						'commission_price'           => learndash_instructor_role_normalize_float_value( $value->commission_price ),
						'product_type'               => $value->product_type,
						'currency'                   => learndash_get_currency_symbol( $currency_code ),
						'product_price_formatted'    => learndash_get_price_formatted( $value->actual_price, $currency_code ),
						'commission_price_formatted' => learndash_get_price_formatted( $value->commission_price, $currency_code ),
					];
				}
			}

			// Prepare the response.
			$data = [
				'order_details' => $instructor_order_details,
				'pagination'    => [
					'current_page' => $page,
					'total_pages'  => ceil( $total_count / $per_page ),
					'total_items'  => $total_count,
				],
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Fetches the post edit link to be displayed
		 *
		 * @param int $post_id Post ID of the post.
		 */
		public function get_post_edit_link( $post_id ) {
			if ( get_post_status( $post_id ) === false ) {
				return 'style="pointer-event:none;"';
			}
			return 'href="' . site_url( 'wp-admin/post.php?post=' . $post_id . '&action=edit' ) . '"';
		}

		/**
		 * Fetches the post title to be displayed
		 *
		 * @param int $post_id Post ID of the post.
		 */
		public function get_post_title( $post_id ) {
			$post_title = get_the_title( $post_id );
			if ( empty( $post_title ) ) {
				return sprintf(
					/* translators: Course label */
					__( 'Product/ %s has been deleted !', 'wdm_instructor_role' ),
					\LearnDash_Custom_Label::get_label( 'Course' )
				);
			}
			return $post_title;
		}

		/**
		 * Restore commission permissions check
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_commission_order_details_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Fetch the manual transaction history.
		 *
		 * @param WP_REST_Request<array{mixed}> $request  The request object.
		 */
		public function get_commission_manual_transaction( $request ) {
			$instructor_id     = $request->get_param( 'instructor_id' );
			$page              = $request->get_param( 'page' );
			$per_page          = $request->get_param( 'per_page' );
			$search            = $request->get_param( 'search' );
			$search_conditions = '';
			$currency_code     = function_exists( 'get_woocommerce_currency' )
				? get_woocommerce_currency()
				: learndash_get_currency_code();
			$offset            = ( $page - 1 ) * $per_page;

			if ( empty( $instructor_id ) ) {
				$instructor_id = get_current_user_id();
			}

			global $wpdb;
			$manual_transaction_log = [];

			// Build the search conditions for each column.
			if ( ! empty( $search ) ) {
				$search_conditions = $wpdb->prepare(
					' AND (
						amount LIKE %s
						OR remaining LIKE %s
						OR notes LIKE %s
					)',
					'%' . $wpdb->esc_like( $search ) . '%',
					'%' . $wpdb->esc_like( $search ) . '%',
					'%' . $wpdb->esc_like( $search ) . '%'
				);
			}

			// Retrieve the total count of items.
			$total_count_sql = "SELECT COUNT(*) FROM {$wpdb->prefix}ir_commission_logs
			WHERE user_id = %d {$search_conditions}";
			$total_count     = $wpdb->get_var( $wpdb->remove_placeholder_escape( $wpdb->prepare( $total_count_sql, $instructor_id ) ) );

			// Retrieve the paginated results.
			$sql     = "SELECT * FROM {$wpdb->prefix}ir_commission_logs
			WHERE user_id = %d {$search_conditions} ORDER BY date_time DESC LIMIT %d OFFSET %d";
			$results = $wpdb->get_results( $wpdb->remove_placeholder_escape( $wpdb->prepare( $sql, $instructor_id, $per_page, $offset ) ) );

			if ( ! empty( $results ) ) {
				foreach ( $results as $value ) {
					$row                      = [
						'id'                  => $value->id,
						'date'                => strtotime( $value->date_time ),
						'amount'              => number_format_i18n( $value->amount, 2 ),
						'remaining'           => number_format_i18n( $value->remaining, 2 ),
						'note'                => $value->notes,
						'currency'            => learndash_get_currency_symbol( $currency_code ),
						'amount_formatted'    => learndash_get_price_formatted( $value->amount, $currency_code ),
						'remaining_formatted' => learndash_get_price_formatted( $value->remaining, $currency_code ),
					];
					$manual_transaction_log[] = $row;
				}
			}

			// Prepare the response.
			$data = [
				'manual_transaction_log' => $manual_transaction_log,
				'pagination'             => [
					'current_page' => $page,
					'total_pages'  => ceil( $total_count / $per_page ),
					'total_items'  => $total_count,
				],
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Restore commission permissions check
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_commission_manual_transaction_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Fetch the paypal transaction history
		 *
		 * @param WP_REST_Request<array{mixed}> $request  The request object.
		 */
		public function get_commission_paypal_transaction( $request ) {
			$instructor_id = $request->get_param( 'instructor_id' );
			$page          = $request->get_param( 'page' );
			$per_page      = $request->get_param( 'per_page' );
			$offset        = ( $page - 1 ) * $per_page;
			$currency_code = get_option( 'ir_payout_currency' );

			if ( empty( $instructor_id ) ) {
				$instructor_id = get_current_user_id();
			}

			if ( empty( $currency_code ) ) {
				// Try to get the currency code using the same logic we have in other places to be consistent.
				$currency_code = function_exists( 'get_woocommerce_currency' )
						? get_woocommerce_currency()
						: learndash_get_currency_code();
			}

			global $wpdb;
			$paypal_transaction_log = [];

			// Retrieve the total count of items.
			$total_count_sql = "SELECT COUNT(*) FROM {$wpdb->prefix}ir_paypal_payouts_transactions
			WHERE user_id = %d";
			$total_count     = $wpdb->get_var( $wpdb->prepare( $total_count_sql, $instructor_id ) );

			// Retrieve the paginated results.
			$sql     = "SELECT * FROM {$wpdb->prefix}ir_paypal_payouts_transactions
			WHERE user_id = %d ORDER BY id DESC LIMIT %d OFFSET %d";
			$results = $wpdb->get_results( $wpdb->prepare( $sql, $instructor_id, $per_page, $offset ) );

			if ( ! empty( $results ) ) {
				foreach ( $results as $value ) {
					$row                      = [
						'batch_id'         => $value->batch_id,
						'amount'           => learndash_instructor_role_normalize_float_value( $value->amount ),
						'amount_formatted' => learndash_get_price_formatted( $value->amount, $currency_code ),
						'type'             => $value->type,
						'status'           => $value->status,
						'currency'         => learndash_get_currency_symbol( $currency_code ),
					];
					$paypal_transaction_log[] = $row;
				}
			}

			// Prepare the response.
			$data = [
				'paypal_transaction_log' => $paypal_transaction_log,
				'pagination'             => [
					'current_page' => $page,
					'total_pages'  => ceil( $total_count / $per_page ),
					'total_items'  => $total_count,
				],
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Restore commission permissions check
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_commission_paypal_transaction_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Fetch the single payout for Paypal Transaction
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_commission_paypal_single_payout_transaction( $request ) {
			$batch_id                      = $request->get_param( 'batch_id' );
			$instructor_role_payout_object = Instructor_Role_Payouts::get_instance();
			$single_payout_details_object  = $instructor_role_payout_object->get_payout_request_details( $batch_id );
			$single_payout_details         = [];
			if ( is_object( $single_payout_details_object ) ) {
				$row = [
					'amount'             => learndash_get_price_formatted(
						$single_payout_details_object->result->batch_header->amount->value,
						Cast::to_string( $single_payout_details_object->result->batch_header->amount->currency )
					),
					'status'             => $single_payout_details_object->statusCode,
					'time_created'       => ir_get_date_in_site_timezone( $single_payout_details_object->result->batch_header->time_created ),
					'transaction_status' => $single_payout_details_object->result->items[0]->transaction_status,
					'error'              => $single_payout_details_object->result->items[0]->errors->name,
					'description'        => $single_payout_details_object->result->items[0]->errors->message,

				];
				$single_payout_details[] = $row;
			}

			// Prepare the response.
			$data = [
				'batch_id'              => $batch_id,
				'single_payout_details' => $single_payout_details,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Restore commission permissions check
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_commission_paypal_single_payout_transaction_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get graph formatted earnings data.
		 *
		 * @since 5.0.0
		 *
		 * @param array $records    Array of commission records.
		 * @return array            List of commission records in graph formatted manner.
		 */
		public function get_formatted_earnings( $records ) {
			$formatted_data = [];

			if ( empty( $records ) ) {
				return $formatted_data;
			}

			$commissions_data = [];

			foreach ( $records as $record ) {
				$timestamp   = '';
				$date_string = '';

				$date_string = explode( ' ', $record->transaction_time )[0];
				$time_stamp  = strtotime( $date_string );

				if ( ! array_key_exists( $time_stamp, $commissions_data ) ) {
					$commissions_data[ $time_stamp ] = $record->commission_price;
				} else {
					$commissions_data[ $time_stamp ] += $record->commission_price;
				}
			}

			foreach ( $commissions_data as $timestamp => $amount ) {
				$formatted_data[] = [
					'timestamp' => $timestamp * 1000,
					'amount'    => learndash_instructor_role_normalize_float_value( $amount ),
				];
			}

			return $formatted_data;
		}

		/**
		 * Fetch the earnings data for instructor.
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_earnings_graph_data( $request ) {
			$data = [];

			$instructor_id = Cast::to_int( $request->get_param( 'instructor_id' ) );

			if ( empty( $instructor_id ) ) {
				$instructor_id = get_current_user_id();
			}

			$commission_records = ir_get_instructor_commission_records( $instructor_id );

			$graph_data = $this->get_formatted_earnings( $commission_records );

			// Total commission.
			$total_commission = 0;
			foreach ( $commission_records as $record ) {
				$total_commission += floatval( $record->commission_price );
			}

			// Calculate paid and unpaid amounts.

			$paid_amount   = floatval( get_user_meta( $instructor_id, 'wdm_total_amount_paid', 1 ) );
			$unpaid_amount = $total_commission - $paid_amount;

			$currency_code = function_exists( 'get_woocommerce_currency' )
				? get_woocommerce_currency()
				: learndash_get_currency_code();

			$data = [
				'total_commission'           => learndash_instructor_role_normalize_float_value( $total_commission ),
				'paid'                       => learndash_instructor_role_normalize_float_value( $paid_amount ),
				'unpaid'                     => learndash_instructor_role_normalize_float_value( $unpaid_amount ),
				'total_commission_formatted' => learndash_get_price_formatted( $total_commission, $currency_code ),
				'paid_formatted'             => learndash_get_price_formatted( $paid_amount, $currency_code ),
				'unpaid_formatted'           => learndash_get_price_formatted( $unpaid_amount, $currency_code ),
				'commission_data'            => $graph_data,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get earnings data permission check.
		 *
		 * @since 5.0.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_earnings_graph_data_permissions_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();

			$instructor_id = Cast::to_int( $request->get_param( 'instructor_id' ) );

			if ( empty( $instructor_id ) ) {
				$instructor_id = get_current_user_id();
			}

			// If admin, no further checks needed.
			if ( ! wdm_is_instructor( $current_user_id ) || $current_user_id !== $instructor_id ) {
				return new WP_Error( 'ir_rest_not_allowed', esc_html__( 'You are not allowed access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			return true;
		}

		/**
		 * Common REST permission checks for group leader requests.
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function group_leader_request_permission_check( $request ) {
			// If admin, no further checks needed.
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}

			// Check if logged in user and instructor.
			$current_user_id = get_current_user_id();
			if ( empty( $current_user_id ) || ! learndash_is_group_leader_user( $current_user_id ) ) {
				return new WP_Error( 'ir_rest_not_logged_in', esc_html__( 'You are not currently logged in as an instructor or group leader.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if Learndash active.
			if ( ! class_exists( 'LDLMS_Factory_Post' ) ) {
				return new WP_Error( 'ir_rest_plugin_not_found', esc_html__( 'LearnDash is not activated.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
			return true;
		}

		/**
		 * Allow Rest API access for groups, essays and assignment post types.
		 *
		 * @param array  $args          Array of arguments before registering post type.
		 * @param string $post_type     Post type slug.
		 *
		 * @return array                Updated array of arguments
		 */
		public function allow_rest_api_access( $args, $post_type ) {
			// Check if logged-in user is an instructor or an administrator.
			if (
				! wdm_is_instructor()
				&& ! current_user_can( LEARNDASH_ADMIN_CAPABILITY_CHECK )
			) {
				return $args;
			}

			$valid_post_types = [
				learndash_get_post_type_slug( 'essay' ),
				learndash_get_post_type_slug( 'group' ),
				learndash_get_post_type_slug( 'assignment' ),
			];

			// Check if valid post type.
			if ( in_array( $post_type, $valid_post_types, 1 ) ) {
				// Check whether rest api access enabled.
				if ( array_key_exists( 'show_in_rest', $args ) && ! $args['show_in_rest'] ) {
					// Allow rest api access.
					$args['show_in_rest'] = true;
				}
			}
			return $args;
		}

		/**
		 * Get instructor list data
		 *
		 * @since 5.9.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 *
		 * @return WP_REST_Response|WP_Error WP_REST_Response on success, WP_Error on failure.
		 */
		public function get_instructor_list( $request ) {
			global $wpdb;
			$data     = [];
			$search   = Cast::to_string( $request->get_param( 'search' ) );
			$sort     = Cast::to_string( $request->get_param( 'sort' ) );
			$page     = Cast::to_int( $request->get_param( 'page' ) );
			$page     = $page > 0 ? $page : 1;
			$per_page = Cast::to_int( $request->get_param( 'per_page' ) );
			$per_page = $per_page > 0 ? $per_page : 10;
			$offset   = ( $page - 1 ) * $per_page;

			$base_query = DB::table( DB::raw( $wpdb->users ), 'users' )
				->innerJoin(
					DB::raw( $wpdb->usermeta ),
					'users.ID',
					'usermeta.user_id',
					'usermeta'
				)
				->join(
					function ( JoinQueryBuilder $builder ) use ( $wpdb ) {
						$builder
						->leftJoin(
							DB::raw( $wpdb->usermeta ),
							'commission_percentage'
						)
						->on(
							'users.ID',
							'commission_percentage.user_id'
						)
						->andOn(
							'commission_percentage.meta_key',
							'wdm_commission_percentage',
							true
						);
					}
				)
				->join(
					function ( JoinQueryBuilder $builder ) use ( $wpdb ) {
						$builder
						->leftJoin(
							DB::raw( $wpdb->usermeta ),
							'commission_disabled'
						)
						->on(
							'users.ID',
							'commission_disabled.user_id'
						)
						->andOn(
							'commission_disabled.meta_key',
							'ir_commission_disabled',
							true
						);
					}
				)
				->where(
					'usermeta.meta_key',
					"{$wpdb->prefix}capabilities"
				)
				->whereLike(
					'usermeta.meta_value',
					'wdm_instructor'
				);

			// Searching instructors logic.
			if ( ! empty( $search ) ) {
				$base_query->whereLike(
					'users.user_login',
					DB::esc_like( $search )
				);
			}

			$total_count_query = clone $base_query;
			$total_count       = $total_count_query->count( 'users.ID' );

			$instructor_listing_query = clone $base_query;
			$instructor_listing_query
				->select(
					[ 'users.ID', 'instructor_id' ],
					[ 'users.user_login', 'instructor_name' ],
					[ 'users.user_email', 'instructor_email' ],
					[ 'commission_percentage.meta_value', 'commission_percentage' ],
					[ 'commission_disabled.meta_value', 'commission_disabled' ],
				)
				->limit( $per_page )
				->offset( $offset );

			// Sorting instructors logic.
			if ( ! empty( $sort ) ) {
				$instructor_listing_query->orderBy(
					'CAST(commission_percentage.meta_value AS DECIMAL(10,2))',
					$sort === 'high_to_low' ? 'DESC' : 'ASC'
				);
			} else {
				$instructor_listing_query->orderBy(
					'users.user_registered',
					'DESC'
				);
			}

			$instructor_listing = $instructor_listing_query->getAll();
			if ( ! is_array( $instructor_listing ) ) {
				$instructor_listing = [];
			}

			// Fetching Gravatar for each user.
			foreach ( $instructor_listing as $instructor ) {
				$gravatar_url                = get_avatar_url( $instructor->instructor_email, [ 'size' => 96 ] );
				$instructor->profile_link    = get_site_url( null, 'instructor/' . $instructor->instructor_name . '/' );
				$instructor->frontend_link   = get_site_url( null, 'wp-login.php?frontend=1&action=wdm_ir_switch_user&user_id=' . $instructor->instructor_id . '' );
				$instructor->backend_link    = get_site_url( null, 'wp-login.php?action=wdm_ir_switch_user&user_id=' . $instructor->instructor_id . '' );
				$instructor->gravatar_url    = $gravatar_url;
				$instructor->profile_enabled = ir_get_settings( 'ir_enable_profile_links' );
			}

			$data = [
				'posts'        => $instructor_listing,
				'posts_count'  => ( ! empty( $instructor_listing ) ) ? count( $instructor_listing ) : 0,
				'total_posts'  => $total_count,
				'max_page_num' => ceil( $total_count / $per_page ),
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			if ( ! is_wp_error( $response ) ) {
				// Add a custom status code.
				$response->set_status( 200 );
			}

			return $response;
		}

		/**
		 * Get instructor list data permissions check
		 *
		 * @since 5.9.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function get_instructor_list_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Allow instructors LD Rest API Access.
		 *
		 * @since 5.8.0
		 *
		 * @param WP_REST_Response              $response   Result to send to the client.
		 * @param array                         $handler    Route handler used for the request.
		 * @param WP_REST_Request<array{mixed}> $request    Request used to generate the response.
		 *
		 * @return WP_REST_Response|mixed Updated result to send for the request. mixed is to account for callback functions that may return something incorrect.
		 */
		public function allow_instructor_ld_api_access( $response, $handler, $request ) {
			// Check whether user is instructor.
			$user_id = get_current_user_id();
			if ( ! wdm_is_instructor( $user_id ) ) {
				return $response;
			}

			// Check if LD API request.
			$route = $request->get_route();
			if ( false !== strpos( $route, LEARNDASH_REST_API_NAMESPACE ) ) {
				$course_namespace     = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_REST_API', 'sfwd-courses' );
				$course_namespace     = empty( $course_namespace ) ? 'sfwd-courses' : $course_namespace;
				$quiz_namespace       = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_REST_API', 'sfwd-quiz ' );
				$quiz_namespace       = empty( $quiz_namespace ) ? 'sfwd-quiz' : $quiz_namespace;
				$essay_namespace      = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_REST_API', 'sfwd-essay ' );
				$essay_namespace      = empty( $essay_namespace ) ? 'sfwd-essay' : $essay_namespace;
				$assignment_namespace = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_REST_API', 'sfwd-assignment ' );
				$assignment_namespace = empty( $assignment_namespace ) ? 'sfwd-assignment' : $assignment_namespace;
				$question_namespace   = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_REST_API', 'sfwd-question ' );
				$question_namespace   = empty( $question_namespace ) ? 'sfwd-question' : $question_namespace;
				$lesson_namespace     = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_REST_API', 'sfwd-lessons ' );
				$lesson_namespace     = empty( $lesson_namespace ) ? 'sfwd-lessons' : $lesson_namespace;
				$topic_namespace      = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_REST_API', 'sfwd-topic ' );
				$topic_namespace      = empty( $topic_namespace ) ? 'sfwd-topic' : $topic_namespace;
				$group_namespace      = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_REST_API', 'groups ' );
				$group_namespace      = empty( $group_namespace ) ? 'groups' : $group_namespace;

				// Course Requests.
				if ( false !== strpos( $route, $course_namespace ) ) {
					// If get single course request, verify is course accessible.
					if ( array_key_exists( 'id', $request->get_params() ) ) {
						$course_id = Cast::to_int( $request->get_param( 'id' ) );

						if ( ! in_array( $course_id, ir_get_instructor_complete_course_list( $user_id, 1 ), true ) ) {
							return $response;
						}
					}

					return call_user_func( $handler['callback'], $request );
				}

				// Lesson and Topic Requests.
				if ( false !== strpos( $route, $lesson_namespace ) || false !== strpos( $route, $topic_namespace ) ) {
					// If get single lesson or topic request, verify is parent course accessible.
					if ( array_key_exists( 'course_id', $request->get_params() ) ) {
						$course_id = Cast::to_int( $request->get_param( 'course_id' ) );
						if ( in_array( $course_id, ir_get_instructor_complete_course_list( $user_id, 1 ), true ) ) {
							return call_user_func( $handler['callback'], $request );
						}
					}
				}

				// Quiz Requests.
				if ( false !== strpos( $route, $quiz_namespace ) ) {
					// If get single quiz request, verify is quiz accessible.
					if ( array_key_exists( 'id', $request->get_params() ) ) {
						$quiz_id = Cast::to_int( $request->get_param( 'id' ) );
						if ( in_array( $quiz_id, ir_get_instructor_complete_quiz_list( $user_id ), 1 ) ) {
							return call_user_func( $handler['callback'], $request );
						}
					}
				}

				// Essay Requests.
				if ( false !== strpos( $route, $essay_namespace ) ) {
					// Verify is essay accessible.
					if ( array_key_exists( 'id', $request->get_params() ) ) {
						$essay_id = Cast::to_int( $request->get_param( 'id' ) );
						$quiz_id  = Cast::to_int( get_post_meta( $essay_id, 'quiz_post_id', true ) );

						if ( in_array( $quiz_id, ir_get_instructor_complete_quiz_list( $user_id ), 1 ) ) {
							return call_user_func( $handler['callback'], $request );
						}
					}
				}

				// Assignment Requests.
				if ( false !== strpos( $route, $assignment_namespace ) ) {
					// Verify is assignment accessible.
					if ( array_key_exists( 'id', $request->get_params() ) ) {
						$assignment_id = Cast::to_int( $request->get_param( 'id' ) );
						$course_id     = Cast::to_int( get_post_meta( $assignment_id, 'course_id', true ) );

						if ( in_array( $course_id, ir_get_instructor_complete_course_list( $user_id ), 1 ) ) {
							return call_user_func( $handler['callback'], $request );
						}
					}
				}

				// Questions Requests.
				if ( false !== strpos( $route, $question_namespace ) ) {
					// Get quiz ID and check if accessible.
					if ( array_key_exists( 'quiz', $request->get_params() ) ) {
						$quiz_id = Cast::to_int( $request->get_param( 'quiz' ) );

						if ( in_array( $quiz_id, ir_get_instructor_complete_quiz_list( $user_id ), 1 ) ) {
							return call_user_func( $handler['callback'], $request );
						}
					}
				}

				// Group Requests.
				if ( false !== strpos( $route, $group_namespace ) ) {
					// Get group ID and check if accessible.
					if ( array_key_exists( 'id', $request->get_params() ) ) {
						$group_id = Cast::to_int( $request->get_param( 'id' ) );
						$group    = get_post( $group_id );

						if ( Cast::to_int( $group->post_author ) === $user_id ) {
							return call_user_func( $handler['callback'], $request );
						}
					}
				}
			}

			return $response;
		}

		/**
		 * Delete instructor.
		 *
		 * @since 5.9.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function delete_instructor( $request ) {
			$instructor_id = Cast::to_int( $request->get_param( 'id' ) );

			// Check if user is admin.
			if ( ! current_user_can( 'manage_options' ) || empty( $instructor_id ) || false === get_userdata( $instructor_id ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'You do not have sufficient privileges to perform this action', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			if ( ! function_exists( 'wp_delete_user' ) ) {
				require_once ABSPATH . 'wp-admin/includes/user.php';
			}
			$data = wp_delete_user( $instructor_id, get_current_user_id() );

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Delete Instructor permissions check.
		 *
		 * @since 5.9.0
		 *
		 * @param WP_REST_Request<array{mixed}> $request WP_REST_Request instance.
		 */
		public function delete_instructor_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}
	}
}
