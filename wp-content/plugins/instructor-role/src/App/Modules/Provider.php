<?php
/**
 * Modules provider class file.
 *
 * @since 5.9.3
 *
 * @package LearnDash\Instructor_Role
 */

namespace LearnDash\Instructor_Role\Modules;

use StellarWP\Learndash\lucatume\DI52\ServiceProvider;

/**
 * Modules service provider class.
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
		$this->container->register( Instructor_Dashboard\Provider::class );
	}
}
