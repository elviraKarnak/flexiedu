<?php
/**
 * Admin Notices service provider class file.
 *
 * @since 2.1.2
 *
 * @package LearnDash\Groups_Plus
 */

namespace LearnDash\Groups_Plus\Module\Notices;

use LearnDash\Groups_Plus\lucatume\DI52\ServiceProvider;

/**
 * Admin Notices service provider class.
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
		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 2.1.2
	 *
	 * @return void
	 */
	private function hooks(): void {
		add_action( 'admin_init', $this->container->callback( Notices::class, 'register_admin_notices' ) );
	}
}
