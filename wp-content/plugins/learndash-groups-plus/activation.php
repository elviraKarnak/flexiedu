<?php
/**
 * Plugin activation file for LearnDash LMS - Groups Plus.
 *
 * @since 1.0
 *
 * @package LearnDash\Groups_Plus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_activation_hook( LEARNDASH_GROUPS_PLUS_PLUGIN_FILE, function () {
	$message = 'Unfortunately, it appears that the authentication token you have provided is invalid. We kindly request that you re-download the package and attempt to install again. If the issue persists, please do not hesitate to contact our support team for further assistance.';

	if ( ! file_exists( LEARNDASH_GROUPS_PLUS_DIR . '/auth-token.php' ) ) {
		die( $message );
	}

	$auth_token = include_once LEARNDASH_GROUPS_PLUS_DIR . '/auth-token.php';
	if ( empty( $auth_token ) ) {
		die( $message );
	}
	$response = wp_remote_post(
		LICENSING_SITE_URL,
		array(
			'body' => array(
				'site_url'   => site_url(),
				'auth_token' => $auth_token,
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		die( $response->get_error_message() );
	}
	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );
	if ( wp_remote_retrieve_response_code( $response ) === 200 ) {
		update_option( 'learndash_groups_plus_license', $data );
	} else {
		$error_message = $data['message'] ?? $message;
		die( $error_message );
	}
} );
