<?php
/**
 * Shortcode: Frontend Gradebook
 *
 * @since 2.0.0
 * @updated 3.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes/shortcodes
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_SC_FrontendGradebook
 *
 * Contains the grade for a given user.
 *
 * @since 2.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes/shortcodes
 */
class LD_GB_SC_FrontendGradebook extends LD_GB_Shortcode {
	/**
	 * LD_GB_SC_FrontendGradebook constructor.
	 *
	 * @since 2.0.0
	 * @updated 3.0.0
	 */
	function __construct() {
		parent::__construct( 'ld_gradebook' );  }

	/**
	 * Outputs the shortcode.
	 *
	 * @since 2.0.0
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

		return LearnDash_Gradebook()->frontend_gradebook->render( $atts );  }
}
