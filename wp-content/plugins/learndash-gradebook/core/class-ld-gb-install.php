<?php
/**
 * Installs the plugin.
 *
 * @since 1.2.0
 *
 * @package LearnDash_Gradebook
 */

defined( 'ABSPATH' ) || die;

/**
 * Class LD_GB_Install
 *
 * Installs the plugin.
 *
 * @since 1.2.0
 */
class LD_GB_Install {
	/**
	 * Loads the install functions.
	 *
	 * @since 1.2.0
	 */
	static function install() {
		try {
			update_option( 'learndash_gradebook_db_version', '1.0.0' );

			self::authenticate();

			self::setup_capabilities();
		} catch ( Exception $exception ) {
			deactivate_plugins( plugin_basename( LEARNDASH_GRADEBOOK_FILE ) );
			die( $exception->getMessage() );
		}   }

	/**
	 * Validate a stored Auth Token, or attempt to grab one via a stored License Key (For past Real Big Plugins customers)
	 *
	 * @access public
	 * @since 4.2.0
	 * @return void
	 */
	public static function authenticate() {
		$error_message = sprintf(
			// translators: placeholders: HTML for a link to the support page.
			__( 'Unfortunately, it appears that the authentication token you have provided for Gradebook by LearnDash is invalid. We kindly request that you re-download the package and attempt to install again. If the issue persists, please do not hesitate to %1$scontact our support team%2$s for further assistance.', 'learndash-gradebook' ),  // cspell: disable-line -- That's why I don't like the practice to include HTML in translations.
			'<a href="https://account.learndash.com/?tab=support" target="_blank">',
			'</a>'
		);

		try {
			if ( ! file_exists( LEARNDASH_GRADEBOOK_DIR . '/auth-token.php' ) ) {
				$auth_token = self::maybe_generate_token();
			} else {
				$auth_token = include_once LEARNDASH_GRADEBOOK_DIR . '/auth-token.php';
			}

			if ( empty( $auth_token ) ) {
				throw new Exception( $error_message );
			}

			$response = wp_safe_remote_post(
				LEARNDASH_GRADEBOOK_LICENSING_SITE_URL,
				[
					'body'    => [
						'site_url'   => site_url(),
						'auth_token' => $auth_token,
					],
					'timeout' => 10,
				]
			);

			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );

			if ( wp_remote_retrieve_response_code( $response ) === 200 ) {
				update_option( 'learndash_gradebook_license', $data );
			} else {
				$error_message = $data['message'] ?? $error_message;
				throw new Exception( $error_message );
			}

			// Clear out any saved Auth Error.
			$saved_notices = get_option( 'ld_gb_notices' );
			$saved_notices = json_decode( $saved_notices, true );

			if ( ! isset( $saved_notices['auth_error'] ) ) {
				return;
			}

			unset( $saved_notices['auth_error'] );

			update_option( 'ld_gb_notices', json_encode( $saved_notices ) );

			delete_option( 'ld_gb_auth_error' );
		} catch ( Exception $exception ) {
			delete_option( 'learndash_gradebook_license' );
			throw $exception;
		}   }

	/**
	 * Generate an Access Token based on a legacy License Key
	 *
	 * @throws Exception       On failure communicating with the Real Big Plugins website
	 *
	 * @access public
	 * @since 4.2.0
	 * @return string|boolean  String Access Token on Success. False if we don't need to proceed.
	 */
	public static function maybe_generate_token() {
		$license_key = get_option( 'learndash_gradebook_license_key' );

		if ( ! $license_key ) {
			return false;
		}

		if ( file_exists( LEARNDASH_GRADEBOOK_DIR . '/auth-token.php' ) ) {
			$auth_token = include_once LEARNDASH_GRADEBOOK_DIR . '/auth-token.php';

			if ( ! empty( $auth_token ) ) {
				return $auth_token;
			}
		}

		$error_message = sprintf(
			// translators: placeholders: HTML for a link to the support page.
			__( 'There was an issue converting your Real Big Plugins license key to a LearnDash authentication token. If the issue persists, please do not hesitate to %1$scontact our support team%2$s for further assistance.', 'learndash-gradebook' ), // cspell: disable-line -- That's why I don't like the practice to include HTML in translations.
			'<a href="https://account.learndash.com/?tab=support" target="_blank">',
			'</a>'
		);

		// In order to hit the necessary LearnDash API Endpoint, we need the Email Address, which we have only stored in a transient.
		$license_data = get_transient( 'learndash_gradebook_license_data' );

		if ( empty( $license_data ) ) {
			$response = wp_safe_remote_get(
				'https://realbigplugins.com/',
				[
					'body'    => [
						'edd_action' => 'check_license',
						'license'    => $license_key,
						'url'        => site_url(),
						'item_id'    => 695,
					],
					'timeout' => 10,
				]
			);

			if ( is_wp_error( $response ) ) {
				throw new Exception( $error_message );
			}

			if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
				throw new Exception( $error_message );
			}

			$body         = wp_remote_retrieve_body( $response );
			$license_data = json_decode( $body, true );
		}

		if ( empty( $license_data ) ) {
			throw new Exception( $error_message );
		}

		// This License Key is no longer Active, so don't try to proceed.
		if ( isset( $license_data['error'] ) || ! in_array(
			$license_data['license'],
			[
				'valid',
				'inactive',
			]
		) ) {
			return false;
		}

		if ( ! isset( $license_data['customer_email'] ) || ! trim( $license_data['customer_email'] ) ) {
			throw new Exception( $error_message );
		}

		$email = $license_data['customer_email'];

		// Now that we have all the information we need, we can attempt to convert this License Key into an Auth Token.
		$response = wp_safe_remote_post(
			LEARNDASH_GRADEBOOK_LICENSING_CHECK_LICENSE_URL,
			[
				'body'    => [
					'license_key' => $license_key,
					'email'       => $email,
					'site_url'    => site_url(),
				],
				'timeout' => 10,
			]
		);

		if ( is_wp_error( $response ) ) {
			throw new Exception( $response->get_error_message() );
		}

		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! isset( $data['token'] ) || ! $data['token'] ) {
			return false;
		}

		// We have no need for this anymore.
		delete_option( 'learndash_gradebook_license_key' );
		delete_transient( 'learndash_gradebook_license_data' );

		// Store for future use if the plugin is deactivated and reactivated.
		file_put_contents( LEARNDASH_GRADEBOOK_DIR . '/auth-token.php', "<?php return \"{$data['token']}\";" );

		return $data['token'];  }

	/**
	 * Sets up custom capabilities
	 *
	 * @since 1.2.0
	 * @access private
	 */
	public static function setup_capabilities() {
		foreach ( ld_gb_get_capabilities() as $role_ID => $capabilities ) {
			$role = get_role( $role_ID );

			if ( ! $role ) {
				continue;
			}

			foreach ( $capabilities as $capability ) {
				if ( ! $role->has_cap( $capability ) ) {
					$role->add_cap( $capability );
				}
			}
		}
	}
}
