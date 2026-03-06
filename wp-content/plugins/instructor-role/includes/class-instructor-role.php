<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization and
 * all module hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since 3.5.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 *
 * cspell:ignore instuctor persistant certi // ignoring misspelled words that we can't change now.
 */

namespace InstructorRole\Includes;

defined( 'ABSPATH' ) || exit;

/**
 * Core Classes
 */
use InstructorRole\Includes\Instructor_Role_Loader;
use InstructorRole\Includes\Instructor_Role_I18n;

/**
 * Module Classes
 */
use InstructorRole\Modules\Classes\Instructor_Role_Admin;
use InstructorRole\Modules\Classes\Instructor_Role_Comments;
use InstructorRole\Modules\Classes\Instructor_Role_Groups;
use InstructorRole\Modules\Classes\Instructor_Role_Multiple_Instructors;
use InstructorRole\Modules\Classes\Instructor_Role_Notifications;
use InstructorRole\Modules\Classes\Instructor_Role_Payouts;
use InstructorRole\Modules\Classes\Instructor_Role_Profile;
use InstructorRole\Modules\Classes\Instructor_Role_Reports;
use InstructorRole\Modules\Classes\Instructor_Role_Emails;
use InstructorRole\Modules\Classes\Instructor_Role_Woocommerce;
use InstructorRole\Modules\Classes\Instructor_Role_LearnDash_Handler;
use InstructorRole\Modules\Classes\Instructor_Role_LearnDash_Menu_Handler;
use InstructorRole\Modules\Classes\Instructor_Role_Commission;
use InstructorRole\Modules\Classes\Instructor_Role_Review;
use InstructorRole\Modules\Classes\Instructor_Role_Settings;
use InstructorRole\Modules\Classes\Instructor_Role_Student_Communication;
use InstructorRole\Modules\Classes\Instructor_Role_Dashboard;
use InstructorRole\Modules\Classes\Instructor_Role_User_Switch;
use InstructorRole\Modules\Classes\Instructor_Role_Frontend_Dashboard;
use InstructorRole\Modules\Classes\Instructor_Role_Dashboard_Block;
use InstructorRole\Modules\Classes\Instructor_Role_Quiz_Statistics_Export;

/**
 * Other Classes
 */
use InstructorRole\Modules\Shortcodes\Instructor_Role_Shortcode_Handler;
use InstructorRole\Modules\Api\Instructor_Role_Api_Handler;
use InstructorRole\Modules\Api\Instructor_Role_Dashboard_Block_Api_Handler;
use InstructorRole\Modules\Api\Instructor_Role_Woocommerce_Api_Handler;
use InstructorRole\Modules\Api\Instructor_Role_Comments_Api_Handler;
use InstructorRole\Modules\Api\Instructor_Role_Essays_Api_Handler;
use InstructorRole\Modules\Api\Instructor_Role_Submissions_Api_Handler;
use InstructorRole\Modules\Api\Instructor_Role_Course_Reports_Api_Handler;
use InstructorRole\Modules\Api\Instructor_Role_Quiz_Attempts_Api_Handler;
use InstructorRole\Modules\Api\Instructor_Role_Groups_Api_Handler;
use InstructorRole\Modules\Api\Instructor_Role_Certificates_Api_Handler;

/**
 * Instructor Role core class
 */
class Instructor_Role {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since 3.5.0
	 * @access protected
	 * @var Instructor_Role_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 3.5.0
	 * @access protected
	 * @var string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since 3.5.0
	 * @access protected
	 * @var string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {
		if ( defined( 'INSTRUCTOR_ROLE_PLUGIN_VERSION' ) ) {
			$this->version = INSTRUCTOR_ROLE_PLUGIN_VERSION;
		} else {
			$this->version = '5.4.0';
		}
		$this->plugin_name = 'instructor-role';
		$this->load_dependencies();
		$this->handle_activation();
		$this->set_locale();

		$this->define_admin_hooks();
		$this->define_module_hooks();
		$this->define_shortcodes();
		$this->define_api_endpoints();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Instructor_Role_Loader. Orchestrates the hooks of the plugin.
	 * - Instructor_Role_I18n. Defines internationalization functionality.
	 * - Instructor_Role_Admin. Defines all hooks for the admin area.
	 * - Instructor_Role_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since 3.5.0
	 * @access private
	 */
	private function load_dependencies() {
		/**
		 * ---------------------- Licensing Files ----------------------
		 */

		/**
		 * The class responsible for handling licensing functionalities of the
		 * core plugin.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'licensing/class-wdm-license.php';
		require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/class-instructor-role-license.php';

		/**
		 * ---------------------- Core Files -----------------------------
		 */

		/**
		 * The class responsible for handling activation functionalities of the
		 * core plugin.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/class-instructor-role-activator.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/class-instructor-role-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/class-instructor-role-i18n.php';

		/**
		 * The file responsible for defining common functionality
		 * of the plugin.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/instructor-role-functions.php';

		/**
		 * The file responsible for defining common static variables
		 * of the plugin.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/instructor-role-constants.php';

		/**
		 * The file responsible for handling deprecated functionality
		 * of the plugin.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/instructor-role-deprecated.php';

		/**
		 * The file responsible for handling admin notices
		 * of the plugin.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/instructor-role-admin-notices.php';

		/**
		 * -------------------------- Module Files --------------------------
		 */

		/**
		 * The file responsible for handling core admin functionality
		 * of the plugin
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-admin.php';

		/**
		 * The class responsible for defining all actions to control comments related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-comments.php';

		/**
		 * The class responsible for defining all actions to control group related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-groups.php';

		/**
		 * The class responsible for defining all actions to control multiple instructors related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-multiple-instructors.php';

		/**
		 * The class responsible for defining all actions to control notifications related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-notifications.php';

		/**
		 * The class responsible for defining all actions to control payouts related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-payouts.php';

		/**
		 * The class responsible for defining all actions to control profile related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-profile.php';

		/**
		 * The class responsible for defining all actions to control reports related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-reports.php';

		/**
		 * The class responsible for defining all actions to control emails related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-emails.php';

		/**
		 * The class responsible for defining all actions to control woocommerce related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-woocommerce.php';

		/**
		 * The class responsible for defining all actions to control learndash handling related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-learndash-handler.php';

		/**
		 * The class responsible for defining all actions to control learndash menu handling related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-learndash-menu-handler.php';

		/**
		 * The class responsible for defining all actions to control commission related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-commission.php';

		/**
		 * The class responsible for defining all actions to control review related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-review.php';

		/**
		 * The class responsible for defining all actions to control student communication related functionalities.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-student-communication.php';

		/**
		 * The class responsible for defining all actions to control dashboard related functionalities.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-dashboard.php';

		/**
		 * The class responsible for defining all actions to control frontend dashboard related functionalities.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-frontend-dashboard.php';

		/**
		 * The class responsible for defining all actions to control user-switching related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-userswitch.php'; // cspell:disable-line .

		/**
		 * The class responsible for defining all actions to control the frontend dashboard block related functionalities.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-dashboard-block.php';

		/**
		 * The class responsible for defining all actions to control settings related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-settings.php';

		/**
		 * -------------------------- Shortcode Files --------------------------
		 */

		/**
		 * The class responsible for defining all actions to control shortcode related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/shortcodes/class-instructor-role-shortcode-handler.php';

		/**
		 * -------------------------- Rest API Files --------------------------
		 */

		/**
		 * The class responsible for defining all actions to control api related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/api/class-instructor-role-api-handler.php';

		/**
		 * The class responsible for defining all actions to control dashboard block api related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/api/class-instructor-role-dashboard-block-api-handler.php';

		/**
		 * The class responsible for defining all actions to control woocommerce api related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/api/class-instructor-role-woocommerce-api-handler.php';

		/**
		 * The class responsible for defining all actions to control comments api related functionalities.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/api/class-instructor-role-comments-api-handler.php';

		/*
		 * The class responsible for defining all actions to control essays api related functionalities.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/api/class-instructor-role-essays-api-handler.php';

		/**
		 * The class responsible for defining all actions to control submissions api related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/api/class-instructor-role-submissions-api-handler.php';

		/**
		 * The class responsible for defining all actions to control course reports api related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/api/class-instructor-role-course-reports-api-handler.php';

		/**
		 * The class responsible for defining all actions to control quiz attempts api related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/api/class-instructor-role-quiz-attempts-api-handler.php';

		/**
		 * The class responsible for defining all actions to control quiz attempts api related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/api/class-instructor-role-groups-api-handler.php';

		/**
		 * The class responsible for defining all actions to control certificates api related functionalities
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/api/class-instructor-role-certificates-api-handler.php';

		/**
		 * -------------------------- Quiz Questions Processing files --------------------------
		 */

		/**
		 * The class responsible for defining all actions to fetch question attempt related data
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-question-data.php';

		/**
		 * The class responsible for defining all actions to fetch assessment question attempt related data.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-assessment-question-data.php';

		/**
		 * The class responsible for defining all actions to fetch cloze question attempt related data
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-cloze-question-data.php';

		/**
		 * The class responsible for defining all actions to fetch essay question attempt related data
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-essay-question-data.php';

		/**
		 * The class responsible for defining all actions to fetch free question attempt related data
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-free-question-data.php';

		/**
		 * The class responsible for defining all actions to fetch single question attempt related data
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-single-question-data.php';

		/**
		 * The class responsible for defining all actions to fetch sort question attempt related data
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-sort-question-data.php';

		/**
		 * The class responsible for defining all actions to fetch matrix sort question attempt related data
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-matrix-sort-question-data.php';

		/**
		 * -------------------------- Quiz Export Files --------------------------
		 */

		/**
		 * The class responsible for defining all actions to export quiz statistics data.
		 */
		require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-quiz-statistics-export.php';

		/**
		 * Load Composer Packages.
		 */
		// require_once INSTRUCTOR_ROLE_ABSPATH . 'libs/vendor/autoload.php';.

		$this->loader = new Instructor_Role_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Instructor_Role_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since 3.5.0
	 * @access private
	 */
	private function set_locale() {
		$plugin_i18n = new Instructor_Role_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain', 100 );
	}

	/**
	 * Handles some actions related to the activation/setup.
	 */
	private function handle_activation() {
		$plugin_activator = new Instructor_Role_Activator();

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_activator, 'enqueue_plugin_update_css' );
		$this->loader->add_action( 'admin_init', $plugin_activator, 'identify_customer_type' );
		$this->loader->add_action( 'admin_footer', $plugin_activator, 'display_new_features_pop_up' );
		$this->loader->add_action( 'admin_notices', $plugin_activator, 'display_new_features_notice' );
		$this->loader->add_action( 'wp_ajax_fdb_introduced', $plugin_activator, 'ajax_fdb_introduced' );
		$this->loader->add_action( 'wp_ajax_fdb_update_pattern', $plugin_activator, 'ajax_fdb_update_pattern' );
		$this->loader->add_action( 'wp_ajax_dismiss_pattern_update_popup', $plugin_activator, 'ajax_add_pattern_update_notice' );
		$this->loader->add_action( 'wp_ajax_new_features_popup_dismissed', $plugin_activator, 'ajax_new_features_popup_dismissed' );
	}

	/**
	 * Define admin side functionality
	 *
	 * @since 3.5.0
	 * @access private
	 */
	private function define_admin_hooks() {
		$plugin_admin = Instructor_Role_Admin::get_instance();

		$this->loader->add_action( 'learndash_settings_pages_init', $plugin_admin, 'load_overview_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wdmir_set_post_types' );
		$this->loader->add_filter( 'pre_get_posts', $plugin_admin, 'wdm_set_author' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'wdmLoadScriptsAll' );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'hide_update_notice_to_all_but_admin_users', 1 );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wdm_remove_dashboard_tab', 99 );

		$this->loader->add_action( 'current_screen', $plugin_admin, 'wdm_this_screen' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wdm_remove_admin_menus', 999 );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wdm_remove_dashboard_widgets' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'show_admin_messages' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wdm_remove_copy_question_action' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wdm_reset_author_metabox' );
		$this->loader->add_action( 'init', $plugin_admin, 'wdm_set_capabilities' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wdmirAddDashboardTabs', 999 );
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'wdm_remove_top_menu', 1 );
		$this->loader->add_filter( 'admin_head', $plugin_admin, 'ir_remove_help_tab' );
		$this->loader->add_filter( 'edd_add_ons_tabs', $plugin_admin, 'irRemoveEddTabs' );
		$this->loader->add_action( 'after_setup_theme', $plugin_admin, 'register_instructor_menu_setting', 100 );
		$this->loader->add_filter( 'login_redirect', $plugin_admin, 'irInstructorRedirect', 999, 3 );
		$this->loader->add_filter( 'woocommerce_login_redirect', $plugin_admin, 'irWooInstructorRedirect', 10, 2 );
		$this->loader->add_filter( 'sfwd_lms_has_access', $plugin_admin, 'irAutoEnrollInstructorCourses', 10, 3 );
		$this->loader->add_filter( 'learndash_prerequities_bypass', $plugin_admin, 'irByPassInstructorLinearAccess', 10, 4 ); // cspell:disable-line .
		$this->loader->add_filter( 'learndash_previous_step_completed', $plugin_admin, 'irByPassInstructorPreviousCompleted', 10, 3 );
		$this->loader->add_filter( 'learndash_user_can_bypass', $plugin_admin, 'ir_bypass_instructor_user_access', 10, 4 );
		$this->loader->add_filter( 'ld_template_args_profile', $plugin_admin, 'irUpdateInstructorProfileCourses', 10 );
		$this->loader->add_filter( 'ir_filter_deny_page_access', $plugin_admin, 'irRestrictJetpackAccess', 10, 2 );
		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'irEnableUserMenusForInstructors', 100 );
		$this->loader->add_filter( 'wp_insert_post_data', $plugin_admin, 'irUpdateCourseToInstructors', 10, 2 );
		$this->loader->add_filter( 'learndash_header_data', $plugin_admin, 'updateLearnDashDataForInstructors', 999, 1 );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'ir_add_instructor_dashboard_menu_items' );

		$this->loader->add_filter( 'rest_user_query', $plugin_admin, 'irFilterAuthorDropDown', 10, 2 );
		$this->loader->add_filter( 'ir_filter_instructor_query', $plugin_admin, 'get_valid_course_steps_legacy', 10, 1 );
		// Additional Settings.
		$this->loader->add_filter( 'learndash_post_args', $plugin_admin, 'toggle_category_settings' );
		$this->loader->add_action( 'do_meta_boxes', $plugin_admin, 'toggle_instructor_metaboxes' );
		$this->loader->add_filter( 'fw_post_options', $plugin_admin, 'toggle_elumine_metaboxes' );
		$this->loader->add_action( 'wdmir_settings_save_after', $plugin_admin, 'save_additional_instructor_settings' );
		$this->loader->add_filter( 'learndash_settings_fields', $plugin_admin, 'update_instructor_course_pricing_options', 10, 2 );
		$this->loader->add_filter( 'learndash_listing_selectors', $plugin_admin, 'update_instructor_listing', 100, 2 );
		// White labelling.
		$this->loader->add_filter( 'gettext', $plugin_admin, 'whitelabel_learndash_strings', 100, 3 );
		$this->loader->add_filter( 'gettext_with_context', $plugin_admin, 'whitelabel_learndash_context_strings', 100, 4 );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'disable_ld_more_help_section' );

		// Remove post count view details.
		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'filter_list_table_views', 100 );

		// Add group administration menu.
		$this->loader->add_filter( 'learndash_submenu', $plugin_admin, 'ir_add_ld_group_administration_submenu', 999 );
		// Mandatory modules.
		$this->define_dashboard_module_hooks();

		$this->loader->add_action( 'admin_footer', $plugin_admin, 'load_beacon_helpscout_cpt_form_edit' );  }

	/**
	 * Register all of the module hooks
	 *
	 * @since 3.5.0
	 * @access private
	 */
	private function define_module_hooks() {
		// Fetch updated list of active modules.
		$modules = $this->loader->fetch_active_modules();

		// If empty, fetch default modules list.
		if ( false === $modules ) {
			$this->loader->set_default_active_modules();
			$modules = $this->loader->fetch_active_modules();
		}

		// Define all active module action and filter hooks.
		foreach ( $modules as $module ) {
			// check if the method exist before calling it.
			if ( method_exists( $this, 'define_' . $module . '_module_hooks' ) ) {
				call_user_func( [ $this, 'define_' . $module . '_module_hooks' ] );
			}
		}
	}

	/**
	 * Register all of the hooks related to the comments module functionality
	 * of the plugin.
	 *
	 * @since 3.5.0
	 * @access private
	 */
	private function define_comments_module_hooks() {
		$module_comments = Instructor_Role_Comments::get_instance();
		$this->loader->add_filter( 'wdmir_add_dash_tabs', $module_comments, 'enable_comments_screen_access', 10, 1 );
		$this->loader->add_filter( 'wdmir_set_post_types', $module_comments, 'allow_comments_access', 10, 1 );
		$this->loader->add_filter( 'comments_clauses', $module_comments, 'filter_instructor_comments', 10, 2 );
		$this->loader->add_filter( 'ir_filter_instructor_query', $module_comments, 'filter_instructor_comment_queries', 10, 1 );
		$this->loader->add_filter( 'user_has_cap', $module_comments, 'allow_shared_course_comments_access', 10, 4 );
		$this->loader->add_filter( 'comment_notification_recipients', $module_comments, 'alter_comment_notification_recipients', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the groups module functionality
	 * of the plugin.
	 *
	 * @since 3.5.0
	 * @access private
	 */
	private function define_groups_module_hooks() {
		$module_groups = Instructor_Role_Groups::get_instance();

		// Add group leader caps.
		$this->loader->add_filter( 'admin_init', $module_groups, 'add_group_capabilities' );

		// Allow access to groups.
		$this->loader->add_filter( 'wdmir_set_post_types', $module_groups, 'enable_access_to_groups_post_type' );

		// Filter group users and leaders for instructors.
		$this->loader->add_filter( 'learndash_binary_selector_args', $module_groups, 'filter_selector_group_users', 10, 2 );

		// Filter group courses to include shared courses for instructors.
		$this->loader->add_filter( 'learndash_binary_selector_args', $module_groups, 'filter_selector_group_courses', 10, 2 );

		// Include instructors in list of group leaders.
		$this->loader->add_filter( 'learndash_binary_selector_args', $module_groups, 'add_instructors_to_group_leaders_for_admin', 10, 2 );

		// Filter groups accessible to instructor on course edit page.
		$this->loader->add_filter( 'learndash_binary_selector_args', $module_groups, 'filter_instructor_groups_for_course', 10, 2 );

		// Filter groups list on course edit page.
		$this->loader->add_filter( 'ir_filter_instructor_query', $module_groups, 'ir_filter_course_group_tab_data', 10, 1 );

		// Filter group administration screen data.
		$this->loader->add_filter( 'ir_filter_instructor_query', $module_groups, 'ir_filter_group_admin_screen_data', 10, 1 );

		// Filter group courses on groups edit screen.
		$this->loader->add_filter( 'ir_filter_instructor_query', $module_groups, 'ir_filter_group_courses', 10, 1 );

		// Remove secondary author dropdown on groups edit page.
		$this->loader->add_filter( 'rest_user_query', $module_groups, 'remove_secondary_author_dropdown', 10, 2 );

		// Filter group list on groups listing page.
		$this->loader->add_action( 'parse_query', $module_groups, 'ir_filter_group_list', 10, 1 );

		// Remove group leader restrictions for assignment access.
		$this->loader->add_action( 'init', $module_groups, 'ir_remove_group_leader_restrictions', 11 ); }

	/**
	 * Register all of the hooks related to the multiple instructors module functionality
	 * of the plugin.
	 *
	 * @since 3.5.0
	 * @access private
	 */
	private function define_multiple_instructors_module_hooks() {
		$module_multiple_instructors = Instructor_Role_Multiple_Instructors::get_instance();

		$this->loader->add_action( 'add_meta_boxes', $module_multiple_instructors, 'addCourseShareMetabox' );
		$this->loader->add_action( 'admin_enqueue_scripts', $module_multiple_instructors, 'enqueueCourseSharingScripts' );
		$this->loader->add_action( 'save_post_sfwd-courses', $module_multiple_instructors, 'saveSharedInstructorMeta', 10, 2 );

		$this->loader->add_filter( 'user_has_cap', $module_multiple_instructors, 'enableMultipleInstructors', 10, 4 );
		$this->loader->add_filter( 'posts_where_request', $module_multiple_instructors, 'filterInstructorCourseList', 10, 1 );
		$this->loader->add_filter( 'ir_filter_instructor_query', $module_multiple_instructors, 'allowCourseAccessToInstructors', 10, 1 );
		$this->loader->add_filter( 'ir_filter_instructor_query', $module_multiple_instructors, 'allowCourseContentAccessToInstructors', 10, 1 );
		$this->loader->add_filter( 'ir_filter_quiz_access', $module_multiple_instructors, 'allowQuestionAccessToInstructors', 10, 2 );
		$this->loader->add_filter( 'ir_filter_pro_quiz_access', $module_multiple_instructors, 'allowQuestionAccessToInstructors', 10, 2 );

		$this->loader->add_action( 'save_post_sfwd-question', $module_multiple_instructors, 'clearLDQuizDirtyFlag', 100, 3 );

		// Filter users accessible to instructor on course edit page.
		$this->loader->add_filter( 'learndash_binary_selector_args', $module_multiple_instructors, 'filter_instructor_users_for_course', 100, 2 );
		$this->loader->add_filter( 'learndash_settings_fields', $module_multiple_instructors, 'filter_instructor_shared_course_contents', 100, 2 );

		$this->loader->add_action( 'wp', $module_multiple_instructors, 'remove_course_step_check_for_instructors', 9 );
	}

	/**
	 * Register all of the hooks related to the notifications module functionality
	 * of the plugin.
	 *
	 * @since 3.5.0
	 * @access private
	 */
	private function define_notifications_module_hooks() {
		// If LearnDash Notifications plugin not activated then return.
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( ! is_plugin_active( 'learndash-notifications/learndash-notifications.php' ) ) {
			return;
		}

		$module_notifications = Instructor_Role_Notifications::get_instance();

		$this->loader->add_filter( 'wdmir_set_post_types', $module_notifications, 'allow_notifications_access', 10, 1 );
		$this->loader->add_filter( 'learndash_submenu', $module_notifications, 'enable_notifications_screen_access', 10, 1 );
		$this->loader->add_filter( 'user_has_cap', $module_notifications, 'override_notification_privileges_for_instructors', 10, 4 );
		$this->loader->add_filter( 'learndash_notifications_recipients', $module_notifications, 'add_instructor_in_recipients_list', 10, 1 );
		$this->loader->add_filter( 'learndash_notification_recipients_emails', $module_notifications, 'add_instructor_email_in_recipients_emails', 10, 4 );
		$this->loader->add_filter( 'learndash_notification_settings', $module_notifications, 'update_instructor_notification_settings', 10, 1 );
		$this->loader->add_filter( 'wdmir_exclude_post_types', $module_notifications, 'allow_notification_post_type_access' );
	}

	/**
	 * Register all of the hooks related to the payouts module functionality
	 * of the plugin.
	 *
	 * @since 3.5.0
	 * @access private
	 */
	private function define_payouts_module_hooks() {
		$module_payouts = Instructor_Role_Payouts::get_instance();

		$this->loader->add_action( 'admin_init', $module_payouts, 'create_payouts_table', 10, 0 );
		$this->loader->add_action( 'show_user_profile', $module_payouts, 'add_instructor_payout_fields', 10, 1 );
		$this->loader->add_action( 'edit_user_profile', $module_payouts, 'add_instructor_payout_fields', 10, 1 );
		$this->loader->add_action( 'personal_options_update', $module_payouts, 'save_instructor_payout_details', 10, 1 );
		$this->loader->add_action( 'edit_user_profile_update', $module_payouts, 'save_instructor_payout_details', 10, 1 );
		$this->loader->add_filter( 'ir_filter_instructor_setting_tabs', $module_payouts, 'add_payouts_admin_settings_tab', 10, 2 );
		$this->loader->add_action( 'instuctor_tab_checking', $module_payouts, 'add_payouts_admin_settings_tab_content', 10, 1 );
		$this->loader->add_action( 'admin_init', $module_payouts, 'save_payouts_admin_settings' );
		$this->loader->add_filter( 'ir_filter_template_path', $module_payouts, 'add_payout_commissions_template', 10, 1 );
		$this->loader->add_action( 'admin_enqueue_scripts', $module_payouts, 'enqueue_payout_scripts' );
		$this->loader->add_action( 'wp_ajax_ir_payout_transaction', $module_payouts, 'ajax_ir_payout_transaction' );
		$this->loader->add_action( 'ir_action_paypal_logs', $module_payouts, 'add_paypal_transactions_report' );
		$this->loader->add_action( 'wp_ajax_ir-get-payout-transaction-details', $module_payouts, 'ajax_fetch_payout_transaction_details' );
		$this->loader->add_action( 'admin_init', $module_payouts, 'process_scheduled_payout_transactions' );
	}

	/**
	 * Register all of the hooks related to the instructor profile module functionality
	 * of the plugin.
	 *
	 * @since 3.5.0
	 * @access private
	 */
	private function define_profile_module_hooks() {
		$module_profile = Instructor_Role_Profile::get_instance();

		// Add new rewrite rule for instructor profile.
		$this->loader->add_action( 'init', $module_profile, 'add_profile_rewrite_rule' );
		$this->loader->add_filter( 'query_vars', $module_profile, 'add_profile_query_var' );

		// Add the instructor profile template.
		$this->loader->add_filter( 'template_include', $module_profile, 'add_instructor_profile_template', 10, 1 );

		// Enqueue necessary styles and scripts.
		$this->loader->add_action( 'wp_enqueue_scripts', $module_profile, 'enqueue_profile_assets' );

		// Enqueue profile settings styles and scripts.
		$this->loader->add_action( 'admin_enqueue_scripts', $module_profile, 'enqueue_profile_settings_assets' );

		// Add profile settings tab.
		$this->loader->add_filter( 'ir_filter_instructor_setting_tabs', $module_profile, 'add_profile_settings_tab', 10, 2 );

		// Add profile settings tab contents.
		$this->loader->add_action( 'instuctor_tab_checking', $module_profile, 'add_profile_settings_tab_contents', 10, 1 );

		// Save profile settings.
		$this->loader->add_action( 'admin_init', $module_profile, 'save_profile_settings' );

		// Add additional profile info.
		$this->loader->add_action( 'edit_user_profile', $module_profile, 'add_extra_instructor_profile_fields', 100 );
		$this->loader->add_action( 'show_user_profile', $module_profile, 'add_extra_instructor_profile_fields', 100 );

		// Save additional profile info.
		$this->loader->add_action( 'personal_options_update', $module_profile, 'save_extra_instructor_profile_fields', 100, 1 );
		$this->loader->add_action( 'edit_user_profile_update', $module_profile, 'save_extra_instructor_profile_fields', 100, 1 );

		// Add introduction sections.
		$this->loader->add_action( 'edit_user_profile', $module_profile, 'add_instructor_introduction_sections', 100 );
		$this->loader->add_action( 'show_user_profile', $module_profile, 'add_instructor_introduction_sections', 100 );

		// Save introduction sections.
		$this->loader->add_action( 'personal_options_update', $module_profile, 'save_instructor_introduction_sections', 100, 1 );
		$this->loader->add_action( 'edit_user_profile_update', $module_profile, 'save_instructor_introduction_sections', 100, 1 );

		$this->loader->add_filter( 'ld_course_list_shortcode_attr_values', $module_profile, 'filter_ld_course_list_for_instructors', 10, 2 );

		// Load the Instructor metabox in the WP Nav Menu Admin UI.

		// Override buddypress author links to redirect to instructor profile.
		$this->loader->add_action( 'bp_core_get_user_domain', $module_profile, 'update_bp_course_author_links', 10, 4 );

		// Override buddyboss theme author links to redirect to instructor profile.
		$this->loader->add_action( 'author_link', $module_profile, 'update_buddyboss_course_author_links', 10, 3 );

		// Override elumine theme author links to redirect to instructor profile.
		$this->loader->add_action( 'elumine_author_metadata', $module_profile, 'update_elumine_course_author_links', 10, 1 );

		// Save student communication settings.
		$this->loader->add_action( 'admin_init', $module_profile, 'save_student_communication_settings' );

		// Save instructor features settings.
		$this->loader->add_action( 'admin_init', $module_profile, 'save_instructor_features_settings' );

		// Navigation update and user switch fixes.
		$module_user_switch = Instructor_Role_User_Switch::get_instance();

		$this->loader->add_action( 'init', $module_user_switch, 'action_init' );
		$this->loader->add_action( 'show_user_profile', $module_user_switch, 'ir_show_profile_links' );
		$this->loader->add_action( 'edit_user_profile', $module_user_switch, 'ir_show_profile_links' );  }

	/**
	 * Register all of the hooks related to the instructor reports module functionality
	 * of the plugin
	 *
	 * @since 3.5.0
	 * @access private
	 */
	private function define_reports_module_hooks() {
		$module_reports = Instructor_Role_Reports::get_instance();

		$this->loader->add_action( 'admin_menu', $module_reports, 'add_report_menu_page', 999 );
		$this->loader->add_action( 'current_screen', $module_reports, 'send_mail_from_report_page' );

		// Ajax call for showing report.
		$this->loader->add_action( 'wp_ajax_wdm_get_report_html', $module_reports, 'ajax_fetch_course_reports' );
		$this->loader->add_action( 'wp_ajax_wdm_get_user_html', $module_reports, 'ajax_fetch_course_report_page' );
		$this->loader->add_action( 'admin_footer', $module_reports, 'display_message_section' );

		// Hook for ajax where it sends mail to individual user.
		$this->loader->add_action( 'wp_ajax_wdm_send_mail_to_individual_user', $module_reports, 'send_mail_to_individual_user' );

		// For exporting users data in CSV file.
		$this->loader->add_action( 'admin_init', $module_reports, 'export_course_report_to_csv' );

		// Instructor report Email configuration updates.
		$this->loader->add_filter( 'wp_mail_from', $module_reports, 'update_sender_email_id', 1, 1 );
		$this->loader->add_filter( 'wp_mail_from_name', $module_reports, 'update_sender_name', 999, 1 );
		$this->loader->add_action( 'phpmailer_init', $module_reports, 'configure_wp_smtp_settings', 999, 1 );
	}

	/**
	 * Register all of the hooks related to the instructor emails module functionality
	 * of the plugin
	 *
	 * @since 3.5.0
	 * @access private
	 */
	private function define_emails_module_hooks() {
		$module_emails = Instructor_Role_Emails::get_instance();

		$this->loader->add_filter( 'ir_filter_instructor_setting_tabs', $module_emails, 'ir_add_instructor_email_tab', 10, 1 );
		$this->loader->add_action( 'instuctor_tab_checking', $module_emails, 'ir_add_instructor_email_tab_content', 10, 1 );
		$this->loader->add_action( 'woocommerce_order_status_completed', $module_emails, 'ir_send_course_purchase_email_to_instructor', 10, 1 );
	}

	/**
	 * Register all of the hooks related to the instructor woocommerce module functionality
	 * of the plugin
	 *
	 * @since 3.5.0
	 * @access private
	 */
	private function define_woocommerce_module_hooks() {
		$module_woocommerce = Instructor_Role_Woocommerce::get_instance();

		$this->loader->add_filter( 'product_type_selector', $module_woocommerce, 'restrict_product_types' );
		$this->loader->add_filter( 'woocommerce_json_search_found_products', $module_woocommerce, 'filter_woocommerce_products', 10, 3 );
		$this->loader->add_filter( 'woocommerce_rest_check_permissions', $module_woocommerce, 'allow_instructors_rest_api_access', 10, 4 );
	}

	/**
	 * Register all of the hooks related to the learndash handler module functionality
	 * of the plugin
	 *
	 * @since 3.5.0
	 * @access private
	 */
	private function define_learndash_handler_module_hooks() {
		$module_learndash_handler = Instructor_Role_LearnDash_Handler::get_instance();

		$this->loader->add_filter( 'pre_get_posts', $module_learndash_handler, 'wdm_show_assignments_of_my_course', 11 );
		$this->loader->add_action( 'admin_init', $module_learndash_handler, 'wdm_restrict_assignment_edit' );
		$this->loader->add_action( 'admin_init', $module_learndash_handler, 'wdm_remove_assignment_author' );

		$this->loader->add_filter( 'wp', $module_learndash_handler, 'ir_allow_essay_permissions', 9 );
		$this->loader->add_filter( 'wp', $module_learndash_handler, 'ir_allow_assignment_permissions', 9 );
		$this->loader->add_filter( 'learndash_current_admin_tabs_on_page', $module_learndash_handler, 'wdm_remove_tabs_certi', 10, 4 );
		$this->loader->add_filter( 'learndash_current_admin_tabs_on_page', $module_learndash_handler, 'wdm_remove_tabs_course', 10, 4 );
		$this->loader->add_filter( 'learndash_select_a_course', $module_learndash_handler, 'wdm_load_my_courses' );
		$this->loader->add_filter( 'learndash_current_admin_tabs_on_page', $module_learndash_handler, 'wdm_remove_tabs_lessons', 10, 4 );
		$this->loader->add_action( 'admin_footer', $module_learndash_handler, 'wdm_prerequisite_remove_others' );
		$this->loader->add_action( 'admin_init', $module_learndash_handler, 'wdm_restrict_quiz_edit' );
		$this->loader->add_action( 'admin_init', $module_learndash_handler, 'skip_user_filtering_for_instructors' );
		$this->loader->add_filter( 'learndash_question_quiz_post_options', $module_learndash_handler, 'filter_instructor_shared_quizzes', 100, 2 );
		$this->loader->add_filter( 'ir_filter_instructor_query', $module_learndash_handler, 'allow_fetching_shared_quizzes', 10 );
		$this->loader->add_filter( 'ir_filter_instructor_query', $module_learndash_handler, 'filter_learndash_queries' );

		$this->loader->add_filter( 'learndash_listing_selectors', $module_learndash_handler, 'remove_post_listing_filters', 10, 2 );
		$this->loader->add_filter( 'learndash_listing_selector_post_type_query_args', $module_learndash_handler, 'allow_learndash_post_type_filters', 100, 3 );
		$this->loader->add_filter( 'learndash_listing_selector_value', $module_learndash_handler, 'set_listing_selector_values', 100, 2 );
		$this->loader->add_filter( 'learndash_listing_table_query_vars_filter', $module_learndash_handler, 'process_learndash_post_type_filters', 100, 3 );
		$this->loader->add_filter( 'pre_get_users', $module_learndash_handler, 'filter_quiz_statistic_users', 100, 1 );
		$this->loader->add_filter( 'learndash_fetch_quiz_statistic_history_where', $module_learndash_handler, 'filter_quiz_statistic_users_overview', 100, 2 );
		$this->loader->add_filter( 'learndash_fetch_quiz_toplist_history_where', $module_learndash_handler, 'filter_quiz_statistic_users_overview', 100, 2 );
		$this->loader->add_filter( 'learndash_fetch_quiz_statistic_overview_where', $module_learndash_handler, 'filter_quiz_statistic_users_overview', 100, 2 );
	}

	/**
	 * Register all of the hooks related to the learndash menu handler module functionality
	 * of the plugin
	 *
	 * @since 3.5.0
	 * @access private
	 */
	private function define_learndash_menu_handler_module_hooks() {
		if ( ! class_exists( 'Instructor_Role_LearnDash_Menu_Handler' ) ) {
			return;
		}

		$module_learndash_menu_handler = Instructor_Role_LearnDash_Menu_Handler::get_instance_object();

		$this->loader->add_action( 'learndash_admin_tabs_set', $module_learndash_menu_handler, 'learndash_admin_menu_early' );
		$this->loader->add_action( 'admin_init', $module_learndash_menu_handler, 'prevent_others_access' );
	}

	/**
	 * Register all of the hooks related to the commission handler module functionality
	 * of the plugin
	 *
	 * @since 3.5.0
	 * @access private
	 */
	private function define_commission_module_hooks() {
		$module_commission = Instructor_Role_Commission::get_instance();

		$this->loader->add_action( 'admin_head', $module_commission, 'wdm_instructor_table_setup' );
		$this->loader->add_action( 'wp_ajax_wdm_amount_paid_instructor', $module_commission, 'wdm_amount_paid_instructor' );
		if ( 1 === intval( ir_get_settings( 'instructor_commission' ) ) ) {
			$this->loader->add_action( 'woocommerce_order_status_completed', $module_commission, 'wdm_add_record_to_db' );
			$this->loader->add_action( 'learndash_transaction_created', $module_commission, 'track_learndash_purchase' );
		}
		$this->loader->add_filter( 'woocommerce_prevent_admin_access', $module_commission, 'wdmAllowDashboardAccess' );
		$this->loader->add_filter( 'wdmir_set_post_types', $module_commission, 'wdmAddWoocommercePostType' );
		$this->loader->add_filter( 'wdmir_add_dash_tabs', $module_commission, 'wdmAddWoocommerceMenu' );
		$this->loader->add_filter( 'user_has_cap', $module_commission, 'allowInstructorsToRelateCourses', 10, 4 );

		// Commission logs.
		$this->loader->add_action( 'ir_action_plugin_activated', $module_commission, 'create_commission_logs_table' );
		$this->loader->add_action( 'plugins_loaded', $module_commission, 'create_commission_logs_table', 100 );
		$this->loader->add_action( 'admin_enqueue_scripts', $module_commission, 'enqueue_commission_logs_scripts' );
		$this->loader->add_action( 'ir_action_manual_logs', $module_commission, 'add_commission_logs_table' );
		$this->loader->add_action( 'wp_ajax_delete_manual_commission_log', $module_commission, 'ajax_delete_manual_commission_log' );
		$this->loader->add_action( 'wp_ajax_update_manual_commission_log', $module_commission, 'ajax_update_manual_commission_log' );
		$this->loader->add_action( 'wp_ajax_export_manual_commission_log', $module_commission, 'ajax_export_manual_commission_log' );
		$this->loader->add_action( 'wp_ajax_export_order_details', $module_commission, 'ajax_export_order_details' );
		$this->loader->add_action( 'wp_ajax_bulk_update_commission_log', $module_commission, 'ajax_bulk_update_commission_log' );
		$this->loader->add_action( 'wp_ajax_disable_commission', $module_commission, 'ajax_disable_commission' );   }

	/**
	 * Register all of the hooks related to the review module functionality
	 * of the plugin
	 *
	 * @since 3.5.0
	 * @access private
	 */
	private function define_review_module_hooks() {
		$module_review = Instructor_Role_Review::get_instance();
		$wp_version    = get_bloginfo( 'version' );

		$this->loader->add_action( 'save_post', $module_review, 'approve_instructor_product_updates', 999, 2 );
		$this->loader->add_action( 'publish_product', $module_review, 'wdmir_product_published_notification', 10, 2 );
		$this->loader->add_filter( 'learndash_content', $module_review, 'wdmir_approval_course_content', 100, 2 );
		$this->loader->add_action( 'save_post', $module_review, 'approve_instructor_course_updates', 11, 2 );
		$this->loader->add_action( 'admin_init', $module_review, 'wdmir_approval_meta_box' );
		$this->loader->add_action( 'post_submitbox_misc_actions', $module_review, 'wdmir_approve_field_publish' );
		if ( '5.0.0' < $wp_version ) {
			$this->loader->add_action( 'admin_enqueue_scripts', $module_review, 'wdmir_approve_field_publish_test' );
		}
		$this->loader->add_action( 'wp_ajax_approve_instructor_update_ajax', $module_review, 'approveInstructorUpdateAjaxHandler' );
		$this->loader->add_action( 'save_post', $module_review, 'wdmir_ld_approve_content', 11, 2 );
		$this->loader->add_action( 'save_post', $module_review, 'wdmir_on_course_approval_update', 11, 2 );
		$this->loader->add_filter( 'manage_sfwd-courses_posts_columns', $module_review, 'wdmir_pending_column_head', 10 );
		$this->loader->add_action( 'manage_sfwd-courses_posts_custom_column', $module_review, 'wdmir_pending_column_content', 10, 2 );
		$this->loader->add_action( 'enqueue_block_editor_assets', $module_review, 'enqueue_block_editor_review_messages' );
		$this->loader->add_action( 'admin_notices', $module_review, 'display_review_notifications' );

		$this->loader->add_filter( 'ir_filter_is_post_updated_for_review', $module_review, 'manage_dashboard_product_reviews', 10, 3 );
	}

	/**
	 * Register all of the hooks related to the settings module functionality
	 * of the plugin
	 *
	 * @since 3.5.0
	 * @access private
	 */
	private function define_settings_module_hooks() {
		$module_settings = Instructor_Role_Settings::get_instance();

		$this->loader->add_filter( 'admin_menu', $module_settings, 'instuctor_menu', 2000 );
		$this->loader->add_action( 'init', $module_settings, 'initialize_instructor_admin_menu', 99999 );
		$this->loader->add_action( 'admin_init', $module_settings, 'save_overview_settings', 2000 );
		$this->loader->add_action( 'admin_init', $module_settings, 'wdmir_email_settings_save' );
		$this->loader->add_action( 'admin_init', $module_settings, 'wdm_export_csv_date_filter' );
		$this->loader->add_action( 'admin_init', $module_settings, 'save_instructor_mail_template_data' );
		$this->loader->add_action( 'learndash_quiz_completed', $module_settings, 'send_email_to_instructor', 10, 2 );
		$this->loader->add_action( 'wp_ajax_wdm_update_commission', $module_settings, 'wdm_update_commission' );
		$this->loader->add_action( 'admin_init', $module_settings, 'wdm_export_commission_report' );
		$this->loader->add_action( 'admin_init', $module_settings, 'wdmir_settings_save' );
		$this->loader->add_action( 'admin_head', $module_settings, 'hide_category_links' );
		$this->loader->add_action( 'admin_init', $module_settings, 'toggle_module_activation', 11 );
		$this->loader->add_action( 'init', $module_settings, 'toggle_instructor_backend_access' );
		$this->loader->add_action( 'wp_ajax_update_admin_settings', $module_settings, 'ajax_update_admin_settings' );
		$this->loader->add_action( 'wp_ajax_dismiss_onboarding_steps', $module_settings, 'ajax_dismiss_onboarding_steps' );
		$this->loader->add_action( 'wp_ajax_add_instructor', $module_settings, 'ajax_add_instructor' );
		$this->loader->add_action( 'wp_head', $module_settings, 'add_dashboard_onboarding_step' );
		$this->loader->add_action( 'wp_ajax_edit_page_onboarding', $module_settings, 'edit_page_onboarding' );
	}

	/**
	 * Register all of the hooks related to the student communication module functionality
	 * of the plugin
	 *
	 * @since 3.6.0
	 * @access private
	 */
	private function define_student_communication_module_hooks() {
		$module_student_communication = Instructor_Role_Student_Communication::get_instance();

		// Add message modal on focus mode screen.
		$this->loader->add_action( 'learndash-focus-content-end', $module_student_communication, 'add_message_modal_window', 10, 2 );

		$this->loader->add_action( 'learndash-lesson-after', $module_student_communication, 'buddyboss_add_message_modal_window', 10, 3 );
		$this->loader->add_action( 'learndash-topic-after', $module_student_communication, 'buddyboss_add_message_modal_window', 10, 3 );

		// Add message top menu for elumine.
		$this->loader->add_action( 'learndash-focus-header-usermenu-after', $module_student_communication, 'add_message_menu', 10, 2 );

		// Add styles and scripts.
		$this->loader->add_action( 'wp_enqueue_scripts', $module_student_communication, 'enqueue_message_scripts' );

		// Send message to instructor.
		$this->loader->add_action( 'wp_ajax_ir_send_doubt_to_instructor', $module_student_communication, 'ajax_ir_send_doubt_to_instructor' );

		// Get lesson topics.
		$this->loader->add_action( 'wp_ajax_ir_get_lesson_topics', $module_student_communication, 'ajax_ir_get_lesson_topics' );

		// Get all messages for a thread.
		$this->loader->add_action( 'wp_ajax_ir_get_doubt_messages', $module_student_communication, 'ajax_ir_get_doubt_messages' );

		// Add send button to tinymce for message editor.
		$this->loader->add_filter( 'mce_external_plugins', $module_student_communication, 'add_message_editor_plugin', 10, 2 );

		$this->loader->add_filter( 'mce_buttons', $module_student_communication, 'add_send_button_to_editor', 10, 2 );

		// Add placeholder to the tinymce for message editor.
		$this->loader->add_filter( 'the_editor', $module_student_communication, 'set_message_editor_placeholder' );

		// Add student communication settings tab contents.
		$this->loader->add_action( 'instuctor_tab_checking', $module_student_communication, 'add_student_communication_settings_tab_contents', 10, 1 );

		// Enqueue student communication styles and scripts.
		$this->loader->add_action( 'admin_enqueue_scripts', $module_student_communication, 'enqueue_student_communication_settings_assets' );

		// Display course, lesson and topic details for buddypress messages ( except buddyboss theme ).
		$this->loader->add_filter( 'bp_get_message_thread_subject', $module_student_communication, 'default_display_bp_messages_ld_post_data', 10 );
		$this->loader->add_filter( 'bp_get_the_thread_subject', $module_student_communication, 'default_display_bp_messages_ld_post_data', 10 );

		// Display course, lesson and topic details for buddypress messages ( specifically for buddyboss theme ).
		$this->loader->add_filter( 'bp_nouveau_get_message_date', $module_student_communication, 'buddyboss_display_bp_messages_ld_post_data', 10, 4 );
	}

	/**
	 * Register all of the hooks related to the dashboard module functionality
	 * of the plugin
	 *
	 * @since 4.0
	 * @access private
	 */
	private function define_dashboard_module_hooks() {
		$module_dashboard = Instructor_Role_Dashboard::get_instance();

		$this->loader->add_action( 'admin_enqueue_scripts', $module_dashboard, 'enqueue_dashboard_scripts' );
		$this->loader->add_action( 'admin_footer', $module_dashboard, 'add_save_and_continue_section' );
		$this->loader->add_action( 'admin_enqueue_scripts', $module_dashboard, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_head', $module_dashboard, 'add_instructor_logo' );
		$this->loader->add_action( 'admin_enqueue_scripts', $module_dashboard, 'add_accent_color_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $module_dashboard, 'add_font_settings' );
		$this->loader->add_action( 'in_admin_header', $module_dashboard, 'add_instructor_menu', 0 );
		$this->loader->add_filter( 'wdmir_exclude_post_types', $module_dashboard, 'exclude_menu_items' );
		$this->loader->add_action( 'admin_head', $module_dashboard, 'remove_admin_bar', 11 );
		$this->loader->add_filter( 'admin_footer_text', $module_dashboard, 'dashboard_footer_text', 10, 1 );
		$this->loader->add_filter( 'update_footer', $module_dashboard, 'dashboard_footer_text', 11, 1 );
		$this->loader->add_filter( 'wp_nav_menu_items', $module_dashboard, 'render_persistant_menu_items', 11, 1 );

		// Add appearance settings tab contents.
		$this->loader->add_action( 'ir_action_appearance_settings_content', $module_dashboard, 'add_appearance_settings_tab_contents', 10, 1 );

		// Save appearance settings.
		$this->loader->add_action( 'admin_init', $module_dashboard, 'save_appearance_settings' );

		// Hide additional LD sections on specific pages.
		$this->loader->add_action( 'admin_enqueue_scripts', $module_dashboard, 'hide_sections_on_dashboard' );

		// Update LMS Label.
		$this->loader->add_action( 'admin_menu', $module_dashboard, 'update_instructor_lms_label' );

		// Add mobile menu icons to menu.
		$this->loader->add_filter( 'wp_nav_menu', $module_dashboard, 'add_mobile_icons_to_dashboard', 10, 2 );

		// Add layout class to body tag.
		$this->loader->add_filter( 'admin_body_class', $module_dashboard, 'add_layout_classes_to_body', 10, 1 );

		// Set new layouts for new customers.
		$this->loader->add_action( 'ir_action_new_customer_4_4_0', $module_dashboard, 'set_new_layouts' );

		// Add frontend dashboard settings tab.
		$this->loader->add_filter( 'ir_filter_instructor_setting_tabs', $module_dashboard, 'add_frontend_dashboard_settings_tab', 10, 2 );

		// Add frontend dashboard settings tab contents.
		$this->loader->add_action( 'instuctor_tab_checking', $module_dashboard, 'add_frontend_dashboard_settings_tab_contents', 10, 1 );

		// Save frontend dashboard settings.
		$this->loader->add_action( 'admin_init', $module_dashboard, 'save_frontend_dashboard_settings' );

		// Implement optimization by changing the content loaded on the dashboard.
		$this->loader->add_filter( 'render_block', $module_dashboard, 'load_dashboard_block_content', 10, 2 );

		// Enqueue admin setting script for frontend course creator.
		$this->loader->add_action( 'admin_enqueue_scripts', $module_dashboard, 'enqueue_settings_scripts' );

		// Sync FCC appearance setting on frontend dashboard page.
		$this->loader->add_action( 'save_post', $module_dashboard, 'check_dashboard_sync', 10, 3 );

		// Save general course creation settings.
		$this->loader->add_action( 'admin_init', $module_dashboard, 'save_general_course_creation_settings' );
	}

	/**
	 * Register all of the hooks related to the frontend dashboard module functionality
	 * of the plugin
	 *
	 * @since 4.4.0
	 * @access private
	 */
	private function define_frontend_dashboard_module_hooks() {
		$module_frontend_dashboard = Instructor_Role_Frontend_Dashboard::get_instance();

		// Enqueue necessary styles and scripts.
		$this->loader->add_action( 'wp_enqueue_scripts', $module_frontend_dashboard, 'enqueue_scripts', 1000 );
		$this->loader->add_action( 'admin_enqueue_scripts', $module_frontend_dashboard, 'enqueue_admin_scripts' );

		// Initialize blocks.
		$this->loader->add_action( 'init', $module_frontend_dashboard, 'register_blocks' );

		// Add new rewrite rule for instructor dashboard.
		$this->loader->add_filter( 'init', $module_frontend_dashboard, 'add_dashboard_rewrite_tag' );
		$this->loader->add_action( 'init', $module_frontend_dashboard, 'add_dashboard_rewrite_rule' );

		// Add new rewrite rule for instructor quiz dashboard.
		$this->loader->add_filter( 'init', $module_frontend_dashboard, 'add_quiz_dashboard_rewrite_tag' );
		$this->loader->add_action( 'init', $module_frontend_dashboard, 'add_quiz_dashboard_rewrite_rule' );

		// Prevent WordPress from treating course_builder and quiz_builder queries as the home page.
		$this->loader->add_action( 'parse_query', $module_frontend_dashboard, 'prevent_detection_as_home_page' );

		// Add the instructor dashboard template. Late priority to override plugins' template filters.
		$this->loader->add_filter( 'template_include', $module_frontend_dashboard, 'add_course_builder_template', 100, 1 );
		// Add the instructor quiz dashboard template.
		$this->loader->add_filter( 'template_include', $module_frontend_dashboard, 'add_quiz_builder_template', 10, 1 );
		$this->loader->add_filter( 'query_vars', $module_frontend_dashboard, 'add_course_builder_query_vars' );
		$this->loader->add_filter( 'query_vars', $module_frontend_dashboard, 'add_quiz_builder_query_vars' );

		// Add row actions.
		$this->loader->add_filter( 'post_row_actions', $module_frontend_dashboard, 'add_course_builder_option', 100, 2 );
		$this->loader->add_filter( 'get_edit_post_link', $module_frontend_dashboard, 'update_course_title_link', 10, 3 );

		// Add row actions.
		$this->loader->add_filter( 'post_row_actions', $module_frontend_dashboard, 'add_quiz_builder_option', 10, 2 );

		// Add create new button.
		$this->loader->add_action( 'learndash_header_buttons', $module_frontend_dashboard, 'add_header_buttons' );
		$this->loader->add_action( 'wp_ajax_ir_fcb_new_course', $module_frontend_dashboard, 'ajax_create_new_course' );
		$this->loader->add_action( 'wp_ajax_ir_fqb_new_quiz', $module_frontend_dashboard, 'ajax_create_new_quiz' );

		// Hide lesson and topic sections if FCC links disabled.
		$this->loader->add_action( 'admin_enqueue_scripts', $module_frontend_dashboard, 'hide_ld_sections_for_fcc' );
		$this->loader->add_filter( 'admin_footer', $module_frontend_dashboard, 'edit_with_fcc_fqb_button' );

		// Load quiz templates.
		$this->loader->add_action( 'wp_ajax_ir_fqb_load_quiz_template', $module_frontend_dashboard, 'ajax_load_quiz_template' );

		// Remove private and protected prefixes.
		$this->loader->add_filter( 'private_title_format', $module_frontend_dashboard, 'remove_private_protected_from_titles', 10, 2 );

		// Ajax action for Back to WordPress Editor Button.
		$this->loader->add_filter( 'wp_ajax_back_to_wordpress_editor', $module_frontend_dashboard, 'ajax_back_to_wordpress_editor' );

		// Action for checking if course is edited by elementor.
		$this->loader->add_filter( 'rest_prepare_sfwd-courses', $module_frontend_dashboard, 'is_elementor_editor_enabled', 10, 3 );

		// Action for checking if lesson is edited by elementor.
		$this->loader->add_filter( 'rest_prepare_sfwd-lessons', $module_frontend_dashboard, 'is_elementor_editor_enabled', 10, 3 );

		// Action for checking if topic is edited by elementor.
		$this->loader->add_filter( 'rest_prepare_sfwd-topic', $module_frontend_dashboard, 'is_elementor_editor_enabled', 10, 3 );

		// Action for checking if quiz is edited by elementor.
		$this->loader->add_filter( 'rest_prepare_sfwd-quiz', $module_frontend_dashboard, 'is_elementor_editor_enabled', 10, 3 );

		// Change title for FCC and FQB.
		$this->loader->add_filter( 'pre_get_document_title', $module_frontend_dashboard, 'override_fcc_title', 20 );    }

	/**
	 * Register all of the hooks related to the dashboard block module functionality
	 * of the plugin
	 *
	 * @since 5.0.0
	 * @access private
	 */
	private function define_dashboard_block_module_hooks() {
		$module_dashboard_block = Instructor_Role_Dashboard_Block::get_instance();

		$this->loader->add_filter( 'theme_page_templates', $module_dashboard_block, 'add_frontend_dashboard_template' );
		$this->loader->add_filter( 'wp_insert_post_data', $module_dashboard_block, 'register_frontend_dashboard_templates' );
		$this->loader->add_filter( 'template_include', $module_dashboard_block, 'view_frontend_dashboard_template' );

		// Initialize blocks.
		$this->loader->add_action( 'init', $module_dashboard_block, 'register_blocks' );
		$this->loader->add_action( 'enqueue_block_assets', $module_dashboard_block, 'enqueue_scripts' );

		// Handle post status on post list page.
		$this->loader->add_filter( 'display_post_states', $module_dashboard_block, 'add_dashboard_display_state', 10, 2 );

		$this->loader->add_action( 'admin_init', $module_dashboard_block, 'save_dashboard_block_settings' );
		$this->loader->add_action( 'admin_enqueue_scripts', $module_dashboard_block, 'enqueue_settings_scripts' );

		// Handle onboarding.
		$this->loader->add_action( 'wp_ajax_create_frontend_dashboard', $module_dashboard_block, 'setup_frontend_dashboard' );
		$this->loader->add_filter( 'the_content', $module_dashboard_block, 'handle_dashboard_launch_modal' );
		$this->loader->add_action( 'wp_ajax_complete_dashboard_launch', $module_dashboard_block, 'complete_dashboard_launch' );

		// Handle logged out redirects.
		$this->loader->add_action( 'template_redirect', $module_dashboard_block, 'handle_guest_dashboard_template' );

		// Add Dashboard Preset Pattern.
		$this->loader->add_action( 'init', $module_dashboard_block, 'register_default_dashboard_pattern' );

		// Ajax to create new Dashboard Page.
		$this->loader->add_action( 'wp_ajax_ir_create_new_dashboard_page', $module_dashboard_block, 'create_new_frontend_dashboard_page' );

		// Handle dashboard page deletion.
		$this->loader->add_action( 'wp_trash_post', $module_dashboard_block, 'reset_frontend_dashboard_page' );

		// Save frontend dashboard settings.
		$this->loader->add_action( 'admin_init', $module_dashboard_block, 'save_frontend_settings' );

		// Force enqueue block assets for instructor-role blocks (WP 6.9+ optimization fix).
		$this->loader->add_filter( 'enqueue_empty_block_content_assets', $module_dashboard_block, 'force_enqueue_block_assets', 10, 2 );
	}

	/**
	 * Define plugin shortcodes.
	 *
	 * @since 3.5.7
	 */
	private function define_shortcodes() {
		$shortcode_handler = Instructor_Role_Shortcode_Handler::get_instance();
		$this->loader->add_action( 'init', $shortcode_handler, 'initialize_shortcodes' );
		$this->loader->add_action( 'wp_enqueue_scripts', $shortcode_handler, 'enqueue_scripts' );
	}

	/**
	 * Define custom API Endpoints.
	 *
	 * @since 4.4.0
	 */
	private function define_api_endpoints() {
		$this->manage_api_endpoints();
		$this->manage_dashboard_block_api_endpoints();
		$this->manage_woocommerce_api_endpoints();
		$this->manage_essays_api_endpoints();
		$this->manage_submissions_api_endpoints();
		$this->manage_comments_api_endpoints();
		$this->manage_course_reports_api_endpoints();
		$this->manage_quiz_attempts_api_endpoints();
		$this->manage_groups_api_endpoints();
		$this->manage_quiz_export_api_endpoints();
		$this->manage_certificates_api_endpoints();
	}

	/**
	 * Manage API endpoints for instructor role.
	 *
	 * @since 5.8.0
	 */
	private function manage_api_endpoints() {
		$api_handler = Instructor_Role_Api_Handler::get_instance();

		$this->loader->add_action( 'rest_api_init', $api_handler, 'register_custom_endpoints' );
		$this->loader->add_action( 'rest_user_query', $api_handler, 'update_api_user_query', 10, 2 );
		$this->loader->add_filter( 'rest_endpoints', $api_handler, 'remove_user_endpoint_limits', 10, 1 );
		$this->loader->add_filter( 'rest_post_dispatch', $api_handler, 'update_remaining_quiz_details', 10, 3 );
		$this->loader->add_filter( 'rest_post_dispatch', $api_handler, 'update_remaining_lesson_details', 100, 3 );
		$this->loader->add_filter( 'rest_post_dispatch', $api_handler, 'update_remaining_topic_details', 100, 3 );
		$this->loader->add_filter( 'rest_post_dispatch', $api_handler, 'handle_instructor_course_review', 100, 3 );
		$this->loader->add_filter( 'rest_prepare_sfwd-courses', $api_handler, 'handle_post_passwords_in_request', 100, 3 );
		$this->loader->add_filter( 'rest_prepare_sfwd-lessons', $api_handler, 'handle_post_passwords_in_request', 100, 3 );
		$this->loader->add_filter( 'rest_prepare_sfwd-topic', $api_handler, 'handle_post_passwords_in_request', 100, 3 );
		$this->loader->add_filter( 'rest_prepare_sfwd-quiz', $api_handler, 'handle_post_passwords_in_request', 100, 3 );
		$this->loader->add_filter( 'rest_prepare_groups', $api_handler, 'handle_post_passwords_in_request', 100, 3 );
	}

	/**
	 * Manage API endpoints for groups.
	 *
	 * @since 5.8.0
	 */
	private function manage_groups_api_endpoints() {
		$groups_api_handler = Instructor_Role_Groups_Api_Handler::get_instance();

		$this->loader->add_action( 'rest_api_init', $groups_api_handler, 'register_custom_endpoints' );
		$this->loader->add_filter( 'ir_filter_api_post_list_single', $groups_api_handler, 'add_additional_group_info', 10, 4 );

		// Fetch missing group details.
		$this->loader->add_filter( 'rest_prepare_groups', $groups_api_handler, 'fetch_missing_group_details', 10, 3 );
		$this->loader->add_filter( 'rest_post_dispatch', $groups_api_handler, 'update_missing_group_details', 10, 3 );

		$this->loader->add_action( 'wp_ajax_send_group_users_email_notification', $groups_api_handler, 'ajax_send_group_users_email_notification' );
		$this->loader->add_action( 'wp_ajax_send_group_single_user_email_notification', $groups_api_handler, 'ajax_send_group_single_user_email_notification' );
		$this->loader->add_filter( 'ir_filter_dashboard_localized_data', $groups_api_handler, 'add_groups_tab_localized_data' );
		$this->loader->add_filter( 'rest_prepare_user', $groups_api_handler, 'add_user_email_to_response', 10, 3 );
		$this->loader->add_action( 'wp_ajax_enable_group_start_and_end_date', $groups_api_handler, 'ajax_enable_group_start_and_end_date' );
		$this->loader->add_action( 'wp_ajax_is_course_in_other_group', $groups_api_handler, 'ajax_is_course_in_other_group' );
	}

	/**
	 * Manage API endpoints for certificates.
	 *
	 * @since 5.8.0
	 */
	private function manage_certificates_api_endpoints() {
		$certificates_api_handler = Instructor_Role_Certificates_Api_Handler::get_instance();
		$this->loader->add_action( 'rest_api_init', $certificates_api_handler, 'register_custom_endpoints' );
		$this->loader->add_filter( 'ir_filter_api_post_list_single', $certificates_api_handler, 'add_additional_certificate_details', 10, 4 );
		$this->loader->add_action( 'plugins_loaded', $certificates_api_handler, 'initialize_editor', 100 );
	}

	/**
	 * Manage API endpoints for course reports.
	 *
	 * @since 5.8.0
	 */
	private function manage_course_reports_api_endpoints() {
		$dashboard_block_course_reports_api_handler = Instructor_Role_Course_Reports_Api_Handler::get_instance();

		$this->loader->add_action( 'rest_api_init', $dashboard_block_course_reports_api_handler, 'register_custom_endpoints' );
		$this->loader->add_action( 'wp_ajax_send_course_users_email_notification', $dashboard_block_course_reports_api_handler, 'ajax_send_course_users_email_notification' );
		$this->loader->add_action( 'wp_ajax_send_selected_course_users_email_notification', $dashboard_block_course_reports_api_handler, 'ajax_send_selected_course_users_email_notification' );
		$this->loader->add_action( 'wp_ajax_send_filtered_course_users_email_notification', $dashboard_block_course_reports_api_handler, 'ajax_send_filtered_course_users_email_notification' );
		$this->loader->add_action( 'wp_ajax_send_learner_email_notification', $dashboard_block_course_reports_api_handler, 'ajax_send_learner_email_notification' );
		$this->loader->add_action( 'wp_ajax_export_course_reports', $dashboard_block_course_reports_api_handler, 'ajax_export_course_report' );
		$this->loader->add_action( 'wp_ajax_export_learner_reports', $dashboard_block_course_reports_api_handler, 'ajax_export_learner_report' );
	}

	/**
	 * Manage API endpoints for comments.
	 *
	 * @since 5.8.0
	 */
	private function manage_comments_api_endpoints() {
		$dashboard_block_comments_api_handler = Instructor_Role_Comments_Api_Handler::get_instance();

		$this->loader->add_action( 'rest_api_init', $dashboard_block_comments_api_handler, 'register_custom_endpoints' );
		$this->loader->add_filter( 'rest_preprocess_comment', $dashboard_block_comments_api_handler, 'check_instructor_comment_access', 10, 2 );
	}

	/**
	 * Manage API endpoints for quiz exports.
	 *
	 * @since 5.8.0
	 */
	private function manage_quiz_export_api_endpoints() {
		$quiz_export_handler = Instructor_Role_Quiz_Statistics_Export::instance();

		$this->loader->add_action( 'wp_ajax_export_quiz_statistics', $quiz_export_handler, 'export_quiz_statistics' );
	}

	/**
	 * Manage API endpoints for quiz attempts.
	 *
	 * @since 5.8.0
	 */
	private function manage_quiz_attempts_api_endpoints() {
		$dashboard_block_quiz_attempts_api_handler = Instructor_Role_Quiz_Attempts_Api_Handler::get_instance();

		$this->loader->add_action( 'rest_api_init', $dashboard_block_quiz_attempts_api_handler, 'register_custom_endpoints' );
	}

	/**
	 * Manage API endpoints for submissions.
	 *
	 * @since 5.8.0
	 */
	private function manage_submissions_api_endpoints() {
		$dashboard_block_submissions_api_handler = Instructor_Role_Submissions_Api_Handler::get_instance();

		$this->loader->add_action( 'rest_api_init', $dashboard_block_submissions_api_handler, 'register_custom_endpoints' );
	}

	/**
	 * Manage API endpoints for essays.
	 *
	 * @since 5.8.0
	 */
	private function manage_essays_api_endpoints() {
		$dashboard_block_essays_api_handler = Instructor_Role_Essays_Api_Handler::get_instance();

		$this->loader->add_action( 'rest_api_init', $dashboard_block_essays_api_handler, 'register_custom_endpoints' );
	}

	/**
	 * Manage API endpoints for woocommerce.
	 *
	 * @since 5.8.0
	 */
	private function manage_woocommerce_api_endpoints() {
		$dashboard_block_woo_api_handler = Instructor_Role_Woocommerce_Api_Handler::get_instance();

		$this->loader->add_action( 'rest_api_init', $dashboard_block_woo_api_handler, 'register_custom_endpoints' );
		$this->loader->add_filter( 'ir_filter_api_post_list_single', $dashboard_block_woo_api_handler, 'add_additional_product_details', 10, 4 );
		$this->loader->add_filter( 'woocommerce_rest_prepare_product_object', $dashboard_block_woo_api_handler, 'add_featured_image_check', 10, 3 );
		$this->loader->add_filter( 'woocommerce_rest_prepare_product_object', $dashboard_block_woo_api_handler, 'add_featured_image_check', 10, 3 );
		$this->loader->add_filter( 'woocommerce_rest_pre_insert_product_object', $dashboard_block_woo_api_handler, 'handle_empty_featured_image_save', 10, 3 );
		$this->loader->add_filter( 'woocommerce_rest_pre_insert_product_object', $dashboard_block_woo_api_handler, 'handle_product_review_save', 10, 3 );
		$this->loader->add_action( 'woocommerce_rest_insert_product_object', $dashboard_block_woo_api_handler, 'handle_new_product_review_save', 10, 3 );
	}

	/**
	 * Manage API endpoints for dashboard block.
	 *
	 * @since 5.8.0
	 */
	private function manage_dashboard_block_api_endpoints() {
		$dashboard_block_api_handler = Instructor_Role_Dashboard_Block_Api_Handler::get_instance();

		$this->loader->add_action( 'rest_api_init', $dashboard_block_api_handler, 'register_custom_endpoints' );
		$this->loader->add_filter( 'learndash-cpt-options', $dashboard_block_api_handler, 'allow_rest_api_access', 10, 2 );

		// Override LD API to allow access to instructors.
		$this->loader->add_filter( 'rest_request_after_callbacks', $dashboard_block_api_handler, 'allow_instructor_ld_api_access', 10, 3 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 3.5.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since 1.0.0
	 * @return string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since 1.0.0
	 * @return Instructor_Role_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since 1.0.0
	 * @return string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
