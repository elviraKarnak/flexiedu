<?php
/**
 * Provider for modules.
 *
 * @since 3.0.0
 *
 * @package LearnDash\Reports
 */

namespace LearnDash\Reports\Modules;

use StellarWP\Learndash\lucatume\DI52\ServiceProvider;
use StellarWP\Learndash\lucatume\DI52\ContainerException;

/**
 * Provider class for modules.
 *
 * @since 3.0.0
 */
class Provider extends ServiceProvider {
	/**
	 * Registers the service provider bindings.
	 *
	 * @since 3.0.0
	 *
	 * @throws ContainerException If the container is not set.
	 *
	 * @return void
	 */
	public function register() {
		$this->container->register( Reports_Base\Provider::class );
		$this->container->register( Migration\Provider::class );
	}
}
