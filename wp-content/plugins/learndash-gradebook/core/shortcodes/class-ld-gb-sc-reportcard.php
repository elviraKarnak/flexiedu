<?php
/**
 * Shortcode: User Grade
 *
 * @since 1.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes/shortcodes
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_SC_ReportCard
 *
 * Contains the grade for a given user.
 *
 * @since 1.0.0
 * @updated 3.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes/shortcodes
 */
class LD_GB_SC_ReportCard extends LD_GB_Shortcode {
	/**
	 * LD_GB_SC_ReportCard constructor.
	 *
	 * @since 1.0.0
	 * @updated 3.0.0
	 */
	function __construct() {
		parent::__construct( 'ld_report_card' );    }

	/**
	 * Outputs the shortcode.
	 *
	 * @since 1.0.0
	 * @updated 3.0.0
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return mixed
	 */
	function shortcode( $atts = [], $content = '' ) {
		// If no attributes are provided, WP makes this an empty String
		if ( ! is_array( $atts ) ) {
			$atts = [];
		}

		return LearnDash_Gradebook()->report_card->render( $atts ); }
}
