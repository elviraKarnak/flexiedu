<?php
/**
 * This class handles licensing
 *
 * @deprecated 5.9.1 This file is no longer in use.
 *
 * @package wisdmlabs-licensing
 */

namespace Licensing;

if ( ! class_exists( 'Licensing\WdmGetLicenseData' ) ) {
	/**
	 * This class is used to get license data
	 */
	class WdmGetLicenseData {

		/**
		 * This variable is used to store response data
		 *
		 * @var array
		 */
		private static $response_data = array();

		/**
		 * Retrieves licensing information from database. If valid information is not found, sends request to server to get info.
		 *
		 * @param array $plugin_data Plugin data.
		 * @param bool  $cache      When cache is true, it returns the value stored in static variable $response_data. When set to false, it forcefully retrieves value from database. Example: If you want to show plugin's settings page after activating license, then pass false, so that it will forcefully get the data from database.
		 *
		 * @return string returns 'available' if license is valid or expired else returns 'unavailable'
		 */
		public static function get_data_from_db( $plugin_data, $cache = true ) {
			$plugin_name = $plugin_data['pluginName'];
			$plugin_slug = $plugin_data['pluginSlug'];
			$store_url   = $plugin_data['storeUrl'];

			if ( isset( self::$response_data[ $plugin_slug ] ) && null !== self::$response_data[ $plugin_slug ] && true === $cache ) {
				return self::$response_data[ $plugin_slug ];
			}

			$license_transient = WdmLicense::get_cached_version_info( 'wdm_' . $plugin_slug . '_license_trans' );

			$license_status = get_option( 'edd_' . $plugin_slug . '_license_status' );
			if ( $license_transient || EXPIRED === $license_status ) {
				$license_status = get_option( 'edd_' . $plugin_slug . '_license_status' );
				$active_site    = self::get_site_list( $plugin_slug );

				self::set_response_data( $license_status, $active_site, $plugin_slug );

				return self::$response_data[ $plugin_slug ];
			}

			$license_key = trim( get_option( 'edd_' . $plugin_slug . LICENSE_KEY ) );

			if ( $license_key ) {
				self::check_license_on_server( $license_key, $plugin_name, $plugin_slug, $store_url, $plugin_data, $license_status );
			}

			return isset( self::$response_data[ $plugin_slug ] ) ? self::$response_data[ $plugin_slug ] : '';
		}

		/**
		 * Set license status response
		 * Set transient if set_transient parameter is true
		 *
		 * @param string  $license_status current license status.
		 * @param string  $active_site    Active sites.
		 * @param string  $plugin_slug    Plugin slug.
		 * @param boolean $set_transient  whether to set transient or not.
		 */
		public static function set_response_data( $license_status, $active_site, $plugin_slug, $set_transient = false ) {
			self::$response_data[ $plugin_slug ] = 'unavailable';

			if ( EXPIRED === $license_status && ( ! empty( $active_site ) || '' !== $active_site ) ) {
				self::$response_data[ $plugin_slug ] = 'unavailable';
			} elseif ( EXPIRED === $license_status || VALID === $license_status ) {
				self::$response_data[ $plugin_slug ] = 'available';
			}

			if ( $set_transient ) {
				if ( VALID === $license_status ) {
					$time = 7;
				} else {
					$time = 1;
				}
				WdmLicense::set_version_info_cache( 'wdm_' . $plugin_slug . '_license_trans', $time, $license_status );
			}
		}

		/**
		 * This function is used to get list of sites where license key is already acvtivated.
		 *
		 * @param type $plugin_slug current plugin's slug.
		 *
		 * @return string list of site
		 *
		 * @author Foram Rambhiya
		 */
		public static function get_site_list( $plugin_slug ) {
			$sites        = get_option( 'wdm_' . $plugin_slug . '_license_key_sites' );
			$max          = get_option( 'wdm_' . $plugin_slug . '_license_max_site' );
			$current_site = get_site_url();
			// EDD treats site with www as a different site. Solving this issue.
			$current_site = str_ireplace( 'www.', '', $current_site );
			$current_site = preg_replace( '#^https?://#', '', $current_site );

			$site_count  = 0;
			$active_site = '';

			if ( is_array( $sites ) && ( ! empty( $sites ) || '' !== $sites ) ) {
				foreach ( $sites as $key ) {
					if ( empty( $key ) ) {
						continue;
					}
					foreach ( $key as $value ) {
						$value = rtrim( $value, '/' );

						if ( 0 !== strcasecmp( $value, $current_site ) ) {
							$active_site .= '<li>' . $value . '</li>';
							++$site_count;
						}
					}
				}
			}

			if ( $site_count >= $max ) {
				return $active_site;
			} else {
				return '';
			}
		}

		/**
		 * This function is used to check license status from server.
		 *
		 * @param string $license_key       License key of plugin.
		 * @param string $plugin_name       Plugin name.
		 * @param string $plugin_slug       Plugin Slug.
		 * @param string $store_url         Store URL.
		 * @param array  $plugin_data        Plugin Data.
		 * @param string $license_status    License status.
		 * @return boolean
		 */
		public static function check_license_on_server( $license_key, $plugin_name, $plugin_slug, $store_url, $plugin_data, $license_status ) {
			$api_params = array(
				'edd_action'      => 'check_license',
				'license'         => $license_key,
				'item_name'       => rawurlencode( $plugin_name ),
				'current_version' => $plugin_data['pluginVersion'],
				'plugin_slug'     => $plugin_slug,
				'item_id'         => $plugin_data['itemId'],
			);

			$api_params = WdmSendDataToServer::get_analytics_data( $api_params, $license_key );

			$response = wp_remote_post(
				add_query_arg( $api_params, $store_url ),
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'blocking'  => true,
				)
			);

			if ( is_wp_error( $response ) ) {
				return false;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			$valid_response_code = array( '200', '301' );

			$current_response_code = wp_remote_retrieve_response_code( $response );

			if ( null === $license_data || ! in_array( $current_response_code, $valid_response_code, true ) ) {
				// if server does not respond, read current license information.
				$license_status = get_option( 'edd_' . $plugin_slug . '_license_status', '' );
				if ( empty( $license_data ) ) {
					WdmLicense::set_version_info_cache( 'wdm_' . $plugin_slug . '_license_trans', 1, 'server_did_not_respond' );
				}
			} else {
				include_once plugin_dir_path( __FILE__ ) . 'class-wdm-add-license-data.php';
				$license_status = WdmAddLicenseData::update_status( $license_data, $plugin_slug );
			}

			$active_site = self::get_site_list( $plugin_slug );

			self::set_response_data( $license_status, $active_site, $plugin_slug, true );
		}
	}
}
