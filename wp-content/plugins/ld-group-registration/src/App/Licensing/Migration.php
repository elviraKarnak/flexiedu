<?php
/**
 * Automatically attempts to migrate a saved WisdmLabs License Key to a LearnDash Auth Token.
 * On failure, a persistent notice will be created displaying an error message.
 * This only runs once, regardless of the result.
 * If a future Authentication succeeds, the persistent notice is automatically hidden.
 *
 * @since 4.3.14
 *
 * @package LearnDash\Seats_Plus
 *
 * cspell:ignore scontact
 */

namespace LearnDash\Seats_Plus\Licensing;

use WP_Error;

/**
 * Plugin Legacy License Migration class.
 *
 * @since 4.3.14
 */
class Migration {
	/**
	 * Notice Key to use to store our generated Notice text, to remove the need to query the API every time.
	 * This also is used to dismiss the Notice.
	 *
	 * @since 4.3.14
	 *
	 * @var string
	 */
	private const NOTICE_KEY = 'ld_seats_plus_wisdmlabs_license_migration_error';

	/**
	 * Database Option Key where we store a flag to ensure we only run this once.
	 *
	 * @since 4.3.14
	 *
	 * @var string
	 */
	private const UPGRADE_RAN_OPTION_KEY = 'ld_seats_plus_wisdmlabs_license_migration_ran';

	/**
	 * Legacy License Key option key.
	 *
	 * @since 4.3.14
	 *
	 * @var string
	 */
	private const LEGACY_LICENSE_OPTION_KEY = 'edd_wdm_ld_group_license_key';

	/**
	 * Attempt to convert an existing Legacy License Key to an Auth Token.
	 * On failure, show a persistent notice to let the User know that the failure has occurred.
	 *
	 * @since 4.3.14
	 *
	 * @return void
	 */
	public static function run(): void {
		// If we have a stored error message, grab it now.
		$notice_text = get_option( self::NOTICE_KEY, '' );
		if ( is_scalar( $notice_text ) ) {
			$notice_text = strval( $notice_text );
		} else {
			$notice_text = '';
		}

		// If we haven't attempted to convert the Legacy License Key yet, try it now.
		if ( empty( get_option( self::UPGRADE_RAN_OPTION_KEY ) ) ) {
			$result = self::maybe_generate_token();

			// Regardless of the result, set this flag.
			update_option( self::UPGRADE_RAN_OPTION_KEY, time() );

			if ( ! is_wp_error( $result ) ) {
				// Delete the saved notice text, in case it exists.
				delete_option( self::NOTICE_KEY );

				return;
			}

			$notice_text = $result->get_error_message();

			// Store the error message for future use.
			update_option( self::NOTICE_KEY, $notice_text );
		}

		// We don't have an error to show, so don't create a Notice.
		if ( empty( $notice_text ) ) {
			return;
		}

		printf(
			'<div class="notice notice-error %1$s" data-id="%2$s"><h2 class="notice-title">%3$s</h2><p>%4$s</p></div>',
			'learndash-seats-plus-notice',
			esc_attr( self::NOTICE_KEY ),
			esc_html__( 'LearnDash Group Registration: Authentication Error', 'wdm_ld_group' ),
			wp_kses(
				$notice_text,
				[
					'a' => [
						'href'   => [],
						'target' => [],
					],
				]
			)
		);
	}

	/**
	 * Generate an Access Token based on a legacy License Key.
	 *
	 * @since 4.3.14
	 *
	 * @return false|string|WP_Error False if we shouldn't attempt to generate an Auth Token, String Auth Token on Success, WP_Error on failure.
	 */
	public static function maybe_generate_token() {
		$license_key = get_option( self::LEGACY_LICENSE_OPTION_KEY );

		if ( ! $license_key ) {
			return false;
		}

		// Sanity check.
		if ( file_exists( WDM_LDGR_PLUGIN_DIR . '/auth-token.php' ) ) {
			$auth_token = include_once WDM_LDGR_PLUGIN_DIR . '/auth-token.php';

			if ( ! empty( $auth_token ) ) {
				return $auth_token;
			}
		}

		$error_message = sprintf(
			// translators: placeholders: HTML for a link to the support page.
			__( 'There was an issue migrating your WisdmLabs license key to a LearnDash authentication token. If the issue persists, please do not hesitate to %1$scontact our support team%2$s for further assistance.', 'wdm_ld_group' ),
			'<a href="https://account.learndash.com/?tab=support" target="_blank">',
			'</a>'
		);

		// Now that we have all the information we need, we can attempt to convert this License Key into an Auth Token.
		$response = wp_safe_remote_post(
			WDM_LDGR_LICENSING_CHECK_LICENSE_URL,
			[
				'body'    => [
					'license_key' => $license_key,
					'site_url'    => site_url(),
					'plugin_slug' => Authentication::PLUGIN_SLUG,
				],
				'timeout' => 10,
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			$message = $data['message'] ?? $error_message;

			if ( ! is_scalar( $message ) ) {
				$message = $error_message;
			} else {
				$message = strval( $message );
			}

			return new WP_Error( 403, wp_kses_post( $message ) );
		}

		if ( empty( $data['token'] ) ) {
			return new WP_Error( 403, wp_kses_post( $error_message ) );
		}

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once trailingslashit( ABSPATH ) . 'wp-admin/includes/file.php';
		}

		global $wp_filesystem;

		// Obtain file write permissions.
		if ( ! WP_Filesystem( request_filesystem_credentials( '' ) ) ) {
			return new WP_Error( 403, wp_kses_post( $error_message ) );
		}

		$token = $data['token'];
		if ( is_scalar( $token ) ) {
			$token = strval( $token );
		} else {
			return new WP_Error( 403, wp_kses_post( $error_message ) );
		}

		// Store the Auth Token for future use if the plugin is deactivated and reactivated.
		if (
			! $wp_filesystem->put_contents(
				WDM_LDGR_PLUGIN_DIR . '/auth-token.php',
				"<?php return \"{$token}\";",
				FS_CHMOD_FILE
			)
		) {
			return new WP_Error( 403, wp_kses_post( $error_message ) );
		}

		// We have no need for this anymore.
		delete_option( self::LEGACY_LICENSE_OPTION_KEY );

		return $token;
	}

	/**
	 * Deletes an existing Notice.
	 *
	 * @since 4.3.14
	 *
	 * @return void
	 */
	public static function clear_notice(): void {
		delete_option( self::NOTICE_KEY );
	}
}
