<?php
/**
 * Reports Module
 *
 * @since 4.0
 * @package Ld_Group_Registration
 * @subpackage Ld_Group_Registration/modules/classes
 */

namespace LdGroupRegistration\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Ld_Group_Registration_Reports' ) ) {
	/**
	 * Class LD Group Registration Reports
	 */
	class Ld_Group_Registration_Reports {
		/**
		 * Class Instance
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 4.3.3
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Get reports data for a group
		 *
		 * @param int $group_id     ID of the group.
		 */
		public static function get_group_report( $group_id, $tab_id ) {
			// Register and enqueue scripts.
			wp_enqueue_style( 'dashicons' );

			if ( ! wp_style_is( 'wdm_datatable_css', 'enqueued' ) ) {
				wp_enqueue_style(
					'wdm_datatable_css',
					plugins_url(
						'css/datatables.min.css',
						__DIR__
					),
					[],
					LD_GROUP_REGISTRATION_VERSION
				);
			}

			wp_enqueue_script(
				'wdm-ldgr-group-report-js',
				plugins_url( 'js/wdm-ldgr-group-report.js', __DIR__ ),
				[ 'jquery-ui-core', 'wdm_datatable_js' ],
				LD_GROUP_REGISTRATION_VERSION
			);

			// check if any course associated with group, any user enrolled.
			$group_courses = learndash_group_enrolled_courses( $group_id );

			/**
			 * Filter the list of courses in the group on groups dashboard.
			 *
			 * @param array $group_courses  List of courses in the group.
			 * @param int $group_id         ID of the group.
			 *
			 * @since 4.1.5
			 */
			$group_courses = apply_filters( 'ldgr_filter_group_course_list', $group_courses, $group_id );

			// check if the course has a certificate associated with it.
			$rewards        = false;
			$certificate_id = empty( $group_courses ) ? 0 : learndash_get_setting( $group_courses[0], 'certificate' );
			if ( ! empty( $certificate_id ) && 0 !== $certificate_id ) {
				$rewards = true;
			}

			/**
			 * Filter group reports localized data
			 *
			 * @since 4.2.3
			 *
			 * @param array $localized_array    Group reports localized data.
			 */
			$localized_array = apply_filters(
				'ldgr_filter_group_reports_localized_data',
				[
					'ajax_url'            => admin_url( 'admin-ajax.php' ),
					'group_id'            => $group_id,
					'course_not_selected' => sprintf(
						// translators: Course label.
						__( 'Please select a %s', 'wdm_ld_group' ),
						\LearnDash_Custom_Label::get_label( 'course' )
					),
					'columns'             => [
						[
							'className'      => 'details-control',
							'orderable'      => false,
							'data'           => null,
							'defaultContent' => '<span class="dashicons dashicons-arrow-down-alt2"></span>',
							'width'          => '10%',
						],
						[
							'data'      => 'name',
							'orderable' => false,
							'className' => 'dt-body-left name',
						],
						[
							'data'      => 'email_id',
							'orderable' => false,
							'className' => 'dt-body-left email',
						],
						[
							'data'      => 'course_progress',
							'orderable' => false,
							'className' => 'dt-body-center dt-head-left course-progress',
						],
						// { "data": "last_name", "orderable": false, "className": "dt-body-left", width: '160px' },
						// { "data": "course_status", "orderable": false, "className": "dt-body-center dt-head-center", width: '160px' }
					],
					'rewards'             => $rewards,
					'length_menu'         => [ [ 10, 25, 50, -1 ], [ 10, 25, 50, 'All' ] ],
				]
			);

			if ( $rewards ) {
				$localized_array['columns'][3]['width'] = '23%';
				$localized_array['columns'][]           = [
					'data'      => 'reward',
					'orderable' => false,
					'className' => 'dt-body-center dt-head-center reward',
				];
			}

			wp_localize_script(
				'wdm-ldgr-group-report-js',
				'ajax_object',
				$localized_array
			);

			wp_enqueue_style(
				'wdm_ldgr_report_css',
				plugins_url(
					'css/wdm_ldgr_report_css.css',
					__DIR__
				),
				[],
				LD_GROUP_REGISTRATION_VERSION
			);

			/**
			 * Replaced `include` with `ldgr_get_template`
			 *
			 * @since 4.1.2
			 */
			ldgr_get_template(
				WDM_LDGR_PLUGIN_DIR . '/modules/templates/ldgr-group-report.template.php',
				[
					'group_id' => $group_id,
					'tab_id'   => $tab_id,
				]
			);
		}

		/**
		 * Create Report Table
		 */
		public function create_report_table_callback() {
			if ( is_user_logged_in() ) {
				if ( learndash_is_group_leader_user( get_current_user_id() ) || learndash_is_group_leader_user( get_current_user_id() ) || current_user_can( 'manage_options' ) ) {
					$admin_group_ids = learndash_get_administrators_group_ids( get_current_user_id() );
					$course_id       = filter_input( INPUT_POST, 'course_id', FILTER_SANITIZE_NUMBER_INT );
					$group_id        = filter_input( INPUT_POST, 'group_id', FILTER_SANITIZE_NUMBER_INT );

					// check if the course has a certificate associated with it.
					$rewards        = false;
					$certificate_id = learndash_get_setting( $course_id, 'certificate' );
					if ( ! empty( $certificate_id ) && 0 !== $certificate_id ) {
						$rewards = true;
					}
					$rewards = apply_filters( 'wdm_ldgr_report_show_rewards_column', $rewards, $course_id, $group_id );
					if ( ! in_array( $group_id, $admin_group_ids ) ) {
						echo json_encode(
							[
								'group_id' => $group_id,
								'error'    => __(
									'You are not the owner of this group',
									'wdm_ld_group'
								),
							]
						);
						die();
					}

					/**
					 * Replaced `include` with `ldgr_get_template`
					 *
					 * @since 4.1.2
					 */
					$table = ldgr_get_template(
						WDM_LDGR_PLUGIN_DIR . '/modules/templates/ldgr-group-report-table.template.php',
						[
							'course_id' => $course_id,
							'group_id'  => $group_id,
							'rewards'   => $rewards,
						],
						1
					);

					$table = preg_replace( "/\r|\n/", '', $table );

					/**
					 * Filter group report generated data.
					 *
					 * @since 4.1.2
					 *
					 * @var array   $report_data    Group report data.
					 * @var int     $course_id      ID of the Learndash Course.
					 * @var int     $group_id       ID of the Learndash Group.
					 */
					$report_data = apply_filters(
						'ldgr_filter_group_report_data',
						[
							'table'   => $table,
							'rewards' => $rewards,
						],
						$course_id,
						$group_id
					);

					echo json_encode( $report_data );
					die();
				} else {
					echo json_encode( [ 'error' => __( "You don't have privilege to do this action", 'wdm_ld_group' ) ] );
					die();
				}
			}
		}

		/**
		 * Display group report
		 */
		public function display_ldgr_group_report_callback() {
			// temporary.
			$empty_data_set = [
				'recordsTotal'    => 0,
				'recordsFiltered' => 0,
				'data'            => [],
			];

			// Is group and course id set?
			if ( ! isset( $_POST['course_id'] ) || ! isset( $_POST['group_id'] ) ) {
				echo json_encode( $empty_data_set );
				wp_die();
			}

			// Check if user logged in.
			if ( ! is_user_logged_in() ) {
				echo json_encode( $empty_data_set );
				wp_die();
			}

			// Check if group leader.
			if ( learndash_is_group_leader_user( get_current_user_id() ) || learndash_is_group_leader_user( get_current_user_id() ) || current_user_can( 'manage_options' ) ) {
				$admin_group_ids = learndash_get_administrators_group_ids( get_current_user_id() );
				$course_id       = filter_input( INPUT_POST, 'course_id', FILTER_SANITIZE_NUMBER_INT );
				$group_id        = filter_input( INPUT_POST, 'group_id', FILTER_SANITIZE_NUMBER_INT );
				$show_rewards    = filter_input( INPUT_POST, 'show_rewards' );
				$show_rewards    = filter_var( $show_rewards, FILTER_VALIDATE_BOOLEAN );

				// Check if user is admin of current group.
				if ( ! in_array( $group_id, $admin_group_ids ) ) {
					echo json_encode( $empty_data_set );
					wp_die();
				}

				// get the limit data parameters.
				$limit  = 10;
				$offset = 0;
				$offset = filter_input( INPUT_POST, 'start', FILTER_SANITIZE_NUMBER_INT );
				$limit  = filter_input( INPUT_POST, 'length', FILTER_SANITIZE_NUMBER_INT );

				// get the users according to limit.
				$group_users_objects = $this->get_next_group_users( $group_id, $limit, $offset );

				if ( empty( $group_users_objects ) ) {
					echo wp_json_encode( $empty_data_set );
					wp_die();
				}

				$data = [];

				foreach ( $group_users_objects['data'] as $key => $group_users_object ) {
					// Full name of the user.
					$user_id              = $group_users_object->ID;
					$userdata             = get_userdata( $user_id );
					$data[ $key ]['name'] = $userdata->first_name . ' ' . $userdata->last_name;
					// $data[$key]['last_name'] = $userdata->last_name;

					// Email.
					$data[ $key ]['email_id'] = $group_users_object->user_email;
					$course_progress_attr     = [
						'course_id' => $course_id,
						'user_id'   => $user_id,
						'array'     => true,
					];

					// Check learndash version.
					if ( version_compare( LEARNDASH_VERSION, '3.4.0', '>=' ) ) { // @phpstan-ignore-line -- False positive.
						// Get course steps.
						$total_steps     = learndash_course_get_steps_count( $course_id );
						$completed_steps = learndash_course_get_completed_steps( $user_id, $course_id );
					} else {
						// Get course progress.
						$lesson_progress = $this->get_user_lesson_progress( $user_id, $course_id );

						$total_steps     = $lesson_progress['total'];
						$completed_steps = $lesson_progress['completed'];
					}
					// Get total course steps count.
					$lessons_progress_html = sprintf(
						// translators: completed lessons, total lessons, lessons label.
						esc_html__( '%1$d / %2$d %3$s completed', 'wdm_ld_group' ),
						$completed_steps,
						$total_steps,
						__( 'steps', 'wdm_ld_group' )
					);
					$completion_percentage = 0;
					if ( ! empty( $completed_steps ) ) {
						$completion_percentage = round( ( $completed_steps / $total_steps ) * 100, 2 );
					}
					$data[ $key ]['course_progress'] = '<div class="ldgr-group-report-item"><div class="ldgr-course-progress-bar ldgr-bg-color" data-progress="' . $completion_percentage . '" style="width:' . $completion_percentage . '%"></div></div><span class="ldgr-course-lesson-progress" >' . $lessons_progress_html . '</span>';
					// Course Progress.
					// $course_progress = learndash_course_progress( $course_progress_attr );
					// $lesson_progress = $this->get_user_lesson_progress( $user_id, $course_id );
					// $lessons_label   = \LearnDash_Custom_Label::label_to_lower( 'lessons' );

					// $lessons_progress_html = sprintf(
					// translators: completed lessons, total lessons, lessons label.
					// esc_html__( '%1$d / %2$d %3$s completed', 'wdm_ld_group' ),
					// $lesson_progress['completed'],
					// $lesson_progress['total'],
					// $lessons_label
					// );
					// $data[ $key ]['course_progress'] = '<div class="ldgr-group-report-item"><div class="ldgr-course-progress-bar ldgr-bg-color" data-progress="' . $course_progress['percentage'] . '" style="width:' . $course_progress['percentage'] . '%"></div></div><span class="ldgr-course-lesson-progress" >' . $lessons_progress_html . '</span>';

					// $data[$key]['course_status'] = learndash_course_status($course_id, $user_id, false);

					// Rewards.
					if ( $show_rewards ) {
						$certificate_link = learndash_get_course_certificate_link( $course_id, $user_id );
						if ( '' == $certificate_link ) {
							$reward = '-';
						} else {
							// cspell:disable-next-line .
							$reward = '<a href="' . $certificate_link . '" class="wdm-prnt-cf button" title="' . __( 'certificate', 'wdm_ld_group' ) . '" target="_blank"></a>';
							// $reward .= do_action('wdm_ldgr_report_add_rewards', $user_id, $course_id, $group_id);
						}
						// 4.2.2 - Changed location of filter from inside the else section to outside the if block.
						/**
						 * Filter the reward details displayed for group reports.
						 *
						 * @since 4.2.2
						 *
						 * @param string $reward        HTML to be displayed for rewards.
						 * @param int $user_id          ID of the user.
						 * @param int $course_id        ID of the course.
						 * @param int $group_id         ID of the group.
						 */
						$reward                 = apply_filters( 'wdm_ldgr_report_add_rewards', $reward, $user_id, $course_id, $group_id );
						$data[ $key ]['reward'] = $reward;
					}

					// Course Report.
					$data[ $key ]['course_report'] = $this->get_detailed_course_report( $course_id, $user_id );
				}

				/**
				 * Filter group report data table
				 *
				 * @since 4.2.0
				 *
				 * @param array $final_report_data  Array of data to be used to create the final data table.
				 * @param array $data               Array of data with all student report details.
				 */
				$final_report_data = apply_filters(
					'ldgr_filter_group_report_data_table',
					[
						'recordsTotal'    => $group_users_objects['recordsTotal'],
						'recordsFiltered' => $group_users_objects['recordsTotal'],
						'data'            => $data,
					],
					$data
				);

				echo wp_json_encode( $final_report_data );
				wp_die();
			}
		}

		/**
		 * Get next group users based on limit and offset
		 *
		 * @param int $group_id     ID of the group.
		 * @param int $limit        Number of group users to fetch.
		 * @param int $offset       Offset from where to fetch.
		 * @return array
		 */
		public function get_next_group_users( $group_id, $limit, $offset ) {
			$group_users_object = [];

			if ( empty( $group_id ) ) {
				return $group_users_object;
			}

			$user_query_args = [
				// 'exclude'     =>  $group_leader_user_ids,
				'number'      => intval( $limit ),
				'offset'      => intval( $offset ),
				'orderby'     => 'display_name',
				'order'       => 'ASC',
				'count_total' => true,
				'fields'      => [ 'ID', 'user_email', 'display_name' ],
				'meta_query'  => [
					[
						'key'     => 'learndash_group_users_' . intval( $group_id ),
						'compare' => 'EXISTS',
					],
				],
			];

			$user_query = new \WP_User_Query( $user_query_args );

			if ( isset( $user_query->results ) ) {
				$group_users_objects['data']         = $user_query->results;
				$group_users_objects['recordsTotal'] = $user_query->total_users;
				// $group_users_objects['recordsFiltered'] = $user_query->total_users;
			}

			return $group_users_objects;
		}

		/**
		 * Get detailed course report
		 *
		 * @param int $course_id    ID of the course.
		 * @param int $user_id      ID of the user.
		 *
		 * @return string           Detailed course report in HTML.
		 */
		public function get_detailed_course_report( $course_id, $user_id ) {
			$course_report = '';
			if ( empty( $course_id ) || empty( $user_id ) ) {
				return $course_report;
			}

			// Get course details.
			$course      = get_post( $course_id );
			$course_link = get_permalink( $course_id );
			$progress    = learndash_course_progress(
				[
					'user_id'   => $user_id,
					'course_id' => $course_id,
					'array'     => true,
				]
			);
			// cspell:disable-next-line .
			$status = ( 100 == $progress['percentage'] ) ? 'completed' : 'notcompleted';

			// Get quiz details.
			$usermeta           = get_user_meta( $user_id, '_sfwd-quizzes', true );
			$quiz_attempts_meta = empty( $usermeta ) ? false : $usermeta;
			$quiz_attempts      = [];

			if ( ! empty( $quiz_attempts_meta ) ) {
				foreach ( $quiz_attempts_meta as $quiz_attempt ) {
					if ( ! isset( $quiz_attempt['course'] ) ) {
						$quiz_attempt['course'] = learndash_get_course_id( $quiz_attempt['quiz'] );
					}
					$quiz_course_id = intval( $quiz_attempt['course'] );

					if ( intval( $course_id ) !== $quiz_course_id ) {
						continue;
					}

					$c                          = learndash_certificate_details( $quiz_attempt['quiz'], $user_id );
					$quiz_attempt['post']       = get_post( $quiz_attempt['quiz'] );
					$quiz_attempt['percentage'] = ! empty( $quiz_attempt['percentage'] ) ? $quiz_attempt['percentage'] : ( ! empty( $quiz_attempt['count'] ) ? $quiz_attempt['score'] * 100 / $quiz_attempt['count'] : 0 );

					if ( ! empty( $c['certificateLink'] ) && ( ( isset( $quiz_attempt['percentage'] ) && $quiz_attempt['percentage'] >= $c['certificate_threshold'] * 100 ) ) ) {
						$quiz_attempt['certificate'] = $c;
					}

					$quiz_attempts[ $course_id ][] = $quiz_attempt;
				}
			}

			// Generate the course report.
			/**
			 * Replaced `include` with `ldgr_get_template`.
			 *
			 * @since 4.1.2
			 */
			$course_report = ldgr_get_template(
				WDM_LDGR_PLUGIN_DIR . '/modules/templates/ldgr-detailed-course-report.template.php',
				[
					'course_id'     => $course_id,
					'user_id'       => $user_id,
					'quiz_attempts' => $quiz_attempts,
					'progress'      => $progress,
				],
				1
			);

			return $course_report;
		}

		/**
		 * Get list of completed and total lessons for a course for a user.
		 *
		 * @since 4.2.0
		 *
		 * @param int $user_id      ID of the user.
		 * @param int $course_id    ID of the learndash course.
		 *
		 * @return array            List of completed and total lessons for the course by the user.
		 */
		public function get_user_lesson_progress( $user_id, $course_id ) {
			$lesson_progress = [
				'completed' => 0,
				'total'     => 0,
			];

			if ( empty( $user_id ) || empty( $course_id ) ) {
				return $lesson_progress;
			}

			$lesson_progress['total'] = count( learndash_get_lesson_list( $course_id ) );

			$user_progress = get_user_meta( $user_id, '_sfwd-course_progress', 1 );
			if ( ! empty( $user_progress ) && array_key_exists( $course_id, $user_progress ) ) {
				foreach ( $user_progress[ $course_id ]['lessons'] as $is_lesson_complete ) {
					if ( $is_lesson_complete ) {
						++$lesson_progress['completed'];
					}
				}
			}

			/**
			 * Filter the user lesson progress for a course for group course reports.
			 *
			 * @since 4.2.0
			 *
			 * @param int $user_id      ID of the user.
			 * @param int $course_id    ID of the learndash course.
			 */
			return apply_filters( 'ldgr_filter_get_user_lesson_progress', $lesson_progress, $user_id, $course_id );
		}

		/**
		 * Filter group reports for instructor role users.
		 *
		 * @param WP_Object $query
		 *
		 * @return $query
		 *
		 * @since 4.3.6
		 */
		public function filter_group_reports_for_instructor( $query ) {
			// Check if ajax query
			if ( wp_doing_ajax() ) {
				// Check if user is logged in and instructor
				if ( ! is_user_logged_in() || ! function_exists( 'wdm_is_instructor' ) || ! wdm_is_instructor() ) {
					return $query;
				}

				// Check if group report request
				if ( ! empty( $_POST ) && array_key_exists( 'action', $_POST ) && 'wdm_display_ldgr_group_report' == $_POST['action'] ) {
					$query->set( 'author__in', [] );
				}
			}
			return $query;
		}
	}
}
