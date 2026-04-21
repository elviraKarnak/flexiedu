<?php
/**
 * Functions for uninstall Gradebook by LearnDash
 *
 * @since 4.3.2
 *
 * @package LearnDash\Gradebook
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'vendor-prefixed/autoload.php';

/**
 * Fires on plugin uninstall.
 *
 * @since 4.3.2
 *
 * @return void
 */
do_action( 'learndash_gradebook_uninstall' );
