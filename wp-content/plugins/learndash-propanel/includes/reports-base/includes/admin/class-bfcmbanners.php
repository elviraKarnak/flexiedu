<?php
/**
 * Report Export Form/AJAX request Submission.
 *
 * @deprecated 1.8.2 This file is no longer in use.
 *
 * @package LearnDash\Reports
 */

namespace bfcm_banner {
	defined( 'ABSPATH' ) || exit;

	_deprecated_file( __FILE__, '1.8.2' );

	/**
	 * Class for handling the BFCM banners.
	 *
	 * @deprecated 1.8.2
	 */
	class BfcmBanners {
		/**
		 * Instance of this class.
		 *
		 * @since 1.4.1.
		 *
		 * @var object
		 */
		protected static $instance = null;
		/**
		 * Initialization.
		 *
		 * @deprecated 1.8.2
		 */
		public function __construct() {
			_deprecated_constructor( __CLASS__, '1.8.2' );

			add_action( 'admin_notices', array( $this, 'wrld_banner_logic' ) );
		}

		/**
		 * Returns an instance of this class.
		 *
		 * @since 1.4.1
		 * @deprecated 1.8.2
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			_deprecated_function( __METHOD__, '1.8.2' );

			// If the single instance hasn't been set, set it now.
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * This function handles the banner display logic
		 *
		 * @since 1.4.1
		 * @deprecated 1.8.2
		 */
		public function wrld_banner_logic() {
			_deprecated_function( __METHOD__, '1.8.2' );

			global $current_user;
			if ( ( in_array( 'wdm_instructor', $current_user->roles, true ) || in_array( 'group_leader', $current_user->roles, true ) ) && ! in_array( 'administrator', $current_user->roles, true ) ) {
				return;
			}
			$get_data = filter_var_array( $_GET, FILTER_UNSAFE_RAW ); // phpcs:ignore WordPress.Security.NonceVerification
			if ( isset( $get_data['wdm_banner_type'] ) ) {
				if ( 'pre' === $get_data['wdm_banner_type'] ) {
					update_option( 'wdm_pre_bfcm_dismiss', true );
				}

				if ( 'upgrade' === $get_data['wdm_banner_type'] ) {
					update_option( 'wrld_upgrade_bfcm_dismiss', true );
				}

				if ( 'lifetime' === $get_data['wdm_banner_type'] ) {
					update_option( 'wrld_lifetime_bfcm_dismiss', true );
				}
			}

			$date_from_user = gmdate( 'Y-m-d' );

			// Spinner banner per BFCM.
			if ( ! get_option( 'wdm_pre_bfcm_dismiss' ) ) {
				$start_date = '2022-11-04';
				$end_date   = '2022-11-23';
				$is_pre     = $this->check_in_range( $start_date, $end_date, $date_from_user );
				if ( $is_pre ) {
					$this->wrld_bfcm_black_friday();
				}
			}

				$start_date = '2022-11-24';
				$end_date   = '2022-11-30';
				$is_during  = $this->check_in_range( $start_date, $end_date, $date_from_user );
			if ( $is_during ) {
				$validity = get_option( 'edd_learndash-reports-pro_license_status', false );
				if ( 'valid' !== $validity && defined( 'LDRP_PLUGIN_VERSION' ) ) {// plugin activation check.
					if ( ! get_option( 'wrld_upgrade_bfcm_dismiss' ) ) {
						$this->wrld_bfcm_during();
					}
				} elseif ( ! get_option( 'wrld_lifetime_bfcm_dismiss' ) ) {
						$license_key = trim( get_option( 'edd_learndash-reports-pro_license_key' ) );
						$key_data    = get_option( 'edd_learndash-reports-pro_' . $license_key . '_data', array() );
					if ( ! empty( $key_data ) ) {
						$expiration_date = $key_data->expires;
						if ( 'lifetime' !== $expiration_date ) {
							$this->wrld_bfcm_during_lifetime();
						}
					} else {
						$this->wrld_bfcm_during_lifetime();
					}
				}
			}
		}

		/**
		 * Helper function that checks the specified date is in a given range or not.
		 *
		 * @since 1.4.1
		 * @deprecated 1.8.2
		 *
		 * @param string $start_date     This is the start date of the pre BFCM Sale.
		 * @param string $end_date       This is the end date of the pre BFCM Sale.
		 * @param string $date_from_user This is the referenced current date of the system.
		 */
		public function check_in_range( $start_date, $end_date, $date_from_user ) {
			_deprecated_function( __METHOD__, '1.8.2' );

			// Convert to timestamp.
			$start_ts = strtotime( $start_date );
			$end_ts   = strtotime( $end_date );
			$user_ts  = strtotime( $date_from_user );

			// Check that user date is between start & end.
			return ( ( $user_ts >= $start_ts ) && ( $user_ts <= $end_ts ) );
		}

		/**
		 * Returns html bfcm content.
		 *
		 * @since 1.4.1
		 * @deprecated 1.8.2
		 */
		public function wrld_bfcm_black_friday() {
			_deprecated_function( __METHOD__, '1.8.2' );

			global $wp;
			$current_url = get_permalink( add_query_arg( array(), $wp->request ) );
			if ( is_rtl() ) {
				wp_enqueue_style( 'wrld_bfcm_style', WRLD_REPORTS_SITE_URL . '/assets/css/bfcm.rtl.css', array(), LDRP_PLUGIN_VERSION );
			} else {
				wp_enqueue_style( 'wrld_bfcm_style', WRLD_REPORTS_SITE_URL . '/assets/css/bfcm.css', array(), LDRP_PLUGIN_VERSION );
			}

			$banner_head       = __( 'Play a game with WisdmLabs to get ahead of the black friday sale', 'learndash-reports-pro' );
			$banner_message    = __( 'Spin the Wheel to Win Free Access or discount on our Premium WordPress and LearnDash Product', 'learndash-reports-pro' );
			$button_text       = __( 'SPIN AND WIN', 'learndash-reports-pro' );
			$link              = '';
			$wisdm_logo        = WRLD_REPORTS_SITE_URL . '/assets/images/learndash.png';
			$background        = WRLD_REPORTS_SITE_URL . '/assets/images/spin_wheel.png';
			$dismiss_attribute = add_query_arg( array( 'wdm_banner_type' => 'pre' ), $current_url );
			include WRLD_REPORTS_PATH . '/includes/templates/bfcm-banner.php';
		}

		/**
		 * Returns html of banners.
		 *
		 * @since 1.4.1
		 * @deprecated 1.8.2
		 */
		public function wrld_bfcm_during() {
			_deprecated_function( __METHOD__, '1.8.2' );

			global $wp;
			$current_url = get_permalink( add_query_arg( array(), $wp->request ) );
			if ( is_rtl() ) {
				wp_enqueue_style( 'wrld_bfcm_style', WRLD_REPORTS_SITE_URL . '/assets/css/bfcm.rtl.css', array(), LDRP_PLUGIN_VERSION );
			} else {
				wp_enqueue_style( 'wrld_bfcm_style', WRLD_REPORTS_SITE_URL . '/assets/css/bfcm.css', array(), LDRP_PLUGIN_VERSION );
			}
			$banner_head          = __( 'Save BIG on both Money and Time this Black Friday Cyber Monday Season! ', 'learndash-reports-pro' );
			$banner_message       = __( 'Bridge the gap with a complete Reporting Solution and grow your e-learning business', 'learndash-reports-pro' );
			$banner_message_addon = __( 'Get up to 70% off on LearnDash LMS - Reports', 'learndash-reports-pro' );
			$button_text          = __( 'UPGRADE NOW', 'learndash-reports-pro' );
			$link                 = 'https://www.learndash.com/reports-by-learndash';
			$wisdm_logo           = WRLD_REPORTS_SITE_URL . '/assets/images/learndash.png';
			$background           = WRLD_REPORTS_SITE_URL . '/assets/images/bfcm-right-during.png';
			$dismiss_attribute    = add_query_arg( array( 'wdm_banner_type' => 'upgrade' ), $current_url );
			include WRLD_REPORTS_PATH . '/includes/templates/bfcm-banner.php';
		}

		/**
		 * Returns html of banners.
		 *
		 * @since 1.4.1
		 * @deprecated 1.8.2
		 */
		public function wrld_bfcm_during_lifetime() {
			_deprecated_function( __METHOD__, '1.8.2' );

			global $wp;
			$current_url = get_permalink( add_query_arg( array(), $wp->request ) );
			if ( is_rtl() ) {
				wp_enqueue_style( 'wrld_bfcm_style', WRLD_REPORTS_SITE_URL . '/assets/css/bfcm.rtl.css', array(), LDRP_PLUGIN_VERSION );
			} else {
				wp_enqueue_style( 'wrld_bfcm_style', WRLD_REPORTS_SITE_URL . '/assets/css/bfcm.css', array(), LDRP_PLUGIN_VERSION );
			}
			$banner_head          = __( 'Save BIG on both Money and Time this Black Friday Cyber Monday Season! ', 'learndash-reports-pro' );
			$banner_message       = __( 'Bridge the gap with a complete Reporting Solution and grow your e-learning business', 'learndash-reports-pro' );
			$banner_message_addon = __(
				'Get up to 70% off on LearnDash LMS - Reports Lifetime Pack
            ',
				'learndash-reports-pro'
			);
			$button_text          = __( 'Upgrade to Lifetime', 'learndash-reports-pro' );
			$link                 = 'https://www.learndash.com/reports-by-learndash';
			$wisdm_logo           = WRLD_REPORTS_SITE_URL . '/assets/images/learndash.png';
			$banner_height        = '200px';
			$background           = WRLD_REPORTS_SITE_URL . '/assets/images/bfcm-right-during.png';
			$dismiss_attribute    = add_query_arg( array( 'wdm_banner_type' => 'lifetime' ), $current_url );
			include WRLD_REPORTS_PATH . '/includes/templates/bfcm-banner.php';
		}
	}
}
