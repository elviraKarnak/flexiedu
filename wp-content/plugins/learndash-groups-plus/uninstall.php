<?php
/**
 * Functions for uninstall LearnDash LMS - Groups Plus
 *
 * @since 2.1.2
 *
 * @package LearnDash\Groups_Plus
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'vendor-prefixed/autoload.php';

/**
 * Fires on plugin uninstall.
 *
 * @since 2.1.2
 *
 * @return void
 */
do_action( 'learndash_groups_plus_uninstall' );
