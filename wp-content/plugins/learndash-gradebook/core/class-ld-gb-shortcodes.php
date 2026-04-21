<?php
/**
 * Manages all plugin shortcodes.
 *
 * @since 1.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_Shortcodes
 *
 * Contains the grade for a given user.
 *
 * @since 1.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes
 */
class LD_GB_Shortcodes {
	/**
	 * All plugin shortcodes.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $shortcodes = [];

	/**
	 * LD_GB_Shortcodes constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		add_filter( 'ld_gb_shortcodes', [ $this, 'included_shortcodes' ] );
		add_action( 'init', [ $this, 'init_shortcodes' ] );
	}

	/**
	 * Adds included shortcodes.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string, LD_GB_Shortcode> $shortcodes Shortcodes.
	 *
	 * @return array<string, LD_GB_Shortcode>
	 */
	public function included_shortcodes( $shortcodes ) {
		require_once LEARNDASH_GRADEBOOK_DIR . 'core/shortcodes/class-ld-gb-sc-reportcard.php'; // cspell: disable-line.

		$shortcodes['ld_reportcard'] = new LD_GB_SC_ReportCard(); // cspell: disable-line.

		require_once LEARNDASH_GRADEBOOK_DIR . 'core/shortcodes/class-ld-gb-sc-overallgrade.php'; // cspell: disable-line.

		$shortcodes['ld_overallgrade'] = new LD_GB_SC_OverallGrade(); // cspell: disable-line.

		require_once LEARNDASH_GRADEBOOK_DIR . 'core/shortcodes/class-ld-gb-sc-frontendgradebook.php'; // cspell: disable-line.

		$shortcodes['ld_gradebook'] = new LD_GB_SC_FrontendGradebook(); // cspell: disable-line.

		return $shortcodes;
	}

	/**
	 * Initializes all plugin shortcodes.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	function init_shortcodes() {
		/**
		 * Shortcodes for Gradebook by LearnDash.
		 *
		 * @since 1.0.0
		 */
		$shortcodes = apply_filters( 'ld_gb_shortcodes', [] );

		foreach ( $shortcodes as $id => $shortcode ) {
			$this->shortcodes[ $id ] = $shortcode;
		}
	}
}
