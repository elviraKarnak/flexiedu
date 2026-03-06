<?php
/**
 * Handles Plugin Activation logic
 *
 * @since 4.3.14
 *
 * @package LearnDash\Seats_Plus
 */

namespace LearnDash\Seats_Plus;

/**
 * Plugin Activation class.
 *
 * @since 4.3.14
 */
class Activation {
	/**
	 * Runs an authentication check. If the check fails, the plugin is deactivated and an error message is shown.
	 *
	 * @since 4.3.14
	 *
	 * @return void
	 */
	public static function run(): void {
		$result = Licensing\Authentication::verify_token();

		if ( ! is_wp_error( $result ) ) {
			return;
		}

		deactivate_plugins( plugin_basename( WDM_LDGR_PLUGIN_FILE ) );

		die( esc_html( $result->get_error_message() ) );
	}
}
