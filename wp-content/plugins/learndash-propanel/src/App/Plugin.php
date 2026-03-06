<?php
/**
 * Plugin service provider class file.
 *
 * @since 3.0.0
 *
 * @package LearnDash\Reports
 */

namespace LearnDash\Reports;

use StellarWP\Learndash\lucatume\DI52\ServiceProvider;
use StellarWP\Learndash\lucatume\DI52\ContainerException;

/**
 * Plugin service provider class.
 *
 * @since 3.0.0
 */
class Plugin extends ServiceProvider {
	/**
	 * Register service provider.
	 *
	 * @since 3.0.0
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
	 * Registers plugin general hooks.
	 *
	 * It only contains hooks related to the old plugin structure.
	 * New hooks should be added to the respective service provider.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	protected function hooks(): void {
		add_action( 'plugins_loaded', 'ldrp_include_files', 70 ); // Load after the Reports base (old Reports Free) plugin.
		add_action( 'init', 'ldrp_load_textdomain' );

		add_action( 'wp_enqueue_scripts', 'ldrp_load_common_assets' );
		add_action( 'wp_enqueue_scripts', 'ldrp_register_frontend_assets' );
		add_action( 'wp_enqueue_scripts', 'ldrp_localize_custom_settings' );
		add_action( 'admin_enqueue_scripts', 'ldrp_localize_custom_settings' );

		add_filter( 'wisdm_ld_reports_pro_version', '__return_true' );
		add_filter( 'the_content', 'show_statistic_detail_screen', 99999, 1 );

		add_shortcode( 'ldrp_quiz_reports', 'ldrp_quiz_reports_handler' );
	}
}
