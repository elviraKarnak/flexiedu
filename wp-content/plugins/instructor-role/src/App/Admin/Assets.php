<?php
/**
 * Admin Assets Loader.
 *
 * @since 5.9.7
 *
 * @package LearnDash\Instructor_Role
 */

namespace LearnDash\Instructor_Role\Admin;

use LearnDash\Instructor_Role\StellarWP\Assets\Asset;
use LearnDash\Instructor_Role\StellarWP\Assets\Assets as Base_Assets;

/**
 * Admin Assets Loader.
 *
 * @since 5.9.7
 */
class Assets {
	/**
	 * Asset Group to register our Assets to and enqueue from.
	 *
	 * @since 5.9.7
	 *
	 * @var string
	 */
	public static string $group = 'learndash-instructor-role-admin';

	/**
	 * Registers assets to the asset group.
	 *
	 * @since 5.9.7
	 *
	 * @return void
	 */
	public function register_assets(): void {
		Asset::add( 'learndash-instructor-role-admin-pointers', 'pointers.css' )
			->add_to_group( self::$group )
			->set_path( 'dist/css/admin', false )
			->set_dependencies( 'wp-pointer' )
			->enqueue_on( 'admin_enqueue_scripts', 10 ) // Must match the where it is hooked in the provider.
			->register();
	}
}
