<?php
/**
 * Instructor Reports Module
 *
 * @since 3.5.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

namespace InstructorRole\Modules\Classes;

use InstructorRole\Modules\Classes\Instructor_Role_Overview;
use InstructorRole\Modules\Classes\Instructor_Role_Dashboard;
use InstructorRole\Modules\Api\Instructor_Role_Course_Reports_Api_Handler;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Reports' ) ) {
	/**
	 * Class Instructor Role Reports Module
	 */
	class Instructor_Role_Reports {
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
		 * Add instructor reports menu page
		 */
		public function add_report_menu_page() {
			$course_label = __( 'Course', 'wdm_instructor_role' );
			if ( class_exists( '\LearnDash_Custom_Label' ) ) {
				$course_label = \LearnDash_Custom_Label::get_label( 'course' );
			}
			add_submenu_page(
				'learndash-lms',
				/* translators: Course */
				sprintf( __( '%s Reports', 'wdm_instructor_role' ), $course_label ),
				sprintf( __( '%s Reports', 'wdm_instructor_role' ), $course_label ),
				'instructor_reports',
				'instructor_lms_reports',
				[ $this, 'show_reports_page' ]
			);
		}

		/**
		 * Callback function to show instructor reports page.
		 */
		public function show_reports_page() {
			// Fetch all the courses owned by the instructor.
			$course_list = get_posts(
				'post_type=sfwd-courses&posts_per_page=-1&fields=ids'
			);

			// Fetch all shared courses.
			$shared_courses = ir_get_instructor_shared_course_list();

			$course_list = array_merge( $course_list, $shared_courses );

			// Preselect first course.
			if ( ! empty( $course_list ) ) {
				$selected_course_id = $course_list[0];
			}

			// Check if course id already set in form.
			if ( array_key_exists( 'course_id', $_POST ) && ! empty( $_POST['course_id'] ) ) {
				$selected_course_id = $_POST['course_id'];
			}

			/**
			 * Filter report template path for others to extend
			 *
			 * @since 3.3.0
			 */
			$template = apply_filters(
				'ir_filter_course_report_template_path',
				INSTRUCTOR_ROLE_ABSPATH . 'modules/templates/course-report/ir-course-reports-page.template.php'
			);

			include $template;
		}

		/**
		 * Display course reports for the selected course
		 *
		 * @param int  $course_id   ID of the course to display reports for.
		 * @param bool $echo        Whether to echo the output or return as a string. Default true.
		 *
		 * @return string           If echo is false, then returns generated course reports.
		 *
		 * @since 3.3.0
		 */
		public function display_course_reports( $course_id, $echo = true ) {
			// Verify if course id set.
			if ( empty( $course_id ) ) {
				return false;
			}

			// Get title of the course.
			$course_title = get_the_title( $course_id );

			// Get list of all students who have access to the course enrolled either through.
			// group or directly in the course.
			$course_access_users = ir_get_users_with_course_access( $course_id, [ 'direct', 'group' ] );

			// Get total count of all the users.
			$total_users = count( $course_access_users );

			$course_statistics = [
				'not_started' => 0,
				'in_progress' => 0,
				'completed'   => 0,
				'total'       => $total_users,
			];

			$default_pagination_count = count( $course_access_users );

			// Check if pagination updated.
			if ( array_key_exists( 'wdm_pagination_select', $_POST ) && ! empty( $_POST['wdm_pagination_select'] ) ) {
				$pagination_count = filter_input( INPUT_POST, 'wdm_pagination_select', FILTER_SANITIZE_NUMBER_INT );
			}

			// Set pagination count.
			$pagination_count = empty( $pagination_count ) ? $default_pagination_count : $pagination_count;

			$course_users_paged       = [];
			$course_access_user_count = count( $course_access_users );
			$page_count               = 0;

			$course_progress_details = [
				'not_started' => 0,
				'in_progress' => 0,
				'completed'   => 0,
			];

			if ( function_exists( 'learndash_course_get_steps_count' ) ) {
				$total_course_steps = learndash_course_get_steps_count( $course_id );
			} else {
				$total_course_steps = learndash_get_course_steps_count( $course_id );
			}

			// Check if course user list not empty.
			if ( ! empty( $course_access_users ) ) {
				$count = 0;
				foreach ( $course_access_users as $user_id ) {
					$user_completed_steps = learndash_course_get_completed_steps( $user_id, $course_id );

					$course_users_paged[ $page_count ][] = $user_id;

					// Check if page count full or last user in list.
					if ( 0 == ( ( $count + 1 ) % $pagination_count ) || ( $count + 1 ) == $course_access_user_count ) {
						$course_users_paged[ $page_count ] = implode( ',', $course_users_paged[ $page_count ] );
						++$page_count;
					}

					if ( empty( $user_completed_steps ) ) {
						// If no completed steps, means not started yet.
						++$course_statistics['not_started'];
					} elseif ( $user_completed_steps > 0 && $user_completed_steps < $total_course_steps ) {
						// If not zero but less than total course steps, means in progress.
						++$course_statistics['in_progress'];
					} elseif ( $user_completed_steps === $total_course_steps ) {
						// If completed steps equal to total course steps, means course completed.
						++$course_statistics['completed'];
					}

					++$count;
				}

				// Get course progress details in percentages.
				$course_progress_details = $this->calculate_progress_details( $course_statistics );

				/**
				 * Filter course reports main path for others to extend
				 *
				 * @since 3.3.0
				 */
				$template = apply_filters(
					'ir_filter_course_reports_main_template_path',
					INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/course-report/ir-course-reports-main.template.php'
				);

				// Check return type.
				if ( ! $echo ) {
					ob_start();
					include $template;
					$html_data = ob_get_clean();
					$data      = [
						'html'            => $html_data,
						'not_started_per' => $course_progress_details['not_started'],
						'in_progress_per' => $course_progress_details['in_progress'],
						'completed_per'   => $course_progress_details['completed'],
						'graph_heading'   => __( 'Status of', 'wdm_instructor_role' ) . ' "' . wp_specialchars_decode( $course_title ) . '"',
						'paged_users'     => ( $course_users_paged ),
						'paged_index'     => 0,
					];

					return json_encode( $data );
				} else {
					include $template;
				}
			}

			// Enqueue necessary scripts.
			$this->enqueue_course_report_scripts( $course_id, $course_users_paged, $course_progress_details );

			if ( $echo && empty( $course_access_users ) ) {
				echo $this->get_no_reports_display_section();
			} else {
				return json_encode( false );
			}
		}

		/**
		 * Get calculated course progress details
		 *
		 * @param array $course_stats
		 * @return array
		 *
		 * @since 3.3.0
		 */
		public function calculate_progress_details( $course_stats ) {
			$course_progress = [
				'not_started' => 0,
				'in_progress' => 0,
				'completed'   => 0,
			];

			if ( $course_stats['total'] > 0 ) {
				$course_progress['not_started'] = round( ( $course_stats['not_started'] / $course_stats['total'] ) * 100, 2 );
				$course_progress['in_progress'] = round( ( $course_stats['in_progress'] / $course_stats['total'] ) * 100, 2 );
				$course_progress['completed']   = 100 - ( $course_progress['not_started'] + $course_progress['in_progress'] );
			}

			return $course_progress;
		}

		/**
		 * Get course progress for a specific user
		 *
		 * @param int $user_id      ID of the user.
		 * @param int $course_id    ID of the course.
		 * @param int $total_course_steps Number of steps in the course.
		 *
		 * @return array            Progress of the user for the given course.
		 *
		 * @since 3.3.0
		 */
		public function get_user_course_progress( $user_id, $course_id, $total_course_steps = 0 ) {
			$course_progress = [
				'total_steps'         => $total_course_steps,
				'completed_steps'     => 0,
				'percentage'          => 0,
				'course_completed_on' => 0,
			];
			if ( empty( $user_id ) || empty( $course_id ) || 0 === $total_course_steps ) {
				return $course_progress;
			}
			$course_completed_date = '-';

			$completed_steps = learndash_course_get_completed_steps( $user_id, $course_id );

			if ( $completed_steps > 0 ) {
				$course_completed_meta = '';
				if ( $completed_steps === $total_course_steps ) {
					$course_completed_meta = get_user_meta( $user_id, 'course_completed_' . $course_id, true );
					if ( function_exists( 'learndash_adjust_date_time_display' ) ) {
						$course_completed_date = learndash_adjust_date_time_display( $course_completed_meta );
					} else {
						$course_completed_date = gmdate( 'F j, Y H:i:s', $course_completed_meta );
					}
				}

				$course_progress = [
					'total_steps'                => $total_course_steps,
					'completed_steps'            => $completed_steps,
					'percentage'                 => round( ( $completed_steps / $total_course_steps ) * 100 ),
					'course_completed_on'        => $course_completed_date,
					'course_completed_timestamp' => $course_completed_meta,
				];
			}
			return $course_progress;
		}

		/**
		 * Calculate total course steps for a course
		 *
		 * @param array $total_steps    List of total steps in the course.
		 * @param int   $course_id        ID of the course.
		 *
		 * @return mixed                Returns total steps if set, empty if no course id and
		 *                              calculates total steps if not set.
		 *
		 * @since 3.3.0
		 */
		public function calculate_total_course_steps( $total_steps, $course_id ) {
			// If steps set the return.
			if ( ! empty( $total_steps ) ) {
				return $total_steps;
			}

			$total_steps = 0;

			// Check if course id set.
			if ( empty( $course_id ) ) {
				return $total_steps;
			}

			$total_quizzes = learndash_get_global_quiz_list( $course_id );
			$total_lessons = learndash_get_lesson_list( $course_id );

			if ( ! empty( $total_quizzes ) ) {
				$total_steps = 1;
			}
			if ( ! empty( $total_lessons ) ) {
				$total_steps += count( $total_lessons );
			}

			return $total_steps;
		}

		/**
		 * Enqueue course reports scripts
		 *
		 * @param int   $course_id                    ID of the course
		 * @param array $course_users_paged         List of users for the course, pagewise
		 * @param array $course_progress_details    List of course progress details
		 *
		 * @since 3.3.0
		 */
		public function enqueue_course_report_scripts( $course_id, $course_users_paged, $course_progress_details ) {
			global $screen;

			// Get title of the course.
			$course_title = get_the_title( $course_id );

			// Get data to be displayed for no reports displayed.
			$no_reports_html = $this->get_no_reports_display_section();

			$localized_data = [
				'not_started_text'    => __( 'Not Started', 'wdm_instructor_role' ),
				'in_progress_text'    => __( 'In Progress', 'wdm_instructor_role' ),
				'completed_text'      => __( 'Completed', 'wdm_instructor_role' ),
				'not_started_per'     => $course_progress_details['not_started'],
				'in_progress_per'     => $course_progress_details['in_progress'],
				'completed_per'       => $course_progress_details['completed'],
				'graph_heading'       => __( 'Status of', 'wdm_instructor_role' ) . ' "' . wp_specialchars_decode( $course_title ) . '"',
				'piece_title'         => __( 'Users', 'wdm_instructor_role' ),
				'course_title'        => $course_title,
				'admin_ajax_path'     => admin_url( 'admin-ajax.php' ),
				'paged_users'         => $course_users_paged,
				'paged_index'         => 0,
				'success_msg'         => __( ' Mail sent successfully!!! ', 'wdm_instructor_role' ),
				'failure_msg'         => __( ' Mail not sent!!! ', 'wdm_instructor_role' ),
				'no_reports_html'     => $no_reports_html,
				'colors'              => $this->get_chart_colors(),
				'empty_email_body'    => __( 'Please enter email body', 'wdm_instructor_role' ),
				'empty_email_subject' => __( 'Please enter email subject', 'wdm_instructor_role' ),
				'i18n'                => [
					'decimal'        => '',
					'emptyTable'     => __( 'No data available in table', 'wdm_instructor_role' ),
					'info'           => __( 'Showing _START_ to _END_ of _TOTAL_ entries', 'wdm_instructor_role' ),
					'infoEmpty'      => __( 'Showing 0 to 0 of 0 entries', 'wdm_instructor_role' ),
					'infoFiltered'   => __( '(filtered from _MAX_ total entries)', 'wdm_instructor_role' ),
					'infoPostFix'    => '',
					'thousands'      => ',',
					'lengthMenu'     => __( 'Show _MENU_ entries', 'wdm_instructor_role' ),
					'loadingRecords' => __( 'Loading...', 'wdm_instructor_role' ),
					'processing'     => '',
					'search'         => __( 'Search :', 'wdm_instructor_role' ),
					'zeroRecords'    => __( 'No matching records found', 'wdm_instructor_role' ),
					'paginate'       => [
						'first'    => __( 'First', 'wdm_instructor_role' ),
						'last'     => __( 'Last', 'wdm_instructor_role' ),
						'next'     => __( '', 'wdm_instructor_role' ),
						'previous' => __( '', 'wdm_instructor_role' ),
					],
					'aria'           => [
						'sortAscending'  => __( ': activate to sort column ascending', 'wdm_instructor_role' ),
						'sortDescending' => __( ': activate to sort column descending', 'wdm_instructor_role' ),
					],
				],
			];

			$path = 'js/min/reports.min.js';
			// if ( defined( 'SCRIPT_DEBUG' ) && true == SCRIPT_DEBUG ) {
				$path = 'js/reports.js';
			// }

			wp_enqueue_script( 'ir-lib-apex-charts', plugins_url( 'js/dashboard/apexcharts.min.js', __DIR__ ) );
			wp_enqueue_script( 'wdm_new_reports', plugin_dir_url( __DIR__ ) . $path, [ 'jquery' ] );
			wp_enqueue_script( 'wdm_popup.js', plugin_dir_url( __DIR__ ) . 'js/wdm_popup.js', [ 'jquery' ], '0.0.1' );
			wp_enqueue_script(
				'ir-datatables-script',
				'https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js'
			);

			wp_enqueue_style(
				'ir-datatable-styles',
				'https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css'
			);

			// Custom css.
			wp_enqueue_style( 'wdmCss', plugin_dir_url( __DIR__ ) . 'css/style.css' );
			wp_enqueue_style( 'wdmPopEmailCss', plugin_dir_url( __DIR__ ) . 'css/wdm_popup_ins_mail.css' /*, array('editor-style.css')*/ );

			wp_localize_script( 'wdm_new_reports', 'wdm_reports_obj', $localized_data );
		}

		/**
		 * Display Course User Records.
		 *
		 * @param array $course_users_paged     List of users for the course, pagewise.
		 * @param int   $course_id              ID of the course.
		 * @param bool  $echo                   Whether to echo the output or return as a string. Default true
		 *
		 * @since 3.3.0
		 */
		public function display_course_report_user_records( $course_users_paged, $course_id, $echo = true ) {
			// Check if course users and ID set.
			if ( empty( $course_users_paged ) || empty( $course_id ) ) {
				return;
			}

			// Get first page users, so page 0.
			$user_ids = $course_users_paged;
			if ( is_array( $course_users_paged ) ) {
				$user_ids = $course_users_paged[0];
			}

			$user_ids = explode( ',', $user_ids );
			$user_ids = array_filter( $user_ids );

			// If no users then return.
			if ( empty( $user_ids ) ) {
				return;
			}

			$course_users = [];

			$all_user_rows = '';

			if ( function_exists( 'learndash_course_get_steps_count' ) ) {
				$total_course_steps = learndash_course_get_steps_count( $course_id );
			} else {
				$total_course_steps = learndash_get_course_steps_count( $course_id );
			}

			foreach ( $user_ids as $user_id ) {
				$user_row_data              = '';
				$user_meta                  = get_userdata( $user_id );
				$course_progress            = $this->get_user_course_progress( $user_id, $course_id, $total_course_steps );
				$completed_percentage       = empty( $course_progress['percentage'] ) ? 0 : $course_progress['percentage'];
				$completed_steps            = empty( $course_progress['completed_steps'] ) ? 0 : $course_progress['completed_steps'];
				$total_steps                = $total_course_steps;
				$course_completed_on        = empty( $course_progress['course_completed_on'] ) ? '-' : $course_progress['course_completed_on'];
				$course_completed_timestamp = empty( $course_progress['course_completed_timestamp'] ) ? '' : $course_progress['course_completed_timestamp'];

				ob_start();
				include apply_filters(
					'ir_filter_course_reports_user_record_single_template_path',
					INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/course-report/ir-course-reports-user-record-single.template.php'
				);
				$user_row_data  = ob_get_clean();
				$all_user_rows .= $user_row_data;
			}

			// Whether to echo or return.
			if ( $echo ) {
				echo $all_user_rows;
			} else {
				return $all_user_rows;
			}
		}

		/**
		 * [send_mail_from_report_page sends mail to multiple users of that group].
		 */
		public function send_mail_from_report_page() {
			if ( empty( $_POST['submit_instructor_email'] ) || empty( $_POST['learndash_instructor_message'] ) || empty( $_POST['learndash_instructor_subject'] ) || ! isset( $_POST['course_id'] ) ) {
				return;
			}

			$course_id = $_POST['course_id'];

			$users = $this->get_course_access_users( $course_id );
			if ( empty( $users ) ) {
				return;
			}

			// Check if the "from" input field is filled out.
			$message = stripslashes( $_POST['learndash_instructor_message'] );
			$subject = stripslashes( $_POST['learndash_instructor_subject'] );
			$subject = strip_tags( $subject );
			// message lines should not exceed 70 characters (PHP rule), so wrap it.
			$message = wordwrap( $message, 70 );
			// send mail.
			$headers = [ 'Content-Type: text/html; charset=UTF-8' ];

			foreach ( $users as $user_id ) {
				$user = get_user_by( 'ID', $user_id );
				wp_mail( $user->data->user_email, $subject, $message, $headers );
			}
			// To redirect to the page after sending email.
			$server_req_uri = $_SERVER['REQUEST_URI'];
			$url            = parse_url( $server_req_uri, PHP_URL_QUERY );
			parse_str( $url, $url_params );
			$url_params_string = '?page=' . $url_params['page'] . '&course_id=' . $_POST['course_id'];
			$url               = explode( '?', $server_req_uri );
			wp_redirect( $url[0] . $url_params_string );
			exit;
		}

		/**
		 * Get all users ids of a course.
		 *
		 * @param int $post_id post id of a course
		 *
		 * @return array array of users id
		 *
		 * @since 3.3.0
		 */
		public function get_course_access_users( $post_id ) {
			$course_access_users = [];

			if ( empty( $post_id ) ) {
				return $course_access_users;
			}

			$course_progress_data = [];
			$courses              = [ get_post( $post_id ) ];

			// Get users who have access to course.
			// 1. Direct course access
			// 2. Access to course from group

			$ir_users = ir_get_users_with_course_access( $post_id, [ 'direct', 'group' ] );

			if ( ! empty( $ir_users ) ) {
				$course_progress_data = $this->get_user_course_progress_data( $ir_users, $courses );
			}
			foreach ( $course_progress_data as $value ) {
				array_push( $course_access_users, $value['user_id'] );
			}

			return $course_access_users;
		}

		/**
		 * Get course progress data for a user.
		 *
		 * @param [array] $users                 [users]
		 * @param [array] $courses               [courses]
		 */
		public function get_user_course_progress_data( $users, $courses ) {
			$course_progress_data = [];

			// Check if users.
			if ( empty( $users ) ) {
				return $course_progress_data;
			}

			foreach ( $users as $user_id ) {
				// Get user course progress details.
				$usermeta = get_user_meta( $user_id, '_sfwd-course_progress', true );

				if ( ! empty( $usermeta ) ) {
					$usermeta = maybe_unserialize( $usermeta );
				}

				if ( ! empty( $courses[0] ) ) {
					foreach ( $courses as $course ) {
						$course_id = $course->ID;

						if ( empty( $course->post_title ) || ! sfwd_lms_has_access( $course_id, $user_id ) ) {
							continue;
						}

						$course_steps = [
							'completed' => '',
							'total'     => '',
						];
						$course_steps = empty( $usermeta[ $course_id ] ) ? $course_steps : $usermeta[ $course_id ];

						$course_completed_meta = get_user_meta( $user_id, 'course_completed_' . $course->ID, true );

						if ( function_exists( 'learndash_adjust_date_time_display' ) ) {
							$course_completed_date = empty( $course_completed_meta ) ? '-' : learndash_adjust_date_time_display( $course_completed_meta );
						} else {
							$course_completed_date = empty( $course_completed_meta ) ? '-' : gmdate( 'F j, Y H:i:s', $course_completed_meta );
						}

						$row  = [
							'user_id'             => $user_id,
							'name'                => get_the_author_meta( 'display_name', $user_id ),
							'email'               => get_the_author_meta( 'user_email', $user_id ),
							'course_id'           => $course_id,
							'course_title'        => $course->post_title,
							'total_steps'         => $course_steps['total'],
							'completed_steps'     => $course_steps['completed'],
							'course_completed'    => ( ! empty( $course_steps['total'] ) && $course_steps['completed'] >= $course_steps['total'] ) ? __( 'Yes', 'wdm_instructor_role' ) : __( 'No', 'wdm_instructor_role' ),
							'course_completed_on' => $course_completed_date,
						];
						$loop = 1;
						if ( ! empty( $course_steps['lessons'] ) ) {
							foreach ( $course_steps['lessons'] as $lesson_id => $completed ) {
								if ( ! empty( $completed ) ) {
									if ( empty( $lessons[ $lesson_id ] ) ) {
										$lesson = $lessons[ $lesson_id ] = get_post( $lesson_id );
									} else {
										$lesson = $lessons[ $lesson_id ];
									}

									$row[ 'lesson_completed_' . $loop ] = $lesson->post_title;
									++$loop;
								}
							}
						}

						$course_progress_data[] = $row;
					}
				}
			}
			return $course_progress_data;
		}

		/**
		 * Fetch course reports via ajax.
		 *
		 * @return array
		 *
		 * @since 3.3.0
		 */
		public function ajax_fetch_course_reports() {
			// Check if course id set.
			if ( ! array_key_exists( 'course_id', $_POST ) || empty( $_POST['course_id'] ) ) {
				wp_die();
			}

			// Get Course ID.
			$course_id = filter_input( INPUT_POST, 'course_id', FILTER_SANITIZE_NUMBER_INT );

			// Generate Course Report.
			echo $this->display_course_reports( $course_id, false );

			wp_die();
		}

		/**
		 * Fetch Course Report pagination pages
		 *
		 * @return string
		 */
		public function ajax_fetch_course_report_page() {
			if ( ! array_key_exists( 'action', $_POST ) || 'wdm_get_user_html' != $_POST['action'] ) {
				wp_die();
			}

			$users     = sanitize_text_field( wp_unslash( $_POST['users'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.NonceVerification.Missing -- Check it later.
			$course_id = filter_input( INPUT_POST, 'current_post', FILTER_SANITIZE_NUMBER_INT );

			echo $this->display_course_report_user_records( $users, $course_id, false );

			wp_die();
		}

		/**
		 * Add footer section to display messages
		 */
		public function display_message_section() {
			echo '<div id="blanket" style="display:none;"></div>';
		}

		/**
		 * Sends email to an individual user.
		 *
		 * @return [boolean] [true or false]
		 */
		public function send_mail_to_individual_user() {
			$email = '';
			if ( isset( $_POST['email'] ) ) {
				$email = $_POST['email'];
			}

			if ( $email ) {
				if ( isset( $_POST['subject'] ) ) {
					$subject = strip_tags( $_POST['subject'] );
				}

				if ( isset( $_POST['body'] ) ) {
					$body = $_POST['body'];
				}
				$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
				if ( wp_mail( $email, $subject, $body, $headers ) ) {
					echo 1;
				} else {
					// On successful message sent.
					echo 0;
				}
			}

			die();
		}

		/**
		 * Export course reports to a CSV file.
		 */
		public function export_course_report_to_csv() {
			if ( empty( $_REQUEST['post_id_report'] ) || empty( $_POST['post_id_report'] ) ) {
				return;
			}

			$field_names = [
				__( 'User ID', 'wdm_instructor_role' ),
				__( 'Name', 'wdm_instructor_role' ),
				__( 'Email', 'wdm_instructor_role' ),
				/* translators: Course label */
				sprintf( __( '%s ID', 'wdm_instructor_role' ), \LearnDash_Custom_Label::get_label( 'course' ) ),
				/* translators: Course label */
				sprintf( __( '%s Title', 'wdm_instructor_role' ), \LearnDash_Custom_Label::get_label( 'course' ) ),
				__( 'Completed Steps', 'wdm_instructor_role' ),
				/* translators: Course label */
				sprintf( __( '%s Completed', 'wdm_instructor_role' ), \LearnDash_Custom_Label::get_label( 'course' ) ),
				__( 'Completion Date', 'wdm_instructor_role' ),
			];

			$content = self::generate_csv_export_data( $_POST['post_id_report'], $field_names );

			/**
			 * Allow 3rd party plugins to update CSV report header titles.
			 *
			 * @since 3.2.2
			 */
			$field_names = apply_filters( 'ir_filter_csv_field_titles', $field_names, $content );

			// file name to export.
			$file_name = sanitize_file_name( get_the_title( $_POST['post_id_report'] ) . '-' . date( 'Y-m-d' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.DateTime.RestrictedFunctions.date_date -- Check it later.

			if ( empty( $content ) ) {
				$content[] = [ 'status' => __( 'No attempts', 'wdm_instructor_role' ) ];
			}

			/**
			 * Include parseCSV to write csv file.
			 */
			require_once LEARNDASH_LMS_LIBRARY_DIR . '/parsecsv.lib.php';

			$csv                  = new \lmsParseCSV();
			$csv->file            = $file_name;
			$csv->output_filename = $file_name;
			/**
			 * Filters csv object.
			 *
			 * @since 4.0
			 *
			 * @param \lmsParseCSV $csv CSV object.
			 * @param string       $context The context of the csv object.
			 */
			$csv = apply_filters( 'ir_filter_csv_object', $csv, 'ir_course_reports' );

			/**
			 * Filters the content will print onto the exported CSV
			 *
			 * @since 4.0
			 *
			 * @param void|array|mixed $content CSV content.
			 */
			$content = apply_filters( 'ir_filter_course_export_data', $content );

			$csv->output( $file_name . '.csv', $content, array_keys( reset( $content ) ) );
			die();
		}

		/**
		 * Generate CSV export data for course report
		 *
		 * @param int   $course_id        ID of the course whose report to export.
		 * @param array $field_names    Names of the cSV title fields.
		 * @return mixed                On success, CSV data to be exported, else false.
		 */
		public static function generate_csv_export_data( $course_id, &$field_names ) {
			// Check if empty course_id.
			if ( empty( $course_id ) ) {
				return false;
			}

			// Get current user.
			$current_user = wp_get_current_user();

			// Check if user has capability to export instructor reports.
			if ( empty( $current_user ) || ! current_user_can( 'instructor_reports' ) ) {
				return false;
			}

			// Get course details.
			$course = get_post( $course_id );

			// Check if valid course.
			if ( empty( $course ) ) {
				return false;
			}

			// Get Course members.
			$course_members = ir_get_users_with_course_access( $course_id, [ 'direct', 'group' ] );

			// Return if no members in the course.
			if ( empty( $course_members ) ) {
				return false;
			}

			$course_progress_data = [];

			$lessons = [];

			if ( function_exists( 'learndash_course_get_steps_count' ) ) {
				$total_steps = learndash_course_get_steps_count( $course_id );
			} else {
				$total_steps = learndash_get_course_steps_count( $course_id );
			}

			foreach ( $course_members as $member_id ) {
				$completed_steps = learndash_course_get_completed_steps( $member_id, $course->ID );

				$member_data = get_userdata( $member_id );

				$course_completed_meta = get_user_meta( $member_id, 'course_completed_' . $course->ID, true );

				if ( function_exists( 'learndash_adjust_date_time_display' ) ) {
					$course_completed_date = empty( $course_completed_meta ) ? '-' : learndash_adjust_date_time_display( $course_completed_meta );
				} else {
					$course_completed_date = empty( $course_completed_meta ) ? '-' : gmdate( 'F j, Y H:i:s', $course_completed_meta );
				}

				$course_completed_status = __( 'NO', 'wdm_instructor_role' );
				if ( ! empty( $total_steps ) && $completed_steps >= $total_steps ) {
					$course_completed_status = __( 'YES', 'wdm_instructor_role' );
				}

				$row = [
					'user_id'             => $member_id,
					'name'                => $member_data->display_name,
					'email'               => $member_data->user_email,
					'course_id'           => $course->ID,
					'course_title'        => $course->post_title,
					'completed_steps'     => $completed_steps . '/' . $total_steps,
					'course_completed'    => $course_completed_status,
					'course_completed_on' => $course_completed_date,
				];

				$temp_i = 1;

				if ( ! empty( $course_progress['lessons'] ) ) {
					foreach ( $course_progress['lessons'] as $lesson_id => $completed ) {
						$field_name = '';
						if ( ! empty( $completed ) ) {
							if ( empty( $lessons[ $lesson_id ] ) ) {
								$lesson = $lessons[ $lesson_id ] = get_post( $lesson_id );
							} else {
								$lesson = $lessons[ $lesson_id ];
							}
							// translators: Lesson and Current lesson count.
							$field_name = sprintf( __( '%1$s Completed %2$d', 'wdm_instructor_role' ), \LearnDash_Custom_Label::get_label( 'lesson' ), $temp_i );
							if ( ! in_array( $field_name, $field_names ) ) {
								array_push( $field_names, $field_name );
							}
							$row[ 'lesson_completed_' . $temp_i ] = $lesson->post_title;
							++$temp_i;
						}
					}
				}

				$course_progress_data[] = $row;
			}

			/**
			 * Filter generated CSV report data
			 *
			 * @since 3.5.3
			 *
			 * @param array $course_progress_data   Generated CSV export data.
			 * @param int $course_id                ID of the course.
			 */
			return apply_filters( 'ir_filter_generate_csv_export_data', $course_progress_data, $course_id );
		}

		/**
		 * Generate CSV export data for learner report
		 *
		 * @param int   $learner        user object whose report to export.
		 * @param array $field_names    Names of the cSV title fields.
		 * @return mixed                On success, CSV data to be exported, else false.
		 */
		public static function generate_csv_export_data_for_learner( $learner, &$field_names ) {
			// Check if empty course_id.
			if ( empty( $learner ) ) {
				return false;
			}

			// Get current user.
			$current_user = wp_get_current_user();

			// Check if user has capability to export instructor reports.
			if ( empty( $current_user ) || ! current_user_can( 'instructor_reports' ) ) {
				return false;
			}

			// Get user's progress data.
			$progress_data = maybe_unserialize( get_user_meta( $learner->ID, '_sfwd-course_progress', true ) );

			if ( empty( $progress_data ) ) {
				return false;
			}

			$course_progress_data = [];

			foreach ( $progress_data as $course_id => $progress ) {
				$course_enrolled_on_meta = Instructor_Role_Course_Reports_Api_Handler::get_instance()->get_user_course_enrollment_info( $learner->ID, $course_id );
				$course_enrolled_on      = ! empty( $course_enrolled_on_meta ) ? $course_enrolled_on_meta['time_stamp'] : '';
				$course_enrollment_date  = empty( $course_enrolled_on ) ? '-' : date_i18n( 'F-j-y (g:i a)', $course_enrolled_on );
				$course_completed_meta   = get_user_meta( $learner->ID, 'course_completed_' . $course_id, true );
				$course_completed_date   = empty( $course_completed_meta ) ? '-' : date_i18n( 'F-j-y (g:i a)', $course_completed_meta );

				$row                    = [
					'course_id'             => $course_id,
					'course_title'          => get_the_title( $course_id ),
					'user_id'               => $learner->ID,
					'name'                  => $learner->display_name,
					'email'                 => $learner->user_email,
					'status'                => ucfirst( str_replace( '_', ' ', $progress['status'] ) ),
					'completed_steps'       => $progress['completed'] . '/' . $progress['total'],
					'completion_percentage' => floatval( number_format( 100 * $progress['completed'] / $progress['total'], 2, '.', '' ) ),
					'course_enrolled_on'    => $course_enrollment_date,
					'course_completed_on'   => $course_completed_date,
				];
				$course_progress_data[] = $row;
			}

			/**
			 * Filter generated CSV report data
			 *
			 * @since 3.5.3
			 *
			 * @param array $course_progress_data   Generated CSV export data.
			 * @param int $learner                ID of the course.
			 */
			return apply_filters( 'ir_filter_generate_learner_csv_export_data', $course_progress_data, $course_id );
		}

		/**
		 * Update the sender email address for emails sent by instructor.
		 *
		 * @param string $from_email  Sender email to be updated.
		 *
		 * @return string $from_email  Updated sender email address.
		 *
		 * @since 3.0.0
		 */
		public function update_sender_email_id( $from_email ) {
			$instructor_email = false;
			// Check if individual email action.
			if ( array_key_exists( 'action', $_POST ) && 'wdm_send_mail_to_individual_user' === $_POST['action'] ) {
				$instructor_email = true;
			}

			// Check if course email action.
			if ( array_key_exists( 'submit_instructor_email', $_POST ) && ! empty( $_POST['submit_instructor_email'] ) ) {
				$instructor_email = true;
			}

			/**
			 * Filter whether to set from email to instructor email address.
			 *
			 * @since 3.5.6
			 *
			 * @param bool $instructor_email    True if from email to be set as instructor email, false otherwise.
			 * @param string $from_email        From email address for this email.
			 */
			$instructor_email = apply_filters( 'ir_filter_email_from_email_check', $instructor_email, $from_email );

			if ( $instructor_email && function_exists( 'wp_get_current_user' ) && ! is_super_admin() && wdm_is_instructor() ) {
				$current_user = wp_get_current_user();
				if ( $current_user ) {
					return esc_html( $current_user->user_email );
				}
			}
			return $from_email;
		}

		/**
		 * Update the sender name for emails sent by instructor.
		 *
		 * @since 3.0.0
		 *
		 * @param string $from_name Sender name to be updated.
		 *
		 * @return string Updated sender name.
		 */
		public function update_sender_name( $from_name ) {
			$instructor_email = false;
			// Check if individual email action.
			if ( array_key_exists( 'action', $_POST ) && 'wdm_send_mail_to_individual_user' === $_POST['action'] ) {
				$instructor_email = true;
			}

			// Check if course email action.
			if ( array_key_exists( 'submit_instructor_email', $_POST ) && ! empty( $_POST['submit_instructor_email'] ) ) {
				$instructor_email = true;
			}

			/**
			 * Filter whether to set from name to instructor name.
			 *
			 * @since 3.5.6
			 *
			 * @param bool $instructor_email    True if from name to be set to instructor name, false otherwise.
			 * @param string $from_name         From name for this email.
			 */
			$instructor_email = apply_filters( 'ir_filter_email_from_name_check', $instructor_email, $from_name );

			if ( $instructor_email && function_exists( 'wp_get_current_user' ) && ! is_super_admin() && wdm_is_instructor() ) {
				$current_user = wp_get_current_user();
				if ( $current_user ) {
					if ( ! empty( $current_user->user_firstname ) ) {
						return esc_html( $current_user->user_firstname );
					}
					return esc_html( $current_user->user_login );
				}
			}

			return $from_name;
		}

		/**
		 * Making compatible with WP SMTP plugin.
		 *
		 * @since 3.0.0
		 *
		 * @param object $phpmailer Email configuration.
		 */
		public function configure_wp_smtp_settings( $phpmailer ) {
			global $wsOptions;

			// Check if global exists.
			if ( empty( $wsOptions ) ) {
				return;
			}

			if ( function_exists( 'wp_get_current_user' ) && ! is_super_admin() && wdm_is_instructor() ) {
				$current_user = wp_get_current_user();
				if ( $current_user ) {
					if ( ! is_email( $wsOptions['from'] ) || empty( $wsOptions['host'] ) || ! isset( $wsOptions ) ) {
						return;
					}
					$phpmailer->Mailer = 'smtp';
					$phpmailer->From   = esc_html( $current_user->user_email );
					$wsOptions['from'] = esc_html( $current_user->user_email );
					if ( ! empty( $current_user->user_firstname ) ) {
						$phpmailer->FromName = esc_html( $current_user->user_firstname );
					}
					$phpmailer->FromName = esc_html( $current_user->user_login ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Lib parameter.

					// Return-Path.
					$phpmailer->Sender = esc_html( $current_user->user_email ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Lib parameter.

					// Reply-To.
					$phpmailer->AddReplyTo( $phpmailer->From, $phpmailer->FromName ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Lib parameter.

					$phpmailer->Host = $wsOptions['host']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase, WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- Lib parameter and legacy code.

					// cspell:disable-next-line .
					$phpmailer->SMTPSecure = $wsOptions['smtpsecure']; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase, WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- Lib parameter and legacy code.
					$phpmailer->Port       = $wsOptions['port'];

					// cspell:disable-next-line .
					$phpmailer->SMTPAuth = ( $wsOptions['smtpauth'] == 'yes' ) ? true : false; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase, WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- Lib parameter and legacy code.
					if ( $phpmailer->SMTPAuth ) {
						$phpmailer->Username = $wsOptions['username'];
						$phpmailer->Password = $wsOptions['password'];
					}
				}
			}
		}

		/**
		 * Get HTML data to be displayed when no course reports are found
		 *
		 * @return string       HTML in string.
		 *
		 * @since 3.3.0
		 */
		public function get_no_reports_display_section() {
			$icon_path = apply_filters(
				'ir_filter_no_reports_display_icon_path',
				INSTRUCTOR_ROLE_ABSPATH . 'modules/media/no-reports.svg'
			);

			ob_start();
			include apply_filters(
				'ir_filter_no_reports_display_template_path',
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/course-report/no-reports.php'
			);
			$no_reports_html = ob_get_clean();

			return $no_reports_html;
		}

		/**
		 * Get reports chart colors
		 *
		 * @since 4.3.0
		 */
		public function get_chart_colors() {
			$def           = [ '#6940c1', '#7270ea', '#a5a4ff' ];
			$preset_colors = $this->get_preset_colors();

			$chart_colors = $def;
			if ( isset( $preset_colors['primary'] ) ) {
				$chart_colors = [
					$preset_colors['primary'],
					Instructor_Role_Overview::hex2rgba( $preset_colors['primary'], 0.3 ),
					Instructor_Role_Overview::hex2rgba( $preset_colors['primary'], 0.6 ),
				];
			}

			return $chart_colors;
		}

		/**
		 * Get preset colors for the active layout
		 *
		 * @since 4.3.0
		 *
		 * @return array
		 */
		public function get_preset_colors() {
			$layout        = ir_get_settings( 'ir_dashboard_layout' );
			$preset_colors = [];
			if ( 'layout-2' === $layout ) {
				$ir_color_preset = ir_get_settings( 'ir_color_preset_2' );
				$preset_colors   = Instructor_Role_Dashboard::get_preset_colors( $ir_color_preset, 2 );
			} else {
				$ir_color_preset = ir_get_settings( 'ir_color_preset' );
				$preset_colors   = Instructor_Role_Dashboard::get_preset_colors( $ir_color_preset );
			}
			return $preset_colors;
		}
	}
}
