<?php
/**
 * LearnDash Handler Module
 *
 * @since 3.5.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 *
 * cspell:ignore certi // ignoring misspelled words that we can't fix now.
 */

namespace InstructorRole\Modules\Classes;

use LearnDash\Core\Utilities\Cast;
use WP_Query;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_LearnDash_Handler' ) ) {
	/**
	 * Class Instructor Role LearnDash Handler Module
	 */
	class Instructor_Role_LearnDash_Handler {
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
		 * Filters assignments and essays to only show assignments and essays that an instructor has access to.
		 *
		 * @since 3.5.0
		 * @since 5.9.11 Allow the filter to run on REST requests.
		 *
		 * @param WP_Query $query The WP_Query object.
		 *
		 * @return WP_Query
		 */
		public function wdm_show_assignments_of_my_course( $query ) {
			global $current_screen;

			$allowed_screens    = [ 'edit-sfwd-essays', 'edit-sfwd-assignment' ];
			$allowed_post_types = [ 'sfwd-assignment', 'sfwd-essays' ];

			if ( ! in_array( Cast::to_string( $query->get( 'post_type' ) ), $allowed_post_types, true ) ) {
				return $query;
			}

			// Allows the filter to run on admin and REST requests.
			if (
				(
					! $query->is_admin
					|| ! in_array( Cast::to_string( $current_screen->id ), $allowed_screens, true )
				)
				&& ! defined( 'REST_REQUEST' )
			) {
				return $query;
			}

			$user_id = get_current_user_id();

			// Checks if the user is an instructor.
			if ( ! wdm_is_instructor( $user_id ) ) {
				return $query;
			}

			// Get instructor courses.
			$instructor_courses = ir_get_instructor_complete_course_list( $user_id );

			// If no courses, then no assignments.
			if ( empty( $instructor_courses ) ) {
				$query->set( 'post__in', [ 0 ] );
				return $query;
			}

			// First remove any author queries.
			$query->set( 'author__in', [] );

			// Get meta query if set and course_id meta set.
			$meta_query = $query->get( 'meta_query' );

			// Check if meta query set ( i.e. if any filters are selected ).
			if (
				! empty( $meta_query )
				&& is_array( $meta_query )
			) {
				// Update meta query to search only in instructor courses.
				$course_meta_set = false;
				foreach ( $meta_query as $key => $meta ) {
					if (
						isset( $meta['key'] )
						&& isset( $meta['compare'] )
						&& 'course_id' === $meta['key']
						&& 'IN' === $meta['compare']
					) {
						$meta_query[ $key ]['value'] = $instructor_courses;
						$course_meta_set             = true;
					}
				}

				if ( ! $course_meta_set ) {
					array_push(
						$meta_query,
						[
							'key'     => 'course_id',
							'value'   => $instructor_courses,
							'compare' => 'IN',
						]
					);
				}

				$query->set(
					'meta_query',
					$meta_query
				);
			}

			return $query;
		}

		public function wdm_restrict_assignment_edit() {
			$current_user_id = get_current_user_id();

			// Check if instructor.
			if ( ! wdm_is_instructor( $current_user_id ) ) {
				return;
			}

			// Get the post id.
			if ( empty( $post_id ) ) {
				$post_id = get_the_ID();
			}

			if ( empty( $post_id ) && ! empty( $_GET ) && array_key_exists( 'post', $_GET ) ) {
				$post_id = absint( $_GET['post'] );
			}

			$post = get_post( $post_id );

			// Check if assignment.
			if ( ! empty( $post ) && learndash_get_post_type_slug( 'assignment' ) !== $post->post_type ) {
				return;
			}

			$course_id = learndash_get_course_id( $post_id );
			// Check if course id set.
			if ( empty( $course_id ) ) {
				return;
			}

			$instructor_courses = ir_get_instructor_complete_course_list();
			// Check if current user is same as course author.
			if ( ! in_array( $course_id, $instructor_courses ) ) {
				wp_die( __( 'Cheating uh?', 'wdm_instructor_role' ) );
			}
		}

		/**
		 * Function to check lesson id is matching or not with post id.
		 */
		public function wdmCheckSelection( $lesson_id, $post_id ) {
			if ( $lesson_id == $post_id ) {
				return 'selected="selected"';
			}

			return '';
		}

		public function wdm_remove_assignment_author() {
			if ( wdm_is_instructor() ) {
				remove_meta_box( 'authordiv', 'sfwd-assignment', 'normal' );
			}
		}

		/**
		 * Allow instructors to view shared essays on frontend
		 *
		 * @since 3.5.3
		 */
		public function ir_allow_essay_permissions() {
			// Remove default LD essay permissions action.
			remove_action( 'wp', 'learndash_essay_permissions' );

			if ( is_singular( learndash_get_post_type_slug( 'essay' ) ) ) {
				$can_view_file = false;

				$post = get_post();
				if ( ( $post ) && ( is_a( $post, 'WP_Post' ) ) && ( learndash_get_post_type_slug( 'essay' ) === $post->post_type ) ) {
					$user_id = get_current_user_id();

					if ( ! empty( $user_id ) ) {
						if ( ( learndash_is_admin_user( $user_id ) ) || ( $post->post_author == $user_id ) ) {
							$can_view_file = true;
						} elseif ( ( learndash_is_group_leader_user( $user_id ) ) && ( learndash_is_group_leader_of_user( $user_id, $post->post_author ) ) ) {
							$can_view_file = true;
						} elseif ( wdm_is_instructor( $user_id ) && in_array( learndash_get_course_id( $post->ID ), ir_get_instructor_complete_course_list( $user_id ) ) ) {
							$can_view_file = true;
						}
					}
				}

				if ( true === $can_view_file ) {
					$uploaded_file = get_post_meta( $post->ID, 'upload', true );
					if ( ( ! empty( $uploaded_file ) ) && ( ! strstr( $post->post_content, $uploaded_file ) ) ) {
						/**
						 * Filters quiz essay upload link HTML output.
						 *
						 * @param string $upload_link Essay upload link HTML output.
						 */
						$post->post_content .= apply_filters( 'learndash-quiz-essay-upload-link', '<p><a target="_blank" href="' . esc_url( $uploaded_file ) . '">' . esc_html__( 'View uploaded file', 'learndash' ) . '</a></p>' );
					}
					return;
				} else {
					if ( empty( $user_id ) ) {
						$current_url     = remove_query_arg( 'test' );
						$redirect_to_url = wp_login_url( esc_url( $current_url ), true );
					} else {
						$redirect_to_url = get_bloginfo( 'url' );
					}
					/**
					 * Filters the URL to redirect a user if it does not have permission to view the essay.
					 *
					 * @param string $redirect_url Redirect URL.
					 */
					$redirect_to_url = apply_filters( 'learndash_essay_permissions_redirect_url', $redirect_to_url );
					if ( ! empty( $redirect_to_url ) ) {
						learndash_safe_redirect( $redirect_to_url );
					}
				}
			}
		}

		/**
		 * Allow instructors to view shared assignments on frontend
		 *
		 * @since 3.5.3
		 */
		public function ir_allow_assignment_permissions() {
			// Remove default LD assignment permissions action.
			remove_action( 'wp', 'learndash_assignment_permissions' );

			global $post;

			if ( ! empty( $post->post_type ) && learndash_get_post_type_slug( 'assignment' ) === $post->post_type && is_singular() ) {
				$user_id = get_current_user_id();

				if ( learndash_is_admin_user( $user_id ) ) {
					return;
				}

				if ( absint( $user_id ) === absint( $post->post_author ) ) {
					return;
				} elseif ( learndash_is_group_leader_of_user( $user_id, $post->post_author ) ) {
					return;
				} elseif ( wdm_is_instructor( $user_id ) && in_array( learndash_get_course_id( $post->ID ), ir_get_instructor_complete_course_list( $user_id ) ) ) {
					return;
				} else {
					/**
					 * Filters Assignment permission redirect URL.
					 *
					 * @param string $redirect_url Redirect URL.
					 */
					learndash_safe_redirect( apply_filters( 'learndash_assignment_permissions_redirect_url', get_bloginfo( 'url' ) ) );
				}
			}
		}

		/**
		 * To remove "Lesson options" tab from admin lessons' page to instructor.
		 */
		public function wdm_remove_tabs_certi( $current_page_id_data, $admin_tabs, $admin_tabs_on_page, $current_page_id ) {
			if ( wdm_is_instructor() ) {
				$course_pages = [ 'edit-sfwd-certificates', 'sfwd-certificates', 'admin_page_learndash-lms-certificate_shortcodes' ]; // certificate page IDs.
				$course_pages = apply_filters( 'wdmir_course_page', $course_pages ); // added in v2.4.0.
				if ( in_array( $current_page_id, $course_pages ) ) { // if admin lessons page.
					foreach ( $current_page_id_data as $key => $value ) {
						if ( 130 == $value ) { // Categories tab.
							unset( $current_page_id_data[ $key ] );
							break;
						}
					}
				}
			}

			return $current_page_id_data;
		}

		/**
		 * To remove "Lesson options" tab from admin lessons' page to instructor.
		 */
		public function wdm_remove_tabs_course( $current_page_id_data, $admin_tabs, $admin_tabs_on_page, $current_page_id ) {
			if ( wdm_is_instructor() ) {
				$course_pages = [ 'edit-sfwd-courses', 'sfwd-courses', 'admin_page_learndash-lms-course_shortcodes' ]; // lesson page IDs.

				if ( in_array( $current_page_id, $course_pages ) ) { // if admin lessons page.
					foreach ( $current_page_id_data as $key => $value ) {
						if ( 24 == $value ) { // Categories tab.
							unset( $current_page_id_data[ $key ] );
						} elseif ( 26 == $value ) { // Tags tab.
							unset( $current_page_id_data[ $key ] );
						} elseif ( 28 == $value ) { // Course Shortcodes.
							unset( $current_page_id_data[ $key ] );
						}
					}
				}
			}

			return $current_page_id_data;
		}

		/**
		 * To load posts of current user (instructor) only in the backend.
		 */
		public function wdm_load_my_courses( $options ) {
			if ( is_admin() ) {
				$wdm_user_id = get_current_user_id();

				if ( wdm_is_instructor( $wdm_user_id ) ) {
					$options['author__in'] = $wdm_user_id;
				}
			}

			return $options;
		}

		/**
		 * To remove "Lesson options" tab from admin lessons' page to instructor
		 */
		public function wdm_remove_tabs_lessons( $current_page_id_data, $admin_tabs, $admin_tabs_on_page, $current_page_id ) {
			if ( wdm_is_instructor() ) {
				$lesson_pages = [ 'sfwd-lessons', 'edit-sfwd-lessons' ]; // lesson page IDs.

				if ( in_array( $current_page_id, $lesson_pages ) ) { // if admin lessons page.
					foreach ( $current_page_id_data as $key => $value ) {
						if ( 50 == $value ) { // lesson options tab.
							unset( $current_page_id_data[ $key ] );
							break;
						}
					}
				}
			}
			return $current_page_id_data;
		}

		/**
		 * Function to remove other quizzes.
		 */
		public function wdm_prerequisite_remove_others() {
			if ( wdm_is_instructor() ) {
				$args    = [
					'post_type'   => 'sfwd-quiz',
					'post_status' => 'publish',
				];
				$quizzes = get_posts( $args );

				$my_quizzes = [];
				if ( function_exists( 'learndash_get_setting' ) ) {
					foreach ( $quizzes as $quiz ) {
						$settings = learndash_get_setting( $quiz, 'quiz_pro', true );
						array_push( $my_quizzes, (string) $settings );
					}
				}
				?>
				<script>
					var my_quizzes =
				<?php
				echo json_encode( $my_quizzes );
				?>
				;
					jQuery(document).ready(function () {
						jQuery("#sfwd-quiz_quiz_pro").hide();

						if (jQuery("select[name=quizList]").length) {

							jQuery("select[name=quizList] option").each(function () {

								if (jQuery.inArray(jQuery(this).val(), my_quizzes) == -1 && jQuery(this).val() != '0') {
									jQuery(this).remove();
								}
							});
						}
					});
				</script>
				<?php
			}
		}

		/**
		 * Function to restrict other users from quiz edit except specific instructor.
		 */
		public function wdm_restrict_quiz_edit() {
			$wdm_user_id = get_current_user_id();

			if ( wdm_is_instructor( $wdm_user_id ) ) {
				$post_id = isset( $_GET['post_id'] ) ? $_GET['post_id'] : 0;
				if ( ! empty( $post_id ) ) {
					$authorID     = wdm_get_author( $post_id );
					$allow_access = true;

					if ( $wdm_user_id != $authorID ) {
						$allow_access = false;
					}

					$allow_access = apply_filters( 'ir_filter_quiz_access', $allow_access, $post_id );

					if ( ! $allow_access ) {
						wp_die( __( 'Cheating uh?', 'wdm_instructor_role' ) );
					}
				}
			}
		}

		/**
		 * Skip user filtering based on related group users for instructors.
		 *
		 * @since 3.4.1
		 */
		public function skip_user_filtering_for_instructors() {
			if ( wdm_is_instructor() ) {
				remove_filter( 'learndash_fetch_quiz_statistic_history_where', 'learndash_fetch_quiz_statistic_history_where_filter', 10, 2 );
				remove_filter( 'learndash_fetch_quiz_toplist_history_where', 'learndash_fetch_quiz_statistic_history_where_filter', 10, 2 );
				remove_filter( 'learndash_fetch_quiz_statistic_overview_where', 'learndash_fetch_quiz_statistic_history_where_filter', 10, 2 );
			}
		}

		/**
		 * Add instructor shared quizzes to associated question
		 *
		 * @since 3.4.0
		 *
		 * @param string $query_options     Metabox key for the settings section.
		 * @param array  $settings_fields   Array of settings for the question.
		 *
		 * @return array
		 */
		public function filter_instructor_shared_quizzes( $query_options, $settings ) {
			if ( ! wdm_is_instructor() ) {
				return $query_options;
			}

			$user_id        = get_current_user_id();
			$shared_courses = ir_get_instructor_shared_course_list( $user_id );

			// If no shared courses, then return.
			if ( empty( $shared_courses ) ) {
				return $query_options;
			}

			global $wpdb;

			$quizzes = [];
			foreach ( $shared_courses as $course_id ) {
				$table1 = $wpdb->posts;
				$table2 = $wpdb->postmeta;

				$sql = $wpdb->prepare( "SELECT post_id FROM $table1 INNER JOIN $table2 ON $table1.ID = $table2.post_id WHERE $table1.post_type = %s AND $table2.meta_key = %s AND $table2.meta_value = %d", 'sfwd-quiz', 'course_id', $course_id );

				$results = $wpdb->get_col( $sql );
				if ( empty( $results ) ) {
					continue;
				}
				$quizzes = array_merge( $quizzes, $results );
			}

			if ( ! empty( $quizzes ) ) {
				$query_options['post__in']  = $quizzes;
				$query_options['ir_filter'] = 1;
			}

			return $query_options;
		}

		/**
		 * Allow filtering learndash actions on instructor dashboard
		 *
		 * @param object $query WP_Query Object
		 * @return object       WP_Query Object
		 * @since 3.5.0
		 */
		public function filter_learndash_queries( $query ) {
			if ( ! wdm_is_instructor() ) {
				return $query;
			}

			// Check if learndash filter request.
			if ( ! empty( $_POST ) && array_key_exists( 'action', $_POST ) && 'learndash_listing_select2_query' == $_POST['action'] ) {
				$query->set( 'author__in', [] );
			}

			// Check if learndash ajax pagination request.
			if ( ! empty( $_POST ) && array_key_exists( 'action', $_POST ) && 'ld30_ajax_pager' == $_POST['action'] ) {
				$query->set( 'author__in', [] );
			}

			return $query;
		}

		/**
		 * Allow fetching shared course quizzes under questions settings
		 *
		 * @param object $query
		 * @return object
		 * @since 3.5.0
		 */
		public function allow_fetching_shared_quizzes( $query ) {
			if ( $query->get( 'ir_filter' ) ) {
				$query->set( 'ir_filter', false );
				$query->set( 'author__in', [] );
			}
			return $query;
		}

		/**
		 * Remove post listing filters from instructor listing pages
		 *
		 * @since 3.5.1
		 *
		 * @param array $selectors  List of filter selectors.
		 * @param array $post_type  Post type of the current page.
		 */
		public function remove_post_listing_filters( $selectors, $post_type ) {
			// Remove author filter.
			if ( wdm_is_instructor() && array_key_exists( 'author', $selectors ) ) {
				unset( $selectors['author'] );
			}

			// Remove user filter.
			if ( wdm_is_instructor() && array_key_exists( 'user_id', $selectors ) ) {
				unset( $selectors['user_id'] );
			}

			// Remove group filter.
			if ( wdm_is_instructor() && array_key_exists( 'group_id', $selectors ) ) {
				unset( $selectors['group_id'] );
			}

			// Remove quiz filter.
			if ( wdm_is_instructor() && array_key_exists( 'quiz_id', $selectors ) ) {
				unset( $selectors['quiz_id'] );
			}

			// Remove question filter.
			if ( wdm_is_instructor() && array_key_exists( 'question_id', $selectors ) ) {
				unset( $selectors['question_id'] );
			}
			return $selectors;
		}

		/**
		 * Allow filtering of learndash post type filters
		 *
		 * @param array  $selector_arguments     Selector arguments for the listing page.
		 * @param object $selector              The LD post listing class object used for filtering.
		 * @param string $selector_post_type    Post of the selector filter.
		 */
		public function allow_learndash_post_type_filters( $selector_arguments, $selector, $selector_post_type ) {
			if ( wdm_is_instructor() ) {
				$allowed_ld_post_types = apply_filters(
					'ir_filter_allowed_learndash_filters',
					[
						'sfwd-courses',
						'sfwd-lessons',
						'sfwd-topic',
						'sfwd-quiz',
						'sfwd-essays',
						'sfwd-assignment',
						'ld-exam',
					]
				);

				if ( in_array( $selector_post_type, $allowed_ld_post_types, true ) && isset( $selector_arguments['post_type'] ) ) {
					$filter_post_type = $selector_arguments['post_type'];
					$query_data       = isset( $_POST['query_data'] ) ? $_POST['query_data'] : '';
					$selector_nonce   = isset( $query_data['selector_key'] ) ? $query_data['selector_key'] : '';

					switch ( $filter_post_type ) {
						case 'sfwd-courses':
							$selector_arguments['post__in'] = ir_get_instructor_complete_course_list();
							break;

						case 'sfwd-lessons':
							if ( wp_verify_nonce( $selector_nonce, 'lesson_id' ) ) {
								$selected_filters = $query_data['selector_filters'];
								$filter_key       = key( $selected_filters );
								$filter_val       = $selected_filters[ $filter_key ];
								if ( wp_verify_nonce( $filter_key, 'course_id' ) ) {
									$course_id                      = intval( $filter_val );
									$lessons_list                   = learndash_get_lesson_list( $course_id );
									$selector_arguments['post__in'] = array_column( $lessons_list, 'ID' );
								}
							}
							break;

						case 'sfwd-topic':
							if ( wp_verify_nonce( $selector_nonce, 'topic_id' ) ) {
								$selected_filters = $query_data['selector_filters'];
								foreach ( $selected_filters as $filter_key => $filter_val ) {
									if ( wp_verify_nonce( $filter_key, 'course_id' ) ) {
										$course_id = intval( $filter_val );
									}
									if ( wp_verify_nonce( $filter_key, 'lesson_id' ) ) {
										$lesson_id = intval( $filter_val );
									}
								}
								$topic_list                     = learndash_get_topic_list( $lesson_id, $course_id );
								$selector_arguments['post__in'] = array_column( $topic_list, 'ID' );
							}
							break;

						default:
							break;
					}
				}
			}

			return $selector_arguments;
		}

		/**
		 * Set the proper selector values for the filters selected for the LD post listing filters.
		 *
		 * @param mixed $value      The value set for the selected filter.
		 * @param array $selector  The selector object for the LD filter.
		 *
		 * @return mixed            Updated value set for the selected filter for instructors.
		 */
		public function set_listing_selector_values( $value, $selector ) {
			if ( wdm_is_instructor() && array_key_exists( 'ld-listing-nonce', $_GET ) && ! empty( $_GET['ld-listing-nonce'] ) ) {
				$allowed_ld_post_types = apply_filters(
					'ir_filter_allowed_learndash_filters',
					[
						'sfwd-courses',
						'sfwd-lessons',
						'sfwd-topic',
						'sfwd-quiz',
						'sfwd-essays',
						'sfwd-assignment',
						'ld-exam',
					]
				);
				$post_type             = '';
				if ( array_key_exists( 'post_type', $selector ) ) {
					$post_type = $selector['post_type'];
				}

				$current_post_type = filter_input( INPUT_GET, 'post_type', FILTER_DEFAULT );
				if ( in_array( $current_post_type, $allowed_ld_post_types ) ) {
					switch ( $post_type ) {
						case 'sfwd-courses':
							// Course Filter.
							if ( array_key_exists( 'course_id', $_GET ) && ! empty( $_GET['course_id'] ) ) {
								$course_id          = intval( $_GET['course_id'] );
								$instructor_courses = ir_get_instructor_complete_course_list();
								if ( in_array( $course_id, $instructor_courses ) ) {
									return $course_id;
								}
							}
							break;
						case 'sfwd-lessons':
							// Lesson Filter.
							if ( array_key_exists( 'lesson_id', $_GET ) && ! empty( $_GET['lesson_id'] ) ) {
								$course_id = intval( $_GET['course_id'] );
								$lesson_id = intval( $_GET['lesson_id'] );
								$lessons   = array_column( learndash_get_lesson_list( $course_id ), 'ID' );
								if ( in_array( $lesson_id, $lessons ) ) {
									return $lesson_id;
								}
							}
							break;
						case 'sfwd-topic':
							// Topic Filter.
							if ( array_key_exists( 'topic_id', $_GET ) && ! empty( $_GET['topic_id'] ) ) {
								$course_id = intval( $_GET['course_id'] );
								$lesson_id = intval( $_GET['lesson_id'] );
								$topic_id  = intval( $_GET['topic_id'] );

								$topics = learndash_get_topic_list( $lesson_id, $course_id );
								if ( in_array( $topic_id, $topics ) ) {
									return $topic_id;
								}
							}
							break;

							// Group Filter.
						default:
							// code...
							break;
					}
				}
			}
			return $value;
		}

		/**
		 * Process the LD post type filters to update the WP query to add proper meta query as per the filter values
		 * selected
		 *
		 * @param array  $query_vars    Query variables.
		 * @param string $post_type     Current post type.
		 * @param object $query         WP_Query object for the request.
		 *
		 * @return array                Updated query variables to filter data on instructor dashboard.
		 */
		public function process_learndash_post_type_filters( $query_vars, $post_type, $query ) {
			if ( wdm_is_instructor() && array_key_exists( 'ld-listing-nonce', $_GET ) && ! empty( $_GET['ld-listing-nonce'] ) ) {
				$allowed_ld_post_types = apply_filters(
					'ir_filter_allowed_learndash_filters',
					[
						'sfwd-courses',
						'sfwd-lessons',
						'sfwd-topic',
						'sfwd-quiz',
						'sfwd-essays',
						'sfwd-assignment',
						'ld-exam',
					]
				);
				$current_post_type     = filter_input( INPUT_GET, 'post_type', FILTER_DEFAULT );

				if ( in_array( $current_post_type, $allowed_ld_post_types ) ) {
					// Course Filter.
					if ( array_key_exists( 'course_id', $_GET ) && ! empty( $_GET['course_id'] ) ) {
						$course_id          = intval( $_GET['course_id'] );
						$instructor_courses = ir_get_instructor_complete_course_list();
						if ( in_array( $course_id, $instructor_courses ) ) {
							if ( ! isset( $query_vars['meta_query'] ) ) {
								$query_vars['meta_query'] = [];
							}

							$query_vars['meta_query'][] = [
								'key'   => 'course_id',
								'value' => absint( $course_id ),
							];
						}
					}
					// Lesson Filter.
					if ( array_key_exists( 'lesson_id', $_GET ) && ! empty( $_GET['lesson_id'] ) ) {
						$course_id   = intval( $_GET['course_id'] );
						$lesson_id   = intval( $_GET['lesson_id'] );
						$lesson_list = learndash_get_lesson_list( $course_id );
						if ( in_array( $lesson_id, $lesson_list ) ) {
							if ( ! isset( $query_vars['meta_query'] ) ) {
								$query_vars['meta_query'] = [];
							}

							$query_vars['meta_query'][] = [
								'key'   => 'lesson_id',
								'value' => absint( $lesson_id ),
							];
						}
					}
					// Topic Filter.
					if ( array_key_exists( 'lesson_id', $_GET ) && ! empty( $_GET['lesson_id'] ) ) {
						$course_id = intval( $_GET['course_id'] );
						$lesson_id = intval( $_GET['lesson_id'] );
						$topic_id  = intval( $_GET['topic_id'] );
						$topics    = learndash_get_topic_list( $lesson_id, $course_id );
						if ( in_array( $topic_id, $topics ) ) {
							if ( ! isset( $query_vars['meta_query'] ) ) {
								$query_vars['meta_query'] = [];
							}

							$query_vars['meta_query'][] = [
								'key'   => 'topic_id',
								'value' => absint( $topic_id ),
							];
						}
					}
					// Group Filter.
				}
			}
			return $query_vars;
		}

		/**
		 * Filter the list of users when checking quiz statistics
		 *
		 * @param object $user_query    WP_User_Query object.
		 */
		public function filter_quiz_statistic_users( $user_query ) {
			global $current_screen;
			$user_id = get_current_user_id();

			if ( ! empty( $current_screen ) && 'sfwd-quiz_page_ldAdvQuiz' === $current_screen->id && wdm_is_instructor( $user_id ) ) {
				// Final instructor course list.
				$course_list = ir_get_instructor_complete_course_list( $user_id );

				// No courses yet...
				if ( ! empty( $course_list ) && array_sum( $course_list ) > 0 ) {
					// Fetch the list of students in the courses.
					$all_students = [];
					foreach ( $course_list as $course_id ) {
						// Check if trashed course.
						if ( 'trash' == get_post_status( $course_id ) ) {
							continue;
						}

						$students_list = ir_get_users_with_course_access( $course_id, [ 'direct', 'group' ] );

						if ( empty( $students_list ) ) {
							continue;
						}
						$all_students = array_merge( $all_students, $students_list );
					}
					$unique_students_list = array_unique( $all_students );

					if ( empty( $unique_students_list ) ) {
						$user_query->set( 'include', [ 0 ] );
					} else {
						$user_query->set( 'include', implode( ',', $unique_students_list ) );
					}
				}
			}
			return $user_query;
		}

		/**
		 * Filter Quiz Statistics user listing to show only related users.
		 *
		 * @since 3.5.6
		 *
		 * @param string $where Statistics WHERE clause string.
		 * @param array  $args  Array of query args.
		 * @return string $where
		 */
		public function filter_quiz_statistic_users_overview( $where, $args ) {
			global $current_screen;
			$user_id = get_current_user_id();

			if ( wdm_is_instructor( $user_id ) ) {
				// Final instructor course list.
				$course_list = ir_get_instructor_complete_course_list( $user_id );

				// No courses yet...
				if ( ! empty( $course_list ) && array_sum( $course_list ) > 0 ) {
					// Fetch the list of students in the courses.
					$all_students = [];
					foreach ( $course_list as $course_id ) {
						// Check if trashed course.
						if ( 'trash' == get_post_status( $course_id ) ) {
							continue;
						}

						$students_list = ir_get_users_with_course_access( $course_id, [ 'direct', 'group' ] );

						if ( empty( $students_list ) ) {
							continue;
						}
						$all_students = array_merge( $all_students, $students_list );
					}
					$unique_students_list = array_unique( $all_students );

					if ( empty( $unique_students_list ) ) {
						$where .= ' AND u.ID = -1 ';
					} else {
						$where .= ' AND u.ID IN (' . implode( ',', $unique_students_list ) . ') ';
					}
				}
			}
			return $where;
		}
	}
}
