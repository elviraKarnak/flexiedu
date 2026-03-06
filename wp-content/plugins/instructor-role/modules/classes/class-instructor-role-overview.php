<?php
/**
 * Instructor Overview Module
 *
 * @since 3.5.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

namespace InstructorRole\Modules\Classes;

use InstructorRole\Modules\Classes\Instructor_Role_Dashboard;
use LearnDash\Core\Utilities\Cast;

defined( 'ABSPATH' ) || exit;

if ( ( class_exists( 'LearnDash_Settings_Page' ) ) && ( ! class_exists( 'Instructor_Role_Overview' ) ) ) {
	/**
	 * Class Instructor Role Overview Module
	 */
	class Instructor_Role_Overview extends \LearnDash_Settings_Page {
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

		/**
		 * Course Count
		 *
		 * @var int
		 * @since 3.1.0
		 */
		public $course_count = null;

		/**
		 * Student Count
		 *
		 * @var int
		 * @since 3.1.0
		 */
		public $student_count = null;

		/**
		 * Addon Details
		 *
		 * Details about woocommerce and edd data count.
		 *
		 * @var array
		 * @since 3.1.0
		 */
		public $addon_info = null;

		/**
		 * Instructor earnings
		 *
		 * @var array
		 * @since 3.1.0
		 */
		public $earnings = null;

		/**
		 * Page links for various instructor pages
		 *
		 * @var array
		 * @since 3.1.0
		 */
		public $page_links = null;

		/**
		 * Courses label for LearnDash courses
		 *
		 * @var string
		 * @since 3.4.0
		 */
		public $courses_label = '';

		public function __construct() {
			$this->plugin_slug           = INSTRUCTOR_ROLE_TXT_DOMAIN;
			$this->parent_menu_page_url  = 'admin.php?page=ir_instructor_overview';
			$this->menu_page_capability  = 'edit_courses';
			$this->settings_page_id      = 'ir_instructor_overview';
			$this->settings_page_title   = esc_html__( 'Instructor Overview', 'learndash' );
			$this->settings_tab_title    = esc_html__( 'Overview', 'learndash' );
			$this->settings_tab_priority = 0;
			$this->page_links            = [
				'courses' => add_query_arg( [ 'post_type' => 'sfwd-courses' ], admin_url( 'edit.php' ) ),
				'woo'     => add_query_arg( [ 'post_type' => 'product' ], admin_url( 'edit.php' ) ),
			];

			// Commented since `LearnDash_Custom_Label` not present when instance is created.
			$this->courses_label = __( 'Courses', 'wdm_instructor_role' );
			if ( class_exists( '\LearnDash_Custom_Label' ) ) {
				$this->courses_label = \LearnDash_Custom_Label::get_label( 'courses' );
			}

			// Get all the data.
			$this->irSetInstructorOverviewData();

			add_filter( 'learndash_submenu', [ $this, 'irAddSubmenuItem' ], 200 );
			add_filter( 'learndash_header_data', [ $this, 'admin_header' ], 40, 3 );
			add_action( 'admin_enqueue_scripts', [ $this, 'irOverviewEnqueueScripts' ] );
			add_action( 'wp_ajax_ir-update-course-chart', [ $this, 'ajaxUpdateCourseChart' ] );
			add_filter( 'ir_filter_earnings_localized_data', [ $this, 'addEarningsLocalizedData' ], 10, 1 );
			add_filter( 'ir_filter_chart_localized_data', [ $this, 'addChartLocalizedData' ], 10, 1 );

			parent::__construct();
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
		 * Control visibility of submenu items
		 *
		 * @since 3.1.0
		 *
		 * @param array $submenu Submenu item to check.
		 * @return array $submenu
		 */
		public function irAddSubmenuItem( $submenu ) {
			if ( ! isset( $submenu[ $this->settings_page_id ] ) ) {
				$submenu_save = $submenu;
				$submenu      = [];

				$submenu[ $this->settings_page_id ] = [
					'name'  => $this->settings_tab_title,
					'cap'   => $this->menu_page_capability,
					'link'  => $this->parent_menu_page_url,
					'class' => 'submenu-ldlms-overview',
				];

				$submenu = array_merge( $submenu, $submenu_save );
			}

			return $submenu;
		}

		/**
		 * Filter the admin header data. We don't want to show the header panel on the Overview page.
		 *
		 * @since 3.0
		 * @param array  $header_data Array of header data used by the Header Panel React app.
		 * @param string $menu_key The menu key being displayed.
		 * @param array  $menu_items Array of menu/tab items.
		 *
		 * @return array $header_data.
		 */
		public function admin_header( $header_data = [], $menu_key = '', $menu_items = [] ) {
			// Clear out $header_data if we are showing our page.
			if ( $menu_key === $this->parent_menu_page_url ) {
				$header_data = [];
			}

			return $header_data;
		}

		/**
		 * Filter for page title wrapper.
		 *
		 * @since 3.0.0
		 */
		public function get_admin_page_title() {
			return apply_filters( 'learndash_admin_page_title', '<h1>' . $this->settings_page_title . '</h1>' );
		}

		/**
		 * Custom display function for page content.
		 *
		 * @since 3.1.0
		 */
		public function show_settings_page() {
			$course_list            = ir_get_instructor_complete_course_list();
			$ajax_icon              = plugins_url( 'css/images/loading.svg', __DIR__ );
			$layout                 = ir_get_settings( 'ir_dashboard_layout' );
			$ir_overview_settings   = ir_get_settings( 'ir_overview_settings' );
			$user_id                = get_current_user_id();
			$is_commission_disabled = get_user_meta( $user_id, 'ir_commission_disabled', true );

			if ( 'layout-2' === $layout ) {
				ir_get_template(
					INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/overview/ir-instructor-overview-layout-2.template.php',
					[
						'course_list'            => $course_list,
						'ajax_icon'              => $ajax_icon,
						'instance'               => $this,
						'ir_overview_settings'   => $ir_overview_settings,
						'ir_commission_disabled' => Cast::to_bool( $is_commission_disabled ),
					]
				);
			} else {
				ir_get_template(
					INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/overview/ir-instructor-overview.template.php',
					[
						'course_list'          => $course_list,
						'ajax_icon'            => $ajax_icon,
						'instance'             => $this,
						'ir_overview_settings' => $ir_overview_settings,
					]
				);
			}
		}

		/**
		 * Set instructor overview data
		 */
		private function irSetInstructorOverviewData() {
			$this->course_count  = 0;
			$this->student_count = 0;

			$user_id = get_current_user_id();

			// Refresh shared courses.
			ir_refresh_shared_course_details( $user_id );

			// Final instructor course list.
			$course_list = ir_get_instructor_complete_course_list( $user_id );

			// No courses yet...
			if ( ! empty( $course_list ) && array_sum( $course_list ) > 0 ) {
				$this->course_count = count( $course_list );

				// Fetch the list of students in the courses.
				$all_students = [];
				foreach ( $course_list as $course_id ) {
					// Check if trashed course.
					if ( 'trash' == get_post_status( $course_id ) ) {
						--$this->course_count;
					}

					$students_list = ir_get_users_with_course_access( $course_id, [ 'direct', 'group' ] );

					if ( empty( $students_list ) ) {
						continue;
					}
					$all_students = array_merge( $all_students, $students_list );
				}

				$unique_students_list = array_unique( $all_students );
				$this->student_count  = count( $unique_students_list );
			}

			$this->setAddonDetails( $user_id );
		}

		/**
		 * Set addon details
		 *
		 * @param int $user_id      User ID
		 */
		private function setAddonDetails( $user_id = 0 ) {
			if ( empty( $user_id ) ) {
				$user_id = get_current_user_id();
			}

			// Check if woocommerce activated.
			if ( class_exists( 'WooCommerce' ) && wdmCheckWooDependency() ) {
				$products                     = new \WP_Query(
					[
						'post_type' => 'product',
						'author'    => $user_id,
					]
				);
				$this->addon_info['products'] = $products->found_posts;
			}

			/**
			 * Filter to modify addon details on overview page.
			 *
			 * @since 3.5.6
			 *
			 * @param array $addon_info     Information on addon details for overview page.
			 */
			$this->addon_info = apply_filters( 'ir_dashboard_addon_info', $this->addon_info );
		}

		/**
		 * Fetch course data for the chart
		 *
		 * @param int $course_id    ID of the course
		 * @return array            Array of course chart data.
		 */
		protected function fetchCourseChartData( $course_id ) {
			$enrolled_user_list = ir_get_users_with_course_access( $course_id, [ 'direct', 'group' ] );

			$chart_data = [
				'title'       => get_the_title( $course_id ),
				'not_started' => 0,
				'in_progress' => 0,
				'completed'   => 0,
				'total'       => count( $enrolled_user_list ),
			];

			$chart_data = apply_filters( 'ir_filter_chart_localized_data', $chart_data );

			if ( function_exists( 'learndash_course_get_steps_count' ) ) {
				$total_course_steps = learndash_course_get_steps_count( $course_id );
			} else {
				$total_course_steps = learndash_get_course_steps_count( $course_id );
			}

			foreach ( $enrolled_user_list as $user_id ) {
				$user_completed_steps = learndash_course_get_completed_steps( $user_id, $course_id );

				if ( empty( $user_completed_steps ) ) {
					// If no completed steps, means not started yet.
					++$chart_data['not_started'];
				} elseif ( $user_completed_steps > 0 && $user_completed_steps < $total_course_steps ) {
					// If not zero but less than total course steps, means in progress.
					++$chart_data['in_progress'];
				} elseif ( $user_completed_steps === $total_course_steps ) {
					// If completed steps equal to total course steps, means course completed.
					++$chart_data['completed'];
				}
			}

			return $chart_data;
		}

		/**
		 * Enqueue Overview scripts
		 */
		public function irOverviewEnqueueScripts() {
			global $current_screen;

			// Check if is instructor.
			if ( ! wdm_is_instructor() ) {
				return;
			}

			// Check if overview page.
			if ( 'admin_page_' . $this->settings_page_id != $current_screen->id ) {
				return;
			}

			// Get instructor complete course list.
			$course_list = ir_get_instructor_complete_course_list();

			wp_enqueue_style(
				'woo-icon-fonts',
				plugins_url( 'css/woo-fonts/style.css', __DIR__ )
			);

			if ( is_rtl() ) {
				$path = plugins_url( 'css/ir-instructor-overview-styles-rtl.css', __DIR__ );
			} else {
				$path = plugins_url( 'css/ir-instructor-overview-styles.css', __DIR__ );
			}
			wp_enqueue_style(
				'ir-instructor-overview-styles',
				$path
			);

			$ir_overview_settings = $ir_overview_settings = ir_get_settings( 'ir_overview_settings' );
			if ( in_array( 'on', array_values( $ir_overview_settings ) ) ) {
				wp_enqueue_script( 'ir-lib-apex-charts', plugins_url( 'js/dashboard/apexcharts.min.js', __DIR__ ) );
			}

			wp_enqueue_script(
				'ir-instructor-overview-script',
				plugins_url( 'js/ir-instructor-overview-script.js', __DIR__ ),
				[ 'ir-lib-apex-charts' ]
			);

			wp_enqueue_script(
				'ir-datatables-script',
				'https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js'
			);

			$earnings = self::calculateInstructorEarnings();

			/**
			 * Filter earnings chart data displayed on the instructor overview page.
			 *
			 * @since 3.5.0
			 */
			$earnings = apply_filters( 'ir_filter_earnings_localized_data', $earnings );

			$course_id = '';

			if ( ! empty( $course_list ) ) {
				$course_id = array_shift( $course_list );
			}

			$chart_data = $this->fetchCourseChartData( $course_id );

			$chart_data = apply_filters( 'ir_filter_chart_localized_data', $chart_data );

			$currency_code = function_exists( 'get_woocommerce_currency' )
						? get_woocommerce_currency()
						: learndash_get_currency_code();

			$localized_data = [
				'chart_data'      => $chart_data,
				'course_id'       => $course_id,
				'ajax_url'        => admin_url( 'admin-ajax.php' ),
				'earnings'        => $earnings,
				'is_rtl'          => true,
				'empty_reports'   => ir_get_template( INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/course-report/no-reports.php', [], 1 ),
				'i18n'            => [
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
						'next'     => __( 'Next', 'wdm_instructor_role' ),
						'previous' => __( 'Previous', 'wdm_instructor_role' ),
					],
					'aria'           => [
						'sortAscending'  => __( ': activate to sort column ascending', 'wdm_instructor_role' ),
						'sortDescending' => __( ': activate to sort column descending', 'wdm_instructor_role' ),
					],
				],
				'currency_symbol' => learndash_get_currency_symbol( $currency_code ),
			];

			wp_localize_script( 'ir-instructor-overview-script', 'ir_data', $localized_data );
		}

		/**
		 * Update course chart via ajax
		 */
		public function ajaxUpdateCourseChart() {
			if ( empty( $_POST ) || ! ( array_key_exists( 'action', $_POST ) && 'ir-update-course-chart' == $_POST['action'] ) ) {
				die();
			}
			$course_id = filter_input( INPUT_POST, 'course_id', FILTER_SANITIZE_NUMBER_INT );

			if ( empty( $course_id ) ) {
				echo json_encode( [ 'error' => __( 'No Data Found', 'wdm_instructor_role' ) ] );
				die();
			}

			$course_data = $this->fetchCourseChartData( $course_id );
			echo json_encode( $course_data );
			die();
		}

		/**
		 * Generate submission reports for the overview page
		 */
		public function generateSubmissionReports() {
			$no_of_records = 10;
			$page_no       = 1;

			/**
			 * Allow 3rd party plugins to filter through the submissions array.
			 *
			 * @since 3.1.0
			 */
			$submissions = apply_filters( 'ir_overview_submissions', $this->getSubmissionReportData( $page_no, $no_of_records ) );

			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/overview/ir-submission-reports.template.php',
				[
					'submissions' => $submissions,
					'instance'    => $this,
				]
			);
		}

		/**
		 * Calculate Instructor earnings
		 *
		 * @param int $user_id      ID of the user.
		 */
		public static function calculateInstructorEarnings( $user_id = 0 ) {
			if ( empty( $user_id ) ) {
				$user_id = get_current_user_id();
			}

			if ( empty( $user_id ) ) {
				return false;
			}

			$earnings = [
				'paid'   => 0,
				'unpaid' => 0,
				'total'  => 0,
			];

			global $wpdb;

			$table = $wpdb->prefix . 'wdm_instructor_commission';
			$sql   = $wpdb->prepare( "SELECT commission_price FROM $table where user_id = %d", $user_id );

			$commissions = $wpdb->get_col( $sql );
			if ( empty( $commissions ) ) {
				return $earnings;
			}

			$total_commission = array_sum( $commissions );
			$paid_amount      = floatval( get_user_meta( $user_id, 'wdm_total_amount_paid', 1 ) );

			if ( empty( $paid_amount ) ) {
				$paid_amount = 0;
			}

			$earnings['paid']   = learndash_instructor_role_normalize_float_value( $paid_amount );
			$earnings['total']  = learndash_instructor_role_normalize_float_value( $total_commission );
			$earnings['unpaid'] = learndash_instructor_role_normalize_float_value( $total_commission - $paid_amount );

			return $earnings;
		}

		/**
		 * Get essay points
		 *
		 * @param int $essay_id         ID of the essay.
		 * @param int $question_id      ID of the question.
		 * @return mixed
		 */
		public function getEssayPoints( $essay_id, $question_id ) {
			$essay = get_post( $essay_id );
			if ( empty( $essay ) || empty( $question_id ) ) {
				return false;
			}

			$author = $essay->post_author;

			$quiz_data = maybe_unserialize( get_user_meta( $author, '_sfwd-quizzes', 1 ) );

			if ( empty( $quiz_data ) ) {
				return false;
			}

			$grade_data = array_column( $quiz_data, 'graded' );
			if ( empty( $grade_data ) || ! is_array( $grade_data ) ) {
				return false;
			}
			$grade_data = $grade_data[0];
			if ( ! array_key_exists( $question_id, $grade_data ) ) {
				return false;
			}

			return $grade_data[ $question_id ]['points_awarded'];
		}

		/**
		 * Add earnings localized data
		 *
		 * @param array $earnings       Earnings data to be localized.
		 * @return array
		 */
		public function addEarningsLocalizedData( $earnings ) {
			// Fetch Theme Colors.
			$colors                          = $this->get_preset_colors();
			$chart_colors                    = isset( $colors['primary'] ) ? [ $colors['primary'], '#C4C4C4' ] : [ '#0dbc92', '#fa4671' ];
			$earnings['title']               = __( 'Earnings', 'wdm_instructor_role' );
			$earnings['paid_label']          = __( 'Paid', 'wdm_instructor_role' );
			$earnings['unpaid_label']        = __( 'Unpaid', 'wdm_instructor_role' );
			$earnings['default_units_value'] = __( 'Amount', 'wdm_instructor_role' );
			$earnings['colors']              = $chart_colors;

			return $earnings;
		}

		/**
		 * Add Charts localized data
		 *
		 * @param array $chart_data     Chart data to be localized.
		 * @return array
		 * @since
		 */
		public function addChartLocalizedData( $chart_data ) {
			$colors                           = $this->get_preset_colors();
			$chart_colors                     = isset( $colors['primary'] ) ? [ $colors['primary'], self::hex2rgba( $colors['primary'], 0.3 ), self::hex2rgba( $colors['primary'], 0.6 ) ] : [ '#6940c1', '#7270ea', '#a5a4ff' ];
			$chart_data['not_started_label']  = __( 'Not Started', 'wdm_instructor_role' );
			$chart_data['in_progress_label']  = __( 'In Progress', 'wdm_instructor_role' );
			$chart_data['completed_label']    = __( 'Completed', 'wdm_instructor_role' );
			$chart_data['default_user_value'] = __( 'Users', 'wdm_instructor_role' );

			$chart_data['colors'] = $chart_colors;

			return $chart_data;
		}

		/**
		 * Get submission reports data
		 *
		 * @param int $page_no          Page number.
		 * @param int $no_of_records    No of records.
		 * @return mixed
		 */
		public function getSubmissionReportData( $page_no, $no_of_records ) {
			$current_user_id = intval( get_current_user_id() );

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

			// Complete instructor course list.
			$course_list = ir_get_instructor_complete_course_list( $current_user_id );

			$assignments = [];
			foreach ( $assignment_ids as $assignment_id ) {
				$assignment_details = get_post_meta( $assignment_id );

				if ( empty( $assignment_details ) ) {
					continue;
				}

				// Find the course related to the assignment.
				$course_id = $assignment_details['course_id'][0];

				// If course not owned or shared with current instructor, continue to next assignment.
				if ( ! in_array( $course_id, $course_list ) ) {
					continue;
				}

				$course_title = get_the_title( $assignment_details['course_id'][0] );
				$course_title = empty( $course_title ) ? '-' : $course_title;

				$lesson_title = $assignment_details['lesson_title'][0];
				$lesson_title = empty( $lesson_title ) ? '-' : $lesson_title;

				$date = get_the_date( 'd M y, H:i', $assignment_id );

				$points = array_key_exists( 'points', $assignment_details ) ? $assignment_details['points'][0] : '-';

				$status = array_key_exists(
					'approval_status',
					$assignment_details
				) ? $assignment_details['approval_status'][0] : 0;

				$download_link = $assignment_details['file_link'][0];
				$download_link = empty( $download_link ) ? '' : $download_link;

				// Post assignment author.
				$assignment_author_id   = get_post_field( 'post_author', $assignment_id );
				$assignment_author_data = get_userdata( $assignment_author_id );

				array_push(
					$assignments,
					[
						'title'       => $assignment_details['file_name'][0],
						'course'      => $course_title,
						'lesson'      => $lesson_title,
						'date'        => $date,
						'timestamp'   => get_post_timestamp( $assignment_id ),
						'points'      => $points,
						'status'      => ( $status ) ? __( 'Approved', 'wdm_instructor_role' ) : __( 'Not Approved', 'wdm_instructor_role' ),
						'edit_link'   => get_the_permalink( $assignment_id ),
						'link'        => add_query_arg(
							[
								'post'   => $assignment_id,
								'action' => 'edit',
							],
							admin_url( 'post.php' )
						),
						'type'        => __( 'Assignment', 'wdm_instructor_role' ),
						'author_img'  => get_avatar_url( $assignment_author_id ),
						'author_name' => $assignment_author_data->data->display_name,
					]
				);
			}

			if ( count( $assignment_ids ) == $no_of_records ) {
				return $assignments;
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

			$essays = [];
			foreach ( $essay_ids as $essay_id ) {
				$essay_details = get_post_meta( $essay_id );
				if ( empty( $essay_details ) ) {
					continue;
				}

				$essay_course_id = $essay_details['course_id'][0];

				if ( ! in_array( $essay_course_id, $course_list ) ) {
					continue;
				}

				$question_title = get_the_title( $essay_id );
				$question_title = empty( $question_title ) ? '-' : $question_title;

				$course_title = get_the_title( $essay_course_id );
				$course_title = empty( $course_title ) ? '-' : $course_title;

				$lesson_title = empty( $essay_details['lesson_title'][0] ) ? get_the_title( $essay_details['lesson_id'][0] ) : '';
				$lesson_title = empty( $lesson_title ) ? '-' : $lesson_title;

				$date = get_the_date( 'd M y, H:i', $essay_id );

				$points = $this->getEssayPoints( $essay_id, $essay_details['question_id'][0] );
				$points = ( false === $points ) ? '-' : $points;

				$status = get_post_status( $essay_id );

				// Get essay author.
				$essay_post        = get_post( $essay_id );
				$essay_author_id   = $essay_post->post_author;
				$essay_author_data = get_userdata( $essay_author_id );

				array_push(
					$essays,
					[
						'title'       => $question_title,
						'course'      => $course_title,
						'lesson'      => $lesson_title,
						'date'        => $date,
						'timestamp'   => get_post_timestamp( $essay_id ),
						'points'      => $points,
						'status'      => ( 'graded' == $status ) ? __( 'Graded', 'wdm_instructor_role' ) : __( 'Not Graded', 'wdm_instructor_role' ),
						'link'        => add_query_arg(
							[
								'post'   => $essay_id,
								'action' => 'edit',
							],
							admin_url( 'post.php' )
						),
						'type'        => __( 'Essay', 'wdm_instructor_role' ),
						'author_img'  => get_avatar_url( $essay_author_id ),
						'author_name' => $essay_author_data->data->display_name,
					]
				);
			}

			$submissions = array_merge( $assignments, $essays );

			return $submissions;
		}

		/**
		 * Add ellipses to long titles
		 *
		 * @param string $title
		 * @return string
		 */
		public function addEllipses( $title ) {
			if ( empty( $title ) ) {
				return $title;
			}
			$length = strlen( $title );

			if ( 15 > $length ) {
				return $title;
			}

			return substr( $title, 0, 12 ) . '...';
		}

		/**
		 * Convert Hex color code to rgba
		 *
		 * @since 4.3.0
		 *
		 * @param string $color     Color code in Hex.
		 * @param string $opacity   Opacity for the color.
		 */
		public static function hex2rgba( $color, $opacity = false ) {
			$default = 'rgb(0,0,0)';

			// Return default if no color provided.
			if ( empty( $color ) ) {
				return $default;
			}

			// Sanitize $color if "#" is provided.
			if ( '#' === $color[0] ) {
				$color = substr( $color, 1 );
			}

				// Check if color has 6 or 3 characters and get values.
			if ( strlen( $color ) == 6 ) {
					$hex = [ $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] ];
			} elseif ( strlen( $color ) == 3 ) {
					$hex = [ $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] ];
			} else {
					return $default;
			}

			// Convert hexadecimal to rgb.
			$rgb = array_map( 'hexdec', $hex );

			// Check if opacity is set(rgba or rgb).
			if ( $opacity ) {
				if ( abs( $opacity ) > 1 ) {
					$opacity = 1.0;
				}
				$output = 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
			} else {
				$output = 'rgb(' . implode( ',', $rgb ) . ')';
			}

			// Return rgb(a) color string.
			return $output;
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
