<?php
/**
 * Plugin Name: Instructor Role
 * Plugin URI: https://learndash.com/instructor-role-by-learndash
 * Description: This extension adds a user role 'Instructor' into your WordPress website and provides capabilities to create courses content and track student progress in your LearnDash LMS.
 * Version: 5.9.13
 * Requires at least: 6.6
 * Tested up to: 6.9.1
 * Requires PHP: 7.4
 * Author: LearnDash
 * Author URI: https://learndash.com
 * Text Domain: wdm_instructor_role
 * Domain Path: /languages
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor-prefixed/autoload.php';

use LearnDash\Instructor_Role\Activation;
use LearnDash\Instructor_Role\Deactivation;
use LearnDash\Instructor_Role\Dependency_Checker;
use LearnDash\Instructor_Role\Plugin;
use LearnDash\Instructor_Role\Licensing\Migration as License_Migration;
use LearnDash\Instructor_Role\StellarWP\Assets;

/**
 * Set Plugin Version
 *
 * @since 3.5.0
 */
if ( ! defined( 'INSTRUCTOR_ROLE_PLUGIN_VERSION' ) ) {
	define( 'INSTRUCTOR_ROLE_PLUGIN_VERSION', '5.9.13' );
}

/**
 * Plugin dir path Constant
 *
 * @since 3.1.0
 */
if ( ! defined( 'INSTRUCTOR_ROLE_ABSPATH' ) ) {
	define( 'INSTRUCTOR_ROLE_ABSPATH', plugin_dir_path( __FILE__ ) );
}

/**
 * Plugin BaseName Constant
 *
 * @since 3.1.1
 */
if ( ! defined( 'INSTRUCTOR_ROLE_BASE' ) ) {
	define( 'INSTRUCTOR_ROLE_BASE', plugin_basename( __FILE__ ) );
}

/**
 * Set the plugin slug as default text domain.
 *
 * @since 3.5.0
 */
if ( ! defined( 'INSTRUCTOR_ROLE_TXT_DOMAIN' ) ) {
	define( 'INSTRUCTOR_ROLE_TXT_DOMAIN', 'wdm_instructor_role' );
}

/**
 * Define the main plugin file.
 *
 * @since 5.9.1
 */
if ( ! defined( 'INSTRUCTOR_ROLE_PLUGIN_FILE' ) ) {
	define( 'INSTRUCTOR_ROLE_PLUGIN_FILE', __FILE__ );
}

/**
 * Define LearnDash Licensing URL.
 *
 * @since 5.9.1
 */
if ( ! defined( 'INSTRUCTOR_ROLE_LICENSING_SITE_URL' ) ) {
	define( 'INSTRUCTOR_ROLE_LICENSING_SITE_URL', 'https://checkout.learndash.com/wp-json/learndash/v2/site/auth_token' );
}

/**
 * Define LearnDash Check Licensing URL.
 *
 * @since 5.9.1
 */
if ( ! defined( 'INSTRUCTOR_ROLE_LICENSING_CHECK_LICENSE_URL' ) ) {
	define( 'INSTRUCTOR_ROLE_LICENSING_CHECK_LICENSE_URL', 'https://checkout.learndash.com/wp-json/learndash/v2/site/auth' );
}

register_activation_hook( INSTRUCTOR_ROLE_PLUGIN_FILE, [ Activation::class, 'run' ] );
register_deactivation_hook( INSTRUCTOR_ROLE_PLUGIN_FILE, [ Deactivation::class, 'run' ] );

add_action( 'admin_notices', [ License_Migration::class, 'run' ] );

require INSTRUCTOR_ROLE_ABSPATH . 'includes/class-instructor-role.php';

/**
 * Begins execution of the plugin.
 *
 * @since 3.5.0
 *
 * @return void
 */
function run_instructor_role() { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- Legacy function.
	$plugin = new \InstructorRole\Includes\Instructor_Role();
	$plugin->run();
}

add_action(
	'plugins_loaded',
	function () {
		// We need to load the translations manually in this plugin to prevent warnings on WP 6.7+.
		learndash_instructor_role_load_translations();

		// Set plugin dependencies.
		Dependency_Checker::get_instance()->set_dependencies(
			[
				'sfwd-lms/sfwd_lms.php' => [
					'label'            => '<a href="https://www.learndash.com" target="_blank">' . __( 'LearnDash LMS', 'wdm_instructor_role' ) . '</a>',
					'class'            => 'SFWD_LMS',
					'version_constant' => 'LEARNDASH_VERSION',
					'min_version'      => '4.7.0',
				],
			]
		);

		Dependency_Checker::get_instance()->set_message(
			esc_html__( 'Instructor Role requires the following plugin(s) to be active:', 'wdm_instructor_role' )
		);
	},
	1 // High priority to make sure the dependencies are set.
);

add_action(
	'plugins_loaded', // It would be great to refactor the plugin, so we can use the 'learndash_init' hook.
	function () {
		// If plugin requirements aren't met, don't run anything else to prevent possible fatal errors.
		if (
			! Dependency_Checker::get_instance()->check_dependency_results()
			|| php_sapi_name() === 'cli'
		) {
			return;
		}

		Assets\Config::set_hook_prefix( 'learndash_instructor_role' );
		Assets\Config::set_path( INSTRUCTOR_ROLE_ABSPATH );
		Assets\Config::set_version( INSTRUCTOR_ROLE_PLUGIN_VERSION );

		Assets\Config::set_relative_asset_path( 'dist/' );

		run_instructor_role(); // The legacy code should be run first.
		learndash_register_provider( Plugin::class );
	},
	50
);

/**
 * Load the plugin translations.
 *
 * @since 5.9.5
 * @since 5.9.8 Added support for WordPress 6.8+.
 *
 * @see SFWD_LMS::i18nize() // cSpell:disable-line
 *
 * @return void
 */
function learndash_instructor_role_load_translations(): void {
	$plugin_basename = trailingslashit( plugin_basename( constant( 'INSTRUCTOR_ROLE_ABSPATH' ) ) );
	$relative_path   = $plugin_basename . 'languages';
	$absolute_path   = trailingslashit( constant( 'WP_PLUGIN_DIR' ) ) . $relative_path;
	$text_domain     = constant( 'INSTRUCTOR_ROLE_TXT_DOMAIN' );

	/**
	 * If we're running on a version of WordPress prior to 6.7,
	 * we can use load_plugin_textdomain() at all times without issue.
	 *
	 * This will properly load from the global WordPress languages directory instead if a matching file exists.
	 */
	if (
		version_compare(
			get_bloginfo( 'version' ),
			'6.7',
			'<'
		)
	) {
		load_plugin_textdomain( $text_domain, false, $relative_path );

		return;
	}

	$wordpress_languages_directory = trailingslashit( constant( 'WP_LANG_DIR' ) ) . 'plugins/';

	$mo_file_name = $text_domain . '-' . determine_locale() . '.mo';

	// Prioritize the WordPress languages directory.
	$mo_file_path = $wordpress_languages_directory . $mo_file_name;

	// Fallback to LearnDash plugin location.
	if ( ! file_exists( $mo_file_path ) ) {
		$mo_file_path = trailingslashit( $absolute_path ) . $mo_file_name;
	}

	/**
	 * Filter the path to the .mo file to use for LearnDash.
	 *
	 * @since 5.9.5
	 *
	 * @param string $mo_file_path Full path to the .mo file.
	 * @param string $mo_file_name Name of the .mo file.
	 * @param string $locale       Locale.
	 *
	 * @return string
	 */
	$mo_file_path = apply_filters(
		'learndash_instructor_role_mo_file_path',
		$mo_file_path,
		$mo_file_name,
		determine_locale()
	);

	if ( file_exists( $mo_file_path ) ) {
		// If the .mo file does not exist, load_plugin_textdomain() will show a PHP notice on WordPress 6.7+.
		load_plugin_textdomain( $text_domain, false, $relative_path );
	} else {
		/**
		 * This fixes an issue with WordPress 6.8+ support.
		 *
		 * If the file doesn't exist, we need to fake a loaded translation to prevent
		 * _load_textdomain_just_in_time() from running.
		 *
		 * Using NOOP_Translations will prevent any translations from running, but if a translation file does
		 * not exist, this would be expected functionality anyway.
		 */
		global $l10n;
		$l10n[ $text_domain ] = new NOOP_Translations(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Expected, see above.
	}

	/**
	 * Workaround for WordPress 6.7+ support.
	 *
	 * Pre-WP 6.7, load_plugin_textdomain() would run load_textdomain() for us instead of using
	 * _load_textdomain_just_in_time().
	 *
	 * As we're loading many things that use translation methods such as `__()` prior to the `init` hook,
	 * we need to do this to ensure our translations are loaded correctly in WordPress 6.7+.
	 */
	load_textdomain( $text_domain, $mo_file_path );
}
