<?php
/**
 * Handles Plugin License Authentication logic.
 *
 * @since 1.8.2
 *
 * @package LearnDash\Reports
 */

namespace LearnDash\Reports\Licensing;

use WP_Error;

/**
 * Plugin License Authentication class.
 *
 * @since 1.8.2
 */
class Authentication {
	/**
	 * Option Key where we store our License Data on successful authentication.
	 *
	 * @since 1.8.2
	 *
	 * @var string
	 */
	public const LICENSE_DATA_OPTION_KEY = 'learndash_reports_license';

	/**
	 * Plugin slug to send with our API request.
	 *
	 * @since 1.8.2
	 *
	 * @var string
	 */
	public const PLUGIN_SLUG = 'learndash-reports-pro';

	/**
	 * Verifies the included Auth-Token against the licensing server.
	 *
	 * @since 1.8.2
	 *
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	public static function verify_token() {
		$message = __( 'Unfortunately, it appears that the authentication token you have provided is invalid. We kindly request that you re-download the package and attempt to install again. If the issue persists, please do not hesitate to contact our support team for further assistance.', 'wdm_learndash_reports' );

		if ( ! file_exists( dirname( __DIR__, 3 ) . '/auth-token.php' ) ) {
			$auth_token = Migration::maybe_generate_token();
		} else {
			$auth_token = include_once dirname( __DIR__, 3 ) . '/auth-token.php';
		}

		// If we don't have an Auth Token, create a WP_Error.
		if ( empty( $auth_token ) ) {
			$auth_token = new WP_Error( 403, $message );
		}

		if ( is_wp_error( $auth_token ) ) {
			return $auth_token;
		}

		$response = wp_remote_post(
			self::get_auth_token_check_url(),
			[
				'body' => [
					'auth_token'  => $auth_token,
					'site_url'    => site_url(),
					'plugin_slug' => self::PLUGIN_SLUG,
				],
			]
		);
		$body = wp_remote_retrieve_body( $response );

		if ( is_string( $body ) ) {
			$body = (array) json_decode( $body, true );
		}

		if (
			! empty( $body['subscription_type'] )
			&& $body['subscription_type'] === 'not_found'
		) {
			$response = new WP_Error( 403, $message );
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			$error_message = $body['message'] ?? $message;

			if ( ! is_scalar( $error_message ) ) {
				$error_message = $message;
			} else {
				$error_message = strval( $error_message );
			}

			return new WP_Error( 403, $error_message );
		}

		update_option( self::LICENSE_DATA_OPTION_KEY, $body );

		/**
		 * Clear out a saved error message from a failed token generation to prevent confusion.
		 * This is especially important if support has the user re-download the ZIP,
		 * which should include a valid Auth Token by default.
		 */
		Migration::clear_notice();

		return true;
	}

	/**
	 * Retrieves the Auth Token check URL.
	 *
	 * @since 3.0.1
	 *
	 * @return string
	 */
	private static function get_auth_token_check_url(): string {
		if ( defined( 'LDRP_LICENSING_SITE_URL' ) ) {
			return constant( 'LDRP_LICENSING_SITE_URL' );
		}

		return 'https://checkout.learndash.com/wp-json/learndash/v2/site/auth_token';
	}
}
