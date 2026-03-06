<?php
/**
 * Functions for uninstall Instructor role.
 *
 * @since 5.9.2
 *
 * @package LearnDash\Instructor_Role
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'vendor-prefixed/autoload.php';

/**
 * Fires on plugin uninstall.
 *
 * @since 5.9.2
 *
 * @return void
 */
do_action( 'learndash_instructor_role_uninstall' );
