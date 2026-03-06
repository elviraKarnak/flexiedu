<?php
/**
 * Instructor Dashboard provider class file.
 *
 * @since 5.9.3
 *
 * @package LearnDash\Instructor_Role
 */

namespace LearnDash\Instructor_Role\Modules\Instructor_Dashboard;

use StellarWP\Learndash\lucatume\DI52\ServiceProvider;

/**
 * Instructor Dashboard service provider class.
 *
 * @since 5.9.3
 */
class Provider extends ServiceProvider {
	/**
	 * Register service providers.
	 *
	 * @since 5.9.3
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->register( Manage_Instructor\Provider::class );
	}
}
