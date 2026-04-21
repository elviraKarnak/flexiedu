<?php
/**
 * Manages custom post types for the plugin.
 *
 * @since 1.2.0
 *
 * @package LearnDash_Gradebook
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_PostTypes
 *
 * Manages custom post types for the plugin.
 *
 * @since 1.2.0
 */
class LD_GB_PostTypes {
	/**
	 * Gradebook post type.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @var LD_GB_PostType_Gradebook
	 */
	public $gradebook;

	/**
	 * LD_GB_PostTypes constructor.
	 *
	 * @since 1.2.0
	 */
	function __construct() {
		require_once LEARNDASH_GRADEBOOK_DIR . 'core/post-types/class-ld-gb-posttype-gradebook.php'; // cspell: disable-line.

		$this->gradebook = new LD_GB_PostType_Gradebook();
	}

	/**
	 * Getter for the Gradebook post type.
	 *
	 * @since 1.2.0
	 *
	 * @return LD_GB_PostType_Gradebook
	 */
	public function gradebook() {
		return $this->gradebook;
	}
}
