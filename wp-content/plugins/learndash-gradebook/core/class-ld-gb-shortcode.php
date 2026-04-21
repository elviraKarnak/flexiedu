<?php
/**
 * Shortcode class framework
 *
 * @since 1.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_Shortcode
 *
 * Contains the grade for a given user.
 *
 * @since 1.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes
 */
abstract class LD_GB_Shortcode {
	/**
	 * The shortcode ID (tag).
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $id;

	/**
	 * The shortcode attributes.
	 *
	 * @since 1.0.0
	 *
	 * @var array|bool
	 */
	public $atts;

	/**
	 * LD_GB_Shortcode constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string     $id
	 * @param array|bool $atts
	 */
	function __construct( $id, $atts = false ) {
		$this->id   = $id;
		$this->atts = $atts;

		add_shortcode( $this->id, [ $this, 'shortcode' ] );
	}

	/**
	 * Sets up the default attributes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts
	 *
	 * @return array
	 */
	public function default_atts( $atts ) {
		if ( $this->atts ) {
			$this->atts = shortcode_atts( $this->atts, $atts, $this->id );
		}

		return $this->atts;
	}

	/**
	 * Outputs the shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return mixed The shortcode output.
	 */
	public function shortcode( $atts = [], $content = '' ) {}
}
