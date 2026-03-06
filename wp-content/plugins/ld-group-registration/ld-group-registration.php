<?php
/**
 * Plugin Name: LearnDash LMS - Group Registration
 * Plugin URI: https://go.learndash.com/groupreg
 * Description: Allows Group leaders to purchase a course (or courses) on behalf of students, and then enroll members to the course.
 * Version: 4.3.17
 * Requires PHP: 7.4
 * Requires at least: 6.6
 * Tested up to: 6.8.2
 * Author: LearnDash
 * Author URI: https://learndash.com
 * Text Domain: wdm_ld_group
 * Domain Path: /languages
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor-prefixed/autoload.php';

use LearnDash\Seats_Plus\Activation;
use LearnDash\Seats_Plus\Dependency_Checker;
use LearnDash\Seats_Plus\Plugin;
use LearnDash\Seats_Plus\Licensing\Migration as License_Migration;

/**
 * Set Plugin Version
 */
define( 'LD_GROUP_REGISTRATION_VERSION', '4.3.17' );

/**
 * Set Default Plugin File Path Constant
 */
if ( ! defined( 'WDM_LDGR_PLUGIN_FILE' ) ) {
	define( 'WDM_LDGR_PLUGIN_FILE', __FILE__ );
}

/**
 * Set the plugin slug as default text domain.
 */
if ( ! defined( 'WDM_LDGR_TXT_DOMAIN' ) ) {
	define( 'WDM_LDGR_TXT_DOMAIN', 'wdm_ld_group' );
}

/**
 * Set Default Plugin Directory Path Constant
 */
if ( ! defined( 'WDM_LDGR_PLUGIN_DIR' ) ) {
	define( 'WDM_LDGR_PLUGIN_DIR', __DIR__ );
}

/**
 * Define LearnDash Licensing URL.
 *
 * @since 4.3.14
 */
if ( ! defined( 'WDM_LDGR_LICENSING_SITE_URL' ) ) {
	define( 'WDM_LDGR_LICENSING_SITE_URL', 'https://checkout.learndash.com/wp-json/learndash/v2/site/auth_token' );
}

/**
 * Define LearnDash Check Licensing URL.
 *
 * @since 4.3.14
 */
if ( ! defined( 'WDM_LDGR_LICENSING_CHECK_LICENSE_URL' ) ) {
	define( 'WDM_LDGR_LICENSING_CHECK_LICENSE_URL', 'https://checkout.learndash.com/wp-json/learndash/v2/site/auth' );
}

register_activation_hook( WDM_LDGR_PLUGIN_FILE, [ Activation::class, 'run' ] );

add_action( 'admin_notices', [ License_Migration::class, 'run' ] );

/**
 * Begins execution of the plugin.
 */
function run_ld_group_registration() {
	$plugin = new \LdGroupRegistration\Includes\Ld_Group_Registration();
	$plugin->run();
}

$learndash_seats_plus_dependency_checker = Dependency_Checker::get_instance();
$learndash_seats_plus_dependency_checker->set_dependencies(
	[
		'sfwd-lms/sfwd_lms.php' => [
			'label'            => '<a href="https://www.learndash.com" target="_blank">LearnDash LMS</a>',
			'class'            => 'SFWD_LMS',
			'version_constant' => 'LEARNDASH_VERSION',
			'min_version'      => '4.7.0',
		],
	]
);

require plugin_dir_path( __FILE__ ) . 'includes/class-ld-group-registration.php';

add_action(
	'plugins_loaded',
	function () {
		$learndash_seats_plus_dependency_checker = Dependency_Checker::get_instance();

		// If plugin requirements aren't met, don't run anything else to prevent possible fatal errors.
		if ( ! $learndash_seats_plus_dependency_checker->check_dependency_results() || php_sapi_name() === 'cli' ) {
			return;
		}

		run_ld_group_registration();
		learndash_register_provider( Plugin::class );
	},
	50 // It must be greater than 11 to ensure that dependencies are loaded and validated by the Dependency checker. Using 50 to give an extra space for other customizations.
);

/**
 * Set the message for the dependency checker after init so text domains are loaded.
 */
add_action(
	'init',
	function () {
		$learndash_seats_plus_dependency_checker = Dependency_Checker::get_instance();
		$learndash_seats_plus_dependency_checker->set_message(
			esc_html__( 'LearnDash LMS - Group Registration requires the following plugin(s) to be active:', 'wdm_ld_group' )
		);
	}
);
