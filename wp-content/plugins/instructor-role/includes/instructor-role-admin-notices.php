<?php
/**
 * Handling admin notices for the plugin
 *
 * @link https://learndash.com
 * @since 3.5.8
 *
 * @package LearnDash\Instructor_Role
 */

namespace InstructorRole\Includes;

defined( 'ABSPATH' ) || exit;

/**
 * Handling admin notices for the plugin
 *
 * @since 3.5.8
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */
class Instructor_Role_Admin_Notices {
	/**
	 * Constructor.
	 *
	 * @since 3.5.8
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'show_admin_notice' ] );
	}

	/**
	 * Show admin notices.
	 *
	 * @since 3.5.8
	 * @access public
	 */
	public function show_admin_notice() {
		if ( isset( $_GET['ir_dismiss_survey_notice'] ) && ! empty( $_GET['ir_dismiss_survey_notice'] ) ) {
			update_option( 'ir_dismiss_survey_notice', 'yes', false );
		}

		// Add buddypress admin notice.
		add_action( 'admin_notices', [ $this, 'display_buddypress_activation_notice' ] );
		add_action( 'network_admin_notices', [ $this, 'display_buddypress_activation_notice' ] );
	}

	/**
	 * Display admin survey notice.
	 *
	 * @deprecated 5.9.1
	 *
	 * @since 3.5.8
	 *
	 * @return void
	 */
	public function display_admin_survey_notice() {
		_deprecated_function( __METHOD__, '5.9.1' );
	}

	/**
	 * Displays BuddyPress activation notice.
	 *
	 * @return void
	 */
	public function display_buddypress_activation_notice() {
		$ir_admin_settings = get_option( '_wdmir_admin_settings', [] );
		if ( ! current_user_can( 'activate_plugins' ) || empty( $ir_admin_settings ) || empty( $ir_admin_settings['ir_student_communication_check'] ) ) {
			return;
		}

		// Display BuddyPress deactivation message.
		if ( ! function_exists( 'bp_is_active' ) ) {
			$bp_plugins_url = is_network_admin() ? network_admin_url( 'plugins.php' ) : admin_url( 'plugins.php' );
			$link_plugins   = sprintf( "<a href='%s'>%s</a>", $bp_plugins_url, __( 'activate', 'wdm_instructor_role' ) );
			?>

			<div id="message" class="error notice">
				<p><strong><?php esc_html_e( 'Instructor Role: Student Teacher Communication is disabled.', 'wdm_instructor_role' ); ?></strong></p>
				<p>
				<?php
				printf(
					/* translators: activate link */
					esc_html__( 'The Student Teacher Communication feature can\'t work without the BuddyPress plugin. Please %s BuddyPress to re-enable the module.', 'wdm_instructor_role' ),
					$link_plugins
				);
				?>
					</p>
			</div>
			<?php
		}

		// Display BuddyPress messages component deactivation message.
		if ( function_exists( 'bp_is_active' ) && ! bp_is_active( 'messages' ) ) {
			$bp_plugins_url = bp_get_admin_url( add_query_arg( [ 'page' => 'bp-components' ], 'admin.php' ) );
			$link_plugins   = sprintf( "<a href='%s'>%s</a>", $bp_plugins_url, __( 'activate', 'wdm_instructor_role' ) );
			?>

			<div id="message" class="error notice">
				<p><strong><?php esc_html_e( 'Instructor Role: Student Teacher Communication is disabled.', 'wdm_instructor_role' ); ?></strong></p>
				<p>
				<?php
				printf(
					/* translators: activate link */
					esc_html__( 'The Student Teacher Communication feature can\'t work without the Private Messaging component in BuddyPress plugin. Please %s BuddyPress Private Messaging to re-enable the module.', 'wdm_instructor_role' ),
					$link_plugins
				);
				?>
					</p>
			</div>
			<?php
		}
	}
}

new Instructor_Role_Admin_Notices();
