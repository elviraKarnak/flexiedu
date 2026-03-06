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
 * @since 4.0
 * @package Ld_Group_Registration
 * @subpackage Ld_Group_Registration/includes
 */

namespace LdGroupRegistration\Includes;

defined( 'ABSPATH' ) || exit;

use LdGroupRegistration\Includes\Ld_Group_Registration_Loader;
use LdGroupRegistration\Includes\Ld_Group_Registration_I18n;
use LdGroupRegistration\Includes\Ld_Group_Registration_Activator;
use LdGroupRegistration\WisdmSetup\Ld_Group_Registration_Setup_Wizard;

use LdGroupRegistration\Modules\Classes\Ld_Group_Registration_Groups;
use LdGroupRegistration\Modules\Classes\Ld_Group_Registration_Settings;
use LdGroupRegistration\Modules\Classes\Ld_Group_Registration_Subscriptions;
use LdGroupRegistration\Modules\Classes\Ld_Group_Registration_Users;
use LdGroupRegistration\Modules\Classes\Ld_Group_Registration_Woocommerce;
use LdGroupRegistration\Modules\Classes\Ld_Group_Registration_Reports;
use LdGroupRegistration\Modules\Classes\Ld_Group_Registration_Group_Code;
use LdGroupRegistration\Modules\Classes\Ld_Group_Registration_Group_Code_Registration;
use LdGroupRegistration\Modules\Classes\Ld_Group_Registration_Unlimited_Members;
use LdGroupRegistration\Modules\Classes\Ld_Group_Registration_Sub_Groups;
use LdGroupRegistration\Modules\Classes\Ld_Group_Registration_Dynamic_Group;
use LdGroupRegistration\Modules\Classes\Ld_Group_Registration_Dynamic_Fields;


/**
 * LD Group Registration class
 */
class Ld_Group_Registration {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since 4.0
	 * @access protected
	 * @var Ld_Group_Registration_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 4.0
	 * @access protected
	 * @var string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since 4.0
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
	 * @since 4.0
	 */
	public function __construct() {
		if ( defined( 'LD_GROUP_REGISTRATION_VERSION' ) ) {
			$this->version = LD_GROUP_REGISTRATION_VERSION;
		} else {
			$this->version = '4.3.8';
		}
		$this->plugin_name = 'ld-group-registration';

		$this->load_dependencies();
		$this->handle_activation();
		$this->set_locale();
		$this->define_module_hooks();
		$this->initialize_setup_wizard();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ld_Group_Registration_Loader. Orchestrates the hooks of the plugin.
	 * - Ld_Group_Registration_I18n. Defines internationalization functionality.
	 * - Ld_Group_Registration_Admin. Defines all hooks for the admin area.
	 * - Ld_Group_Registration_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since 4.0
	 * @access private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for handling activation functionalities of the
		 * core plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-ld-group-registration-activator.php';

		/**
		 * The class responsible for handling deactivation functionalities of the
		 * plugin.
		 */
		// require_once plugin_dir_path( __DIR__ ) . 'includes/class-ld-group-registration-deactivator.php';.

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-ld-group-registration-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-ld-group-registration-i18n.php';

		/**
		 * The file responsible for defining common functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/ld-group-registration-functions.php';

		/**
		 * The file responsible for defining common static variables
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/ld-group-registration-constants.php';

		/**
		 * The file responsible for handling deprecated functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/ld-group-registration-deprecated.php';

		// Load Modules.

		/**
		 * The class responsible for defining all actions to control group related functionalities
		 */
		require_once plugin_dir_path( __DIR__ ) . 'modules/classes/class-ld-group-registration-groups.php';

		/**
		 * The class responsible for defining all actions to control sub group related functionalities
		 */
		require_once plugin_dir_path( __DIR__ ) . 'modules/classes/class-ld-group-registration-sub-groups.php';

		/**
		 * The class responsible for defining all actions to control settings related functionalities
		 */
		require_once plugin_dir_path( __DIR__ ) . 'modules/classes/class-ld-group-registration-settings.php';

		/**
		 * The class responsible for defining all actions to control user related functionalities
		 */
		require_once plugin_dir_path( __DIR__ ) . 'modules/classes/class-ld-group-registration-users.php';

		/**
		 * The class responsible for defining all actions to control woocommerce related functionalities
		 */
		require_once plugin_dir_path( __DIR__ ) . 'modules/classes/class-ld-group-registration-woocommerce.php';

		/**
		 * The class responsible for defining all actions to control woocommerce subscriptions related functionalities
		 */
		require_once plugin_dir_path( __DIR__ ) . 'modules/classes/class-ld-group-registration-subscriptions.php';

		/**
		 * The class responsible for defining all actions to control reports related functionalities
		 */
		require_once plugin_dir_path( __DIR__ ) . 'modules/classes/class-ld-group-registration-reports.php';

		/**
		 * The class responsible for defining all actions to control group code related functionalities
		 */
		require_once plugin_dir_path( __DIR__ ) . 'modules/classes/class-ld-group-registration-group-code.php';

		/**
		 * The class responsible for defining all actions to control group code registration related functionalities
		 */
		require_once plugin_dir_path( __DIR__ ) . 'modules/classes/class-ld-group-registration-group-code-registration.php';

		/**
		 * The class responsible for defining all actions to control group code registration related functionalities
		 */
		require_once plugin_dir_path( __DIR__ ) . 'modules/classes/class-ld-group-registration-unlimited-members.php';

		/**
		 * The class responsible for defining all actions to control dynamic group related functionalities
		 */
		require_once plugin_dir_path( __DIR__ ) . 'modules/classes/class-ld-group-registration-dynamic-group.php';

		/**
		 * The class responsible for defining all actions to control dynamic fields related functionalities
		 */
		require_once plugin_dir_path( __DIR__ ) . 'modules/classes/class-ld-group-registration-dynamic-fields.php';

		/**
		 * The class responsible for defining all actions to control the setup wizard related functionalities.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'wisdm-setup/class-wisdm-setup-wizard.php';

		require_once plugin_dir_path( __DIR__ ) . 'wisdm-setup/class-ld-group-registration-setup-wizard.php';

		$this->loader = new Ld_Group_Registration_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ld_Group_Registration_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since 4.0
	 * @access private
	 */
	private function set_locale() {
		$plugin_i18n = new Ld_Group_Registration_I18n();

		$this->loader->add_action(
			'plugins_loaded',
			$plugin_i18n,
			'load_plugin_textdomain',
			55 // Priority should be higher than the priority of the `plugins_loaded` action in the main plugin file.
		);
	}

	/**
	 * Handle plugin activation
	 */
	private function handle_activation() {
		$plugin_activator = new Ld_Group_Registration_Activator();

		$this->loader->add_action( 'init', $plugin_activator, 'activate' );
		$this->loader->add_action( 'init', $plugin_activator, 'migrate_old_data' );
		$this->loader->add_action( 'admin_init', $plugin_activator, 'admin_activate' );
		$this->loader->add_action( 'in_plugin_update_message-ld-group-registration/ld-group-registration.php', $plugin_activator, 'handle_update_notices', 10, 2 );
	}

	/**
	 * Register all of the module hooks
	 *
	 * @since 4.0
	 * @access private
	 */
	private function define_module_hooks() {
		$modules = [
			'groups',
			'settings',
			'subscriptions',
			'users',
			'woocommerce',
			'reports',
			'group_code',
			'group_code_registration',
			'unlimited_members',
			'sub_groups',
			'dynamic_groups',
			'dynamic_registration_fields',
		];

		foreach ( $modules as $module ) {
			call_user_func( [ $this, 'define_' . $module . '_module_hooks' ] );
		}
	}

	/**
	 * Initialize setup wizard.
	 *
	 * @since 4.2.0
	 * @access private
	 */
	private function initialize_setup_wizard() {
		$setup_wizard = new Ld_Group_Registration_Setup_Wizard();
	}

	/**
	 * Register all of the hooks related to the groups module functionality
	 * of the plugin.
	 *
	 * @since 4.0
	 * @access private
	 */
	private function define_groups_module_hooks() {
		$plugin_groups = Ld_Group_Registration_Groups::get_instance();

		$this->loader->add_filter( 'manage_edit-groups_columns', $plugin_groups, 'add_column_heading', 20, 1 );
		$this->loader->add_action( 'manage_groups_posts_custom_column', $plugin_groups, 'add_column_data', 20, 2 );

		$this->loader->add_action( 'wp', $plugin_groups, 'handle_group_enrollment_form' );
		$this->loader->add_action( 'before_delete_post', $plugin_groups, 'handle_group_deletion' );
		$this->loader->add_action( 'wp_ajax_wdm_group_unenrollment', $plugin_groups, 'handle_group_unenrollment' );
		$this->loader->add_action( 'wp_ajax_bulk_unenrollment', $plugin_groups, 'handle_bulk_remove_group_users' );
		$this->loader->add_action( 'wp_ajax_new_user_validation', $plugin_groups, 'enroll_form_validation_for_sub_groups' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_groups, 'add_groups_metaboxes' );
		$this->loader->add_action( 'save_post_groups', $plugin_groups, 'handle_registrations_left_save', 100, 3 );
		$this->loader->add_action( 'wp_ajax_wdm_ld_group_request_accept', $plugin_groups, 'handle_accept_request', 100 );
		$this->loader->add_action( 'wp_ajax_wdm_ld_group_request_reject', $plugin_groups, 'handle_reject_request', 100 );
		$this->loader->add_action( 'wp_ajax_bulk_group_request_accept', $plugin_groups, 'handle_bulk_accept_request', 100 );
		$this->loader->add_action( 'wp_ajax_bulk_group_request_reject', $plugin_groups, 'handle_bulk_reject_request', 100 );
		$this->loader->add_action( 'wdm_group_limit_is_zero', $plugin_groups, 'handle_group_limit_empty' );

		// Reinvite Ajax call.
		$this->loader->add_action( 'wp_ajax_wdm_send_reinvite_mail', $plugin_groups, 'send_reinvite_mail_callback' );

		// Upload Users CSV Ajax Call.
		$this->loader->add_action( 'wp_ajax_wdm_upload_users_csv', $plugin_groups, 'ajax_upload_users_from_csv' );

		// Edit Group Details.
		$this->loader->add_action( 'wp_ajax_ldgr_update_group_details', $plugin_groups, 'ajax_update_group_details' );

		// Remove Group Image.
		$this->loader->add_action( 'wp_ajax_ldgr_remove_group_image', $plugin_groups, 'ajax_ldgr_remove_group_image' );

		// Shortcode for group users.
		$this->loader->add_action( 'init', $plugin_groups, 'add_groups_shortcodes' );

		// Restrict group leader upload privileges.
		$this->loader->add_filter( 'ajax_query_attachments_args', $plugin_groups, 'restrict_group_leader_upload_privileges' );

		// Trigger group enrollment emails from the Learndash Core.
		$this->loader->add_action( 'ld_added_group_access', $plugin_groups, 'trigger_group_enrollment_emails', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the settings module functionality
	 * of the plugin.
	 *
	 * @since 4.0
	 * @access private
	 */
	private function define_settings_module_hooks() {
		$plugin_settings = Ld_Group_Registration_Settings::get_instance();
		$this->loader->add_action( 'admin_menu', $plugin_settings, 'add_settings_menu', 100 );
		$this->loader->add_filter( 'plugin_action_links_' . plugin_basename( WDM_LDGR_PLUGIN_FILE ), $plugin_settings, 'add_settings_page_link' );
		$this->loader->add_filter( 'login_redirect', $plugin_settings, 'handle_wp_login_redirect', 10, 3 );
		$this->loader->add_filter( 'woocommerce_login_redirect', $plugin_settings, 'handle_woo_login_redirect', 10, 2 );
		// cspell:disable-next-line .
		$this->loader->add_action( 'admin_menu', $plugin_settings, 'save_apperance_settings', 100 );

		// Migrate and recalculate old group seats data [ added in 4.3.8 ].
		$this->loader->add_action(
			'plugins_loaded',
			$plugin_settings,
			'migrate_and_recalculate_group_seats',
			55 // Priority should be higher than the priority of the `plugins_loaded` action in the main plugin file.
		);
	}

	/**
	 * Register all of the hooks related to the subscriptions module functionality
	 * of the plugin.
	 *
	 * @since 4.0
	 * @access private
	 */
	private function define_subscriptions_module_hooks() {
		$plugin_subscriptions = Ld_Group_Registration_Subscriptions::get_instance();

		$this->loader->add_action( 'woocommerce_product_after_variable_attributes', $plugin_subscriptions, 'handle_variation_settings_fields', 10, 3 );
		$this->loader->add_action( 'woocommerce_save_product_variation', $plugin_subscriptions, 'save_variation_settings_fields', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the users module functionality
	 * of the plugin.
	 *
	 * @since 4.0
	 * @access private
	 */
	private function define_users_module_hooks() {
		$plugin_users = Ld_Group_Registration_Users::get_instance();

		$this->loader->add_action( 'woocommerce_subscription_status_on-hold', $plugin_users, 'restrict_users_after_sub_put_on_hold' );
		$this->loader->add_action( 'woocommerce_subscription_status_cancelled', $plugin_users, 'restrict_users_after_sub_put_on_hold' );
		$this->loader->add_action( 'woocommerce_subscription_status_expired', $plugin_users, 'restrict_users_after_sub_put_on_hold' );
		$this->loader->add_action( 'woocommerce_subscription_status_active', $plugin_users, 'give_access_to_users_after_sub_active' );
		$this->loader->add_action( 'wdm_created_new_group_using_ldgr', $plugin_users, 'save_additional_data', 10, 3 );
		/**
		 * Commented below filter hook callback, since it adds the complete subscription title before the group name
		 * and the reason to have done this is not known
		 *
		 * @since 3.8.2
		 */
		// $this->loader->add_filter('wdm_modify_ldgr_group_title', array($plugin_users, 'modify_product_title_on_grp_reg_page'), 10, 2);
		$this->loader->add_filter( 'ld_woocommerce_add_subscription_course_access', $plugin_users, 'handle_group_leader_paid_course_access', 10, 3 );

		$this->loader->add_action( 'wp_ajax_group_users_seat_recalculate', $plugin_users, 'ajax_group_users_seat_recalculate', 100 );
	}

	/**
	 * Register all of the hooks related to the woocommerce module functionality
	 * of the plugin.
	 *
	 * @since 4.0
	 * @access private
	 */
	private function define_woocommerce_module_hooks() {
		$plugin_woocommerce = Ld_Group_Registration_Woocommerce::get_instance();

		$this->loader->add_action( 'woocommerce_order_status_completed', $plugin_woocommerce, 'handle_group_creation_on_order_completion', 100, 1 );
		$this->loader->add_action( 'add_meta_boxes', $plugin_woocommerce, 'add_group_purchase_metabox' );
		$this->loader->add_action( 'save_post_product', $plugin_woocommerce, 'save_group_purchase_options', 100 );
		$this->loader->add_action( 'woocommerce_before_add_to_cart_button', $plugin_woocommerce, 'display_woo_group_registration_options', 100 );
		// $this->loader->add_filter( 'woocommerce_cart_item_subtotal', $plugin_woocommerce, 'ldgr_woocommerce_cart_item_subtotal', 10, 3 );
		$this->loader->add_action( 'woocommerce_cart_calculate_fees', $plugin_woocommerce, 'ldgr_woocommerce_cart_calculate_fees', 10, 1 );
		// Store the custom fields.
		$this->loader->add_filter( 'woocommerce_add_cart_item_data', $plugin_woocommerce, 'save_cart_item_data', 10, 2 );
		$this->loader->add_filter( 'woocommerce_get_cart_item_from_session', $plugin_woocommerce, 'check_group_registration_status_for_product', 1, 3 );
		$this->loader->add_action( 'woocommerce_add_order_item_meta', $plugin_woocommerce, 'update_woo_order_item_meta', 1, 2 );
		$this->loader->add_filter( 'woocommerce_cart_item_name', $plugin_woocommerce, 'woo_update_cart_item_name', 10, 3 );
		$this->loader->add_filter( 'woocommerce_cart_item_quantity', $plugin_woocommerce, 'woo_update_cart_item_quantity', 10, 3 );
		$this->loader->add_filter( 'woocommerce_add_to_cart_validation', $plugin_woocommerce, 'handle_woo_add_to_cart_validation', 10, 3 );
		$this->loader->add_action( 'wp_ajax_wdm_show_enroll_option', $plugin_woocommerce, 'ajax_show_enroll_option_callback' );
		$this->loader->add_action( 'woocommerce_after_add_to_cart_button', $plugin_woocommerce, 'woo_add_group_details', 10 );

		// Update Group Title if customer set a different name for it.
		$this->loader->add_filter( 'wdm_group_name', $plugin_woocommerce, 'woo_update_group_title', 10, 5 );

		// Hide group registration order meta on cart, checkout and order pages.
		// cspell:disable-next-line .
		$this->loader->add_filter( 'woocommerce_hidden_order_itemmeta', $plugin_woocommerce, 'hide_admin_group_reg_order_meta' );

		// Add go to dashboard button on thankyou page.
		$this->loader->add_action( 'woocommerce_thankyou', $plugin_woocommerce, 'display_goto_dashboard_button', 100 );
		// $this->loader->add_action( 'wp_enqueue_scripts', $plugin_woocommerce, 'enqueue_thankyou_page_scripts', 100 );

		// Enforce min quantity.
		$this->loader->add_filter( 'woocommerce_quantity_input_min', $plugin_woocommerce, 'enforce_min_quantity_for_products', 10, 2 );

		$this->loader->add_filter( 'woocommerce_add_to_cart_quantity', $plugin_woocommerce, 'update_product_quantity', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the reports module functionality
	 * of the plugin.
	 *
	 * @since 4.0
	 * @access private
	 */
	private function define_reports_module_hooks() {
		$plugin_reports = Ld_Group_Registration_Reports::get_instance();

		// Ajax call for the table.
		$this->loader->add_action( 'wp_ajax_wdm_lgdr_create_report_table', $plugin_reports, 'create_report_table_callback' ); // cspell:disable-line .
		// Ajax call for report.
		$this->loader->add_action( 'wp_ajax_wdm_display_ldgr_group_report', $plugin_reports, 'display_ldgr_group_report_callback' );
		// Instructor reports filtering
		$this->loader->add_action( 'ir_filter_instructor_query', $plugin_reports, 'filter_group_reports_for_instructor' );
	}

	/**
	 * Register all of the hooks related to the group code module functionality
	 * of the plugin.
	 *
	 * @since 4.1.0
	 * @access private
	 */
	private function define_group_code_module_hooks() {
		$plugin_group_code = Ld_Group_Registration_Group_Code::get_instance();

		// Settings.
		$this->loader->add_filter( 'ldgr_setting_tab_headers', $plugin_group_code, 'add_group_code_setting_tab_header', 10, 1 );
		$this->loader->add_action( 'ldgr_settings_tab_content_end', $plugin_group_code, 'add_group_code_setting_tab_contents', 10, 1 );
		$this->loader->add_action( 'admin_menu', $plugin_group_code, 'save_group_code_settings', 100 );
		$this->loader->add_action( 'admin_menu', $plugin_group_code, 'add_group_code_submenu', 100 );
		// Check if settings enabled.
		$ldgr_enable_group_code = get_option( 'ldgr_enable_group_code' );
		if ( 'on' != $ldgr_enable_group_code ) {
			return;
		}
		// Create the group code post type.
		$this->loader->add_action( 'init', $plugin_group_code, 'create_group_code_post_type' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_group_code, 'enqueue_group_code_scripts' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_group_code, 'admin_enqueue_group_code_scripts' );
		$this->loader->add_action( 'save_post_ldgr_group_code', $plugin_group_code, 'admin_save_group_code', 10, 3 );
		$this->loader->add_filter( 'ldgr_filter_group_registration_tab_headers', $plugin_group_code, 'add_group_code_tab_header', 10, 2 );
		$this->loader->add_filter( 'ldgr_filter_group_registration_tab_contents', $plugin_group_code, 'add_group_code_tab_contents', 10, 2 );

		// Various group code ajax methods
		$this->loader->add_action( 'wp_ajax_ldgr-create-group-code', $plugin_group_code, 'ajax_create_group_code' );
		$this->loader->add_action( 'wp_ajax_ldgr-update-group-code', $plugin_group_code, 'ajax_update_group_code' );
		$this->loader->add_action( 'wp_ajax_ldgr-generate-group-code', $plugin_group_code, 'ajax_generate_group_code' );
		$this->loader->add_action( 'wp_ajax_ldgr-delete-group-code', $plugin_group_code, 'ajax_delete_group_code' );
		$this->loader->add_action( 'wp_ajax_ldgr-group-code-status-toggle', $plugin_group_code, 'ajax_group_code_status_toggle' );
		$this->loader->add_action( 'wp_ajax_ldgr-fetch-group-code-details', $plugin_group_code, 'ajax_fetch_group_code_details' );  }

	/**
	 * Register all of the hooks related to the group code registration module functionality
	 * of the plugin.
	 *
	 * @since 4.1.0
	 * @access private
	 */
	private function define_group_code_registration_module_hooks() {
		$plugin_group_code_registration = Ld_Group_Registration_Group_Code_Registration::get_instance();

		// Check if group code settings enabled.
		$ldgr_enable_group_code = get_option( 'ldgr_enable_group_code' );

		if ( 'on' != $ldgr_enable_group_code ) {
			return;
		}

		// Shortcode for group code registration.
		$this->loader->add_action( 'init', $plugin_group_code_registration, 'add_group_code_registration_shortcodes' );

		// Enqueue scripts and styles.
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_group_code_registration, 'enqueue_group_code_registration_scripts' );

		// Ajax handler for group code reg form submission.
		$this->loader->add_action( 'wp_ajax_nopriv_ldgr-submit-group-code-reg-form', $plugin_group_code_registration, 'ajax_submit_group_code_reg_form' );
		$this->loader->add_action( 'wp_ajax_nopriv_ldgr-submit-group-code-enroll-form', $plugin_group_code_registration, 'ajax_submit_group_code_enroll_form' );
		$this->loader->add_action( 'wp_ajax_ldgr-submit-group-code-enroll-form', $plugin_group_code_registration, 'ajax_submit_group_code_enroll_form' );
	}

	/**
	 * Register all of the hooks related to the unlimited members module functionality
	 * of the plugin.
	 *
	 * @since 4.1.0
	 * @access private
	 */
	private function define_unlimited_members_module_hooks() {
		$plugin_unlimited_members = Ld_Group_Registration_Unlimited_Members::get_instance();

		// * Add and Save Metaboxes on product create/edit page for unlimited groups.
		$this->loader->add_action( 'save_post_product', $plugin_unlimited_members, 'save_unlimited_member_settings', 100 );

		// * Handle Product single page.
		$this->loader->add_action( 'woocommerce_before_add_to_cart_button', $plugin_unlimited_members, 'display_unlimited_members_product_options', 100 );

		// * Handle Cart Page.
		// * Save cart and order meta.
		$this->loader->add_filter( 'woocommerce_add_cart_item_data', $plugin_unlimited_members, 'save_unlimited_members_product_options', 99, 2 );
		$this->loader->add_filter( 'woocommerce_get_item_data', $plugin_unlimited_members, 'render_details_on_cart_and_checkout', 99, 2 );
		$this->loader->add_action( 'woocommerce_add_order_item_meta', $plugin_unlimited_members, 'update_order_meta_details', 99, 3 );
		// cspell:disable-next-line .
		$this->loader->add_filter( 'woocommerce_hidden_order_itemmeta', $plugin_unlimited_members, 'hide_unlimited_seats_order_meta', 10, 1 );

		// * Dynamically update price.
		$this->loader->add_action( 'woocommerce_before_calculate_totals', $plugin_unlimited_members, 'calculate_unlimited_members_product_price', 99 );

		// Disable quantity for unlimited members products.
		$this->loader->add_filter( 'woocommerce_cart_item_quantity', $plugin_unlimited_members, 'remove_quantity_for_unlimited_member_products', 10, 3 );
		// * Handle meta for unlimited groups after group creation.
		$this->loader->add_filter( 'wdm_change_group_quantity', $plugin_unlimited_members, 'update_group_quantity_to_unlimited', 10, 4 );

		$this->loader->add_action( 'ldgr_action_after_update_group', $plugin_unlimited_members, 'update_group_meta_for_unlimited_seats', 10, 5 );
		$this->loader->add_action( 'ldgr_action_after_create_group', $plugin_unlimited_members, 'update_group_meta_for_unlimited_seats', 10, 5 );
	}

	/**
	 * Register all of the hooks related to the unlimited members module functionality
	 * of the plugin.
	 *
	 * @since 4.2.0
	 * @access private
	 */
	private function define_sub_groups_module_hooks() {
		$plugin_sub_groups = Ld_Group_Registration_Sub_Groups::get_instance();

		// Enqueue sub-groups scripts.
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_sub_groups, 'enqueue_sub_groups_scripts' );
		// Create Sub Group.
		$this->loader->add_action( 'wp_ajax_ldgr_create_sub_group', $plugin_sub_groups, 'ajax_create_sub_group' );

		// Show Sub Group.
		$this->loader->add_action( 'wp_ajax_ldgr_show_edit_sub_group', $plugin_sub_groups, 'ajax_show_edit_sub_group' );

		// Edit Sub Group.
		$this->loader->add_action( 'wp_ajax_ldgr_edit_sub_group', $plugin_sub_groups, 'ajax_edit_sub_group' );

		// Add custom label for sub group.
		$this->loader->add_filter( 'learndash_custom_label_fields', $plugin_sub_groups, 'add_custom_sub_group_label_setting', 20, 1 );
		$this->loader->add_filter( 'learndash_get_label', $plugin_sub_groups, 'add_sub_group_label', 10, 2 );

		$this->loader->add_filter( 'ldgr_filter_group_registration_tab_headers', $plugin_sub_groups, 'add_sub_group_tab_header', 10, 2 );
		$this->loader->add_filter( 'ldgr_filter_group_registration_tab_contents', $plugin_sub_groups, 'add_sub_group_tab_contents', 10, 2 );    }

	/**
	 * Register all of the hooks related to the dynamic groups module functionality
	 * of the plugin.
	 *
	 * @since 4.3.0
	 * @access private
	 */
	private function define_dynamic_groups_module_hooks() {
		$plugin_dynamic_groups = Ld_Group_Registration_Dynamic_Group::get_instance();

		$this->loader->add_action( 'save_post_product', $plugin_dynamic_groups, 'save_dynamic_group_settings', 100 );
		$this->loader->add_action( 'woocommerce_after_add_to_cart_button', $plugin_dynamic_groups, 'woo_add_dynamic_products_dropdown', 5 );
		$this->loader->add_action( 'woocommerce_after_add_to_cart_button', $plugin_dynamic_groups, 'woo_add_dynamic_courses', 15 );
		$this->loader->add_action( 'woocommerce_before_calculate_totals', $plugin_dynamic_groups, 'update_dynamic_price', 10, 2 );
		$this->loader->add_action( 'woocommerce_add_order_item_meta', $plugin_dynamic_groups, 'update_order_item_meta', 100, 2 );
		$this->loader->add_filter( 'woocommerce_add_to_cart_validation', $plugin_dynamic_groups, 'handle_woo_add_to_cart_validation', 30, 3 );
		$this->loader->add_filter( 'woocommerce_cart_item_name', $plugin_dynamic_groups, 'add_courses_to_cart_item_name', 15, 3 );
		$this->loader->add_filter( 'woocommerce_cart_item_name', $plugin_dynamic_groups, 'increase_seats_item_name', 15, 3 );
		$this->loader->add_filter( 'ldgr_order_courses_data', $plugin_dynamic_groups, 'add_dynamic_courses_in_order', 10, 2 );
		$this->loader->add_filter( 'woocommerce_add_cart_item_data', $plugin_dynamic_groups, 'add_courses_to_cart_item_meta', 15, 2 );
		$this->loader->add_filter( 'ldgr_order_group_data', $plugin_dynamic_groups, 'add_dynamic_data_before_group_creation', 10, 2 );
		$this->loader->add_filter( 'woocommerce_cart_item_quantity', $plugin_dynamic_groups, 'remove_quantity_for_add_courses', 10, 3 );
		// cspell:disable-next-line .
		$this->loader->add_filter( 'woocommerce_hidden_order_itemmeta', $plugin_dynamic_groups, 'hide_admin_group_reg_order_meta' );
		$this->loader->add_filter( 'woocommerce_order_item_get_formatted_meta_data', $plugin_dynamic_groups, 'hide_public_group_reg_order_meta', 10, 2 );
		// Ajax for getting variation groups.
		$this->loader->add_action( 'wp_ajax_ldgr_get_variation_groups', $plugin_dynamic_groups, 'ajax_get_variation_groups' );
		// Ajax for getting variation related course list.
		$this->loader->add_action( 'wp_ajax_ldgr_update_variation_course_list', $plugin_dynamic_groups, 'ajax_update_variation_course_list' );
	}
	/**
	 * Register all hooks related to dynamic fields on registration form.
	 *
	 * @return void
	 */
	private function define_dynamic_registration_fields_module_hooks() {
		$plugin_dynamic_fields = Ld_Group_Registration_Dynamic_Fields::get_instance();

		$this->loader->add_filter( 'ldgr_setting_tab_headers', $plugin_dynamic_fields, 'add_fields_setting_tab_header', 80, 1 );

		// This filter is used to add the fields to the dynamic csv export.
		$this->loader->add_filter( 'ldgr_filter_csv_data_list', $plugin_dynamic_fields, 'alter_csv_data', 10, 3 );
		$this->loader->add_filter( 'wdm_ld_gr_alter_upload_data', $plugin_dynamic_fields, 'alter_upload_data', 10, 3 );
		$this->loader->add_filter( 'ldgr_filter_csv_data_list_map', $plugin_dynamic_fields, 'alter_csv_data_list', 10, 1 );
		$this->loader->add_filter( 'ldgr_filter_required_csv_columns', $plugin_dynamic_fields, 'alter_required_columns', 10, 3 );
		$this->loader->add_filter( 'ldgr_filter_valid_csv_data', $plugin_dynamic_fields, 'validate_dynamic_csv_fields', 10, 4 );

		$this->loader->add_action( 'ldgr_settings_tab_content_end', $plugin_dynamic_fields, 'add_dynamic_fields_setting_tab_contents', 10, 1 );
		$this->loader->add_action( 'admin_menu', $plugin_dynamic_fields, 'save_dynamic_fields_options', 100 );
		// These hooks are used to display custom fields on profile and user edit page
		$this->loader->add_action( 'show_user_profile', $plugin_dynamic_fields, 'display_dynamic_user_fields', 100 );
		$this->loader->add_action( 'edit_user_profile', $plugin_dynamic_fields, 'display_dynamic_user_fields', 100 );

		// Hook is used to save custom fields that have been added to the WordPress profile page (if current user)
		$this->loader->add_action( 'personal_options_update', $plugin_dynamic_fields, 'update_dynamic_user_fields', 100 );
		$this->loader->add_action( 'edit_user_profile_update', $plugin_dynamic_fields, 'update_dynamic_user_fields', 100 );
		$this->loader->add_action( 'ldgr_action_registration_form_before_group_code_field', $plugin_dynamic_fields, 'add_dynamic_fields_group_code', 100 );

		// This action is used to save dynamic field data when user is created from group code page
		$this->loader->add_action( 'ldgr_action_group_code_user_created', $plugin_dynamic_fields, 'save_dynamic_field_form_data', 100, 3 );
		$this->loader->add_action( 'ldgr_action_group_code_user_enrolled', $plugin_dynamic_fields, 'save_dynamic_field_form_data', 100, 3 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 4.0
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
	 * @return Ld_Group_Registration_Loader    Orchestrates the hooks of the plugin.
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
