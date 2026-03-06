<?php
/**
 * Comments Module
 *
 * @since 3.5.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

namespace InstructorRole\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Comments' ) ) {
	/**
	 * Class Instructor Role Comments Module
	 */
	class Instructor_Role_Comments {
		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
		 *
		 * @since 3.3.0
		 */
		protected static $instance = null;

		/**
		 * Plugin Slug
		 *
		 * @var string  $plugin_slug
		 *
		 * @since 3.3.0
		 */
		protected $plugin_slug = '';

		public function __construct() {
			$this->plugin_slug = INSTRUCTOR_ROLE_TXT_DOMAIN;
		}

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 3.5.0
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Enable access to comments screen
		 *
		 * @since 3.3.0
		 */
		public function enable_comments_screen_access( $allowed_tabs ) {
			$allowed_tabs[] = 'edit-comments.php';
			return $allowed_tabs;
		}

		/**
		 * Filter the comments list to include only current instructor comments
		 *
		 * @param array $query_clauses
		 * @param obj   $comment_query
		 *
		 * @return array
		 *
		 * @since 3.3.0
		 */
		public function filter_instructor_comments( $query_clauses, $comment_query ) {
			if ( ! is_admin() ) {
				return $query_clauses;
			}

			if ( ! wdm_is_instructor() ) {
				return $query_clauses;
			}

			// Complete instructor course list.
			$course_list = ir_get_instructor_complete_course_list();

			// Get all topics,lessons and quizzes which is owned by the instructor.
			$user_id                 = get_current_user_id();
			$owned_topic_lesson_quiz = get_posts(
				[
					'post_type'   => [ 'sfwd-topic', 'sfwd-lessons', 'sfwd-quiz' ],
					'author'      => $user_id,
					'fields'      => 'ids',
					'numberposts' => -1,
				]
			);

			$all_course_content_list = $this->get_all_course_contents( $course_list );

			if ( ! empty( $all_course_content_list ) && is_array( $all_course_content_list ) ) {
				if ( ! empty( $owned_topic_lesson_quiz ) ) {
					$all_course_content_list = array_unique( array_merge( $all_course_content_list, $owned_topic_lesson_quiz ), SORT_REGULAR );
				}

				$query_clauses['where'] .= sprintf( ' AND comment_post_ID IN ( %s )', implode( ',', $all_course_content_list ) );
			}

			return $query_clauses;
		}

		/**
		 * Allow access to comments to instructors
		 *
		 * @param array $allowed_data   List of allowed data types to instructors.
		 * @return array                Updated list of allowed data types.
		 *
		 * @since 3.3.0
		 */
		public function allow_comments_access( $allowed_data ) {
			if ( ! wdm_is_instructor() ) {
				return $allowed_data;
			}

			$allowed_data[] = 'comments';
			return $allowed_data;
		}

		/**
		 * Get list of all lesson, topic and quiz ids for the given course ids.
		 *
		 * @param array $course_ids           List of all course ids.
		 *
		 * @return array $course_content_ids   On success returns list of all lesson, topic
		 *                                      and quiz ids for the given courses else false.
		 * @since 3.3.0
		 */
		public function get_all_course_contents( $course_ids ) {
			if ( empty( $course_ids ) ) {
				return false;
			}

			$content_list = [];
			foreach ( $course_ids as $course_id ) {
				array_push( $content_list, $course_id );

				// Get lessons in this course.
				$lessons = learndash_get_course_lessons_list( $course_id, null, [ 'num' => 0 ] );

				$lesson_id = 0;
				if ( is_array( $lessons ) && ! empty( $lessons ) ) {
					foreach ( $lessons as $lesson ) {
						$lesson_id = $lesson['post']->ID;
						array_push( $content_list, $lesson_id );

						// Get topics in the lessons.
						$topics = learndash_topic_dots( $lesson_id, false, 'array', null, $course_id );
						if ( is_array( $topics ) && ! empty( $topics ) ) {
							$topic_id = 0;
							foreach ( $topics as $topic ) {
								$topic_id = $topic->ID;
								array_push( $content_list, $topic_id );

								// Get quizzes in the topics.
								$topic_quizzes = learndash_get_lesson_quiz_list( $topic_id, null, $course_id );

								foreach ( $topic_quizzes as $topic_quiz ) {
									array_push( $content_list, $topic_quiz['post']->ID );
								}
							}
						}

						// Get quizzes in the lessons.
						$lesson_quizzes = learndash_get_lesson_quiz_list( $lesson_id, null, $course_id );
						foreach ( $lesson_quizzes as $lesson_quiz ) {
							array_push( $content_list, $lesson_quiz['post']->ID );
						}
					}
				}

				// Get quizzes in the course.
				$course_quizzes = learndash_get_course_quiz_list( $course_id );

				foreach ( $course_quizzes as $course_quiz ) {
					array_push( $content_list, $course_quiz['post']->ID );
				}
			}

			// Get course assignments and essays.
			$course_assignments = $this->get_course_submissions( $course_ids );

			$content_list = array_merge( $content_list, $course_assignments );

			return $content_list;
		}

		/**
		 * Filter instructor comment queries
		 *
		 * @param object $query Current WP_Query object.
		 *
		 * @return object Updated WP_Query object.
		 *
		 * @since 3.3.0
		 */
		public function filter_instructor_comment_queries( $query ) {
			global $current_screen;

			// Check if instructor.
			if ( ! wdm_is_instructor() ) {
				return $query;
			}

			// Check if comments screen.
			if ( empty( $current_screen ) || 'edit-comments' != $current_screen->id ) {
				return $query;
			}

			// Reset author query.
			$query->set( 'author__in', [] );

			return $query;
		}

		/**
		 * Allow access to shared courses comments to co-instructors.
		 *
		 * @param array  $all_caps           List of all user capabilities.
		 * @param array  $requested_caps     List of requested capabilities.
		 * @param array  $args               Additional arguments.
		 * @param object $user              WP_User object of the user to provide access.
		 *
		 * @return array                    Updated list of all user capabilities.
		 */
		public function allow_shared_course_comments_access( $all_caps, $requested_caps, $args, $user ) {
			global $post, $current_screen;

			// Check if logged in and instructor.
			if ( empty( $user ) || ! wdm_is_instructor( $user->ID ) ) {
				return $all_caps;
			}

			// Check if edit comments access requested.
			if ( 'edit_comment' !== $args[0] ) {
				return $all_caps;
			}

			// Check if edit courses access.
			if ( ! in_array( 'edit_others_courses', $requested_caps ) ) {
				return $all_caps;
			}

			// Extract comment ID.
			$comment_id = intval( $args[2] );

			// Get comment.
			$comment = get_comment( $comment_id );

			// If empty return.
			if ( empty( $comment ) ) {
				return $all_caps;
			}

			$related_post_id = $comment->comment_post_ID;
			$related_post    = get_post( $related_post_id );

			// If empty return.
			if ( empty( $related_post ) ) {
				return $all_caps;
			}

			$sfwd_post_types = [
				'sfwd-courses',
				'sfwd-lessons',
				'sfwd-topic',
				'sfwd-quiz',
			];

			if ( ! in_array( $related_post->post_type, $sfwd_post_types ) ) {
				return $all_caps;
			}

			// Get course ID.
			$course_id = $related_post->ID;
			if ( 'sfwd-courses' != $related_post->post_type ) {
				$course_id = learndash_get_course_id( $related_post->ID );
			}

			// Get instructor courses list.
			$instructor_course_list = ir_get_instructor_complete_course_list();

			// If no access to course then return.
			if ( ! in_array( $course_id, $instructor_course_list ) ) {
				return $all_caps;
			}

			// Provide capability to edit shared course.
			$all_caps['edit_others_courses'] = 1;

			return $all_caps;
		}

		/**
		 * Get assignments and essays related to the courses
		 *
		 * @since 3.5.0
		 *
		 * @param int $course_ids    List of course IDS.
		 *
		 * @return array            List of assignments related to the course.
		 */
		public function get_course_submissions( $course_ids ) {
			$course_submissions = [];

			// Check if empty.
			if ( empty( $course_ids ) ) {
				return $course_submissions;
			}

			// Check if array.
			if ( ! is_array( $course_ids ) ) {
				$course_ids = [ $course_ids ];
			}

			// Assignments.
			$assignment_ids = get_posts(
				[
					'post_type'   => 'sfwd-assignment',
					'numberposts' => -1,
					'orderby'     => 'date',
					'order'       => 'DESC',
					'post_status' => 'publish',
					'fields'      => 'ids',
				]
			);

			// Get related assignments.
			foreach ( $assignment_ids as $assignment_id ) {
				$assignment_details = get_post_meta( $assignment_id );
				if ( empty( $assignment_details ) ) {
					continue;
				}

				$assignment_course_id = 0;

				// Find the course related to the assignment.
				$assignment_course_id = $assignment_details['course_id'][0];

				// If not related to course continue.
				if ( ! in_array( $assignment_course_id, $course_ids ) ) {
					continue;
				}

				array_push( $course_submissions, $assignment_id );
			}

			// Essays.
			$essay_ids = get_posts(
				[
					'post_type'   => 'sfwd-essays',
					'numberposts' => -1,
					'orderby'     => 'date',
					'order'       => 'DESC',
					'post_status' => [ 'graded', 'not_graded' ],
					'fields'      => 'ids',
				]
			);

			foreach ( $essay_ids as $essay_id ) {
				$essay_details = get_post_meta( $essay_id );
				if ( empty( $essay_details ) ) {
					continue;
				}

				$essay_course_id = 0;
				// Find the course related to the essay.
				$essay_course_id = $essay_details['course_id'][0];

				// If not related to course continue.
				if ( ! in_array( $essay_course_id, $course_ids ) ) {
					continue;
				}

				array_push( $course_submissions, $essay_id );
			}

			// Remove duplicates.
			$course_submissions = array_unique( $course_submissions );

			/**
			 * Filter the course submissions returned
			 *
			 * @since 3.4.2
			 */
			return apply_filters( 'ir_filter_get_course_submission_ids', $course_submissions );
		}

		/**
		 * Redirect essay email notification's to the right recipients.
		 *
		 * @since 4.1.1
		 *
		 * @param array $emails   List of emails.
		 * @param int   $comment   Comment ID.
		 *
		 * @return array            List of email recipients.
		 */
		public function alter_comment_notification_recipients( $emails, $comment ) {
			include_once ABSPATH . 'wp-content/plugins/sfwd-lms/themes/ld30/includes/helpers.php';

			// get comment details.
			$my_comment      = get_comment( $comment );
			$comment_post_id = $my_comment->comment_post_ID;
			$exists          = true;

			// get essay details.
			$essay_details = \learndash_get_user_quiz_entry_for_essay( $comment_post_id );

			// check if email belongs to essay.
			if ( $essay_details ) {
				// get quiz author from essay details.
				$quiz_id      = $essay_details['quiz'];
				$quiz_post    = get_post( $quiz_id );
				$author_id    = $quiz_post->post_author;
				$author_email = get_the_author_meta( 'user_email', $author_id );

				// get current user email.
				$current_user       = wp_get_current_user();
				$current_user_email = $current_user->user_email;
				$emails[]           = $current_user_email;
				if ( $current_user_email != $author_email ) {
					$emails[] = $author_email;
				}

				// get parent comment email.
				$parent_comment = get_comment( $my_comment->comment_parent );
				if ( $parent_comment ) {
					$parent_comment_email = $parent_comment->comment_author_email;
					if ( $parent_comment_email != $current_user_email && $parent_comment_email != $author_email ) {
						$emails[] = $parent_comment_email;
					}
				}

				// Sanitizing emails.
				foreach ( $emails as $author_email_index => $email ) {
					// do not sent the same email to current user who is commenting.
					if ( $current_user_email == $email ) {
						unset( $emails[ $author_email_index ] );
					}
					// parent email if check and current user email and author email are not same.
					if ( isset( $parent_comment ) && $parent_comment_email == $email && true == $exists ) {
						$exists = false;
					} elseif ( isset( $parent_comment ) && $parent_comment_email == $email ) {
						unset( $emails[ $author_email_index ] );
					}
				}
			}

			return $emails;
		}
	}
}
