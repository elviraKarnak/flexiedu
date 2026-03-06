<?php
/**
 * Renders the Whatsnew tab.
 *
 * @package LearnDash_Reports
 *
 * @since 3.0.0
 * @deprecated 3.0.0 This file is no longer used.
 */

namespace WRLDAdmin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

_deprecated_file( __FILE__, '3.0.0', 'This file is no longer used.' );

// cspell:ignore whatsnew whatsnewtab .

if ( ! class_exists( 'WhatsnewPage' ) ) {
	/**
	 * Class for showing tabs of WRLD.
	 */
	class WhatsnewPage {
		/**
		 * Constructor.
		 *
		 * @deprecated 3.0.0
		 */
		public function __construct() {
			_deprecated_constructor( __METHOD__, '3.0.0' );

			if ( is_rtl() ) {
				// cspell:disable-next-line .
				wp_enqueue_style( 'wrld_admin_dashboard_contentainer_style', WRLD_REPORTS_SITE_URL . '/includes/admin/dashboard/assets/css/content-page.rtl.css', array(), LDRP_PLUGIN_VERSION );
				wp_enqueue_style( 'wrld_admin_whatsnew_tab_style', WRLD_REPORTS_SITE_URL . '/includes/admin/dashboard/assets/css/whatsnewtab.rtl.css', array(), LDRP_PLUGIN_VERSION );
			} else {
				// cspell:disable-next-line .
				wp_enqueue_style( 'wrld_admin_dashboard_contentainer_style', WRLD_REPORTS_SITE_URL . '/includes/admin/dashboard/assets/css/content-page.css', array(), LDRP_PLUGIN_VERSION );
				wp_enqueue_style( 'wrld_admin_whatsnew_tab_style', WRLD_REPORTS_SITE_URL . '/includes/admin/dashboard/assets/css/whatsnewtab.css', array(), LDRP_PLUGIN_VERSION );
			}
		}

		/**
		 * Renders the Whatsnew tab.
		 *
		 * @deprecated 3.0.0
		 *
		 * @return void
		 */
		public static function render() {
			_deprecated_function( __METHOD__, '3.0.0' );
			?>
			<div class='wrld-whatsnew-container'>
				<?php
					self::get_current_installed_version_banner();
					self::get_post_section();
				?>
			</div>
			<?php
		}

		/**
		 * Outputs the current installed version banner.
		 *
		 * @deprecated 3.0.0
		 *
		 * @return void
		 */
		public static function get_current_installed_version_banner() {
			_deprecated_function( __METHOD__, '3.0.0' );
			?>

				<div class="wrld-whatsnew-header">
					<h2><?php esc_html_e( 'LearnDash LMS - Reports', 'learndash-reports-pro' ); ?>  <b> <?php esc_html_e( 'Version', 'learndash-reports-pro' ); ?> <?php echo LDRP_PLUGIN_VERSION; ?></b></h2></p>
				</div>
			<?php
		}

		/**
		 * Outputs the post section.
		 *
		 * @deprecated 3.0.0
		 *
		 * @return void
		 */
		public static function get_post_section() {
			_deprecated_function( __METHOD__, '3.0.0' );

			include_once 'templates/post-container.php';
		}
	}
}
