<?php
/**
 * Handles Plugin License Authentication logic.
 *
 * @since 4.3.14
 *
 * @package LearnDash\Seats_Plus
 */

namespace LearnDash\Seats_Plus\Licensing;

use WP_Error;

/**
 * Plugin License Authentication class.
 *
 * @since 4.3.14
 */
class Authentication {
	/**
	 * Option Key where we store our License Data on successful authentication.
	 *
	 * @since 4.3.14
	 *
	 * @var string
	 */
	public const LICENSE_DATA_OPTION_KEY = 'learndash_seats_plus_license';

	/**
	 * Plugin slug to send with our API request.
	 *
	 * @since 4.3.14
	 *
	 * @var string
	 */
	public const PLUGIN_SLUG = 'ld-group-registration';

	/**
	 * Verifies the included Auth-Token against the licensing server.
	 *
	 * @since 4.3.14
	 *
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	public static function verify_token() {
		$message = __( 'Unfortunately, it appears that the authentication token you have provided is invalid. We kindly request that you re-download the package and attempt to install again. If the issue persists, please do not hesitate to contact our support team for further assistance.', 'wdm_ld_group' );

		if ( ! file_exists( WDM_LDGR_PLUGIN_DIR . '/auth-token.php' ) ) {
			$auth_token = Migration::maybe_generate_token();
		} else {
			$auth_token = include_once WDM_LDGR_PLUGIN_DIR . '/auth-token.php';
		}

		// If we don't have an Auth Token, create a WP_Error.
		if ( empty( $auth_token ) ) {
			$auth_token = new WP_Error( 403, $message );
		}

		if ( is_wp_error( $auth_token ) ) {
			return $auth_token;
		}

		$response = wp_remote_post(
			WDM_LDGR_LICENSING_SITE_URL,
			[
				'body' => [
					'auth_token'  => $auth_token,
					'site_url'    => site_url(),
					'plugin_slug' => self::PLUGIN_SLUG,
				],
			]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = (array) json_decode( $body, true );

		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			$error_message = $data['message'] ?? $message;

			if ( ! is_scalar( $error_message ) ) {
				$error_message = $message;
			} else {
				$error_message = strval( $error_message );
			}

			return new WP_Error( 403, $error_message );
		}

		update_option( self::LICENSE_DATA_OPTION_KEY, $data );

		/**
		 * Clear out a saved error message from a failed token generation to prevent confusion.
		 * This is especially important if support has the user re-download the ZIP,
		 * which should include a valid Auth Token by default.
		 */
		Migration::clear_notice();

		return true;
	}
}
