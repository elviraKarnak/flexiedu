<?php
/**
 * Class to control the dashboard page.
 *
 * @package LearnDash\Reports
 *
 * @since 3.0.0
 */

namespace WRLDAdmin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'DashboardPage' ) ) {
	/**
	 * Class for showing tabs of WRLD.
	 */
	class DashboardPage {
		public function __construct() {
			if ( is_rtl() ) {
				// cspell:disable-next-line .
				wp_enqueue_style( 'wrld_admin_dashboard_contentainer_style', WRLD_REPORTS_SITE_URL . '/includes/admin/dashboard/assets/css/content-page.rtl.css', array(), LDRP_PLUGIN_VERSION );
			} else {
				// cspell:disable-next-line .
				wp_enqueue_style( 'wrld_admin_dashboard_contentainer_style', WRLD_REPORTS_SITE_URL . '/includes/admin/dashboard/assets/css/content-page.css', array(), LDRP_PLUGIN_VERSION );
			}
		}

		public static function render() {
			$settings_data     = get_option( 'wrld_settings', false );
			$wrld_page         = get_option( 'ldrp_reporting_page', false );
			$wrld_student_page = get_option( 'ldrp_student_page', false );
			?>
			<div class='wrld-dashboard-page-container'>
				<div class="wrld-dashboard-page-content-wrapper">
					<?php
					if ( $wrld_page && $wrld_page > 0 && 'publish' === get_post_status( $wrld_page ) ) {
						self::content_when_configured( $wrld_page );
					} else {
						self::content_when_not_configured( $wrld_page, $settings_data );
					}

					if ( $wrld_student_page && $wrld_student_page > 0 && 'publish' === get_post_status( $wrld_student_page ) ) {
						self::content_when_student_configured( $wrld_student_page );
					} else {
						self::content_when_student_not_configured( $wrld_student_page, $settings_data );
					}
					?>
				</div>
				<?php
				self::content_sidebar();
				?>
			</div>
			<?php
		}

		/**
		 * Outputs the content when student page is not available (plugin free version).
		 *
		 * @deprecated 3.0.0. This function is no longer used because we don't have the plugin free version.
		 *
		 * @return void
		 */
		public static function content_when_student_not_available() {
			_deprecated_function( __METHOD__, '3.0.0' );
			?>
				<div class='wrld-dashboard-page-content'>
					<div>
						<h3><?php esc_html_e( 'Student Quiz Reports page', 'learndash-reports-pro' ); ?></h3>
						<span class='wrld-dashboard-text'> <?php esc_html_e( 'The Student Quiz Result Reports allows learners to analyze past quiz results and improve their performance accordingly.', 'learndash-reports-pro' ); ?> </span>
						<ul>
							<li>Simple and easy to understand Student Quiz Reports accessible by learners</li>
							<li>Student Quiz Reports Gutenberg block can be embedded on any page</li>
						</ul>
						<br/>
						<span class="wrld-dashboard-text"> <?php esc_html_e( 'Unlock the benefits of the Student Quiz Reports by upgrading to LearnDash LMS - Reports PRO.', 'learndash-reports-pro' ); ?> </span>
					</div>
					<div class='wrld-dashboard-action'>
						<a href="<?php echo esc_url( 'https://www.learndash.com/reports-by-learndash' ); ?>" target="_blank">
						<button class='wrld-dashboard-button-action secondary'><?php echo esc_html( 'Upgrade to PRO' ); ?></button>
						</a>
					</div>
				</div>
			<?php
		}

		public static function content_when_student_not_configured( $wrld_page, $settings_data ) {
			$page_link             = get_edit_post_link( $wrld_page );
			$add_menu_link         = isset( $settings_data['wrld-menu-config-setting'] ) && boolval( $settings_data['wrld-menu-config-setting'] );
			$add_student_menu_link = isset( $settings_data['wrld-menu-student-setting'] ) && boolval( $settings_data['wrld-menu-student-setting'] );
			$action_text           = __( 'Configure page', 'learndash-reports-pro' );
			if ( false == $wrld_page || null == $page_link || 'trash' === get_post_status( $wrld_page ) ) {
				$action_text = __( 'Create page', 'learndash-reports-pro' );
				$page_link   = add_query_arg( array( 'create_student_reports' => true ) );
			}
			?>
				<div class='wrld-dashboard-page-content'>
					<div>
						<h3><?php esc_html_e( 'Student Quiz Reports page', 'learndash-reports-pro' ); ?></h3>
						<span class='wrld-dashboard-text'> <?php esc_html_e( 'This can be accessed at any time from here.', 'learndash-reports-pro' ); ?> </span>
						<br/>
						<span class='wrld-dashboard-text'> <?php esc_html_e( 'Configure the setting below and launch the Student Quiz Reports page.', 'learndash-reports-pro' ); ?> </span>
						<br/>
						<div class='wrld-dashboard-note-container'>
							<span class='wrld-dashboard-note'><strong> <?php esc_html_e( 'Note: ', 'learndash-reports-pro' ); ?> </strong></span>
							<span class='wrld-dashboard-note'> <?php esc_html_e( 'Checking the setting below and configuring will create a page on the front-end of your site which is the Student Quiz Reports page. The link of this page will also get added to your Header Menu.', 'learndash-reports-pro' ); ?> </span>
						</div>
						<div class='wrld-dashboard-config-setting'>
							<?php self::get_add_menu_link_setting( $add_menu_link, $add_student_menu_link );// pending setting ?>
						</div>
					</div>
					<div class='wrld-dashboard-action'>
						<a href="<?php echo esc_attr( $page_link ); ?>">
						<button class='wrld-dashboard-button-action set-config secondary'><?php echo esc_html( $action_text ); ?></button>
						</a>
					</div>
				</div>
			<?php
		}

		public static function content_when_not_configured( $wrld_page, $settings_data ) {
			$page_link             = get_edit_post_link( $wrld_page );
			$add_menu_link         = isset( $settings_data['wrld-menu-config-setting'] ) && boolval( $settings_data['wrld-menu-config-setting'] );
			$add_student_menu_link = isset( $settings_data['wrld-menu-student-setting'] ) && boolval( $settings_data['wrld-menu-student-setting'] );
			$action_text           = __( 'Configure dashboard', 'learndash-reports-pro' );
			if ( false == $wrld_page || null == $page_link || 'trash' === get_post_status( $wrld_page ) ) {
				$action_text = __( 'Create ProPanel Dashboard', 'learndash-reports-pro' );
				$page_link   = add_query_arg( array( 'create_wisdm_reports' => true ) );
			}
			?>
				<div class='wrld-dashboard-page-content grey'>
					<div>
						<h3><?php esc_html_e( 'ProPanel dashboard', 'learndash-reports-pro' ); ?></h3>
						<span class='wrld-dashboard-text'> <?php esc_html_e( 'The ProPanel Dashboard can be accessed at any time from here.', 'learndash-reports-pro' ); ?> </span>
						<br/>
						<span class='wrld-dashboard-text'> <?php esc_html_e( 'Configure the setting below and launch the ProPanel Dashboard.', 'learndash-reports-pro' ); ?> </span>
						<br/>
						<div class='wrld-dashboard-note-container'>
							<span class='wrld-dashboard-note'><strong> <?php esc_html_e( 'Note: ', 'learndash-reports-pro' ); ?> </strong></span>
							<span class='wrld-dashboard-note'> <?php esc_html_e( 'Checking the setting below and launching the dashboard will create a page on the front-end of your site which will include the ProPanel Dashboard and will also add the link to this page to your Header Menu.', 'learndash-reports-pro' ); ?> </span>
						</div>
						<div class='wrld-dashboard-config-setting'>
							<?php self::get_add_menu_link_setting( $add_menu_link, $add_student_menu_link, 'first' );// pending setting ?>
						</div>
						<div class='wrld-dashboard-note-container'>
							<span class='wrld-dashboard-note'> <?php esc_html_e( 'To control the accessibility of the ProPanel Dashboard for Group Leaders and other user roles go to ', 'learndash-reports-pro' ); ?> </span>
							<span class='wrld-dashboard-note'> <a href='admin.php?page=wrld-settings'><?php esc_html_e( 'more settings.', 'learndash-reports-pro' ); ?> </a></span>
						</div>
					</div>
					<div class='wrld-dashboard-action'>
						<a href="<?php echo esc_attr( $page_link ); ?>">
						<button class='wrld-dashboard-button-action set-config'><?php echo esc_html( $action_text ); ?></button>
						</a>
					</div>
				</div>
			<?php
		}

		public static function content_when_configured( $wrld_page ) {
			$page_link = get_post_permalink( $wrld_page );
			?>
				<div class='wrld-dashboard-page-content'>
					<div>
						<h3><?php esc_html_e( 'ProPanel dashboard', 'learndash-reports-pro' ); ?></h3>
						<span class='wrld-dashboard-text'> <?php esc_html_e( 'The ProPanel Dashboard can be accessed at any time from here.', 'learndash-reports-pro' ); ?> </span>
					</div>
					<div class='wrld-dashboard-action'>
						<a href="<?php echo esc_attr( $page_link ); ?>">
						<button class='wrld-dashboard-button-action'><?php esc_html_e( 'View dashboard', 'learndash-reports-pro' ); ?></button>
						</a>
					</div>
				</div>
			<?php
		}

		public static function content_when_student_configured( $wrld_page ) {
			$page_link = get_post_permalink( $wrld_page );
			?>
				<div class='wrld-dashboard-page-content'>
					<div>
						<h3><?php esc_html_e( 'Student Quiz Reports page', 'learndash-reports-pro' ); ?></h3>
						<span class='wrld-dashboard-text'> <?php esc_html_e( 'The Student Quiz Results page can be accessed at any time from here.', 'learndash-reports-pro' ); ?> </span>
					</div>
					<div class='wrld-dashboard-action'>
						<a href="<?php echo esc_attr( $page_link ); ?>">
						<button class='wrld-dashboard-button-action secondary'><?php esc_html_e( 'View page', 'learndash-reports-pro' ); ?></button>
						</a>
					</div>
				</div>
			<?php
		}

		public static function content_sidebar() {
			?>
				<div class='wrld-dashboard-page-sidebar'>
					<?php self::sidebar_block_upgrade(); ?>
					<?php self::sidebar_block_help(); ?>
				</div>
			<?php
		}

		public static function sidebar_block_upgrade() {
			if ( defined( 'LDRP_PLUGIN_VERSION' ) ) {
				return '';
			} else {
				?>
				<div class='wrld-sidebar-block'>
					<div class='wrld-sidebar-block-head'>
						<div class='wrld-sidebar-head-icon'>
							<span class='upgrade-icon'></span>
						</div>
						<div class='wrld-sidebar-head-text'>
							<span><?php esc_html_e( 'Upgrade your FREE LearnDash LMS - Reports Plugin to PRO!', 'learndash-reports-pro' ); ?></span>
						</div>
					</div>
					<div class='wrld-sidebar-body'>
						<div class='wrld-sidebar-body-text'>
							<span><?php esc_html_e( 'Click the button below to upgrade your FREE LearnDash LMS - Reports Plugin to PRO!', 'learndash-reports-pro' ); ?></span>
						</div>
						<a href="https://go.learndash.com/ppaddon" target='__blank'>
							<button class='wrld-sidebar-body-button'><?php esc_html_e( 'Upgrade to PRO', 'learndash-reports-pro' ); ?></button>
						</a>
					</div>
				</div>
				<?php
			}
		}

		public static function sidebar_block_help() {
			?>
			<div class='wrld-sidebar-block'>
					<div class='wrld-sidebar-block-head'>
						<div class='wrld-sidebar-head-icon'>
							<span class='help-icon'></span>
						</div>
						<div class='wrld-sidebar-head-text'>
							<span><?php esc_html_e( 'Looking for help?', 'learndash-reports-pro' ); ?></span>
						</div>
					</div>
					<div class='wrld-sidebar-body'>
						<div class='wrld-sidebar-body-text'>
							<span><?php esc_html_e( 'Need help with creating separate ProPanel dashboards for different user roles?', 'learndash-reports-pro' ); ?></span>
						</div>
						<ul>
						<li><a href="https://go.learndash.com/ppdocs" target='__blank'><?php esc_html_e( 'Documentation', 'learndash-reports-pro' ); ?></a></li>
						</ul>
					</div>
				</div>
			<?php
		}

		public static function get_add_menu_link_setting( $enabled, $enabled2, $show_first = 'second' ) {
			if ( 'first' === $show_first ) {
				?>
					<label for='wrld-menu-config-setting'>
						<input type="checkbox" name="wrld-menu-config-setting" id="wrld-menu-config-setting" class="dashicons" <?php checked( $enabled ); ?>>
						<span><?php esc_html_e( 'Add the link of the ProPanel Dashboard to the Header Menu', 'learndash-reports-pro' ); ?></span>
					</label>
				<?php
			} else {
				?>
				<label for='wrld-menu-student-setting'>
					<input type="checkbox" name="wrld-menu-student-setting" id="wrld-menu-student-setting" class="dashicons" <?php checked( $enabled2 ); ?>>
					<span><?php esc_html_e( 'Add the link of the Student Quiz Reports page to the Header Menu', 'learndash-reports-pro' ); ?></span>
				</label>
				<?php
			}
		}
	}
}
