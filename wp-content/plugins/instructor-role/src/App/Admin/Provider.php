<?php
/**
 * Admin provider class file.
 *
 * @since 5.9.2
 *
 * @package LearnDash\Instructor_Role
 */

namespace LearnDash\Instructor_Role\Admin;

use LearnDash\Instructor_Role\Admin\Role;
use LearnDash_Settings_Section;
use StellarWP\Learndash\lucatume\DI52\ContainerException;
use StellarWP\Learndash\lucatume\DI52\ServiceProvider;

/**
 * Admin service provider class.
 *
 * @since 5.9.2
 */
class Provider extends ServiceProvider {
	/**
	 * Register service providers.
	 *
	 * @since 5.9.2
	 *
	 * @throws ContainerException If the service provider is not registered.
	 *
	 * @return void
	 */
	public function register(): void {
		// Ensure that the Instance is only created once, using Core's methods to create and return the instance.
		$this->container->singleton(
			Translation::class,
			function () {
				Translation::add_section_instance();

				return LearnDash_Settings_Section::get_section_instance( Translation::class );
			}
		);

		$this->container->register( Quiz\Provider::class );

		$this->hooks();
	}

	/**
	 * Hooks wrapper.
	 *
	 * @since 5.9.2
	 *
	 * @throws ContainerException If the service provider is not registered.
	 *
	 * @return void
	 */
	public function hooks() {
		// Translation.

		add_action(
			'init',
			$this->container->callback(
				Translation::class,
				'add_section_instance'
			)
		);

		// Role.

		add_filter(
			'learndash_notes_allowed_roles',
			$this->container->callback(
				Role::class,
				'add_notes_capabilities'
			)
		);

		// Assets.

		add_action(
			'admin_init',
			$this->container->callback( Assets::class, 'register_assets' )
		);
	}
}
