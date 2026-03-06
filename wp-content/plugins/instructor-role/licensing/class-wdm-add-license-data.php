<?php
/**
 * This class handles licensing
 *
 * @deprecated 5.9.1 This file is no longer in use.
 *
 * @package wisdmlabs-licensing
 */

namespace Licensing;

if ( ! class_exists( 'Licensing\WdmAddLicenseData' ) ) {
	/**
	 * This class is used to Add license data
	 */
	class WdmAddLicenseData {

		/**
		 * Short Name for plugin.
		 *
		 * @var string
		 */
		private $plugin_short_name = '';

		/**
		 * Slug to be used in url and functions name.
		 *
		 * @var string
		 */
		private $plugin_slug = '';

		/**
		 * Stores the current plugin version
		 *
		 * @var string
		 */
		private $plugin_version = '';

		/**
		 * Handles the plugin name.
		 *
		 * @var string
		 */
		private $plugin_name = '';

		/**
		 * Stores the URL of store. Retrieves updates from this store
		 *
		 * @var string
		 */
		private $store_url = '';

		/**
		 * Name of the Author
		 *
		 * @var string
		 */
		private $author_name = '';
		/**
		 * Product Item Id
		 *
		 * @var string
		 */
		private $item_id = '';

		/**
		 * Base folder URL
		 *
		 * @var string
		 */
		private $base_folder_url = '';

		/**
		 * Constructor sets values to variables.
		 * Add actions.
		 *
		 * @param array $plugin_data    Array of plugin data.
		 */
		public function __construct( $plugin_data ) {
			$this->author_name        = $plugin_data['authorName'];
			$this->plugin_name        = $plugin_data['pluginName'];
			$this->plugin_short_name  = $plugin_data['pluginShortName'];
			$this->plugin_slug        = $plugin_data['pluginSlug'];
			$this->plugin_version     = $plugin_data['pluginVersion'];
			$this->store_url          = $plugin_data['storeUrl'];
			$this->plugin_text_domain = $plugin_data['pluginTextDomain'];
			$this->item_id            = $plugin_data['itemId'];

			$this->base_folder_url = $plugin_data['baseFolderUrl'];
			add_action( 'init', array( $this, 'add_data' ), 2 );
			// This action is used to add license menu.
			add_action( 'admin_menu', array( $this, 'license_menu' ) );
			// This action is used to display plugin on licensing page.
			add_action( 'wdm_display_licensing_options', array( $this, 'display_license_page' ) );
		}

		/**
		 * This function is used to add license menu if not added by any other wisdmlabs plugin.
		 */
		public function license_menu() {
			if ( ! in_array( 'wisdmlabs-licenses', $GLOBALS['admin_page_hooks'], true ) ) {
				add_menu_page(
					__( 'WisdmLabs License Options', $this->plugin_text_domain ),
					__( 'WisdmLabs License Options', $this->plugin_text_domain ),
					apply_filters( $this->plugin_slug . '_license_page_capability', 'manage_options' ),
					'wisdmlabs-licenses',
					array( $this, 'license_page' ),
					$this->base_folder_url . '/licensing/assets/images/wisdmlabs-icon.png',
					99
				);
			}
		}

		/**
		 * This function calls license page template.
		 */
		public function license_page() {
			include_once trailingslashit( dirname( dirname( __FILE__ ) ) ) . 'licensing/license-page.php';
		}

		/**
		 * This function adds license row in license page.
		 */
		public function display_license_page() {
			$license_key = trim( get_option( 'edd_' . $this->plugin_slug . LICENSE_KEY ) );

			$previous_status = '';

			// Get License Status.
			$status = $this->get_status( $previous_status );

			$display = $this->get_site_list();

			if ( isset( $_POST ) && ! empty( $_POST ) ) {
				if ( ! check_admin_referer( 'edd_' . $this->plugin_slug . '_nonce', 'edd_' . $this->plugin_slug . '_nonce' ) ) {
					return;
				}

				if ( isset( $_POST[ 'edd_' . $this->plugin_slug . '_license_deactivate' ] ) || isset( $_POST[ 'edd_' . $this->plugin_slug . '_license_activate' ] ) ) {
					$this->show_server_response( $status, $display );
				}
			}

			$this->display_notice_for_expired( $status );

			settings_errors( 'wdm_' . $this->plugin_slug . '_errors' );

			$renew_link = get_option( 'wdm_' . $this->plugin_slug . '_product_site' );
			?>
			<tr>
				<td class="product-name">
				<?php
				echo esc_html( $this->plugin_name );
				?>
			</td>
				<td class="license-key">
					<?php
					if ( VALID === $status || EXPIRED === $status || VALID === $previous_status || EXPIRED === $previous_status ) {
						?>
						<input id="<?php echo esc_attr( 'edd_' . $this->plugin_slug . LICENSE_KEY ); ?>" name="<?php echo esc_attr( 'edd_' . $this->plugin_slug . LICENSE_KEY ); ?>" type="text" class="regular-text" value="<?php echo esc_attr( $license_key ); ?>" readonly/>
						<?php
					} else {
						?>
						<input id="<?php echo esc_attr( 'edd_' . $this->plugin_slug . LICENSE_KEY ); ?>" name="<?php echo esc_attr( 'edd_' . $this->plugin_slug . LICENSE_KEY ); ?>" type="text" class="regular-text" value="<?php esc_attr_e( $license_key ); ?>" />
						<?php
					}
					?>
					<label class="description" for="<?php echo esc_attr( 'edd_' . $this->plugin_slug . LICENSE_KEY ); ?>"></label>
				</td>
				<td class="license-status">
				<?php
				$this->display_license_status( $status, $previous_status );
				?>
			</td>
				<td class="wdm-actions">
					<?php
					if ( false !== $status && ( VALID === $status || EXPIRED === $status || VALID === $previous_status || EXPIRED === $previous_status ) ) {
						?>
						<?php
						wp_nonce_field( 'edd_' . $this->plugin_slug . '_nonce', 'edd_' . $this->plugin_slug . '_nonce' );
						?>
						<input type="submit" class="wdm-link" name="<?php echo esc_attr( 'edd_' . $this->plugin_slug . '_license_deactivate' ); ?>" value="<?php esc_attr_e( 'Deactivate', $this->plugin_text_domain ); ?>"/>
						<?php
						if ( EXPIRED === $status ) {
							?>
							<input type="button" class="button" name="<?php echo esc_attr( 'edd_' . $this->plugin_slug . '_license_renew' ); ?>" value="<?php esc_attr_e( 'Renew', $this->plugin_text_domain ); ?>" onclick="window.open('<?php echo esc_attr( $renew_link ); ?>')"/>
							<?php
						}
					} else {
						wp_nonce_field( 'edd_' . $this->plugin_slug . '_nonce', 'edd_' . $this->plugin_slug . '_nonce' );
						?>
						<input type="submit" class="button" name="<?php echo esc_attr( 'edd_' . $this->plugin_slug . '_license_activate' ); ?>" value="<?php esc_attr_e( 'Activate', $this->plugin_text_domain ); ?>"/>
						<?php
					}
					?>
				</td>
			</tr>
			<?php
		}

		/**
		 * Notice text for expired license key
		 *
		 * @param string $status License status.
		 * @return void
		 */
		public function display_notice_for_expired( $status ) {
			if ( EXPIRED === $status ) {
				$renew_msg           = __( 'Once you renew the License, you must Deactivate Plugin and then Activate it again.', $this->plugin_text_domain );
				$registered_settings = get_settings_errors();
				if ( ! $registered_settings && empty( $registered_settings ) ) {
					add_settings_error(
						'wdm_license_errors',
						esc_attr( 'has-expired-license' ),
						$renew_msg,
						'error'
					);
					settings_errors( 'wdm_license_errors' );
				}
			}
		}

		/**
		 * This function is used to get the status received from the server.
		 *
		 * @param string $previous_status previous status stored in database.
		 *
		 * @return string Status of response.
		 */
		public function get_status( &$previous_status ) {
			$status = get_option( 'edd_' . $this->plugin_slug . '_license_status' );

			if ( isset( $GLOBALS[ 'wdm_server_null_response_' . $this->plugin_slug ] ) && $GLOBALS[ 'wdm_server_null_response_' . $this->plugin_slug ] ) {
				$status          = 'server_did_not_respond';
				$previous_status = get_option( 'edd_' . $this->plugin_slug . '_license_status' );
			} elseif ( isset( $GLOBALS[ 'wdm_license_activation_failed_' . $this->plugin_slug ] ) && $GLOBALS[ 'wdm_license_activation_failed_' . $this->plugin_slug ] ) {
				$status = 'license_activation_failed';
			} elseif ( isset( $_POST[ 'edd_' . $this->plugin_slug . LICENSE_KEY ] ) && check_admin_referer( 'edd_' . $this->plugin_slug . '_nonce', 'edd_' . $this->plugin_slug . '_nonce' ) && empty( $_POST[ 'edd_' . $this->plugin_slug . LICENSE_KEY ] ) ) {
				$status = 'no_license_key_entered';
			} elseif ( isset( $GLOBALS[ 'wdm_server_curl_error_' . $this->plugin_slug ] ) && $GLOBALS[ 'wdm_server_curl_error_' . $this->plugin_slug ] ) {
				$status          = 'server_curl_error';
				$previous_status = get_option( 'edd_' . $this->plugin_slug . '_license_status' );
			}

			return $status;
		}

		/**
		 * This function is used to get list of site on which license key is active.
		 *
		 * @return string List of sites(in html list)
		 */
		public function get_site_list() {
			include_once dirname( plugin_dir_path( __FILE__ ) ) . '/licensing/class-wdm-get-license-data.php';
			$display     = '';
			$active_site = WdmGetLicenseData::get_site_list( $this->plugin_slug );
			if ( ! empty( $active_site ) || '' !== $active_site ) {
				$display = '<ul>' . $active_site . '</ul>';
			}

			return $display;
		}

		/**
		 * Notice to display based on response from server.
		 *
		 * @param string $status  current status of license.
		 * @param [type] $display [description].
		 */
		public function show_server_response( $status, $display ) {
			$success_messages = array(
				VALID => __( 'Your license key is activated(' . $this->plugin_name . ')', $this->plugin_text_domain ),
			);

			$error_messages = array(
				'server_did_not_respond'    => __( 'No response from server. Please try again later.(' . $this->plugin_name . ')', $this->plugin_text_domain ),
				'license_activation_failed' => __( 'License Activation Failed. Please try again or contact support on (' . $this->plugin_name . ')', $this->plugin_text_domain ),
				'no_license_key_entered'    => __( 'Please enter license key.(' . $this->plugin_name . ')', $this->plugin_text_domain ),
				'no_activations_left'       => ( ! empty( $display ) ) ? sprintf( __( 'Your License Key is already activated at : %s Please deactivate the license from one of the above site(s) to successfully activate it on your current site.(' . $this->plugin_name . ')', $this->plugin_text_domain ), $display ) : __( 'No Activations Left.(' . $this->plugin_name . ')', $this->plugin_text_domain ),
				EXPIRED                     => __( 'Your license key has Expired. Please, Renew it.(' . $this->plugin_name . ')', $this->plugin_text_domain ),
				'disabled'                  => __( 'Your License key is disabled(' . $this->plugin_name . ')', $this->plugin_text_domain ),
				INVALID                     => __( 'Please enter valid license key(' . $this->plugin_name . ')', $this->plugin_text_domain ),
				'inactive'                  => __( 'Please try to activate license again. If it does not activate, contact support (' . $this->plugin_name . ')', $this->plugin_text_domain ),
				'site_inactive'             => ( ! empty( $display ) ) ? sprintf( __( 'Your License Key is already activated at : %s Please deactivate the license from one of the above site(s) to successfully activate it on your current site.(' . $this->plugin_name . ')', $this->plugin_text_domain ), $display ) : __( 'Site inactive (Press Activate license to activate plugin(' . $this->plugin_name . '))', $this->plugin_text_domain ),
				'deactivated'               => __( 'License Key is deactivated(' . $this->plugin_name . ')', $this->plugin_text_domain ),
				'default'                   => sprintf( __( 'Following Error Occurred: %s. Please contact support if you are not sure why this error is occurring(' . $this->plugin_name . ')', $this->plugin_text_domain ), $status ),
				'server_curl_error'         => __( 'There was an error while connecting to the server. please try again later.(' . $this->plugin_name . ')', $this->plugin_text_domain ),
			);
			if ( false !== $status ) {
				if ( array_key_exists( $status, $success_messages ) ) {
					add_settings_error(
						'wdm_' . $this->plugin_slug . '_errors',
						esc_attr( 'settings_updated' ),
						$success_messages[ $status ],
						'updated'
					);
				} else {
					if ( array_key_exists( $status, $error_messages ) ) {
						add_settings_error(
							'wdm_' . $this->plugin_slug . '_errors',
							esc_attr( 'settings_updated' ),
							$error_messages[ $status ],
							'error'
						);
					} else {
						add_settings_error(
							'wdm_' . $this->plugin_slug . '_errors',
							esc_attr( 'settings_updated' ),
							$error_messages['default'],
							'error'
						);
					}
				}
			}
		}

		/**
		 * Display licensing status in license row.
		 *
		 * @param string $status         Current response status of license.
		 * @param string $previous_status Previous response stored in database.
		 */
		public function display_license_status( $status, $previous_status ) {
			if ( false !== $status ) {
				if ( VALID === $status || VALID === $previous_status ) {
					?>
					<span style="color:green;">
					<?php
					esc_attr_e( 'Active', $this->plugin_text_domain );
					?>
					</span>
					<?php
				} elseif ( EXPIRED === $status || EXPIRED === $previous_status ) {
					?>
					<span style="color:red;">
					<?php
					esc_html_e( 'Expired', $this->plugin_text_domain );
					?>
					</span>
					<?php
				} else {
					?>
					<span style="color:red;"><?php esc_attr_e( 'Not Active', $this->plugin_text_domain ); ?></span>
					<?php
				}
			}

			if ( false === $status ) {
				?>
				<span style="color:red;"><?php esc_attr_e( 'Not Active', $this->plugin_text_domain ); ?></span>
				<?php
			}
		}
		/**
		 * Updates license status in the database and returns status value.
		 *
		 * @param object $license_data License data returned from server.
		 * @param string $plugin_slug  Slug of the plugin. Format of the key in options table is 'edd_<$plugin_slug>_license_status'.
		 *
		 * @return string Returns status of the license.
		 */
		public static function update_status( $license_data, $plugin_slug ) {
			$status = '';
			if ( isset( $license_data->success ) ) {
				// Check if request was successful. Even if success property is blank, technically it is false.
				if ( false === $license_data->success && ( ! isset( $license_data->error ) || empty( $license_data->error ) ) ) {
						$license_data->error = INVALID;
				}
				// Is there any licensing related error? If there are no errors, $status will be blank.
				$status = self::check_licensing_error( $license_data );

				if ( ! empty( $status ) ) {
					update_option( 'edd_' . $plugin_slug . '_license_status', $status );

					return $status;
				}
				// Check license status retrieved from EDD.
				$status = self::check_license_status( $license_data, $plugin_slug );
			}

			$status = ( empty( $status ) ) ? INVALID : $status;
			update_option( 'edd_' . $plugin_slug . '_license_status', $status );

			return $status;
		}

		/**
		 * Checks if there is any error in response.
		 *
		 * @param object $license_data License Data obtained from server.
		 *
		 * @return string empty if no error or else error
		 */
		public static function check_licensing_error( $license_data ) {
			$status = '';
			if ( isset( $license_data->error ) && ! empty( $license_data->error ) ) {
				switch ( $license_data->error ) {
					case 'revoked':
						$status = 'disabled';
						break;

					case EXPIRED:
						$status = EXPIRED;
						break;

					case 'item_name_mismatch':
						$status = INVALID;
						break;

					default:
						$status = '';
				}
			}

			return $status;
		}

		/**
		 * Check license status from response from server.
		 *
		 * @param object $license_data License data received from server.
		 * @param string $plugin_slug  plugin slug.
		 *
		 * @return string License status
		 */
		public static function check_license_status( $license_data, $plugin_slug ) {
			$status = INVALID;
			if ( isset( $license_data->license ) && ! empty( $license_data->license ) ) {
				switch ( $license_data->license ) {
					case INVALID:
						$status = INVALID;
						if ( isset( $license_data->activations_left ) && 0 === $license_data->activations_left ) {
							include_once plugin_dir_path( __FILE__ ) . 'class-wdm-get-license-data.php';
							$active_site = WdmGetLicenseData::get_site_list( $plugin_slug );

							if ( ! empty( $active_site ) || '' !== $active_site ) {
								$status = 'no_activations_left';
							}
						}

						break;

					case 'failed':
						$status = 'failed';
						$GLOBALS[ 'wdm_license_activation_failed_' . $plugin_slug ] = true;
						break;

					default:
						$status = $license_data->license;
				}
			}

			return $status;
		}

		/**
		 * Checks if any response received from server or not after making an API call. If no response obtained, then sets next api request after 24 hours.
		 *
		 * @param object $license_data         License Data obtained from server.
		 * @param string $current_response_code Response code of the API request.
		 * @param array  $valid_response_code   Array of acceptable response codes.
		 *
		 * @return bool returns false if no data obtained. Else returns true.
		 */
		public function check_if_no_data( $license_data, $current_response_code, $valid_response_code ) {
			if ( null === $license_data || ! in_array( $current_response_code, $valid_response_code, true ) ) {
				$GLOBALS[ 'wdm_server_null_response_' . $this->plugin_slug ] = true;
				WdmLicense::set_version_info_cache( 'wdm_' . $this->plugin_slug . '_license_trans', 1, 'server_did_not_respond' );

				return false;
			}

			return true;
		}

		/**
		 * Activates License.
		 */
		public function activate_license() {
			if ( ! check_admin_referer( 'edd_' . $this->plugin_slug . '_nonce', 'edd_' . $this->plugin_slug . '_nonce' ) ) {
				return;
			}
			$post_data = wp_unslash( $_POST );

			if ( isset( $post_data[ 'edd_' . $this->plugin_slug . LICENSE_KEY ] ) ) {
				$license_key = trim( $post_data[ 'edd_' . $this->plugin_slug . LICENSE_KEY ] );
			}

			if ( $license_key ) {
				update_option( 'edd_' . $this->plugin_slug . LICENSE_KEY, $license_key );

				$response = $this->get_remote_data( 'activate_license', $license_key );

				if ( is_wp_error( $response ) ) {
					$GLOBALS[ 'wdm_server_curl_error_' . $this->plugin_slug ] = true;
					return false;
				}

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				$valid_response_code = array( 200, 301 );

				$current_response_code = wp_remote_retrieve_response_code( $response );

				$is_data_available = $this->check_if_no_data( $license_data, $current_response_code, $valid_response_code );

				if ( ! $is_data_available ) {
					return;
				}

				$expiration_time = $this->get_expiration_time( $license_data );
				$current_time    = time();

				// Check if license is not expired.
				if ( isset( $license_data->expires ) && ( false !== $license_data->expires ) && ( 'lifetime' !== $license_data->expires ) && $expiration_time <= $current_time && 0 !== $expiration_time && ! isset( $license_data->error ) ) {
					$license_data->error = EXPIRED;
				}

				// Add License renew link in the database.
				if ( isset( $license_data->renew_link ) && ( ! empty( $license_data->renew_link ) || '' !== $license_data->renew_link ) ) {
					update_option( 'wdm_' . $this->plugin_slug . '_product_site', $license_data->renew_link );
				}

				// It will give all sites on which license is activated including current site.
				$this->update_number_of_sites_using_license( $license_data );

				// Save License Status in the database.
				$license_status = self::update_status( $license_data, $this->plugin_slug );

				$this->set_transient_on_activation( $license_status );
			}
		}

		/**
		 * Get the expiration time of license key.
		 *
		 * @param object $license_data License response received from server.
		 *
		 * @return string Expiration time
		 */
		public function get_expiration_time( $license_data ) {
			$expiration_time = 0;
			if ( isset( $license_data->expires ) ) {
				$expiration_time = strtotime( $license_data->expires );
			}

			return $expiration_time;
		}

		/**
		 * Update sites list in database on which license key is active.
		 *
		 * @param object $license_data License response received from server.
		 */
		public function update_number_of_sites_using_license( $license_data ) {
			if ( isset( $license_data->sites ) && ( ! empty( $license_data->sites ) || '' !== $license_data->sites ) ) {
				update_option( 'wdm_' . $this->plugin_slug . '_license_key_sites', $license_data->sites );
				update_option( 'wdm_' . $this->plugin_slug . '_license_max_site', $license_data->license_limit );
			} else {
				update_option( 'wdm_' . $this->plugin_slug . '_license_key_sites', '' );
				update_option( 'wdm_' . $this->plugin_slug . '_license_max_site', '' );
			}
		}

		/**
		 * Set transient on site on license activation
		 * Transient is set for 7 days
		 * After 7 days request is sent to server for fresh license status.
		 *
		 * @param string $license_status Current license status.
		 */
		public function set_transient_on_activation( $license_status ) {
			if ( ! empty( $license_status ) ) {
				if ( VALID === $license_status ) {
					$time = 7;
				} else {
					$time = 1;
				}
				WdmLicense::set_version_info_cache( 'wdm_' . $this->plugin_slug . '_license_trans', $time, $license_status );
			}
		}

		/**
		 * Send request on server and get the data from server on various license actions.
		 *
		 * @param string $action     action performed by user.
		 * @param string $license_key license key for which request is sent.
		 *
		 * @return [type] [description]
		 */
		public function get_remote_data( $action, $license_key ) {
			$api_params = array(
				'edd_action'      => $action,
				'license'         => $license_key,
				'item_name'       => rawurlencode( $this->plugin_name ),
				'plugin_slug'     => $this->plugin_slug,
				'current_version' => $this->plugin_version,
			);
			if ( $this->item_id ) {
				$api_params['item_id'] = $this->item_id;
			}

			$api_params = WdmSendDataToServer::get_analytics_data( $api_params, $license_key );

			return wp_remote_post(
				add_query_arg( $api_params, $this->store_url ),
				array(
					'timeout'   => 15,
					'sslverify' => false, // cspell:disable-line .
					'blocking'  => true,
				)
			);
		}

		/**
		 * Deactivates License.
		 */
		public function deactivate_license() {
			$license_key = trim( get_option( 'edd_' . $this->plugin_slug . LICENSE_KEY ) );

			if ( $license_key ) {
				$response = $this->get_remote_data( 'deactivate_license', $license_key );

				if ( is_wp_error( $response ) ) {
					return false;
				}

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				$valid_response_code = array( 200, 301 );

				$current_response_code = wp_remote_retrieve_response_code( $response );

				$is_data_available = $this->check_if_no_data( $license_data, $current_response_code, $valid_response_code );

				if ( ! $is_data_available ) {
					return;
				}

				if ( 'deactivated' === $license_data->license || 'failed' === $license_data->license ) {
					update_option( 'edd_' . $this->plugin_slug . '_license_status', 'deactivated' );
				}

				WdmLicense::set_version_info_cache( 'wdm_' . $this->plugin_slug . '_license_trans', 0, $license_data->license );
			}
		}

		/**
		 * Perform an activate or deactivate action.
		 *
		 * @return void
		 */
		public function add_data() {
			if ( isset( $_POST[ 'edd_' . $this->plugin_slug . '_license_activate' ] ) ) {
				if ( ! check_admin_referer( 'edd_' . $this->plugin_slug . '_nonce', 'edd_' . $this->plugin_slug . '_nonce' ) ) {
					return;
				}
				$this->activate_license();
			} elseif ( isset( $_POST[ 'edd_' . $this->plugin_slug . '_license_deactivate' ] ) ) {
				if ( ! check_admin_referer( 'edd_' . $this->plugin_slug . '_nonce', 'edd_' . $this->plugin_slug . '_nonce' ) ) {
					return;
				}
				$this->deactivate_license();
			}
		}
	}
}
