<?php
/**
 * This class handles licensing
 *
 * @deprecated 5.9.1 This file is no longer in use.
 *
 * @package wisdmlabs-licensing
 */

namespace Licensing;

if ( ! class_exists( 'Licensing\WdmLicense' ) ) {
	/**
	 * This class is used to handle licensing
	 */
	class WdmLicense {

		/**
		 * This variable is used for plugin data
		 *
		 * @var array
		 */
		private static $plugin_data = array();

		/**
		 * In constructor following things are done.
		 *
		 * Constants are defined.
		 *
		 * Licensing classes are initialised.
		 *
		 * @param array $plugin_data Plugin Data.
		 */
		public function __construct( $plugin_data ) {
			$slug                       = $plugin_data['pluginSlug'];
			self::$plugin_data[ $slug ] = $plugin_data;

			// Constants.
			if ( ! defined( 'LICENSE_KEY' ) ) {
				define( 'LICENSE_KEY', '_license_key' );
			}

			if ( ! defined( 'VALID' ) ) {
				define( 'VALID', 'valid' );
			}

			if ( ! defined( 'EXPIRED' ) ) {
				define( 'EXPIRED', 'expired' );
			}

			if ( ! defined( 'INVALID' ) ) {
				define( 'INVALID', 'invalid' );
			}

			require_once 'class-wdm-send-customer-data.php';
			$send_data_to_server = new \Licensing\WdmSendDataToServer( $plugin_data );

			require_once 'class-wdm-add-license-data.php';
			$add_license_data = new \Licensing\WdmAddLicenseData( $plugin_data );

			$get_data_from_db = self::checkLicenseAvailiblity( $slug, false );
			if ( 'available' === $get_data_from_db ) {
				require_once 'class-wdm-plugin-updater.php';
				$plugin_updater = new \Licensing\wdmPluginUpdater( $plugin_data['baseFolderDir'] . '/' . $plugin_data['mainFileName'], $plugin_data );
			}

			$old_transient = get_transient( 'wdm_' . $slug . '_license_trans' );
			if ( $old_transient ) {
				delete_transient( 'wdm_' . $slug . '_license_trans' );
				self::set_version_info_cache( 'wdm_' . $slug . '_license_trans', 7, $old_transient );
			}

			unset( $add_license_data );
			unset( $send_data_to_server );
			unset( $plugin_updater );
		}

		/**
		 * This function is used to check license status
		 *
		 * @param string  $slug     PLugin slug.
		 * @param boolean $cache    should get data from cache.
		 * @return string
		 */
		public static function checkLicenseAvailiblity( $slug, $cache = true ) {
			require_once 'class-wdm-get-license-data.php';

			return \Licensing\WdmGetLicenseData::get_data_from_db( self::$plugin_data[ $slug ], $cache );
		}

		/**
		 * This function is used to get cached licensing info
		 *
		 * @param string $cache_key  Cache key.
		 * @return string cache info on success or false.
		 */
		public static function get_cached_version_info( $cache_key ) {
			$cache = get_option( $cache_key );
			if ( empty( $cache ) ) {
				return false;
			}
			if ( 0 !== $cache['timeout'] && ( empty( $cache['timeout'] ) || time() > $cache['timeout'] ) ) {
				return false; // Cache is expired.
			}

			return json_decode( $cache['value'] );
		}

		/**
		 * This function is used to set licensing cache
		 *
		 * @param string $cache_key    Cache Key.
		 * @param [type] $time         Time of expiry.
		 * @param string $value        License status.
		 * @return void
		 */
		public static function set_version_info_cache( $cache_key, $time, $value = '' ) {
			if ( 0 === $time ) {
				$time_out = 0;
			} else {
				$time_out = strtotime( '+' . $time . ' day', time() );
			}
			$data = array(
				'timeout' => $time_out,
				'value'   => wp_json_encode( $value ),
			);
			update_option( $cache_key, $data );
		}
	}
}
