<?php
/**
 * Plugin service provider class file.
 *
 * @since 4.3.15
 *
 * @package LearnDash\Seats_Plus
 */

namespace LearnDash\Seats_Plus;

use StellarWP\Learndash\lucatume\DI52\ServiceProvider;
use StellarWP\Learndash\lucatume\DI52\ContainerException;

/**
 * Plugin service provider class.
 *
 * @since 4.3.15
 */
class Plugin extends ServiceProvider {
	/**
	 * Register service provider.
	 *
	 * @since 4.3.15
	 *
	 * @throws ContainerException If the service provider is not registered.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->register( Admin\Provider::class );
	}
}
