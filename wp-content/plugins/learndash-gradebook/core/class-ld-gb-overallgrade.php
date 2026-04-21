<?php
/**
 * Overall Grade
 *
 * @since 3.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_OverallGrade
 *
 * Contains the grade for a given user.
 *
 * @since 3.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes
 */
class LD_GB_OverallGrade {
	/**
	 * Whether or not this has been output.
	 *
	 * @since 3.0.0
	 *
	 * @var bool
	 */
	private $used = false;

	/**
	 * LD_GB_OverallGrade constructor.
	 *
	 * @since 3.0.0
	 */
	function __construct() {    }

	/**
	 * Renders the output.
	 *
	 * @since 3.0.0
	 *
	 * @param array<string,mixed> $atts     Shortcode/Block attributes.
	 * @param bool                $is_block Whether this is rendering a Block. Defaults to false.
	 *
	 * @return string
	 */
	public function render( $atts = [], $is_block = false ) {
		// Remove empty data
		$atts = array_filter( $atts );

		$atts = shortcode_atts(
			[
				'user'               => get_current_user_id(),
				'gradebook'          => false,
				'format'             => ld_gb_get_option_field( 'grade_display_mode', 'letter' ),
				'logged_out_message' => __( 'Please log in to view your overall grade.', 'learndash-gradebook' ),
			],
			$atts,
			'ld_overall_grade'
		);

		if ( ! is_user_logged_in() ) {
			return '<div class="ld-gb-report-card-overall">' . $atts['logged_out_message'] . '</div>';
		}

		if ( ! $atts['gradebook'] ) {
			return '<div class="ld-gb-report-card-overall">' . _x( 'You must define a Gradebook ID.', 'Overall Grade Shortcode/Block missing a Gradebook ID error', 'learndash-gradebook' ) . '</div>';
		}

		// Get user
		if ( ! ( $user = get_user_by( 'id', $atts['user'] ) ) ) {
			return '<div class="ld-gb-report-card-overall">' . _x( 'Cannot get user.', 'Overall Grade Shortcode/Block missing a User ID error', 'learndash-gradebook' ) . '</div>';
		}

		// Check if the current user has access to the gradebook.

		$current_user_id = get_current_user_id();

		$current_user_has_access = ld_gb_is_super_admin()
			|| (int) $atts['user'] === $current_user_id
			|| (
				learndash_is_group_leader_user( $current_user_id )
				&& (
					learndash_get_group_leader_manage_users() === 'advanced'
					|| learndash_is_group_leader_of_user( $current_user_id, $atts['user'] )
				)
			);

		$gradebook_id = $atts['gradebook'];

		ob_start();

		if ( ! get_post( $gradebook_id ) ) {
			ld_gb_locate_template(
				'overall-grade/overall-grade-error.php',
				[
					'gradebook_id' => $gradebook_id,
					'is_block'     => $is_block,
				]
			);
		} else {
			$user_grade = new LD_GB_UserGrade( $user, $gradebook_id );

			// Allow the theme to load the template instead of the plugin
			ld_gb_locate_template(
				'overall-grade/overall-grade.php',
				[
					'user_grade'   => $user_grade,
					'gradebook_id' => $gradebook_id,
					'format'       => $atts['format'],
					'is_block'     => $is_block,
				]
			);
		}

		$return = trim( (string) ob_get_clean() );

		$match = preg_match( '/<[\s\S]*?>([\s\S]*?)<\/[\s\S]*?>/sim', $return, $matches );

		if ( $match && isset( $matches[1] ) && $matches[1] ) {
			// Remove tabs and tabbed spaces
			$return = str_replace( $matches[1], trim( $matches[1] ), $return );
		}

		return $return;
	}
}
