<?php
/**
 * Submissions Rest API Handler Module
 *
 * @since 5.2.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

namespace InstructorRole\Modules\Api;

use WP_Rest_Server;
use WP_Error;
use WP_REST_Posts_Controller;
use WP_Post, WP_Query;
use WP_User;
use WP_User_Query;
use WP_REST_Request;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Submissions_Api_Handler' ) ) {
	/**
	 * Class Instructor Role Submissions Api Handler
	 */
	class Instructor_Role_Submissions_Api_Handler extends Instructor_Role_Dashboard_Block_Api_Handler {
		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
		 *
		 * @since 5.2.0
		 */
		protected static $instance = null;

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 5.2.0
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
		 * @since 5.2.0
		 */
		public function register_custom_endpoints() {
			// List Assignment Lessons and Topics.
			register_rest_route(
				$this->namespace,
				'/assignments/lessons',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_assignments_lessons' ],
						'permission_callback' => [ $this, 'get_assignments_lessons_permissions_check' ],
						'args'                => [
							'search'  => [
								'description' => esc_html__( 'Limit results to those matching a string.', 'wdm_instructor_role' ),
								'type'        => 'string',
							],
							'page'    => [
								'description' => esc_html__( 'Current page of the collection.', 'wdm_instructor_role' ),
								'type'        => 'integer',
								'default'     => 1,
							],
							'orderby' => [
								'description' => esc_html__( 'Sort results by selected parameter.', 'wdm_instructor_role' ),
								'type'        => 'string',
								'default'     => 'latest',
							],
							'course'  => [
								'description' => esc_html__( 'Limit results to selected course.', 'wdm_instructor_role' ),
								'type'        => 'string',
							],
							'lesson'  => [
								'description' => esc_html__( 'Limit results to selected lesson', 'wdm_instructor_role' ),
								'type'        => 'string',
							],
							'topic'   => [
								'description' => esc_html__( 'Limit results to selected topic.', 'wdm_instructor_role' ),
								'type'        => 'string',
							],
						],
					],
				]
			);

			// Get Assignment Lesson and Topic filter details.
			register_rest_route(
				$this->namespace,
				'/assignments/lessons/filters',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_assignments_lessons_filters_data' ],
						'permission_callback' => [ $this, 'get_assignments_lessons_filters_data_permissions_check' ],
					],
				]
			);

			// List Submission Lesson Assignments.
			register_rest_route(
				$this->namespace,
				'/assignments/lessons/(?P<id>[\d]+)',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_lesson_assignments' ],
						'permission_callback' => [ $this, 'get_lesson_assignments_permissions_check' ],
					],
				]
			);

			// List Submission Lesson Assignments Filters.
			register_rest_route(
				$this->namespace,
				'/assignments/lessons/(?P<id>[\d]+)/filters',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_lesson_assignments_filters_data' ],
						'permission_callback' => [ $this, 'get_lesson_assignments_filters_data_permissions_check' ],
					],
				]
			);

			// Trash assignments.
			register_rest_route(
				$this->namespace,
				'/assignments/trash',
				[
					[
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => [ $this, 'trash_assignments' ],
						'permission_callback' => [ $this, 'trash_assignments_permissions_check' ],
					],
				]
			);

			// Restore assignments.
			register_rest_route(
				$this->namespace,
				'/assignments/restore',
				[
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'restore_assignments' ],
						'permission_callback' => [ $this, 'restore_assignments_permissions_check' ],
					],
				]
			);

			// Assignments Endpoint.
			register_rest_route(
				$this->namespace,
				'/assignments/(?P<id>[\d]+)',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_assignment' ],
						'permission_callback' => [ $this, 'get_assignment_permissions_check' ],
					],
				]
			);

			// Search Students Endpoint.
			register_rest_route(
				$this->namespace,
				'/students',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_students' ],
						'permission_callback' => [ $this, 'get_students_permissions_check' ],
					],
				]
			);

			// Assignments Comments Endpoint.
			register_rest_route(
				$this->namespace,
				'/assignments/comments/(?P<id>[\d]+)',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_assignment_comments' ],
						'permission_callback' => [ $this, 'get_assignment_comments_permissions_check' ],
					],
				]
			);
		}

		/**
		 * Get assignment lessons permissions check
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_assignments_lessons_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get instructor assignments lessons and topics list page data
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_assignments_lessons( $request ) {
			$data            = [];
			$found_lessons   = [];
			$current_user_id = get_current_user_id();

			// Extract query parameters.
			$parameters = shortcode_atts(
				[
					'search'      => '',
					'page'        => 1,
					'no_of_posts' => 7,
					'order_by'    => 'latest',
					'course'      => 0,
				],
				$request->get_params()
			);

			// For instructor user.
			if ( wdm_is_instructor( $current_user_id ) ) {
				$instructor_courses = ir_get_instructor_complete_course_list( $current_user_id, true );
				if ( empty( $instructor_courses ) ) {
					$instructor_courses = [ 0 ];
				}
				$parameters['instructor_courses'] = $instructor_courses;
			}

			// Filter results to selected course.
			if ( ! empty( $parameters['course'] ) ) {
				$parameters['course'] = intval( $parameters['course'] );
			}

			// Find requested submissions.
			$query_results = $this->get_query_results( $parameters );

			foreach ( $query_results['posts'] as $lesson ) {
				$course_list = array_unique( explode( ',', $lesson->courses ) );
				$courses     = $this->get_formatted_courses_info( $course_list );

				$post            = get_post( $lesson->lesson_id );
				$found_lessons[] = [
					'id'                   => $post->ID,
					'title'                => html_entity_decode( get_the_title( $post->ID ) ),
					'date'                 => $lesson->last_updated,
					'view_url'             => get_the_permalink( $post->ID ),
					'course'               => $courses['pretty'],
					'course_full'          => $courses['full'],
					'lesson'               => ( learndash_get_post_type_slug( 'topic' ) === $post->post_type ) ? get_the_title( learndash_get_lesson_id( $post->ID ) ) : '',
					'type'                 => ( learndash_get_post_type_slug( 'topic' ) === $post->post_type ) ? 'topic' : 'lesson',
					'submission_count'     => intval( $lesson->no_of_submissions ),
					'not_graded_count'     => intval( $lesson->no_of_not_graded ),
					'has_multiple_parents' => ( count( $course_list ) > 1 ) ? true : false,
				];
			}

			// Final data.
			$data = [
				'posts'        => $found_lessons,
				'posts_count'  => count( $found_lessons ),
				'total_posts'  => intval( $query_results['total'] ),
				'max_page_num' => ( 0 === intval( $query_results['total'] % intval( $parameters['no_of_posts'] ) ) ) ? intval( $query_results['total'] / intval( $parameters['no_of_posts'] ) ) : ( intval( $query_results['total'] / intval( $parameters['no_of_posts'] ) ) ) + 1,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get lessons assignments permissions check
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_lesson_assignments_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get instructor lessons and topics assignments list page data
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_lesson_assignments( $request ) {
			$data              = [];
			$found_submissions = [];
			$current_user_id   = get_current_user_id();

			$post = get_post( $request['id'] );

			// Check if valid WP_Post object.
			if ( empty( $post ) || ! $post instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			if ( ! current_user_can( 'manage_options' ) && ! in_array( $post->ID, ir_get_instructor_course_steps( [ 'lesson', 'topic' ] ), true ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			$parameters = shortcode_atts(
				[
					'search'          => '',
					'page'            => 1,
					'no_of_posts'     => 7,
					'status'          => 'any',
					'month'           => '',
					'approval_status' => '',
					'student_id'      => '',
				],
				$request->get_params()
			);

			// Default query parameters.
			$args = [
				'post_type'      => learndash_get_post_type_slug( 'assignment' ),
				'posts_per_page' => $parameters['no_of_posts'],
				'post_status'    => 'any',
				'paged'          => $parameters['page'],
				'order_by'       => 'author',
				'meta_query'     => [
					[
						'key'   => 'lesson_id',
						'value' => $post->ID,
					],
				],
			];

			// Search assignments.
			if ( isset( $parameters['search'] ) && ! empty( $parameters['search'] ) ) {
				$args['s'] = trim( $parameters['search'] );
			}

			// Filter by student ID.
			if ( isset( $parameters['student_id'] ) && ! empty( $parameters['student_id'] ) ) {
				$args['author'] = trim( $parameters['student_id'] );
			}

			// Filter by month.
			if ( ! empty( $parameters['month'] ) ) {
				$args['m'] = trim( $parameters['month'] );
			}

			// Filter by approval status.
			if ( ! empty( $parameters['approval_status'] ) ) {
				if ( 'not_approved' === $parameters['approval_status'] ) {
					$approval_meta_query = [
						'key'     => 'approval_status',
						'compare' => 'NOT EXISTS',
					];
				} else {
					$approval_meta_query = [
						'key'   => 'approval_status',
						'value' => '1',
					];
				}
				$args['meta_query'][] = $approval_meta_query;
			}

			// Filter results for instructor user.
			if ( wdm_is_instructor( $current_user_id ) ) {
				$instructor_courses = ir_get_instructor_complete_course_list( $current_user_id, true );
				if ( ! empty( $instructor_courses ) ) {
					$args['meta_query'][] = [
						'key'     => 'course_id',
						'compare' => 'IN',
						'value'   => $instructor_courses,
					];
				}
			}

			// Find requested submissions.
			$assignments_list_query = new WP_Query( $args );

			// Check if points enabled.
			$points_enabled = learndash_get_setting( $post->ID, 'lesson_assignment_points_enabled' );
			$lesson_points  = '';
			if ( 'on' === $points_enabled ) {
				$lesson_points = learndash_get_setting( $post, 'lesson_assignment_points_amount' );
			}
			$user_list = [];
			foreach ( $assignments_list_query->posts as $assignment ) {
				if ( ! $assignment instanceof WP_Post ) {
					continue;
				}

				if ( ! array_key_exists( $assignment->post_author, $user_list ) ) {
					$user_list[ $assignment->post_author ] = get_userdata( $assignment->post_author );
				}
				$course_id = get_post_meta( $assignment->ID, 'course_id', 1 );

				// It was added in LearnDash 4.10.3 only.
				// If it exists, we should use it instead of the direct link.
				if ( function_exists( 'learndash_assignment_get_download_url' ) ) {
					$download_link = learndash_assignment_get_download_url( $assignment->ID );
				} else {
					$download_link = get_post_meta( $assignment->ID, 'file_link', 1 );
				}

				$found_submissions[] = [
					'id'              => $assignment->ID,
					'title'           => html_entity_decode( $assignment->post_title ),
					'author_url'      => get_avatar_url( $assignment->post_author ),
					'author_name'     => $user_list[ $assignment->post_author ]->display_name,
					'date'            => $assignment->post_date,
					'status'          => $assignment->post_status,
					'points_enabled'  => $points_enabled,
					'scored_points'   => get_post_meta( $assignment->ID, 'points', 1 ),
					'total_points'    => $lesson_points,
					'file_name'       => get_post_meta( $assignment->ID, 'file_name', 1 ),
					'download_link'   => $download_link,
					'approval_status' => get_post_meta( $assignment->ID, 'approval_status', 1 ),
					'comments_count'  => $assignment->comment_count,
					'view_url'        => get_the_permalink( $assignment ),
					'course'          => get_the_title( $course_id ),
					'lesson'          => ( learndash_get_post_type_slug( 'lesson' ) === $post->post_type ) ? $post->post_title : get_the_title( learndash_get_lesson_id( $post->ID, $course_id ) ),
					'type'            => ( learndash_get_post_type_slug( 'topic' ) === $post->post_type ) ? 'topic' : 'lesson',

				];
			}

			// Final data.
			$data = [
				'posts'        => $found_submissions,
				'posts_count'  => $assignments_list_query->post_count,
				'total_posts'  => $assignments_list_query->found_posts,
				'max_page_num' => $assignments_list_query->max_num_pages,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get data for assignment lesson for the frontend dashboard
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_assignments_lessons_filters_data( $request ) {
			$data    = [];
			$user_id = get_current_user_id();

			// Get Course Filter Args.
			$course_filter_args = [
				'post_type'      => learndash_get_post_type_slug( 'course' ),
				'post_status'    => 'any',
				'posts_per_page' => -1,
			];

			if ( wdm_is_instructor( $user_id ) ) {
				$course_ids = ir_get_instructor_complete_course_list( get_current_user_id(), true );

				$course_filter_args['post__in'] = empty( $course_ids ) ? [ 0 ] : $course_ids;
			}

			$course_filter = $this->get_quiz_filter_options( 'courses', $course_filter_args );

			$data = [
				'course_filter' => $course_filter,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get lessons list filtered lessons and topics data permissions check
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_assignments_lessons_filters_data_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get data for assignment filters for the frontend dashboard
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_lesson_assignments_filters_data( $request ) {
			$data = [];
			$post = get_post( $request['id'] );

			// Check if valid WP_Post object.
			if ( empty( $post ) || ! $post instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			if ( ! current_user_can( 'manage_options' ) && ! in_array( $post->ID, ir_get_instructor_course_steps( [ 'lesson', 'topic' ] ), true ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Get date filters data.
			$args = [
				'post_type'      => learndash_get_post_type_slug( 'assignment' ),
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'meta_query'     => [
					[
						'key'   => 'lesson_id',
						'value' => $post->ID,
					],
				],
			];

			$assignment_list = new WP_Query( $args );

			$date_filter   = [];
			$date_keys     = [];
			$date_filter[] = [
				'value' => '',
				'label' => __( 'All dates', 'wdm_instructor_role' ),
			];

			foreach ( $assignment_list->posts as $single_course ) {
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

			$data = [
				'date_filter' => $date_filter,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get lessons list filtered lessons and topics data permissions check
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_lesson_assignments_filters_data_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get assignment lessons and topics by querying the database.
		 *
		 * @since 5.2.0
		 *
		 * @param array $args   Array of arguments to filter the results.
		 * @return array        Array of results.
		 */
		public function get_query_results( $args ) {
			$results = [];
			global $wpdb;

			$select       = '';
			$select_count = '';
			$where        = [];
			$join_where   = [];
			$order_by     = '';
			$limit        = '';

			$sql_clauses = [];

			$select .= 'SELECT lesson_id, COUNT( join_1.ID ) as no_of_submissions, COUNT( join_1.ID ) - COUNT( join_2.approval_status ) as no_of_not_graded, GROUP_CONCAT( join_1.course_id ) as courses, MAX( join_1.post_modified ) as last_updated';

			$select_count = 'SELECT lesson_id';

			$join_1 = "( SELECT posts.ID, posts.post_type, posts.post_status, posts.post_modified, postmeta1.meta_value as course_id FROM {$wpdb->posts} as posts JOIN {$wpdb->postmeta} as postmeta1 ON posts.ID=postmeta1.post_id WHERE postmeta1.meta_key = 'course_id' ) as join_1";

			$join_2 = "(
				SELECT join_3.ID, lesson_id, approval_status FROM
				(
					SELECT posts.ID, posts.post_modified, postmeta1.meta_value as lesson_id
					FROM {$wpdb->posts} as posts JOIN {$wpdb->postmeta} as postmeta1 ON posts.ID=postmeta1.post_id
					WHERE postmeta1.meta_key = 'lesson_id'
				) as join_3
				LEFT JOIN
				(
					SELECT posts.ID, postmeta1.meta_value as approval_status
					FROM {$wpdb->posts} as posts JOIN {$wpdb->postmeta} as postmeta1 ON posts.ID=postmeta1.post_id
					WHERE postmeta1.meta_key = 'approval_status'
				) as join_4
				ON join_3.ID = join_4.ID
			) as join_2
			ON join_1.ID = join_2.ID";

			$join_where = [
				$wpdb->prepare( 'join_1.post_type = %s', learndash_get_post_type_slug( 'assignment' ) ),
				$wpdb->prepare( 'join_1.post_status != %s', 'trash' ),
			];

			// Filter Instructor Courses.
			if ( array_key_exists( 'instructor_courses', $args ) ) {
				$instructor_courses = implode( ',', $args['instructor_courses'] );

				$join_1 = "(
					SELECT posts.ID, posts.post_type, posts.post_status, posts.post_modified, postmeta1.meta_value as course_id FROM {$wpdb->posts} as posts JOIN {$wpdb->postmeta} as postmeta1 ON posts.ID=postmeta1.post_id
					WHERE postmeta1.meta_key = 'course_id'
					AND postmeta1.meta_value IN ( {$instructor_courses} )
				) as join_1";
			}

			// Filter Specific Course.
			if ( ! empty( $args['course'] ) ) {
				$course_id = $args['course'];
				if ( ! empty( $args['search'] ) ) {
					$where[] = $wpdb->remove_placeholder_escape(
						$wpdb->prepare(
							"CONCAT(',', lesson_assignments.courses, ',') LIKE %s",
							'%,' . $wpdb->esc_like( $course_id ) . ',%'
						)
					);
				} else {
					$join_where[] = $wpdb->remove_placeholder_escape(
						$wpdb->prepare(
							"CONCAT(',', join_1.course_id, ',') LIKE %s",
							'%,' . $wpdb->esc_like( $course_id ) . ',%'
						)
					);
				}
			}

			$join_2 .= ' WHERE ' . implode( ' AND ', $join_where ) . ' GROUP BY lesson_id';

			// Filter Keyword Search.
			if ( ! empty( $args['search'] ) ) {
				$select       = "SELECT p.post_title, lesson_assignments.* FROM ( {$select} ";
				$select_count = "SELECT p.post_title FROM ( {$select_count} ";

				$join_2 .= ") as lesson_assignments JOIN {$wpdb->posts} as p ON lesson_assignments.lesson_id = p.ID";
				$where[] = $wpdb->remove_placeholder_escape(
					$wpdb->prepare(
						'p.post_title LIKE %s',
						'%' . $wpdb->esc_like( $args['search'] ) . '%'
					)
				);
			}

			// Order By.
			if ( ! empty( $args['order_by'] ) ) {
				$column_name = '';
				if ( ! empty( $args['search'] ) ) {
					$column_name = 'lesson_assignments.';
				}
				$order_by = "ORDER BY {$column_name}last_updated DESC";
				if ( 'not_graded' === $args['order_by'] ) {
					$order_by = "ORDER BY {$column_name}no_of_not_graded DESC";
				}
			}

			// Paginate.
			if ( ! empty( $args['page'] ) ) {
				$count = $args['no_of_posts'];
				$limit = "LIMIT $count";

				$page = intval( $args['page'] );
				if ( 1 < $page ) {
					$offset = $count * ( intval( $args['page'] ) - 1 );
					$limit  = "LIMIT $offset, $count";
				}
			}

			$sql_clauses['select']   = $select;
			$sql_clauses['from']     = "FROM $join_1 JOIN $join_2";
			$sql_clauses['where']    = empty( $where ) ? '' : 'WHERE ' . implode( ' AND ', $where );
			$sql_clauses['order_by'] = $order_by;
			$sql_clauses['limit']    = $limit;

			$sql_query = "
				{$sql_clauses['select']}
				{$sql_clauses['from']}
				{$sql_clauses['where']}
				{$sql_clauses['order_by']}
				{$sql_clauses['limit']}
			";

			$count_query = "
			SELECT COUNT( * ) FROM (
				{$select_count}
				{$sql_clauses['from']}
				{$sql_clauses['where']}
				) as derived_table
			";

			$total_rows = $wpdb->get_var( $count_query );
			$results    = $wpdb->get_results( $sql_query );

			return [
				'posts' => $results,
				'total' => $total_rows,
			];
		}

		/**
		 * Get assignment data permissions check
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_assignment_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get assignment
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_assignment( $request ) {
			$data = [];

			$post = get_post( $request['id'] );

			// Check if valid WP_Post object.
			if ( empty( $post ) || ! $post instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			$current_user_id = get_current_user_id();

			// If instructor user, check for access.
			if ( wdm_is_instructor( $current_user_id ) ) {
				$instructor_courses = ir_get_instructor_complete_course_list( $current_user_id );
				$course_id          = intval( get_post_meta( $post->ID, 'course_id', true ) );

				if ( empty( $course_id ) || ! in_array( $course_id, $instructor_courses ) ) {
					return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
				}
			}

			$lesson = get_post( get_post_meta( $post->ID, 'lesson_id', true ) );
			// Check if points enabled.
			$points_enabled = learndash_get_setting( $lesson->ID, 'lesson_assignment_points_enabled' );
			$lesson_points  = '';
			if ( 'on' === $points_enabled ) {
				$lesson_points = learndash_get_setting( $lesson, 'lesson_assignment_points_amount' );
			}

			$author_details = get_userdata( $post->post_author );

			$is_image      = false;
			$download_file = get_post_meta( $post->ID, 'file_link', 1 );
			$check         = wp_check_filetype( $download_file );

			// It was added in LearnDash 4.10.3 only.
			// If it exists, we should use it instead of the direct link.
			if ( function_exists( 'learndash_assignment_get_download_url' ) ) {
				$download_file = learndash_assignment_get_download_url( $post->ID );
			}

			// Check if download file is of type image.
			if ( ! empty( $check['ext'] ) ) {
				$ext        = $check['ext'];
				$image_exts = [ 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'webp' ];
				if ( in_array( $ext, $image_exts, true ) ) {
					$is_image = true;
				}
			}
			$course_id = get_post_meta( $post->ID, 'course_id', 1 );

			$data = [
				'id'              => $post->ID,
				'title'           => html_entity_decode( $post->post_title ),
				'author_url'      => get_avatar_url( $post->post_author ),
				'author_name'     => $author_details->display_name,
				'author_email'    => $author_details->user_email,
				'date'            => $post->post_date,
				'status'          => $post->post_status,
				'view_url'        => get_the_permalink( $post ),
				'slug'            => $post->post_name,
				'featured_image'  => wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) ),
				'scored_points'   => get_post_meta( $post->ID, 'points', 1 ),
				'total_points'    => $lesson_points,
				'points_enabled'  => $points_enabled,
				'file_name'       => get_post_meta( $post->ID, 'file_name', 1 ),
				'download_link'   => $download_file,
				'is_image'        => $is_image,
				'approval_status' => get_post_meta( $post->ID, 'approval_status', 1 ),
				'comments_count'  => $post->comment_count,
				'course'          => get_the_title( $course_id ),
				'lesson'          => ( learndash_get_post_type_slug( 'lesson' ) === $lesson->post_type ) ? $lesson->post_title : get_the_title( learndash_get_lesson_id( $lesson->ID, $course_id ) ),
				'type'            => ( learndash_get_post_type_slug( 'topic' ) === $lesson->post_type ) ? 'topic' : 'lesson',
				'comment_status'  => $post->comment_status,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Trash assignments
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function trash_assignments( $request ) {
			$data             = [];
			$user_id          = get_current_user_id();
			$query_parameters = $request->get_params();
			$delete           = false;

			// Get the assignment(s) to be trashed.
			$trash_ids = [];
			if ( isset( $query_parameters['assignments'] ) ) {
				$trash_ids = explode( ',', $query_parameters['assignments'] );
			}

			// Check whether to trash or permanently delete.
			if ( isset( $query_parameters['action'] ) && 'delete' === $query_parameters['action'] ) {
				$delete = true;
			}

			// If instructor, get instructor courses.
			$instructor_courses = [];
			if ( wdm_is_instructor( $user_id ) ) {
				$instructor_courses = ir_get_instructor_complete_course_list( $user_id );
			}

			foreach ( $trash_ids as $assignment_id ) {
				$assignment = get_post( $assignment_id );

				// Check if valid assignment.
				if ( empty( $assignment ) || ! $assignment instanceof WP_Post || learndash_get_post_type_slug( 'assignment' ) !== $assignment->post_type ) {
					continue;
				}

				// Verify if user has access to assignment or is admin.
				$has_access = true;
				if ( ! current_user_can( 'manage_options' ) ) {
					$parent_course = get_post_meta( $assignment_id, 'course_id', true );
					if ( ! in_array( $parent_course, $instructor_courses ) ) {
						$has_access = false;
					}
				}

				if ( $has_access ) {
					if ( ! $delete ) {
						$trashed_assignment = wp_trash_post( $assignment_id );
						$data['trashed'][]  = $trashed_assignment;
					} else {
						$deleted_assignment = wp_delete_post( $assignment_id, $delete );
						$data['deleted'][]  = $deleted_assignment;
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
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function trash_assignments_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Restore trashed assignments.
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request  WP_REST_Request instance.
		 */
		public function restore_assignments( $request ) {
			$data    = [];
			$user_id = get_current_user_id();

			$parameters = $request->get_body_params();

			// If empty get all params.
			if ( empty( $parameters ) ) {
				$parameters = $request->get_params();
			}

			// Get the assignment(s) to be restored.
			$restore_ids = [];

			if ( isset( $parameters['assignments'] ) ) {
				$restore_ids = explode( ',', $parameters['assignments'] );
			}

			// If instructor, get instructor courses.
			$instructor_courses = [];
			if ( wdm_is_instructor( $user_id ) ) {
				$instructor_courses = ir_get_instructor_complete_course_list( $user_id );
			}

			foreach ( $restore_ids as $assignment_id ) {
				$assignment = get_post( $assignment_id );

				// Check if valid trashed assignment.
				if ( empty( $assignment ) || ! $assignment instanceof WP_Post || learndash_get_post_type_slug( 'assignment' ) !== $assignment->post_type || 'trash' !== $assignment->post_status ) {
					continue;
				}

				// Verify if user has access to assignment or is admin.
				$has_access = true;
				if ( ! current_user_can( 'manage_options' ) ) {
					$parent_course = get_post_meta( $assignment_id, 'course_id', true );
					if ( ! in_array( $parent_course, $instructor_courses ) ) {
						$has_access = false;
					}
				}

				// Verify if user is assignment author or admin.
				if ( $has_access ) {
					// Restore assignment.
					$restored_assignment = wp_untrash_post( $assignment_id );
					if ( ! empty( $restored_assignment ) ) {
						$data['restored'][] = $restored_assignment;
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
		 * Restore assignments permissions check
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function restore_assignments_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get students.
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request  WP_REST_Request instance.
		 */
		public function get_students( $request ) {
			$data = [];

			// Extract query parameters.
			$params = shortcode_atts(
				[
					'search' => '',
					'roles'  => '',
					'fields' => '',
					'course' => '',
					'group'  => '',
					'number' => -1,
				],
				$request->get_params()
			);

			// Default query parameters.
			$atts = [
				'search'  => $params['search'],
				'orderby' => 'id',
				'number'  => $params['number'],
			];

			// Filter student user role.
			if ( array_key_exists( 'roles', $params ) && ! empty( $params['roles'] ) ) {
				$atts['role__in'] = explode( ',', $params['roles'] );
			}

			$user_id = get_current_user_id();

			if ( array_key_exists( 'course', $params ) && ! empty( $params['course'] ) ) {
				$course = get_post( $params['course'] );

				if ( empty( $course ) || ! $course instanceof WP_Post ) {
					return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
				}

				$group_users = [];

				if ( array_key_exists( 'group', $params ) && ! empty( $params['group'] ) ) {
					$group = get_post( $params['group'] );

					if ( empty( $group ) || ! $group instanceof WP_Post ) {
						return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
					}

					if ( wdm_is_instructor() ) {
						$group_leaders = learndash_get_groups_administrator_ids( $group->ID );
						if ( empty( $group_leaders ) || ! in_array( $user_id, $group_leaders ) ) {
							return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Sorry but you are not an administrator of the selected group.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
						}
					}

					$groups = learndash_get_course_groups( $course->ID );
					if ( empty( $groups ) || ! in_array( $group->ID, $groups ) ) {
						return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Sorry but this course is not part of the selected group.', 'wdm_instructor_role' ), [ 'status' => 400 ] );
					}

					$group_users = learndash_get_groups_user_ids( $group->ID );

					if ( empty( $group_users ) ) {
						$group_users = [ 0 ];
					}

					$atts['include'] = $group_users;
				}

				// Filter students if instructor.
				if ( wdm_is_instructor() ) {
					$unique_students_list = [];

					// Refresh shared courses.
					ir_refresh_shared_course_details( $user_id );

					// Final instructor course list.
					$course_list = ir_get_instructor_complete_course_list( $user_id );

					if ( empty( $course_list ) || ! in_array( $course->ID, $course_list ) ) {
						return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Sorry but you do not have access to this course.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
					}

					$all_students = ir_get_users_with_course_access( $course->ID, [ 'direct', 'group' ] );

					$unique_students_list = array_unique( $all_students );

					if ( ! empty( $group_users ) ) {
						$unique_students_list = array_intersect( $group_users, $unique_students_list );
					}

					if ( empty( $unique_students_list ) ) {
						$unique_students_list = [ 0 ];
					}

					$atts['include'] = $unique_students_list;
				} else {
					$users = learndash_get_users_for_course( $course->ID );
					if ( ! empty( $users ) && ! is_array( $users ) ) {
						$users = $users->get_results();
					}
					if ( ! empty( $group_users ) ) {
						$users = array_intersect( $group_users, $users );
					}

					if ( empty( $users ) ) {
						$users = [ 0 ];
					}
					$atts['include'] = $users;
				}
			} elseif ( array_key_exists( 'group', $params ) && ! empty( $params['group'] ) ) {
				$group = get_post( $params['group'] );

				if ( empty( $group ) || ! $group instanceof WP_Post ) {
					return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 404 ] );
				}

				if ( wdm_is_instructor() ) {
					$group_leader_access_to = learndash_get_administrators_group_ids( $user_id );
					if ( $user_id !== (int) $group->post_author && ( empty( $group_leader_access_to ) || ! in_array( $group->ID, $group_leader_access_to ) ) ) {
						return new WP_Error( 'ir_rest_post_invalid_id', esc_html__( 'Sorry but you are not an administrator of the selected group.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
					}
				}

				$group_users = learndash_get_groups_user_ids( $group->ID );

				if ( empty( $group_users ) ) {
					$group_users = [ 0 ];
				}

				$atts['include'] = $group_users;
			} else {
				// Filter students if instructor.
				if ( wdm_is_instructor() ) {
					// Get course list.
					$course_list          = ir_get_instructor_complete_course_list();
					$unique_students_list = [];
					$user_id              = get_current_user_id();

					// Refresh shared courses.
					ir_refresh_shared_course_details( $user_id );

					// Final instructor course list.
					$course_list = ir_get_instructor_complete_course_list( $user_id );

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
					$atts['include'] = $unique_students_list;
				}
			}

			// Search students.
			if ( isset( $params['search'] ) && ! empty( $params['search'] ) ) {
				$atts['search'] = '*' . trim( $params['search'] ) . '*';
			}

			// Get admin and instructor users.
			$query = new \WP_User_Query( $atts );

			foreach ( $query->results as $user ) {
				if ( array_key_exists( 'fields', $params ) && 'all' === $params['fields'] ) {
					array_push(
						$data,
						[
							'id'       => $user->ID,
							'name'     => $user->display_name,
							'image'    => get_avatar_url( $user->ID ),
							'email'    => $user->user_email,
							'username' => $user->user_login,
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
		 * Get students permissions check
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_students_permissions_check( $request ) {
			return $this->group_leader_request_permission_check( $request );
		}

		/**
		 * Get parent courses information in a formatted way.
		 *
		 * @since 5.2.0
		 *
		 * @param array $course_list    List of courses.
		 * @return array                Formatted courses information.
		 */
		public function get_formatted_courses_info( $course_list ) {
			$courses_info = [
				'pretty' => '',
				'full'   => '',
			];

			$count                  = count( $course_list );
			$courses_info['pretty'] = get_the_title( $course_list[0] );

			if ( strlen( $courses_info['pretty'] ) > 50 ) {
				$courses_info['pretty'] = mb_strimwidth( $courses_info['pretty'], 0, 50, '...' );
			}

			if ( $count > 1 ) {
				$courses_info['pretty'] .= sprintf(
					/* translators: Count of additional terms. */
					__( ' + %d more', 'wdm_instructor_role' ),
					$count - 1
				);
			}

			$courses_info['full'] = array_map( 'get_the_title', $course_list );

			return $courses_info;
		}

		/**
		 * Get assignment data permissions check
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_assignment_comments_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get assignment comments.
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_assignment_comments( $request ) {
			$data = [];

			$post = get_post( $request['id'] );

			// Check if valid WP_Post object.
			if ( empty( $post ) || ! $post instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			$current_user_id = get_current_user_id();

			// If instructor user, check for access.
			if ( wdm_is_instructor( $current_user_id ) ) {
				$instructor_courses = ir_get_instructor_complete_course_list( $current_user_id );
				$course_id          = intval( get_post_meta( $post->ID, 'course_id', true ) );

				if ( empty( $course_id ) || ! in_array( $course_id, $instructor_courses ) ) {
					return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
				}
			}

			// Get assignment comments.
			$comments = get_comments(
				[
					'post_id' => $post->ID,
				]
			);

			foreach ( $comments as $comment ) {
				$data[] = [
					'ID'         => $comment->comment_ID,
					'author'     => $comment->comment_author,
					'email'      => $comment->comment_author_email,
					'date'       => $comment->comment_date,
					'author_url' => get_avatar_url( $comment->user_id ),
					'content'    => $comment->comment_content,
					'parent'     => $comment->comment_parent,
					'approved'   => $comment->comment_approved,
				];
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}
	}
}
