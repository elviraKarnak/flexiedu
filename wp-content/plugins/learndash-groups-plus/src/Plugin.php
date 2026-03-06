<?php
/**
 * Plugin service provider class file.
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore autologin shortcodes pagename atts ulgm sweetalert rgar childgroup numberposts
 */

namespace LearnDash\Groups_Plus;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Exception;
use LearnDash\Core\Utilities\Cast;
use LearnDash\Groups_Plus\Emails\Welcome;
use LearnDash\Groups_Plus\lucatume\DI52\ServiceProvider;
use LearnDash\Groups_Plus\Module\Notices\Notices;
use LearnDash\Groups_Plus\Module\BuddyBoss\Sync;
use LearnDash\Groups_Plus\Module\Custom_Label;
use LearnDash\Groups_Plus\Module\Group;
use LearnDash\Groups_Plus\Module\Migration;
use LearnDash\Groups_Plus\Module\Team_Member;
use LearnDash\Groups_Plus\Module\WooCommerce\Organizations;
use LearnDash\Groups_Plus\Module\WooCommerce\Organizations_Groups;
use LearnDash\Groups_Plus\Module\WooCommerce\Seats;
use LearnDash\Groups_Plus\Module\WooCommerce\Teams;
use LearnDash\Groups_Plus\StellarWP\AdminNotices\AdminNotices;
use LearnDash\Groups_Plus\Utility\Database;
use LearnDash\Groups_Plus\Utility\Dependency_Checker;
use LearnDash\Groups_Plus\Utility\SharedFunctions;
use LearnDash\Groups_Plus\Utility\Timer;
use WP_Post;
use WP_Query;
use WP_Screen;
use WP_Session_Tokens;
use WP_User;

/**
 * Plugin service provider class.
 *
 * @since 1.0
 */
class Plugin extends ServiceProvider {
	/**
	 * Dependency checker object.
	 *
	 * @since 1.0.0
	 *
	 * @var Dependency_Checker
	 */
	private $dependency_checker;

	/**
	 * Register service provider.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->singleton( Custom_Label::class );
		$this->container->singleton( Migration::class );
		$this->container->bind( 'Teams_Product_Edit', Teams\Product_Edit::class );
		$this->container->bind( 'Teams_Product_Single', Teams\Product_Single::class );
		$this->container->bind( 'Teams_Add_To_Cart', Teams\Add_To_Cart::class );
		$this->container->bind( 'Teams_Checkout', Teams\Checkout::class );
		$this->container->bind( 'Teams_Subscriptions', Teams\Subscriptions::class );

		$this->container->setVar( 'Teams_WooCommerce_Product_Type', 'groups_plus_teams' );

		// Register service providers.
		$this->container->register( Module\Provider::class );
		$this->container->register( Admin\Provider::class );

		$this->dependency_checker = $this->container->get( Dependency_Checker::class );

		register_activation_hook( LEARNDASH_GROUPS_PLUS_PLUGIN_FILE, array( $this, 'activate' ) );

		$this->set_plugin_dependencies();
	}

	/**
	 * Service provider boot method.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function boot() {
		if ( $this->dependency_checker->check_dependency_results() && php_sapi_name() !== 'cli' ) {
			// Admin Notices.
			AdminNotices::initialize(
				'learndash_groups_plus',
				LEARNDASH_GROUPS_PLUS_URL . 'vendor-prefixed/stellarwp/admin-notices'
			);

			$admin_modules = array();

			if ( is_admin() ) {
				$version       = LEARNDASH_GROUPS_PLUS_VERSION;
        		$version  	   = str_replace( '.', '_', $version );
				$upgrade_class = 'LearnDash\Groups_Plus\Module\Upgrade\To_' . $version;

				$admin_modules = array(
					Settings::class => $this->container->singleton( Settings::class ),
					$upgrade_class => $this->container->get( $upgrade_class ),
				);

				Settings::init();
			}

			$modules = array(
				Custom_Label::class         => $this->container->get( Custom_Label::class ),
				Migration::class            => $this->container->get( Migration::class ),
				Teams\Product_Edit::class   => $this->container->get( 'Teams_Product_Edit' ),
				Teams\Product_Single::class => $this->container->get( 'Teams_Product_Single' ),
				Teams\Add_To_Cart::class    => $this->container->get( 'Teams_Add_To_Cart' ),
				Teams\Checkout::class       => $this->container->get( 'Teams_Checkout' ),
				Teams\Subscriptions::class  => $this->container->get( 'Teams_Subscriptions' ),
			);

			Group::get_instance();
			Team_Member::get_instance();
			Organizations::get_instance();
			Organizations_Groups::get_instance();
			new Seats();
			new Sync();
			new Timer;

			$this->container->setVar( 'modules', array_merge( $admin_modules, $modules ) );
			$this->container->setVar( 'Teams_WooCommerce_Product_Type', 'groups_plus_teams' );

			$this->register_shortcodes();
			$this->register_blocks();
			$this->hook_actions();
			$this->hook_filters();
			$this->hook_ajax();
		}
	}

	/**
	 * Method contains action hooks used in the plugin.
	 *
	 * @return void
	 */
	public function hook_actions(): void {
		add_action( 'admin_menu', $this->container->callback( $this, 'menu' ) );
		add_action( 'admin_notices', $this->container->callback( $this, 'learndash_group_hierarchy_check' ) );
		add_action( 'admin_enqueue_scripts', $this->container->callback( $this, 'admin_enqueue_scripts' ) );
		add_action( 'add_meta_boxes', $this->container->callback( $this, 'add_meta_boxes' ), 10, 2 );
		add_action( 'save_post', $this->container->callback( $this, 'save_post' ), 10, 3 );

		add_action( 'init', $this->container->callback( $this, 'rewrite' ) );
		add_action( 'set_404', $this->container->callback( $this, 'set_404' ) );
		add_action( 'init', $this->container->callback( $this, 'set_role_capabilities' ), 20 );
		add_action( 'wp_enqueue_scripts', $this->container->callback( $this, 'wp_enqueue_scripts' ) );
		add_action( 'wp_head', $this->container->callback( $this, 'override_frontend_style' ), 20 );
		add_action( 'wp_login', $this->container->callback( $this, 'set_last_login' ) );
		add_action( 'edit_user_profile', $this->container->callback( $this, 'edit_user_profile' ), 1, 1 );
		add_action( 'profile_update', $this->container->callback( $this, 'profile_update' ), 10, 3 );

		if ( SharedFunctions::is_gravity_form_active() ) {
			add_action( 'gform_entry_created', $this->container->callback( $this, 'gform_entry_created' ), 10, 2 );
			add_action( 'save_post', $this->container->callback( $this, 'gform_save_setting' ), 10, 3 );
		}

		// Defer localization until after init.
		$checker = $this->dependency_checker;
		add_action(
			'init',
			static function () use ( $checker ) {
				$checker->set_message(
					esc_html__( 'LearnDash LMS - Groups Plus Add-on requires the following plugin(s) to be active:', 'learndash-groups-plus' )
				);
			}
		);
	}

	/**
	 * Method contains filter hooks used in the plugin.
	 *
	 * @return void
	 */
	public function hook_filters(): void {
		add_filter( 'plugin_action_links', $this->container->callback( $this, 'add_action_plugin' ), 10, 5 );
		add_filter( 'query_vars', $this->container->callback( $this, 'query_vars' ) );
		add_filter( 'init', $this->container->callback( $this, 'autologin' ) );
		add_filter( 'template_include', $this->container->callback( $this, 'change_template' ) );
		add_filter( 'document_title_parts', $this->container->callback( $this, 'document_title_parts' ), 20 );
		add_filter( 'learndash_course_progression_enabled', $this->container->callback( $this, 'learndash_course_progression_enabled' ), 10, 2 );

		add_filter( 'learndash_lesson_progress_alert', $this->container->callback( $this, 'learndash_lesson_progress_alert' ), 10, 3 );
		add_filter( 'learndash_topic_progress_alert', $this->container->callback( $this, 'learndash_lesson_progress_alert' ), 10, 3 );

		add_filter( 'hidden_columns', $this->container->callback( $this, 'set_hidden_columns' ), 10, 3 );
		add_filter( 'learndash_listing_columns', $this->container->callback( $this, 'add_groups_posts_columns' ), 20, 2 );

		if ( SharedFunctions::is_gravity_form_active() ) {
			add_filter( 'learndash_settings_fields', $this->container->callback( $this, 'learndash_settings_fields' ), 10, 2 );
		}
	}

	/**
	 * Method contains AJAX hooks used in the plugin.
	 *
	 * @return void
	 */
	public function hook_ajax(): void {
		// Manage organization.
		add_action( 'wp_ajax_action_manage_organization_add_team', $this->container->callback( $this, 'action_manage_organization_add_team' ) );
		add_action( 'wp_ajax_action_manage_organization_delete_team', $this->container->callback( $this, 'action_manage_organization_delete_team' ) );
		add_action( 'wp_ajax_action_manage_organization_add_team_leader_welcome_email', $this->container->callback( $this, 'action_manage_organization_add_team_leader_welcome_email' ) );

		// Manage team.
		add_action( 'wp_ajax_action_manage_team_team_leaders', $this->container->callback( $this, 'action_manage_team_team_leaders' ) );
		add_action( 'wp_ajax_action_manage_team_add_team_leader', $this->container->callback( $this, 'action_manage_team_add_team_leader' ) );
		add_action( 'wp_ajax_action_manage_team_delete_team_leader', $this->container->callback( $this, 'action_manage_team_delete_team_leader' ) );
		add_action( 'wp_ajax_action_manage_team_add_team_leader_as_team_member', $this->container->callback( $this, 'action_manage_team_add_team_leader_as_team_member' ) );
		add_action( 'wp_ajax_action_manage_team_delete_team_leader_from_team_member', $this->container->callback( $this, 'action_manage_team_delete_team_leader_from_team_member' ) );
		add_action( 'wp_ajax_action_manage_team_delete_team_leader_permanently', $this->container->callback( $this, 'action_manage_team_delete_team_leader_permanently' ) );
		add_action( 'wp_ajax_action_manage_team_courses', $this->container->callback( $this, 'action_manage_team_courses' ) );

		// Manage reports.
		add_action( 'wp_ajax_action_fetch_organization_report', $this->container->callback( $this, 'action_fetch_organization_report' ) );
		add_action( 'wp_ajax_action_fetch_team_report', $this->container->callback( $this, 'action_fetch_team_report' ) );

		// Manage team members.
		add_action( 'wp_ajax_action_manage_team_edit_team_member', $this->container->callback( $this, 'action_manage_team_edit_team_member' ) );
		add_action( 'wp_ajax_action_manage_team_delete_team_member', $this->container->callback( $this, 'action_manage_team_delete_team_member' ) );
		add_action( 'wp_ajax_action_manage_team_delete_team_member_permanently', $this->container->callback( $this, 'action_manage_team_delete_team_member_permanently' ) );
		add_action( 'wp_ajax_action_save_groups_plus_team_member_password', $this->container->callback( $this, 'action_save_groups_plus_team_member_password' ) );
		add_action( 'wp_ajax_action_save_groups_plus_team_member', $this->container->callback( $this, 'action_save_groups_plus_team_member' ) );
		add_action( 'wp_ajax_action_add_team_member_email', $this->container->callback( $this, 'action_add_team_member_email' ) );
		add_action( 'wp_ajax_approved_request', $this->container->callback( $this, 'approve_action_catcher' ) );

		// Broadcasts.
		add_action( 'wp_ajax_action_send_broadcast_email_to_team_members', $this->container->callback( $this, 'action_send_broadcast_email_to_team_members' ) );
		add_action( 'wp_ajax_action_send_broadcast_message_to_team_members', $this->container->callback( $this, 'action_send_broadcast_message_to_team_members' ) );
		add_action( 'wp_ajax_action_send_broadcast_email_to_team_leaders', $this->container->callback( $this, 'action_send_broadcast_email_to_team_leaders' ) );
		add_action( 'wp_ajax_action_get_broadcast_messages', $this->container->callback( $this, 'action_get_broadcast_messages' ) );
		add_action( 'wp_ajax_action_delete_broadcast_message', $this->container->callback( $this, 'action_delete_broadcast_message' ) );

		// Others.
		add_action( 'wp_ajax_action_lock_unlock_lesson', $this->container->callback( $this, 'action_lock_unlock_lesson' ) );
		add_action( 'wp_ajax_action_import_users_groups_plus', $this->container->callback( $this, 'action_import_users_groups_plus' ) );
	}

	/**
	 * Check plugin dependencies.
	 *
	 * @return void
	 */
	private function set_plugin_dependencies() {
		$this->dependency_checker->set_dependencies(
			array(
				'sfwd-lms/sfwd_lms.php' => array(
					'label'       => '<a href="https://learndash.com">LearnDash LMS</a>',
					'class'       => 'SFWD_LMS',
					'min_version' => '4.7.0',
				),
			)
		);
	}

	/**
	 * Method to run on plugin activation.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function activate(): void {
		set_transient( 'learndash_groups_plus_flush', 1, 60 );

		// Set default options values.
		$options = array(
			'hide_exclude_from_publicly_sold_seats_checkbox' => 'yes',
		);

		foreach ( $options as $option => $value ) {
			$current_value = get_site_option( $option );

			if ( $current_value === false ) {
				update_site_option( $option, $value );
			}
		}
	}

	/**
	 * Filter global query_vars.
	 *
	 * @since 1.0
	 *
	 * @param array $query_vars Global query_vars variable.
	 * @return array
	 */
	public function query_vars( $query_vars ): array {
		$query_vars[] = 'learndash-groups-plus';
		$query_vars[] = 'group_id';
		return $query_vars;
	}

	/**
	 * Auto login user when request meets certain criteria.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function autologin(): void {
		$autologin = isset( $_GET['action_autologin'] ) ? boolval( $_GET['action_autologin'] ) : null;

		if ( isset( $autologin ) && $autologin === true ) {
			$username = isset( $_GET['username'] )
				? sanitize_text_field( wp_unslash( $_GET['username'] ) )
				: null;

			if ( ! $username ) {
				wp_die( 'Invalid username' );
			}

			$user = get_user_by( 'login', $username );

			if ( ! $user instanceof WP_User ) {
				wp_die( 'Invalid user' );
			}

			$token = isset( $_GET['token'] )
				? sanitize_text_field( wp_unslash( $_GET['token'] ) )
				: null;

			if ( ! isset( $token ) ) {
				wp_die( 'Cheatin\' huh?' );
			}

			$session_token_manager = WP_Session_Tokens::get_instance( $user->ID );

			if ( ! $session_token_manager->verify( $token ) ) {
				wp_die( 'Invalid token' );
			}

			$unique_id = isset( $_GET['unique_id'] ) ? sanitize_text_field( wp_unslash( $_GET['unique_id'] ) ) : null;

			if ( isset( $unique_id ) ) {
				$user_unique_id = get_user_meta( $user->ID, 'unique_id', true );

				if ( $user_unique_id === $unique_id ) {
					wp_set_current_user( $user->ID, $user->user_login );
					wp_set_auth_cookie( $user->ID );

					// Destroy the user token after successful login.
					$session_token_manager->destroy( $token );

					// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
					do_action( 'wp_login', $user->user_login, $user );

					wp_safe_redirect(
						add_query_arg(
							[
								Notices::$admin_notice_key => Notices::$admin_notice_autologin_set_password,
							],
							admin_url( 'profile.php' )
						)
					);
					exit;
				} else {
					wp_die( esc_html__( 'Link may be expired.', 'learndash-groups-plus' ) );
				}
			}
		}
	}

	/**
	 * Register shortcodes used throughout the plugin.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function register_shortcodes(): void {
		add_shortcode( 'learndash_groups_plus', $this->container->callback( $this, 'groups_plus_shortcode' ) );
		add_shortcode( 'learndash_groups_plus_primary_report', $this->container->callback( $this, 'groups_plus_primary_report_shortcode' ) );
		add_shortcode( 'learndash_groups_plus_report', $this->container->callback( $this, 'groups_plus_report_shortcode' ) );
		add_shortcode( 'learndash_groups_plus_message_board', $this->container->callback( $this, 'message_board' ) );
	}

	/**
	 * Register Gutenberg blocks.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function register_blocks(): void {
		include_once trailingslashit( \LEARNDASH_LMS_PLUGIN_DIR ) . 'includes/gutenberg/lib/class-learndash-gutenberg-block.php';

		$blocks = array(
			'Groups_Plus',
			'Groups_Plus_Primary_Report',
			'Groups_Plus_Report',
			'Groups_Plus_Message_Board',
		);

		foreach ( $blocks as $block_class ) {
			$block_class = "\\LearnDash\\Groups_Plus\\Block\\{$block_class}";
			new $block_class();
		}
	}

	/**
	 * Add rewrite rules used in the plugin.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function rewrite(): void {
		add_rewrite_endpoint( 'learndash-groups-plus', EP_ROOT );
		add_rewrite_rule( 'learndash-groups-plus/group/([a-zA-Z0-9-]+)[/]?$', 'index.php?pagename=learndash-groups-plus&group_id=$matches[1]', 'top' );

		if ( get_transient( 'learndash_groups_plus_flush' ) ) {
			delete_transient( 'learndash_groups_plus_flush' );
			flush_rewrite_rules();
		}
	}

	/**
	 * Modify 404 status code for groups plus pseudo pages.
	 *
	 * @since 1.0
	 *
	 * @param WP_Query $query WP_Query object.
	 *
	 * @return void
	 */
	public function set_404( $query ) {
		if (
			isset( $query->query_vars['name'] )
			&& $query->query_vars['name'] === 'learndash-groups-plus'
			&& isset( $query->query_vars['group_id'] )
			&& ! empty( $query->query_vars['group_id'] )
		) {
			$query->is_404 = false;
		}
	}

	/**
	 * Set role capabilities.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function set_role_capabilities() {
		$role = get_role( 'group_leader' );

		$role->add_cap( 'wpProQuiz_show_statistics' );
	}

	/**
	 * Check group hierarchy setting and display notice accordingly.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function learndash_group_hierarchy_check(): void {
		$learndash_settings_groups_management_display = get_option( 'learndash_settings_groups_management_display', false );

		if (
			! $learndash_settings_groups_management_display
			|| ! isset( $learndash_settings_groups_management_display['group_hierarchical_enabled'] )
			|| $learndash_settings_groups_management_display['group_hierarchical_enabled'] === 'no'
			|| empty( $learndash_settings_groups_management_display['group_hierarchical_enabled'] )
		) {
			?>
			<div class="notice notice-info">
				<h4>
					<?php
						// translators: HTML tags.
						echo sprintf( esc_html__( 'Warning: LearnDash LMS - Groups Plus requires LearnDash Group Hierarchy setting enabled to make it working as expected. %sClick here to enable%s', 'learndash-groups-plus' ), '<a href="' . esc_url( admin_url( 'admin.php?page=groups-options' ) ) . '">', '</a>' );
					?>
				</h4>
			</div>
			<?php
		}
	}

	/**
	 * Add plugin action links on plugins page.
	 *
	 * @since 1.0
	 *
	 * @param array  $actions		Existing plugin action links.
	 * @param string $plugin_file	Current plugin file.
	 * @return array Returned action links.
	 */
	public function add_action_plugin( $actions, $plugin_file ) {
		static $plugin;

		if ( ! isset( $plugin ) ) {
			$plugin = plugin_basename( __FILE__ );
		}

		if ( $plugin === $plugin_file ) {
			// Add new plugin action links.
		}

		return $actions;
	}

	/**
	 * Filter template file.
	 *
	 * @since 1.0
	 *
	 * @param string $template Template file path.
	 * @return string Returned template file path.
	 */
	public function change_template( $template ) {
		global $wp;

		if (
			isset( $wp->query_vars['learndash-groups-plus'] )
			&& get_query_var( 'group_id', false ) === false
		) {
			$parent_group_id = Group::get_parent_group_id();

			// If the first Group that is found is a Team, ensure that we properly set up the page to load as a Team.
			if (
				is_int( $parent_group_id )
				&& get_post_meta( $parent_group_id, SharedFunctions::$is_non_organization_team, true )
			) {
				// Load the Team-specific layout.
				$wp->query_vars['pagename'] = 'learndash-groups-plus';

				set_query_var( 'group_id', general_encrypt_decrypt( 'encrypt', $parent_group_id ) );
				$_GET['group'] = get_query_var( 'group_id' );
			}
		}

		if ( isset( $wp->query_vars['learndash-groups-plus'] ) && get_query_var( 'group_id', false ) === false ) {
			if ( ! is_user_logged_in() ) {
				esc_html_e( 'You must be logged in to use this tool.', 'learndash-groups-plus' );
				return;
			}

			http_response_code( 200 );

			// Check theme directory first - new folder structure.
			$new_template = locate_template( [ 'learndash/groups-plus/template-learndash-groups-plus.php' ] );

			if ( '' !== $new_template ) {
				return $new_template;
			}

			// Check theme directory - old folder structure (backwards compatibility).
			$new_template = locate_template( [ 'learndash-groups-plus/template-learndash-groups-plus.php' ] );
			if ( '' != $new_template ) {
				return $new_template;
			}

			// Check plugin directory next.
			$new_template = LEARNDASH_GROUPS_PLUS_DIR . 'src/resources/templates/template-learndash-groups-plus.php';
			if ( file_exists( $new_template ) ) {
				return $new_template;
			}
		} elseif ( isset( $wp->query_vars['pagename'] ) && $wp->query_vars['pagename'] === 'learndash-groups-plus' && get_query_var( 'group_id' ) !== '' ) {
			if ( ! is_user_logged_in() ) {
				esc_html_e( 'You must be logged in to see this report.', 'learndash-groups-plus' );
				return;
			}

			http_response_code( 200 );

			// Check theme directory first - new folder structure.
			$new_template = locate_template( [ 'learndash/groups-plus/template-learndash-groups-plus-group.php' ] );

			if ( '' !== $new_template ) {
				return $new_template;
			}

			// Check theme directory - old folder structure (backwards compatibility).
			$new_template = locate_template( [ 'learndash-groups-plus/template-learndash-groups-plus-group.php' ] );

			if ( '' !== $new_template ) {
				return $new_template;
			}

			// Check plugin directory next.
			$new_template = LEARNDASH_GROUPS_PLUS_DIR . 'src/resources/templates/template-learndash-groups-plus-group.php';

			if ( file_exists( $new_template ) ) {
				return $new_template;
			}
		}

		// Fall back to original template
		return $template;

	}

	/**
	 * Filter HTML page title tag parts.
	 *
	 * @since 1.0
	 *
	 * @param array $title Title parts.
	 *
	 * @return array
	 */
	public function document_title_parts( $title ) {
		global $wp;

		if ( isset( $wp->query_vars['learndash-groups-plus'] ) ) {
			$title['title'] = 'LearnDash Groups Plus';
		} elseif (
			isset( $wp->query_vars['pagename'] )
			&& $wp->query_vars['pagename'] === 'learndash-groups-plus'
			&& ! empty( $wp->query_vars['group_id'] )
		) {
			$title['title'] = sprintf(
				esc_html__( 'Manage %s', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team' )
			);
		}

		return $title;
	}

	/**
	 * Outputs the `learndash_groups_plus` shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,mixed> $atts           Shortcode Attributes.
	 * @param string              $content        Shortcode Content.
	 * @param string              $shortcode_name Shortcode Name.
	 *
	 * @return string Shortcode output.
	 */
	public function groups_plus_shortcode( $atts, $content = null, $shortcode_name = null ) {
		if ( is_admin() ) {
			return '';
		}

		if ( ! is_user_logged_in() ) {
			return esc_html__( 'You must be logged in to see this report.', 'learndash-groups-plus' );
		}

		ob_start();

		$parent_group_id = Group::get_parent_group_id();

		if (
			is_int( $parent_group_id )
			&& get_post_meta( $parent_group_id, SharedFunctions::$is_non_organization_team, true )
		) {
			set_query_var( 'group_id', general_encrypt_decrypt( 'encrypt', $parent_group_id ) );
			$_GET['group'] = get_query_var( 'group_id' );
		}

		if ( isset( $_GET['group'] ) && $_GET['group'] != '' ) {

			// Check theme directory first - new folder structure.
			$new_template = locate_template( [ 'learndash/groups-plus/template-learndash-groups-plus-group.php' ] );

			if ( $new_template === '' ) {
				// Check theme directory - old folder structure (backwards compatibility).
				$new_template = locate_template( [ 'learndash-groups-plus/template-learndash-groups-plus-group.php' ] );
			}

			if ( '' !== $new_template ) {
				include $new_template;
			} else {
				include LEARNDASH_GROUPS_PLUS_DIR . 'src/resources/templates/template-learndash-groups-plus-group.php';
			}
		} else {
			// Check theme directory first - new folder structure.
			$new_template = locate_template( [ 'learndash/groups-plus/template-learndash-groups-plus.php' ] );

			if ( $new_template === '' ) {
				// Check theme directory - old folder structure (backwards compatibility).
				$new_template = locate_template( [ 'learndash-groups-plus/template-learndash-groups-plus.php' ] );
			}

			if ( '' !== $new_template ) {
				include $new_template;
			} else {
				include LEARNDASH_GROUPS_PLUS_DIR . 'src/resources/templates/template-learndash-groups-plus.php';
			}
		}

		return Cast::to_string( ob_get_clean() );
	}

	/**
	 * Groups Plus summery report for primary group leader.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,mixed> $atts           Shortcode Attributes.
	 * @param string              $content        Shortcode Content.
	 * @param string              $shortcode_name Shortcode Name.
	 *
	 * @return string Shortcode output.
	 */
	public function groups_plus_primary_report_shortcode( $atts, $content = null, $shortcode_name = null ) {

		if ( current_user_can( 'administrator' ) ) {
			// return;
		}

		if ( ! is_user_logged_in() ) {
			return esc_html__( 'You must be logged in to see the this report.', 'learndash-groups-plus' );

		}
		$current_user        = wp_get_current_user();
		$logged_in_user_roles = (array) $current_user->roles;

		if ( ! in_array( 'group_leader', $logged_in_user_roles ) && ! in_array( 'administrator', $logged_in_user_roles ) ) {
			return esc_html__( 'Only group leaders have access to this report.', 'learndash-groups-plus' );
		}
		ob_start();

		// Check theme directory first - new folder structure.
		$new_template = locate_template( [ 'learndash/groups-plus/templates/shortcodes/primary-report.php' ] );

		if ( $new_template === '' ) {
			// Check theme directory - old folder structure (backwards compatibility).
			$new_template = locate_template( [ 'learndash-groups-plus/templates/shortcodes/primary-report.php' ] );
		}

		if ( '' !== $new_template ) {
			include $new_template;
		} else {
			include LEARNDASH_GROUPS_PLUS_DIR . 'src/resources/templates/shortcodes/primary-report.php';
		}

		return Cast::to_string( ob_get_clean() );
	}

	/**
	 * Team Member report.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,mixed> $atts           Shortcode Attributes.
	 * @param string              $content        Shortcode Content.
	 * @param string              $shortcode_name Shortcode Name.
	 *
	 * @return string Shortcode output.
	 */
	public function groups_plus_report_shortcode( $atts, $content = null, $shortcode_name = null ) {
		if ( current_user_can( 'administrator' ) ) {
			// return;
		}

		ob_start();

		if ( ! is_user_logged_in() ) {
			return esc_html__( 'You must be logged in to see this report.', 'learndash-groups-plus' );

		}

		$current_user         = wp_get_current_user();
		$logged_in_user_roles = (array) $current_user->roles;

		if ( ! in_array( 'group_leader', $logged_in_user_roles ) && ! in_array( 'administrator', $logged_in_user_roles ) ) {
			return esc_html__( 'Only group leaders have access to this tool.', 'learndash-groups-plus' );
		}

		// Check theme directory first - new folder structure.
		$new_template = locate_template( [ 'learndash/groups-plus/templates/shortcodes/team-member-report.php' ] );

		if ( $new_template === '' ) {
			// Check theme directory - old folder structure (backwards compatibility).
			$new_template = locate_template( [ 'learndash-groups-plus/templates/shortcodes/team-member-report.php' ] );
		}

		if ( '' !== $new_template ) {
			include $new_template;
		} else {
			include LEARNDASH_GROUPS_PLUS_DIR . 'src/resources/templates/shortcodes/team-member-report.php';
		}

		return Cast::to_string( ob_get_clean() );
	}

	public function add_meta_boxes( $post_type, $post ) {
		add_meta_box( 'learndash-groups-plus-team-members-meta-box', sprintf( esc_html__( 'Learndash Groups Plus %s', 'learndash-groups-plus' ), learndash_get_custom_label( 'team_members' ) ), array( $this, 'meta_box_callback' ), 'groups', 'side', 'high' );
	}

	/**
	 * Meta Box callback for Groups.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post Post Object.
	 *
	 * @return void
	 *
	 * @phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
	 */
	public function meta_box_callback( $post ) {
		if ( ! $post instanceof WP_Post ) {
			return;
		}

		$ulgm_total_seats = get_post_meta( $post->ID, '_ulgm_total_seats', true );
		$is_upgraded      = get_post_meta( $post->ID, '_ulgm_is_upgraded', true );

		wp_nonce_field( 'learndash_groups_plus_team_members_metabox', 'learndash_groups_plus_team_members_metabox_nonce' );

		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if (
			! is_plugin_active( 'uncanny-learndash-groups/uncanny-learndash-groups.php' )
			|| (
				empty( $ulgm_total_seats )
				&& 'yes' !== $is_upgraded
			)
		) {
			$this->show_number_of_licenses_field( $post );
		}

		$team_leader_use_seat = get_site_option( 'team_leader_use_seat' );
		if ( $team_leader_use_seat ) {
			echo '<br /><br />';
			$this->show_number_of_team_leaders_to_exclude_field( $post );
		}

		if ( $post->post_parent === 0 ) {
			echo '<br /><br />';
			$this->show_disable_team_creation_field( $post );
		}

		if (
			SharedFunctions::is_woocommerce_active()
			&& 'yes' === get_option( 'enable_wc', 'no' )
			&& '1' === get_option( 'enable_add_seats', '0' )
		) {
			echo '<br /><br />';
			$this->show_exclude_from_woocommerce_product_single_field( $post );
		}
	}

	/**
	 * Outputs the Number of Licenses field for the current Post.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $post Post Object.
	 *
	 * @return void
	 */
	protected function show_number_of_licenses_field( WP_Post $post ): void {
		?>
		<label for="number_of_licenses">
			<?php
			echo esc_html(
				sprintf(
					// translators: Team Members.
					__( 'Number of %s', 'learndash-groups-plus' ),
					learndash_get_custom_label( 'team_members' )
				)
			);
			?>
		</label>
		<input
			type="number"
			name="number_of_licenses"
			id="number_of_licenses"
			class="number_of_licenses"
			value="<?php echo esc_attr( get_post_meta( $post->ID, 'number_of_licenses', true ) ); ?>"
			style="width:100%"
		/>
		<?php
	}

	/**
	 * Outputs the Number of Team Leaders to Exclude field for the current Post.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $post Post Object.
	 *
	 * @return void
	 */
	protected function show_number_of_team_leaders_to_exclude_field( WP_Post $post ): void {
		$no_of_team_leaders_to_exclude_field = get_post_meta( $post->ID, 'no_of_team_leaders_to_exclude', true );
		$no_of_team_leaders_to_exclude_field = empty( $no_of_team_leaders_to_exclude_field ) ? '0' : $no_of_team_leaders_to_exclude_field;
		?>
		<label for="no_of_team_leaders_to_exclude">
			<?php
			echo esc_html(
				sprintf(
					// translators: Team Leaders.
					__( 'Number of %s not using seats', 'learndash-groups-plus' ),
					learndash_get_custom_label( 'team_laders' )
				)
			);
			?>
		</label>
		<input
			type="number"
			name="no_of_team_leaders_to_exclude"
			id="no_of_team_leaders_to_exclude"
			class="no_of_team_leaders_to_exclude"
			value="<?php echo esc_attr( $no_of_team_leaders_to_exclude_field ); ?>"
			style="width:100%"
		/>
		<?php
	}

	/**
	 * Outputs the Disable Team Creation field for the current Post.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $post Post Object.
	 *
	 * @return void
	 */
	protected function show_disable_team_creation_field( WP_Post $post ): void {
		?>
		<input
			type="checkbox"
			name="<?php echo esc_attr( SharedFunctions::$is_non_organization_team ); ?>"
			id="<?php echo esc_attr( SharedFunctions::$is_non_organization_team ); ?>"
			value="1"
			<?php
			checked(
				get_post_meta( $post->ID, SharedFunctions::$is_non_organization_team, true ),
				'1'
			);
			?>
		/>
		<label for="<?php echo esc_attr( SharedFunctions::$is_non_organization_team ); ?>">
			<?php
			echo esc_html(
				sprintf(
					// translators: Teams.
					__( 'Disable %s creation?', 'learndash-groups-plus' ),
					learndash_get_custom_label( 'teams' )
				)
			);
			?>
		</label>
		<?php
	}

	/**
	 * Outputs the Exclude from WooCommerce Product Single field for the current Post.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $post Post Object.
	 *
	 * @return void
	 */
	protected function show_exclude_from_woocommerce_product_single_field( WP_Post $post ): void {
		$exclude_from_individual_seat_purchase = get_post_meta( $post->ID, 'exclude_from_individual_seat_purchase', true );
		?>
		<input
			type="checkbox"
			name="exclude_from_individual_seat_purchase"
			id="exclude_from_individual_seat_purchase"
			class="no_of_team_leaders_to_exclude"
			value="1"
			<?php
			checked(
				$exclude_from_individual_seat_purchase,
				'1'
			);
			?>
		/>
		<label for="exclude_from_individual_seat_purchase">
			<?php echo esc_html__( 'Exclude from WooCommerce product pages', 'learndash-groups-plus' ); ?>
		</label>
		<?php
	}


	/**
	 * Handles saving post meta for Groups
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_id Current Post ID.
	 * @param WP_Post $post    Current Post object.
	 * @param bool    $update  Whether this is an existing post being updated.
	 *
	 * @return void
	 *
	 * @phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
	 */
	public function save_post( $post_id, $post, $update ) {
		if ( ! $post instanceof WP_Post ) {
			return;
		}

		if ( $post->post_type !== 'groups' ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$post_data = wp_unslash( $_POST );

		if (
			empty( $post_data['learndash_groups_plus_team_members_metabox_nonce'] )
			|| ! wp_verify_nonce(
				sanitize_text_field(
					$post_data['learndash_groups_plus_team_members_metabox_nonce']
				),
				'learndash_groups_plus_team_members_metabox'
			)
		) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $post_data['number_of_licenses'] ) ) {
			update_post_meta(
				$post_id,
				'number_of_licenses',
				sanitize_text_field(
					$post_data['number_of_licenses']
				)
			);
		}

		if ( isset( $post_data['no_of_team_leaders_to_exclude'] ) ) {
			update_post_meta(
				$post_id,
				'no_of_team_leaders_to_exclude',
				sanitize_text_field(
					$post_data['no_of_team_leaders_to_exclude']
				)
			);
		}

		if ( isset( $post_data[ SharedFunctions::$is_non_organization_team ] ) ) {
			update_post_meta(
				$post_id,
				SharedFunctions::$is_non_organization_team,
				sanitize_text_field(
					$post_data[ SharedFunctions::$is_non_organization_team ]
				)
			);
		}

		if ( isset( $post_data['exclude_from_individual_seat_purchase'] ) ) {
			update_post_meta(
				$post_id,
				'exclude_from_individual_seat_purchase',
				sanitize_text_field(
					$post_data['exclude_from_individual_seat_purchase']
				)
			);
		} else {
			delete_post_meta(
				$post_id,
				'exclude_from_individual_seat_purchase'
			);
		}
	}

	public function approve_action_catcher() {
		global $wpdb;
		$assignmentID      = intval( $_POST['assignmentID'] );
		$assignment_points = absint( $_POST['points'] );
		if ( function_exists( 'learndash_approve_assignment' ) ) {
			$assignment_post = get_post( $assignmentID );
			$user_id         = $assignment_post->post_author;

			$lesson_id = intval( get_post_meta( $assignmentID, 'lesson_id', true ) );
			if ( ! empty( $lesson_id ) ) {
				$max_points = learndash_get_setting( $lesson_id, 'lesson_assignment_points_amount' );

				if ( $assignment_points > $max_points ) {
					$assignment_points = $max_points;
				}
				update_post_meta( $assignmentID, 'points', $assignment_points );
			}

			learndash_approve_assignment( $user_id, $lesson_id, $assignmentID );
		}

		wp_die();
	}

	public function admin_enqueue_scripts( $hook ) {
		$asset = require( LEARNDASH_GROUPS_PLUS_DIR . 'build/admin.asset.php' );

		wp_register_style( 'learndash-groups-plus-admin', LEARNDASH_GROUPS_PLUS_URL . 'build/admin.css', array(), $asset['version'] );

		wp_enqueue_style( 'learndash-groups-plus-admin' );

		wp_enqueue_script( 'learndash-groups-plus-admin', LEARNDASH_GROUPS_PLUS_URL . 'build/admin.js', array( 'jquery' ), $asset['version'] );
	}

	/**
	 * Enqueues scripts and styles for the frontend.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function wp_enqueue_scripts() {
		global $post_type;
		global $post;

		$asset = require( LEARNDASH_GROUPS_PLUS_DIR . 'build/frontend.asset.php' );
		$script_dependencies = array(
			'learndash-groups-plus-pair-select',
			'learndash-groups-plus-sweet-alert',
			'learndash-groups-plus-jquery-validate',
			'learndash-groups-plus-select2',
			'wp-i18n',
		);

		$style_dependencies = array(
			'learndash-groups-plus-font-awesome',
			'learndash-groups-plus-select2',
		);

		// if ( is_page_template( 'template-learndash-groups-plus.php' ) ) {
			wp_register_style( 'learndash-groups-plus-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css', array(), '5.15.2' );

			wp_register_script( 'learndash-groups-plus-pair-select', LEARNDASH_GROUPS_PLUS_URL . 'build/library/pair-select.min.js', array(), '1.0', true );

			wp_register_script( 'learndash-groups-plus-sweet-alert', LEARNDASH_GROUPS_PLUS_URL . 'build/library/sweetalert.min.js', array( 'jquery' ), '3.2.2', true );

			wp_register_script( 'learndash-groups-plus-jquery-validate', LEARNDASH_GROUPS_PLUS_URL . 'build/library/jquery.validate.min.js', array(), '1.11.1', true );

			wp_register_style( 'learndash-groups-plus-select2', LEARNDASH_GROUPS_PLUS_URL . 'build/library/select2.min.css', array(), '4.0.13' );
			wp_register_script( 'learndash-groups-plus-select2', LEARNDASH_GROUPS_PLUS_URL . 'build/library/select2.min.js', array( 'jquery' ), '4.0.13', true );

			wp_enqueue_script( 'learndash-groups-plus-frontend', LEARNDASH_GROUPS_PLUS_URL . 'build/frontend.js', $script_dependencies, $asset['version'], true );

			wp_enqueue_style( 'learndash-groups-plus-frontend', LEARNDASH_GROUPS_PLUS_URL . 'build/frontend.css', $style_dependencies, $asset['version'] );

			// Localize the script with new data
			$translation_array = array(
				'ajaxurl'      => admin_url( 'admin-ajax.php' ),
				'translations' => array(
					'placeholder_select_a_team_leaders' => sprintf(
						esc_html__( 'Select %s', 'learndash-groups-plus' ),
						learndash_get_custom_label_lower( 'team_leaders' )
					),
					'placeholder_select_a_team_members' => sprintf(
						esc_html__( 'Select %s', 'learndash-groups-plus' ),
						learndash_get_custom_label_lower( 'team_members' )
					),
					'please_wait_to_load'               => esc_html__( 'Please wait to load...', 'learndash-groups-plus' ),
				),
				'settings'     => [
					'hide_organization_delete_team_leader_trashcan_icon' => get_site_option( 'hide_organization_delete_team_leader_trashcan_icon', 'no' ),
					'hide_organization_delete_team_leader_person_x_icon' => get_site_option( 'hide_organization_delete_team_leader_person_x_icon', 'no' ),
					'hide_organization_delete_groups_plus_icon'          => get_site_option( 'hide_organization_delete_groups_plus_icon', 'no' ),
				],
			);

			wp_localize_script( 'learndash-groups-plus-frontend', 'learndashGroupsPlusFrontend', $translation_array );

			wp_set_script_translations( 'learndash-groups-plus-frontend', 'learndash-groups-plus' );

			if ( ! is_404() && ! is_archive() && in_array( $post_type, array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz' ) ) && is_user_logged_in() ) {
				$course_id = learndash_get_course_id( $post->ID );

				if ( '0' === $course_id || '' === $course_id ) {
					$lesson_id = learndash_get_lesson_id( $post->ID, $course_id );
					if ( '0' === $lesson_id || '' === $lesson_id ) {
						$course_id = 1;
					} else {
						$course_id = learndash_get_course_id( $lesson_id );
					}
				}
				if ( 'sfwd-courses' === $post->post_type ) {
					$course_id = $post->ID;
				}

				// Timer.
				$timer_asset = require( LEARNDASH_GROUPS_PLUS_DIR . 'build/frontend-timer.asset.php' );

				$timer_script_dependencies = array(
					'jquery',
				);

				wp_enqueue_script( 'learndash-groups-plus-frontend-timer', LEARNDASH_GROUPS_PLUS_URL . 'build/frontend-timer.js', $timer_script_dependencies, $timer_asset['version'], true );

				$localize_data_array = array(
					'courseID' => $course_id,
					'postID'   => $post->ID,
				);

				wp_localize_script( 'learndash-groups-plus-frontend-timer', 'learndash_groups_plus_timer', $localize_data_array );
			}

			// }
	}

	/**
	 * Override frontend style using setting values.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function override_frontend_style(): void {
		$assignment_grade_button_bg_color      =  get_site_option('assignment_grade_button_bg_color', '#FF0000');
		$assignment_grade_button_text_color    =  get_site_option('assignment_grade_button_text_color', '#FFFFFF');
		$assignment_resubmit_button_bg_color   =  get_site_option('assignment_resubmit_button_bg_color', '#FFA500');
		$assignment_resubmit_button_text_color =  get_site_option('assignment_resubmit_button_text_color', '#FFFFFF');
		$assignment_approved_button_bg_color   =  get_site_option('assignment_approved_button_bg_color', '#008000');
		$assignment_approved_button_text_color =  get_site_option('assignment_approved_button_text_color', '#FFFFFF');
		$assignment_buttons_border_radius      =  get_site_option('assignment_buttons_border_radius', '20px');
		?>
			<style type="text/css" id="learndash-groups-plus-override-style">
				.div-table-container .assignment-table .div-table-col.div-approve a.btn-learndash-groups-plus-assignment-grade,
				.div-table-container .assignment-table .div-table-col.div-approve a.btn-learndash-groups-plus-assignment-grade:focus {
					background: <?php echo $assignment_grade_button_bg_color; ?>;
					border-radius: <?php echo $assignment_buttons_border_radius; ?>;
					color: <?php echo $assignment_grade_button_text_color; ?>;
				}
				.div-table-container .assignment-table .div-table-col.div-approve a.btn-learndash-groups-plus-assignment-resubmit,
				.div-table-container .assignment-table .div-table-col.div-approve a.btn-learndash-groups-plus-assignment-resubmit:focus {
					background: <?php echo $assignment_resubmit_button_bg_color; ?>;
					border-radius: <?php echo $assignment_buttons_border_radius; ?>;
					color: <?php echo $assignment_resubmit_button_text_color; ?>;
				}
				.div-table-container .assignment-table .div-table-col.div-approve a.btn-learndash-groups-plus-assignment-approved,
				.div-table-container .assignment-table .div-table-col.div-approve a.btn-learndash-groups-plus-assignment-approved:focus {
					background: <?php echo $assignment_approved_button_bg_color; ?>;
					border-radius: <?php echo $assignment_buttons_border_radius; ?>;
					color: <?php echo $assignment_approved_button_text_color; ?>;
				}
			</style>
		<?php
	}

	public function menu() {
		add_submenu_page( 'learndash-lms', 'LearnDash LMS - Groups Plus', 'Groups Plus', 'manage_options', 'learndash-groups-plus-settings', array( $this, 'settings' ), 8 );
	}

	/**
	 * Settings Page Output.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function settings() {
		$setting_tabs = array(
			'general' => esc_html__( 'General', 'learndash-groups-plus' ),
			'sett'    => esc_html__( 'Settings', 'learndash-groups-plus' ),
			'email'   => esc_html__( 'Global Email', 'learndash-groups-plus' ),
			'design'  => esc_html__( 'Design', 'learndash-groups-plus' ),
			'hooks'   => esc_html__( 'Developer Resources', 'learndash-groups-plus' ),
		);

		$current_tab = ( isset( $_GET['tab'] ) ) ? sanitize_key( $_GET['tab'] ) : 'general';
		?>
			<h2 class="nav-tab-wrapper">
				<?php
				foreach ( $setting_tabs as $name => $label ) {
					echo '<a href="' . esc_url( admin_url( 'admin.php?page=learndash-groups-plus-settings&tab=' . $name ) ) . '" class="nav-tab ' . ( $current_tab == $name ? esc_attr( 'nav-tab-active' ) : '' ) . '">' . esc_html( $label ) . '</a>';
				}
				?>
			</h2>
		<?php
		foreach ( array_keys( $setting_tabs ) as $setting_tab_key ) {
			switch ( $setting_tab_key ) {
				case $current_tab:
					do_action( '' . $setting_tab_key . '_setting_save_field' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Pre-existing hook name in the plugin.
					do_action( '' . $setting_tab_key . '_setting' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
					do_action( '' . $setting_tab_key . '_create_setting' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
					break;
			}
		}
	}

	/**
	 * AJAX handler to manage organizations' add team action.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action doesn't pass security or permission checks.
	 *
	 * @return void
	 */
	public function action_manage_organization_add_team() {
		$error   = null;
		$success = false;

		try {
			$data = array();
			parse_str( $_POST['data'], $data );
			$data = stripslashes_deep( $data );

			if ( ! isset( $data['nonce'] ) || empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'learndash-groups-plus-manage-organization-add-team' ) ) {
				throw new Exception( 'Security issue' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$groups_plus_group_name = sanitize_text_field( $data['groups_plus_group_name'] );

			$groups_plus_team_leader_firstname = sanitize_text_field( $data['groups_plus_team_leader_firstname'] );
			$groups_plus_team_leader_lastname  = sanitize_text_field( $data['groups_plus_team_leader_lastname'] );
			$groups_plus_team_leader_username  = sanitize_text_field( $data['groups_plus_team_leader_username'] );
			$groups_plus_team_leader_email     = sanitize_email( $data['groups_plus_team_leader_email'] );
			$groups_plus_team_leader_password  = sanitize_text_field( $data['groups_plus_team_leader_password'] );
			$primary_group_id              = sanitize_text_field( $data['primary_group_id'] );

			$has_team_leader_exists      = $data['has_team_leader_exists'] ?? null;
			$groups_plus_team_leader_ids = $data['groups_plus_team_leader_ids'] ?? array();
			$courses                 = $data['courses'] ?? array();
			$group_key               = sanitize_text_field( $data['group'] );
			$group_id                = Cast::to_int( general_encrypt_decrypt( 'decrypt', $group_key ) );

			if ( ! is_numeric( $group_id ) ) {
				throw new Exception( esc_html__( 'Invalid group', 'learndash-groups-plus' ) );
			}
			if ( ! $groups_plus_group_name ) {
				// Translators: %s: team label.
				throw new Exception( sprintf( esc_html__( '%s name is required', 'learndash-groups-plus' ), learndash_get_custom_label( 'team' ) ) );
			}

			if ( ! isset( $has_team_leader_exists ) && ! empty( $groups_plus_team_leader_username ) ) {
				// $groups_plus_team_leader_username = groups_plus_clear_key($groups_plus_team_leader_username);
				if ( username_exists( $groups_plus_team_leader_username ) ) {
					// Translators: %s: username.
					throw new Exception( sprintf( esc_html__( 'Username %s exists', 'learndash-groups-plus' ), $groups_plus_team_leader_username ) );
				}
			}

			if ( ! isset( $has_team_leader_exists ) && ! empty( $groups_plus_team_leader_ids ) ) {
				throw new Exception(
					sprintf(
						// Translators: %1$s: team leader label, %2$s: team leader label.
						esc_html__( 'You must check %1$s already exists option if you want to set existing user as %2$s.', 'learndash-groups-plus' ),
						learndash_get_custom_label_lower( 'team_leader' ),
						learndash_get_custom_label_lower( 'team_leader' ),
					)
				);
			}

			if ( isset( $has_team_leader_exists ) && empty( $groups_plus_team_leader_ids ) ) {
				throw new Exception(
					sprintf(
						// Translators: %1$s: team leader label, %2$s: team label.
						esc_html__( 'You must set existing %1$s for the %2$s.', 'learndash-groups-plus' ),
						learndash_get_custom_label_lower( 'team_leader' ),
						learndash_get_custom_label_lower( 'team' ),
					)
				);
			}

			if ( ! isset( $has_team_leader_exists ) ) {
				if ( empty( $groups_plus_team_leader_username ) ) {
					throw new Exception( esc_html__( 'Username field is required.', 'learndash-groups-plus' ) );
				}

				if ( empty( $groups_plus_team_leader_password ) ) {
					throw new Exception( esc_html__( 'Password field is required.', 'learndash-groups-plus' ) );
				}

				if ( empty( $groups_plus_team_leader_email ) ) {
					throw new Exception( esc_html__( 'Email field is required and to be in correct format.', 'learndash-groups-plus' ) );
				}

				if ( empty( $groups_plus_team_leader_firstname ) ) {
					throw new Exception( esc_html__( 'First name field is required.', 'learndash-groups-plus' ) );
				}
			}

			$current_user_id = get_current_user_id();
			$groups_id       = learndash_get_administrators_group_ids( $current_user_id );
			$parent_group_id = $primary_group_id;
			if ( $groups_id ) {
				// Get parent group
				$args         = array(
					'numberposts' => 1,
					'post_type'   => 'groups',
					'post__in'    => $groups_id,
					'orderby'     => 'menu_order',
					'order'       => 'DESC',
					'post_parent' => 0,
				);
				$parent_group = get_posts( $args );
				// $parent_group_id = $parent_group[0]->ID;
			}
			$group = array(
				'post_type'   => 'groups',
				'post_status' => 'publish',
				'post_title'  => $groups_plus_group_name,
				'post_parent' => Cast::to_int( $parent_group_id ),
			);
			if ( $group_id == 0 ) {
				$group_id = wp_insert_post( $group );

				// hook
				do_action( 'create_groups_plus', $group_id, $current_user_id );
			}
			if ( $courses ) {
				learndash_set_group_enrolled_courses( $group_id, $courses );
			} else {
				learndash_set_group_enrolled_courses( $group_id, array() );
			}
			if ( $group_id > 0 ) {
				$group['ID'] = $group_id;
				wp_update_post( $group );
			}

			// create team team leader
			$userdata = array();

			$groups_plus_team_leader_ids = ! empty( $groups_plus_team_leader_ids ) ? $groups_plus_team_leader_ids : array();
			if ( $has_team_leader_exists ) {
				$team_leader_user_ids = $groups_plus_team_leader_ids;
			} else {
				$userdata        = array(
					'user_login'   => $groups_plus_team_leader_username,
					'user_pass'    => $groups_plus_team_leader_password,
					'user_email'   => $groups_plus_team_leader_email,
					'display_name' => trim( $groups_plus_team_leader_firstname . ' ' . $groups_plus_team_leader_lastname ),
					'first_name'   => $groups_plus_team_leader_firstname,
					'last_name'    => $groups_plus_team_leader_lastname,
				);
				$team_leader_user_id = Cast::to_int( wp_insert_user( $userdata ) );
				wp_update_user(
					array(
						'ID'   => $team_leader_user_id,
						'role' => 'group_leader',
					)
				);
				$team_leader_user_ids = array( $team_leader_user_id );

				// hook
				do_action( 'create_team_leader', $team_leader_user_id, $userdata );
			}

			if ( ! empty( $group_id ) && ! empty( $team_leader_user_ids ) ) {
				learndash_set_groups_administrators( $group_id, $team_leader_user_ids );
			}

			// Send Welcome mail to team leader
			foreach ( $team_leader_user_ids as $team_leader_user_id ) {
				$team_leader = get_user_by( 'ID', $team_leader_user_id );
				if ( ! empty( $team_leader->user_email ) ) {
					$email = new Welcome( Cast::to_int( $team_leader_user_id ), $group_id, $userdata, 'team_leader' );
					$email->send();
				}
			}

			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	/**
	 * AJAX handler to manage teams' courses action.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action doesn't pass security or permission checks.
	 *
	 * @return void
	 */
	public function action_manage_team_courses() {
		$error   = null;
		$success = false;
		try {
			$data = array();
			parse_str( $_POST['data'], $data );
			$data = stripslashes_deep( $data );

			if ( ! isset( $data['nonce'] ) || empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'learndash-groups-plus-manage-team-courses' ) ) {
				throw new Exception( 'Security issue' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$courses   = $data['courses'] ?? array();
			$group_key = sanitize_text_field( $data['group'] );
			$group_id  = general_encrypt_decrypt( 'decrypt', $group_key );

			if ( ! is_numeric( $group_id ) ) {
				throw new Exception( 'Invalid Group' );
			}

			learndash_set_group_enrolled_courses( $group_id, $courses );

			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	/**
	 * AJAX handler to manage teams' team leaders action.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action doesn't pass security or permission checks.
	 *
	 * @return void
	 */
	public function action_manage_team_team_leaders() {
		$error   = null;
		$success = false;
		try {
			$data = array();
			parse_str( $_POST['data'], $data );
			$data = stripslashes_deep( $data );

			if ( ! isset( $data['nonce'] ) || empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'learndash-groups-plus-manage-team-team-leaders' ) ) {
				throw new Exception( 'Security issue' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$group_key = sanitize_text_field( $data['group'] );
			$group_id  = general_encrypt_decrypt( 'decrypt', $group_key );

			if ( ! is_numeric( $group_id ) ) {
				throw new Exception( 'Invalid Group' );
			}

			// learndash_set_group_enrolled_courses( $group_id, $courses);

			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	/**
	 * AJAX handler to manage teams' add team leader action.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action doesn't pass security or permission checks.
	 *
	 * @return void
	 */
	public function action_manage_team_add_team_leader() {
		$error   = null;
		$success = false;
		try {
			$data = array();
			parse_str( $_POST['data'], $data );
			$data = stripslashes_deep( $data );

			if ( ! isset( $data['nonce'] ) || empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'learndash-groups-plus-manage-team-add-team-leader' ) ) {
				throw new Exception( 'Security issue' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$groups_plus_team_leader_firstname = sanitize_text_field( $data['groups_plus_team_leader_firstname'] );
			$groups_plus_team_leader_lastname  = sanitize_text_field( $data['groups_plus_team_leader_lastname'] );
			$groups_plus_team_leader_username  = sanitize_text_field( $data['groups_plus_team_leader_username'] );
			$groups_plus_team_leader_email     = sanitize_email( $data['groups_plus_team_leader_email'] );
			$groups_plus_team_leader_password  = sanitize_text_field( $data['groups_plus_team_leader_password'] );

			$has_team_leader_exists      = $data['has_team_leader_exists'] ?? null;
			$groups_plus_team_leader_ids = $data['groups_plus_team_leader_ids'] ?? array();

			$group_key = sanitize_text_field( $data['group'] );
			$group_id  = general_encrypt_decrypt( 'decrypt', $group_key );

			if ( ! is_numeric( $group_id ) ) {
				throw new Exception( 'Invalid Group' );
			}

			$group_id = Cast::to_int( $group_id );

			if ( ! isset( $has_team_leader_exists ) && ! empty( $groups_plus_team_leader_ids ) ) {
				throw new Exception(
					sprintf(
						// Translators: %1$s: team leader label, %2$s: team leader label.
						esc_html__( 'You must check %1$s already exists option if you want to set existing user as %2$s.', 'learndash-groups-plus' ),
						learndash_get_custom_label_lower( 'team_leader' ),
						learndash_get_custom_label_lower( 'team_leader' ),
					)
				);
			}

			if ( isset( $has_team_leader_exists ) && empty( $groups_plus_team_leader_ids ) ) {
				throw new Exception(
					sprintf(
						// Translators: %1$s: team leader label, %2$s: team label.
						esc_html__( 'You must set existing %1$s for the %2$s.', 'learndash-groups-plus' ),
						learndash_get_custom_label_lower( 'team_leader' ),
						learndash_get_custom_label_lower( 'team' ),
					)
				);
			}

			if ( ! isset( $has_team_leader_exists ) && ! empty( $groups_plus_team_leader_username ) ) {
				// $groups_plus_team_leader_username = groups_plus_clear_key($groups_plus_team_leader_username);
				if ( username_exists( $groups_plus_team_leader_username ) ) {
					throw new Exception( 'Username ' . $groups_plus_team_leader_username . ' exists' );
				}
			}

			if ( ! isset( $has_team_leader_exists ) ) {
				if ( empty( $groups_plus_team_leader_username ) ) {
					throw new Exception( esc_html__( 'Username field is required.', 'learndash-groups-plus' ) );
				}

				if ( empty( $groups_plus_team_leader_password ) ) {
					throw new Exception( esc_html__( 'Password field is required.', 'learndash-groups-plus' ) );
				}

				if ( empty( $groups_plus_team_leader_email ) ) {
					throw new Exception( esc_html__( 'Email field is required and to be in correct format.', 'learndash-groups-plus' ) );
				}

				if ( empty( $groups_plus_team_leader_firstname ) ) {
					throw new Exception( esc_html__( 'First name field is required.', 'learndash-groups-plus' ) );
				}
			}

			$team_leader_user_ids = learndash_get_groups_administrator_ids( $group_id );

			// create team team leader
			$userdata = array();
			if ( $has_team_leader_exists && ! empty( $groups_plus_team_leader_ids ) ) {
				$team_leader_user_ids = array_merge( $team_leader_user_ids, $groups_plus_team_leader_ids );
			} elseif ( ! isset( $has_team_leader_exists ) ) {
				$userdata        = array(
					'user_login'   => $groups_plus_team_leader_username,
					'user_pass'    => $groups_plus_team_leader_password,
					'user_email'   => $groups_plus_team_leader_email,
					'display_name' => trim( $groups_plus_team_leader_firstname . ' ' . $groups_plus_team_leader_lastname ),
					'first_name'   => $groups_plus_team_leader_firstname,
					'last_name'    => $groups_plus_team_leader_lastname,
				);
				$team_leader_user_id = wp_insert_user( $userdata );
				wp_update_user(
					array(
						'ID'   => $team_leader_user_id,
						'role' => 'group_leader',
					)
				);
				$team_leader_user_ids[] = $team_leader_user_id;

				$groups_plus_team_leader_ids[] = $team_leader_user_id;

				do_action( 'create_team_leader', $team_leader_user_id, $userdata );
			}

			if ( ! empty( $group_id ) && ! empty( $team_leader_user_ids ) ) {
				learndash_set_groups_administrators( $group_id, $team_leader_user_ids );
			}

			// Send Welcome mail to team leader
			foreach ( $groups_plus_team_leader_ids as $team_leader_user_id ) {
				$team_leader = get_user_by( 'ID', $team_leader_user_id );
				if ( ! empty( $team_leader->user_email ) ) {
					$email = new Welcome( $team_leader_user_id, $group_id, $userdata, 'team_leader' );
					$email->send();
				}

				// Hook for Buddy boss user sync
				do_action( 'bb_add_group_leader', $group_id, $team_leader_user_id );
			}
			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	/**
	 * AJAX handler to manage teams' add team leader as team member action.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action doesn't pass security or permission checks.
	 *
	 * @return void
	 */
	public function action_manage_team_add_team_leader_as_team_member() {
		$error   = null;
		$success = false;
		try {
			$data = array();
			parse_str( $_POST['data'], $data );
			$data = stripslashes_deep( $data );

			if ( ! isset( $data['nonce'] ) || empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'learndash-groups-plus-manage-team-add-team-leader-as-team-member' ) ) {
				throw new Exception( 'Security issue' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$groups_plus_team_leader_ids = $data['groups_plus_team_leader_ids'] ?? array();

			$group_key = sanitize_text_field( $data['group'] );
			$group_id  = general_encrypt_decrypt( 'decrypt', $group_key );

			if ( ! is_numeric( $group_id ) ) {
				throw new Exception( 'Invalid Group' );
			}

			$current_user_id = get_current_user_id();

			$team_member_user_ids = learndash_get_groups_user_ids( $group_id );

			if ( ! empty( $groups_plus_team_leader_ids ) ) {
				$team_member_user_ids = array_merge( $team_member_user_ids, $groups_plus_team_leader_ids );
			}

			learndash_set_groups_users( $group_id, $team_member_user_ids );

			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	/**
	 * AJAX handler for "action_manage_team_delete_team_leader" action.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action is not valid.
	 *
	 * @return void
	 */
	public function action_manage_team_delete_team_leader() {
		$error   = null;
		$success = false;
		try {
			$data = array();
			parse_str( $_POST['data'], $data );
			$data = stripslashes_deep( $data );

			if ( ! isset( $data['nonce'] ) || empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'learndash-groups-plus-manage-team-delete-team-leader' ) ) {
				throw new Exception( 'Security issue' );
			}

			// Check if the option to "hide the delete team leader from groups plus/organization button" is enabled.
			if ( get_site_option( 'hide_organization_delete_team_leader_trashcan_icon', 'no' ) === 'yes' ) {
				throw new Exception( 'You are not allowed to do this action' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$group_key = sanitize_text_field( $data['group'] );
			$user_id   = sanitize_text_field( $data['user_id'] );
			$group_id  = general_encrypt_decrypt( 'decrypt', $group_key );

			$team_leader_user_ids = learndash_get_groups_administrator_ids( $group_id );

			if ( ( $key = array_search( $user_id, $team_leader_user_ids ) ) !== false ) {
				unset( $team_leader_user_ids[ $key ] );

				// Hook for Buddy boss user sync
				do_action( 'bb_remove_group_leader', $group_id, $user_id );
			}

			learndash_set_groups_administrators( $group_id, $team_leader_user_ids );

			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	/**
	 * AJAX handler for "action_manage_team_delete_team_leader_permanently" action.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action is not valid.
	 *
	 * @return void
	 */
	public function action_manage_team_delete_team_leader_permanently() {
		$error   = null;
		$success = false;
		try {
			$data = array();
			parse_str( $_POST['data'], $data );
			$data = stripslashes_deep( $data );

			if ( ! isset( $data['nonce'] ) || empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'learndash-groups-plus-manage-team-delete-team-leader' ) ) {
				throw new Exception( 'Security issue' );
			}

			// Check if the option to "hide the delete team leader from system button" is enabled.
			if ( get_site_option( 'hide_organization_delete_team_leader_person_x_icon', 'no' ) === 'yes' ) {
				throw new Exception( 'You are not allowed to do this action' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$group_key = sanitize_text_field( $data['group'] );
			$user_id   = sanitize_text_field( $data['user_id'] );
			$group_id  = general_encrypt_decrypt( 'decrypt', $group_key );

			if ( ! Group::can_edit_user( $user_id, get_current_user_id() ) ) {
				throw new Exception( __( "You don't have permission for this", 'learndash-groups-plus' ) );
			}

			$team_leader_user_ids = learndash_get_groups_administrator_ids( $group_id );

			if ( ( $key = array_search( $user_id, $team_leader_user_ids ) ) !== false ) {
				unset( $team_leader_user_ids[ $key ] );

				// Hook for Buddy boss user sync
				do_action( 'bb_remove_group_leader', $group_id, $user_id );
			}

			learndash_set_groups_administrators( $group_id, $team_leader_user_ids );

			wp_delete_user( $user_id );

			// hook
			do_action( 'delete_team_leader', $user_id );

			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	/**
	 * AJAX handler to manage teams' delete team leader from team member action.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action is not valid.
	 *
	 * @return void
	 */
	public function action_manage_team_delete_team_leader_from_team_member() {
		$error   = null;
		$success = false;
		try {
			$data = array();
			parse_str( $_POST['data'], $data );
			$data = stripslashes_deep( $data );

			if ( ! isset( $data['nonce'] ) || empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'learndash-groups-plus-manage-team-delete-team-leader-from-team-member' ) ) {
				throw new Exception( 'Security issue' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$group_key = sanitize_text_field( $data['group'] );
			$user_id   = sanitize_text_field( $data['user_id'] );
			$group_id  = general_encrypt_decrypt( 'decrypt', $group_key );

			$team_member_user_ids = learndash_get_groups_user_ids( $group_id );

			if ( ( $key = array_search( $user_id, $team_member_user_ids ) ) !== false ) {
				unset( $team_member_user_ids[ $key ] );

				// Hook for Buddy boss user sync
				do_action( 'bb_remove_group_leader', $group_id, $user_id );
			}

			learndash_set_groups_users( $group_id, $team_member_user_ids );

			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	/**
	 * AJAX handler to manage organizations' delete team action.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action is not valid.
	 *
	 * @return void
	 */
	public function action_manage_organization_delete_team() {
		$error   = null;
		$success = false;
		try {
			$data = array();
			parse_str( $_POST['data'], $data );
			$data = stripslashes_deep( $data );

			if ( ! isset( $data['nonce'] ) || empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'learndash-groups-plus-manage-organization-delete-team' ) ) {
				throw new Exception( 'Security issue' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$group_id = sanitize_text_field( $data['group_id'] );
			// $user_id = sanitize_text_field($data['user_id']);
			$group_id       = general_encrypt_decrypt( 'decrypt', $group_id );
			$group_team_members = Group::get_group_users( $group_id );
			if ( $group_team_members ) {
				throw new Exception( 'Please delete team members before delete the group' );
			} else {
				wp_delete_post( $group_id );

				// hook
				do_action( 'delete_groups_plus', $group_id );
			}

			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	/**
	 * AJAX handler to fetch organizations' report.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action is not valid.
	 *
	 * @return void
	 */
	public function action_fetch_organization_report() {
		$error   = null;
		$success = false;
		try {

			if ( ! isset( $_POST['nonce'] ) || empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'learndash-groups-plus-fetch-team-report' ) ) {
				throw new Exception( 'Security issue' );
			}
			$groups_plus_courses_users = array();
			$return_html               = '';
			if ( isset( $_POST['export_csv'] ) && $_POST['export_csv'] == 'true' ) {
				$export_csv = true;
			} else {
				$export_csv = false;
			}

			if ( learndash_is_groups_hierarchical_enabled() ) {
				$design_setting = get_site_option( 'design_setting' );
				if ( empty( $design_setting['color_codes']['not_start'] ) ) {
					$design_setting['color_codes']['not_start'] = '#FF0000';
				}
				if ( empty( $design_setting['color_codes']['in_progress'] ) ) {
					$design_setting['color_codes']['in_progress'] = '#FFA500';
				}
				if ( empty( $design_setting['color_codes']['completed'] ) ) {
					$design_setting['color_codes']['completed'] = '#008000';
				}

				$parent_group_id = intval( sanitize_text_field( $_POST['parent_group_id'] ) );
				$group_ids       = learndash_get_administrators_group_ids( Group::get_group_leader_id_of_group( $parent_group_id ) );
				$groups          = array();
				$return_result   = array();

				$child_groups = Group::get_child_groups( $parent_group_id );

				foreach ( $child_groups as $child_group ) {
					$single_class = array(
						'class_id'      => $child_group->ID,
						'class_name'    => $child_group->post_title,
						'class_courses' => array(),
					);

					$courses_data = array();
					$courses      = learndash_group_enrolled_courses( $child_group->ID );

					if ( ! empty( $courses ) ) {
						$course_orderby = get_site_option( 'course_orderby' ) ?? 'title';
						$course_order   = get_site_option( 'course_order' ) ?? 'ASC';

						$args    = array(
							'numberposts' => -1,
							'post_type'   => 'sfwd-courses',
							'orderby'     => $course_orderby,
							'post__in'    => $courses,
							'fields'      => 'ids',
							'order'       => $course_order,
						);
						$courses = get_posts( $args );
					}

					foreach ( $courses as $course ) {
						$course_title  = get_the_title( $course );
						$single_course = array(
							'course_id'    => $course,
							'course_name'  => $course_title,
							'avg'          => 0.00,
							'status_color' => $design_setting['color_codes']['not_start'],
							'team_members'     => array(),
						);
						$users_data    = array();

						$group_users    = Group::get_group_users( $child_group->ID );
						$percentage_sum = $course_avg = 0;

						$group_users_html = '';
						foreach ( $group_users as $group_user ) {
							$progress  = learndash_course_progress(
								array(
									'user_id'   => $group_user->data->ID,
									'course_id' => $course,
									'array'     => true,
								)
							);
							$user_info = get_userdata( $group_user->data->ID );

							if ( $progress['percentage'] == 100 ) {
								$user_row_color = $design_setting['color_codes']['completed'];
							} elseif ( $progress['percentage'] > 0 ) {
								$user_row_color = $design_setting['color_codes']['in_progress'];
							} else {
								$user_row_color = $design_setting['color_codes']['not_start'];
							}
							$percentage_sum      = $percentage_sum + $progress['percentage'];
							$course_completed_on = get_user_meta( $group_user->data->ID, 'course_completed_' . $course, true );
							// error_log("course_completed_on--->" . $course_completed_on)   ;
							if ( empty( $course_completed_on ) ) {
								$course_completed_on = '';
							} else {
								$course_completed_on = gmdate( 'm/d/Y H:i:s', $course_completed_on );
							}
							$single_course['team_members'][] = array(
								'user_id'             => $group_user->data->ID,
								'first_name'          => $user_info->first_name,
								'last_name'           => $user_info->last_name,
								'user_name'           => $user_info->user_login,
								'email'               => $user_info->user_email,
								'status_color'        => $user_row_color,
								'percentage'          => $progress['percentage'],
								'progress'            => $progress,
								'course_completed_on' => $course_completed_on,
							);
						}

						if ( count( $group_users ) ) {
							$course_avg           = number_format( ( $percentage_sum / count( $group_users ) ) );
							$single_course['avg'] = $course_avg;
						}

						if ( $course_avg == 100 ) {
							$course_row_color = $design_setting['color_codes']['completed'];
						} elseif ( $course_avg > 0 ) {
							$course_row_color = $design_setting['color_codes']['in_progress'];
						} else {
							$course_row_color = $design_setting['color_codes']['not_start'];
						}
						$single_course['status_color']   = $course_row_color;
						$single_class['class_courses'][] = $single_course;

					}

					$return_result[] = $single_class;
				}

				if ( $export_csv === false ) {
					if ( empty( $return_result ) ) {
						$return_html     .= '<div class="div-table-row-header">';
							$return_html .= '<div class="div-table-col" align="left">' . esc_html__( 'No record found.', 'learndash-groups-plus' ) . '</div>';
							$return_html .= '<div class="div-table-col" align="center"></div>';
							$return_html .= '<div class="div-table-col" align="right"></div>';
						$return_html     .= '</div>';
					}

					foreach ( $return_result as $single_class_row ) {
						$return_html .= '<div class="div-table-row-header div-table-row-team-name-header">';
						$return_html .= '<div class="div-table-col" align="center">&nbsp;</div>';
						$return_html .= '<div class="div-table-col" align="center">' . $single_class_row['class_name'] . '</div>';
						$return_html .= '<div class="div-table-col" align="right"><i class="fa fa-plus" aria-hidden="true"></i></div>';
						$return_html .= '</div>';

						$return_html .= '<div class="courses-content">';

						$return_html     .= '<div class="div-table-row-header div-table-row-sub-header">';
							$return_html .= '<div class="div-table-col" align="center">' . sprintf( esc_html( '%s' ), esc_attr( \LearnDash_Custom_Label::get_label( 'course' ) ) ) . '</div>';
							$return_html .= '<div class="div-table-col" align="center">' . sprintf( esc_html__( '%s Name', 'learndash-groups-plus' ), learndash_get_custom_label( 'team_member' ) ) . '</div>';
							$return_html .= '<div class="div-table-col" align="center">' . esc_html__( 'Completion', 'learndash-groups-plus' ) . '</div>';
							$return_html .= '<div class="learndash-groups-plus-arrow-spacer" aria-hidden="true"></div>';
						$return_html     .= '</div>';

						foreach ( $single_class_row['class_courses'] as $single_course_row ) {

							$group_users_html = '';
							foreach ( $single_course_row['team_members'] as $single_team_member_row ) {
								$group_users_html     .= '<div class="div-table-row" style="color:' . $single_team_member_row['status_color'] . '">';
									$group_users_html .= '<div class="div-table-col" align="center">' . $single_course_row['course_name'] . '</div>';
									$group_users_html .= '<div class="div-table-col" align="center">' . $single_team_member_row['first_name'] . ' ' . $single_team_member_row['last_name'] . ' (' . $single_team_member_row['user_name'] . ')</div>';
									$group_users_html .= '<div class="div-table-col" align="center">' . ' (' . $single_team_member_row['progress']['completed'] . '/' . $single_team_member_row['progress']['total'] . ') ' . sprintf( '%s%%', $single_team_member_row['progress']['percentage'] ) . '</div>';
									$group_users_html .= '<div class="learndash-groups-plus-arrow-spacer" aria-hidden="true"></div>';
								$group_users_html     .= '</div>';
							}
							$return_html         .= '<div class="div-table-row-parent">';
								$return_html     .= '<div class="div-table-row-header">';
									$return_html .= '<div class="div-table-col" align="center" style="color:' . $single_course_row['status_color'] . '"><strong>' . $single_course_row['course_name'] . '</strong></div>';
									$return_html .= '<div class="div-table-col" align="center" style="color:' . $single_course_row['status_color'] . '">&nbsp;</div>';
									$return_html .= '<div class="div-table-col" align="center" style="color:' . $single_course_row['status_color'] . '"><strong>' . sprintf(
										// translators: team average percentage.
										esc_html__( '%1$s Avg: %2$s%%', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'team' ),
										$single_course_row['avg']
									) . '</strong></div>';
									$return_html .= '<span class="learndash-groups-plus-icon-arrow" style="color:' . $single_course_row['status_color'] . '; display:' . ( empty( $group_users_html ) ? 'none' : 'block' ) . '; "></span>';
								$return_html     .= '</div>';
								$return_html     .= '<div class="courses-users-content">';
									$return_html .= $group_users_html;
								$return_html     .= '</div>';
							$return_html         .= '</div>';
						}
						$return_html .= '</div>';
					}
					echo $return_html;
				} else {
					$csv_rows = array();
					foreach ( $return_result as $single_class_row ) {
						// if(count($single_class_row['class_courses'])){
						foreach ( $single_class_row['class_courses'] as $single_course_row ) {
							// if(count($single_course_row['team_members'])){
							foreach ( $single_course_row['team_members'] as $single_team_member_row ) {
								$csv_row    = array(
									'class_name'          => $single_class_row['class_name'],
									'course_name'         => $single_course_row['course_name'],
									'course_avg'          => $single_course_row['avg'] . '%',
									'first_name'          => $single_team_member_row['first_name'],
									'last_name'           => $single_team_member_row['last_name'],
									'user_name'           => $single_team_member_row['user_name'],
									'email'               => $single_team_member_row['email'],
									'steps'               => $single_team_member_row['progress']['completed'] . '/' . $single_team_member_row['progress']['total'],
									'course_completed_on' => $single_team_member_row['course_completed_on'],
									'percentage'          => $single_team_member_row['progress']['percentage'] . '%',
								);
								$csv_rows[] = $csv_row;
							}
								// }
						}
						// }
					}

					$fp         = fopen( 'php://output', 'wb' );
					$csv_header = array(
						'Team Name',
						'Course Name',
						'Team Avg',
						'First Name',
						'Last Name',
						'Username',
						'Email',
						'Completed Steps',
						'Completion Date',
						'Completion Percentage',
					);
					fputcsv( $fp, $csv_header );
					foreach ( $csv_rows as $csv_row ) {
						// $val = explode(",", $line);
						fputcsv( $fp, $csv_row );
					}
					fclose( $fp );
					exit();
					return;
				}
			}
			// print_r($return_result);

			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		wp_die();
	}

	/**
	 * AJAX handler to fetch team report.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action is not valid.
	 *
	 * @return void
	 */
	public function action_fetch_team_report() {
		try {
			if ( get_site_option( 'hide_groups_plus_export_csv_button', 'no' ) === 'yes' ) {
				wp_send_json_error(
					array(
						'errors' => __( 'This feature has been disabled', 'learndash-groups-plus' ),
					)
				);
			}

			if ( ! isset( $_POST['nonce'] ) || empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'learndash-groups-plus-team-member-list-table' ) ) {
				wp_send_json_error(
					array(
						'errors' => __( 'Security issue', 'learndash-groups-plus' ),
					)
				);
			}
			$groups_plus_courses_users = array();
			$return_html               = '';

			$export_csv = true;

			if ( learndash_is_groups_hierarchical_enabled() ) {
				$design_setting = get_site_option( 'design_setting' );
				if ( empty( $design_setting['color_codes']['not_start'] ) ) {
					$design_setting['color_codes']['not_start'] = '#FF0000';
				}
				if ( empty( $design_setting['color_codes']['in_progress'] ) ) {
					$design_setting['color_codes']['in_progress'] = '#FFA500';
				}
				if ( empty( $design_setting['color_codes']['completed'] ) ) {
					$design_setting['color_codes']['completed'] = '#008000';
				}

				$group_key = sanitize_text_field( $_POST['group_key'] );
				$group_id  = general_encrypt_decrypt( 'decrypt', $group_key );
				$groups        = array();
				$return_result = array();

				$child_groups = array( $group_id );
				foreach ( $child_groups as $child_group ) {
					$single_class = array(
						'class_id'      => $child_group,
						'class_name'    => get_the_title( $child_group ),
						'class_courses' => array(),
					);

					$courses_data = array();
					$courses      = learndash_group_enrolled_courses( $child_group );

					foreach ( $courses as $course ) {
						$course_title  = get_the_title( $course );
						$single_course = array(
							'course_id'    => $course,
							'course_name'  => $course_title,
							'avg'          => 0.00,
							'status_color' => $design_setting['color_codes']['not_start'],
							'team_members'     => array(),
						);
						$users_data    = array();

						$group_users    = Group::get_group_users( $child_group );
						$percentage_sum = $course_avg = 0;

						$group_users_html = '';
						foreach ( $group_users as $group_user ) {
							$progress  = learndash_course_progress(
								array(
									'user_id'   => $group_user->data->ID,
									'course_id' => $course,
									'array'     => true,
								)
							);
							$user_info = get_userdata( $group_user->data->ID );

							if ( $progress['percentage'] == 100 ) {
								$user_row_color = $design_setting['color_codes']['completed'];
							} elseif ( $progress['percentage'] > 0 ) {
								$user_row_color = $design_setting['color_codes']['in_progress'];
							} else {
								$user_row_color = $design_setting['color_codes']['not_start'];
							}
							$percentage_sum      = $percentage_sum + $progress['percentage'];
							$course_completed_on = get_user_meta( $group_user->data->ID, 'course_completed_' . $course, true );
							// error_log("course_completed_on--->" . $course_completed_on)   ;
							if ( empty( $course_completed_on ) ) {
								$course_completed_on = '';
							} else {
								$course_completed_on = gmdate( 'm/d/Y H:i:s', $course_completed_on );
							}
							$single_course['team_members'][] = array(
								'user_id'             => $group_user->data->ID,
								'first_name'          => $user_info->first_name,
								'last_name'           => $user_info->last_name,
								'user_name'           => $user_info->user_login,
								'email'               => $user_info->user_email,
								'status_color'        => $user_row_color,
								'percentage'          => $progress['percentage'],
								'progress'            => $progress,
								'course_completed_on' => $course_completed_on,
							);
						}

						if ( count( $group_users ) ) {
							$course_avg           = number_format( ( $percentage_sum / count( $group_users ) ) );
							$single_course['avg'] = $course_avg;
						}

						if ( $course_avg == 100 ) {
							$course_row_color = $design_setting['color_codes']['completed'];
						} elseif ( $course_avg > 0 ) {
							$course_row_color = $design_setting['color_codes']['in_progress'];
						} else {
							$course_row_color = $design_setting['color_codes']['not_start'];
						}
						$single_course['status_color']   = $course_row_color;
						$single_class['class_courses'][] = $single_course;

					}

					$return_result[] = $single_class;
				}

				$csv_rows = array();
				foreach ( $return_result as $single_class_row ) {
					// if(count($single_class_row['class_courses'])){
					foreach ( $single_class_row['class_courses'] as $single_course_row ) {
						// if(count($single_course_row['team_members'])){
						foreach ( $single_course_row['team_members'] as $single_team_member_row ) {
							$csv_row    = array(
								'class_name'          => $single_class_row['class_name'],
								'course_name'         => $single_course_row['course_name'],
								'course_avg'          => $single_course_row['avg'] . '%',
								'first_name'          => $single_team_member_row['first_name'],
								'last_name'           => $single_team_member_row['last_name'],
								'user_name'           => $single_team_member_row['user_name'],
								'email'               => $single_team_member_row['email'],
								'steps'               => $single_team_member_row['progress']['completed'] . '/' . $single_team_member_row['progress']['total'],
								'course_completed_on' => $single_team_member_row['course_completed_on'],
								'percentage'          => $single_team_member_row['progress']['percentage'] . '%',
							);
							$csv_rows[] = $csv_row;
						}
							// }
					}
					// }
				}

				$fp         = fopen( 'php://output', 'wb' );
				$csv_header = array(
					'Team Name',
					'Course Name',
					'Team Avg',
					'First Name',
					'Last Name',
					'Username',
					'Email',
					'Completed Steps',
					'Completion Date',
					'Completion Percentage',
				);
				fputcsv( $fp, $csv_header );
				foreach ( $csv_rows as $csv_row ) {
					// $val = explode(",", $line);
					fputcsv( $fp, $csv_row );
				}
				fclose( $fp );
				exit();
				return;

			}
			// print_r($return_result);

			$success = true;
		} catch ( Exception $e ) {
			wp_send_json_error(
				array(
					'errors' => $e->getMessage(),
				)
			);
		}

		wp_send_json_success();
	}

	/**
	 * AJAX handler to manage teams' edit team member action.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action is not valid.
	 *
	 * @return void
	 */
	public function action_manage_team_edit_team_member() {
		$error   = null;
		$success = false;
		try {
			$groups_plus_team_member = array();
			parse_str( $_POST['data'], $groups_plus_team_member );
			$groups_plus_team_member = stripslashes_deep( $groups_plus_team_member );

			if ( ! isset( $groups_plus_team_member['nonce'] ) || empty( $groups_plus_team_member['nonce'] ) || ! wp_verify_nonce( $groups_plus_team_member['nonce'], 'learndash-groups-plus-manage-team-edit-team-member' ) ) {
				throw new Exception( 'Security issue' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$user_id_key = sanitize_text_field( $groups_plus_team_member['user'] );
			$user_id     = user_encrypt_decrypt( 'decrypt', $user_id_key );

			if ( ! is_numeric( $user_id ) ) {
				throw new Exception( 'Invalid user' );
			}

			if ( ! Group::can_edit_user( $user_id, get_current_user_id() ) ) {
				throw new Exception( __( "You don't have permission for this", 'learndash-groups-plus' ) );
			}

			$errors = array();
			if ( empty( $groups_plus_team_member['firstname'] ) && strlen( $groups_plus_team_member['firstname'] ) < 3 ) {
				$errors[] = esc_html__( 'Firstname is required and at least 3 letters.', 'learndash-groups-plus' );
			}

			if ( empty( $groups_plus_team_member['username'] ) && strlen( $groups_plus_team_member['username'] ) < 3 ) {
				$errors[] = esc_html__( 'Username is required and at least 3 letters.', 'learndash-groups-plus' );
			}

			$username_exists = username_exists( $groups_plus_team_member['username'] );

			if ( ! empty( $username_exists ) && $username_exists != $user_id ) {
				$errors[] = esc_html__( 'Username already used', 'learndash-groups-plus' );
			}

			$email_exists = email_exists( $groups_plus_team_member['email'] );
			if ( ! empty( $groups_plus_team_member['email'] ) && ! empty( $email_exists ) && $email_exists != $user_id ) {
				$errors[] = esc_html__( 'Email address already used', 'learndash-groups-plus' );
			}

			if ( $errors ) {
				$message = implode( '<br>', $errors );
				throw new Exception( $message );
			}

			$user = array(
				'firstname' => sanitize_text_field( $groups_plus_team_member['firstname'] ),
				'lastname'  => sanitize_text_field( $groups_plus_team_member['lastname'] ),
				'username'  => sanitize_text_field( $groups_plus_team_member['username'] ),
				'email'     => sanitize_text_field( $groups_plus_team_member['email'] ),
			);

			if ( $user_id ) {
				$userdata = array(
					'ID'           => $user_id,
					'display_name' => $user['firstname'] . ' ' . $user['lastname'],
					'user_email'   => $user['email'],
					'username'     => $user['username'],
					'first_name'   => $user['firstname'],
					'last_name'    => $user['lastname'],
				);

				$lock_team_leader_and_team_member_names = get_site_option( 'lock_team_leader_and_team_member_names' );
				if ( $lock_team_leader_and_team_member_names === 'yes' ) {
					unset( $userdata['first_name'] );
					unset( $userdata['last_name'] );
				}

				$user_id = wp_update_user( $userdata );
				if ( is_wp_error( $user_id ) ) {
					$error = $user_id->get_error_message();
					throw new Exception( $error );
				}
			}

			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	/**
	 * AJAX handler to manage teams' delete team member action.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action is not valid.
	 *
	 * @return void
	 */
	public function action_manage_team_delete_team_member() {
		$error   = null;
		$success = false;
		try {

			$groups_plus_team_member = array();
			parse_str( $_POST['data'], $groups_plus_team_member );
			$groups_plus_team_member = stripslashes_deep( $groups_plus_team_member );

			if ( ! isset( $groups_plus_team_member['nonce'] ) || empty( $groups_plus_team_member['nonce'] ) || ! wp_verify_nonce( $groups_plus_team_member['nonce'], 'learndash-groups-plus-manage-team-edit-team-member' ) ) {
				throw new Exception( 'Security issue' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$user_id_key = sanitize_text_field( $groups_plus_team_member['user'] );
			$user_id     = user_encrypt_decrypt( 'decrypt', $user_id_key );

			$group_key = sanitize_text_field( $groups_plus_team_member['group'] );
			$group_id  = general_encrypt_decrypt( 'decrypt', $group_key );

			if ( ! is_numeric( $user_id ) ) {
				throw new Exception( 'Invalid user' );
			}

			if ( ! is_numeric( $group_id ) ) {
				throw new Exception( 'Invalid group' );
			}

			ld_update_group_access( $user_id, $group_id, true );
			// Finally clear our cache for other services
			$transient_key = 'learndash_group_users_' . $group_id;
			delete_transient( $transient_key );

			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();

	}

	/**
	 * AJAX handler to manage teams' delete team member permanently action.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action is not valid.
	 *
	 * @return void
	 */
	public function action_manage_team_delete_team_member_permanently() {
		$error   = null;
		$success = false;
		try {

			$groups_plus_team_member = array();
			parse_str( $_POST['data'], $groups_plus_team_member );
			$groups_plus_team_member = stripslashes_deep( $groups_plus_team_member );

			if ( ! isset( $groups_plus_team_member['nonce'] ) || empty( $groups_plus_team_member['nonce'] ) || ! wp_verify_nonce( $groups_plus_team_member['nonce'], 'learndash-groups-plus-manage-team-edit-team-member' ) ) {
				throw new Exception( 'Security issue' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$user_id_key = sanitize_text_field( $groups_plus_team_member['user'] );
			$user_id     = user_encrypt_decrypt( 'decrypt', $user_id_key );

			if ( ! Group::can_edit_user( $user_id, get_current_user_id() ) ) {
				throw new Exception( __( "You don't have permission for this", 'learndash-groups-plus' ) );
			}

			$group_key = sanitize_text_field( $groups_plus_team_member['group'] );
			$group_id  = general_encrypt_decrypt( 'decrypt', $group_key );

			if ( ! is_numeric( $user_id ) ) {
				throw new Exception( 'Invalid user' );
			}

			if ( ! is_numeric( $group_id ) ) {
				throw new Exception( 'Invalid group' );
			}

			ld_update_group_access( $user_id, $group_id, true );
			// Finally clear our cache for other services
			$transient_key = 'learndash_group_users_' . $group_id;
			delete_transient( $transient_key );

			wp_delete_user( $user_id );

			// hook
			do_action( 'delete_team_member', $user_id );

			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	/**
	 * AJAX handler to save organizations' team member password.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action is not valid.
	 *
	 * @return void
	 */
	public function action_save_groups_plus_team_member_password() {
		$error   = null;
		$success = false;
		try {
			if ( get_site_option( 'hide_change_password_team_member_button', 'no' ) === 'yes' ) {
				wp_send_json_error(
					array(
						'errors' => __( 'This feature has been disabled', 'learndash-groups-plus' ),
					)
				);
			}

			$data = array();
			parse_str( $_POST['data'], $data );
			$data = stripslashes_deep( $data );

			if ( ! isset( $data['nonce'] ) || empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'learndash-groups-plus-change-team-member-password' ) ) {
				wp_send_json_error(
					array(
						'errors' => __( 'Security issue', 'learndash-groups-plus' ),
					)
				);
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				wp_send_json_error(
					array(
						'errors' => __( "You don't have permission for this", 'learndash-groups-plus' ),
					)
				);
			}

			$user_id_key = sanitize_text_field( $data['user'] );
			$user_id     = user_encrypt_decrypt( 'decrypt', $user_id_key );

			if ( ! is_numeric( $user_id ) ) {
				wp_send_json_error(
					array(
						'errors' => __( 'Invalid user', 'learndash-groups-plus' ),
					)
				);
			}

			if ( ! Group::can_edit_user( $user_id, get_current_user_id() ) ) {
				wp_send_json_error(
					array(
						'errors' => __( "You don't have permission for this", 'learndash-groups-plus' ),
					)
				);
			}

			$errors           = array();
			$password         = $data['new_password'];
			$confirm_password = $data['confirm_password'];

			if ( empty( $password ) ) {
				wp_send_json_error(
					array(
						'errors' => __( 'Password is required', 'learndash-groups-plus' ),
					)
				);
			}

			if ( strlen( $password ) < 5 ) {
				wp_send_json_error(
					array(
						'errors' => __( 'Minimum length for password is 5', 'learndash-groups-plus' ),
					)
				);
			}

			if ( empty( $confirm_password ) && strlen( $confirm_password ) < 5 ) {
				wp_send_json_error(
					array(
						'errors' => __( 'Confirm password', 'learndash-groups-plus' ),
					)
				);
			}

			if ( ! empty( $confirm_password ) && ! empty( $password ) && $password != $confirm_password ) {
				wp_send_json_error(
					array(
						'errors' => __( "Passwords don't match", 'learndash-groups-plus' ),
					)
				);
			}

			wp_set_password( $password, $user_id );

			$success = true;
		} catch ( Exception $e ) {
			wp_send_json_error(
				array(
					'errors' => $e->getMessage(),
				)
			);
		}

		wp_send_json_success();
	}

	/**
	 * AJAX handler to save organizations' team member.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action is not valid.
	 *
	 * @return void
	 */
	public function action_save_groups_plus_team_member() {
		$error   = null;
		$success = false;

		try {
			$data = array();
			parse_str( $_POST['data'], $data );
			$data = stripslashes_deep( $data );

			if ( ! isset( $data['nonce'] ) || empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'learndash-groups-plus-add-team-member' ) ) {
				throw new Exception( 'Security issue' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$user = $data['groups_plus_team_member'];

			$groups_plus_team_member = $user;
			$group_key           = $groups_plus_team_member['group'];
			$group_id            = general_encrypt_decrypt( 'decrypt', $group_key );
			$license             = get_licenses_info_user( $group_id );

			if ( $license['licenses_remaining'] <= 0 ) {
				throw new Exception( 'You don\'t have any licenses remaining' );
			}

			if ( ! $group_id ) {
				throw new Exception( 'Invalid group plus' );
			}

			$userdata = $users_array = array();
			if ( empty( $data['has_team_member_exists'] ) ) {
				$errors = array();
				if ( empty( $groups_plus_team_member['username'] ) && strlen( $groups_plus_team_member['username'] ) < 3 ) {
					$errors[] = esc_html__( 'Username is required', 'learndash-groups-plus' );
				}

				if ( empty( $groups_plus_team_member['firstname'] ) && strlen( $groups_plus_team_member['firstname'] ) < 3 ) {
					$errors[] = esc_html__( 'Firstname is required', 'learndash-groups-plus' );
				}
				if ( empty( $groups_plus_team_member['password'] ) && strlen( $groups_plus_team_member['password'] ) < 3 ) {
					$errors[] = esc_html__( 'Password is required', 'learndash-groups-plus' );
				}
				if ( ! empty( $groups_plus_team_member['email'] ) && email_exists( $groups_plus_team_member['email'] ) ) {
					$errors[] = esc_html__( 'Email address already used', 'learndash-groups-plus' );
				}
				if ( $errors ) {
					$message = implode( '<br>', $errors );
					throw new Exception( $message );
				}
				$current_user_id = get_current_user_id();
				$username        = $groups_plus_team_member['username'];
				$user_id         = Cast::to_int( username_exists( $username ) );
				if ( $user_id ) {
					throw new Exception( 'Username ' . $username . ' exists' );
				}

				$user = array(
					'firstname' => sanitize_text_field( $groups_plus_team_member['firstname'] ),
					'lastname'  => sanitize_text_field( $groups_plus_team_member['lastname'] ),
					'username'  => sanitize_user( $username, true ),
					'email'     => sanitize_text_field( $groups_plus_team_member['email'] ),
					'password'  => $groups_plus_team_member['password'],
				);

				$userdata = array(
					'user_login'   => $user['username'],
					'user_pass'    => $user['password'],
					'user_email'   => $user['email'],
					'display_name' => $user['firstname'] . ' ' . $user['lastname'],
					'first_name'   => $user['firstname'],
					'last_name'    => $user['lastname'],
				);
				$user_id  = wp_insert_user( $userdata );
				if ( is_wp_error( $user_id ) ) {
					$error = $user_id->get_error_message();
					throw new Exception( $error );
				}
				$users_array[] = $user_id;
				do_action( 'create_team_member', $user_id, $userdata );
			} else {
				$user_id     = $data['groups_plus_team_member_id'];
				$users_array = $data['groups_plus_team_member_id'];
			}

			foreach ( $users_array as $user_id ) {
				ld_update_group_access( $user_id, $group_id );
				// Finally clear our cache for other services
				$transient_key = 'learndash_group_users_' . $group_id;
				delete_transient( $transient_key );

				// Send welcome mail to team member
				if ( ! empty( $user['email'] ) ) {
					$email = new Welcome( Cast::to_int( $user_id ), $group_id, $userdata, 'team_member' );
					$email->send();
				}
			}

			$user['ID'] = $user_id;

			if ( $user['ID'] ) {
				$success = true;
			}
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	/**
	 * AJAX handler to import organizations' users.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action is not valid.
	 *
	 * @return void
	 */
	public function action_import_users_groups_plus() {

		$error   = null;
		$success = false;
		try {

			if ( ! isset( $_POST['nonce'] ) || empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'learndash-groups-plus-import-team-members' ) ) {
				throw new Exception( 'Security issue' );
			}

			$file = $_FILES['groups_plus_csv']['tmp_name'];

			$group_key  = $_POST['group'];
			$import_log = array(
				'success_users' => array(),
				'failed_users'  => array(),
			);

			if ( ! $file ) {
				throw new Exception( 'CSV file is required' );
			}

			if ( ! $group_key ) {
				throw new Exception( 'Group is required' );
			}

			$delimiter = ',';
			if ( ! file_exists( $file ) || ! is_readable( $file ) ) {
				throw new Exception( "File doesn't exist or is not readable" );
			}

			$allowed_columns = array(
				'firstname',
				'lastname',
				'username',
				'password',
				'email',
			);
			// error_reporting(E_ALL);
			$header = null;
			$data   = array();
			if ( ( $handle = fopen( $file, 'r' ) ) !== false ) {
				$i = 0;
				while ( ( $row = fgetcsv( $handle, 100000, $delimiter ) ) !== false ) {
					if ( ! $header ) {
						$header                = array_map(
							function( $key ) {
								return groups_plus_clear_key( $key );
							},
							$row
						);
						$header_required_count = 0;

						if ( $header ) {
							foreach ( $header as $header_key ) {
								if ( in_array( $header_key, $allowed_columns ) ) {
									$header_required_count++;
								}
							}
						}
						if ( $header_required_count != count( $allowed_columns ) ) {
							throw new Exception( 'Please use the model from csv' );
						}
					} else {
						$user   = array_combine( $header, $row );
						$data[] = $user;
					}
					$i++;
				}
				fclose( $handle );
			}
			$users = $data;

			$current_user_id = get_current_user_id();

			if ( $users ) {
				foreach ( $users as $key => $user ) {
					$user_validate = array_unique( $user );
					if ( ! $user && count( $user_validate ) != 4 ) {
						continue;
					}
					$user['group'] = $group_key;
					try {
						// addGroupsPlusTeam Member($user);

						$groups_plus_team_member = $user;
						$group_key           = $groups_plus_team_member['group'];
						$group_id            = general_encrypt_decrypt( 'decrypt', $group_key );
						$license             = get_licenses_info_user( $group_id );

						if ( $license['licenses_remaining'] <= 0 ) {
							throw new Exception( 'You don\'t have any licenses remaining' );
						}

						if ( ! $group_id ) {
							throw new Exception( 'Invalid group plus' );
						}

						$errors                          = array();
						$groups_plus_team_member['username'] = trim( $groups_plus_team_member['username'] );
						$groups_plus_team_member['username'] = $groups_plus_team_member['username'];
						if ( empty( $groups_plus_team_member['username'] ) && strlen( $groups_plus_team_member['username'] ) < 3 ) {
							$errors[] = esc_html__( 'Username is required', 'learndash-groups-plus' );
						}

						if ( empty( $groups_plus_team_member['firstname'] ) && strlen( $groups_plus_team_member['firstname'] ) < 3 ) {
							$errors[] = esc_html__( 'Firstname is required', 'learndash-groups-plus' );
						}

						// if(empty($groups_plus_team_member['lastname']) && strlen($groups_plus_team_member['lastname']) < 3){
						// $errors[] = "Lastname is required";
						// }
						if ( empty( $groups_plus_team_member['password'] ) && strlen( $groups_plus_team_member['password'] ) < 3 ) {
							$errors[] = esc_html__( 'Password is required', 'learndash-groups-plus' );
						}

						if ( $errors ) {
							$message = implode( '<br>', $errors );
							throw new Exception( $message );
						}

						$username      = $groups_plus_team_member['username'];
						$existing_user = false;
						$user_id       = username_exists( $username );
						if ( $user_id ) {
							$existing_user = get_user_by( 'id', $user_id );
						}
						$errors = array();
						if ( ! empty( $groups_plus_team_member['email'] ) && ( isset( $existing_user->user_email ) && empty( $existing_user->user_email ) && email_exists( $groups_plus_team_member['email'] ) ) ) {
							$errors[] = esc_html__( 'Email address already used', 'learndash-groups-plus' );
						} elseif ( ! empty( $groups_plus_team_member['email'] ) && ( isset( $existing_user->user_email ) && ! empty( $existing_user->user_email ) && email_exists( $groups_plus_team_member['email'] ) != false && $user_id != email_exists( $groups_plus_team_member['email'] ) ) ) {
							$errors[] = esc_html__( 'Email address already used', 'learndash-groups-plus' );
						}

						if ( $errors ) {
							$message = implode( '<br>', $errors );
							throw new Exception( $message );
						}

						$user = array(
							'firstname' => sanitize_text_field( $groups_plus_team_member['firstname'] ),
							'lastname'  => sanitize_text_field( $groups_plus_team_member['lastname'] ),
							'username'  => sanitize_user( $username, true ),
							'password'  => $groups_plus_team_member['password'],
							'email'     => sanitize_text_field( $groups_plus_team_member['email'] ),
						);

						if ( $user_id ) {
							$allow_existing_users_to_be_updated = get_site_option( 'allow_existing_users_to_be_updated' );
							if ( $allow_existing_users_to_be_updated === 'yes' ) {

								if ( Group::can_edit_user( $existing_user->ID, $current_user_id ) ) {

									$userdata = array(
										'ID'           => $existing_user->ID,
										'user_pass'    => $user['password'],
										'user_email'   => $user['email'],
										'display_name' => $user['firstname'] . ' ' . $user['lastname'],
										'first_name'   => $user['firstname'],
										'last_name'    => $user['lastname'],
									);

									$user_data = wp_update_user( $userdata );

								}

							} else {
								throw new Exception( 'Username ' . $username . ' exists' );
							}
						}

						if ( ! $user_id ) {
							// $user_id = wp_create_user( $username, $password);
							$userdata = array(
								'user_login'   => $user['username'],
								'user_pass'    => $user['password'],
								'user_email'   => $user['email'],
								'display_name' => $user['firstname'] . ' ' . $user['lastname'],
								'first_name'   => $user['firstname'],
								'last_name'    => $user['lastname'],
							);
							$user_id  = wp_insert_user( $userdata );
							if ( is_wp_error( $user_id ) ) {
								$error = $user_id->get_error_message();
								throw new Exception( $error );
							}
							if ( $user_id ) {
								ld_update_group_access( $user_id, $group_id );
								// Finally clear our cache for other services
								$transient_key = 'learndash_group_users_' . $group_id;
								delete_transient( $transient_key );

								// Send welcome mail to team member
								if ( ! empty( $user['email'] ) ) {
									$email = new Welcome( $user_id, $group_id, $userdata, 'team_member' );
									$email->send();
								}
								do_action( 'create_team_member', $user_id, $userdata );
							} else {
								throw new Exception( 'User was not created' );
							}
							$user['ID'] = $user_id;
						} else {
							// throw new \Exception('Username exists');
							ld_update_group_access( $user_id, $group_id );

							// Finally clear our cache for other services
							$transient_key = 'learndash_group_users_' . $group_id;
							delete_transient( $transient_key );

							// Send welcome mail to team member
							if ( ! empty( $user['email'] ) ) {
								$email = new Welcome( $user_id, $group_id, [], 'team_member' );
								$email->send();
							}
						}

						$import_log['success_users'][ $key ] = array(
							'user'    => $user,
							'message' => 'User successfully created',
						);
					} catch ( Exception $e ) {
						$import_log['failed_users'][ $key ] = array(
							'user'    => $user,
							'message' => $e->getMessage(),
						);
					}
				}
			} else {
				throw new Exception( 'Users are required' );
			}
			$message = '';
			if ( $import_log['success_users'] ) {
				foreach ( $import_log['success_users'] as $success_users ) {
					$message .= '<span style="color: #8fae1b">' . $success_users['user']['firstname'] . ' ' . $success_users['user']['lastname'] . ' (<strong>' . $success_users['user']['username'] . '</strong>): ' . $success_users['message'] . '</span><br />';
				}
				$success = true;
			}

			if ( $import_log['failed_users'] ) {
				foreach ( $import_log['failed_users'] as $failed_users ) {
					$message .= '<span style="color: #c00">' . $failed_users['user']['firstname'] . ' ' . $failed_users['user']['lastname'] . ' (<strong>' . $failed_users['user']['username'] . '</strong>): ' . $failed_users['message'] . '</span><br />';
				}
				$success = false;
			}

			if ( $message ) {
				throw new Exception( $message );
			}
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	public function set_last_login( $login ) {
		$user = get_user_by( 'login', $login );
		if ( ! empty( $user ) ) {
			$ck_login_time = get_user_meta( $user->ID, 'current_login', true );
			// add or update the last login value for logged in user
			if ( ! is_array( $ck_login_time ) ) {
				$ck_login_time = array();
			}
			$ck_login_time[] = current_time( 'mysql' );
			update_user_meta( $user->ID, 'current_login', $ck_login_time );
		}
	}

	/**
	 * Outputs the user roles field in the user profile.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_User $user User object.
	 *
	 * @return void
	 */
	public function edit_user_profile( $user ) {
		if ( ! current_user_can( 'promote_users', $user->ID ) ) {
			return;
		}

		$roles = array_reverse( get_editable_roles() );
		?>

		<h2><?php _e( 'User Roles', 'learndash-groups-plus' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr class="user-role-wrap">
				<th>
					<label for="roles"><?php _e( 'Roles', 'learndash-groups-plus' ); ?></label>
				</th>
				<td>
					<select name="roles[]" id="roles" multiple="multiple">
						<?php foreach( $roles as $key => $role ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php echo in_array( $key, $user->roles ) ? 'selected="selected"' : ''; ?>><?php echo esc_html( $role['name'] ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		</table>

		<?php
	}

	/**
	 * Save user roles field on the user profile.
	 *
	 * @since 1.0.0
	 *
	 * @param int                  $user_id       User ID.
	 * @param array<string, mixed> $old_user_data Old user data.
	 * @param array<string, mixed> $userdata      User data.
	 *
	 * @return void
	 */
	public function profile_update( $user_id, $old_user_data, $userdata ) {
		if ( ! current_user_can( 'promote_users', $user_id ) ) {
			return;
		}

		$user = get_user_by( 'ID', $user_id );
		$original_roles = $user->roles;
		if ( isset( $_POST['roles'] ) && is_array( $_POST['roles'] ) ) {
			$old_roles = array_diff( $original_roles, $_POST['roles'] );
			if( is_array( $old_roles ) ) {
				foreach ( $old_roles as $role ) {
					$user->remove_role( $role );
				}
			}

			$new_roles = array_map( 'sanitize_text_field', array_diff( $_POST['roles'], $original_roles ) );
			if ( is_array( $new_roles ) ) {
				foreach( $new_roles as $role ) {
					$user->add_role( $role );
				}
			}
		}
	}

	/***
	 * Save team leader email and send
	 */
	public function action_manage_organization_add_team_leader_welcome_email() {
		$error   = null;
		$success = false;
		try {
			$data = array();
			parse_str( $_POST['data'], $data );
			$data = stripslashes_deep( $data );

			if ( ! isset( $data['nonce'] ) || empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'learndash-groups-plus-manage-organization-add-team-leader-welcome-email' ) ) {
				throw new Exception( 'Security issue' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$team_leader_email_subject = sanitize_text_field( $data['team_leader_email_subject'] );
			$team_leader_email_body    = wp_kses_post( $data['team_leader_email_body'] );

			if ( ! $team_leader_email_subject ) {
				throw new Exception( 'Subject is required' );
			}
			if ( ! $team_leader_email_body ) {
				throw new Exception( 'Body is required' );
			}

			$user_id = get_current_user_id();

			// Save subject and body to user meta
			$team_leader_email_data = array(
				'team_leader_email_subject' => $team_leader_email_subject,
				'team_leader_email_body'    => $team_leader_email_body,
			);
			update_user_meta( $user_id, 'team_leader_email_data', $team_leader_email_data );

			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	/***
	 * Save team member email and send
	 */
	public function action_add_team_member_email() {
		$error   = null;
		$success = false;
		try {
			$data = array();
			parse_str( $_POST['data'], $data );
			$data = stripslashes_deep( $data );

			if ( ! isset( $data['nonce'] ) || empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'learndash-groups-plus-add-team-member-email-groups-plus' ) ) {
				throw new Exception( 'Security issue' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$team_member_email_subject = sanitize_text_field( $data['team_member_email_subject'] );
			$team_member_email_body = wp_kses_post( $data['team_member_email_body'] );
			$group_id           = sanitize_text_field( $data['group_id'] );
			$group_id           = general_encrypt_decrypt( 'decrypt', $group_id );

			if ( ! $team_member_email_subject ) {
				throw new Exception( 'Subject is required' );
			}
			if ( ! $team_member_email_body ) {
				throw new Exception( 'Body is required' );
			}

			$current_user_id = get_current_user_id();

			// Save subject and body to user meta
			$team_member_email_data = array(
				'team_member_email_subject' => $team_member_email_subject,
				'team_member_email_body'    => $team_member_email_body,
			);
			update_user_meta( $current_user_id, 'team_member_email_data', $team_member_email_data );

			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	/***
	 * Send broadcast mail to team members
	 * **/
	public function action_send_broadcast_email_to_team_members() {
		$error   = null;
		$success = false;
		try {
			$data = array();
			parse_str( $_POST['data'], $data );
			$data = stripslashes_deep( $data );

			if ( ! isset( $data['nonce'] ) || empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'learndash-groups-plus-send-broadcast-email-to-team-members' ) ) {
				throw new Exception( 'Security issue' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$team_member_email_subject = sanitize_text_field( $data['team_member_email_subject'] );
			$team_member_email_body    = wp_kses_post( $data['team_member_email_body'] );
			$group_id              = sanitize_text_field( $data['group_id'] );
			 $group_id             = general_encrypt_decrypt( 'decrypt', $group_id );

			if ( ! $team_member_email_subject ) {
				throw new Exception( 'Subject is required' );
			}
			if ( ! $team_member_email_body ) {
				throw new Exception( 'Body is required' );
			}

			$current_user_id = get_current_user_id();

			// Send Welcome mail to team member
			$parent_group    = array();
			$parent_group_id = 0;
			if ( $group_id ) {
				// Secondary Group;
				$args            = array(
					'numberposts' => 1,
					'post_type'   => 'groups',
					'post__in'    => array( $group_id ),
					'orderby'     => 'menu_order',
					'order'       => 'DESC',
				);
				$child_group     = get_posts( $args );
				$parent_group_id = $child_group[0]->post_parent;
			}

			// Primary Group;
			$args           = array(
				'numberposts' => 1,
				'post_type'   => 'groups',
				'orderby'     => 'menu_order',
				'order'       => 'DESC',
				'post__in'    => array( $parent_group_id ),
				'post_parent' => 0,
			);
			$parent_group       = get_posts( $args );
			$group_team_members = Group::get_group_users( $group_id );

			$find = array( '{group_name}', '{childgroup_name}', '{team_member_name}', '{user_name}' );
			foreach ( $group_team_members as $group_team_member ) {
				$team_member_first_name = get_user_meta( $group_team_member->ID, 'first_name', true );
				$team_member_last_name  = get_user_meta( $group_team_member->ID, 'last_name', true );
				if ( ! empty( $group_team_member->user_email ) ) {
					$replace = array(
						$parent_group[0]->post_title ?? '',
						$child_group[0]->post_title ?? '',
						trim( $team_member_first_name . ' ' . $team_member_last_name ),
						$group_team_member->user_login
					);

					$filter_team_member_email_body = str_replace( $find, $replace, $team_member_email_body );

					$sender  = wp_get_current_user();
					$to      = $group_team_member->user_email;
					$subject = $team_member_email_subject;
					$body    = $filter_team_member_email_body;
					$headers = 'Content-Type: text/html; charset=UTF-8;';

					if ( $sender ) {
						$headers .= "\r\n" . ' From: ' . $sender->display_name . ' <' . $sender->user_email . '>';
					}

					learndash_emails_send(
						$to,
						array(
							'subject'      => $subject,
							'message'      => $body,
							'content_type' => 'text/html',
						),
						$headers
					);
				}
			}

			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	/**
	 * Action handler to send broadcast message to team members.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action is not valid.
	 *
	 * @return void
	 */
	public function action_send_broadcast_message_to_team_members() {
		$error   = null;
		$success = false;
		try {
			$data = array();
			if ( ! isset( $_POST['data']['nonce'] ) || empty( $_POST['data']['nonce'] ) || ! wp_verify_nonce( $_POST['data']['nonce'], 'learndash-groups-plus-send-broadcast-message-to-team-members' ) ) {
				throw new Exception( 'Security issue' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$team_member_message_body = stripslashes_deep( $_POST['data']['team_member_message_body'] );
			$group_id             = sanitize_text_field( $_POST['data']['group_id'] );
			$group_id             = general_encrypt_decrypt( 'decrypt', $group_id );

			if ( empty( $team_member_message_body ) ) {
				throw new Exception( 'Message is required' );
			}

			$current_user_id = get_current_user_id();

			$attr = array(
				'group_id'        => $group_id,
				'group_leader_id' => $current_user_id,
				'message'         => $team_member_message_body,
			);

			Database::add_broadcast_message( $attr );

			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	/***
	 * Send broadcast mail to team leaders
	 * **/
	public function action_send_broadcast_email_to_team_leaders() {
		$error   = null;
		$success = false;
		try {
			$data = array();
			parse_str( $_POST['data'], $data );
			$data = stripslashes_deep( $data );

			if ( ! isset( $data['nonce'] ) || empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'learndash-groups-plus-send-broadcast-email-to-team-leaders' ) ) {
				throw new Exception( 'Security issue' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$team_leader_email_subject = sanitize_text_field( $data['team_leader_email_subject'] );
			$team_leader_email_body = wp_kses_post( $data['team_leader_email_body'] );

			if ( ! $team_leader_email_subject ) {
				throw new Exception( 'Subject is required' );
			}
			if ( ! $team_leader_email_body ) {
				throw new Exception( 'Body is required' );
			}

			$parent_group    = array();
			$parent_group_id = $data['primary_group_id'];
			$child_groups    = learndash_get_group_children( $parent_group_id );

			foreach ( $child_groups as $child_group ) {
				$group_leaders   = learndash_get_groups_administrators( $child_group );
				$childgroup_name = get_the_title( $child_group );

				$group_leader  	   = array();
				$team_leader_email = $team_leader_first_name = $team_leader_last_name = '';

				if ( ! empty( $group_leaders ) ) {
					$find = array( '{group_name}', '{childgroup_name}', '{team_leader_name}', '{team_leader_username}' );
					foreach ( $group_leaders as $group_leader ) {
						$team_leader_email      = $group_leader->user_email;
						$team_leader_first_name = get_user_meta( $group_leader->ID, 'first_name', true );
						$team_leader_last_name  = get_user_meta( $group_leader->ID, 'last_name', true );
						$team_leader_username   = $group_leader->user_login;

						if ( ! empty( $team_leader_email ) ) {
							$replace = array(
								$parent_group[0]->post_title ?? '',
								$childgroup_name,
								trim( $team_leader_first_name . ' ' . $team_leader_last_name ),
								$team_leader_username
							);

							$filter_team_leader_email_body = str_replace( $find, $replace, $team_leader_email_body );

							$to      = $team_leader_email;
							$subject = $team_leader_email_subject;
							$body    = $filter_team_leader_email_body;
							$sender  = wp_get_current_user();
							$headers = 'Content-Type: text/html; charset=UTF-8;';

							if ( $sender ) {
								$headers .= "\r\n" . ' From: ' . $sender->display_name . ' <' . $sender->user_email . '>';
							}

							learndash_emails_send(
								$to,
								array(
									'subject'      => $subject,
									'message'      => $body,
									'content_type' => 'text/html',
								),
								$headers
							);
						}
					}
				}
			}

			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	/**
	 * AJAX handler to save lock/unlock lesson to user meta.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action is not valid.
	 *
	 * @return void
	 */
	public function action_lock_unlock_lesson() {
		$error   = null;
		$success = false;
		try {
			$data = array();

			if ( ! isset( $_POST['nonce'] ) || empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'learndash-groups-plus-lesson-table-nonce' ) ) {
				throw new Exception( 'Security issue' );
			}
			$user_id   = sanitize_text_field( $_POST['user_id'] );
			$lesson_id = sanitize_text_field( $_POST['lesson_id'] );
			$course_id = sanitize_text_field( $_POST['course_id'] );
			$lock      = sanitize_text_field( $_POST['lock'] );

			// add or update the last login value for logged in user
			$lock_courses_lessons = array();

			if ( $lock == 'true' ) {
				$lock_courses_lessons[ $course_id ] = array( 'lesson_id' => $lesson_id );
			} else {
				unset( $lock_courses_lessons[ $course_id ] );
			}

			update_user_meta( $user_id, 'lock_courses_lessons', $lock_courses_lessons );

			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	public function learndash_course_progression_enabled( $setting, $course_id ) {
		global $post;
		$user_id = get_current_user_id();

		$course_steps         = learndash_get_course_steps( $course_id, array( 'sfwd-lessons' ) );
		$lock_courses_lessons = get_user_meta( $user_id, 'lock_courses_lessons', true );
		if ( ! empty( $lock_courses_lessons ) && isset( $lock_courses_lessons[ $course_id ] ) ) {
			$lock_courses_lessons = $lock_courses_lessons[ $course_id ];
		} else {
			$lock_courses_lessons = array();
		}

		if ( ! empty( $lock_courses_lessons ) ) {
			$key          = array_search( intval( $lock_courses_lessons['lesson_id'] ), $course_steps, true );
			$course_steps = array_slice( $course_steps, $key );

			if ( in_array( $post->ID, $course_steps ) ) {
				return true;
			} else {
				return false;
			}
		} else {
			return $setting;
		}

	}

	public function learndash_lesson_progress_alert( $alert, $id, $course_id ) {
		global $post;
		$user_id = get_current_user_id();

		$course_steps         = learndash_get_course_steps( $course_id, array( 'sfwd-lessons' ) );
		$lock_courses_lessons = get_user_meta( $user_id, 'lock_courses_lessons', true );
		if ( ! empty( $lock_courses_lessons ) && isset( $lock_courses_lessons[ $course_id ] ) ) {
			$lock_courses_lessons = $lock_courses_lessons[ $course_id ];
		} else {
			$lock_courses_lessons = array();
		}

		if ( ! empty( $lock_courses_lessons ) ) {
			$key          = array_search( intval( $lock_courses_lessons['lesson_id'] ), $course_steps, true );
			$course_steps = array_slice( $course_steps, $key );

			if ( in_array( $post->ID, $course_steps ) ) {
				$alert['message'] = sprintf(
					esc_html__( 'Please wait for your %s to unlock this information', 'learndash-groups-plus' ),
					learndash_get_custom_label_lower( 'team_leader' )
				);
			}
		}

		return $alert;
	}

	/**
	 * Set hidden columns for groups post listing.
	 *
	 * @since 1.0.0
	 *
	 * @param array  	$columns		Existing hidden columns.
	 * @param WP_Screen $screen			Current screen object.
	 * @param bool 		$use_defaults	Whether it uses default hidden columns or not.
	 *
	 * @return array<string>
	 */
	public function set_hidden_columns( $columns, $screen, $use_defaults ) {
		if ( $screen->post_type === 'groups' ) {
			$new_hidden = array(
				'author',
				'date',
				'categories'
			);

			$columns = array_merge( $columns, $new_hidden );
		}


		return $columns;
	}

	/**
	 * Add groups posts list columns.
	 *
	 * @param array  $columns	 Original columns.
	 * @param string $post_type	 Current screen post type.
	 *
	 * @return array 			 New $columns.
	 */
	public function add_groups_posts_columns( $columns, $post_type ) {
		if ( $post_type === 'groups' ) {
			$new_columns = array(
				'groups_plus_type' => array(
					'label' => esc_html__( 'Type', 'learndash-groups-plus' ),
					'display' => $this->container->callback( $this, 'display_groups_plus_type_column' ),
					'after' => 'title',
				),
				'lead_organizers' => array(
					'label' => esc_html__( 'Lead Organizers', 'learndash-groups-plus' ),
					'display' => $this->container->callback( $this, 'display_lead_organizers_column' ),
					'after' => 'groups_plus_type',
				),
				'team_leaders' => array(
					'label' => sprintf(
						'%s',
						learndash_get_custom_label( 'team_leaders' )
					),
					'display' => $this->container->callback( $this, 'display_team_leaders_column' ),
					'after' => 'lead_organizers',
				),
				'team_members' => array(
					'label' => esc_html__( 'Members', 'learndash-groups-plus' ),
					'display' => $this->container->callback( $this, 'display_team_members_column' ),
					'after' => 'team_leaders',
				),
			);

			$columns = array_merge( $columns, $new_columns );
		}

		return $columns;
	}

	/**
	 * Display group_plus_type column output.
	 *
	 * @param int	 $post_id Current WP_Post ID.
	 * @param string $column  Current column key name.
	 *
	 * @return void
	 */
	public function display_groups_plus_type_column( $post_id, $column ) {
		$display = '';
		$post    = get_post( $post_id );

		if ( empty( $post->post_parent ) ) {
			$wc_enabled      = boolval( get_post_meta( $post_id, '_is_group_wc_enabled', true ) );

			if ( $wc_enabled ) {
				// translators: organization label.
				$display = sprintf( esc_html__( 'WooCommerce %s template', 'learndash-groups-plus' ), learndash_get_custom_label_lower( 'organization' ) );
			} else {
				$display = learndash_get_custom_label( 'organization' );
			}
		} else {
			$parent     = get_post( $post->post_parent );
			$wc_enabled = false;

			if ( $parent instanceof WP_Post ) {
				$wc_enabled = boolval( get_post_meta( $parent->ID, '_is_group_wc_enabled', true ) );
			}

			if ( $wc_enabled ) {
				$org_label = sprintf( esc_html__( 'WooCommerce %s template', 'learndash-groups-plus' ), learndash_get_custom_label_lower( 'organization' ) );
			} else {
				$org_label = learndash_get_custom_label_lower( 'organization' );
			}

			$display =  sprintf(
				// translators: team of org name.
				esc_html__( '%1$s of %2$s %3$s %4$s', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team' ),
				$parent->post_title,
				$org_label,
				"(ID: {$parent->ID})"
			);
		}

		if (
			empty( $post->post_parent )
			&& get_post_meta( $post->ID, SharedFunctions::$is_non_organization_team, true )
		) {
			$display = learndash_get_custom_label( 'team' );
		}

		echo $display;
	}

	/**
	 * Display lead_organizers column output.
	 *
	 * @param int	 $post_id Current WP_Post ID.
	 * @param string $column  Current column key name.
	 *
	 * @return void
	 */
	public function display_lead_organizers_column( $post_id, $column ) {
		$display = '&mdash;';
		$post    = get_post( $post_id );

		if ( empty( $post->post_parent ) ) {
			$wc_enabled = boolval( get_post_meta( $post_id, '_is_group_wc_enabled', true ) );

			if ( ! $wc_enabled ) {
				$leaders = learndash_get_groups_administrators( $post_id );

				if ( ! empty( $leaders ) && is_array( $leaders ) ) {
					$display = '<ul class="users-list lead-organizers">';

					$visible_leaders = array_slice( $leaders, 0, 3 );

					foreach ( $visible_leaders as $leader ) {
						$edit_url = get_edit_user_link( $leader->data->ID );

						$display .= sprintf(
							'<li class="item"><a href="%s">%s</a></li>',
							$edit_url,
							esc_html( $leader->data->display_name )
						);
					}

					$display .= '</ul>';

					if ( count( $leaders ) > 3 ) {
						$more_url = add_query_arg( array(
							'currentTab' => 'learndash_group_users',
						), get_edit_post_link( $post_id ) );

						$display .= sprintf(
							'<p><a href="%s">%s</a></p>',
							$more_url,
							esc_html__( 'See more', 'learndash-groups-plus' )
						);
					}
				}
			}
		}

		echo $display;
	}

	/**
	 * Display team_leaders column output.
	 *
	 * @param int	 $post_id Current WP_Post ID.
	 * @param string $column  Current column key name.
	 *
	 * @return void
	 */
	public function display_team_leaders_column( $post_id, $column ) {
		$display = '&mdash;';
		$post    = get_post( $post_id );

		if ( ! empty( $post->post_parent ) ) {
			$parent = get_post( $post->post_parent );

			$wc_enabled = boolval( get_post_meta( $parent->ID, '_is_group_wc_enabled', true ) );

			if ( ! $wc_enabled ) {
				$leaders = learndash_get_groups_administrators( $post_id );

				if ( ! empty( $leaders ) && is_array( $leaders ) ) {
					$display = '<ul class="users-list team-leaders">';

					$visible_leaders = array_slice( $leaders, 0, 3 );

					foreach ( $visible_leaders as $leader ) {
						$edit_url = get_edit_user_link( $leader->data->ID );

						$display .= sprintf(
							'<li class="item"><a href="%s">%s</a></li>',
							$edit_url,
							esc_html( $leader->data->display_name )
						);
					}

					$display .= '</ul>';

					if ( count( $leaders ) > 3 ) {
						$more_url = add_query_arg( array(
							'currentTab' => 'learndash_group_users',
						), get_edit_post_link( $post_id ) );

						$display .= sprintf(
							'<p><a href="%s">%s</a></p>',
							$more_url,
							esc_html__( 'See more', 'learndash-groups-plus' )
						);
					}
				}
			}
		}

		echo $display;
	}

	/**
	 * Display team_members column output.
	 *
	 * @param int	 $post_id Current WP_Post ID.
	 * @param string $column  Current column key name.
	 *
	 * @return void
	 */
	public function display_team_members_column( $post_id, $column ) {
		$display = '&mdash;';
		$post    = get_post( $post_id );

		if ( ! empty( $post->post_parent ) ) {
			$parent     = get_post( $post->post_parent );
			$wc_enabled = false;

			if ( $parent instanceof WP_Post ) {
				$wc_enabled = boolval( get_post_meta( $parent->ID, '_is_group_wc_enabled', true ) );
			}

			if ( ! $wc_enabled ) {
				$users = learndash_get_groups_users( $post_id );

				if ( ! empty( $users ) && is_array( $users ) ) {
					$display = '<ul class="users-list team-members">';

					$visible_users = array_slice( $users, 0, 3 );

					foreach ( $visible_users as $user ) {
						$edit_url = get_edit_user_link( $user->data->ID );

						$display .= sprintf(
							'<li class="item"><a href="%s">%s</a></li>',
							$edit_url,
							esc_html( $user->data->display_name )
						);
					}

					$display .= '</ul>';

					if ( count( $users ) > 3 ) {
						$more_url = add_query_arg( array(
							'currentTab' => 'learndash_group_users',
						), get_edit_post_link( $post_id ) );

						$display .= sprintf(
							'<p><a href="%s">%s</a></p>',
							$more_url,
							esc_html__( 'See more', 'learndash-groups-plus' )
						);
					}
				}
			}
		}

		echo $display;
	}

	/**
	 * AJAX handler to get broadcast messages.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action is not valid.
	 *
	 * @return void
	 */
	public function action_get_broadcast_messages() {
		$error   = null;
		$success = false;
		try {

			if ( ! isset( $_POST['data']['nonce'] ) || empty( $_POST['data']['nonce'] ) || ! wp_verify_nonce( $_POST['data']['nonce'], 'learndash-groups-plus-manage-broadcast-messages' ) ) {
				throw new Exception( 'Security issue' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}

			$group_id = sanitize_text_field( $_POST['data']['group_id'] );
			$group_id = general_encrypt_decrypt( 'decrypt', $group_id );
			global $wpdb;
			$return_html            = '';
			$broadcast_messages_tbl = $wpdb->prefix . Database::$broadcast_messages_tbl;
			$results                = $wpdb->get_results( " SELECT * FROM {$broadcast_messages_tbl} WHERE group_id = {$group_id}" );
			// print_r ( $results );

			if ( empty( $results ) ) {
				$return_html     .= '<tr>';
					$return_html .= '<td colspan="3">' . esc_html__( 'No record found.', 'learndash-groups-plus' ) . '</td>';
				$return_html     .= '</tr>';
			}
			foreach ( $results as $row ) {
				$return_html     .= "<tr data-id='" . $row->ID . "'>";
					$return_html .= '<td>' . $row->message . '</td>';
					$return_html .= '<td>' . $row->message_datetime . '</td>';
					$return_html .= '<td class="text-center"><span class="icon-delete-broadcast-message"><i class="fas fa-trash" title="Delete"></i></span></td>';
				$return_html     .= ' </tr>';
			}
			echo $return_html;
			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}

		wp_die();
	}

	/**
	 * AJAX handler to delete broadcast message.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If the action is not valid.
	 *
	 * @return void
	 */
	public function action_delete_broadcast_message() {
		$error   = null;
		$success = false;
		try {

			if ( ! isset( $_POST['data']['nonce'] ) || empty( $_POST['data']['nonce'] ) || ! wp_verify_nonce( $_POST['data']['nonce'], 'learndash-groups-plus-manage-broadcast-messages' ) ) {
				throw new Exception( 'Security issue' );
			}

			if ( learndash_is_group_leader_user() == false && ! current_user_can( 'administrator' ) ) {
				throw new Exception( "You don't have permission for this" );
			}
			global $wpdb;
			$broadcast_messages_tbl = $wpdb->prefix . Database::$broadcast_messages_tbl;
			$query                  = "DELETE FROM {$broadcast_messages_tbl} WHERE ID = {$_POST['data']['id']}";
			$wpdb->query( $query );
			$success = true;
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}
		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
			)
		);

		wp_die();
	}

	public function message_board( $atts, $content = null, $shortcode_name = null ) {
		if ( current_user_can( 'administrator' ) ) {
			// return;
		}

		ob_start();

		if ( ! is_user_logged_in() ) {
			return esc_html__( 'You must be logged in to see this messages.', 'learndash-groups-plus' );

		}
		$current_user_id = get_current_user_id();

		$group_ids = learndash_get_users_group_ids( $current_user_id );

		global $wpdb;
		$return_html            = "<div class='my_broadcast_messages_list'>";
		$broadcast_messages_tbl = $wpdb->prefix . Database::$broadcast_messages_tbl;

		$no_messages = '<div>' . esc_html__( 'No messages found.', 'learndash-groups-plus' ) . '</div>';
		if ( ! empty( $group_ids ) ) {
			$group_ids_string = implode( ',', $group_ids );
			$rows             = $wpdb->get_results( "SELECT * FROM {$broadcast_messages_tbl} WHERE group_id IN ( {$group_ids_string} ) ORDER BY ID DESC " );
			// print_r($rows);
			foreach ( $rows as $row ) {
				$return_html     .= '<div>';
					$return_html .= '<div><b>' . esc_html__( 'Sent On: ', 'learndash-groups-plus' ) . '</b>' . $row->message_datetime . '</div>';
					$return_html .= '<div>' . $row->message . '</div>';
				$return_html     .= '</div>';
			}
			if ( empty( $rows ) ) {
				$return_html .= $no_messages;
			}
		} else {
			$return_html .= $no_messages;
		}
		$return_html .= '</div>';
		echo $return_html;

		return ob_get_clean();
	}

	/**
	 * Add the Gravity form fields to the lesson and topic post type.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,mixed> $setting_option_fields Setting Option Fields.
	 * @param string              $settings_metabox_key  Setting Metabox Key.
	 *
	 * @return array<string,mixed>
	 */
	public function learndash_settings_fields( $setting_option_fields = array(), $settings_metabox_key = '' ) {
		if ( 'learndash-lesson-display-content-settings' === $settings_metabox_key || 'learndash-topic-display-content-settings' === $settings_metabox_key ) {
			$post_id = get_the_ID();

			// Add field for GF Page URL.
			$gf_page_url_value = get_post_meta( $post_id, 'gf-page-url', true );

			if ( empty( $gf_page_url_value ) ) {
				$gf_page_url_value = '';
			}

			if ( ! isset( $setting_option_fields['gf-page-url'] ) ) {
				$setting_option_fields['gf-page-url'] = array(
					'name'      => 'gf-page-url',
					'label'     => esc_html__( 'Gravity Form Page URL (Assignment Upload)', 'learndash-groups-plus' ),
					// Check the LD fields library under includes/settings/settings-fields.
					'type'      => 'text',
					'class'     => '-medium',
					'value'     => $gf_page_url_value,
					'help_text' => esc_html__( '', 'learndash-groups-plus' ),
					'default'   => '',
				);
			}

			// Add field for GF Form ID.
			$gf_form_id_value = get_post_meta( $post_id, 'gf-form-id', true );
			if ( empty( $gf_form_id_value ) ) {
						$gf_form_id_value = '';
			}

			if ( ! isset( $setting_option_fields['gf-form-id'] ) ) {
				$setting_option_fields['gf-form-id'] = array(
					'name'      => 'gf-form-id',
					'label'     => esc_html__( 'Gravity Form ID', 'learndash-groups-plus' ),
					// Check the LD fields library under includes/settings/settings-fields.
					'type'      => 'text',
					'class'     => '-small',
					'value'     => $gf_form_id_value,
					'help_text' => esc_html__( '', 'learndash-groups-plus' ),
					'default'   => '',
				);
			}

			// Add field for GF Form field ID.
			$gf_form_field_id_value = get_post_meta( $post_id, 'gf-form-field-id', true );
			if ( empty( $gf_form_field_id_value ) ) {
						$gf_form_field_id_value = '';
			}

			if ( ! isset( $setting_option_fields['gf-form-field-id'] ) ) {
				$setting_option_fields['gf-form-field-id'] = array(
					'name'      => 'gf-form-field-id',
					'label'     => esc_html__( 'Gravity Form Field ID (Field must have value of Competent or Satisfactory to Approve assignment)', 'learndash-groups-plus' ),
					// Check the LD fields library under includes/settings/settings-fields.
					'type'      => 'text',
					'class'     => '-small',
					'value'     => $gf_form_field_id_value,
					'help_text' => esc_html__( '', 'learndash-groups-plus' ),
					'default'   => '',
				);
			}

			// Add field for GF Form field of Team Member ID.
			$gf_form_field_team_member_id_value = get_post_meta( $post_id, 'gf-form-field-team-member-id', true );
			if ( empty( $gf_form_field_team_member_id_value ) ) {
						$gf_form_field_team_member_id_value = '';
			}

			if ( ! isset( $setting_option_fields['gf-form-field-team-member-id'] ) ) {
				$setting_option_fields['gf-form-field-team-member-id'] = array(
					'name'      => 'gf-form-field-team-member-id',
					'label'     => sprintf(
						esc_html__( 'Optional: Gravity Form %1$s Field ID (Field must be %2$s Dropdown)', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'team_member' ),
						learndash_get_custom_label( 'team_member' ),
					),
					// Check the LD fields library under includes/settings/settings-fields.
					'type'      => 'text',
					'class'     => '-small',
					'value'     => $gf_form_field_team_member_id_value,
					'help_text' => esc_html__( '', 'learndash-groups-plus' ),
					'default'   => '',
				);
			}
		}

		// Always return $setting_option_fields
		return $setting_option_fields;
	}

	public function gform_save_setting( $post_id = 0, $post = null, $update = false ) {
		/**
		 *  Lesson setting
		 *  */
		if ( isset( $_POST['learndash-lesson-display-content-settings']['gf-page-url'] ) ) {
			$gf_page_url_value = esc_attr( $_POST['learndash-lesson-display-content-settings']['gf-page-url'] );
			// Then update the post meta
			update_post_meta( $post_id, 'gf-page-url', $gf_page_url_value );
		}

		if ( isset( $_POST['learndash-lesson-display-content-settings']['gf-form-id'] ) ) {
			$gf_form_id_value = esc_attr( $_POST['learndash-lesson-display-content-settings']['gf-form-id'] );
			// Then update the post meta
			update_post_meta( $post_id, 'gf-form-id', $gf_form_id_value );
		}

		if ( isset( $_POST['learndash-lesson-display-content-settings']['gf-form-field-id'] ) ) {
			$gf_form_field_id_value = esc_attr( $_POST['learndash-lesson-display-content-settings']['gf-form-field-id'] );
			// Then update the post meta
			update_post_meta( $post_id, 'gf-form-field-id', $gf_form_field_id_value );
		}

		if ( isset( $_POST['learndash-lesson-display-content-settings']['gf-form-field-team-member-id'] ) ) {
			$gf_form_field_team_member_id_value = esc_attr( $_POST['learndash-lesson-display-content-settings']['gf-form-field-team-member-id'] );
			// Then update the post meta
			update_post_meta( $post_id, 'gf-form-field-team-member-id', $gf_form_field_team_member_id_value );
		}

		/**
		 *  Topic setting
		 *  */
		if ( isset( $_POST['learndash-topic-display-content-settings']['gf-page-url'] ) ) {
			$gf_page_url_value = esc_attr( $_POST['learndash-topic-display-content-settings']['gf-page-url'] );
			// Then update the post meta
			update_post_meta( $post_id, 'gf-page-url', $gf_page_url_value );
		}

		if ( isset( $_POST['learndash-topic-display-content-settings']['gf-form-id'] ) ) {
			$gf_form_id_value = esc_attr( $_POST['learndash-topic-display-content-settings']['gf-form-id'] );
			// Then update the post meta
			update_post_meta( $post_id, 'gf-form-id', $gf_form_id_value );
		}

		if ( isset( $_POST['learndash-topic-display-content-settings']['gf-form-field-id'] ) ) {
			$gf_form_field_id_value = esc_attr( $_POST['learndash-topic-display-content-settings']['gf-form-field-id'] );
			// Then update the post meta
			update_post_meta( $post_id, 'gf-form-field-id', $gf_form_field_id_value );
		}

		if ( isset( $_POST['learndash-topic-display-content-settings']['gf-form-field-team-member-id'] ) ) {
			$gf_form_field_team_member_id_value = esc_attr( $_POST['learndash-topic-display-content-settings']['gf-form-field-team-member-id'] );
			// Then update the post meta
			update_post_meta( $post_id, 'gf-form-field-team-member-id', $gf_form_field_team_member_id_value );
		}
	}

	public function gform_entry_created( $entry, $form ) {
		// echo "<pre>";
		// print_r($form);
		// echo "</pre>";
		$gf_hidden_assignment_id_field = 0;
		$gf_hidden_team_member_id_field    = 0;
		// Let's iterate through the "fields" array and find our fields there
		foreach ( $form['fields'] as $field ) {
			if ( isset( $field['inputName'] ) && $field['inputName'] === 'assignment_id' ) {
				$gf_hidden_assignment_id_field = $field['id'];
			} elseif ( isset( $field['inputName'] ) && $field['inputName'] === 'team_member_id' ) {
				$gf_hidden_team_member_id_field = $field['id'];
			}
		}

		if ( $gf_hidden_assignment_id_field ) {
			$ld_assignment_id = rgar( $entry, $gf_hidden_assignment_id_field );

			if ( $ld_assignment_id ) {

				$ld_lesson_id = get_post_meta( $ld_assignment_id, 'lesson_id', true );

				$gf_page_url      = get_post_meta( $ld_lesson_id, 'gf-page-url', true );
				$gf_form_id       = get_post_meta( $ld_lesson_id, 'gf-form-id', true );
				$gf_form_field_id = get_post_meta( $ld_lesson_id, 'gf-form-field-id', true );

				// Check if the hidden team_member_id field set to form. if set then use that user id, else use gf-form-field-team-member-id meta set under the post(lesson/topic) settings
				if ( isset( $gf_hidden_team_member_id_field ) && is_int( $gf_hidden_team_member_id_field ) ) {
					$gf_form_field_team_member_id = $gf_hidden_team_member_id_field;
				} else {
					$gf_form_field_team_member_id = get_post_meta( $ld_lesson_id, 'gf-form-field-team-member-id', true );
				}
				$user_id = rgar( $entry, $gf_form_field_team_member_id );

				if ( 'on' == learndash_get_setting( $ld_lesson_id, 'lesson_assignment_upload' ) && ! empty( $gf_page_url ) && ! empty( $gf_form_id ) && ! empty( $gf_form_field_id ) ) {

					$search_criteria = array();

					// $search_criteria['field_filters'][] = array( 'key' => 'created_by', 'value' => $user_id );
					$search_criteria['field_filters'][] = array(
						'key'   => $gf_form_field_team_member_id,
						'value' => $user_id,
					);

					$search_criteria['field_filters'][] = array(
						'key'   => $gf_hidden_assignment_id_field,
						'value' => $ld_assignment_id,
					);

					$entries = \GFAPI::get_entries( $gf_form_id, $search_criteria );

					$choice_text = ''; // not graded;
					if ( is_array( $entries ) && ! empty( $entries ) ) {
						$selected_value = $entries[0][ $gf_form_field_id ];
						$field_one      = \GFAPI::get_field( $gf_form_id, $gf_form_field_id );
						if ( $selected_value && is_array( $field_one->choices ) ) {

							$choices_key = array_search( $selected_value, array_column( $field_one->choices, 'value' ) );
							$choice_text = $field_one->choices[ $choices_key ]['text'];

						}

						if ( $choice_text === 'Competent' || $choice_text === 'Satisfactory' ) {
							// Approve assignment
							learndash_approve_assignment_by_id( $ld_assignment_id );
						}
					}
				}
			}
		}
	}
}
