<?php
/**
 * Adds the User Grades admin page.
 *
 * @since 1.2.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/admin
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_AdminPage_UserGrades
 *
 * Adds the Gradebook admin page.
 *
 * @since 1.2.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/admin
 */
class LD_GB_AdminPage_UserGrades {
	/**
	 * LD_GB_AdminPage_UserGrades constructor.
	 *
	 * @since 1.2.0
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'page_actions' ] );
		add_filter( 'ld_gb_admin_script_data', [ $this, 'localize_data' ] );
		add_filter( 'ld_gb_admin_page_learndash-gradebook-user-grades_sections', [ $this, 'page_sections' ] );
		add_action( 'wp_ajax_ld_gb_edit_grade', [ $this, 'edit_grade' ] );
		add_action( 'wp_ajax_ld_gb_edit_component_grade', [ $this, 'edit_component_grade' ] );
	}

	/**
	 * Loads on the Gradebook page only.
	 *
	 * @since 1.2.0
	 * @access private
	 */
	function page_actions() {
		if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'learndash-gradebook-user-grades' ) {
			return;
		}

		add_filter( 'rbm_fieldhelpers_load_select2', '__return_true' );
	}

	/**
	 * Provides data for localization
	 *
	 * @since 1.2.0
	 * @access private
	 *
	 * @param array $data Data to be localized.
	 *
	 * @return array
	 */
	function localize_data( $data ) {
		$data['l10n']['change']                         = _x( 'Change', 'User Grade Change button text', 'learndash-gradebook' );
		$data['l10n']['close']                          = _x( 'Close', 'Close User Grade button text', 'learndash-gradebook' );
		$data['l10n']['could_not_edit_grade']           = _x( 'Could not edit grade.', 'User Grade Could not edit grade error message', 'learndash-gradebook' );
		$data['l10n']['manual_grade_add_error']         = _x( 'Please fill out Name and Score', 'Manual Grade missing Name and Score error message', 'learndash-gradebook' );
		$data['l10n']['manual_grade_add_error_numbers'] = _x( 'Score can only contain numbers', 'Manual Grade invalid score error message', 'learndash-gradebook' );
		$data['l10n']['manual_grade_processing']        = _x( 'Processing', 'Manual Grade processing status message', 'learndash-gradebook' );
		$data['l10n']['manual_grade_add_button']        = _x( 'Add Grade', 'Manual Grade Add button text', 'learndash-gradebook' );
		$data['l10n']['manual_grade_confirm_delete']    = _x( 'Are you sure you want to delete this grade?', 'Manual Grade deletion confirmation message', 'learndash-gradebook' );
		$data['l10n']['component_grade_override']       = _x( 'Override', 'User Grade Component Override button text', 'learndash-gradebook' );
		$data['l10n']['component_grade_modify']         = _x( 'Modify', 'User Grade override an overridden Component button text', 'learndash-gradebook' );

		$data['gradebook'] = LD_GB_AdminPage_Gradebook::get_active_gradebook();

		return $data;
	}

	/**
	 * This page's sections.
	 *
	 * @since 1.1.0
	 * @access private
	 *
	 * @return array
	 */
	function page_sections() {
		$user      = get_user_by( 'id', $_GET['user'] );
		$gradebook = get_post( $_GET['gradebook'] );

		$label = sprintf(
			// translators: %1$s is the user display name, %2$s is the Gradebook name.
			_x( '%1$s\'s grades for %2$s', 'User Grades Admin Page Title. %1$s is user name, %2$s is Gradebook name', 'learndash-gradebook' ),
			$user->display_name,
			$gradebook->post_title
		);

		return [
			[
				'id'       => 'main',
				'label'    => $label,
				'callback' => [ $this, 'user_grades_page' ],
			],
		];
	}

	/**
	 * The admin page output.
	 *
	 * @since 1.2.0
	 * @access private
	 */
	function user_grades_page() {
		// Fix specific to the Instructor Roles plugin by WisdmLabs
		if ( function_exists( 'wdm_set_author' ) ) {
			remove_filter( 'pre_get_posts', 'wdm_set_author' );
		} elseif ( class_exists( 'InstructorRole\Modules\Classes\Instructor_Role_Admin' ) && method_exists( 'InstructorRole\Modules\Classes\Instructor_Role_Admin', 'wdm_set_author' ) ) {
			learndash_gradebook_remove_class_action( 'pre_get_posts', 'InstructorRole\Modules\Classes\Instructor_Role_Admin', 'wdm_set_author' );
		}

		$gradebook   = (int) $_GET['gradebook'];
		$user_grade  = new LD_GB_UserGrade( $_GET['user'], $gradebook );
		$user        = get_user_by( 'id', $_GET['user'] );
		$is_weighted = ld_gb_get_field( 'gradebook_weighting_enable', $gradebook ) === '1';

		$grade_status_options = [];

		if ( $grade_statuses = ld_gb_get_grade_statuses() ) {
			foreach ( $grade_statuses as $status_ID => $status ) {
				// We do not want to allow "Pending Approval" to be an option for users. This is specific to Assignment handling
				if ( $status_ID == 'pending' ) {
					continue;
				}

				$grade_status_options[ $status_ID ] = $status['label'];
			}
		}

		include LEARNDASH_GRADEBOOK_DIR . 'admin/views/html-user-grades-page.php';

		if ( function_exists( 'wdm_set_author' ) ) {
			add_filter( 'pre_get_posts', 'wdm_set_author' );
		} elseif ( class_exists( 'InstructorRole\Modules\Classes\Instructor_Role_Admin' ) && method_exists( 'InstructorRole\Modules\Classes\Instructor_Role_Admin', 'wdm_set_author' ) ) {
			add_action( 'pre_get_posts', [ InstructorRole\Modules\Classes\Instructor_Role_Admin::get_instance(), 'wdm_set_author' ] );
		}   }

	/**
	 * Edits a grade.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	function edit_grade() {
		if ( ! isset( $_POST['data'] ) ) {
			wp_send_json_error(
				[
					'error' => _x( 'No grade data received', 'Edit Grade: No Grade Data sent error message', 'learndash-gradebook' ),
				]
			);
		}

		$grade = $_POST['data'];

		// Deal with slashes
		$grade = array_map( 'wp_unslash', $grade );

		switch ( $grade['type'] ) {
			case 'manual':
				if ( ld_gb_get_option_field( 'disable_manual_grades', false ) ) {
					$data = [
						'type' => 'error',
						'data' => [
							'error' => _x( 'Manual Grades have been disabled.', 'Edit Grade: Manual Grades have been disabled error message', 'learndash-gradebook' ),
						],
					];
				} elseif ( $grade['delete'] == '1' ) {
						$data = learndash_gradebook_delete_manual_grade( $grade );
				} elseif ( $grade['new'] == '1' ) {
						$data = learndash_gradebook_update_manual_grade( $grade, false );
				} else {
					$data = learndash_gradebook_update_manual_grade( $grade );
				}

				break;

			case 'quiz':
				$data = $this->edit_quiz_grade( $grade );
				break;

			case 'assignment':
				$data = $this->edit_assignment_grade( $grade );
				break;

			case 'lesson':
				$data = $this->edit_lesson_grade( $grade );
				break;

			case 'topic':
				$data = $this->edit_topic_grade( $grade );
				break;

			default:
				$data = [
					'type' => 'error',
					'data' => [
						'error' => _x( 'Could not edit grade', 'Edit Grade: Fallback error message', 'learndash-gradebook' ),
					],
				];
		}

		if ( $data['type'] == 'error' ) {
			wp_send_json_error( $data['data'] );
		} else {
			wp_send_json_success( $data['data'] );
		}
	}

	/**
	 * AJAX callback for editing a component grade override.
	 */
	function edit_component_grade() {
		if ( ld_gb_get_option_field( 'disable_component_override', false ) ) {
			wp_send_json_error(
				[
					'error' => _x( 'Overriding Component Grades has been disabled', 'Override Component Grade: Component Grade Overrides disabled error message', 'learndash-gradebook' ),
				]
			);
		}

		$action       = $_POST['data']['action'];
		$new_grade    = $_POST['data']['new_grade'];
		$user_ID      = $_POST['data']['user_id'];
		$component_ID = $_POST['data']['component_id'];
		$gradebook    = $_POST['data']['gradebook'];

		if ( $user_ID === null || $component_ID === null ) {
			wp_send_json_error(
				[
					'error' => _x( 'User ID or Component ID not provided', 'Override Component Grade: User ID or Component ID not provided error message', 'learndash-gradebook' ),
				]
			);
		}

		switch ( $action ) {
			case 'save':
				$result = learndash_gradebook_update_component_grade_override( $user_ID, $gradebook, $component_ID, $new_grade );

				break;

			case 'delete':
				$result = learndash_gradebook_delete_component_grade_override( $user_ID, $gradebook, $component_ID );

				break;
		}

		if ( is_wp_error( $result ) ) {
			wp_send_json_error(
				[
					'error' => implode( ';', $result->get_error_messages() ),
				]
			);
		}

		// Get final score
		$user_grade = new LD_GB_UserGrade( $user_ID, $gradebook );

		// Get component grade
		$component                = $user_grade->get_component( $component_ID );
		$component_grade          = [];
		$component_grade['grade'] = $component['averaged_score'];
		$component_grade['score'] = LD_GB_UserGrade::get_display_grade( $component['averaged_score'], ld_gb_get_option_field( 'grade_display_mode', 'letter' ) );
		$component_grade['color'] = LD_GB_UserGrade::get_display_grade_color( $component['averaged_score'] );

		wp_send_json_success(
			[
				'user_grade'      => [
					'score' => LD_GB_UserGrade::get_display_grade( $user_grade->get_user_grade(), ld_gb_get_option_field( 'grade_display_mode', 'letter' ) ),
					'color' => LD_GB_UserGrade::get_display_grade_color( $user_grade->get_user_grade() ),
				],
				'component_grade' => $component_grade,
			]
		);
	}

	/**
	 * Edits a quiz grade.
	 *
	 * @since 1.0.1
	 *
	 * @param array $grade The grade data.
	 *
	 * @return array Response data.
	 */
	public function edit_quiz_grade( $grade ) {
		$response = learndash_gradebook_edit_post_driven_grade( $grade );

		if ( $response['type'] !== 'error' ) {
			$response['data']['success'] = sprintf(
				// translators: %s is a Quiz label.
				_x( 'Successfully edited %s grade.', 'User Grades: Successfully Edited Quiz Grade message. %s is Quiz', 'learndash-gradebook' ),
				LearnDash_Custom_Label::get_label( 'quiz' )
			);
		}

		return $response;
	}

	/**
	 * Edits a assignment grade.
	 *
	 * @since 1.0.1
	 *
	 * @param array $grade The grade data.
	 *
	 * @return array Response data.
	 */
	public function edit_assignment_grade( $grade ) {
		$response = learndash_gradebook_edit_post_driven_grade( $grade );

		if ( $response['type'] !== 'error' ) {
			$response['data']['success'] = sprintf(
				// translators: %s is an Assignment label.
				_x( 'Successfully edited %s grade.', 'User Grades: Successfully Edited Assignment Grade message. %s is Assignment', 'learndash-gradebook' ),
				LearnDash_Custom_Label::get_label( 'assignment' )
			);
		}

		return $response;
	}

	/**
	 * Edits a lesson grade.
	 *
	 * @since 1.2.0
	 *
	 * @param array $grade The grade data.
	 *
	 * @return array Response data.
	 */
	public function edit_lesson_grade( $grade ) {
		$response = learndash_gradebook_edit_post_driven_grade( $grade );

		if ( $response['type'] !== 'error' ) {
			/* translators: First %s is Lesson */
			$response['data']['success'] = sprintf( _x( 'Successfully edited %s grade.', 'User Grades: Successfully Edited Lesson Grade message. %s is Lesson', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'lesson' ) );
		}

		return $response;
	}

	/**
	 * Edits a topic grade.
	 *
	 * @since 1.2.0
	 *
	 * @param array $grade The grade data.
	 *
	 * @return array Response data.
	 */
	function edit_topic_grade( $grade ) {
		$response = learndash_gradebook_edit_post_driven_grade( $grade );

		if ( $response['type'] !== 'error' ) {
			/* translators: First %s is Topic */
			$response['data']['success'] = sprintf( _x( 'Successfully edited %s grade.', 'User Grades: Successfully Edited Topic Grade message. %s is Topic', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'topic' ) );
		}

		return $response;
	}

	/**
	 * Some component types share editing functionality.
	 *
	 * @since 1.2.0
	 * @access private
	 *
	 * @param array $grade The grade data.
	 *
	 * @return array Response data.
	 */
	private function edit_post_driven_grade( $grade ) {
		if ( isset( $grade['status'] ) ) {
			if ( $grade['status'] ) {
				update_user_meta( $grade['user_id'], "ld_gb_grade_status_{$grade['gradebook']}_{$grade['post_id']}", $grade['status'] );
			} else {
				delete_user_meta( $grade['user_id'], "ld_gb_grade_status_{$grade['gradebook']}_{$grade['post_id']}" );
			}
		}

		// Get final score
		$user_grade = new LD_GB_UserGrade( $grade['user_id'], $grade['gradebook'] );

		// Get component grade.
		$component                = $user_grade->get_component( $grade['component'] );
		$component_grade['score'] = LD_GB_UserGrade::get_display_grade( $component['averaged_score'], ld_gb_get_option_field( 'grade_display_mode', 'letter' ) );
		$component_grade['color'] = LD_GB_UserGrade::get_display_grade_color( $component['averaged_score'] );

		$grade = LD_GB_UserGrade::modify_grade_by_status( $grade );

		return [
			'status' => 'success',
			'data'   => [
				'score_display'   => $grade['score_display'],
				'component_grade' => $component_grade,
				'user_grade'      => [
					'score' => LD_GB_UserGrade::get_display_grade( $user_grade->get_user_grade(), ld_gb_get_option_field( 'grade_display_mode', 'letter' ) ),
					'color' => LD_GB_UserGrade::get_display_grade_color( $user_grade->get_user_grade() ),
				],
			],
		];
	}
}
