<?php
/**
 * Handling plugin licenses
 *
 * @link https://learndash.com
 * @since 1.0.0
 *
 * @deprecated 5.9.1 This file is no longer in use.
 *
 * @package LearnDash\Instructor_Role
 */

namespace InstructorRole\Includes;

defined( 'ABSPATH' ) || exit;

/**
 * Instructor Role License
 */
class Instructor_Role_License {
	/**
	 * Load license
	 */
	public function load_license() {
		global $instructor_role_plugin_data;

		if ( empty( $instructor_role_plugin_data ) ) {
			$instructor_role_plugin_data = include_once plugin_dir_path( __DIR__ ) . 'license.config.php';
			new \Licensing\WdmLicense( $instructor_role_plugin_data );
		}
	}

	/**
	 * Check if license available
	 *
	 * @return boolean    True if active, false otherwise.
	 */
	public static function is_available_license() {
		global $instructor_role_plugin_data;

		if ( empty( $instructor_role_plugin_data ) ) {
			$instructor_role_plugin_data = include_once plugin_dir_path( __DIR__ ) . 'license.config.php';
			new \Licensing\WdmLicense( $instructor_role_plugin_data );
		}

		$get_data_from_db = \Licensing\WdmLicense::checkLicenseAvailiblity( $instructor_role_plugin_data['pluginSlug'], false ); // cspell:disable-line .

		if ( 'available' == $get_data_from_db ) {
			return true;
		}

		return false;
	}
}
