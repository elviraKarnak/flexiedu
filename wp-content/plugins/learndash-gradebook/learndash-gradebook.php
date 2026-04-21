<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link https://www.learndash.com
 * @since 1.0.0
 * @package LearnDash\Gradebook
 *
 * @wordpress-plugin
 *
 * Plugin Name: Gradebook by LearnDash
 * Plugin URI: https://www.learndash.com/gradebook-by-learndash/
 * Description: Adds Gradebook functionality to LearnDash LMS.
 * Version: 4.3.4
 * Requires PHP: 7.4
 * Requires at least: 6.6
 * Tested up to: 6.9
 * Author: LearnDash
 * Author URI: https://www.learndash.com
 * Text Domain: learndash-gradebook
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'LearnDash_Gradebook' ) ) {
	define( 'LEARNDASH_GRADEBOOK_VERSION', '4.3.4' );
	define( 'LEARNDASH_GRADEBOOK_DIR', plugin_dir_path( __FILE__ ) );
	define( 'LEARNDASH_GRADEBOOK_FILE', __FILE__ );
	define( 'LEARNDASH_GRADEBOOK_URI', trailingslashit( plugins_url( '', __FILE__ ) ) );
	define( 'LEARNDASH_GRADEBOOK_LICENSING_SITE_URL', 'https://checkout.learndash.com/wp-json/learndash/v1/site/auth_token' );
	define( 'LEARNDASH_GRADEBOOK_LICENSING_CHECK_LICENSE_URL', 'https://checkout.learndash.com/wp-json/learndash/v1/site/auth' );

	/**
	 * Class LearnDash_Gradebook
	 *
	 * Initiates the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @package LearnDash_Gradebook
	 */
	final class LearnDash_Gradebook {
		/**
		 * API module.
		 *
		 * @since 1.2.0
		 *
		 * @var LD_GB_API
		 */
		public $api;

		/**
		 * RBM Field Helpers instance.
		 *
		 * @since 1.2.0
		 *
		 * @var RBM_FieldHelpers
		 */
		public $field_helpers;

		/**
		 * Post Types module.
		 *
		 * @since 1.2.0
		 *
		 * @var LD_GB_PostTypes
		 */
		public $posttypes; // @cspell:disable-line.

		/**
		 * Upgrade module.
		 *
		 * @since 1.2.0
		 *
		 * @var LD_GB_Upgrade
		 */
		public $upgrade;

		/**
		 * Admin Pages module.
		 *
		 * @since 1.1.0
		 *
		 * @var LD_GB_AdminPages
		 */
		public $admin_pages;

		/**
		 * Gradebook Page module.
		 *
		 * @since 1.0.0
		 *
		 * @var LD_GB_AdminPage_Gradebook
		 */
		public $gradebook_page;

		/**
		 * Shortcodes module.
		 *
		 * @since 1.0.0
		 *
		 * @var LD_GB_Shortcodes
		 */
		public $shortcodes;

		/**
		 * Blocks module.
		 *
		 * @since 1.0.0
		 *
		 * @var LD_GB_Blocks
		 */
		public $blocks;

		/**
		 * Dashboard Widgets module.
		 *
		 * @since 1.1.0
		 *
		 * @var LD_GB_Dashboard_Widgets
		 */
		public $dashboard_widgets;

		/**
		 * Settings page module.
		 *
		 * @since 1.0.0
		 *
		 * @var LD_GB_AdminPage_Settings
		 */
		public $settings_page;

		/**
		 * User Grades page module.
		 *
		 * @since 1.2.0
		 *
		 * @var LD_GB_AdminPage_UserGrades
		 */
		public $usergrades_page; // cspell: disable-line.

		/**
		 * Notices module.
		 *
		 * @since 1.0.0
		 *
		 * @var LD_GB_Notices
		 */
		public $notices;

		/**
		 * Quickstart module.
		 *
		 * @since 1.0.0
		 *
		 * @var LD_GB_QuickStart
		 */
		public $quickstart;

		/**
		 * Health Check module.
		 *
		 * @since 4.3.0
		 *
		 * @var LD_GB_Health_Check
		 */
		public $health_check;

		/**
		 * Frontend Gradebook module.
		 *
		 * @since 4.3.2
		 *
		 * @var LD_GB_FrontendGradebook
		 */
		public $frontend_gradebook;

		/**
		 * Overall Grade module.
		 *
		 * @since 4.3.2
		 *
		 * @var LD_GB_OverallGrade
		 */
		public $overall_grade;

		/**
		 * Report Card module.
		 *
		 * @since 4.3.2
		 *
		 * @var LD_GB_ReportCard
		 */
		public $report_card;

		/**
		 * Disable cloning.
		 *
		 * @since 1.0.0
		 */
		protected function __clone() {
		}

		/**
		 * Call this method to get singleton
		 *
		 * @since 1.0.0
		 *
		 * @return LearnDash_Gradebook()
		 */
		public static function instance() {
			static $instance = null;

			if ( $instance === null ) {
				$instance = new LearnDash_Gradebook();
			}

			return $instance;
		}

		/**
		 * AC constructor.
		 *
		 * @since 1.0.0
		 */
		private function __construct() {
			$this->load_textdomain();

			$this->require_necessities();

			$this->setup_fieldhelpers();

			add_action( 'init', [ $this, 'register_assets' ] );

			add_action( 'current_screen', [ $this, 'maybe_enqueue_assets' ], 999 );

			if ( isset( $_GET['ld_gb_install_roles'] ) ) {
				add_action( 'init', [ $this, 'setup_roles' ] );
			}

			add_filter( 'learndash_get_label', [ $this, 'learndash_get_label' ], 10, 2 );       }

		/**
		 * Requires all plugin files.
		 *
		 * @since 1.0.0
		 */
		private function require_necessities() {
			require_once LEARNDASH_GRADEBOOK_DIR . 'vendor-prefixed/autoload.php';

			require_once LEARNDASH_GRADEBOOK_DIR . 'core/ld-gb-fieldhelper-functions.php';
			require_once LEARNDASH_GRADEBOOK_DIR . 'core/api/class-ld-gb-api.php';
			require_once LEARNDASH_GRADEBOOK_DIR . 'core/post-types/class-ld-gb-posttypes.php'; // cspell: disable-line.
			require_once LEARNDASH_GRADEBOOK_DIR . 'core/class-ld-gb-upgrade.php';
			require_once LEARNDASH_GRADEBOOK_DIR . 'core/class-ld-gb-user-grade.php';
			require_once LEARNDASH_GRADEBOOK_DIR . 'core/class-ld-gb-frontendgradebook.php'; // cspell: disable-line.
			require_once LEARNDASH_GRADEBOOK_DIR . 'core/class-ld-gb-overallgrade.php'; // cspell: disable-line.
			require_once LEARNDASH_GRADEBOOK_DIR . 'core/class-ld-gb-reportcard.php'; // cspell: disable-line.
			require_once LEARNDASH_GRADEBOOK_DIR . 'core/class-ld-gb-shortcode.php';
			require_once LEARNDASH_GRADEBOOK_DIR . 'core/class-ld-gb-shortcodes.php';
			require_once LEARNDASH_GRADEBOOK_DIR . 'core/class-ld-gb-block.php';
			require_once LEARNDASH_GRADEBOOK_DIR . 'core/class-ld-gb-blocks.php';

			require_once LEARNDASH_GRADEBOOK_DIR . 'core/integrations/learndash-notifications/class-ld-gb-learndash-notifications.php';

			$this->api        = new LD_GB_API();
			$this->posttypes  = new LD_GB_PostTypes(); // @cspell:disable-line.
			$this->upgrade    = new LD_GB_Upgrade();
			$this->shortcodes = new LD_GB_Shortcodes();
			$this->blocks     = new LD_GB_Blocks();

			$this->frontend_gradebook = new LD_GB_FrontendGradebook();
			$this->overall_grade      = new LD_GB_OverallGrade();
			$this->report_card        = new LD_GB_ReportCard();

			// Admin
			if ( is_admin() ) {
				require_once LEARNDASH_GRADEBOOK_DIR . 'admin/includes/class-ld-gb-gradebook-list-table.php';
				require_once LEARNDASH_GRADEBOOK_DIR . 'admin/class-ld-gb-admin-pages.php';
				require_once LEARNDASH_GRADEBOOK_DIR . 'admin/class-ld-gb-adminpage-gradebook.php';
				require_once LEARNDASH_GRADEBOOK_DIR . 'admin/class-ld-gb-adminpage-settings.php';
				require_once LEARNDASH_GRADEBOOK_DIR . 'admin/class-ld-gb-adminpage-user-grades.php';
				require_once LEARNDASH_GRADEBOOK_DIR . 'admin/class-ld-gb-dashboard-widgets.php';
				require_once LEARNDASH_GRADEBOOK_DIR . 'admin/class-ld-gb-notices.php';
				require_once LEARNDASH_GRADEBOOK_DIR . 'admin/class-ld-gb-quickstart.php';

				$this->admin_pages       = new LD_GB_AdminPages();
				$this->gradebook_page    = new LD_GB_AdminPage_Gradebook();
				$this->settings_page     = new LD_GB_AdminPage_Settings();
				$this->usergrades_page   = new LD_GB_AdminPage_UserGrades(); // cspell: disable-line.
				$this->dashboard_widgets = new LD_GB_Dashboard_Widgets();
				$this->notices           = new LD_GB_Notices();

				if ( version_compare( get_bloginfo( 'version' ), '3.3', '>' ) ) {
					$this->quickstart = new LD_GB_QuickStart();
				}
			}

			if ( version_compare( LEARNDASH_VERSION, '4.6.0', '>=' ) ) { // @phpstan-ignore-line -- This constant can be changed.
				require_once LEARNDASH_GRADEBOOK_DIR . 'admin/class-ld-gb-health-check.php';

				$this->health_check = new LD_GB_Health_Check();
			}
		}

		/**
		 * Internationalization
		 *
		 * @access private
		 * @since 3.1.0
		 * @return void
		 */
		private function load_textdomain() {
			// Set filter for language directory.
			$lang_dir = LEARNDASH_GRADEBOOK_DIR . 'languages/';
			$lang_dir = apply_filters( 'learndash_gradebook_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter.
			$locale  = apply_filters( 'plugin_locale', determine_locale(), 'learndash-gradebook' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WP Core hook.
			$mo_file = sprintf( '%1$s-%2$s.mo', 'learndash-gradebook', $locale );

			// Setup paths to current locale file.
			$mo_file_local  = $lang_dir . $mo_file;
			$mo_file_global = trailingslashit( WP_LANG_DIR ) . 'learndash-gradebook/' . $mo_file;

			if ( file_exists( $mo_file_global ) ) {
				// Look in global /wp-content/languages/learndash-gradebook/ folder
				// This way translations can be overridden via the Theme/Child Theme
				load_textdomain( 'learndash-gradebook', $mo_file_global );
			} elseif ( file_exists( $mo_file_local ) ) {
				// Look in local /wp-content/plugins/learndash-gradebook/languages/ folder
				load_textdomain( 'learndash-gradebook', $mo_file_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'learndash-gradebook', false, $lang_dir );
			}
		}

		/**
		 * Initializes Field Helpers.
		 *
		 * @since 1.2.0
		 * @access private
		 */
		private function setup_fieldhelpers() {
			// Last l10n update: RBP Field Helpers v1.4.8
			$this->field_helpers = new RBM_FieldHelpers(
				[
					'ID'   => 'ld_gb',
					'l10n' => [
						'field_table'    => [
							'delete_row'    => _x( 'Delete Row', 'Delete Row text for Table Fields', 'learndash-gradebook' ),
							'delete_column' => _x( 'Delete Column', 'Delete Column text for Table Fields', 'learndash-gradebook' ),
						],
						'field_select'   => [
							'no_options'       => _x( 'No select options.', 'No options text for Select Fields', 'learndash-gradebook' ),
							'error_loading'    => _x( 'The results could not be loaded', 'Results could not be loaded text for Select Fields', 'learndash-gradebook' ),
							/* translators: Delete extra characters message in search for Select fields. %d is number of characters over input limit */
							'input_too_long'   => __( 'Please delete %d character', 'learndash-gradebook' ),
							/* translators: Add more characters message in search for Select fields. %d is number of characters under input limit */
							'input_too_short'  => __( 'Please enter %d or more characters', 'learndash-gradebook' ),
							'loading_more'     => _x( 'Loading more results...', 'Loading more results text for Select Fields', 'learndash-gradebook' ),
							/* translators: Maximum number of items selected text for Select Fields. %d is maximum number items selectable */
							'maximum_selected' => __( 'You can only select %d item(s)', 'learndash-gradebook' ),
							'no_results'       => _x( 'No results found', 'No results found text for Select Fields', 'learndash-gradebook' ),
							'searching'        => _x( 'Searching...', 'Searching status text for Select Fields', 'learndash-gradebook' ),
						],
						'field_repeater' => [
							'collapsable_title' => _x( 'New Row', 'Collapsible Row title text for Repeater Fields', 'learndash-gradebook' ),
							'confirm_delete'    => _x( 'Are you sure you want to delete this element?', 'Row deletion confirmation message for Repeater Fields', 'learndash-gradebook' ),
							'delete_item'       => _x( 'Delete', 'Row delete button text for Repeater Fields', 'learndash-gradebook' ),
							'add_item'          => _x( 'Add', 'Row add button text for Repeater Fields', 'learndash-gradebook' ),
						],
						'field_media'    => [
							'button_text'        => _x( 'Upload / Choose Media', 'Upload/Choose Media button text for Media Fields', 'learndash-gradebook' ),
							'button_remove_text' => _x( 'Remove Media', 'Remove Media button text for Media Fields', 'learndash-gradebook' ),
							'window_title'       => _x( 'Choose Media', 'Choose Media window title for Media Fields', 'learndash-gradebook' ),
						],
						'field_checkbox' => [
							'no_options_text' => _x( 'No options available.', 'No options text for Checkbox Fields', 'learndash-gradebook' ),
						],
					],
				]
			);
		}

		/**
		 * Registers all plugin assets.
		 *
		 * @since 1.0.0
		 * @access private
		 */
		function register_assets() {
			/**
			 * Report Card frontend style.
			 *
			 * @since 2.0.0
			 */
			wp_register_style(
				'ld-gb-report-card',
				LEARNDASH_GRADEBOOK_URI . 'dist/css/report-card' . learndash_min_asset() . '.css',
				apply_filters( 'ld_gb_report_card_style_dependencies', [] ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : LEARNDASH_GRADEBOOK_VERSION
			);

			/**
			 * Frontend Gradebook style.
			 *
			 * @since 2.0.0
			 */
			wp_register_style(
				'ld-gb-frontend-gradebook',
				apply_filters( 'ld_gb_frontend_gradebook_style_src', LEARNDASH_GRADEBOOK_URI . 'dist/css/frontend-gradebook-styles' . learndash_min_asset() . '.css' ),
				apply_filters( 'ld_gb_frontend_gradebook_style_dependencies', [ 'dashicons' ] ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : LEARNDASH_GRADEBOOK_VERSION
			);

			/**
			 * Frontend Gradebook script
			 *
			 * @since 2.0.0
			 */
			wp_register_script(
				'ld-gb-frontend-gradebook',
				LEARNDASH_GRADEBOOK_URI . 'dist/js/frontend-gradebook-scripts' . learndash_min_asset() . '.js',
				apply_filters( 'ld_gb_frontend_gradebook_script_dependencies', [ 'jquery' ] ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : LEARNDASH_GRADEBOOK_VERSION,
				true
			);

			wp_localize_script(
				'ld-gb-frontend-gradebook',
				'LD_GB_FrontendGradebook',
				apply_filters(
					'ld_gb_frontend_gradebook_localize_script',
					[
						'l10n' => [
							'ldFrontendGradebookTableListOptions' => [
								// These are the CSS Classes for fields to search in
								'valueNames' => [
									'id',
								],
								'page'       => apply_filters( 'ld_gb_frontend_gradebook_per_page', 15, 0, 0 ), // This is the number of results per-page
								'pagination' => true,
								'listClass'  => 'ld-gb-frontend-gradebook-list-tbody', // The CSS Class of our Table
							],
							'nonce' => wp_create_nonce( 'wp_rest' ),
							'rest'  => trailingslashit( esc_url_raw( rest_url( 'ld-gb/v1' ) ) ),
							'uri'   => LEARNDASH_GRADEBOOK_URI,
						],
						'i18n' => [
							'validationError' => _x( 'This field is required', 'Required field message for the Frontend Gradebook', 'learndash-gradebook' ),
							'deleteGrade'     => _x( 'Are you sure you want to delete this grade?', 'Grade deletion confirmation message for the Frontend Gradebook', 'learndash-gradebook' ),
							'select2Warning'  => _x( 'Select2 has been loaded by a plugin other than Gradebook by LearnDash. This may cause problems.', 'Select2 already loaded warning message', 'learndash-gradebook' ),
						],
					]
				)
			);

			/**
			 * Global admin style.
			 *
			 * @since 1.0.0
			 */
			wp_register_style(
				'ld-gb-admin',
				LEARNDASH_GRADEBOOK_URI . 'dist/css/admin-styles' . learndash_min_asset() . '.css',
				[],
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : LEARNDASH_GRADEBOOK_VERSION
			);

			/**
			 * Global admin script.
			 *
			 * @since 1.0.0
			 */
			wp_register_script(
				'ld-gb-admin',
				LEARNDASH_GRADEBOOK_URI . 'dist/js/admin-scripts' . learndash_min_asset() . '.js',
				[ 'jquery' ],
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : LEARNDASH_GRADEBOOK_VERSION,
				true
			);
		}

		/**
		 * Ensures that Gradebook's assets only ever load on Admin pages where they're expected to load.
		 *
		 * @access public
		 * @since 1.4.7
		 * @since 4.3.1    Adjusted to explicitly check for any admin screen that should load Gradebook Assets
		 * @return void
		 */
		public function maybe_enqueue_assets() {
			global $current_screen;

			if (
				strpos( $current_screen->base, 'gradebook' ) === false
				&&
				$current_screen->id !== 'gradebook'
				&&
				$current_screen->id !== 'dashboard'
			) {
				learndash_gradebook_remove_class_action( 'admin_enqueue_scripts', 'RBM_FieldHelpers', 'enqueue_scripts' );
			} else {
				add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_assets' ] );
			}       }

		/**
		 * Enqueues all global plugin admin assets.
		 *
		 * @since 1.0.0
		 * @access private
		 */
		function admin_enqueue_assets() {
			$current_screen = get_current_screen();

			// Data
			$data = apply_filters(
				'ld_gb_admin_script_data',
				[
					'current_user_id' => get_current_user_id(),
					'l10n'            => [
						'no_users'          => _x( 'No Users', 'No Users message for the Gradebook Dashboard Widget', 'learndash-gradebook' ),
						'component_no_name' => _x( '- No Name -', 'Component without a name message for the Gradebook Dashboard Widget', 'learndash-gradebook' ),
						'select_a_user'     => _x( 'Select a User', 'Select a user message for the Gradebook Dashboard Widget', 'learndash-gradebook' ),
					],
				]
			);

			wp_localize_script( 'ld-gb-admin', 'LD_GB_Admin', $data );

			// Global admin style
			wp_enqueue_style( 'ld-gb-admin' );
			wp_style_add_data( 'ld-gb-admin', 'rtl', 'replace' );

			// Global admin script
			wp_enqueue_script( 'ld-gb-admin' );
		}

		/**
		 * Manually setup roles when called.
		 *
		 * @since 1.3.4
		 * @access private
		 */
		function setup_roles() {
			LD_GB_Install::setup_capabilities();
		}

		/**
		 * Add support for Assignments in LearnDash_Custom_Label::get_label()
		 * This only operates as a fallback, so if support is added in for Assignments in the future this will not run
		 *
		 * @param string $label  Label
		 * @param string $key    Key
		 *
		 * @access public
		 * @since 1.6.0
		 * @return string          Label
		 */
		public function learndash_get_label( $label, $key ) {
			if ( $label !== '' ) {
				return $label;
			}

			if ( $key == 'assignment' ) {
				return __( 'Assignment', 'learndash' );
			}

			if ( $key == 'assignments' ) {
				return __( 'Assignments', 'learndash' );
			}

			return $label;      }
	}

	// Helper functions
	require_once LEARNDASH_GRADEBOOK_DIR . 'core/ld-gb-functions.php';

	// Load the bootstrapper
	require_once LEARNDASH_GRADEBOOK_DIR . 'learndash-gradebook-bootstrapper.php';
	new LearnDash_Gradebook_Bootstrapper();

	// Install the plugin
	require_once LEARNDASH_GRADEBOOK_DIR . 'core/class-ld-gb-install.php';
	register_activation_hook( __FILE__, [ 'LD_GB_Install', 'install' ] );

	/**
	 * Gets the main class object.
	 *
	 * Used to instantiate the plugin class for the first time and then used subsequent times to return the existing object.
	 *
	 * @since 1.0.0
	 *
	 * @return LearnDash_Gradebook
	 */
	function LearnDash_Gradebook() {
		return LearnDash_Gradebook::instance();
	}
}
