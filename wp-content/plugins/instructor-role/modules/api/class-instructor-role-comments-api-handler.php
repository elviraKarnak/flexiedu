<?php
/**
 * Comments Rest API Handler Module
 *
 * @since 5.0.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 *
 * cspell:ignore hasassignments // ignoring misspelled words that we can't change now.
 */

namespace InstructorRole\Modules\Api;

use WP_Comment_Query;
use WP_Rest_Server;
use WP_Error;
use WP_REST_Posts_Controller;
use WP_Post, WP_Query;
use WP_User;
use WP_User_Query;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Comments_Api_Handler' ) ) {
	/**
	 * Class Instructor_Role_Comments_Api_Handler
	 */
	class Instructor_Role_Comments_Api_Handler extends Instructor_Role_Dashboard_Block_Api_Handler {
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
			// Get Instructor Comments.
			register_rest_route(
				$this->namespace,
				'/comments',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_instructor_comments' ],
						'permission_callback' => [ $this, 'get_instructor_comments_permissions_check' ],
						'args'                => [
							'search'         => [
								'type'    => 'string',
								'default' => '',
							],
							'no_of_posts'    => [
								'type'    => 'integer',
								'default' => 5,
							],
							'page'           => [
								'type'    => 'integer',
								'default' => 1,
							],
							'my_comments'    => [
								'type' => 'string',
							],
							'comment_status' => [
								'type' => 'string',
							],
						],
					],
				]
			);

			// Get comments filters.
			register_rest_route(
				$this->namespace,
				'comments/filters/',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_comments_filters_data' ],
						'permission_callback' => [ $this, 'get_comments_filters_data_permissions_check' ],
					],
				]
			);

			// Perform bulk update.
			register_rest_route(
				$this->namespace,
				'/comments/bulk_update',
				[
					[
						'methods'             => WP_Rest_Server::EDITABLE,
						'callback'            => [ $this, 'get_instructor_comments_bulk_update' ],
						'permission_callback' => [ $this, 'get_instructor_comments_bulk_update_permissions_check' ],
						'args'                => [
							'update_action' => [
								'type' => 'string',
							],
							'comment_ids'   => [
								'type' => 'integer',
							],
						],
					],
				]
			);

			// Get Instructor Comments for that post.
			register_rest_route(
				$this->namespace,
				'/comments/(?P<id>[\d]+)',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_instructor_post_comments' ],
						'permission_callback' => [ $this, 'get_instructor_post_comments_permissions_check' ],
						'args'                => [
							'post_type' => [
								'type' => 'string',
							],
						],
					],
				]
			);

			// Get Instructor Comments for that course.
			register_rest_route(
				$this->namespace,
				'/comments/course/(?P<id>[\d]+)',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_instructor_course_comments' ],
						'permission_callback' => [ $this, 'get_instructor_course_comments_permissions_check' ],
						'args'                => [],
					],
				]
			);
		}

		/**
		 * Get Instructor Comments.
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructor_comments( $request ) {
			$comments_data   = [];
			$user_id         = get_current_user_id();
			$search          = $request->get_param( 'search' );
			$no_of_posts     = 6;
			$page            = $request->get_param( 'page' );
			$comment_status  = $request->get_param( 'comment_status' );
			$start_date_unix = $request->get_param( 'start_date' );
			$end_date_unix   = $request->get_param( 'end_date' );
			$course          = $request->get_param( 'course' );

			if ( ! empty( $start_date_unix ) && ! empty( $end_date_unix ) ) {
				$start_date = date( 'Y-m-d', $start_date_unix );
				$end_date   = date( 'Y-m-d', $end_date_unix );
				$date_query = [
					'after'     => $start_date,
					'before'    => $end_date,
					'inclusive' => true,
				];
			} else {
				$date_query = []; // Empty date_query when parameters are not provided.
			}

			switch ( $comment_status ) {
				case 'unapproved':
					$status = 'hold';
					break;
				case 'spam':
					$status = 'spam';
					break;
				case 'trash':
					$status = 'trash';
					break;
				default:
					$status = 'all';
					break;
			}

			// Combine the IDs from all two arrays into a single array.
			$all_ids2               = ir_get_instructor_course_steps();
			$instructor_all_courses = ir_get_instructor_complete_course_list();

			if ( ! empty( $instructor_all_courses ) ) {
				$all_ids2 = array_merge( $all_ids2, $instructor_all_courses );
			}

			if ( ! empty( $course ) && in_array( $course, $instructor_all_courses ) ) {
				$all_ids2          = [ $course ];
				$lesson_quiz_steps = ir_get_instructor_course_steps( null, [ $course ], $user_id, true );
				$all_ids2          = array_unique( array_merge( $all_ids2, $lesson_quiz_steps ) );
				$essay_ids         = $this->get_essays( [ $course ], $user_id );
				$assignment_ids    = $this->get_assignments( [ $course ] );
			} else {
				$owned_steps_args = [
					'post_type' => [ 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz' ],
					'author'    => $user_id,
					'orderby'   => 'post_date',
					'order'     => 'ASC',
					'fields'    => 'ids',
				];
				$owned_steps      = get_posts( $owned_steps_args );

				if ( empty( $all_ids2 ) ) {
					$all_ids2 = $owned_steps;
				} else {
					$all_ids2 = array_unique( array_merge( $all_ids2, $owned_steps ) );
				}
				$essay_ids      = $this->get_essays( $instructor_all_courses, $user_id );
				$assignment_ids = $this->get_assignments( $instructor_all_courses );
			}
			if ( ! empty( $essay_ids ) ) {
				$all_ids2 = array_merge( $all_ids2, $essay_ids );
			}
			if ( ! empty( $assignment_ids ) ) {
				$all_ids2 = array_merge( $all_ids2, $assignment_ids );
			}

			// Custom WP_Query args to retrieve comments associated with the posts.
			if ( current_user_can( 'manage_options' ) ) {
				if ( ! empty( $course ) ) {
					$all_steps       = learndash_get_course_steps( $course, [ 'sfwd-lessons', 'sfwd-topic' ] );
					$quiz_list       = learndash_course_get_steps_by_type( $course, 'sfwd-quiz' );
					$assignment_list = $this->get_assignments( [ $course ] );
					$essay_list      = $this->get_essays( [ $course ], $user_id );

					if ( ! empty( $quiz_list ) ) {
						$all_steps = array_merge( $all_steps, $quiz_list );
					}
					if ( ! empty( $essay_list ) ) {
						$all_steps = array_merge( $all_steps, $essay_list );
					}
					if ( ! empty( $assignment_list ) ) {
						$all_steps = array_merge( $all_steps, $assignment_list );
					}
					$all_steps = array_merge( $all_steps, [ $course ] );

					$args = [
						'post__in'      => $all_steps,
						'post_type'     => [ 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz', 'sfwd-essays', 'sfwd-assignment' ],
						'post_status'   => 'any',
						'orderby'       => 'comment_date_gmt',
						'search'        => $search,
						'author'        => 0,
						'status'        => $status,
						'number'        => $no_of_posts,
						'paged'         => $page,
						'no_found_rows' => false,
						'date_query'    => $date_query,
					];
				} else {
					$args = [
						'post_type'     => [ 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz', 'sfwd-essays', 'sfwd-assignment' ],
						'post_status'   => 'any',
						'orderby'       => 'comment_date_gmt',
						'search'        => $search,
						'author'        => 0,
						'status'        => $status,
						'number'        => $no_of_posts,
						'paged'         => $page,
						'no_found_rows' => false,
						'date_query'    => $date_query,
					];
				}
			} else {
				$args = [
					'post__in'      => $all_ids2,
					'post_type'     => [ 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz', 'sfwd-essays', 'sfwd-assignment' ],
					'post_status'   => 'any',
					'orderby'       => 'comment_date_gmt',
					'search'        => $search,
					'author'        => 0,
					'status'        => $status,
					'number'        => $no_of_posts,
					'paged'         => $page,
					'no_found_rows' => false,
					'date_query'    => $date_query,
				];
			}

			$query = null;

			if ( ! empty( $all_ids2 ) ) {
				$query = new WP_Comment_Query( $args );
			}

			if (
				$query
				&& $query->comments
			) {
				foreach ( $query->comments as $comment ) {
					$comment_count            = get_comment_count( $comment->comment_post_ID );
					$comment_parent           = $comment->comment_parent;
					$comment_parent_obj       = get_comment( $comment_parent );
					$comment_parent_author_id = $comment_parent_obj->user_id;
					$comments_data[]          = [
						'comment_id'               => $comment->comment_ID,
						'comment_author'           => $comment->comment_author,
						'comment_content'          => $comment->comment_content,
						'author_gravitar'          => get_avatar_url( $comment ),
						'author_id'                => $comment->user_id,
						'post_title'               => get_the_title( $comment->comment_post_ID ),
						'comment_post_id'          => $comment->comment_post_ID,
						'comment_count'            => $comment_count['total_comments'],
						'post_type'                => get_post_type( $comment->comment_post_ID ),
						'post_link'                => get_permalink( $comment->comment_post_ID ),
						'comment_date'             => $comment->comment_date,
						'comment_status'           => $comment->comment_approved,
						'comment_parent'           => $comment_parent,
						'comment_parent_author'    => get_comment_author( $comment_parent ),
						'comment_parent_author_id' => $comment_parent_author_id,
					];
				}
			}

			// Prepare the response.
			$data = [
				'posts'        => $comments_data,
				'posts_count'  => ( ! empty( $comments_data ) ) ? count( $comments_data ) : 0,
				'total_posts'  => $query ? $query->found_comments : 0,
				'max_page_num' => $query ? $query->max_num_pages : 0,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;       }

		/**
		 * Get the essay ids for a given quiz.
		 *
		 * @since 5.4.0
		 *
		 * @param array   $course_ids Array of course_ids.
		 * @param integer $instructor_id User id of the instructor.
		 */
		public function get_essays( $course_ids, $instructor_id ) {
			$essay_ids = [];

			if ( current_user_can( 'manage_options' ) ) {
				$course_values = implode( ', ', $course_ids );
				$quiz_list     = learndash_course_get_steps_by_type( $course_values, 'sfwd-quiz' );
				global $wpdb;

				$sql = " SELECT {$wpdb->prefix}postmeta.post_id AS essay_id
				FROM {$wpdb->prefix}posts
				JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
				WHERE {$wpdb->prefix}posts.post_type = 'sfwd-essays'
				AND {$wpdb->prefix}postmeta.meta_key = 'quiz_post_id'
				AND {$wpdb->prefix}postmeta.meta_value IN (%d)";

				$essay_submissions = $wpdb->get_results( $wpdb->prepare( $sql, $quiz_list ) );

				if ( ! empty( $essay_submissions ) ) {
					foreach ( $essay_submissions as $submission ) {
						$essay_ids[] = $submission->essay_id;
					}
				}

				return $essay_ids;
			} else {
				$step_types         = [ 'quiz' ];
				$instructor_quizzes = ir_get_instructor_course_steps( $step_types, $course_ids, $instructor_id, true );
				global $wpdb;

				// Convert the array of values to a comma-separated string.
				$quiz_values = implode( ', ', $instructor_quizzes );

				$sql = " SELECT {$wpdb->prefix}postmeta.post_id AS essay_id
				FROM {$wpdb->prefix}posts
				JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
				WHERE {$wpdb->prefix}posts.post_type = 'sfwd-essays'
				AND {$wpdb->prefix}postmeta.meta_key = 'quiz_post_id'
				AND {$wpdb->prefix}postmeta.meta_value IN (" . $quiz_values . ')';

				$essay_submissions = [];

				if ( ! empty( $instructor_quizzes ) ) {
					$essay_submissions = $wpdb->get_results( $wpdb->prepare( $sql ) );
				}

				foreach ( $essay_submissions as $submission ) {
					$essay_ids[] = $submission->essay_id;
				}

				return $essay_ids;
			}
		}

		/**
		 * Get the essay ids for a given quiz.
		 *
		 * @since 5.4.0
		 *
		 * @param array $quiz_ids Array of quiz_ids.
		 */
		public function get_essays_for_lesson_topic_quiz( $quiz_ids ) {
			global $wpdb;

			// Convert the array of values to a comma-separated string.
			$quiz_values = implode( ', ', $quiz_ids );

			$sql = " SELECT {$wpdb->prefix}postmeta.post_id AS essay_id
			FROM {$wpdb->prefix}posts
			JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
			WHERE {$wpdb->prefix}posts.post_type = 'sfwd-essays'
			AND {$wpdb->prefix}postmeta.meta_key = 'quiz_post_id'
			AND {$wpdb->prefix}postmeta.meta_value IN (%d)";

			if ( ! empty( $quiz_values ) ) {
				$essay_submissions = $wpdb->get_results( $wpdb->prepare( $sql, $quiz_values ) );
			}

			foreach ( $essay_submissions as $submission ) {
				$essay_ids[] = $submission->essay_id;
			}

				return $essay_ids;      }

		/**
		 * Get the assignment ids for a given quiz.
		 *
		 * @since 5.4.0
		 *
		 * @param array $course_ids Array of course_ids.
		 */
		public function get_assignments( $course_ids ) {
			global $wpdb;

			$assignment_ids = [];

			// Convert the array of values to a comma-separated string.
			$course_id = implode( ', ', $course_ids );

			$sql = " SELECT {$wpdb->prefix}postmeta.post_id AS assignment_id
			FROM {$wpdb->prefix}posts
			JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
			WHERE {$wpdb->prefix}posts.post_type = 'sfwd-assignment'
			AND {$wpdb->prefix}postmeta.meta_key = 'course_id'
			AND {$wpdb->prefix}postmeta.meta_value IN (" . $course_id . ')';

			$assignment_submissions = [];

			if ( ! empty( $course_id ) ) {
				$assignment_submissions = $wpdb->get_results( $wpdb->prepare( $sql ) );
			}

			if ( ! empty( $assignment_submissions ) ) {
				foreach ( $assignment_submissions as $submission ) {
					$assignment_ids[] = $submission->assignment_id;
				}
			}

			return $assignment_ids;
		}

		/**
		 * Get the assignment ids for a given lesson and topic.
		 *
		 * @since 5.4.0
		 *
		 * @param array $lesson_ids Array of lesson_ids.
		 */
		public function get_assignments_for_lesson_topic( $lesson_ids ) {
			global $wpdb;

			// Convert the array of values to a comma-separated string.
			$lesson_id = implode( ', ', $lesson_ids );

			$sql = " SELECT {$wpdb->prefix}postmeta.post_id AS assignment_id
			FROM {$wpdb->prefix}posts
			JOIN {$wpdb->prefix}postmeta ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id
			WHERE {$wpdb->prefix}posts.post_type = 'sfwd-assignment'
			AND {$wpdb->prefix}postmeta.meta_key = 'lesson_id'
			AND {$wpdb->prefix}postmeta.meta_value IN ( " . $lesson_id . ' )';

			if ( ! empty( $lesson_id ) ) {
				$assignment_submissions = $wpdb->get_results( $wpdb->prepare( $sql ) );
			}

			foreach ( $assignment_submissions as $submission ) {
				$assignment_ids[] = $submission->assignment_id;
			}

			return $assignment_ids;
		}

		/**
		 * Instructor Comments permission check.
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructor_comments_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Perform Instructor bulk update.
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructor_comments_bulk_update( $request ) {
			$instructor_id     = get_current_user_id();
			$comments_id       = $request->get_param( 'comments_id' );
			$comments_id_array = explode( ',', $comments_id );
			$update_action     = $request->get_param( 'update_action' );

			foreach ( $comments_id_array as $comment_id ) {
					// Get the post ID associated with the comment.
					$comment_post_id = get_comment( $comment_id )->comment_post_ID;

				if ( ! current_user_can( 'manage_options' ) ) {
					$all_ids2               = ir_get_instructor_course_steps();
					$instructor_all_courses = ir_get_instructor_complete_course_list();
					if ( ! empty( $instructor_all_courses ) ) {
						$all_ids2 = array_merge( $all_ids2, $instructor_all_courses );
					}
					if ( empty( $all_ids2 ) ) {
						$owned_steps_args = [
							'post_type' => [ 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz' ],
							'author'    => $instructor_id,
							'orderby'   => 'post_date',
							'order'     => 'ASC',
							'fields'    => 'ids',
						];
						$owned_steps      = get_posts( $owned_steps_args );
						$all_ids2         = $owned_steps;
					}

					$essay_ids      = $this->get_essays( $instructor_all_courses, $instructor_id );
					$assignment_ids = $this->get_assignments( $instructor_all_courses );

					if ( ! empty( $essay_ids ) ) {
						$all_ids2 = array_merge( $all_ids2, $essay_ids );
					}
					if ( ! empty( $assignment_ids ) ) {
						$all_ids2 = array_merge( $all_ids2, $assignment_ids );
					}
					if ( ! in_array( $comment_post_id, $all_ids2 ) ) {
						return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
					}
				}

				// Update comment status based on $update_action using switch.
				switch ( $update_action ) {
					case 'approved':
						wp_set_comment_status( $comment_id, 'approve' );
						break;

					case 'spam':
						wp_spam_comment( $comment_id );
						break;

					case 'unspam': // cspell:disable-line .
						wp_unspam_comment( $comment_id ); // cspell:disable-line .
						break;

					case 'trash':
						wp_trash_comment( $comment_id );
						break;

					case 'untrash':
						wp_untrash_comment( $comment_id );
						break;

					case 'unapproved':
						wp_set_comment_status( $comment_id, 'hold' );
						break;

					case 'delete':
						wp_delete_comment( $comment_id, true );
						break;
				}
			}

			$success_message = [
				'message' => __( 'Comments have been updated', 'wdm_instructor_role' ),
				'type'    => 'success',
			];
			wp_send_json_success( $success_message );       }

		/**
		 * Perform Instructor bulk update permission check.
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructor_comments_bulk_update_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Check if the comment is accessible or not to the instructor.
		 *
		 * @since 5.4.0
		 *
		 * @param array           $comment_data Data of comment.
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function check_instructor_comment_access( $comment_data, $request ) {
			$instructor_id          = get_current_user_id();
			$is_reply               = $request['isReply'];
			$all_steps_id           = ir_get_instructor_course_steps();
			$instructor_all_courses = ir_get_instructor_complete_course_list();

			if ( true == $is_reply ) {
				$comment_id      = $request['parent'];
				$comment_post_id = get_comment( $comment_id )->comment_post_ID;
			} else {
				$comment_id      = $request['id'];
				$comment_post_id = get_comment( $comment_id )->comment_post_ID;
			}

			if ( empty( $comment_id ) ) {
				$comment_post_id = $request['post'];
			}

			if ( empty( $all_steps_id ) ) {
				$all_steps_id = $instructor_all_courses;
			} else {
				$all_steps_id = array_merge( $all_steps_id, $instructor_all_courses );
			}

			$owned_steps_args = [
				'post_type' => [ 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz' ],
				'author'    => $instructor_id,
				'orderby'   => 'post_date',
				'order'     => 'ASC',
				'fields'    => 'ids',
			];
			$owned_steps      = get_posts( $owned_steps_args );

			if ( empty( $all_steps_id ) ) {
				$all_ids = $owned_steps;
			} else {
				// Combine the IDs from all three arrays into a single array.
				$all_ids = array_unique( array_merge( $all_steps_id, $owned_steps ) );
			}
			$essay_ids      = $this->get_essays( $instructor_all_courses, $instructor_id );
			$assignment_ids = $this->get_assignments( $instructor_all_courses );
			if ( ! empty( $essay_ids ) ) {
				$all_ids = array_merge( $all_ids, $essay_ids );
			}
			if ( ! empty( $assignment_ids ) ) {
				$all_ids = array_merge( $all_ids, $assignment_ids );
			}

			// Check if the post ID exists in the $all_ids array.
			if ( ! in_array( $comment_post_id, $all_ids ) && ! current_user_can( 'manage_options' ) ) {
				return new WP_Error(
					'comment_no_access',
					__( 'You do not have access to edit this comment.', 'wdm_instructor_role' ),
					[ 'status' => 403 ]
				);
			}

			return $comment_data;       }

		/**
		 * Get data for course filters for the frontend dashboard
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_comments_filters_data( $request ) {
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
		 * Instructor Comments filters permission check.
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_comments_filters_data_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get Instructor Comments for that post.
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructor_post_comments( $request ) {
			$post_id                = $request['id'];
			$user_id                = get_current_user_id();
			$type_of_post           = $request->get_param( 'post_type' );
			$correct_post_type      = get_post_type( $post_id );
			$all_steps_id           = ir_get_instructor_course_steps();
			$instructor_all_courses = ir_get_instructor_complete_course_list();

			switch ( $type_of_post ) {
				case 'sfwd-lessons':
					$post_type = 'sfwd-lessons';
					break;
				case 'sfwd-topic':
					$post_type = 'sfwd-topic';
					break;
				case 'sfwd-quiz':
					$post_type = 'sfwd-quiz';
					break;
				case 'sfwd-essays':
					$post_type = 'sfwd-essays';
					break;
				case 'sfwd-assignment':
					$post_type = 'sfwd-assignment';
					break;
				default:
					$post_type = 'sfwd-courses';
					break;
			}

			if ( $type_of_post !== $correct_post_type ) {
				return new WP_Error( 'ir_rest_invalid_post_type', esc_html__( 'Sorry but you have not entered correct post type', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				if ( 'sfwd-courses' == $post_type || 'sfwd-lessons' == $post_type || 'sfwd-topic' == $post_type || 'sfwd-quiz' == $post_type ) {
					if ( ! empty( $instructor_all_courses ) ) {
						$all_steps_id     = array_merge( $all_steps_id, $instructor_all_courses );
						$owned_steps_args = [
							'post_type' => [ 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz' ],
							'author'    => $user_id,
							'orderby'   => 'post_date',
							'order'     => 'ASC',
							'fields'    => 'ids',
						];
						$owned_steps      = get_posts( $owned_steps_args );
						$all_steps_id     = array_merge( $all_steps_id, $owned_steps );
					} else {
						$owned_steps_args = [
							'post_type' => [ 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz' ],
							'author'    => $user_id,
							'orderby'   => 'post_date',
							'order'     => 'ASC',
							'fields'    => 'ids',
						];
						$owned_steps      = get_posts( $owned_steps_args );
						$all_steps_id     = $owned_steps;
					}

					if ( ! in_array( $post_id, $all_steps_id ) ) {
						return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
					}
				} else {
					$essay_ids      = $this->get_essays( $instructor_all_courses, $user_id );
					$assignment_ids = $this->get_assignments( $instructor_all_courses );

					if ( ! empty( $essay_ids ) ) {
						$all_steps_id = array_merge( $all_steps_id, $essay_ids );
					}

					if ( ! empty( $assignment_ids ) ) {
						$all_steps_id = array_merge( $all_steps_id, $assignment_ids );
					}

					if ( ! in_array( $post_id, $all_steps_id ) ) {
						return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
					}
				}
			}

			if ( 'sfwd-courses' == $post_type ) {
				$course = [
					'course_id'    => $post_id,
					'course_title' => get_the_title( $post_id ),
				];

				$args = [
					'post__in'      => $post_id,
					'post_type'     => [ 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz', 'sfwd-essays', 'sfwd-assignment' ],
					'post_status'   => 'any',
					'orderby'       => 'comment_date_gmt',
					'author'        => 0,
					'no_found_rows' => false,
				];

				$query = new WP_Comment_Query( $args );

				if ( $query->comments ) {
					foreach ( $query->comments as $comment ) {
						$comment_count            = get_comment_count( $comment->comment_post_ID );
						$comment_parent           = $comment->comment_parent;
						$comment_parent_obj       = get_comment( $comment_parent );
						$comment_parent_author_id = $comment_parent_obj->user_id;
						$comments_data[]          = [
							'comment_id'               => $comment->comment_ID,
							'comment_author'           => $comment->comment_author,
							'comment_content'          => $comment->comment_content,
							'author_gravitar'          => get_avatar_url( $comment ),
							'author_id'                => $comment->user_id,
							'post_title'               => get_the_title( $comment->comment_post_ID ),
							'comment_post_id'          => $comment->comment_post_ID,
							'comment_count'            => $comment_count['total_comments'],
							'post_type'                => get_post_type( $comment->comment_post_ID ),
							'post_link'                => get_permalink( $comment->comment_post_ID ),
							'comment_date'             => $comment->comment_date,
							'comment_status'           => $comment->comment_approved,
							'comment_parent'           => $comment_parent,
							'comment_parent_author'    => get_comment_author( $comment_parent ),
							'comment_parent_author_id' => $comment_parent_author_id,
						];
					}
				}
			}

			if ( 'sfwd-lessons' == $post_type || 'sfwd-topic' == $post_type || 'sfwd-quiz' == $post_type ) {
				global $wpdb;
				$key_prefix = 'ld_course_';

				$sql = " SELECT {$wpdb->prefix}postmeta.meta_value
				FROM {$wpdb->prefix}postmeta
				WHERE {$wpdb->prefix}postmeta.post_id = %d
				AND {$wpdb->prefix}postmeta.meta_key LIKE %s
				AND {$wpdb->prefix}postmeta.meta_key REGEXP %s";

				$all_course_ids = $wpdb->get_results( $wpdb->remove_placeholder_escape( $wpdb->prepare( $sql, $post_id, $key_prefix . '%', $key_prefix . '[0-9]' ) ) );
				$course_id      = get_post_meta( $post_id, 'course_id', true );
				$not_found      = array_intersect(
					array_map(
						function ( $course_ids ) {
							return $course_ids->meta_value;
						},
						$all_course_ids
					),
					$instructor_all_courses
				);

				if ( ! current_user_can( 'manage_options' ) ) {
					if ( ! empty( $course_id ) ) {
						if ( empty( $not_found ) ) {
							return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
						}
					} elseif ( get_post( $post_id )->post_author != $user_id ) {
							return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
					}
				}

				foreach ( $all_course_ids as $course_ids ) {
					$course[] = [
						'course_id'    => $course_ids->meta_value,
						'course_title' => get_the_title( $course_ids->meta_value ),
					];
				}

				$args = [
					'post__in'      => $post_id,
					'post_type'     => [ 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz', 'sfwd-essays', 'sfwd-assignment' ],
					'post_status'   => 'any',
					'orderby'       => 'comment_date_gmt',
					'author'        => 0,
					'no_found_rows' => false,
				];

				$query = new WP_Comment_Query( $args );

				if ( $query->comments ) {
					foreach ( $query->comments as $comment ) {
						$comment_count            = get_comment_count( $comment->comment_post_ID );
						$comment_parent           = $comment->comment_parent;
						$comment_parent_obj       = get_comment( $comment_parent );
						$comment_parent_author_id = $comment_parent_obj->user_id;
						$comments_data[]          = [
							'comment_id'               => $comment->comment_ID,
							'comment_author'           => $comment->comment_author,
							'comment_content'          => $comment->comment_content,
							'author_gravitar'          => get_avatar_url( $comment ),
							'author_id'                => $comment->user_id,
							'post_title'               => get_the_title( $comment->comment_post_ID ),
							'comment_post_id'          => $comment->comment_post_ID,
							'comment_count'            => $comment_count['total_comments'],
							'course_title'             => get_the_title( $course_id ),
							'post_type'                => get_post_type( $comment->comment_post_ID ),
							'post_link'                => get_permalink( $comment->comment_post_ID ),
							'comment_date'             => $comment->comment_date,
							'comment_status'           => $comment->comment_approved,
							'comment_parent'           => $comment_parent,
							'comment_parent_author'    => get_comment_author( $comment_parent ),
							'comment_parent_author_id' => $comment_parent_author_id,
						];
					}
				}
			}

			if ( 'sfwd-essays' == $post_type || 'sfwd-assignment' == $post_type ) {
				$course[] = [
					'course_id'    => get_post_meta( $post_id, 'course_id', true ),
					'course_title' => get_the_title( get_post_meta( $post_id, 'course_id', true ) ),
				];

				$args = [
					'post__in'      => $post_id,
					'post_type'     => [ 'sfwd-essays', 'sfwd-assignment' ],
					'post_status'   => 'any',
					'orderby'       => 'comment_date_gmt',
					'author'        => 0,
					'no_found_rows' => false,
				];

				$query = new WP_Comment_Query( $args );

				if ( $query->comments ) {
					foreach ( $query->comments as $comment ) {
						$comment_count            = get_comment_count( $comment->comment_post_ID );
						$comment_parent           = $comment->comment_parent;
						$comment_parent_obj       = get_comment( $comment_parent );
						$comment_parent_author_id = $comment_parent_obj->user_id;
						$comments_data[]          = [
							'comment_id'               => $comment->comment_ID,
							'comment_author'           => $comment->comment_author,
							'comment_content'          => $comment->comment_content,
							'author_gravitar'          => get_avatar_url( $comment ),
							'author_id'                => $comment->user_id,
							'post_title'               => get_the_title( $comment->comment_post_ID ),
							'comment_post_id'          => $comment->comment_post_ID,
							'comment_count'            => $comment_count['total_comments'],
							'post_type'                => get_post_type( $comment->comment_post_ID ),
							'post_link'                => get_permalink( $comment->comment_post_ID ),
							'comment_date'             => $comment->comment_date,
							'comment_status'           => $comment->comment_approved,
							'comment_parent'           => $comment_parent,
							'comment_parent_author'    => get_comment_author( $comment_parent ),
							'comment_parent_author_id' => $comment_parent_author_id,
						];
					}
				}
			}

			// Prepare the response.
			$data = [
				'posts'  => $comments_data,
				'course' => $course,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Instructor Post Comments permission check.
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructor_post_comments_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Fetch Instructor Course Comments .
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructor_course_comments( $request ) {
			$course                 = get_post( $request['id'] );
			$course_comment_count   = get_comment_count( $request['id'] );
			$instructor_all_courses = ir_get_instructor_complete_course_list();

			if ( ! current_user_can( 'manage_options' ) ) {
				if ( ! in_array( $course->ID, $instructor_all_courses ) ) {
					return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
				}
			}

			$ld_course_steps_object = \LDLMS_Factory_Post::course_steps( intval( $course->ID ) );

			if ( $ld_course_steps_object ) {
				$steps   = $ld_course_steps_object->get_steps( 'h' );
				$all_ids = [];

				foreach ( $steps as $step_type => $step_data ) {
					if ( ! empty( $step_data ) ) {
						foreach ( $step_data as $step_id => $step_content ) {
							// Fetch the title and comment count.
							$title         = get_the_title( $step_id );
							$comment_count = get_comment_count( $step_id );
							$entry         = [
								'category'      => $step_type,
								'id'            => $step_id,
								'title'         => $title,
								'comment_count' => $comment_count['all'],
								'children'      => [],
							];

							// Check if the step is a lesson.
							if ( 'sfwd-lessons' === $step_type ) {
								// Use learndash_lesson_hasassignments to check for assignments.
								$has_assignments          = learndash_lesson_hasassignments( get_post( $step_id ) );
								$entry['has_assignments'] = $has_assignments;

								// If assignments are present, fetch and append them.
								if ( $has_assignments ) {
									$args             = [
										'post_type'   => learndash_get_post_type_slug( 'assignment' ),
										'post_status' => 'any',
										'orderby'     => 'author',
										'meta_query'  => [
											[
												'key'   => 'lesson_id',
												'value' => $step_id,
											],
										],
									];
									$assignment_query = new WP_Query( $args );

									if ( $assignment_query->have_posts() ) {
										while ( $assignment_query->have_posts() ) {
											$assignment_query->the_post();
											$assignment_id            = get_the_ID();
											$assignment_title         = get_the_title( $assignment_id );
											$assignment_comment_count = get_comment_count( $assignment_id );
											$entry['children'][]      = [
												'category' => 'sfwd-assignment',
												'id'       => $assignment_id,
												'title'    => $assignment_title,
												'comment_count' => $assignment_comment_count['all'],
											];
										}
										wp_reset_postdata();
									}
								}
							}

							// Check if the step is a topic.
							if ( 'sfwd-topic' === $step_type ) {
								// Use learndash_topic_hasassignments to check for assignments.
								$has_assignments          = learndash_lesson_hasassignments( get_post( $step_id ) );
								$entry['has_assignments'] = $has_assignments;

								// If assignments are present, fetch and append them.
								if ( $has_assignments ) {
									$args             = [
										'post_type'   => learndash_get_post_type_slug( 'assignment' ),
										'post_status' => 'any',
										'orderby'     => 'author',
										'meta_query'  => [
											[
												'key'   => 'lesson_id',
												'value' => $step_id,
											],
										],
									];
									$assignment_query = new WP_Query( $args );

									if ( $assignment_query->have_posts() ) {
										while ( $assignment_query->have_posts() ) {
											$assignment_query->the_post();
											$assignment_id            = get_the_ID();
											$assignment_title         = get_the_title( $assignment_id );
											$assignment_comment_count = get_comment_count( $assignment_id );
											$entry['children'][]      = [
												'category' => 'sfwd-assignment',
												'id'       => $assignment_id,
												'title'    => $assignment_title,
												'comment_count' => $assignment_comment_count['all'],
											];
										}
										wp_reset_postdata();
									}
								}
							}

							// Check if the step is a quiz.
							if ( 'sfwd-quiz' === $step_type ) {
								// Add your code to fetch essays here.
								$quiz_post_id = $step_id; // Get the quiz post ID.
								$args         = [
									'post_type'  => learndash_get_post_type_slug( 'essay' ),
									'meta_query' => [
										[
											'key'   => 'quiz_post_id',
											'value' => $quiz_post_id,
										],
									],
								];
								$essay_query  = new WP_Query( $args );

								if ( $essay_query->have_posts() ) {
									while ( $essay_query->have_posts() ) {
										$essay_query->the_post();
										$essay_id            = get_the_ID();
										$essay_title         = get_the_title( $essay_id );
										$essay_comment_count = get_comment_count( $essay_id );
										$entry['children'][] = [
											'category' => 'sfwd-essays',
											'id'       => $essay_id,
											'title'    => $essay_title,
											'comment_count' => $essay_comment_count['all'],
										];
									}
									wp_reset_postdata();
								}
							}

							// Check for nested levels.
							foreach ( $step_content as $sub_step_type => $sub_step_data ) {
								if ( ! empty( $sub_step_data ) ) {
									foreach ( $sub_step_data as $sub_step_id => $sub_step_content ) {
										$comment_count = get_comment_count( $sub_step_id );
										$sub_entry     = [
											'category' => $sub_step_type,
											'id'       => $sub_step_id,
											'title'    => get_the_title( $sub_step_id ),
											'comment_count' => $comment_count['all'],
											'children' => [],
										];

										// Check if the step is a lesson.
										if ( 'sfwd-lessons' === $sub_step_type ) {
											// Use learndash_lesson_hasassignments to check for assignments.
											$has_assignments              = learndash_lesson_hasassignments( get_post( $sub_step_id ) );
											$sub_entry['has_assignments'] = $has_assignments;

											// If assignments are present, fetch and append them.
											if ( $has_assignments ) {
												$args             = [
													'post_type'      => learndash_get_post_type_slug( 'assignment' ),
													'post_status'    => 'any',
													'orderby'        => 'author',
													'meta_query'     => [
														[
															'key'   => 'lesson_id',
															'value' => $sub_step_id,
														],
													],
												];
												$assignment_query = new WP_Query( $args );

												if ( $assignment_query->have_posts() ) {
													while ( $assignment_query->have_posts() ) {
														$assignment_query->the_post();
														$assignment_id            = get_the_ID();
														$assignment_title         = get_the_title( $assignment_id );
														$assignment_comment_count = get_comment_count( $assignment_id );
														$sub_entry['children'][]  = [
															'category' => 'sfwd-assignment',
															'id'       => $assignment_id,
															'title'    => $assignment_title,
															'comment_count' => $assignment_comment_count['all'],
														];
													}
													wp_reset_postdata();
												}
											}
										}

										// Check if the step is a topic.
										if ( 'sfwd-topic' === $sub_step_type ) {
											// Use learndash_topic_hasassignments to check for assignments.
											$has_assignments              = learndash_lesson_hasassignments( get_post( $sub_step_id ) );
											$sub_entry['has_assignments'] = $has_assignments;

											// If assignments are present, fetch and append them.
											if ( $has_assignments ) {
												$args             = [
													'post_type'      => learndash_get_post_type_slug( 'assignment' ),
													'post_status'    => 'any',
													'orderby'        => 'author',
													'meta_query'     => [
														[
															'key'   => 'lesson_id',
															'value' => $sub_step_id,
														],
													],
												];
												$assignment_query = new WP_Query( $args );

												if ( $assignment_query->have_posts() ) {
													while ( $assignment_query->have_posts() ) {
														$assignment_query->the_post();
														$assignment_id            = get_the_ID();
														$assignment_title         = get_the_title( $assignment_id );
														$assignment_comment_count = get_comment_count( $assignment_id );
														$sub_entry['children'][]  = [
															'category' => 'sfwd-assignment',
															'id'       => $assignment_id,
															'title'    => $assignment_title,
															'comment_count' => $assignment_comment_count['all'],
														];
													}
													wp_reset_postdata();
												}
											}
										}

										// Check if the step is a quiz.
										if ( 'sfwd-quiz' === $sub_step_type ) {
											// Add your code to fetch essays here.
											$quiz_post_id = $sub_step_id; // Get the quiz post ID.
											$args         = [
												'post_type'  => learndash_get_post_type_slug( 'essay' ),
												'meta_query' => [
													[
														'key'   => 'quiz_post_id',
														'value' => $quiz_post_id,
													],
												],
											];
											$essay_query  = new WP_Query( $args );

											if ( $essay_query->have_posts() ) {
												while ( $essay_query->have_posts() ) {
													$essay_query->the_post();
													$essay_id                = get_the_ID();
													$essay_title             = get_the_title( $essay_id );
													$essay_comment_count     = get_comment_count( $essay_id );
													$sub_entry['children'][] = [
														'category' => 'sfwd-essays',
														'id'       => $essay_id,
														'title'    => $essay_title,
														'comment_count' => $essay_comment_count['all'],
													];
												}
												wp_reset_postdata();
											}
										}

										// Check for deeper nesting, if necessary.
										foreach ( $sub_step_content as $sub_sub_step_type => $sub_sub_step_data ) {
											if ( ! empty( $sub_sub_step_data ) ) {
												foreach ( $sub_sub_step_data as $sub_sub_step_id => $sub_sub_step_content ) {
													$comment_count           = get_comment_count( $sub_sub_step_id );
													$sub_sub_entry           = [
														'category' => $sub_sub_step_type,
														'id'       => $sub_sub_step_id,
														'title'    => get_the_title( $sub_sub_step_id ),
														'comment_count' => $comment_count['all'],
													];
													$sub_entry['children'][] = $sub_sub_entry;
												}
											}
										}
										$entry['children'][] = $sub_entry;
									}
								}
							}
							$all_ids[] = $entry;
						}
					}
				}
			}

			// Prepare the response.
			$data = [
				'all_ids'              => $all_ids,
				'course_comment_count' => $course_comment_count['total_comments'],
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Instructor Course Comments permission check.
		 *
		 * @since 5.4.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_instructor_course_comments_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}
	}
}
