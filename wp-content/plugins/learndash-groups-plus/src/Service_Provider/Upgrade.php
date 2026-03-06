<?php
/**
 * Upgrade process Service Provider.
 *
 * @since 1.1.0
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore WPINC
 */

namespace LearnDash\Groups_Plus\Service_Provider;

if ( ! defined( 'WPINC' ) ) {
    exit;
}

use LearnDash\Groups_Plus\lucatume\DI52\ServiceProvider;

/**
 * Plugin upgrade service provider class.
 * 
 * @since 1.1.0
 */
class Upgrade extends ServiceProvider {
    /**
     * Register service provider.
     *
     * @since 1.1.0
     * 
     * @return void
     */
    public function register(): void {
        $version = LEARNDASH_GROUPS_PLUS_VERSION;
        $version = str_replace( '.', '_', $version );

        $this->container->singleton( 'LearnDash\Groups_Plus\Module\Upgrade\To_' . $version );
    }
}
