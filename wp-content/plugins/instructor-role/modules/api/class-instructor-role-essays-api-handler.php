<?php
/**
 * Essays Rest API Handler Module
 *
 * @since 5.0.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

namespace InstructorRole\Modules\Api;

use WP_Comment_Query;
use WP_REST_Request;
use WP_Rest_Server;
use WP_Error;
use WP_REST_Posts_Controller;
use WP_Post, WP_Query;
use WP_User;
use WP_User_Query;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Essays_Api_Handler' ) ) {
	/**
	 * Class Instructor_Role_Essays_Api_Handler
	 */
	class Instructor_Role_Essays_Api_Handler extends Instructor_Role_Dashboard_Block_Api_Handler {
		/**
		 * Singleton instance of this class.
		 *
		 * @var object $instance
		 *
		 * @since 5.0.1
		 */
		protected static $instance = null;

		/**
		 * Get a singleton instance of this class.
		 *
		 * @return object
		 * @since 5.0.1
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Register custom endpoints.
		 *
		 * @since 5.0.1
		 */
		public function register_custom_endpoints() {
			// Get Instructor Essays.
			register_rest_route(
				$this->namespace,
				'/essays/questions',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_instructor_essays' ],
						'permission_callback' => [ $this, 'get_instructor_essays_permissions_check' ],
						'args'                => [
							'search' => [
								'type'    => 'string',
								'default' => '',
							],
							'page'   => [
								'type'    => 'integer',
								'default' => 1,
							],
							'sort'   => [
								'type' => 'string',
							],
						],
					],
				]
			);

			// Get Instructor Essay submissions.
			register_rest_route(
				$this->namespace,
				'/essays/questions/(?P<id>[\d]+)',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_instructor_essays_submissions' ],
						'permission_callback' => [ $this, 'get_instructor_essays_submissions_permissions_check' ],
						'args'                => [
							'search'      => [
								'type'    => 'string',
								'default' => '',
							],
							'month'       => [
								'type' => 'integer',
							],
							'question_id' => [
								'type' => 'integer',
							],
							'page'        => [
								'type'    => 'integer',
								'default' => 1,
							],
							'sort'        => [
								'type' => 'string',
							],
						],
					],
				]
			);

			// Get Instructor Essay submissions filter.
			register_rest_route(
				$this->namespace,
				'/essays/questions/(?P<id>[\d]+)/filters',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_instructor_essays_submissions_filters_data' ],
						'permission_callback' => [ $this, 'get_instructor_essays_submissions_filters_data_permissions_check' ],
					],
				]
			);

			// Get essay filters.
			register_rest_route(
				$this->namespace,
				'essays/filters/',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_essays_filters_data' ],
						'permission_callback' => [ $this, 'get_essays_filters_data_permissions_check' ],
					],
				]
			);

			// Get quiz filtered lessons and topics.
			register_rest_route(
				$this->namespace,
				'essays/filters/quiz',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_essays_filtered_quiz_data' ],
						'permission_callback' => [ $this, 'get_essays_filtered_quiz_data_permissions_check' ],
					],
				]
			);

			// Essays bulk trash endpoint.
			register_rest_route(
				$this->namespace,
				'essays/bulk_trash/',
				[
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'get_instructor_essays_submissions_bulk_trash' ],
						'permission_callback' => [ $this, 'get_instructor_essays_submissions_bulk_trash_permissions_check' ],
						'args'                => [
							'essay_ids' => [
								'type' => 'string',
							],
						],
					],
				]
			);

			// Get Instructor Essay submissions.
			register_rest_route(
				$this->namespace,
				'/essays/(?P<id>[\d]+)',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_instructor_essays_single_submissions' ],
						'permission_callback' => [ $this, 'get_instructor_essays_single_submissions_permissions_check' ],
						'args'                => [
							'question_id' => [
								'type' => 'integer',
							],
							'essay_id'    => [
								'type' => 'integer',
							],
						],
					],
				]
			);

			// Essays Comments Endpoint.
			register_rest_route(
				$this->namespace,
				'/essays/comments/(?P<id>[\d]+)',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_essays_comments' ],
						'permission_callback' => [ $this, 'get_essays_comments_permissions_check' ],
					],
				]
			);      }

		/**
		 *  Get Instructor essays.
		 *
		 *  @since 5.2.0
		 *
		 *  @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructor_essays( $request ) {
			$search            = $request->get_param( 'search' );
			$sort              = $request->get_param( 'sort' );
			$page              = $request->get_param( 'page' );
			$course_id         = $request->get_param( 'course' );
			$quiz              = $request->get_param( 'quiz' );
			$quiz_array        = [ $quiz ];
			$user_id           = get_current_user_id();
			$per_page          = 7;
			$offset            = ( $page - 1 ) * $per_page;
			$search_conditions = '';
			$sort_essays       = '';
			$shared_course_ids = $this->get_shared_course_quiz_contents();
			global $wpdb;
			$course_condition = '';

			// Check if $course_id is not empty.
			if ( ! empty( $course_id ) ) {
				$course_condition = $wpdb->prepare(
					" AND CONCAT(',', essay_details.courses, ',') LIKE %s",
					'%' . $wpdb->esc_like( ',' . $course_id . ',' ) . '%'
				);
			}

			$owned_topic_lesson_quiz = get_posts(
				[
					'post_type'   => [ 'sfwd-quiz' ],
					'author'      => $user_id,
					'fields'      => 'ids',
					'numberposts' => -1,
				]
			);

			if ( current_user_can( 'manage_options' ) ) {
				$owned_topic_lesson_quiz = get_posts(
					[
						'post_type'   => [ 'sfwd-quiz' ],
						'fields'      => 'ids',
						'numberposts' => -1,
					]
				);
			}

			// Combine the IDs from all three arrays into a single array.
			$all_ids = array_merge( $shared_course_ids, $owned_topic_lesson_quiz );

			if ( ! empty( $quiz ) && null !== $quiz ) {
				$all_ids = $quiz_array;
			}

			if ( ! empty( $search ) ) {
				$search_conditions = $wpdb->prepare(
					" AND ( {$wpdb->prefix}posts.post_title LIKE %s )",
					'%' . $wpdb->esc_like( $search ) . '%'
				);
			}

			if ( ! empty( $sort ) ) {
				if ( 'latest_submissions' === $sort ) {
					$sort_essays = ' ORDER BY essay_details.last_updated DESC';
				} elseif ( 'not_graded' === $sort ) {
					$sort_essays = ' ORDER BY essay_details.post_status DESC ';
				} else {
					$sort_essays = '';
				}
			}

			$total_count_sql = "SELECT COUNT(*)
			FROM
			(
				SELECT join_2.meta_value AS post_id, GROUP_CONCAT(join_2.ID) AS essay_ids, COUNT(join_2.ID) AS essay_count, MAX(join_2.post_modified) AS last_updated, SUM(CASE WHEN join_2.post_status = 'not_graded' THEN 1 ELSE 0 END) AS post_status, GROUP_CONCAT(join_1.course_id) AS courses
				FROM
				(
					SELECT posts.ID, posts.post_type, posts.post_modified, postmeta1.meta_value as course_id
					FROM `{$wpdb->prefix}posts` as posts JOIN `{$wpdb->prefix}postmeta` as postmeta1 ON posts.ID=postmeta1.post_id
					WHERE postmeta1.meta_key = 'course_id'
				) as join_1
				JOIN (
					SELECT {$wpdb->prefix}posts.ID, {$wpdb->prefix}posts.post_type, {$wpdb->prefix}posts.post_modified, {$wpdb->prefix}posts.post_status, {$wpdb->prefix}postmeta.meta_value
					FROM {$wpdb->prefix}posts
					JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
					WHERE {$wpdb->prefix}postmeta.meta_key = 'question_post_id'
					AND ({$wpdb->prefix}posts.post_status = 'graded' OR {$wpdb->prefix}posts.post_status = 'not_graded')
				) AS join_2
				ON join_1.ID = join_2.ID
				WHERE join_1.post_type = 'sfwd-essays'
				GROUP BY join_2.meta_value
			) as essay_details
			JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}posts.ID = essay_details.post_id
			JOIN {$wpdb->prefix}postmeta AS quiz_meta ON {$wpdb->prefix}posts.ID = quiz_meta.post_id AND quiz_meta.meta_key = 'quiz_id'
			{$course_condition}
			AND {$wpdb->prefix}posts.ID IN (SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = 'quiz_id' AND meta_value IN (" . implode( ',', $all_ids ) . ") )
			WHERE 1=1 {$search_conditions};";

			$total_count = 0;

			if ( ! empty( $all_ids ) ) {
				$total_count = $wpdb->get_var( $wpdb->prepare( $total_count_sql ) );
			}

			$sql = "SELECT essay_details.post_id, {$wpdb->prefix}posts.post_title, essay_details.essay_ids, essay_details.essay_count, essay_details.last_updated, essay_details.post_status, essay_details.courses
			FROM
			(
				SELECT join_2.meta_value AS post_id, GROUP_CONCAT(join_2.ID) AS essay_ids, COUNT(join_2.ID) AS essay_count, MAX(join_2.post_modified) AS last_updated, SUM(CASE WHEN join_2.post_status = 'not_graded' THEN 1 ELSE 0 END) AS post_status, GROUP_CONCAT(join_1.course_id) AS courses
				FROM
				(
					SELECT posts.ID, posts.post_type, posts.post_modified, postmeta1.meta_value as course_id
					FROM `{$wpdb->prefix}posts` as posts JOIN `{$wpdb->prefix}postmeta` as postmeta1 ON posts.ID=postmeta1.post_id
					WHERE postmeta1.meta_key = 'course_id'
				) as join_1
				JOIN (
					SELECT {$wpdb->prefix}posts.ID, {$wpdb->prefix}posts.post_type, {$wpdb->prefix}posts.post_modified, {$wpdb->prefix}posts.post_status, {$wpdb->prefix}postmeta.meta_value
					FROM {$wpdb->prefix}posts
					JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
					WHERE {$wpdb->prefix}postmeta.meta_key = 'question_post_id'
					AND ({$wpdb->prefix}posts.post_status = 'graded' OR {$wpdb->prefix}posts.post_status = 'not_graded')
				) AS join_2
				ON join_1.ID = join_2.ID
				WHERE join_1.post_type = 'sfwd-essays'
				GROUP BY join_2.meta_value
			) AS essay_details
			JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}posts.ID = essay_details.post_id
			JOIN {$wpdb->prefix}postmeta AS quiz_meta ON {$wpdb->prefix}posts.ID = quiz_meta.post_id AND quiz_meta.meta_key = 'quiz_id'
			WHERE {$wpdb->prefix}posts.ID IN (SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = 'quiz_id' AND meta_value IN (" . implode( ',', $all_ids ) . "))
			{$course_condition}
			{$search_conditions}
			{$sort_essays}
			LIMIT %d OFFSET %d;";

			if ( ! empty( $all_ids ) ) {
				$essay_results = $wpdb->get_results( $wpdb->prepare( $sql, $per_page, $offset ) );
			}

			$essay_submissions = [];

			if ( ! empty( $essay_results ) ) {
				foreach ( $essay_results as $essay_value ) {
					$course_list = array_unique( explode( ',', $essay_value->courses ) );
					$courses     = $this->get_formatted_courses_info( $course_list );
					$quiz_id     = get_post_meta( $essay_value->post_id, 'quiz_id', true );

					$essay_submissions[] = [
						'quiz_title'           => get_the_title( $quiz_id ),
						'post_id'              => $essay_value->post_id,
						'courses'              => $courses['pretty'],
						'courses_full'         => $courses['full'],
						'post_title'           => $essay_value->post_title,
						'essay_ids'            => $essay_value->essay_ids,
						'total_submission'     => $essay_value->essay_count,
						'last_updated'         => $essay_value->last_updated,
						'not_graded'           => $essay_value->post_status,
						'has_multiple_parents' => ( count( $course_list ) > 1 ) ? true : false,
					];
				}
			}

			// Prepare the response.
			$data = [
				'posts'        => $essay_submissions,
				'posts_count'  => count( $essay_submissions ),
				'total_posts'  => $total_count,
				'max_page_num' => ceil( $total_count / $per_page ),
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get the lesson IDs, topic IDs, and quiz IDs for a given course.
		 */
		public function get_shared_course_quiz_contents() {
			$course_list = ir_get_instructor_complete_course_list();
			$all_ids     = [];

			foreach ( $course_list as $course_id ) {
				// Get the lesson IDs for the course.
				$lessons = learndash_course_get_children_of_step( $course_id, 0, 'sfwd-lessons', 'ids', true );
				if ( ! empty( $lessons ) ) {
					foreach ( $lessons as $lesson_id ) {
						// Get the topic IDs for each lesson.
						$topics = learndash_course_get_children_of_step( $course_id, $lesson_id, 'sfwd-topic', 'ids', true );
						if ( ! empty( $topics ) ) {
							foreach ( $topics as $topic_id ) {
								// Get the quiz IDs for each topic.
								$quizzes = learndash_course_get_children_of_step( $course_id, $topic_id, 'sfwd-quiz', 'ids', true );
								if ( ! empty( $quizzes ) ) {
									$all_ids = array_merge( $all_ids, $quizzes );
								}
							}
						}
						// Get the quiz IDs for each lesson.
						$quizzes = learndash_course_get_children_of_step( $course_id, $lesson_id, 'sfwd-quiz', 'ids', true );
						if ( ! empty( $quizzes ) ) {
							$all_ids = array_merge( $all_ids, $quizzes );
						}
					}
				}
				// Get the quiz IDs for each course.
				$quizzes = learndash_course_get_children_of_step( $course_id, 0, 'sfwd-quiz', 'ids', true );
				if ( ! empty( $quizzes ) ) {
					$all_ids = array_merge( $all_ids, $quizzes );
				}
			}

			return array_unique( $all_ids );
		}

		/**
		 * Get data for quiz filters for the frontend dashboard
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_essays_filters_data( $request ) {
			$data = [];

			$user_id = get_current_user_id();

			// Get Course Filter Args.
			$course_filter_args = [
				'post_type'      => learndash_get_post_type_slug( 'course' ),
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
				$args['post__in'] = $quiz_ids;

				$course_filter_args['post__in'] = $course_ids;
			}

			// Course Filter.
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
		 * Get essay filtered quiz data.
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_essays_filtered_quiz_data( $request ) {
			$data       = [
				[
					'value' => '',
					'label' => sprintf(
						/* translators: Course, Lesson or Topic Label */
						__( 'All %s', 'wdm_instructor_role' ),
						\LearnDash_Custom_Label::get_label( 'quiz' )
					),
				],
			];
			$course_id  = $request->get_param( 'course' );
			$course_ids = [ $course_id ];
			$user_id    = get_current_user_id();
			$step_types = [ 'quiz' ];
			$quiz_id    = learndash_course_get_children_of_step( $course_id, 0, 'sfwd-quiz', 'ids', true );

			if ( ! empty( $course_id ) ) {
				if ( wdm_is_instructor( $user_id ) ) {
					$quiz_ids = ir_get_instructor_course_steps( $step_types, $course_ids, $user_id, true );
					foreach ( $quiz_ids as $quiz_id ) {
						$data[] = [
							'value' => $quiz_id,
							'label' => get_the_title( $quiz_id ),
						];
					}
				}
			}

				// Check if user has course access.
			if ( current_user_can( 'manage_options' ) ) {
				$quiz_ids = learndash_course_get_children_of_step( $course_id, 0, 'sfwd-quiz', 'ids', true );
				foreach ( $quiz_ids as $quiz_id ) {
					$data[] = [
						'value' => $quiz_id,
						'label' => get_the_title( $quiz_id ),
					];
				}
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get essays list filters data permissions check
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_essays_filtered_quiz_data_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get Instructor essays
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructor_essays_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get essays list filters data permissions check
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_essays_filters_data_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get Instructor essays submissions.
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructor_essays_submissions( $request ) {
			$question_id       = $request['id'];
			$search            = $request->get_param( 'search' );
			$sort              = $request->get_param( 'sort' );
			$month_year        = $request->get_param( 'month' );
			$page              = $request->get_param( 'page' );
			$per_page          = 7;
			$offset            = ( $page - 1 ) * $per_page;
			$quiz_id           = get_post_meta( $question_id, 'quiz_id', true );
			$learndash_steps   = learndash_get_breadcrumbs( get_post( $quiz_id ) );
			$search_conditions = '';
			$sort_essays       = '';
			global $wpdb;

			if ( ! current_user_can( 'manage_options' ) && ! $this->check_instructor_access( $quiz_id ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			if ( 'graded' === $sort ) {
				$sort_essays = " AND {$wpdb->prefix}posts.post_status = 'graded'";
			} elseif ( 'not_graded' === $sort ) {
				$sort_essays = " AND {$wpdb->prefix}posts.post_status = 'not_graded'";
			} else {
				// Exclude other post statuses by default.
				$sort_essays = " AND {$wpdb->prefix}posts.post_status IN ('graded', 'not_graded')";
			}

			if ( ! empty( $search ) ) {
				$search_like       = '%' . $wpdb->esc_like( $search ) . '%';
				$search_conditions = $wpdb->prepare(
					" AND ( {$wpdb->prefix}users.display_name LIKE %s )",
					$search_like
				);
			}

			if ( ! empty( $month_year ) ) {
				$year  = substr( $month_year, 0, 4 );
				$month = substr( $month_year, 4, 2 );

				$start_date = gmdate( 'Y-m-d H:i:s', strtotime( "{$year}-{$month}-01 00:00:00" ) );
				$end_date   = gmdate( 'Y-m-t H:i:s', strtotime( "{$year}-{$month}-01 23:59:59" ) );

				$date_filter = $wpdb->prepare(
					" AND {$wpdb->prefix}posts.post_date >= %s AND {$wpdb->prefix}posts.post_date <= %s",
					$start_date,
					$end_date
				);
			} else {
				$date_filter = '';
			}

			$total_count_sql = "SELECT COUNT(*)
        	FROM (
            SELECT {$wpdb->prefix}postmeta.meta_value as post_id
            FROM {$wpdb->prefix}posts
            JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
			JOIN {$wpdb->prefix}users ON {$wpdb->prefix}posts.post_author = {$wpdb->prefix}users.ID
            WHERE {$wpdb->prefix}posts.post_type = 'sfwd-essays'
            AND {$wpdb->prefix}postmeta.meta_key = 'question_post_id'
            AND {$wpdb->prefix}postmeta.meta_value = %d
            {$sort_essays}
            {$date_filter}
			{$search_conditions}
        	) as essay_details";

			$total_count = $wpdb->get_var( $wpdb->prepare( $total_count_sql, $question_id ) );

			$sql = " SELECT {$wpdb->prefix}postmeta.post_id AS essay_id
			FROM {$wpdb->prefix}posts
			JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
			JOIN {$wpdb->prefix}users ON {$wpdb->prefix}posts.post_author = {$wpdb->prefix}users.ID
			WHERE {$wpdb->prefix}posts.post_type = 'sfwd-essays'
			AND {$wpdb->prefix}postmeta.meta_key = 'question_post_id'
			AND {$wpdb->prefix}postmeta.meta_value = %d
			{$sort_essays}
			{$date_filter}
			{$search_conditions}
			ORDER BY {$wpdb->prefix}posts.post_date DESC
			LIMIT %d OFFSET %d";

			$essay_submissions = $wpdb->get_results( $wpdb->prepare( $sql, $question_id, $per_page, $offset ) );

			$essay_author_data = [];

			foreach ( $essay_submissions as $submission ) {
				$author_id       = get_post_field( 'post_author', $submission->essay_id );
				$essay_details   = learndash_get_essay_details( $submission->essay_id );
				$download_link   = get_post_meta( $submission->essay_id, 'upload', true );
				$quiz_id         = get_post_meta( $submission->essay_id, 'quiz_post_id', true );
				$learndash_steps = learndash_get_breadcrumbs( get_post( $quiz_id ) );

				if ( empty( $download_link ) ) {
					$is_downloadable = false;
				} else {
					$is_downloadable = true;
				}

				// It was added in LearnDash 4.10.3 only.
				// If it exists, we should use it instead of the direct link.
				if ( function_exists( 'learndash_quiz_essay_get_download_url' ) ) {
					$download_link = learndash_quiz_essay_get_download_url( $submission->essay_id );
				}

				$author_query = new WP_User_Query(
					[
						'include'        => [ $author_id ],
						'search'         => '*' . esc_attr( $search ) . '*',
						'search_columns' => [ 'display_name' ],
						'number'         => $per_page,
						'no_found_rows'  => true,
					]
				);

				$authors = $author_query->get_results();

				if ( $authors ) {
					$author = $authors[0];
					$essay  = get_post( $submission->essay_id );

					$essay_author_data[] = [
						'essay_id'            => $submission->essay_id,
						'awarded_points'      => $essay_details['points']['awarded'],
						'total_points'        => $essay_details['points']['total'],
						'status'              => $essay_details['status'],
						'essay_quiz'          => $learndash_steps['current']['title'],
						'essay_course'        => $learndash_steps['course']['title'],
						'submission_date'     => $essay->post_date,
						'comment_count'       => $essay->comment_count,
						'author_display_name' => $author->display_name,
						'author_gravatar'     => get_avatar_url( $author->user_email ),
						'download_link'       => $download_link,
						'view_url'            => get_permalink( $submission->essay_id ),
					];
				}
			}

			// Prepare the response.
			$data = [
				'posts'           => $essay_author_data,
				'course'          => $learndash_steps['course']['title'],
				'lesson'          => $learndash_steps['lesson']['title'],
				'topic'           => $learndash_steps['topic']['title'],
				'quiz'            => $learndash_steps['current']['title'],
				'post_title'      => get_the_title( $question_id ),
				'posts_count'     => count( $essay_author_data ),
				'total_posts'     => $total_count,
				'max_page_num'    => ceil( $total_count / $per_page ),
				'is_downloadable' => $is_downloadable ?? false,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get Instructor submission list filters
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructor_essays_submissions_filters_data( $request ) {
			$data        = [];
			$question_id = $request['id'];
			$quiz_id     = get_post_meta( $question_id, 'quiz_id', true );

			if ( ! current_user_can( 'manage_options' ) && ! $this->check_instructor_access( $quiz_id ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Essay Filter Args.
			$filter_args = [
				'post_type'      => 'sfwd-essays',
				'post_status'    => [ 'graded', 'not_graded' ],
				'posts_per_page' => -1,
				'meta_query'     => [
					[
						'key'   => 'question_post_id',
						'value' => $question_id,
					],
				],
			];

			$query_results = new WP_Query( $filter_args );
			$date_filter[] = [
				'value' => '',
				'label' => __( 'All dates', 'wdm_instructor_role' ),
			];
			$date_keys     = [];

			if ( ! empty( $query_results->posts ) ) {
				foreach ( $query_results->posts as $post ) {
					$essay_date = strtotime( $post->post_date );
					$key        = gmdate( 'Ym', $essay_date );
					if ( ! in_array( $key, $date_keys ) ) {
						$date_filter[] = [
							'value' => gmdate( 'Ym', $essay_date ),
							'label' => gmdate( 'F Y', $essay_date ),
						];
						$date_keys[]   = gmdate( 'Ym', $essay_date );
					}
				}
				$data = [
					'date_filter' => $date_filter,
				];
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get Instructor submission list filters data permissions check
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructor_essays_submissions_filters_data_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get Instructor essays submissions permissions check
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructor_essays_submissions_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 *  Perform bulk trashing in instructor submission
		 *
		 *  @since 5.2.0
		 *
		 *  @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructor_essays_submissions_bulk_trash( $request ) {
			$instructor_id   = get_current_user_id();
			$essay_ids       = $request->get_param( 'essay_ids' );
			$essay_ids_array = explode( ',', $essay_ids );
			$shared_quiz_ids = $this->get_shared_course_quiz_contents();

			$owned_quiz = get_posts(
				[
					'post_type'   => [ 'sfwd-quiz' ],
					'author'      => $instructor_id,
					'fields'      => 'ids',
					'numberposts' => -1,
				]
			);

			$quiz_ids = array_merge( $shared_quiz_ids, $owned_quiz );

			// Loop through each essay ID and update its post status to "trash".
			foreach ( $essay_ids_array as $essay_id ) {
				// Get the associated quiz_post_id for the essay.
				$quiz_post_id = get_post_meta( $essay_id, 'quiz_post_id', true );

				// Check if the associated quiz_post_id is in the list of quiz IDs that the instructor has access to.
				if ( in_array( $quiz_post_id, $quiz_ids ) ) {
					$updated_post = [
						'ID'          => $essay_id,
						'post_status' => 'trash', // Change post status to "trash".
					];

					// Update the post status.
					wp_update_post( $updated_post );

					// Prepare the success response.
					$data = [
						'is_error' => false,
					];
				} else {
					// Prepare the error response.
					$data = [
						'is_error' => true,
					];
				}
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Instructor bulk trashing permissions check
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructor_essays_submissions_bulk_trash_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 *  Get single essays submissions.
		 *
		 *  @since 5.2.0
		 *
		 *  @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructor_essays_single_submissions( $request ) {
			$essay_id            = $request['id'];
			$quiz_id             = get_post_meta( $essay_id, 'quiz_post_id', true );
			$learndash_steps     = learndash_get_breadcrumbs( get_post( $quiz_id ) );
			$post_obj            = get_post( $essay_id );
			$essay_grade_details = learndash_get_essay_details( $essay_id );
			$author_id           = get_post_field( 'post_author', $essay_id );
			$author_obj          = get_user_by( 'ID', $author_id );
			$download_link       = get_post_meta( $essay_id, 'upload', true );
			$is_image            = false;
			$check               = wp_check_filetype( $download_link );

			// It was added in LearnDash 4.10.3 only.
			// If it exists, we should use it instead of the direct link.
			if ( function_exists( 'learndash_quiz_essay_get_download_url' ) ) {
				$download_link = learndash_quiz_essay_get_download_url( $essay_id );
			}

			if ( ! current_user_can( 'manage_options' ) && ! $this->check_instructor_access( $quiz_id ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			if ( 'trash' === $essay_grade_details['status'] ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but the current item is trashed.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if download file is of type image.
			if ( ! empty( $check['ext'] ) ) {
				$ext        = $check['ext'];
				$image_exts = [ 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'webp' ];
				if ( in_array( $ext, $image_exts, true ) ) {
					$is_image = true;
				}
			}

			// Prepare the response.
			$data = [
				'course'              => $learndash_steps['course']['title'],
				'lesson'              => $learndash_steps['lesson']['title'],
				'topic'               => $learndash_steps['topic']['title'],
				'quiz'                => $learndash_steps['current']['title'],
				'post'                => $post_obj->post_title,
				'permalink'           => get_permalink( $essay_id ),
				'slug'                => $post_obj->post_name,
				'content'             => $post_obj->post_content,
				'awarded_points'      => $essay_grade_details['points']['awarded'],
				'total_points'        => $essay_grade_details['points']['total'],
				'status'              => $essay_grade_details['status'],
				'submission_date'     => $post_obj->post_date,
				'author_display_name' => $author_obj->display_name,
				'author_email'        => $author_obj->user_email,
				'author_gravatar'     => get_avatar_url( $author_obj->user_email ),
				'download_link'       => $download_link,
				'is_image'            => $is_image,
				'comment_status'      => $post_obj->comment_status,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;       }

		/**
		 * Single essay permission check
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructor_essays_single_submissions_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Check if the instructor has access to essay or not
		 *
		 * @since 5.2.0
		 *
		 * @param string $quiz_id  Id of quiz to check .
		 */
		public function check_instructor_access( $quiz_id ) {
			$shared_quiz_ids = $this->get_shared_course_quiz_contents();
			$instructor_id   = get_current_user_id();
			$owned_quiz      = get_posts(
				[
					'post_type'   => [ 'sfwd-quiz' ],
					'author'      => $instructor_id,
					'fields'      => 'ids',
					'numberposts' => -1,
				]
			);

			$quiz_ids = array_merge( $shared_quiz_ids, $owned_quiz );

			return in_array( $quiz_id, $quiz_ids );
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
		 * Get essays data permissions check
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_essays_comments_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get essays comments.
		 *
		 * @since 5.2.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_essays_comments( $request ) {
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
