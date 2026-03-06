<?php
/**
 * Team Member module.
 *
 * @since 1.0.0
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore ldquizntf atts srid timespent ldquiz
 */

namespace LearnDash\Groups_Plus\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Team_Member {

	/**
	 * The instance of the class
	 *
	 * @since    2.8.0
	 * @access   private
	 * @var      Boot
	 */
	private static $instance = null;

	/**
	 * This form ID that is use for form
	 */
	static $form_id = '';

	/**
	 * Class constructor
	 */
	public function __construct() {
		// fetch html of team member edit form
		add_action( 'wp_ajax_action_fetch_edit_team_member_html', array( $this, 'edit_team_member_html' ) );

		// remove certificate link to access from team member view
		add_filter( 'learndash_course_certificate_link', array( $this, 'learndash_course_certificate_link' ), 10, 3 );

		// Approve essay
		add_action( 'wp_ajax_approve_essay_request', array( $this, 'approve_essay_request' ) );

		add_action( 'wp_ajax_action_fetch_edit_elc_ldquiz_notification_html', array( $this, 'elc_ldquiz_notification_html' ) );
	}

	/**
	 * Creates singleton instance of class
	 *
	 * @return Team_Member $instance The InitializePlugin Class
	 * @since 2.8.0
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function edit_team_member_html() {
		self::$form_id = 'edit_groups_plus_team_member';

		if ( ! isset( $_POST['nonce'] ) || empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'learndash-groups-plus-team-member-list-table' ) ) {
			exit();
		}
		extract( $_POST );
		$user_id            = user_encrypt_decrypt( 'decrypt', sanitize_text_field( $_POST['user_key'] ) );
		$group_id           = general_encrypt_decrypt( 'decrypt', sanitize_text_field( $_POST['group_key'] ) );
		$user_info          = get_user_by( 'id', $user_id );
		$parent_group_id    = get_parent_group_id( $group_id );
		$is_primary_team_leader = is_primary_group_leader();
		include LEARNDASH_GROUPS_PLUS_DIR . 'src/resources/templates/team-member/ajax/edit-team-member.php';
		exit();
	}

	public function learndash_course_certificate_link( $url, $course_id, $user_id ) {
		$user_meta  = wp_get_current_user();
		$user_roles = $user_meta->roles;
		if ( in_array( 'group_leader', $user_roles ) || in_array( 'administrator', $user_roles ) ) {
			return $url;
		} elseif ( in_array( 'subscriber', $user_roles ) || in_array( 'customer', $user_roles ) || in_array( 'team_member', $user_roles ) ) {
			$allow_course_cert_publicly = get_site_option( 'allow_course_cert_publicly' );
			$user_groups                = learndash_get_users_group_ids( $user_id );
			// $course_groups = learndash_get_course_groups($course_id);
			$is_child_group = false;

			// Get all the groups of the Users and check whether group has parent,
			// if has parent then only check course id in that groupe and allow and MARK them as child group.
			foreach ( $user_groups as $user_group ) {
				if ( Group::get_parent_group_id( $user_group ) ) {
					$group_courses = learndash_get_group_courses_list( $user_group );
					if ( in_array( $course_id, $group_courses ) ) {
						$is_child_group = true;
						break;
					}
				}
			}
			// $user_courses = learndash_get_user_course_access_list($user_id);
			if ( $is_child_group === true ) {
				if ( $allow_course_cert_publicly == 1 ) {
					return $url;
				} else {
					return '';
				}
			}
		}
		return $url;
	}

	public function approve_essay_request() {

		$essay_id   = $_REQUEST['essayID'];
		$essay_post = get_post( $essay_id );

		if ( ( ! empty( $essay_post ) ) && ( $essay_post instanceof \WP_Post ) && ( learndash_get_post_type_slug( 'essay' ) === $essay_post->post_type ) ) {
			if ( 'graded' !== $essay_post->post_status ) {
				$quiz_score_difference = 1;
			}

			// First we update the essay post with the new post_status.
			$essay_post->post_status = 'graded';
			wp_update_post( $essay_post );

			$user_id     = $essay_post->post_author;
			$quiz_id     = get_post_meta( $essay_post->ID, 'quiz_id', true );
			$question_id = get_post_meta( $essay_post->ID, 'question_id', true );

			// Stole the following section ot code from learndash_save_essay_status_metabox_data();
			$submitted_essay_data = learndash_get_submitted_essay_data( $quiz_id, $question_id, $essay_post );

			if ( isset( $submitted_essay_data['points_awarded'] ) ) {
				$original_points_awarded = intval( $submitted_essay_data['points_awarded'] );
			} else {
				$original_points_awarded = 0;
			}

			$submitted_essay_data['status'] = 'graded';

			// get the new assigned points.
			$submitted_essay_data['points_awarded'] = intval( $_REQUEST['points'] );

			learndash_update_submitted_essay_data( $quiz_id, $question_id, $essay_post, $submitted_essay_data );

			if ( ! is_null( $original_points_awarded ) && ! is_null( $submitted_essay_data['points_awarded'] ) ) {
				if ( $submitted_essay_data['points_awarded'] > $original_points_awarded ) {
					$points_awarded_difference = intval( $submitted_essay_data['points_awarded'] ) - intval( $original_points_awarded );
				} else {
					$points_awarded_difference = ( intval( $original_points_awarded ) - intval( $submitted_essay_data['points_awarded'] ) ) * -1;
				}

				$updated_scoring_data = array(
					'updated_question_score'    => $submitted_essay_data['points_awarded'],
					'points_awarded_difference' => $points_awarded_difference,
					'score_difference'          => $quiz_score_difference,
				);

				learndash_update_quiz_data( $quiz_id, $question_id, $updated_scoring_data, $essay_post );
			}

			echo wp_kses_post( learndash_status_bubble( $submitted_essay_data['status'], 'essay', false ) );
		}
		exit();
	}

	public function elc_ldquiz_notification_html() {
		if ( ! isset( $_POST['nonce'] ) || empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'elc_ldquizntf_statistic' ) ) {
			exit();
		}

		if ( ! function_exists( 'elc_ldquiz_prepare_atts' ) || ! function_exists( 'elc_ldquiz_last_quiz_callback' ) ) {
			exit();
		}
		
		extract( $_POST );
		// $user_id = user_encrypt_decrypt('decrypt', sanitize_text_field($_POST['user_key']));
		// $group_id = general_encrypt_decrypt('decrypt', sanitize_text_field($_POST['group_key']));
		// $user_info = get_user_by("id", $user_id);
		// $parent_group_id = get_parent_group_id($group_id);
		// $is_primary_team_leader = is_primary_group_leader();
		$show_ar = array(
			'custom_fields',
			'quiz_title',
			'breadcrumbs',
			'timespent',
			'cat_score',
			'overall_score',
			'result_message',
			'questions_answers',
		);

		// Last Quiz shortcode attributes.
		$pre_atts = array(
			/*
			'show' => implode( ',', $show_ar ),
			'question_cat',
			'show_essay',
			'comments',
			'answer_feedback',*/
			'quiz_id'           => $_POST['quiz_id'],
			'statistics_ref_id' => $_POST['srid'],
		);

		$atts = elc_ldquiz_prepare_atts( $pre_atts );
		$_REQUEST['action'] = 'ldquizntf_submit';
		echo elc_ldquiz_last_quiz_callback( $atts );
		exit();
	}

}
