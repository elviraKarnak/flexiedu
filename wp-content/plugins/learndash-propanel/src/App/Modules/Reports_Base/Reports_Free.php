<?php
/**
 * Supports the incorporation of the free version of this plugin.
 *
 * @since 3.0.0
 *
 * @package LearnDash\Reports
 */

namespace LearnDash\Reports\Modules\Reports_Base;

/**
 * Supports the incorporation of the free version of this plugin.
 *
 * @since 3.0.0
 */
class Reports_Free {
	/**
	 * Reports Free plugin directory path constant.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private const PLUGIN_PATH_CONSTANT = 'WRLD_REPORTS_FILE';

	/**
	 * Reports Free plugin directory name default.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private const PLUGIN_DIRECTORY_DEFAULT = 'wisdm-reports-for-learndash';

	/**
	 * Reports Free plugin file name.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private const PLUGIN_FILE = 'learndash-reports-by-wisdmlabs.php';

	/**
	 * Admin notice transient key for the Reports Free deactivated notice.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private const ADMIN_NOTICE_TRANSIENT_KEY = 'ld_reports_free_deactivated_notice';

	/**
	 * If an installation of the Reports Free plugin is found on the site, deactivate it.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function deactivate(): void {
		if ( ! $this->is_free_plugin_active() ) {
			return;
		}

		deactivate_plugins( $this->get_free_plugin_path() );

		// Create a transient to display an admin notice in the next page load.
		set_transient( self::ADMIN_NOTICE_TRANSIENT_KEY, true, DAY_IN_SECONDS );

		$referrer             = wp_get_referer();
		$redirect_destination = $this->get_redirect( $referrer );

		if ( $redirect_destination === false ) {
			return;
		}

		// Force a redirect to unload the legacy plugin right away.
		wp_safe_redirect( $redirect_destination );

		exit;
	}

	/**
	 * If the Reports Free plugin has not been loaded (via the free plugin, or otherwise) load it.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function load(): void {
		if ( $this->is_loaded() ) {
			return;
		}

		$reports_module_path = LDRP_PLUGIN_DIR . '/includes/reports-base/' . self::PLUGIN_FILE;

		if ( ! file_exists( $reports_module_path ) ) {
			return;
		}

		require_once $reports_module_path;
	}

	/**
	 * Adds an admin notice to inform the user that the Reports Free plugin has been deactivated.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function add_admin_notice_reports_free_deactivated() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! get_transient( self::ADMIN_NOTICE_TRANSIENT_KEY ) ) {
			return;
		}

		delete_transient( self::ADMIN_NOTICE_TRANSIENT_KEY );

		?>
		<div class="notice notice-info is-dismissible">
			<p><?php esc_html_e( 'Reports Free is no longer required and has been deactivated.', 'learndash-reports-pro' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Determines whether the Free Plugin is active.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	private function is_free_plugin_active(): bool {
		$reports_free_path = $this->get_free_plugin_path();

		return is_plugin_active( $reports_free_path )
			|| is_plugin_active_for_network( $reports_free_path );
	}

	/**
	 * Retrieves the path to the Free Plugin.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	private function get_free_plugin_path(): string {
		return (string) preg_replace(
			'/^' .
			preg_quote(
				trailingslashit(
					wp_normalize_path( constant( 'WP_PLUGIN_DIR' ) )
				),
				'/'
			) .
			'/',
			'',
			wp_normalize_path(
				defined( self::PLUGIN_PATH_CONSTANT )
					? constant( self::PLUGIN_PATH_CONSTANT )
					: trailingslashit( self::PLUGIN_DIRECTORY_DEFAULT ) . self::PLUGIN_FILE
			)
		);
	}

	/**
	 * Determines whether the code is already loaded.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	private function is_loaded(): bool {
		return defined( self::PLUGIN_PATH_CONSTANT );
	}

	/**
	 * Given a referrer, determine where they should be redirected to after deactivating the legacy ProPanel plugin.
	 *
	 * @since 3.0.0
	 *
	 * @param string|false $referrer Referrer. Should match valid output of wp_get_referer().
	 *
	 * @return string|false Redirect destination. Empty string to refresh the page, false when they should not be redirected at all.
	 */
	private function get_redirect( $referrer ) {
		if ( $referrer === false ) {
			return '';
		}

		$redirect_to_plugins_list = [
			admin_url( 'update.php' ), // Updating by uploading a ZIP.
			admin_url( 'update-core.php' ), // Updating via the "Update Plugins" workflow in the WordPress Dashboard.
		];

		/**
		 * Some cases are special and we don't want to redirect.
		 *
		 * Particularly, any time when an update is applied via AJAX (LearnDash LMS -> Add-ons and inline updates on
		 * the Plugins List page) could cause the user to be "trapped" until they attempt to navigate away twice unless
		 * we specifically ensure these referrers are not redirected.
		 */
		$never_redirect = [
			add_query_arg( // Updating via LearnDash LMS -> Add-ons.
				[
					'page' => 'learndash-hub',
				],
				admin_url( 'admin.php' )
			),
			admin_url( 'plugins.php' ), // Updating inline or via bulk actions on the Plugins List page.
		];

		$check_array_for_referrer = function ( $search_array ) use ( $referrer ) {
			return ! empty(
				array_filter(
					array_map(
						function ( $current_referrer ) use ( $referrer ) {
							return strpos( $referrer, $current_referrer ) !== false;
						},
						$search_array
					)
				)
			);
		};

		if ( $check_array_for_referrer( $redirect_to_plugins_list ) ) {
			return admin_url( 'plugins.php' );
		}

		if ( $check_array_for_referrer( $never_redirect ) ) {
			return false;
		}

		return $referrer;
	}
}
