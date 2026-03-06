<?php
/**
 * Migration module provider.
 *
 * @since 3.0.0
 *
 * @package LearnDash\Reports
 */

namespace LearnDash\Reports\Modules\Migration;

use StellarWP\Learndash\lucatume\DI52\ContainerException;
use StellarWP\Learndash\lucatume\DI52\ServiceProvider;

/**
 * Migration module provider.
 *
 * @since 3.0.0
 */
class Provider extends ServiceProvider {
	/**
	 * Registers the migration provider bindings.
	 *
	 * @since 3.0.0
	 *
	 * @throws ContainerException If the container is not set.
	 *
	 * @return void
	 */
	public function register() {
		$this->container->register( Notices\Provider::class );
	}
}
