<?php
/**
 * Handles Plugin Deactivation logic.
 *
 * @since 5.9.2
 *
 * @package LearnDash\Instructor_Role
 */

namespace LearnDash\Instructor_Role;

use InstructorRole\Includes\Instructor_Role_Deactivator;

/**
 * Plugin Deactivation class.
 *
 * @since 5.9.2
 */
class Deactivation {
	/**
	 * Runs the deactivation logic.
	 *
	 * @since 5.9.2
	 *
	 * @param bool $network_deactivating Whether the plugin is deactivated for all sites in the network or just the current site. Multisite only.
	 *                                   Default false.
	 *
	 * @return void
	 */
	public static function run( $network_deactivating = false ): void {
		require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/class-instructor-role-deactivator.php';

		$plugin_deactivator = new Instructor_Role_Deactivator();
		$plugin_deactivator->deactivate( $network_deactivating );
	}
}
