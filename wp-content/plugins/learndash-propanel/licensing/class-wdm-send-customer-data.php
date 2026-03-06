<?php
/**
 * This class handles licensing
 *
 * @deprecated 1.8.2 This file is no longer in use.
 *
 * @package wisdmlabs-licensing
 */

namespace Licensing;

_deprecated_file( __FILE__, '1.8.2' );

if ( ! class_exists( 'Licensing\WdmSendDataToServer' ) ) {
 	/**
	 * Class WdmSendDataToServer.
	 *
	 * @deprecated 1.8.2
	 */
	class WdmSendDataToServer {
		/**
		 * Slug to be used in url and functions name.
		 *
		 * @deprecated 1.8.2
		 *
		 * @var string
		 */
		private $pluginSlug = '';

		/**
		 * Textdomain to be used for translations.
		 *
		 * @deprecated 1.8.2
		 *
		 * @var string
		 */
		private $pluginTextDomain = '';

		/**
		 * Base folder URL.
		 *
		 * @deprecated 1.8.2
		 *
		 * @var string
		 */
		private $baseFolderUrl = '';

		/**
		 * Dependencies to be used for translations.
		 *
		 * @deprecated 1.8.2
		 *
		 * @var array<string>
		 */
		private static $dependencies = [];

		/**
		 * Site url to be used for getting site url.
		 *
		 * @deprecated 1.8.2
		 *
		 * @var string
		 */
		private static $siteurl = '';

		/**
		 * Notice shown flag to show notice only once.
		 *
		 * @deprecated 1.8.2
		 *
		 * @var boolean
		 */
		private static $noticeShown = false;

		/**
		 * Constructor.
		 *
		 * @deprecated 1.8.2
		 *
		 * @param array $plugin_data Plugin data.
		 *
		 * @phpstan-param array{
		 *     pluginSlug: string,
		 *     pluginTextDomain: string,
		 *     baseFolderUrl: string,
		 *     dependencies?: array<string>,
		 *     siteUrl?: string
		 * } $plugin_data Plugin data.
		 */
		public function __construct( $plugin_data ) {
			_deprecated_constructor( __CLASS__, '1.8.2' );

			$this->pluginSlug       = $plugin_data['pluginSlug'];
			$this->pluginTextDomain = $plugin_data['pluginTextDomain'];
			$this->baseFolderUrl    = $plugin_data['baseFolderUrl'];
			self::$dependencies     = isset( $plugin_data['dependencies'] ) ? $plugin_data['dependencies'] : array();
			self::$siteurl          = isset( $plugin_data['siteUrl'] ) ? $plugin_data['siteUrl'] : '';

			add_action( 'init', array( $this, 'addData' ), 30 );
			add_action( 'admin_notices', array( $this, 'showNoticesInDashboard' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'addScripts' ) );
			add_action( 'wp_ajax_save_send_data', array( $this, 'updateDb' ) );
			add_action( 'admin_head', array( $this, 'addStyleForConsentBtns' ) );
		}

		/**
		 * Enqueue styles and scripts required for licensing.
		 *
		 * @deprecated 1.8.2
		 *
		 * @param string $hook Current page.
		 *
		 * @return void
		 */
		public function addScripts( $hook ) {
			_deprecated_function( __METHOD__, '1.8.2' );

			if ( $hook != 'toplevel_page_wisdmlabs-licenses' ) {
				return;
			}

			if ( ! wp_style_is( 'license-css', 'enqueued' ) || ! wp_style_is( 'license-css', 'done' ) ) {
				wp_enqueue_style( 'license-css', $this->baseFolderUrl . '/licensing/assets/css/wdm-license.css' );
			}
			if ( ! wp_script_is( 'license-js', 'enqueued' ) || ! wp_script_is( 'license-js', 'done' ) ) {
				wp_enqueue_script( 'license-js', $this->baseFolderUrl . '/licensing/assets/js/wdm-license.js' );
				wp_localize_script( 'license-js', 'license_data', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
			}
		}

		/**
		 * Adding Styling for Buttons displayed along with Consent on all pages.
		 *
		 * @deprecated 1.8.2
		 *
		 * @return void
		 */
		public function addStyleForConsentBtns() {
			_deprecated_function( __METHOD__, '1.8.2' );

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
		 * Update status of notice for sending data on server.
		 *
		 * @deprecated 1.8.2
		 *
		 * @return void
		 */
		public function addData() {
			_deprecated_function( __METHOD__, '1.8.2' );

			if ( isset( $_GET['send-data-response'] ) ) {
				$this->updateNoticeStatus();
			}
		}

		/**
		 * Ajax callback for updating value in Database on send data to server status change.
		 *
		 * @deprecated 1.8.2
		 *
		 * @return void
		 */
		public function updateDb() {
			_deprecated_function( __METHOD__, '1.8.2' );

			if ( $_POST['checkStatus'] === 'yes' ) {
				update_option( 'edd_license_send_data_status', 'yes' );
			} else {
				update_option( 'edd_license_send_data_status', 'no' );
			}
		}

		/**
		 * Update notice status in database.
		 * Notice is displayed first time only.
		 *
		 * @deprecated 1.8.2
		 *
		 * @return void
		 */
		public function updateNoticeStatus() {
			_deprecated_function( __METHOD__, '1.8.2' );

			if ( isset( $_GET['send-data-response'] ) && $_GET['send-data-response'] == 'yes' ) {
				update_option( 'edd_license_send_data_status', 'yes' );
				update_option( 'edd_license_notice_status', '1' );
			} else {
				update_option( 'edd_license_send_data_status', 'no' );
				update_option( 'edd_license_notice_status', '1' );
			}
		}

		/**
		 * Show send data to server notice in dashboard.
		 *
		 * @deprecated 1.8.2
		 *
		 * @return void
		 */
		public function showNoticesInDashboard() {
			_deprecated_function( __METHOD__, '1.8.2' );

			$currentNoticeStatus = get_option( 'edd_license_notice_status' );
			$textDomain          = $this->pluginTextDomain;

			if ( ( ! isset( $currentNoticeStatus ) || ! $currentNoticeStatus ) && ! self::$noticeShown ) {
				self::$noticeShown = true;
				$actual_link       = ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				if ( ! empty( $_GET ) ) {
					$agreeURL  = $actual_link . '&send-data-response=yes';
					$rejectURL = $actual_link . '&send-data-response=no';
				} else {
					$agreeURL  = $actual_link . '?send-data-response=yes';
					$rejectURL = $actual_link . '?send-data-response=no';
				}

				$html  = '<div class="notice notice-info">';
				$html .= '<p>';
				$html .= __( 'Be a part of WisdmLabs\' Product Improvement Plan.', $textDomain );
				$html .= '</p><p>';
				$html .= self::getDataTrackingMessage( 'notice' );
				$html .= '</p>';
				$html .= '<p class="wdm-license-btns">';
				$html .= '<a class="button-primary wdm-license-btn" href = "' . $agreeURL . '">Yes, I agree</a>';
				$html .= '<a class="button-primary wdm-license-btn" href = "' . $rejectURL . '">No thanks</a>';
				$html .= '</p>';
				$html .= '</div>';

				echo $html;
			}
		}

		/**
		 * Get data tracking message.
		 *
		 * @deprecated 1.8.2
		 *
		 * @param string $source Source.
		 *
		 * @return string
		 */
		public static function getDataTrackingMessage( $source ) {
			_deprecated_function( __METHOD__, '1.8.2' );

			$text = '';

			if ( $source == 'page' ) {
				$text = ' uncheck the checkbox ';
			} elseif ( $source == 'notice' ) {
				$text = ' click on "No thanks" ';
			}

			return 'We only gather version dependency data to ensure our plugins are compatible with WordPress and dependant plugin versions. If you wish to opt-out,' . $text . 'and we will never store your version dependency data. <a href="https://wisdmlabs.com/product-support/#product-tracking" target="_blank">Click here</a> to know more about our data policies.';
		}

		/**
		 * Get site data for analytics.
		 *
		 * @deprecated 1.8.2
		 *
		 * @param  array<string, mixed> $apiParams Parameters to be sent in request to server.
		 *
		 * @return array<string, mixed> Parameters including analytics data.
		 */
		public static function getAnalyticsData( $apiParams ) {
			_deprecated_function( __METHOD__, '1.8.2' );

			$analyticsData = get_option( 'edd_license_send_data_status' );

			if ( $analyticsData == 'yes' ) {
				global $wp_version;
				$phpversion = phpversion();
				preg_match( '#^\d+(\.\d+)*#', PHP_VERSION, $phpversion );
				$apiParams['wp_version']  = $wp_version;
				$apiParams['php_version'] = $phpversion[0];
				$apiParams['siteurl']     = self::$siteurl;
				$apiParams['new_request'] = 1;
				if ( ! empty( self::$dependencies ) ) {
					foreach ( self::$dependencies as $key => $value ) {
						$apiParams[ $key ] = $value;
					}
				}
			}

			return $apiParams;
		}
	}
}
