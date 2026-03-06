<?php
/**
 * Functions for uninstall LearnDash LMS - Group Registration
 *
 * @since 4.3.15
 *
 * @package LearnDash\Seats_Plus
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'vendor-prefixed/autoload.php';

/**
 * Fires on plugin uninstall.
 *
 * @since 4.3.15
 *
 * @return void
 */
do_action( 'learndash_seats_plus_uninstall' );
