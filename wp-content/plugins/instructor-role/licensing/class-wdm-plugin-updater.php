<?php
/**
 * This class handles licensing
 * 
 * @deprecated 5.9.1 This file is no longer in use.
 *
 * @package wisdmlabs-licensing
 */

namespace Licensing;

/*
 * Allows plugins to use their own update API.
 */
if ( ! class_exists( 'Licensing\WdmPluginUpdater' ) ) {
	/**
	 * This class is responsible to handle product updates.
	 */
	class WdmPluginUpdater {

		/**
		 * This is for server url
		 *
		 * @var string
		 */
		private $api_url = '';
		/**
		 * This is for api data
		 *
		 * @var array
		 */
		private $api_data = array();
		/**
		 * This is for product name
		 *
		 * @var string
		 */
		private $name = '';
		/**
		 * This is for product slug
		 *
		 * @var string
		 */
		private $slug = '';
		/**
		 * This is for product version
		 *
		 * @var string
		 */
		private $version = '';
		/**
		 * Undocumented variable
		 *
		 * @var boolean
		 */
		private $wp_override = true;
		/**
		 * This is for license status cache option key
		 *
		 * @var string
		 */
		private $cache_key = '';
		/**
		 * This is for product license key
		 *
		 * @var string
		 */
		private $license = '';
		/**
		 * This is for server response data
		 *
		 * @var string
		 */
		private $response_data;

		/**
		 * Class constructor.
		 *
		 * @uses plugin_basename()
		 * @uses hook()
		 *
		 * @param string $plugin_file Path to the plugin file.
		 * @param array  $api_data    Optional data to send with API calls.
		 */
		public function __construct( $plugin_file, $api_data = null ) {
			global $edd_plugin_data;
			$this->api_url  = trailingslashit( $api_data['storeUrl'] );
			$this->api_data = urlencode_deep( $api_data );
			if ( ! isset( $api_data['isTheme'] ) || ! $api_data['isTheme'] ) {
				$this->name         = plugin_basename( $plugin_file );
				$this->slug         = $api_data['pluginSlug'];
				$this->product_type = 'plugin';
			} else {
				$this->name           = $api_data['pluginSlug'];
				$this->slug           = $api_data['pluginSlug'];
				$this->product_type   = 'theme';
				$this->changelog_link = array_key_exists( 'theme_changelog_url', $api_data ) ? $api_data['theme_changelog_url'] : '';
			}
			$this->version                  = $api_data['pluginVersion'];
			$this->wp_override              = ! isset( $api_data['wp_override'] ) || (bool) $api_data['wp_override'];
			$this->license                  = trim( get_option( 'edd_' . urldecode_deep( $this->slug ) . LICENSE_KEY ) );
			$this->cache_key                = md5( serialize( $this->slug . $this->license ) );
			$edd_plugin_data[ $this->slug ] = $this->api_data;

			// Set up hooks.
			$this->hook();
		}

		/**
		 * Set up WordPress filters to hook into WP's update process.
		 *
		 * @uses add_filter()
		 */
		private function hook() {
			if ( 'theme' === $this->product_type ) {
				add_filter( 'pre_set_site_transient_update_themes', array( $this, 'pre_set_site_transient_update_plugins_filter' ) );
				add_filter( 'pre_set_transient_update_themes', array( $this, 'pre_set_site_transient_update_plugins_filter' ) );
				add_filter( 'themes_api', array( $this, 'plugins_api_filter' ), 10, 3 );
			} elseif ( 'plugin' === $this->product_type ) {
				add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'pre_set_site_transient_update_plugins_filter' ) );
				add_filter( 'pre_set_transient_update_plugins', array( $this, 'pre_set_site_transient_update_plugins_filter' ) );
				add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
			}
		}

		/**
		 * Check for Updates at the defined API endpoint and modify the update array.
		 *
		 * This function dives into the update api just when WordPress creates its update array,
		 * then adds a custom API call and injects the custom plugin data retrieved from the API.
		 * It is reassembled from parts of the native WordPress plugin update code.
		 * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
		 *
		 * @uses api_request()
		 *
		 * @param array $transient_data Update array build by WordPress.
		 *
		 * @return array Modified update array with custom plugin data.
		 */
		public function pre_set_site_transient_update_plugins_filter( $transient_data ) {
			global $pagenow;
			if ( ! is_object( $transient_data ) ) {
				$transient_data = new \stdClass();
			}

			if ( 'plugins.php' === $pagenow && is_multisite() ) {
				return $transient_data;
			}
			if ( ! empty( $transient_data->response ) && ! empty( $transient_data->response[ $this->name ] ) && false === $this->wp_override ) {
				return $transient_data;
			}

			$version_info = $this->get_cached_version_info();
			if ( false === $version_info || empty( $version_info ) ) {
				$version_info = $this->api_request( array( 'slug' => $this->slug ) );
				$this->set_version_info_cache( $version_info );
			}

			return $this->get_updated_transient_data( $transient_data, $version_info );
		}

		/**
		 * This function is used to gettransient data
		 *
		 * @param object $transient_data    Transient data.
		 * @param object $version_info      Current version info.
		 * @return object
		 */
		public function get_updated_transient_data( $transient_data, $version_info ) {
			if ( false !== $version_info && is_object( $version_info ) && isset( $version_info->new_version ) ) {
				if ( version_compare( $this->version, $version_info->new_version, '<' ) ) {
					if ( 'theme' === $this->product_type ) {
						$version_info        = (array) $version_info;
						$version_info['url'] = $this->changelog_link;
					}
					$transient_data->response[ $this->name ] = $version_info;
				}
				$transient_data->last_checked           = time();
				$transient_data->checked[ $this->name ] = $this->version;
			}

			return $transient_data;
		}

		/**
		 * Updates information on the "View version x.x details" page with custom data.
		 *
		 * @uses api_request()
		 *
		 * @param mixed  $data     Data.
		 * @param string $action   action to be performed.
		 * @param object $args     additional arguments required for action.
		 *
		 * @return object $data
		 */
		public function plugins_api_filter( $data, $action = '', $args = null ) {
			if ( 'theme' === $this->product_type ) {
				$action_type = 'theme_information';
			}

			if ( 'plugin' === $this->product_type ) {
				$action_type = 'plugin_information';
			}

			if ( $action_type !== $action || ! isset( $args->slug ) || ( $args->slug !== $this->slug ) ) {
				return $data;
			}

			$to_send               = array(
				'slug'   => $this->slug,
				'is_ssl' => is_ssl(),
				'fields' => array(
					'banners' => false, // These will be supported soon hopefully.
					'reviews' => false,
				),
			);
			$api_request_cache_key = 'edd_api_request_' . md5( serialize( $this->slug . $this->license ) );
			// Get the transient where we store the api request for this plugin for 24 hours.
			$edd_api_request_transient = $this->get_cached_version_info( $api_request_cache_key );
			// If we have no transient-saved value, run the API, set a fresh transient with the API value, and return that value too right now.
			if ( empty( $edd_api_request_transient ) ) {
				$api_response = $this->api_request( $to_send );
				// Expires in 6 hours.
				$this->set_version_info_cache( $api_response, $api_request_cache_key );
				if ( false !== $api_response ) {
					$data = $api_response;
				}
			}

			// Convert sections into an associative array, since we're getting an object, but Core expects an array.
			if ( isset( $data->sections ) && ! is_array( $data->sections ) ) {
				$new_sections = array();
				foreach ( $data->sections as $key => $value ) {
					$new_sections[ $key ] = $value;
				}
				$data->sections = $new_sections;
			}

			return $data;
		}

		/**
		 * Calls the API and, if successfull, returns the object delivered by the API.
		 *
		 * @uses get_bloginfo()
		 * @uses wp_remote_get()
		 * @uses is_wp_error()
		 *
		 * @param array $data   Parameters for the API action.
		 *
		 * @return false||object
		 */
		private function api_request( $data ) {
			if ( null !== $this->response_data && ! empty( $this->response_data ) ) {
				return $this->response_data;
			}

			$data = array_merge( $this->api_data, $data );

			$license_key = trim( get_option( 'edd_' . urldecode_deep( $data['pluginSlug'] ) . LICENSE_KEY ) );

			if ( $data['slug'] !== $this->slug || trailingslashit( home_url() ) === $this->api_url || empty( $license_key ) ) {
				return;
			}

			$api_params = array(
				'edd_action'      => 'get_version',
				'license'         => $license_key,
				'slug'            => $this->slug,
				'author'          => $data['authorName'],
				'current_version' => $this->version,
				'url'             => home_url(),
			);

			if ( $data['itemId'] ) {
				$api_params['item_id'] = $data['itemId'];
			}

			$api_params = WdmSendDataToServer::get_analytics_data( $api_params, $license_key );

			$request = wp_remote_post(
				add_query_arg( $api_params, $this->api_url ),
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'blocking'  => true,
				)
			);

			if ( ! is_wp_error( $request ) ) {
				$request = json_decode( wp_remote_retrieve_body( $request ) );
			}

			if ( $request && isset( $request->sections ) ) {
				$request->sections = maybe_unserialize( $request->sections );
			} else {
				$request = false;
			}

			$this->response_data = $request;

			return $request;
		}

		/**
		 * This function is used to get updated cache value.
		 *
		 * @return object
		 */
		public function get_update_cache() {
			$update_cache = get_site_transient( 'update_plugins' );
			return is_object( $update_cache ) ? $update_cache : new \stdClass();
		}

		/**
		 * This function is used to get cache infor
		 *
		 * @param string $cache_key   cache key.
		 * @return json
		 */
		public function get_cached_version_info( $cache_key = '' ) {
			if ( empty( $cache_key ) ) {
				$cache_key = $this->cache_key;
			}
			$cache = get_option( $cache_key );
			if ( empty( $cache['timeout'] ) || time() > $cache['timeout'] ) {
				return false; // Cache is expired.
			}

			return json_decode( $cache['value'] );
		}

		/**
		 * This function is used to set cache
		 *
		 * @param string $value        license value.
		 * @param string $cache_key    Cache key.
		 * @return void
		 */
		public function set_version_info_cache( $value = '', $cache_key = '' ) {
			if ( empty( $cache_key ) ) {
				$cache_key = $this->cache_key;
			}
			$data = array(
				'timeout' => strtotime( '+6 hours', time() ),
				'value'   => wp_json_encode( $value ),
			);
			update_option( $this->cache_key, $data );
		}
	}
}
