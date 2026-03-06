<?php
/**
 * Groups Rest API Handler Module
 *
 * @since 5.7.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

namespace InstructorRole\Modules\Api;

use LearnDash_Custom_Label;
use WP_Error;
use LearnDash_Settings_Section;
use stdClass;
use WP_Post;
use WP_Query;
use WP_REST_Server;
use WP_User;
use WP_User_Query;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Groups_Api_Handler' ) ) {
	/**
	 * Class Instructor Role Groups Api Handler
	 */
	class Instructor_Role_Groups_Api_Handler extends Instructor_Role_Dashboard_Block_Api_Handler {
		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
		 *
		 * @since 5.7.0
		 */
		protected static $instance = null;

		/**
		 * LD Groups Endpoint Namespace.
		 *
		 * @var string  $ld_groups_namespace
		 *
		 * @since 5.7.0
		 */
		public $ld_groups_namespace = '';

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 5.7.0
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
		 * @since 5.7.0
		 */
		public function register_custom_endpoints() {
			$this->ld_groups_namespace = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_REST_API', 'groups' );

			// List Groups.
			register_rest_route(
				$this->namespace,
				'/group-list/',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_groups' ],
						'permission_callback' => [ $this, 'get_groups_permissions_check' ],
					],
				]
			);

			// Get groups list filter details.
			register_rest_route(
				$this->namespace,
				'group-list/filters/',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_group_filters_data' ],
						'permission_callback' => [ $this, 'get_group_filters_data_permissions_check' ],
					],
				]
			);

			// Trash Groups.
			register_rest_route(
				$this->namespace,
				'/groups/trash',
				[
					[
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => [ $this, 'trash_groups' ],
						'permission_callback' => [ $this, 'trash_groups_permissions_check' ],
					],
				]
			);

			// Restore groups.
			register_rest_route(
				$this->namespace,
				'/groups/restore',
				[
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'restore_groups' ],
						'permission_callback' => [ $this, 'restore_groups_permissions_check' ],
					],
				]
			);

			// Groups Overview.
			register_rest_route(
				$this->namespace,
				'/groups/(?P<id>[\d]+)/overview',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_group_overview' ],
						'permission_callback' => [ $this, 'get_group_overview_permissions_check' ],
					],
				]
			);

			// Group Courses.
			register_rest_route(
				$this->namespace,
				'/groups/(?P<id>[\d]+)/courses',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_group_courses' ],
						'permission_callback' => [ $this, 'get_group_courses_permissions_check' ],
					],
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'update_group_courses' ],
						'permission_callback' => [ $this, 'update_group_courses_permissions_check' ],
					],
				]
			);

			// Group Learners.
			register_rest_route(
				$this->namespace,
				'/groups/(?P<id>[\d]+)/learners',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_group_learners' ],
						'permission_callback' => [ $this, 'get_group_learners_permissions_check' ],
					],
				]
			);

			// User Course Details.
			register_rest_route(
				$this->namespace,
				'/course-details/(?P<course_id>[\d]+)/learner/(?P<user_id>[\d]+)',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_course_details' ],
						'permission_callback' => [ $this, 'get_course_details_permissions_check' ],
					],
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'update_course_details' ],
						'permission_callback' => [ $this, 'update_course_details_permissions_check' ],
					],
				]
			);

			// Group Users.
			register_rest_route(
				$this->namespace,
				'/groups/(?P<id>[\d]+)/users',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_group_learners' ],
						'permission_callback' => [ $this, 'get_group_learners_permissions_check' ],
					],
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'update_group_learners' ],
						'permission_callback' => [ $this, 'update_group_learners_permissions_check' ],
					],
				]
			);

			// Group Leaders.
			register_rest_route(
				$this->namespace,
				'/groups/(?P<id>[\d]+)/leaders',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_group_leaders' ],
						'permission_callback' => [ $this, 'get_group_leaders_permissions_check' ],
					],
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'update_group_leaders' ],
						'permission_callback' => [ $this, 'update_group_leaders_permissions_check' ],
					],
				]
			);
		}

		/**
		 * Get groups permissions check
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_groups_permissions_check( $request ) {
			return $this->group_leader_request_permission_check( $request );
		}

		/**
		 * Get groups data
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_groups( $request ) {
			$data              = [];
			$found_groups      = [];
			$users             = [];
			$current_user_id   = get_current_user_id();
			$instructor_groups = [];
			$can_create_groups = false;

			$parameters = shortcode_atts(
				[
					'search'      => '',
					'page'        => 1,
					'no_of_posts' => 9,
					'status'      => 'any',
					'month'       => '',
					'price_type'  => '',
					'learners'    => '',
					'date'        => '',
					'courses'     => '',
				],
				$request->get_params()
			);

			// Default query parameters.
			$args = [
				'post_type'      => learndash_get_post_type_slug( 'group' ),
				'posts_per_page' => $parameters['no_of_posts'],
				'post_status'    => 'any',
				'paged'          => $parameters['page'],
				'order_by'       => 'ID',
			];

			// For group leaders.
			if ( learndash_is_group_leader_user( $current_user_id ) && ! current_user_can( 'manage_options' ) ) {
				if ( null !== learndash_get_group_leader_manage_groups() ) {
					$can_create_groups = true;
				}

				$groups = [];
				// For instructor user.
				if ( wdm_is_instructor( $current_user_id ) ) {
					$groups            = get_posts(
						[
							'post_type'      => learndash_get_post_type_slug( 'group' ),
							'posts_per_page' => -1,
							'post_status'    => 'any',
							'author'         => $current_user_id,
							'fields'         => 'ids',
						]
					);
					$can_create_groups = true;
				}
				$groups = array_merge( $groups, learndash_get_administrators_group_ids( $current_user_id ) );

				if ( empty( $groups ) ) {
					$groups = [ 0 ];
				}
				$args['post__in'] = $groups;
			}

			// Search groups.
			if ( isset( $parameters['search'] ) && ! empty( $parameters['search'] ) ) {
				$args['s'] = trim( $parameters['search'] );
			}

			// Filter by month.
			if ( ! empty( $parameters['month'] ) ) {
				$args['m'] = trim( $parameters['month'] );
			}

			// Filter by status.
			if ( 'any' !== $parameters['status'] ) {
				switch ( $parameters['status'] ) {
					case 'mine':
						$args['author'] = $current_user_id;
						break;

					case 'group-leader':
						$groups = learndash_get_administrators_group_ids( $current_user_id );
						if ( empty( $groups ) ) {
							$groups = [ 0 ];
						}
						$args['post__in'] = $groups;
						break;

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
				}
			}

			// Filter by price type.
			if ( ! empty( $parameters['price_type'] ) ) {
				$args['meta_key']   = '_ld_price_type';
				$args['meta_value'] = trim( $parameters['price_type'] );
			}

			// Find requested groups.
			$group_list_query = new WP_Query( $args );

			foreach ( $group_list_query->posts as $group ) {
				if ( ! array_key_exists( $group->post_author, $users ) ) {
					$users[ $group->post_author ] = get_userdata( $group->post_author );
				}

				$found_groups[] = $this->get_list_single( 'group', $group, $users );
			}

			// If admin allow group creation.
			if ( current_user_can( 'manage_options' ) ) {
				$can_create_groups = true;
			}

			// Final data.
			$data = [
				'posts'             => $found_groups,
				'posts_count'       => $group_list_query->post_count,
				'total_posts'       => $group_list_query->found_posts,
				'max_page_num'      => $group_list_query->max_num_pages,
				'can_create_groups' => $can_create_groups,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get group overview permissions check
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_group_overview_permissions_check( $request ) {
			return $this->group_leader_request_permission_check( $request );
		}

		/**
		 * Get group overview data
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_group_overview( $request ) {
			$group_id              = $request['id'];
			$courses               = learndash_group_enrolled_courses( $group_id );
			$learners              = learndash_get_groups_user_ids( $group_id );
			$completed_all_courses = 0;
			$in_progress           = 0;
			$not_started_yet       = 0;
			$group_id_post         = get_post( $group_id );
			$user_id               = get_current_user_id();
			$edit_access           = false;
			$group_access          = learndash_get_group_leader_manage_groups();

			// Check if valid WP_Post object.
			if ( empty( $group_id ) || ! $group_id_post instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if user has access to group.
			if ( ! $this->user_can_view_group( $user_id, $group_id_post ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			if ( null !== $group_access ) {
				$edit_access = true;
			}

			// Iterate over each learner.
			foreach ( $learners as $learner_id ) {
				$completed_all_courses_flag = true;
				$in_progress_flag           = false;

				// Iterate over each course for the current learner.
				foreach ( $courses as $course_id ) {
					$learner_progress = learndash_user_get_course_progress( $learner_id, $course_id, 'legacy' );

					if ( 'completed' !== $learner_progress['status'] ) {
						$completed_all_courses_flag = false;
					}

					if ( 'completed' === $learner_progress['status'] || 'in_progress' === $learner_progress['status'] ) {
						$in_progress_flag = true;
					}
				}

				// Check learner's progress for all courses.
				if ( $completed_all_courses_flag ) {
					++$completed_all_courses;
				} elseif ( $in_progress_flag ) {
					++$in_progress;
				} else {
					++$not_started_yet;
				}
			}

			if ( ! empty( $learners ) ) {
				$learner_count = count( $learners );
			} else {
				$learner_count = 0;
			}

			if ( ! empty( $courses ) ) {
				$courses_count = count( $courses );
			} else {
				$courses_count = 0;
			}

			$data = [
				'courses'              => $courses_count,
				'learners'             => $learner_count,
				'completed_learners'   => $completed_all_courses,
				'in_progress_learners' => $in_progress,
				'not_started_learners' => $not_started_yet,
				'edit_access'          => $edit_access,
				'title'                => $group_id_post->post_title,
				'can_edit_group'       => $this->user_can_edit_group( $user_id, $group_id_post ),
				'can_manage_users'     => $this->user_can_manage_group_users( $user_id, $group_id_post ),
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get group courses permissions check
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_group_courses_permissions_check( $request ) {
			return $this->group_leader_request_permission_check( $request );
		}

		/**
		 * Get group courses data
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_group_courses( $request ) {
			$group_id     = $request['id'];
			$data         = [];
			$edit_access  = false;
			$group        = get_post( $group_id );
			$user_id      = get_current_user_id();
			$group_access = learndash_get_group_leader_manage_courses();

			// Check if valid WP_Post object.
			if ( empty( $group_id ) || ! $group instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if user has access to group.
			if ( ! $this->user_can_view_group( $user_id, $group ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			if ( null !== $group_access ) {
				$edit_access = true;
			}

			$courses  = learndash_group_enrolled_courses( $group_id );
			$learners = learndash_get_groups_user_ids( $group_id );

			if ( ! empty( $courses ) ) {
				foreach ( $courses as $course_id ) {
					$completed_learners = 0;
					if ( ! empty( $learners ) ) {
						foreach ( $learners as $learner_id ) {
							$learner_progress = learndash_user_get_course_progress( $learner_id, $course_id, 'legacy' );
							if ( 'completed' === $learner_progress['status'] ) {
								++$completed_learners;
							}
						}
					}
					$data[] = [
						'id'                 => $course_id,
						'title'              => get_the_title( $course_id ),
						'completed_learners' => $completed_learners,
						'total_learners'     => ( ! empty( $learners ) ) ? count( $learners ) : 0,
						'edit_access'        => $edit_access,
						'view_link'          => get_permalink( $course_id ),
						'edit_link'          => site_url() . '/course-builder/' . $course_id,
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
		 * Get group learners permissions check
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_group_learners_permissions_check( $request ) {
			return $this->group_leader_request_permission_check( $request );
		}

		/**
		 * Get group learners data
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_group_learners( $request ) {
			global $wpdb;
			$group_id          = $request['id'];
			$search            = $request->get_param( 'search' );
			$page              = $request->get_param( 'page' );
			$page              = empty( $page ) ? 1 : $page;
			$per_page          = $request->get_param( 'per_page' );
			$per_page          = empty( $per_page ) ? 5 : $per_page;
			$offset            = ( $page - 1 ) * $per_page;
			$course_id         = $request->get_param( 'course_id' );
			$courses           = learndash_group_enrolled_courses( $group_id );
			$user_id           = get_current_user_id();
			$group_id_post     = get_post( $group_id );
			$data              = [];
			$search_conditions = '';
			$users             = [];
			$where_conditions  = $wpdb->prepare(
				"{$wpdb->usermeta}.meta_key IN (%s, %s)",
				"group_{$group_id}_access_from",
				"learndash_group_users_{$group_id}"
			);
			$edit_access       = false;
			$group_access      = learndash_get_group_leader_manage_groups();

			// Check if valid WP_Post object.
			if ( empty( $group_id ) || ! $group_id_post instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if user has access to group.
			if ( ! $this->user_can_view_group( $user_id, $group_id_post ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			if ( null !== $group_access ) {
				$edit_access = true;
			}

			if ( ! empty( $search ) ) {
				$search_conditions = $wpdb->prepare(
					" AND ( {$wpdb->prefix}users.display_name LIKE %s )",
					'%' . $wpdb->esc_like( $search ) . '%'
				);
			}

			$total_count_sql = "SELECT COUNT(*)
								FROM {$wpdb->prefix}users
								JOIN {$wpdb->prefix}usermeta ON {$wpdb->prefix}users.ID = {$wpdb->usermeta}.user_id
								WHERE {$where_conditions}
								AND ( {$wpdb->usermeta}.meta_key = %s OR ( {$wpdb->usermeta}.meta_key = %s AND NOT EXISTS ( SELECT 1 FROM {$wpdb->usermeta} AS subquery WHERE subquery.user_id = {$wpdb->usermeta}.user_id AND subquery.meta_key = %s ) ) )
								{$search_conditions}";
			$total_count     = $wpdb->get_var( $wpdb->prepare( $total_count_sql, "group_{$group_id}_access_from", "learndash_group_users_{$group_id}", "group_{$group_id}_access_from" ) );

			$sql      = "SELECT {$wpdb->prefix}users.display_name, {$wpdb->prefix}users.user_email, {$wpdb->usermeta}.meta_value, {$wpdb->prefix}users.ID
					FROM {$wpdb->prefix}users
					JOIN {$wpdb->prefix}usermeta ON {$wpdb->prefix}users.ID = {$wpdb->usermeta}.user_id
					WHERE {$where_conditions}
					AND ( {$wpdb->usermeta}.meta_key = %s OR ( {$wpdb->usermeta}.meta_key = %s AND NOT EXISTS ( SELECT 1 FROM {$wpdb->usermeta} AS subquery WHERE subquery.user_id = {$wpdb->usermeta}.user_id AND subquery.meta_key = %s ) ) )
					{$search_conditions}
					ORDER BY {$wpdb->usermeta}.meta_value DESC
					LIMIT %d OFFSET %d;";
			$learners = $wpdb->get_results( $wpdb->remove_placeholder_escape( $wpdb->prepare( $sql, "group_{$group_id}_access_from", "learndash_group_users_{$group_id}", "group_{$group_id}_access_from", $per_page, $offset ) ) );

			if ( ! empty( $learners ) ) {
				foreach ( $learners as $learners_value ) {
					if ( ! empty( $course_id ) && $course_id > 0 ) {
						$completed_steps = learndash_course_get_completed_steps( $learners_value->ID, $course_id );
						$total_steps     = learndash_course_get_steps_count( $course_id );
						$users[]         = [
							'user_id'      => $learners_value->ID,
							'img_url'      => get_avatar_url( $learners_value->ID ),
							'display_name' => $learners_value->display_name,
							'email'        => $learners_value->user_email,
							'enrolled_on'  => gmdate( 'd/m/Y', $learners_value->meta_value ),
							'percentage'   => ( $total_steps > 0 ) ? number_format( round( ( $completed_steps / $total_steps ) * 100, 2 ), 2 ) : 0,
						];
					} else {
						$users[] = [
							'user_id'           => $learners_value->ID,
							'img_url'           => get_avatar_url( $learners_value->ID ),
							'display_name'      => $learners_value->display_name,
							'email'             => $learners_value->user_email,
							'enrolled_on'       => gmdate( 'd/m/Y', $learners_value->meta_value ),
							'completed_courses' => ( ! empty( $courses ) ) ? $this->get_completed_course_for_learner( $courses, $learners_value->ID ) : 0,
							'total_courses'     => ( ! empty( $courses ) ) ? count( $courses ) : 0,
							'course_list'       => $this->get_course_list_details_for_learner( $courses, $learners_value->ID ),
						];
					}
				}
			}

			$data = [
				'users'        => $users,
				'max_page_num' => ceil( $total_count / $per_page ),
				'users_count'  => ( ! empty( $users ) ) ? count( $users ) : 0,
				'total_users'  => $total_count,
				'edit_access'  => $edit_access,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get course list details for the learner
		 *
		 * @since 5.7.0
		 *
		 * @param array $course_id Course id of the course.
		 * @param int   $learner_id ID of the learner.
		 */
		public function get_course_list_details_for_learner( $course_id, $learner_id ) {
			foreach ( $course_id as $courses ) {
				$quiz_passed      = 0;
				$learner_progress = learndash_user_get_course_progress( $learner_id, $courses, 'legacy' );
				$completed_steps  = learndash_course_get_completed_steps( $learner_id, $courses );
				$total_steps      = learndash_course_get_steps_count( $courses );
				$course_quiz      = learndash_get_course_steps( $courses, [ 'sfwd-quiz' ] );

				if ( ! empty( $course_quiz ) ) {
					foreach ( $course_quiz as $quiz_id ) {
						$flag = learndash_user_quiz_has_completed( $learner_id, $quiz_id, $courses );
						if ( true === $flag ) {
							++$quiz_passed;
						}
					}
				}
				$course_list[] = [
					'id'              => $courses,
					'title'           => get_the_title( $courses ),
					'completed_steps' => $completed_steps,
					'total_steps'     => $total_steps,
					'progress'        => ( $total_steps > 0 ) ? number_format( round( ( $completed_steps / $total_steps ) * 100, 2 ), 2 ) . ' %' : '0 %',
					'quiz_passed'     => ( ! empty( $course_quiz ) ) ? $quiz_passed : '-',
					'total_quiz'      => ( ! empty( $course_quiz ) ) ? count( $course_quiz ) : '-',
					'status'          => $learner_progress['status'],
				];
			}
			return $course_list;
		}

		/**
		 * Get completed learners of a course
		 *
		 * @since 5.7.0
		 *
		 * @param array $course_id Course id of the course.
		 * @param int   $learner_id ID of the learner.
		 */
		public function get_completed_course_for_learner( $course_id, $learner_id ) {
			$completed_learners = 0;
			foreach ( $course_id as $courses ) {
				$learner_progress = learndash_user_get_course_progress( $learner_id, $courses, 'legacy' );
				if ( 'completed' === $learner_progress['status'] ) {
					++$completed_learners;
				}
			}
			return $completed_learners;
		}

		/**
		 * Get course details permissions check
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_course_details_permissions_check( $request ) {
			return $this->group_leader_request_permission_check( $request );
		}

		/**
		 * Get course details data
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_course_details( $request ) {
			$data = [];

			$course_id       = $request['course_id'];
			$course_id_post  = get_post( $course_id );
			$user_id         = $request['user_id'];
			$user_object     = get_userdata( $user_id );
			$completed_steps = learndash_course_get_completed_steps( $user_id, $course_id );
			$total_steps     = learndash_course_get_steps_count( $course_id );

			// Check if valid WP_Post object.
			if ( empty( $course_id ) || ! $course_id_post instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if learner has access to course.
			if ( ! current_user_can( 'manage_options' ) && ! in_array( (int) $course_id, learndash_user_get_enrolled_courses( $user_id ), true ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			$default_progress = [
				'points'       => '',
				'total_points' => '',
				'percentage'   => '',
				'pass'         => 'NA',
				'ref_id'       => '',
			];

			$ld_course_steps_object = \LDLMS_Factory_Post::course_steps( intval( $course_id ) );

			if ( $ld_course_steps_object ) {
				$steps     = $ld_course_steps_object->get_steps( 'h' );
				$all_steps = [
					'lessons' => [],
					'quizzes' => [],
				];

				foreach ( $steps as $step_type => $step_data ) {
					if ( ! empty( $step_data ) ) {
						foreach ( $step_data as $step_id => $step_content ) {
							// Fetch the title and comment count.
							$title = get_the_title( $step_id );
							if ( 'sfwd-quiz' === $step_type ) {
								$user_progress       = learndash_user_get_quiz_progress( $user_id, $step_id, $course_id );
								$latest_quiz_attempt = empty( $user_progress ) ? $default_progress : $user_progress[ array_key_last( $user_progress ) ];
								$entry               = [
									'category'     => $step_type,
									'id'           => $step_id,
									'title'        => $title,
									'complete'     => learndash_is_quiz_complete( $user_id, $step_id, $course_id ),
									'points'       => $latest_quiz_attempt['points'],
									'total_points' => $latest_quiz_attempt['total_points'],
									'percentage'   => $latest_quiz_attempt['percentage'],
									'status'       => ( $latest_quiz_attempt['pass'] ) ? __( 'Passed', 'wdm_instructor_role' ) : __( 'Failed', 'wdm_instructor_role' ),
									'ref_id'       => $latest_quiz_attempt['statistic_ref_id'],
									'passed'       => ( $latest_quiz_attempt['pass'] ) ? true : false,
								];
							} else {
								$entry = [
									'category' => $step_type,
									'id'       => $step_id,
									'title'    => $title,
									'complete' => learndash_is_item_complete( $step_id, $user_id, $course_id ),
									'steps'    => [
										'topic'   => [],
										'quizzes' => [],
									],
								];
							}

							// Check for nested levels.
							foreach ( $step_content as $sub_step_type => $sub_step_data ) {
								if ( ! empty( $sub_step_data ) ) {
									foreach ( $sub_step_data as $sub_step_id => $sub_step_content ) {
										if ( 'sfwd-quiz' === $sub_step_type ) {
											$user_progress       = learndash_user_get_quiz_progress( $user_id, $sub_step_id, $course_id );
											$latest_quiz_attempt = empty( $user_progress ) ? $default_progress : $user_progress[ array_key_last( $user_progress ) ];
											$sub_entry           = [
												'category' => $sub_step_type,
												'id'       => $sub_step_id,
												'title'    => get_the_title( $sub_step_id ),
												'complete' => learndash_is_quiz_complete( $user_id, $sub_step_id, $course_id ),
												'points'   => $latest_quiz_attempt['points'],
												'total_points' => $latest_quiz_attempt['total_points'],
												'percentage' => $latest_quiz_attempt['percentage'],
												'status'   => ( $latest_quiz_attempt['pass'] ) ? __( 'Passed', 'wdm_instructor_role' ) : __( 'Failed', 'wdm_instructor_role' ),
												'ref_id'   => $latest_quiz_attempt['statistic_ref_id'],
												'passed'   => ( $latest_quiz_attempt['pass'] ) ? true : false,
											];
										} else {
											$sub_entry = [
												'category' => $sub_step_type,
												'id'       => $sub_step_id,
												'title'    => get_the_title( $sub_step_id ),
												'complete' => learndash_is_item_complete( $sub_step_id, $user_id, $course_id ),
												'steps'    => [
													'quizzes' => [],
												],
											];
										}

										// Check for deeper nesting, if necessary.
										foreach ( $sub_step_content as $sub_sub_step_type => $sub_sub_step_data ) {
											if ( ! empty( $sub_sub_step_data ) ) {
												foreach ( $sub_sub_step_data as $sub_sub_step_id => $sub_sub_step_content ) {
													$user_progress                   = learndash_user_get_quiz_progress( $user_id, $sub_sub_step_id, $course_id );
													$latest_quiz_attempt             = empty( $user_progress ) ? $default_progress : $user_progress[ array_key_last( $user_progress ) ];
													$sub_sub_entry                   = [
														'category' => $sub_sub_step_type,
														'id'       => $sub_sub_step_id,
														'title'    => get_the_title( $sub_sub_step_id ),
														'complete' => learndash_is_quiz_complete( $user_id, $sub_sub_step_id, $course_id ),
														'points'   => $latest_quiz_attempt['points'],
														'total_points' => $latest_quiz_attempt['total_points'],
														'percentage' => $latest_quiz_attempt['percentage'],
														'status'   => ( $latest_quiz_attempt['pass'] ) ? __( 'Passed', 'wdm_instructor_role' ) : __( 'Failed', 'wdm_instructor_role' ),
														'ref_id' => $latest_quiz_attempt['statistic_ref_id'],
														'passed'       => ( $latest_quiz_attempt['pass'] ) ? true : false,
													];
													$sub_entry['steps']['quizzes'][] = $sub_sub_entry;
												}
											}
										}
										if ( 'sfwd-topic' === $sub_step_type ) {
											$entry['steps']['topic'][] = $sub_entry;
										} else {
											$entry['steps']['quizzes'][] = $sub_entry;
										}
									}
								}
							}
							if ( 'sfwd-lessons' === $step_type ) {
								$all_steps['lessons'][] = $entry;
							} else {
								$all_steps['quizzes'][] = $entry;
							}
						}
					}
				}
			}

			if ( $ld_course_steps_object ) {
				$data = [
					'steps'     => $all_steps,
					'user_info' => [
						'id'                       => $user_id,
						'name'                     => $user_object->display_name,
						'img_url'                  => get_avatar_url( $user_id ),
						'email'                    => $user_object->user_email,
						'course_completed_percent' => ( $total_steps > 0 ) ? number_format( round( ( $completed_steps / $total_steps ) * 100, 2 ), 2 ) : 0,
					],
					'title'     => get_the_title( $course_id ),
				];
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Update course details permissions check
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_course_details_permissions_check( $request ) {
			return $this->group_leader_request_permission_check( $request );
		}

		/**
		 * Update course details data
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_course_details( $request ) {
			$data = [];

			$course_id      = $request['course_id'];
			$course_id_post = get_post( $course_id );
			$user_id        = $request['user_id'];
			$step_id        = $request->get_param( 'step_id' );
			$mark_complete  = filter_var( $request->get_param( 'mark_complete' ), FILTER_VALIDATE_BOOLEAN );
			$results        = [];

			// Check if valid WP_Post object.
			if ( empty( $course_id ) || ! $course_id_post instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if learner has access to course.
			if ( ! current_user_can( 'manage_options' ) && ! in_array( (int) $course_id, learndash_user_get_enrolled_courses( $user_id ), true ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Mark incomplete.
			if ( ! $mark_complete ) {
				if ( 'sfwd-courses' === get_post_type( $step_id ) ) {
					$quizzes       = learndash_get_course_steps( $step_id, [ 'sfwd-quiz' ] );
					$lesson_topics = learndash_get_course_steps( $step_id, [ 'sfwd-lessons', 'sfwd-topic' ] );

					if ( ! empty( $quizzes ) ) {
						foreach ( $quizzes as $quiz ) {
							$results[ $quiz ] = 0;
						}
						$child_steps            = [
							'quiz' => [
								$course_id => $results,
							],
						];
						$quizzes_mark_completed = $this->mark_complete_user_quiz_progress( $user_id, $child_steps );
					}
					if ( ! empty( $lesson_topics ) ) {
						foreach ( $lesson_topics as $lesson_topic_steps ) {
							learndash_process_mark_incomplete( $user_id, $course_id, $lesson_topic_steps );
						}
					}
					$flag = false;
				}

				if ( 'sfwd-quiz' === get_post_type( $step_id ) ) {
					$results[ $step_id ]    = 0;
					$child_steps            = [
						'quiz' => [
							$course_id => $results,
						],
					];
					$quizzes_mark_completed = $this->mark_complete_user_quiz_progress( $user_id, $child_steps );
					$flag                   = false;
				} else {
					$lesson_child_topics   = learndash_course_get_children_of_step( $course_id, $step_id, 'sfwd-topic', 'ids', true );
					$quizzes               = learndash_course_get_children_of_step( $course_id, $step_id, 'sfwd-quiz', 'ids', true );
					$lesson_mark_completed = learndash_process_mark_incomplete( $user_id, $course_id, $step_id );
					if ( ! empty( $lesson_child_topics ) ) {
						foreach ( $lesson_child_topics as $single_topic ) {
							$topic_mark_completed = learndash_process_mark_incomplete( $user_id, $course_id, $single_topic );
						}
					}
					if ( ! empty( $quizzes ) ) {
						foreach ( $quizzes as $quiz ) {
							$results[ $quiz ] = 0;
						}
						$child_steps            = [
							'quiz' => [
								$course_id => $results,
							],
						];
						$quizzes_mark_completed = $this->mark_complete_user_quiz_progress( $user_id, $child_steps );
					}
					$flag = false;
				}
			} else {
				// Mark complete.
				if ( 'sfwd-courses' === get_post_type( $step_id ) ) {
					$course_mark_completed = learndash_user_course_complete_all_steps( $user_id, $course_id );
					$flag                  = true;
				}

				if ( 'sfwd-quiz' === get_post_type( $step_id ) ) {
					$results[ $step_id ]    = 1;
					$child_steps            = [
						'quiz' => [
							$course_id => $results,
						],
					];
					$quizzes_mark_completed = $this->mark_complete_user_quiz_progress( $user_id, $child_steps );
					$flag                   = true;
				} else {
					$lesson_child_topics   = learndash_course_get_children_of_step( $course_id, $step_id, 'sfwd-topic', 'ids', true );
					$quizzes               = learndash_course_get_children_of_step( $course_id, $step_id, 'sfwd-quiz', 'ids', true );
					$lesson_mark_completed = learndash_process_mark_complete( $user_id, $step_id, false, $course_id );
					if ( ! empty( $lesson_child_topics ) ) {
						foreach ( $lesson_child_topics as $single_topic ) {
							$topic_mark_completed = learndash_process_mark_complete( $user_id, $single_topic, false, $course_id );
						}
					}
					if ( ! empty( $quizzes ) ) {
						foreach ( $quizzes as $quiz ) {
							$results[ $quiz ] = 1;
						}
						$child_steps            = [
							'quiz' => [
								$course_id => $results,
							],
						];
						$quizzes_mark_completed = $this->mark_complete_user_quiz_progress( $user_id, $child_steps );
					}
					$flag = true;
				}
			}

			$completed_steps = learndash_course_get_completed_steps( $user_id, $course_id );
			$total_steps     = learndash_course_get_steps_count( $course_id );

			$data = [
				'step_id'         => $step_id,
				'mark_complete'   => $flag,
				'course_progress' => ( $total_steps > 0 ) ? number_format( round( ( $completed_steps / $total_steps ) * 100, 2 ), 2 ) . ' %' : '0 %',
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Marks complete or incomplete user quiz progress.
		 *
		 * Make sure to pass 0 to quiz_id for marking it incomplete and 1 for complete.
		 *
		 * @since 5.7.0
		 * @param int   $user_id       User ID to update.
		 * @param array $user_progress User progress array.
		 * @return array Array of processed course IDs.
		 */
		public function mark_complete_user_quiz_progress( $user_id, $user_progress ) {
			$processed_course_ids = [];
			if ( empty( $user_id ) ) {
				return $processed_course_ids;
			}

			if ( ( isset( $user_progress['quiz'] ) ) && ( ! empty( $user_progress['quiz'] ) ) ) {
				$usermeta      = get_user_meta( $user_id, '_sfwd-quizzes', true );
				$quiz_progress = empty( $usermeta ) ? [] : $usermeta;
				$quiz_changed  = false; // Simple flag to let us know we changed the quiz data so we can save it back to user meta.

				foreach ( $user_progress['quiz'] as $course_id => $course_quiz_set ) {
					foreach ( $course_quiz_set as  $quiz_id => $quiz_new_status ) {
						$quiz_meta = get_post_meta( $quiz_id, '_sfwd-quiz', true );
						if ( ! empty( $quiz_meta ) ) {
							$quiz_old_status = ! learndash_is_quiz_notcomplete( $user_id, [ $quiz_id => 1 ], false, $course_id );

							// For Quiz if the admin marks a qiz complete we don't attempt to update an existing attempt for the user quiz.
							// Instead we add a new entry. LD doesn't care as it will take the complete one for calculations where needed.
							if ( (bool) true === (bool) $quiz_new_status ) {
								if ( (bool) true !== (bool) $quiz_old_status ) {
									if ( isset( $quiz_meta['sfwd-quiz_lesson'] ) ) {
										$lesson_id = absint( $quiz_meta['sfwd-quiz_lesson'] );
									} else {
										$lesson_id = 0;
									}

									if ( isset( $quiz_meta['sfwd-quiz_topic'] ) ) {
										$topic_id = absint( $quiz_meta['sfwd-quiz_topic'] );
									} else {
										$topic_id = 0;
									}

									// If the admin is marking the quiz complete AND the quiz is NOT already complete...
									// Then we add the minimal quiz data to the user profile.
									$quizdata = [
										'quiz'             => $quiz_id,
										'score'            => 0,
										'count'            => 0,
										'question_show_count' => 0,
										'pass'             => true,
										'rank'             => '-',
										'time'             => time(),
										'pro_quizid'       => absint( $quiz_meta['sfwd-quiz_quiz_pro'] ),
										'course'           => $course_id,
										'lesson'           => $lesson_id,
										'topic'            => $topic_id,
										'points'           => 0,
										'total_points'     => 0,
										'percentage'       => 0,
										'timespent'        => 0,
										'has_graded'       => false,
										'statistic_ref_id' => 0,
										'm_edit_by'        => get_current_user_id(), // Manual Edit By ID.
										'm_edit_time'      => time(), // Manual Edit timestamp.
									];

									$quiz_progress[] = $quizdata;

									if ( true === $quizdata['pass'] ) {
										$quizdata_pass = true;
									} else {
										$quizdata_pass = false;
									}

									// Then we add the quiz entry to the activity database.
									learndash_update_user_activity(
										[
											'course_id' => $course_id,
											'user_id'   => $user_id,
											'post_id'   => $quiz_id,
											'activity_type' => 'quiz',
											'activity_action' => 'insert',
											'activity_status' => $quizdata_pass,
											'activity_started' => $quizdata['time'],
											'activity_completed' => $quizdata['time'],
											'activity_meta' => $quizdata,
										]
									);

									$quiz_changed = true;

									if ( ( isset( $quizdata['course'] ) ) && ( ! empty( $quizdata['course'] ) ) ) {
										$quizdata['course'] = get_post( $quizdata['course'] );
									}

									if ( ( isset( $quizdata['lesson'] ) ) && ( ! empty( $quizdata['lesson'] ) ) ) {
										$quizdata['lesson'] = get_post( $quizdata['lesson'] );
									}

									if ( ( isset( $quizdata['topic'] ) ) && ( ! empty( $quizdata['topic'] ) ) ) {
										$quizdata['topic'] = get_post( $quizdata['topic'] );
									}

									/**
									 * Fires after the quiz is marked as complete.
									 *
									 * @param array   $quizdata An array of quiz data.
									 * @param WP_User $user     WP_User object.
									 */
									do_action( 'learndash_quiz_completed', $quizdata, get_user_by( 'ID', $user_id ) );
								}
							} elseif ( true !== $quiz_new_status ) {
								// If we are un-setting a quiz ( changing from complete to incomplete). We need to do some complicated things...
								if ( true === $quiz_old_status ) {
									if ( ! empty( $quiz_progress ) ) {
										foreach ( $quiz_progress as $quiz_idx => $quiz_item ) {
											if ( $quiz_item['quiz'] == $quiz_id && true === (bool) $quiz_item['pass'] ) {
												$quiz_progress[ $quiz_idx ]['pass'] = false;

												// We need to update the activity database records for this quiz_id.
												$activity_query_args = [
													'post_ids' => $quiz_id,
													'user_ids' => $user_id,
													'activity_type' => 'quiz',
												];
												$quiz_activity       = learndash_reports_get_activity( $activity_query_args );
												if ( ( isset( $quiz_activity['results'] ) ) && ( ! empty( $quiz_activity['results'] ) ) ) {
													foreach ( $quiz_activity['results'] as $result ) {
														if ( ( isset( $result->activity_meta['pass'] ) ) && ( true === $result->activity_meta['pass'] ) ) {
															// If the activity meta 'pass' element is set to true we want to update it to false.
															learndash_update_user_activity_meta( $result->activity_id, 'pass', false );

															// Also we need to update the 'activity_status' for this record.
															learndash_update_user_activity(
																[
																	'activity_id' => $result->activity_id,
																	'course_id' => $course_id,
																	'user_id' => $user_id,
																	'post_id' => $quiz_id,
																	'activity_type' => 'quiz',
																	'activity_action' => 'update',
																	'activity_status' => false,
																]
															);
														}
													}
												}

												$quiz_changed = true;
											}

											/**
											 * Remove the quiz lock.
											 *
											 * @since 2.3.1
											 */
											if ( ( isset( $quiz_item['pro_quizid'] ) ) && ( ! empty( $quiz_item['pro_quizid'] ) ) ) {
												learndash_remove_user_quiz_locks( $user_id, $quiz_item['quiz'] );
											}
										}
									}
								}
							}

							$processed_course_ids[ intval( $course_id ) ] = intval( $course_id );
						}
					}
				}

				if ( true === $quiz_changed ) {
					update_user_meta( $user_id, '_sfwd-quizzes', $quiz_progress );
				}
			}

			if ( ! empty( $processed_course_ids ) ) {
				foreach ( array_unique( $processed_course_ids ) as $course_id ) {
					learndash_process_mark_complete( $user_id, $course_id );
					learndash_update_group_course_user_progress( $course_id, $user_id, true );
				}
			}

			return $processed_course_ids;
		}

		/**
		 * Fetch additional group information on group listing page.
		 *
		 * @since 5.7.0
		 *
		 * @param array   $data   Group Information.
		 * @param WP_Post $post   Post Object.
		 * @param string  $type   Type of post.
		 * @param array   $users  User information.
		 *
		 * @return array            Updated group information details.
		 */
		public function add_additional_group_info( $data, $post, $type, $users ) {
			// Check if a group.
			if ( 'group' !== $type || learndash_get_post_type_slug( 'group' ) !== $post->post_type ) {
				return $data;
			}

			// Fetch group users.
			$group_users = learndash_get_groups_users( $post->ID );

			$group_courses = new WP_Query(
				[
					'post_type'      => learndash_get_post_type_slug( 'course' ),
					'post_status'    => [ 'publish', 'draft' ],
					'fields'         => 'ids',
					'posts_per_page' => -1,
					'meta_query'     => [
						[
							'key'     => 'learndash_group_enrolled_' . $post->ID,
							'compare' => 'EXISTS',
						],
					],
				]
			);

			$data['courses_count']  = $group_courses->post_count;
			$data['learners_count'] = count( $group_users );

			// Check if user can edit group.
			$data['can_edit_group'] = $this->user_can_edit_group( get_current_user_id(), $post );

			return $data;
		}

		/**
		 * Trash groups
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function trash_groups( $request ) {
			$data             = [];
			$user_id          = get_current_user_id();
			$delete           = false;
			$query_parameters = $request->get_params();

			// Get the group(s) to be trashed.
			$trash_ids = [];

			if ( isset( $query_parameters['groups'] ) ) {
				$trash_ids = explode( ',', $query_parameters['groups'] );
			}

			// Check whether to trash or permanently delete.
			if ( isset( $query_parameters['action'] ) && 'delete' === $query_parameters['action'] ) {
				$delete = true;
			}

			foreach ( $trash_ids as $group_id ) {
				$group = get_post( $group_id );

				// Check if valid group.
				if ( empty( $group ) || ! $group instanceof WP_Post || learndash_get_post_type_slug( 'group' ) !== $group->post_type ) {
					continue;
				}

				// Verify if user is group author or admin.
				if ( current_user_can( 'manage_options' ) || ( intval( $group->post_author ) === $user_id ) ) {
					// Trash or delete group.
					if ( ! $delete ) {
						$trashed_group     = wp_trash_post( $group_id );
						$data['trashed'][] = $trashed_group;
					} else {
						$deleted_group     = wp_delete_post( $group_id, $delete );
						$data['deleted'][] = $deleted_group;
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
		 * Trash groups permissions check
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function trash_groups_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Restore trashed groups.
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request  WP_REST_Request instance.
		 */
		public function restore_groups( $request ) {
			$data    = [];
			$user_id = get_current_user_id();

			$parameters = $request->get_body_params();

			// If empty get all params.
			if ( empty( $parameters ) ) {
				$parameters = $request->get_params();
			}

			// Get the group(s) to be restored.
			$restore_ids = [];

			if ( isset( $parameters['groups'] ) ) {
				$restore_ids = explode( ',', $parameters['groups'] );
			}

			foreach ( $restore_ids as $group_id ) {
				$group = get_post( $group_id );

				// Check if valid trashed group.
				if ( empty( $group ) || ! $group instanceof WP_Post || learndash_get_post_type_slug( 'group' ) !== $group->post_type || 'trash' !== $group->post_status ) {
					continue;
				}

				// Verify if user is group author or admin.
				if ( current_user_can( 'manage_options' ) || ( intval( $group->post_author ) === $user_id ) ) {
					// Restore group.
					$restored_group = wp_untrash_post( $group_id );
					if ( ! empty( $restored_group ) ) {
						$data['restored'][] = $restored_group;
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
		 * Restore groups permissions check
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function restore_groups_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Fetch missing group details.
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WP_Post          $post     The post object.
		 * @param WP_REST_Request  $request  The request object.
		 *
		 * @return WP_REST_Response          Updated response object.
		 */
		public function fetch_missing_group_details( $response, $post, $request ) {
			if ( 'GET' === $request->get_method() && '/' . LEARNDASH_REST_API_NAMESPACE . '/v2/' . $this->ld_groups_namespace . '/' . $post->ID === $request->get_route() ) {
				$data                        = $response->get_data();
				$data['auto_enroll']         = get_post_meta( $post->ID, 'ld_auto_enroll_group_courses', true );
				$data['auto_enroll_courses'] = get_post_meta( $post->ID, 'ld_auto_enroll_group_course_ids', true );
				$data['interval']            = intval( learndash_get_setting( $post->ID, 'group_price_billing_p3' ) );
				$data['frequency']           = strval( learndash_get_setting( $post->ID, 'group_price_billing_t3' ) );
				$data['repeats']             = intval( learndash_get_setting( $post->ID, 'post_no_of_cycles' ) );
				$data['trial_price']         = intval( learndash_get_setting( $post->ID, 'group_trial_price' ) );
				$data['trial_interval']      = intval( learndash_get_setting( $post->ID, 'group_trial_duration_p1' ) );
				$data['trial_frequency']     = strval( learndash_get_setting( $post->ID, 'group_trial_duration_t1' ) );
				$data['start_date']          = learndash_get_setting( $post->ID, 'group_start_date' );
				$data['end_date']            = learndash_get_setting( $post->ID, 'group_end_date' );
				$data['student_limit']       = learndash_get_setting( $post->ID, 'group_seats_limit' );

				// Fetch private title.
				if ( 'private' === $data['status'] ) {
					$data['private_title'] = $post->post_title;
				}

				// Fetch custom pagination enable/disable for LearnDash < 5.0
				// because LearnDash < v5.0 doesn't pass this setting to the REST API.
				if (
					defined( 'LEARNDASH_VERSION' )
					// @phpstan-ignore-next-line -- Required check for LearnDash < v5.0.
					&& version_compare( LEARNDASH_VERSION, '5.0.0-dev', '<' )
				) {
					$data['courses_per_page_enabled'] = learndash_get_setting( $post->ID, 'group_courses_per_page_enabled' );
				}

				$response->set_data( $data );
			}
			return $response;
		}

		/**
		 * Update missing group details.
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Response $result  Result to send to the client.
		 * @param WP_REST_Server   $server  WP_REST_Server instance.
		 * @param WP_REST_Request  $request WP_REST_Request instance.
		 *
		 * @return WP_REST_Response          Updated result object.
		 */
		public function update_missing_group_details( $result, $server, $request ) {
			if ( 'POST' === $request->get_method() ) {
				$post = get_post( intval( $request['id'] ) );

				if (
					$post instanceof WP_Post
					&& '/' . LEARNDASH_REST_API_NAMESPACE . '/v2/' . $this->ld_groups_namespace . '/' . $post->ID === $request->get_route()
				) {
					$form_data = $request->get_body_params();

					// If empty get all params.
					if ( empty( $form_data ) ) {
						$form_data = $request->get_params();
					}

					// Update auto enroll enable/disable setting.
					if ( array_key_exists( 'auto_enroll_enable', $form_data ) ) {
						if ( 'yes' === $form_data['auto_enroll_enable'] ) {
							update_post_meta( $post->ID, 'ld_auto_enroll_group_courses', 'yes' );
						} else {
							delete_post_meta( $post->ID, 'ld_auto_enroll_group_courses' );
						}
					}

					// Update auto enroll courses setting.
					if ( array_key_exists( 'auto_enroll_courses', $form_data ) ) {
						// Filter valid course ids.
						$course_ids = array_filter(
							array_map(
								function ( $course_id ) {
									return filter_var( trim( $course_id ), FILTER_VALIDATE_INT );
								},
								explode( ',', trim( $form_data['auto_enroll_courses'] ) )
							)
						);

						if ( empty( $course_ids ) ) {
							delete_post_meta( $post->ID, 'ld_auto_enroll_group_course_ids' );
						} else {
							update_post_meta( $post->ID, 'ld_auto_enroll_group_course_ids', $course_ids );
						}
					}

					// Update recurring times setting.
					if ( array_key_exists( 'recurring_times', $form_data ) ) {
						$times = '';
						if ( ! empty( $form_data['recurring_times'] ) ) {
							$times = intval( $form_data['recurring_times'] );
						}
						learndash_update_setting( $post->ID, 'post_no_of_cycles', $times );
					}

					// Update group trial price setting.
					if ( array_key_exists( 'group_trial_price', $form_data ) ) {
						learndash_update_setting( $post->ID, 'group_trial_price', floatval( $form_data['group_trial_price'] ) );
					}

					// Save some settings on LearnDash < v5.0 because
					// LearnDash < v5.0 doesn't pass these settings to the REST API.
					if (
						defined( 'LEARNDASH_VERSION' )
						// @phpstan-ignore-next-line -- Required check for LearnDash < v5.0.
						&& version_compare( LEARNDASH_VERSION, '5.0.0-dev', '<' )
					) {
						// Update group start date setting.
						if ( array_key_exists( 'group_start_date', $form_data ) ) {
							$start_date = strtotime( $form_data['group_start_date'] . ' ' . get_option( 'timezone_string' ) );
							learndash_update_setting( $post->ID, 'group_start_date', $start_date );
						}

						// Update group end date setting.
						if ( array_key_exists( 'group_end_date', $form_data ) ) {
							$end_date = strtotime( $form_data['group_end_date'] . ' ' . get_option( 'timezone_string' ) );
							learndash_update_setting( $post->ID, 'group_end_date', $end_date );
						}

						// Update group student limit setting.
						if ( array_key_exists( 'group_seats_limit', $form_data ) ) {
							learndash_update_setting( $post->ID, 'group_seats_limit', intval( $form_data['group_seats_limit'] ) );
						}

						// Update custom pagination enable/disable setting.
						if ( array_key_exists( 'courses_per_page_enabled', $form_data ) ) {
							learndash_update_setting( $post->ID, 'group_courses_per_page_enabled', $form_data['courses_per_page_enabled'] );
						}

						// Update custom pagination setting.
						if ( array_key_exists( 'courses_per_page_custom', $form_data ) && array_key_exists( 'courses_per_page_enabled', $form_data ) && $form_data['courses_per_page_enabled'] ) {
							learndash_update_setting( $post->ID, 'group_courses_per_page_custom', intval( $form_data['courses_per_page_custom'] ) );
						}
					}

					// Update group extend access settings.
					if ( array_key_exists( 'new_expiration_date', $form_data ) ) {
						if ( array_key_exists( 'group_users_to_extend_access', $form_data ) && array_key_exists( 'group_courses_to_extend_access', $form_data ) ) {
							if ( ! is_array( $form_data['group_courses_to_extend_access'] ) ) {
								$form_data['group_courses_to_extend_access'] = explode( ',', trim( $form_data['group_courses_to_extend_access'] ) );
							}
							$extend_course_ids = array_filter(
								array_map(
									function ( $course_id ) {
										return filter_var( trim( $course_id ), FILTER_VALIDATE_INT );
									},
									$form_data['group_courses_to_extend_access']
								)
							);
							if ( ! is_array( $form_data['group_users_to_extend_access'] ) ) {
								$form_data['group_users_to_extend_access'] = explode( ',', trim( $form_data['group_users_to_extend_access'] ) );
							}
							$extend_user_ids = array_filter(
								array_map(
									function ( $user_id ) {
										return filter_var( trim( $user_id ), FILTER_VALIDATE_INT );
									},
									$form_data['group_users_to_extend_access']
								)
							);

							$new_expiration_date = strtotime( $form_data['new_expiration_date'] . ' ' . get_option( 'timezone_string' ) );

							if ( ! empty( $extend_course_ids ) && ! empty( $extend_user_ids ) && ! empty( $new_expiration_date ) && function_exists( 'learndash_course_extend_user_access' ) ) {
								foreach ( $extend_course_ids as $course_id ) {
									learndash_course_extend_user_access( $course_id, $extend_user_ids, $new_expiration_date, $post->ID );
								}
							}
						}
					}
				}
			}
			return $result;
		}

		/**
		 * Send email notification to group users
		 *
		 * @since 5.7.0
		 */
		public function ajax_send_group_users_email_notification() {
			$response = [
				'message' => __( 'The notification email(s) could not be sent. Please contact the Admin or Instructor.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Verify Nonce.
			if ( ! check_ajax_referer( 'ir_send_group_email_notifications', 'ir_nonce', false ) ) {
				wp_send_json_error( $response );
			}

			// Sanitize data.
			$group_id        = ir_filter_input( 'group_id', INPUT_POST, 'number' );
			$current_user_id = get_current_user_id();

			// Check if user is admin or instructor or group leader and also check if has access to group.
			if ( learndash_is_group_leader_user( $current_user_id ) ) {
				if ( ! current_user_can( 'manage_options' ) && ! in_array( $current_user_id, learndash_get_groups_administrator_ids( $group_id ), true ) ) {
					wp_send_json_error(
						[
							'message' => __( 'You do not have sufficient privileges to perform this action', 'wdm_instructor_role' ),
							'type'    => 'error',
						],
						403
					);
				}
			}

			$group_users = learndash_get_groups_user_ids( $group_id );

			if ( empty( $group_users ) ) {
				wp_send_json_success(
					[
						'message' => __( 'No registered users enrolled in the group', 'wdm_instructor_role' ),
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

			foreach ( $group_users as $user_id ) {
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
					'message' => __( 'Successfully completed group users email notifications', 'wdm_instructor_role' ),
					'type'    => 'success',
					'success' => $success,
					'failed'  => $failed,
				]
			);
		}

		/**
		 * Send email notification to selected user in group administration.
		 *
		 * @since 5.7.0
		 */
		public function ajax_send_group_single_user_email_notification() {
			$response = [
				'message' => __( 'The notification email(s) could not be sent. Please contact the Admin or Instructor.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Verify Nonce.
			if ( ! check_ajax_referer( 'ir_send_group_email_notifications', 'ir_nonce', false ) ) {
				wp_send_json_error( $response );
			}

			// Sanitize data.
			$selected_users = filter_input( INPUT_POST, 'selected_users', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );
			$group_id       = ir_filter_input( 'group_id', INPUT_POST, 'number' );
			$course_id      = ir_filter_input( 'course_id', INPUT_POST, 'number' );

			$current_user_id = get_current_user_id();

			// Check if user is admin or instructor or group leader and also check if has access to group.
			if ( ! current_user_can( 'manage_options' ) && ! learndash_is_group_leader_user( $current_user_id ) ) {
				wp_send_json_error(
					[
						'message' => __( 'You do not have sufficient privileges to perform this action', 'wdm_instructor_role' ),
						'type'    => 'error',
					],
					403
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

			if ( ! empty( $course_id ) ) {
				foreach ( $selected_users as $user_id ) {
					// If user not in course, continue.
					if ( ! in_array( (int) $course_id, learndash_user_get_enrolled_courses( $user_id ), true ) ) {
						wp_send_json_error(
							[
								'message' => sprintf( /* translators: Course Label */__( 'You do not have access to this %s', 'wdm_instructor_role' ), LearnDash_Custom_Label::get_label( 'course' ) ),
								'type'    => 'error',
							],
							403
						);
					}

					$user = get_user_by( 'ID', $user_id );

					if ( empty( $user ) ) {
						wp_send_json_error(
							[
								'message' => __( 'Invalid user', 'wdm_instructor_role' ),
								'type'    => 'error',
							],
							401
						);
					}

					// Store successfully email ids.
					if ( wp_mail( $user->data->user_email, $subject, $message, $headers ) ) {
						$success[] = $user_id;
					} else {
						$failed[] = $user_id;
					}
				}
			}

			if ( ! empty( $group_id ) ) {
				foreach ( $selected_users as $user_id ) {
					// If user not in course, continue.
					if ( ! learndash_is_user_in_group( $user_id, $group_id ) ) {
						continue;
					}

					$user = get_user_by( 'ID', $user_id );

					if ( empty( $user ) ) {
						wp_send_json_error(
							[
								'message' => __( 'Invalid user', 'wdm_instructor_role' ),
								'type'    => 'error',
							],
							401
						);
					}

					// Store successfully email ids.
					if ( wp_mail( $user->data->user_email, $subject, $message, $headers ) ) {
						$success[] = $user_id;
					} else {
						$failed[] = $user_id;
					}
				}
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
		 * Add localized data for groups block on the frontend dashboard.
		 *
		 * @since 5.7.0
		 *
		 * @param array $localized_data     Array of localized data.
		 * @return array
		 */
		public function add_groups_tab_localized_data( $localized_data ) {
			if ( ! array_key_exists( 'groups_block', $localized_data ) ) {
				$current_user_id                = get_current_user_id();
				$localized_data['groups_block'] = [
					'export_progress'        => [
						'nonce'      => wp_create_nonce( 'ld-group-list-view-nonce-' . $current_user_id ),
						'action'     => 'learndash_data_group_reports',
						'slug'       => 'user-courses',
						'data_nonce' => wp_create_nonce( 'learndash-data-reports-user-courses-' . $current_user_id ),
					],
					'export_results'         => [
						'nonce'      => wp_create_nonce( 'ld-group-list-view-nonce-' . $current_user_id ),
						'action'     => 'learndash_data_group_reports',
						'slug'       => 'user-quizzes',
						'data_nonce' => wp_create_nonce( 'learndash-data-reports-user-quizzes-' . $current_user_id ),
					],
					'emails_nonce'           => wp_create_nonce( 'ir_send_group_email_notifications' ),
					'start_end_date_nonce'   => wp_create_nonce( 'ir_group_date_nonce' ),
					'is_course_group_nonce'  => wp_create_nonce( 'ir_is_course_group_nonce' ),
					'manage_groups_enabled'  => learndash_get_group_leader_manage_groups(),
					'manage_courses_enabled' => learndash_get_group_leader_manage_courses(),
					'manage_users_enabled'   => learndash_get_group_leader_manage_users(),
				];
			}
			return $localized_data;
		}

		/**
		 * Get group list filters data permissions check
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_group_filters_data_permissions_check( $request ) {
			return $this->group_leader_request_permission_check( $request );
		}

		/**
		 * Get data for groups filters for the frontend dashboard
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_group_filters_data( $request ) {
			$data    = [];
			$user_id = get_current_user_id();

			// Get date filters data.
			$args = [
				'post_type'      => learndash_get_post_type_slug( 'group' ),
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'author'         => $user_id,
			];

			$group_list = new WP_Query( $args );

			$date_filter   = [];
			$date_keys     = [];
			$date_filter[] = [
				'value' => '',
				'label' => __( 'All dates', 'wdm_instructor_role' ),
			];

			foreach ( $group_list->posts as $single_course ) {
				$group_date = strtotime( $single_course->post_date );
				$key        = gmdate( 'Ym', $group_date );
				if ( ! in_array( $key, $date_keys ) ) {
					$date_filter[] = [
						'value' => gmdate( 'Ym', $group_date ),
						'label' => gmdate( 'F Y', $group_date ),
					];
					$date_keys[]   = gmdate( 'Ym', $group_date );
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

			$data = [
				'date_filter'  => $date_filter,
				'price_filter' => $price_filter,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Add user email to request response.
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WP_User          $user     User object used to create response.
		 * @param WP_REST_Request  $request  Request object.
		 *
		 * @return WP_REST_Response         Updated response object.
		 */
		public function add_user_email_to_response( $response, $user, $request ) {
			if ( false !== strpos( $request->get_route(), '/ldlms/v2/groups/' ) ) {
				if ( '/ldlms/v2/groups/' . $request['id'] . '/users' === $request->get_route() ) {
					$data = $response->get_data();
					if ( ! in_array( 'email', $data, true ) ) {
						$data['email'] = $user->user_email;
						$response->set_data( $data );
					}
				}
			}
			return $response;
		}

		/**
		 * Ajax check if group start and end date are enabled.
		 *
		 * @since 5.7.0
		 */
		public function ajax_enable_group_start_and_end_date() {
			$response = [
				'status'  => 'error',
				'message' => __( 'Some error occurred, please contact site administrator', 'wdm_instructor_role' ),
			];

			if ( ! wp_verify_nonce( ir_filter_input( 'nonce' ), 'ir_group_date_nonce' ) ) {
				echo wp_json_encode( $response );
				wp_die();
			}

			// Check if group contains courses that belong to other groups.
			$group_id          = intval( ir_filter_input( 'group_id' ) );
			$group_courses_ids = learndash_get_group_courses_list( $group_id );
			$has_same_courses  = false;

			foreach ( $group_courses_ids as $course_id ) {
				$group_ids = learndash_get_course_groups( $course_id );

				if ( count( $group_ids ) > 1 ) {
					$has_same_courses = true;
				}
			}

			if ( $has_same_courses ) {
				echo wp_json_encode(
					[
						'status'  => 'error',
						'message' => sprintf( /* translators: Group Label. */__( '%s has at least once course belonging to other groups', 'wdm_instructor_role' ), LearnDash_Custom_Label::get_label( 'group' ) ),
					]
				);
			} else {
				echo wp_json_encode(
					[
						'status'  => 'success',
						'message' => sprintf( /* translators: Group Label. */__( '%s does not have any course belonging to other groups', 'wdm_instructor_role' ), LearnDash_Custom_Label::get_label( 'group' ) ),
					]
				);
			}

			wp_die();
		}

		/**
		 * Ajax check if group start and end date are enabled.
		 *
		 * @since 5.7.0
		 */
		public function ajax_is_course_in_other_group() {
			$response = [
				'status'  => 'error',
				'message' => __( 'Some error occurred, please contact site administrator', 'wdm_instructor_role' ),
			];

			if ( ! wp_verify_nonce( ir_filter_input( 'nonce' ), 'ir_is_course_group_nonce' ) ) {
				echo wp_json_encode( $response );
				wp_die();
			}

			$group_id   = intval( ir_filter_input( 'group_id' ) );
			$course_ids = filter_input( INPUT_POST, 'course_ids', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );

			if ( empty( $group_id ) || empty( $course_ids ) ) {
				echo wp_json_encode(
					[
						'status'  => 'success',
						'message' => sprintf(
							/* translators: 1.Group Label 2.Course Label */
							__( 'Empty %1$s id or %2$s ids', 'wdm_instructor_role' ),
							LearnDash_Custom_Label::label_to_lower( 'group' ),
							LearnDash_Custom_Label::label_to_lower( 'course' ),
						),
					]
				);
				wp_die();
			}

			// Check if group contains courses that belong to other groups.
			foreach ( $course_ids as $course_id ) {
				$group_ids = learndash_get_course_groups( $course_id );

				// adding the current group to the list of groups.
				if ( ! in_array( $group_id, $group_ids, true ) ) {
					array_push( $group_ids, $group_id );
				}

				$conflicting_group = $this->find_conflicting_group_with_start_or_end_date( $group_ids );

				if ( ! empty( $conflicting_group ) ) {
					echo wp_json_encode(
						[
							'status'  => 'error',
							'message' => sprintf(
								// translators: placeholder: course, group, group, groups, group title.
								__(
									'Sorry! The %1$s can not belong to this %2$s because it already belongs to other %3$s and the %4$s "%5$s" has a start or end date.',
									'wdm_instructor_role'
								),
								learndash_get_custom_label( 'course' ),
								learndash_get_custom_label( 'group' ),
								learndash_get_custom_label( 'groups' ),
								learndash_get_custom_label( 'group' ),
								get_the_title( $conflicting_group )
							),
						]
					);

					wp_die();
				}
			}

			echo wp_json_encode(
				[
					'status'  => 'success',
					'message' => sprintf(
						/* translators: 1.Group Label 2.Course Label */
						__( '%1$s does not have any %2$s belonging to other groups', 'wdm_instructor_role' ),
						LearnDash_Custom_Label::get_label( 'group' ),
						LearnDash_Custom_Label::label_to_lower( 'course' ),
					),
				]
			);

			wp_die();
		}

		/**
		 * Check whether user has privilege to edit group.
		 *
		 * @since 5.7.0
		 *
		 * @param int     $user_id  ID of the User.
		 * @param WP_Post $group    Group Post.
		 *
		 * @return bool             True if user can edit group, false otherwise.
		 */
		public function user_can_edit_group( $user_id, $group ) {
			$can_edit_group = false;

			// If admin user then return true.
			if ( user_can( $user_id, 'manage_options' ) ) {
				$can_edit_group = true;
				// For private groups.
				if ( 'private' === $group->post_status && intval( $group->post_author ) !== $user_id ) {
					$can_edit_group = false;
				}
			} elseif ( learndash_is_group_leader_user( $user_id ) ) {
				// If instructor and group owner.
				if ( wdm_is_instructor( $user_id ) ) {
					if ( intval( $group->post_author ) === $user_id ) {
						$can_edit_group = true;
					}
				}
			}

			return $can_edit_group;
		}

		/**
		 * Check whether user has privilege to view group.
		 *
		 * @since 5.7.0
		 *
		 * @param int     $user_id  ID of the User.
		 * @param WP_Post $group    Group Post.
		 *
		 * @return bool             True if user can edit group, false otherwise.
		 */
		public function user_can_view_group( $user_id, $group ) {
			$can_view_group = false;

			// If admin user then return true.
			if ( user_can( $user_id, 'manage_options' ) ) {
				return true;
			}

			// If group leader.
			if ( learndash_is_group_leader_user( $user_id ) ) {
				// If leader of group.'
				$group_leaders = learndash_get_groups_administrator_ids( $group->ID );
				if ( in_array( $user_id, $group_leaders, true ) ) {
					$can_view_group = true;
				}

				// If instructor and group owner.
				if ( wdm_is_instructor( $user_id ) && intval( $group->post_author ) === $user_id ) {
					$can_view_group = true;
				}
			}

			return $can_view_group;
		}

		/**
		 * Check whether user has privilege to manage group users.
		 *
		 * @since 5.7.0
		 *
		 * @param int     $user_id  ID of the User.
		 * @param WP_Post $group    Group Post.
		 *
		 * @return bool             True if user can manage group users, false otherwise.
		 */
		public function user_can_manage_group_users( $user_id, $group ) {
			$can_manage_users = false;

			// If admin user then return true.
			if ( user_can( $user_id, 'manage_options' ) ) {
				return true;
			}

			// If instructor and group owner.
			if ( wdm_is_instructor( $user_id ) && intval( $group->post_author ) === $user_id ) {
				$can_manage_users = true;
			}

			return $can_manage_users;
		}

		/**
		 * Update group courses permissions check
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_group_courses_permissions_check( $request ) {
			return $this->group_leader_request_permission_check( $request );
		}

		/**
		 * Update group courses data
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_group_courses( $request ) {
			$group_id = intval( $request['id'] );
			$data     = [];
			$group    = get_post( $group_id );
			$user_id  = get_current_user_id();

			// Check if valid WP_Post object.
			if ( empty( $group_id ) || ! $group instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if user has access to group.
			if ( ! $this->user_can_edit_group( $user_id, $group ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			$parameters = $request->get_body_params();

			// If empty get all params.
			if ( empty( $parameters ) ) {
				$parameters = $request->get_params();
			}

			// Get the course(s) ids to be updated.
			$course_ids = [];

			if ( array_key_exists( 'course_ids', $parameters ) && ! empty( $parameters['course_ids'] ) ) {
				if ( is_array( $parameters['course_ids'] ) ) {
					$course_ids = $parameters['course_ids'];
				} else {
					$course_ids = explode( ',', $parameters['course_ids'] );
				}
			}

			$course_ids = array_map( 'absint', $course_ids );

			// Remove courses from the group.
			$original_course_ids = learndash_group_enrolled_courses( $group_id );
			foreach ( $original_course_ids as $course_id ) {
				if ( empty( $group_id ) ) {
					continue;
				}

				$course_post = get_post( $course_id );
				if ( ( ! $course_post ) || ( ! is_a( $course_post, 'WP_Post' ) ) || ( learndash_get_post_type_slug( 'course' ) !== $course_post->post_type ) ) {
					continue;
				}

				ld_update_course_group_access( $course_id, $group_id, true );
			}

			if ( ! empty( $course_ids ) ) {
				// Update group course list.
				foreach ( $course_ids as $course_id ) {
					if ( empty( $group_id ) ) {
						continue;
					}

					$data_item = new stdClass();

					$course_post = get_post( $course_id );
					if ( ( ! $course_post ) || ( ! is_a( $course_post, 'WP_Post' ) ) || ( learndash_get_post_type_slug( 'course' ) !== $course_post->post_type ) ) {
						$data_item->course_id = $course_id;
						$data_item->status    = 'failed';
						$data_item->code      = 'ir_rest_invalid_id';
						$data_item->message   = sprintf(
							// translators: placeholder: Course.
							esc_html_x(
								'Invalid %s ID.',
								'placeholder: Course',
								'wdm_instructor_role'
							),
							LearnDash_Custom_Label::get_label( 'course' )
						);
						$data[] = $data_item;

						continue;
					}

					$ret = ld_update_course_group_access( $course_id, $group_id, false );
					if ( true === $ret ) {
						$data_item->course_id = $course_id;
						$data_item->status    = 'success';
						$data_item->code      = 'ir_rest_enroll_success';
						$data_item->message   = sprintf(
							// translators: placeholder: Course, Group.
							esc_html_x(
								'%1$s enrolled in %2$s success.',
								'placeholder: Course, Group',
								'wdm_instructor_role'
							),
							LearnDash_Custom_Label::get_label( 'course' ),
							LearnDash_Custom_Label::get_label( 'group' )
						);
					} else {
						$data_item->course_id = $course_id;
						$data_item->status    = 'failed';
						$data_item->code      = 'ir_rest_enroll_failed';
						$data_item->message   = sprintf(
							// translators: placeholder: Course, Group.
							esc_html_x(
								'%1$s already enrolled in %2$s.',
								'placeholder: Course, Group',
								'wdm_instructor_role'
							),
							LearnDash_Custom_Label::get_label( 'course' ),
							LearnDash_Custom_Label::get_label( 'group' )
						);
					}
					$data[] = $data_item;
				}
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Update group learners permissions check
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_group_learners_permissions_check( $request ) {
			return $this->group_leader_request_permission_check( $request );
		}

		/**
		 * Update group learners data
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_group_learners( $request ) {
			$group_id = intval( $request['id'] );
			$data     = [];
			$group    = get_post( $group_id );
			$user_id  = get_current_user_id();

			// Check if valid WP_Post object.
			if ( empty( $group_id ) || ! $group instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if user has access to group.
			if ( ! $this->user_can_edit_group( $user_id, $group ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			$parameters = $request->get_body_params();

			// If empty get all params.
			if ( empty( $parameters ) ) {
				$parameters = $request->get_params();
			}

			// Get the user(s) ids to be updated.
			$user_ids = [];

			if ( array_key_exists( 'user_ids', $parameters ) && ! empty( $parameters['user_ids'] ) ) {
				if ( is_array( $parameters['user_ids'] ) ) {
					$user_ids = $parameters['user_ids'];
				} else {
					$user_ids = explode( ',', $parameters['user_ids'] );
				}
			}

			$user_ids = array_map( 'absint', $user_ids );

			// Remove users from the group.
			$original_user_ids = learndash_get_groups_user_ids( $group_id );

			foreach ( $original_user_ids as $user_id ) {
				if ( empty( $user_id ) ) {
					continue;
				}

				$user = get_user_by( 'id', $user_id );
				if ( ( ! $user ) || ( ! is_a( $user, 'WP_User' ) ) ) {
					continue;
				}

				ld_update_group_access( $user_id, $group_id, true );
			}

			// Update group users.
			if ( ! empty( $user_ids ) ) {
				foreach ( $user_ids as $user_id ) {
					if ( empty( $user_id ) ) {
						continue;
					}

					$data_item = new stdClass();

					$user = get_user_by( 'id', $user_id );
					if ( ( ! $user ) || ( ! is_a( $user, 'WP_User' ) ) ) {
						$data_item->user_id = $user_id;
						$data_item->status  = 'failed';
						$data_item->code    = 'rest_user_invalid_id';
						$data_item->message = esc_html__( 'Invalid User ID.', 'wdm_instructor_role' );
						$data[]             = $data_item;

						continue;
					}

					$ret = ld_update_group_access( $user_id, $group_id, false );
					if ( true === $ret ) {
						$data_item->user_id = $user_id;
						$data_item->status  = 'success';
						$data_item->code    = 'ir_rest_enroll_success';
						$data_item->message = sprintf(
							// translators: placeholder: Group.
							esc_html_x(
								'User enrolled in %s success.',
								'placeholder: Group',
								'wdm_instructor_role'
							),
							LearnDash_Custom_Label::get_label( 'group' )
						);
					} else {
						$data_item->user_id = $user_id;
						$data_item->status  = 'failed';
						$data_item->code    = 'ir_rest_enroll_failed';
						$data_item->message = sprintf(
							// translators: placeholder: Group.
							esc_html_x(
								'User already enrolled in %s.',
								'placeholder: Group',
								'wdm_instructor_role'
							),
							LearnDash_Custom_Label::get_label( 'group' )
						);
					}
					$data[] = $data_item;
				}
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Get group leaders permissions check
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_group_leaders_permissions_check( $request ) {
			return $this->group_leader_request_permission_check( $request );
		}

		/**
		 * Get group learners data
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_group_leaders( $request ) {
			$group_id = intval( $request['id'] );
			$data     = [];
			$group    = get_post( $group_id );
			$user_id  = get_current_user_id();

			// Check if valid WP_Post object.
			if ( empty( $group_id ) || ! $group instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if user has access to group.
			if ( ! $this->user_can_edit_group( $user_id, $group ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			$group_leaders = learndash_get_groups_administrator_ids( $group_id );

			$user_query = new WP_User_Query(
				[
					'include' => empty( $group_leaders ) ? [ 0 ] : $group_leaders,
				]
			);

			foreach ( $user_query->results as $user ) {
				if ( is_a( $user, 'WP_User' ) ) {
					$data[] = [
						'id'       => $user->ID,
						'name'     => $user->display_name,
						'image'    => get_avatar_url( $user->ID ),
						'email'    => $user->user_email,
						'username' => $user->user_login,
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
		 * Update group leaders permissions check
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_group_leaders_permissions_check( $request ) {
			return $this->group_leader_request_permission_check( $request );
		}

		/**
		 * Update group leaders data
		 *
		 * @since 5.7.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function update_group_leaders( $request ) {
			$group_id = intval( $request['id'] );
			$data     = [];
			$group    = get_post( $group_id );
			$user_id  = get_current_user_id();

			// Check if valid WP_Post object.
			if ( empty( $group_id ) || ! $group instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// Check if user has access to group.
			if ( ! $this->user_can_edit_group( $user_id, $group ) ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			$parameters = $request->get_body_params();

			// If empty get all params.
			if ( empty( $parameters ) ) {
				$parameters = $request->get_params();
			}

			// Get the leader(s) ids to be updated.
			$leader_ids = [];

			if ( array_key_exists( 'user_ids', $parameters ) && ! empty( $parameters['user_ids'] ) ) {
				if ( is_array( $parameters['user_ids'] ) ) {
					$leader_ids = $parameters['user_ids'];
				} else {
					$leader_ids = explode( ',', $parameters['user_ids'] );
				}
			}

			$leader_ids = array_map( 'absint', $leader_ids );

			// Remove users from the group.
			$original_leader_ids = learndash_get_groups_administrator_ids( $group_id );

			foreach ( $original_leader_ids as $user_id ) {
				if ( empty( $user_id ) ) {
					continue;
				}

				$user = get_user_by( 'id', $user_id );
				if ( ( ! $user ) || ( ! is_a( $user, 'WP_User' ) ) ) {
					continue;
				}

				ld_update_leader_group_access( $user_id, $group_id, true );
			}

			// Update group leader.
			foreach ( $leader_ids as $user_id ) {
				if ( empty( $user_id ) ) {
					continue;
				}

				$data_item = new stdClass();

				$user = get_user_by( 'id', $user_id );
				if ( ( ! $user ) || ( ! is_a( $user, 'WP_User' ) ) ) {
					$data_item->user_id = $user_id;
					$data_item->status  = 'failed';
					$data_item->code    = 'rest_user_invalid_id';
					$data_item->message = esc_html__( 'Invalid User ID.', 'wdm_instructor_role' );
					$data[]             = $data_item;

					continue;
				}

				$ret = ld_update_leader_group_access( $user_id, $group_id, false );
				if ( true === $ret ) {
					$data_item->user_id = $user_id;
					$data_item->status  = 'success';
					$data_item->code    = 'ir_rest_enroll_success';
					$data_item->message = sprintf(
						// translators: placeholder: Group.
						esc_html_x(
							'Leader enrolled in %s success.',
							'placeholder: Group',
							'wdm_instructor_role'
						),
						LearnDash_Custom_Label::get_label( 'group' )
					);
				} else {
					$data_item->user_id = $user_id;
					$data_item->status  = 'failed';
					$data_item->code    = 'ir_rest_enroll_failed';
					$data_item->message = sprintf(
						// translators: placeholder: Group.
						esc_html_x(
							'Leader already enrolled in %s.',
							'placeholder: Group',
							'wdm_instructor_role'
						),
						LearnDash_Custom_Label::get_label( 'group' )
					);
				}
				$data[] = $data_item;
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Find conflicting groups with start or end date.
		 *
		 * @since 5.7.0
		 *
		 * @param array $group_ids  List of group Ids.
		 * @return mixed            Null if no or single group found, ID of the conflicting group otherwise.
		 */
		public function find_conflicting_group_with_start_or_end_date( $group_ids ) {
			if ( count( $group_ids ) <= 1 ) {
				return null;
			}

			foreach ( $group_ids as $group_id ) {
				$start_date = learndash_get_setting( $group_id, 'group_start_date' );
				$end_date   = learndash_get_setting( $group_id, 'group_start_date' );

				if ( $start_date || $end_date ) {
					return $group_id;
				}
			}

			return null;
		}
	}
}
