<?php
/**
 * Quiz Admin provider class file.
 *
 * @since 5.9.7
 *
 * @package LearnDash\Instructor_Role
 */

namespace LearnDash\Instructor_Role\Admin\Quiz;

use StellarWP\Learndash\lucatume\DI52\ContainerException;
use StellarWP\Learndash\lucatume\DI52\ServiceProvider;

/**
 * Quiz Admin service provider class.
 *
 * @since 5.9.7
 */
class Provider extends ServiceProvider {
	/**
	 * Register service providers.
	 *
	 * @since 5.9.7
	 *
	 * @throws ContainerException If the service provider is not registered.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->hooks();
	}

	/**
	 * Hooks wrapper.
	 *
	 * @since 5.9.7
	 *
	 * @throws ContainerException If the service provider is not registered.
	 *
	 * @return void
	 */
	protected function hooks() {
		add_action(
			'admin_init',
			$this->container->callback( Assets::class, 'register_assets' ),
			10
		);

		add_action(
			'admin_enqueue_scripts',
			$this->container->callback( Assets::class, 'enqueue_assets' ),
			10
		);

		add_filter(
			'learndash_header_action_menu',
			$this->container->callback( Edit::class, 'add_frontend_edit_button_to_actions_dropdown' ),
			10,
			3
		);
	}
}
