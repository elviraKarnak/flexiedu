<?php
/**
 * Admin provider class file.
 *
 * @since 2.1.3
 *
 * @package LearnDash\Groups_Plus
 */

namespace LearnDash\Groups_Plus\Admin;

use LearnDash\Groups_Plus\lucatume\DI52\ServiceProvider;
use LearnDash\Groups_Plus\lucatume\DI52\ContainerException;
use LearnDash_Settings_Section;

/**
 * Admin service provider class.
 *
 * @since 2.1.3
 */
class Provider extends ServiceProvider {
	/**
	 * Register service providers.
	 *
	 * @since 2.1.3
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

		$this->hooks();
	}

	/**
	 * Hooks wrapper.
	 *
	 * @since 2.1.3
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
	}
}
