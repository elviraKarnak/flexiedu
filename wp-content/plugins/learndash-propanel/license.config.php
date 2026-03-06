<?php
/**
 * Remove comments after integration. Comments are just for reference.
 *
 * @package WisdmLabs/Licensing.
 *
 * @deprecated 1.8.2 This file is no longer in use.
 */

_deprecated_file( __FILE__, '1.8.2' );

$dependencies      = array( 'learndash', 'learndash-reports-by-wisdmlabs' );
$inst_dependencies = array();
if ( ! empty( $dependencies ) && function_exists( 'get_plugin_data' ) ) {
	foreach ( $dependencies as $plugin ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		if ( 'learndash' === strval( $plugin ) ) {
			$inst_dependencies[ $plugin ] = get_plugin_data( WP_PLUGIN_DIR . '/sfwd-lms/sfwd_lms.php' )['Version'];
		} else {
			if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin ) ) {
				$inst_dependencies[ $plugin ] = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin . '/' . $plugin . '.php' )['Version'];
			}
		}
	}
}
$str      = get_home_url();
$site_url = preg_replace( '#^https?://#', '', $str );
return array(
	/**
	* Plugins short name appears on the License Menu Page
	*/
	'pluginShortName'   => 'WISDM Reports for LearnDash PRO',

	/**
	 * This slug is used to store the data in db. License is checked using two options viz edd_<slug>_license_key and edd_<slug>_license_status
	 */
	'pluginSlug'        => 'learndash-reports-pro',

	/**
	 * Download Id on EDD Server
	 */
	'itemId'            => 707478, // Need to assign EDD download ID here.

	/**
	 * Current Version of the plugin. This should be similar to Version tag mentioned in Plugin headers
	 */
	'pluginVersion'     => LDRP_PLUGIN_VERSION,

	/**
	 * Under this Name product should be created on WisdmLabs Site
	 */
	'pluginName'        => 'WISDM Reports for LearnDash PRO',

    /**
     * Url where program pings to check if update is available and license validity
     * plugins using storeUrl "https://store.wisdmlabs.com/check-update" or anything similar should change that to "https://store.wisdmlabs.com" to avoid future issues.
     */
    'storeUrl' => 'https://store.wisdmlabs.com/license-check/',

	/**
	 * Url where program pings to check if update is available and license validity
	 * plugins using storeUrl "https://wisdmlabs.com/check-update" or anything similar should change that to "https://wisdmlabs.com" to avoid future issues.
	 */
	'storeUrl'          => 'https://wisdmlabs.com/license-check/',

	/**
	 * Site url which will pass in API request.
	 */
	'siteUrl'           => $site_url,

	/**
	 * Author Name
	 */
	'authorName'        => 'WisdmLabs',

	/**
	 * Text Domain used for translation
	 */
	'pluginTextDomain'  => 'learndash-reports-pro',

	/**
	 * Base Url for accessing Files
	 * if code is integrated in theme use 'get_template_directory_uri' function
	 * default is plugins_url('/', __FILE__) for plugins.
	 */
	'baseFolderUrl'     => plugins_url( '/', __FILE__ ),

	/**
	 * Base Directory path for accessing Files
	 * if code is integrated in theme use 'untrailingslashit(get_template_directory())' function
	 * default is untrailingslashit(plugin_dir_path(__FILE__)),
	 */
	'baseFolderDir'     => untrailingslashit( plugin_dir_path( __FILE__ ) ),

	/**
	 * Set true if theme
	 */
	'isTheme'           => false,

	/**
	 *  Changelog page link for theme
	 *  should be false for plugin
	 *  eg : https://wisdmlabs.com/elumine/documentation/
	 */
	'themeChangelogUrl' => 'https://wisdmlabs.com/elumine/documentation/change-log/',

	/**
	 * Plugin Main file name
	 */
	'mainFileName'      => 'learndash-reports-pro.php',

	/**
	 * Dependent plugins for your plugin
	 * pass the value in array where plugin name will be key and version number will be value
	 * Do not hard code version. Version should be the current version of dependency fetched dynamically.
	 * Supported plugin names
	 * woocommerce
	 * learndash
	 * wpml
	 * unyson
	 */
	'dependencies'      => $inst_dependencies,
);
