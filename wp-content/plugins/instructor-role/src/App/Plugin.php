<?php
/**
 * Plugin service provider class file.
 *
 * @since 5.9.2
 *
 * @package LearnDash\Instructor_Role
 */

namespace LearnDash\Instructor_Role;

use StellarWP\Learndash\lucatume\DI52\ServiceProvider;
use StellarWP\Learndash\lucatume\DI52\ContainerException;
use LearnDash\Instructor_Role\StellarWP\DB\DB;

/**
 * Plugin service provider class.
 *
 * @since 5.9.2
 */
class Plugin extends ServiceProvider {
	/**
	 * Register service provider.
	 *
	 * @since 5.9.2
	 *
	 * @throws ContainerException If the service provider is not registered.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->register( Admin\Provider::class );
		$this->container->register( Modules\Provider::class );

		$this->hooks();
	}

	/**
	 * Hooks wrapper.
	 *
	 * @since 5.9.2
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_action(
			'plugins_loaded',
			[ DB::class, 'init' ],
			20
		);

		add_action(
			'init',
			$this->container->callback(
				Assets::class,
				'register'
			)
		);
	}
}
