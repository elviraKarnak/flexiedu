<?php
/**
 * This class handles licensing
 * 
 * @deprecated 5.9.1 This file is no longer in use.
 *
 * @package wisdmlabs-licensing
 */

namespace Licensing;

if ( ! class_exists( 'Licensing\WdmSendDataToServer' ) ) {
	/**
	 * This class is responsible to handle customer data.
	 */
	class WdmSendDataToServer {
		/**
		 * Slug to be used in url and functions name
		 *
		 * @var string
		 */
		private $plugin_slug = '';

		/**
		 * Text domain to be used for translations
		 *
		 * @var string
		 */
		private $plugin_text_domain = '';

		/**
		 * Base folder URL
		 *
		 * @var string
		 */
		private $base_folder_url = '';

		/**
		 * Dependencies to be used for translations
		 *
		 * @var string
		 */
		private static $dependencies = '';

		/**
		 * Plugin Data to be used for plugin specific data
		 *
		 * @var array
		 */
		private static $plugin_data = '';

		/**
		 * To be used for get site url
		 *
		 * @var string
		 */
		private static $site_url = '';

		/**
		 * To be used for get usage url
		 *
		 * @var string
		 */
		private static $usage_url = '';

		/**
		 * Flag to show notice only once
		 *
		 * @var boolean
		 */
		private static $notice_shown = false;

		/**
		 * Class constructor.
		 *
		 * @param array $plugin_data    Plugin Data to be used for plugin specific data.
		 */
		public function __construct( $plugin_data ) {
			$this->plugin_slug        = $plugin_data['pluginSlug'];
			$this->plugin_text_domain = $plugin_data['pluginTextDomain'];
			$this->base_folder_url    = $plugin_data['baseFolderUrl'];
			self::$dependencies       = isset( $plugin_data['dependencies'] ) ? $plugin_data['dependencies'] : array();
			self::$plugin_data        = isset( $plugin_data['plugin_data'] ) ? $plugin_data['plugin_data'] : array();
			self::$site_url           = isset( $plugin_data['siteUrl'] ) ? $plugin_data['siteUrl'] : '';
			self::$usage_url          = isset( $plugin_data['usage_url'] ) ? $plugin_data['usage_url'] : '';

			$license_key = trim( get_option( 'edd_' . $this->plugin_slug . LICENSE_KEY ) );
			self::save_site_analytics_in_db( $license_key );

			add_action( 'init', array( $this, 'add_data' ), 30 );
			add_action( 'admin_init', array( $this, 'reset_send_data' ), 30 );
			add_action( 'admin_notices', array( $this, 'show_notices_in_dashboard' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );
			add_action( 'wp_ajax_save_send_data', array( $this, 'update_db' ) );
			add_action( 'admin_head', array( $this, 'add_style_for_consent_btns' ) );
		}

		/**
		 * Enqueue styles and scripts required for licensing
		 *
		 * @param string $hook Current page.
		 */
		public function add_scripts( $hook ) {
			if ( 'toplevel_page_wisdmlabs-licenses' !== $hook ) {
				return;
			}

			if ( ! wp_style_is( 'license-css', 'enqueued' ) || ! wp_style_is( 'license-css', 'done' ) ) {
				wp_enqueue_style( 'license-css', $this->base_folder_url . '/licensing/assets/css/wdm-license.css', array(), '1.0' );
			}
			if ( ! wp_script_is( 'license-js', 'enqueued' ) || ! wp_script_is( 'license-js', 'done' ) ) {
				wp_enqueue_script( 'license-js', $this->base_folder_url . '/licensing/assets/js/wdm-license.js', array(), '1.0', true );
				wp_localize_script( 'license-js', 'license_data', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
			}
		}

		/**
		 * Adding Styling for Buttons displayed along with Consent on all pages.
		 */
		public function add_style_for_consent_btns() {
			echo '
            <style>
                .wdm-license-btn {
                    margin-top: 5px !important;
                    margin-bottom: 5px !important;
                    margin-right: 5px !important;
                }
            </style>
            ';
		}

		/**
		 * Update status of notice for sending data on server
		 */
		public function add_data() {
			if ( isset( $_GET['send-data-response'] ) ) {
				$this->update_notice_status();
			}
		}



		/**
		 * This function is used to reset send data option once after licensing code update.
		 *
		 * @return void
		 */
		public function reset_send_data() {
			$reset_status = get_option( 'edd_license_reset_status' );

			if ( ! $reset_status ) {
				update_option( 'edd_license_send_data_status', 'no' );
				update_option( 'edd_license_notice_status', false );
				update_option( 'edd_license_reset_status', true );
			}
		}

		/**
		 * Ajax callback for updating value in Database on send data to server status change
		 */
		public function update_db() {
			if ( isset( $_POST['checkStatus'] ) && 'yes' === $_POST['checkStatus'] ) {
				update_option( 'edd_license_send_data_status', 'yes' );
			} else {
				update_option( 'edd_license_send_data_status', 'no' );
			}
		}

		/**
		 * Update notice status in database
		 * Notice is displayed first time only
		 */
		public function update_notice_status() {
			if ( isset( $_GET['send-data-response'] ) && 'yes' === $_GET['send-data-response'] ) {
				update_option( 'edd_license_send_data_status', 'yes' );
				update_option( 'edd_license_notice_status', '1' );
			} else {
				update_option( 'edd_license_send_data_status', 'no' );
				update_option( 'edd_license_notice_status', '1' );
			}
		}

		/**
		 * Show send data to server notice in dashboard
		 */
		public function show_notices_in_dashboard() {
			$current_notice_status = get_option( 'edd_license_notice_status' );
			$text_domain           = $this->plugin_text_domain;

			if ( ( ! isset( $current_notice_status ) || ! $current_notice_status ) && ! self::$notice_shown ) {
				self::$notice_shown = true;
				$actual_link        = ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				if ( isset( $_GET ) && ! empty( $_GET ) ) {
					$agree_url  = $actual_link . '&send-data-response=yes';
					$reject_url = $actual_link . '&send-data-response=no';
				} else {
					$agree_url  = $actual_link . '?send-data-response=yes';
					$reject_url = $actual_link . '?send-data-response=no';
				}

				$html  = '<div class="notice notice-info">';
				$html .= '<p>';
				$html .= __( 'Be a part of WisdmLabs\' Product Improvement Plan.', $text_domain );
				$html .= '</p><p>';
				$html .= self::get_data_tracking_message( 'notice' );
				$html .= '</p>';
				$html .= '<p class="wdm-license-btns">';
				$html .= '<a class="button-primary wdm-license-btn" href = "' . $agree_url . '">Yes, I agree</a>';
				$html .= '<a class="button-primary wdm-license-btn" href = "' . $reject_url . '">No thanks</a>';
				$html .= '</p>';
				$html .= '</div>';

				echo $html;
			}
		}

		/**
		 * This function is used to get tracking message for notice.
		 *
		 * @param string $source   Source of function call.
		 * @return string
		 */
		public static function get_data_tracking_message( $source ) {
			if ( 'page' === $source ) {
				$text = ' uncheck the checkbox ';
			} elseif ( 'notice' === $source ) {
				$text = ' click on "No thanks" ';
			}
			return 'We only gather version dependency data to ensure our plugins are compatible with WordPress and dependant plugin versions. If you wish to opt-out,' . $text . 'and we will never store your version dependency data. <a href="https://wisdmlabs.com/product-support/#product-tracking" target="_blank">Click here</a> to know more about our data policies.';
		}

		/**
		 * Get site data for analytics
		 *
		 * @param  array $api_params parameters to be sent in request to server.
		 * @return array            parameters including analytics data.
		 */
		public static function get_analytics_data( $api_params, $license_key ) {
			$analytics_data = get_option( 'edd_license_send_data_status' );

			if ( 'yes' === $analytics_data ) {
				global $wp_version;
				self::fetch_site_analytics_from_db( $license_key );

				$php_version = phpversion();
				preg_match( '#^\d+(\.\d+)*#', PHP_VERSION, $php_version );
				$basic_usage_data['wp_version']  = $wp_version;
				$basic_usage_data['php_version'] = $php_version[0];
				$basic_usage_data['siteurl']     = self::$site_url;
				$basic_usage_data['timestamp']   = time();
				$basic_usage_data['new_request'] = 1;
				if ( ! empty( self::$dependencies ) ) {
					foreach ( self::$dependencies as $key => $value ) {
						$basic_usage_data[ $key ] = $value;
					}
				}

				// Data to be sent on licensing server.
				$api_params = array_merge( $api_params, $basic_usage_data );

				$usage_data = array();

				$usage_data['wordpress_setting'] = self::get_wordpress_settings();
				$usage_data['themes']            = self::get_wordpress_themes();
				$usage_data['plugins']           = self::get_wordpress_plugins();
				$usage_data['server']            = self::get_wordpress_server_details();
				$usage_data['mysql']             = self::get_mysql_details();
				$usage_data['plugin_data']       = self::$plugin_data;

				// Json encode data before sending on server.
				$usage_data = array_merge( $basic_usage_data, $usage_data );

				$response = wp_remote_post(
					self::$usage_url,
					array(
						'timeout'   => 15,
						'sslverify' => false,
						'blocking'  => true,
						'body'      => $usage_data,
					)
				);
			}

			return $api_params;
		}

		/**
		 * This function is used to get WordPress settings
		 *
		 * @return array
		 */
		public static function get_wordpress_settings() {
			return wp_json_encode(
				array(
					'time_zone'       => wp_timezone(),
					'site_language'   => get_locale(),
					'permalink'       => get_option( 'permalink_structure' ),
					'wp_memory_limit' => WP_MEMORY_LIMIT,
					'wp_multisite'    => is_multisite(),
				)
			);
		}

		/**
		 * This function is used to get WordPress themes details
		 *
		 * @return array
		 */
		public static function get_wordpress_themes() {
			$active_theme = wp_get_theme();

			// Get parent theme info if this theme is a child theme, otherwise
			// pass empty info in the response.
			if ( get_template_directory() !== get_stylesheet_directory() ) {
				$parent_theme      = wp_get_theme( $active_theme->template );
				$parent_theme_info = array(
					'parent_name'       => $parent_theme->name,
					'parent_version'    => $parent_theme->version,
					'parent_author_url' => $parent_theme->{'Author URI'},
				);
			} else {
				$parent_theme_info = array(
					'parent_name'           => '',
					'parent_version'        => '',
					'parent_version_latest' => '',
					'parent_author_url'     => '',
				);
			}

			$active_theme_info = array(
				'name'           => $active_theme->name,
				'version'        => $active_theme->version,
				'author_url'     => esc_url_raw( $active_theme->{'Author URI'} ),
				'is_child_theme' => get_template_directory() !== get_stylesheet_directory(),
			);

			return wp_json_encode( array_merge( $active_theme_info, $parent_theme_info ) );
		}

		/**
		 * This function is used to get WordPress Plugins details
		 *
		 * @return array
		 */
		public static function get_wordpress_plugins() {
			// Check if get_plugins() function exists. This is required on the front end of the
			// site, since it is in a file that is normally only loaded in the admin.
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$plugins        = get_plugins();
			$active_plugins = (array) get_option( 'active_plugins', array() );

			if ( is_multisite() ) {
				$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
				$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
			}

			$active_plugins_data   = array();
			$inactive_plugins_data = array();

			foreach ( $active_plugins as $plugin ) {
				$data                  = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
				$active_plugins_data[] = self::format_plugin_data( $plugin, $data );
			}

			foreach ( $plugins as $plugin => $data ) {
				if ( in_array( $plugin, $active_plugins, true ) ) {
					continue;
				}
				$inactive_plugins_data[] = self::format_plugin_data( $plugin, $data );
			}

			return wp_json_encode(
				array(
					'active_plugins'   => $active_plugins_data,
					'inactive_plugins' => $inactive_plugins_data,
				)
			);
		}

		/**
		 * This function is used to get WordPress server details.
		 *
		 * @return array
		 */
		public static function get_wordpress_server_details() {
			// Figure out cURL version, if installed.
			$curl_version = '';
			if ( function_exists( 'curl_version' ) ) {
				$curl_version = curl_version();
				$curl_version = $curl_version['version'] . ', ' . $curl_version['ssl_version'];
			} elseif ( extension_loaded( 'curl' ) ) {
				$curl_version = __( 'cURL installed but unable to retrieve version.', 'woocommerce' );
			}

			return wp_json_encode(
				array(
					'server_info'            => isset( $_SERVER['SERVER_SOFTWARE'] ) ? wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) : '',
					'php_version'            => phpversion(),
					'php_post_max_size'      => (int) ini_get( 'post_max_size' ),
					'php_max_execution_time' => (int) ini_get( 'max_execution_time' ),
					'php_max_input_vars'     => (int) ini_get( 'max_input_vars' ),
					'curl_version'           => $curl_version,
				)
			);
		}

		/**
		 * Get array of database information.
		 *
		 * @return array
		 */
		public static function get_mysql_details() {
			global $wpdb;

			$tables        = array();
			$database_size = array();

			// It is not possible to get the database name from some classes that replace wpdb (e.g., HyperDB)
			// and that is why this if condition is needed.
			if ( defined( 'DB_NAME' ) ) {
				$database_table_information = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT
							table_name AS 'name',
							engine AS 'engine',
							round( ( data_length / 1024 / 1024 ), 2 ) 'data',
							round( ( index_length / 1024 / 1024 ), 2 ) 'index'
						FROM information_schema.TABLES
						WHERE table_schema = %s
						ORDER BY name ASC;",
						DB_NAME
					)
				);

				$database_size = array(
					'data'  => 0,
					'index' => 0,
				);

				$site_tables_prefix = $wpdb->get_blog_prefix( get_current_blog_id() );
				$global_tables      = $wpdb->tables( 'global', true );

				foreach ( $database_table_information as $table ) {
					// Only include tables matching the prefix of the current site, this is to prevent displaying all tables on a MS install not relating to the current.
					if ( is_multisite() && 0 !== strpos( $table->name, $site_tables_prefix ) && ! in_array( $table->name, $global_tables, true ) ) {
						continue;
					}

					$tables [ $table->name ] = array(
						'data'   => $table->data,
						'index'  => $table->index,
						'engine' => $table->engine,
					);

					$database_size['data']  += $table->data;
					$database_size['index'] += $table->index;
				}
			}

			if ( $wpdb->use_mysqli ) {
				$server_info = mysqli_get_server_info( $wpdb->dbh );
			} else {
				$server_info = mysqli_get_server_info( $wpdb->dbh );
			}

			// Return all database info. Described by JSON Schema.
			return wp_json_encode(
				array(
					'server_info'     => $server_info,
					'database_prefix' => $wpdb->prefix,
					'database_tables' => $tables,    //@todo If we send this we are getting 520 error from server
					'database_size'   => $database_size,
				)
			);
		}

		/**
		 * Format plugin data, including data on updates, into a standard format.
		 *
		 * @since 3.6.0
		 * @param string $plugin Plugin directory/file.
		 * @param array  $data Plugin data from WP.
		 * @return array Formatted data.
		 */
		public static function format_plugin_data( $plugin, $data ) {
			require_once ABSPATH . 'wp-admin/includes/update.php';

			if ( ! function_exists( 'get_plugin_updates' ) ) {
				return array();
			}

			return array(
				'plugin'            => $plugin,
				'name'              => $data['Name'],
				'version'           => $data['Version'],
				'url'               => esc_url_raw( $data['PluginURI'] ),	// @todo If we send this we are getting 520 error from server
				'author_name'       => urlencode( $data['AuthorName'] ),
				'author_url'        => esc_url_raw( $data['AuthorURI'] ),	// @todo If we send this we are getting 520 error from server
				'network_activated' => $data['Network'],						//  @todo If we send this we are getting 520 error from server
			);
		}

		/**
		 * Save analytics data in DB.
		 *
		 * @param string $license_key License Key.
		 */
		public static function save_site_analytics_in_db( $license_key ) {
			if ( ! get_transient( 'wdm_analytics_'.$license_key ) ) {
				$combined_data = serialize(
					array(
						'dependencies' => self::$dependencies,
						'plugin_data' => self::$plugin_data,
						'site_url' => self::$site_url,
						'usage_url' => self::$usage_url,
					)
				);
				set_transient( 'wdm_analytics_'.$license_key, $combined_data, WEEK_IN_SECONDS );
			}
		}

		/**
		 * Fetch analytics data from DB.
		 *
		 * @param string $license_key License Key.
		 */
		public static function fetch_site_analytics_from_db( $license_key ) {
			$data = get_transient( 'wdm_analytics_'.$license_key );
			if ( false !== $data ) {
				$site_data = maybe_unserialize( $data );
				self::$dependencies = $site_data['dependencies'];
				self::$plugin_data = $site_data['plugin_data'];
				self::$site_url = $site_data['site_url'];
				self::$usage_url = $site_data['usage_url'];
			}
		}
	}
}
