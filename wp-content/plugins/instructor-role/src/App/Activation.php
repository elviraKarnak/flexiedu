<?php
/**
 * Handles Plugin Activation logic
 *
 * @since 5.9.1
 *
 * @package LearnDash\Instructor_Role
 */

namespace LearnDash\Instructor_Role;

use InstructorRole\Includes\Instructor_Role_Activator;

/**
 * Plugin Activation class.
 *
 * @since 5.9.1
 */
class Activation {
	/**
	 * Runs an authentication check. If the check fails, the plugin is deactivated and an error message is shown.
	 *
	 * @since 5.9.1
	 * @since 5.9.2 Added the optional parameter $network_wide.
	 *
	 * @param bool $network_wide Whether to enable the plugin for all sites in the network or just the current site. Multisite only.
	 *                           Default false.
	 *
	 * @return void
	 */
	public static function run( $network_wide = false ): void {
		$result = Licensing\Authentication::verify_token();

		if ( ! is_wp_error( $result ) ) {
			require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/class-instructor-role-activator.php';

			$plugin_activator = new Instructor_Role_Activator();
			$plugin_activator->activate( $network_wide );
		} else {
			deactivate_plugins( plugin_basename( INSTRUCTOR_ROLE_PLUGIN_FILE ) );

			die( esc_html( $result->get_error_message() ) );
		}
	}
}
