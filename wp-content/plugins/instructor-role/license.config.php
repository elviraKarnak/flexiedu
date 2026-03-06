<?php
/**
 * Remove comments after integration. Comments are just for reference.
 *
 * @package WisdmLabs/Licensing.
 *
 * @deprecated 5.9.1 This file is no longer in use.
 */

// get site url
// Do not change this lines
$str      = get_home_url();
$site_url = preg_replace( '#^https?://#', '', $str );

if ( ! function_exists( 'wdm_get_ir_usage_data' ) ) {
	/**
	 * Get analytics data
	 *
	 * @return array
	 */
	function wdm_get_ir_usage_data() {
		$payout_used = false;

		// Check cache.
		$plugin_data = wp_cache_get( 'ir_usage_data' );

		if ( false !== $plugin_data ) {
			return $plugin_data;
		}

		// To get user Ids of instructors.
		$args                        = array(
			'fields' => array( 'ID', 'display_name', 'user_email' ),
			'role'   => 'wdm_instructor',
		);
		$instructors                 = get_users( $args );
		$instructor_commission_count = 0;

		foreach ( $instructors as $instructor ) {
			$commission_percent = get_user_meta( $instructor->ID, 'wdm_commission_percentage', true );

			if ( '' !== $commission_percent && '0' !== $commission_percent && 0 !== $commission_percent ) {
				$instructor_commission_count++;
			}
		}

		$settings = ir_get_settings();

		if ( ( '' !== get_option( 'ir_payout_client_id' ) && false !== get_option( 'ir_payout_client_id' ) ) && ( '' !== get_option( 'ir_payout_client_secret_key' ) && false !== get_option( 'ir_payout_client_secret_key' ) ) ) {
			$payout_used = true;

		}

		$plugin_data = array(
			'instructor_total'           => count( $instructors ),
			'instructor_with_commission' => $instructor_commission_count,
			'settings'                   => $settings,
			'payout_used'                => $payout_used,

		);

		// Cache data for a week.
		wp_cache_set( 'ir_usage_data', $plugin_data, 'instructor_role', WEEK_IN_SECONDS );

		return $plugin_data;
	}
}

return array(
	/*
	 * Plugins short name appears on the License Menu Page
	 */
	'pluginShortName'     => 'Instructor Role',

	/*
	 * this slug is used to store the data in db. License is checked using two options viz edd_<slug>_license_key and edd_<slug>_license_status
	 */
	'pluginSlug'          => 'instructor_role',

	/*
	 * Download Id on EDD Server(1234 is dummy id please use your plugins ID)
	 */
	'itemId'              => 20277,

	/*
	 * Current Version of the plugin. This should be similar to Version tag mentioned in Plugin headers
	 */
	'pluginVersion'       => '5.9.0',

	/*
	 * Under this Name product should be created on WisdmLabs Site
	 */
	'pluginName'          => 'Instructor Role',

	/*
	 * Url where program pings to check if update is available and license validity
	 * plugins using store_url "https://wisdmlabs.com" or anything similar should change that to "https://wisdmlabs.com/license-check/" to avoid future issues.
	 */
	'storeUrl'            => 'https://store.wisdmlabs.com/license-check/',

	/**
	 * Site url which will pass in API request.
	 */
	'siteUrl'             => $site_url,

	/*
	 * Author Name
	 */
	'authorName'          => 'LearnDash',

	/*
	 * Text Domain used for translation
	 */
	'pluginTextDomain'    => 'wdm_instructor_role',

	/*
	 * Base Url for accessing Files
	 * Change if not accessing this file from main file
	 */
	'baseFolderUrl'       => plugins_url( '/', __FILE__ ),

	/*
	 * Base Directory path for accessing Files
	 * Change if not accessing this file from main file
	 */
	'baseFolderDir'       => untrailingslashit( plugin_dir_path( __FILE__ ) ),

	/*
	 * Plugin Main file name
	 * example : product-enquiry-pro.php
	 */
	'mainFileName'        => 'instructor.php',

	/**
	 * Set true if theme
	 */
	'isTheme'             => false,

	/**
	*  Changelog page link for theme
	*  should be false for plugin
	*  eg : https://wisdmlabs.com/elumine/documentation/
	*/
	'theme_changelog_url' => false,

	/*
	 * Dependent plugins for your plugin
	 * pass the value in array where plugin name will be key and version number will be value
	 * Do not hard code version. Version should be the current version of dependency fetched dynamically.
	 * In given example WC_VERSION is constant defined by woocommerce for version. Check how you can get version dynamically of other dependent plugins
	 * Supported plugin names
	 * woocommerce
	 * learndash
	 * wpml
	 * unyson
	 */
	'dependencies'        => array(
		'learndash' => defined( 'LEARNDASH_VERSION' ) ? LEARNDASH_VERSION : '',
	),

	'plugin_data'         => wdm_get_ir_usage_data(),

	/*
	 * Url where program sends analytics data
	 */
	'usage_url'           => 'https://store.wisdmlabs.com/wp-json/wisdm_products_api/send_usage_data/',



	/**
	 * Sample code if your dependent plugins are not compulsory
	* Please create the following function to fetch dependencies for a theme/plugin.
	* if (!function_exists('wdm_get_active_dependencies')) {
		   function wdm_get_active_dependencies()
		   {
			   $dependencies = array();
			   include_once(ABSPATH . 'wp-admin/includes/plugin.php');
			   if (is_plugin_active('woocommerce/woocommerce.php')) {
				   $dependencies[] = 'woocommerce';
			   }
			   if (is_plugin_active('buddypress/bp-loader.php')) {
				   $dependencies[] = 'buddypress';
			   }
			   if (is_plugin_active('badgeos/badgeos.php')) {
				   $dependencies[] = 'badgeos';
			   }
			   if (is_plugin_active('bbpress/bbpress.php')) {
				   $dependencies[] = 'bbpress';
			   }
			   if (is_plugin_active('sfwd-lms/sfwd_lms.php')) {
				   $dependencies[] = 'learndash';
			   }
			   if (is_plugin_active('unyson/unyson.php')) {
				   $dependencies[] = 'unyson';
			   }
			   return $dependencies;
		   }
	   }
	*/
);
