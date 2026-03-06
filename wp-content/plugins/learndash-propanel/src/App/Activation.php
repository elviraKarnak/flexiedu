<?php
/**
 * Handles Plugin Activation logic
 *
 * @since 1.8.2
 *
 * @package LearnDash\Reports
 */

namespace LearnDash\Reports;

/**
 * Plugin Activation class.
 *
 * @since 1.8.2
 */
class Activation {
	/**
	 * Runs an authentication check. If the check fails, the plugin is deactivated and an error message is shown.
	 *
	 * @since 1.8.2
	 *
	 * @return void
	 */
	public static function run(): void {
		$result = Licensing\Authentication::verify_token();

		if ( ! is_wp_error( $result ) ) {
			return;
		}

		deactivate_plugins( plugin_basename( dirname( __DIR__, 2 ) . '/learndash_propanel.php' ) );

		die( esc_html( $result->get_error_message() ) );
	}
}
