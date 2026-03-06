<?php
/**
 * Migration notices module provider.
 *
 * @since 3.0.0
 *
 * @package LearnDash\Reports
 */

namespace LearnDash\Reports\Modules\Migration\Notices;

use StellarWP\Learndash\lucatume\DI52\ContainerException;
use StellarWP\Learndash\lucatume\DI52\ServiceProvider;

/**
 * Migration notices module provider.
 *
 * @since 3.0.0
 */
class Provider extends ServiceProvider {
	/**
	 * Registers service providers.
	 *
	 * @since 3.0.0
	 *
	 * @throws ContainerException If the container cannot be resolved.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->hooks();
	}

	/**
	 * Registers WordPress hooks.
	 *
	 * @since 3.0.0
	 *
	 * @throws ContainerException If the container cannot be resolved.
	 *
	 * @return void
	 */
	protected function hooks(): void {
		add_action(
			'admin_notices',
			$this->container->callback( Propanel_Rebranding::class, 'add_admin_notice' )
		);
	}
}
