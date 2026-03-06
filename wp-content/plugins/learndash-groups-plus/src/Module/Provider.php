<?php
/**
 * Modules service provider class file.
 *
 * @since 2.1.2
 *
 * @package LearnDash\Groups_Plus
 */

namespace LearnDash\Groups_Plus\Module;

use LearnDash\Groups_Plus\lucatume\DI52\ServiceProvider;

/**
 * Modules service provider class.
 *
 * @since 2.1.2
 */
class Provider extends ServiceProvider {
	/**
	 * Register the service provider.
	 *
	 * @since 2.1.2
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->register( Notices\Provider::class );
	}
}
