<?php
/**
 * Reports base module provider.
 *
 * @since 3.0.0
 *
 * @package LearnDash\Reports
 */

namespace LearnDash\Reports\Modules\Reports_Base;

use StellarWP\Learndash\lucatume\DI52\ContainerException;
use StellarWP\Learndash\lucatume\DI52\ServiceProvider;

/**
 * Reports base module provider.
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
			'plugins_loaded',
			$this->container->callback( Reports_Free::class, 'deactivate' ),
			51 // immediately after the plugin is loaded.
		);

		add_action(
			'plugins_loaded',
			$this->container->callback( Reports_Free::class, 'load' ),
			52
		);

		add_action(
			'admin_notices',
			$this->container->callback( Reports_Free::class, 'add_admin_notice_reports_free_deactivated' )
		);
	}
}
