<?php
/**
 * Functions for uninstall ProPanel
 *
 * @since 3.0.0
 *
 * @package LearnDash\Reports
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'vendor-prefixed/autoload.php';

/**
 * Fires on plugin uninstall.
 *
 * @since 3.0.0
 *
 * @return void
 */
do_action( 'learndash_reports_uninstall' );
