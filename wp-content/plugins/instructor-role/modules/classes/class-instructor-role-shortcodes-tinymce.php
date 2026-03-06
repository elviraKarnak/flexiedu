<?php
/**
 * Shortcodes TinyMce Module
 *
 * @since 5.8.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

namespace InstructorRole\Modules\Classes;

use LearnDash_Shortcodes_TinyMCE;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Shortcodes_TinyMCE' ) && class_exists( 'LearnDash_Shortcodes_TinyMCE' ) ) {
	/**
	 * Class Instructor Role Notifications Module
	 */
	class Instructor_Role_Shortcodes_TinyMCE extends LearnDash_Shortcodes_TinyMCE {
		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
		 *
		 * @since 3.3.0
		 */
		protected static $instance = null;

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 5.8.0
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}
	}
}
