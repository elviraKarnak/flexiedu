<?php
/**
 * Groups Module
 *
 * @since 3.5.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

namespace InstructorRole\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Groups' ) ) {
	/**
	 * Class Instructor Role Groups Module
	 */
	class Instructor_Role_Groups {
		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
		 *
		 * @since 3.3.0
		 */
		protected static $instance = null;

		/**
		 * Plugin slug.
		 *
		 * @since 3.5.0
		 *
		 * @var string
		 */
		protected string $plugin_slug;

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
		 * Add group capabilities to instructor role
		 *
		 * @since 3.3.0
		 */
		public function add_group_capabilities() {
			// Get the instructor role.
			$role = get_role( 'wdm_instructor' );

			// Return if role not found.
			if ( null == $role ) {
				return;
			}

			$group_capabilities = [
				'read_group',
				'publish_groups',
				'edit_groups',
				'delete_groups',
				'delete_group',
				'edit_published_groups',
				'delete_published_groups',
				'group_leader',
			];

			// Add group capabilities.
			foreach ( $group_capabilities as $cap ) {
				$role->add_cap( $cap );
			}
		}

		/**
		 * Enable access to the groups screen to instructors.
		 *
		 * @since 3.3.0
		 */
		public function enable_access_to_groups_post_type( $allowed_post_types ) {
			if ( ! in_array( 'groups', $allowed_post_types ) ) {
				$allowed_post_types[] = 'groups';
			}

			return $allowed_post_types;
		}

		/**
		 * Filter group users for a selector
		 *
		 * @since 3.3.0
		 */
		public function filter_selector_group_users( $args, $class ) {
			// Check if instructor.
			if ( ! wdm_is_instructor() ) {
				return $args;
			}

			// Check if LD Group User Selector.
			if ( 'Learndash_Binary_Selector_Group_Users' === $class ) {
				// Get instructor students.
				$instructor_students = $this->get_instructor_students_list();
				if ( empty( $instructor_students ) ) {
					$instructor_students = [ 0 ];
				}
				$args['included_ids'] = $instructor_students;
			} elseif ( 'Learndash_Binary_Selector_Group_Leaders' === $class ) {
				// Include instructors in the list.
				$args['role__in'][] = 'wdm_instructor';
			}

			return $args;
		}

		/**
		 * Filter group courses for instructor group course selector
		 *
		 * @param array  $args
		 * @param string $class
		 * @return array
		 *
		 * @since 3.3.4
		 */
		public function filter_selector_group_courses( $args, $class ) {
			// Check if instructor.
			if ( ! wdm_is_instructor() ) {
				return $args;
			}

			if ( 'Learndash_Binary_Selector_Group_Courses' === $class ) {
				$course_list = ir_get_instructor_complete_course_list();
				// If no courses found, then return.
				if ( empty( $course_list ) ) {
					return $args;
				}

				$args['included_ids'] = $course_list;
			}

			return $args;
		}

		/**
		 * Get instructor students list
		 *
		 * @since 3.3.0
		 *
		 * @return array           List of unique students list for the instructor.
		 */
		public function get_instructor_students_list() {
			$unique_students_list = [];
			$user_id              = get_current_user_id();

			// Get total instructor course count.
			$course_list = get_posts(
				[
					'post_type' => 'sfwd-courses',
					'author'    => $user_id,
					'fields'    => 'ids',
				]
			);

			// Get shared courses.
			$shared_courses_list = get_user_meta( $user_id, 'ir_shared_courses', 1 );

			if ( ! empty( $shared_courses_list ) ) {
				$shared_courses = explode( ',', $shared_courses_list );
				$course_list    = array_merge( $course_list, $shared_courses );
			}

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
			}

			return apply_filters( 'ir_filter_instructor_student_list', $unique_students_list );
		}

		/**
		 * Filter instructor query to allow data in course group tab
		 *
		 * @param WP_Query $query
		 * @return WP_Query
		 *
		 * @since 3.3.4
		 */
		public function ir_filter_course_group_tab_data( $query ) {
			// If not ajax query, the return.
			if ( ! wp_doing_ajax() ) {
				return $query;
			}

			// If group pagination request then remove instructor filter.
			if ( array_key_exists( 'action', $_POST ) && 'learndash_binary_selector_pager' == $_POST['action'] ) {
				$query->set( 'author__in', [] );
			}

			return $query;
		}

		/**
		 * Include instructors in the list of group leaders for administrators on group edit page.
		 *
		 * @param array  $args
		 * @param string $class
		 * @return array
		 *
		 * @since 3.3.4
		 */
		public function add_instructors_to_group_leaders_for_admin( $args, $class ) {
			// Check if not instructor and admin.
			if ( ! current_user_can( 'manage_options' ) && ! wdm_is_instructor() ) {
				return $args;
			}

			// Check if group leader list.
			if ( 'Learndash_Binary_Selector_Group_Leaders' === $class ) {
				// Check if instructor.
				if ( ! wdm_is_instructor() ) {
					$args['role__in'][] = 'wdm_instructor';
				} else {
					// Include instructors in the list.
					$instructor_students = $this->get_instructor_students_list();
					if ( empty( $instructor_students ) ) {
						$instructor_students = [ 0 ];
					}
					$args['included_ids'] = $instructor_students;
					$args['role__in'][]   = 'wdm_instructor';
				}
			}

			return $args;
		}

		/**
		 * Filter instructor groups on course page
		 *
		 * @since 3.4.0
		 *
		 * @param array  $args       List of arguments for the LD Binary selector
		 * @param string $class     Class of the LD binary selector
		 *
		 * @return array            Updated list of arguments for the LD Binary selector
		 */
		public function filter_instructor_groups_for_course( $args, $class ) {
			if ( ! wdm_is_instructor() ) {
				return $args;
			}

			if ( 'Learndash_Binary_Selector_Course_Groups' === $class ) {
				$user_id = get_current_user_id();
				$groups  = get_posts(
					[
						'fields'    => 'ids',
						'post_type' => 'groups',
						'author'    => $user_id,
						'status'    => [ 'publish', 'draft' ],
					]
				);
				if ( empty( $groups ) ) {
					$groups = [ 0 ];
				}
				$args['included_ids'] = $groups;
			}

			return $args;
		}

		/**
		 * Filter group administration screen details on instructor dashboard
		 *
		 * @param WP_Query $query
		 * @return WP_Query
		 * @since 3.3.4
		 */
		public function ir_filter_group_admin_screen_data( $query ) {
			// Check if instructor.
			if ( ! wdm_is_instructor() ) {
				return $query;
			}

			// Check if group admin page.
			if ( ! empty( $_GET ) && array_key_exists( 'page', $_GET ) && 'group_admin_page' === $_GET['page'] ) {
				$query->set( 'author__in', [] );
			}

			return $query;
		}

		/**
		 * Filter all instructor groups courses to be displayed on the group edit screen
		 *
		 * @param WP_Query $query
		 *
		 * @return WP_Query
		 *
		 * @since 3.3.4
		 */
		public function ir_filter_group_courses( $query ) {
			// Check if instructor.
			if ( ! wdm_is_instructor() ) {
				return $query;
			}

			global $current_screen;

			// Check if group edit screen.
			if ( empty( $current_screen ) || 'groups' !== $current_screen->id ) {
				return $query;
			}

			// Check if post in set in query.
			$included_posts = $query->get( 'post__in' );

			if ( ! empty( $included_posts ) ) {
				$query->set( 'author__in', [] );
			}
			return $query;
		}

		/**
		 * Remove secondary author dropdown on groups edit page
		 *
		 * @since 3.4.1
		 *
		 * @param array  $prepared_args
		 * @param object $request
		 */
		public function remove_secondary_author_dropdown( $prepared_args, $request ) {
			// Check if valid request.
			if ( ! $request instanceof \WP_REST_Request ) {
				return $prepared_args;
			}

			// Verify headers.
			$headers = $request->get_headers();
			if ( empty( $headers ) || ! wp_verify_nonce( $headers['x_wp_nonce'][0], 'wp_rest' ) ) {
				return $prepared_args;
			}

			// Get post id from headers.
			$referer_url     = $headers['referer'][0];
			$referer_details = parse_url( $referer_url );
			$query_args      = [];

			if ( ! array_key_exists( 'query', $referer_details ) ) {
				return $prepared_args;
			}

			// Extract post details.
			parse_str( $referer_details['query'], $query_args );

			$current_post_type = '';

			// Get post type from post.
			if ( array_key_exists( 'post', $query_args ) && ! empty( $query_args['post'] ) ) {
				$post_id           = intval( $query_args['post'] );
				$post              = get_post( $post_id );
				$current_post_type = $post->post_type;
			}

			// or directly from query args if set.
			if ( array_key_exists( 'post_type', $query_args ) ) {
				$current_post_type = $query_args['post_type'];
			}

			// Check if groups post type.
			if ( 'groups' != $current_post_type ) {
				return $prepared_args;
			}

			// Check if author request.
			if ( ! array_key_exists( 'who', $prepared_args ) && 'authors' == $prepared_args['who'] ) {
				return $prepared_args;
			}

			unset( $prepared_args['who'] );

			// Allow admin to be listed in authors.
			$prepared_args['role__in'] = [ 0 ];

			return $prepared_args;
		}

		/**
		 * Filter instructor query to allow only instructor groups in group list
		 *
		 * @param WP_Query $query
		 * @return WP_Query
		 *
		 * @since 3.3.5
		 */
		public function ir_filter_group_list( $query ) {
			global $current_screen;
			if ( $query->is_admin ) {
				$current_user_id = get_current_user_id();
				if ( wdm_is_instructor( $current_user_id ) && isset( $current_screen ) && 'edit-groups' === $current_screen->id ) {
					$query->set( 'author__in', [ $current_user_id ] );
				}
			}
		}

		/**
		 * Remove group leader restrictions on assignment access if user is instructor.
		 *
		 * @since 3.5.5
		 */
		public function ir_remove_group_leader_restrictions() {
			if ( wdm_is_instructor() ) {
				remove_filter( 'user_has_cap', 'learndash_group_leader_can_edit_assignment_filter', 10 );
				remove_filter( 'user_has_cap', 'learndash_group_leader_can_edit_essay_filter', 10 );
			}
		}
	}
}
