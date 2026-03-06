<?php
/**
 * Settings page.
 *
 * @since 1.0.0
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore ramaining tablinks tabcontent shortcodes sshortcodes slearndash stemplates stemplate schild screate autologin childgroup forminp FERPA qmark WPINC classrom titledesc pagenow templating nopaging
 */

namespace LearnDash\Groups_Plus;

if ( ! defined( 'WPINC' ) ) {
	exit;
}

use LearnDash\Groups_Plus\Utility\Background_Process\Migrate_Groups_Batch;
use LearnDash\Groups_Plus\Utility\Background_Process\Downgrade_Groups_Batch;
use LearnDash\Groups_Plus\Utility\Database;
use LearnDash\Groups_Plus\Utility\SharedFunctions;

/**
 * Settings class.
 *
 * @since 1.0.0
 */
class Settings {
	/**
	 * Initialize the settings class.
	 */
	public static function init() {
		self::register_globals();

		// General Tab
		add_action( 'general_create_setting', array( __CLASS__, 'general_create_setting' ) );
		add_action( 'general_setting_save_field', array( __CLASS__, 'general_setting_save_field' ) );
		add_action( 'general_setting', array( __CLASS__, 'general_setting' ) );

		// Settings tab
		global $obj_migrate_groups_batch;
		add_action( 'sett_create_setting', array( __CLASS__, 'sett_create_setting' ) );
		add_action( 'sett_setting_save_field', array( __CLASS__, 'sett_setting_save_field' ) );
		add_action( 'sett_setting', array( __CLASS__, 'sett_setting' ) );
		add_action( 'admin_notices', array( __CLASS__, 'sett_setting_admin_notice' ) );

		// Email tab
		add_action( 'email_create_setting', array( __CLASS__, 'email_create_setting' ) );
		add_action( 'email_setting_save_field', array( __CLASS__, 'email_setting_save_field' ) );
		add_action( 'email_setting', array( __CLASS__, 'email_setting' ) );

		// Design Tab
		add_action( 'design_create_setting', array( __CLASS__, 'design_create_setting' ) );
		add_action( 'design_setting_save_field', array( __CLASS__, 'design_setting_save_field' ) );
		add_action( 'design_setting', array( __CLASS__, 'design_setting' ) );

		// Developers tab
		add_action( 'hooks_setting', array( __CLASS__, 'hooks_setting' ) );

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'media_lib_uploader_enqueue' ) );
	}

	public static function general_create_setting() {
		?>
		<h3><?php esc_html_e( 'Setting Up Groups Plus', 'learndash-groups-plus' ); ?></h3>

		<div class="ld-adm-container" style="display:flex;flex-wrap:wrap;">
			<div class="options-panel">
				<div>
					<h2><?php esc_html_e( 'Before you begin', 'learndash-groups-plus' ); ?></h2>
					<p>
						<?php printf(
							esc_html__( 'LearnDash LMS - Groups Plus addon uses the native LearnDash Groups or UnCanny Groups to apply %1$s and %2$s in your website for %3$s to manage and create %4$s, %5$s, and %6$s.', 'learndash-groups-plus' ),
							learndash_get_custom_label( 'organizations' ),
							learndash_get_custom_label( 'teams' ),
							learndash_get_custom_label( 'lead_organizers' ),
							learndash_get_custom_label_lower( 'teams' ),
							learndash_get_custom_label_lower( 'team_leaders' ),
							learndash_get_custom_label_lower( 'team_members' ),
						); ?>
						<?php printf(
							esc_html__( 'LearnDash LMS - Groups Plus addon also includes a detailed reporting facility for %s and %s to report on only their %s.', 'learndash-groups-plus' ),
							learndash_get_custom_label_lower( 'lead_organizers' ),
							learndash_get_custom_label_lower( 'team_leaders' ),
							learndash_get_custom_label_lower( 'team_members' )
						); ?>
					</p>
				</div>
				<div class="panel">
					<center>
						<h3><?php _e( 'Important settings to consider before performing the steps below!', 'learndash-groups-plus' ); ?>
						</h3>
					</center>

					<div class="panel-item-left">
						<button onclick="document.getElementById('ld-id01').style.display='block'" class="ld-button"><img
								id="ld-adm-qmark"
								src="<?php echo LEARNDASH_GROUPS_PLUS_URL . './build/images/q-mark.png'; ?>" /></button>
						<div style="display:inline-block;vertical-align:middle;">
							<h3><?php printf(
								esc_html__( 'Convert to %s', 'learndash-groups-plus' ),
								learndash_get_custom_label( 'team' )
							) ?></h3>
							<p>
								<strong><?php printf(
									esc_html__( 'Convert your LearnDash %1$s to %2$s.', 'learndash-groups-plus', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'groups' ),
									learndash_get_custom_label( 'teams' ),
								); ?></strong>
							</p>
						</div>
					</div>

					<div class="panel-item-right">
						<button onclick="document.getElementById('ld-id02').style.display='block'" class="ld-button"><img
								id="ld-adm-qmark"
								src="<?php echo LEARNDASH_GROUPS_PLUS_URL . './build/images/q-mark.png'; ?>" /></button>
						<div style="display:inline-block;vertical-align:middle;">
							<h3><?php printf(
								esc_html__( '%s use seats', 'learndash-groups-plus' ),
								learndash_get_custom_label( 'team_leaders' )
							) ?></h3>
							<p>
								<strong>
									<?php printf(
										esc_html__( 'Limit the number of %1$s that will not use %2$s seats per %3$s.', 'learndash-groups-plus' ),
										learndash_get_custom_label_lower( 'team_leaders' ),
										learndash_get_custom_label_lower( 'team_member' ),
										learndash_get_custom_label_lower( 'team' ),
									); ?>
								</strong>
							</p>
						</div>
					</div>

					<div class="panel-item-left">
						<button onclick="document.getElementById('ld-id03').style.display='block'" class="ld-button"><img
								id="ld-adm-qmark"
								src="<?php echo LEARNDASH_GROUPS_PLUS_URL . './build/images/q-mark.png'; ?>" /></button>
						<div style="display:inline-block;vertical-align:middle;">
							<h3><?php _e( 'Use UnCanny Group seats at will', 'learndash-groups-plus' ); ?>
							<p>
								<?php // translators: organizations label. ?>
								<strong><?php printf( esc_html__( 'Sell %s and Seats using the UnCanny Groups plugin.', 'learndash-groups-plus' ), learndash_get_custom_label( 'organizations' ) ); ?></strong>
							</p>
							</h3>
						</div>
					</div>

					<div class="panel-item-right">
						<button onclick="document.getElementById('ld-id04').style.display='block'" class="ld-button"><img
								id="ld-adm-qmark"
								src="<?php echo LEARNDASH_GROUPS_PLUS_URL . './build/images/q-mark.png'; ?>" /></button>
						<div style="display:inline-block;vertical-align:middle;">
							<h3>
								<?php printf(
									esc_html__( '%1$s issue %2$s certificates', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'group_leader' ),
									learndash_get_custom_label_lower( 'course' )
								) ?>
							</h3>
							<p>
								<strong>
									<?php printf(
										__( 'Block %1$s from viewing and downloading their own %2$s certificates.', 'learndash-groups-plus' ),
										learndash_get_custom_label_lower( 'team_members' ),
										learndash_get_custom_label_lower( 'course' ),
									); ?>
								</strong>
							</p>
						</div>
					</div>

					<div class="panel-item-left">
						<button onclick="document.getElementById('ld-id05').style.display='block'" class="ld-button"><img
								id="ld-adm-qmark"
								src="<?php echo LEARNDASH_GROUPS_PLUS_URL . './build/images/q-mark.png'; ?>" /></button>
						<div style="display:inline-block;vertical-align:middle;">
							<h3><?php esc_html_e( 'Create global default email templates', 'learndash-groups-plus' ); ?></h3>
							<p>
								<strong>
									<?php printf(
										esc_html__( 'Create welcome email templates for %s invitation emails.', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'team' )
									); ?>
								</strong>
							</p>
						</div>
					</div>

					<div class="panel-item-right">
						<button onclick="document.getElementById('ld-id06').style.display='block'" class="ld-button"><img
								id="ld-adm-qmark"
								src="<?php echo LEARNDASH_GROUPS_PLUS_URL . './build/images/q-mark.png'; ?>" /></button>
						<div style="display:inline-block;vertical-align:middle;">
							<h3><?php esc_html_e( 'Show full quiz history', 'learndash-groups-plus' ); ?></h3>
							<p>
								<strong>
									<?php printf(
										esc_html__( 'Optionally show %1$s full quiz history in the detailed %2$s report.', 'learndash-groups-plus' ),
										learndash_get_custom_label_lower( 'team_members' ),
										learndash_get_custom_label_lower( 'team_member' )
									); ?>
								</strong>
							</p>
						</div>
					</div>

					<div class="panel-item-left">
						<button onclick="document.getElementById('ld-id07').style.display='block'" class="ld-button"><img
								id="ld-adm-qmark"
								src="<?php echo LEARNDASH_GROUPS_PLUS_URL . './build/images/q-mark.png'; ?>" /></button>
						<div style="display:inline-block;vertical-align:middle;">
							<h3><?php esc_html_e( 'Show all users', 'learndash-groups-plus' ); ?></h3>
							<p>
								<strong>
									<?php printf(
										esc_html__( '%1$s and %2$s can see all %3$s and %4$s.', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'lead_organizers' ),
										learndash_get_custom_label( 'team_leaders' ),
										learndash_get_custom_label_lower( 'team_leaders' ),
										learndash_get_custom_label_lower( 'team_members' ),
									); ?>
								</strong>
							</p>
						</div>
					</div>
					<div class="panel-item-right">
						<button onclick="document.getElementById('ld-id08').style.display='block'" class="ld-button"><img
								id="ld-adm-qmark"
								src="<?php echo LEARNDASH_GROUPS_PLUS_URL . './build/images/q-mark.png'; ?>" /></button>
						<div style="display:inline-block;vertical-align:middle;">
							<?php // translators: organizations label. ?>
							<h3><?php printf( esc_html__( 'Sell %s and Seats using WooCommerce', 'learndash-groups-plus' ), learndash_get_custom_label( 'organizations' ) ); ?></h3>
							<p>
								<?php // translators: organization label. ?>
								<strong><?php printf( esc_html__( 'Create %s and Seat products in WooCommerce.', 'learndash-groups-plus' ), learndash_get_custom_label_lower( 'organization' ) ) ?></strong>
							</p>
						</div>
					</div>

					<div id="ld-id01">
						<div class="ld-modal-content ld-modal-content-1 ld-animate-zoom ld-card-1">
							<header class="ld-container ld-teal">
								<span onclick="document.getElementById('ld-id01').style.display='none'"
									class="ld-display-topright"><img id="ld-adm-close"
										src="<?php echo LEARNDASH_GROUPS_PLUS_URL . './build/images/close-modal.png'; ?>" /></span>
								<center>
									<h3>
										<?php printf(
											esc_html__( 'Convert to %s', 'learndash-groups-plus' ),
											learndash_get_custom_label( 'team' )
										); ?>
									</h3>
								</center>
							</header>
							<div class="ld-container">
								<p>
									<?php printf(
										// translators: 1$: opening HTML strong tag, 2$: closing HTML strong tag, 3$: opening HTML strong tag, 4$: closing HTML strong tag.
										esc_html__( '%1$sExecuted at the bottom of the Settings tab above%2$s, this process will switch on your %3$sLearnDash LMS > Groups > Settings > Group Hierarchy%4$s for you if it is not already turned on.', 'learndash-groups-plus' ),
										'<strong>',
										'</strong>',
										'<strong>',
										'</strong>',
									); ?>
								</p>
								<p>
									<?php printf(
										// translators: organization label.
										esc_html__( 'Next, this process will automatically convert your LearnDash primary groups (and UnCanny Groups if you use them) to a type of %s structure.', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'organization' )
									); ?>
								</p>
								<p>
									<?php printf(
										// translators: 1$: team member label, 2$: group leader label, 3$: course label, 4$: team label.
										esc_html__( 'Finally, it will copy your %1$s(s), %2$s(s) and %2$s(s) in the primary group to a %4$s of the same name.', 'learndash-groups-plus' ),
										learndash_get_custom_label_lower( 'team_member' ),
										learndash_get_custom_label_lower( 'group_leader' ),
										learndash_get_custom_label_lower( 'course' ),
										learndash_get_custom_label_lower( 'team' ),
									); ?>
								</p>
							</div>
						</div> <!-- ld-modal-content-1 -->
					</div> <!-- ld-id01 -->

					<div id="ld-id02">
						<div class="ld-modal-content ld-modal-content-2 ld-animate-zoom ld-card-2">
							<header class="ld-container ld-teal">
								<span onclick="document.getElementById('ld-id02').style.display='none'"
									class="ld-display-topright"><img id="ld-adm-close"
										src="<?php echo LEARNDASH_GROUPS_PLUS_URL . './build/images/close-modal.png'; ?>" /></span>
								<center>
									<h3>
										<?php printf(
											esc_html__( '%s use seats', 'learndash-groups-plus' ),
											learndash_get_custom_label( 'team_leaders' )
										); ?>
									</h3>
								</center>
							</header>
							<div class="ld-container">
								<p>
									<?php printf(
										// translators: 1$: opening HTML strong tag, 2$: closing HTML strong tag, 3$: team leaders label, 4$: team label, 5$: team members label.
										esc_html__( '%1$sSet in the Settings tab above%2$s, this setting will give you the option to have %3$s in any %4$s consume %5$s seats.', 'learndash-groups-plus' ),
										'<strong>',
										'</strong>',
										learndash_get_custom_label_lower( 'team_leaders' ),
										learndash_get_custom_label_lower( 'team' ),
										learndash_get_custom_label_lower( 'team_members' ),
									); ?>
								</p>
								<p>
									<?php printf(
										// translators: 1$: team leader label, 2$: organization label, 3$: team member label.
										esc_html__( 'Any %1$s beyond the set number you enter in each Primary Group (%2$s) will take a seat from the total number of %3$s seats.', 'learndash-groups-plus' ),
										learndash_get_custom_label_lower( 'team_leader' ),
										learndash_get_custom_label( 'organization' ),
										learndash_get_custom_label( 'team_member' ),
									); ?>
								</p>
								<p>
									<?php printf(
										// translators: 1$: opening HTML tag, 2$: closing HTML tag.
										esc_html__( '%1$sThis is set to 0 free seats by default%2$s', 'learndash-groups-plus' ),
										'<strong>',
										'</strong>',
									); ?>
								</p>
							</div>
						</div> <!-- ld-modal-content-2 -->
					</div> <!-- ld-id02 -->

					<div id="ld-id03">
						<div class="ld-modal-content ld-modal-content-3 ld-animate-zoom ld-card-3">
							<header class="ld-container ld-teal">
								<span onclick="document.getElementById('ld-id03').style.display='none'"
									class="ld-display-topright"><img id="ld-adm-close"
										src="<?php echo LEARNDASH_GROUPS_PLUS_URL . './build/images/close-modal.png'; ?>" /></span>
								<center>
									<h3><?php _e( 'Use UnCanny Group seats at will', 'learndash-groups-plus' ); ?></h3>
								</center>
							</header>
							<div class="ld-container">
								<p>
									<?php printf(
										esc_html__( '%1$sSet in the Settings tab above%2$s, this setting enables you to not have to create %3$s %4$s manually, rather Groups Plus addon will be able to use UnCanny Groups selectively.', 'learndash-groups-plus' ),
										'<strong>',
										'</strong>',
										learndash_get_custom_label( 'team' ),
										learndash_get_custom_label_lower( 'groups' ),
									); ?>
								</p>
								<p>
									<?php printf(
										esc_html__( 'You will be able to use a mix of %1$s %2$s and UnCanny groups.', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'team' ),
										learndash_get_custom_label_lower( 'groups' ),
									); ?>
								</p>
								<p>
									<?php printf(
										esc_html__( 'After selecting this checkbox, if any of your %1$s are configured in UnCanny groups, you will not be asked to set any %2$s seats in the %3$s, nor will you need to create a %4$s.', 'learndash-groups-plus' ),
										learndash_get_custom_label_lower( 'groups' ),
										learndash_get_custom_label( 'team_member' ),
										learndash_get_custom_label_lower( 'group' ),
										learndash_get_custom_label_lower( 'group' ),
									); ?>
								</p>
							</div>
						</div> <!-- ld-modal-content-3 -->
					</div> <!-- ld-id03 -->

					<div id="ld-id04">
						<div class="ld-modal-content ld-modal-content-4 ld-animate-zoom ld-card-4">
							<header class="ld-container ld-teal">
								<span onclick="document.getElementById('ld-id04').style.display='none'"
									class="ld-display-topright"><img id="ld-adm-close"
										src="<?php echo LEARNDASH_GROUPS_PLUS_URL . './build/images/close-modal.png'; ?>" /></span>
								<center>
									<h3>
										<?php printf(
											esc_html__( 'Disallow %s certificate access', 'learndash-groups-plus' ),
											learndash_get_custom_label_lower( 'team_member' )
										); ?>
									</h3>
								</center>
							</header>
							<div class="ld-container">
								<p>
									<?php printf(
										esc_html__( '%1$sSet in the Settings tab above%2$s, this setting enables you to globally block all %3$s\' %4$s access to their course certificate.', 'learndash-groups-plus' ),
										'<strong>',
										'</strong>',
										learndash_get_custom_label_lower( 'teams' ),
										learndash_get_custom_label_lower( 'team_members' )
									); ?>
								</p>
								<p>
									<?php printf(
										esc_html__( 'After selecting this checkbox, all %1$s\' %2$s will lose access to their %3$s certificates.%4$sAdministrators and %5$s (%6$s and %7$s) will then be the only users who can view and print certificates for the %8$s.', 'learndash-groups-plus' ),
										learndash_get_custom_label_lower( 'teams' ),
										learndash_get_custom_label_lower( 'team_members' ),
										learndash_get_custom_label_lower( 'courses' ),
										'<br/>',
										learndash_get_custom_label( 'group_leader' ),
										learndash_get_custom_label( 'lead_organizer' ),
										learndash_get_custom_label( 'team_leaders' ),
										learndash_get_custom_label_lower( 'team_members' )
									); ?>
								</p>
								<p>
									<?php esc_html_e( 'You can use the LearnDash Certificate shortcode to override this setting in case you prefer to have some exclusions with this setting enabled.', 'learndash-groups-plus' ); ?>
								</p>
							</div>
						</div> <!-- ld-modal-content-4 -->
					</div> <!-- ld-id04 -->

					<div id="ld-id05">
						<div class="ld-modal-content ld-modal-content-5 ld-animate-zoom ld-card-5">
							<header class="ld-container ld-teal">
								<span onclick="document.getElementById('ld-id05').style.display='none'"
									class="ld-display-topright"><img id="ld-adm-close"
										src="<?php echo LEARNDASH_GROUPS_PLUS_URL . './build/images/close-modal.png'; ?>" /></span>
								<center>
									<h3><?php _e( 'Create global default email templates', 'learndash-groups-plus' ); ?></h3>
								</center>
							</header>
							<div class="ld-container">
								<p>
									<?php printf(
										esc_html__( '%1$sSet in the Global Email tab above%2$s, this setting lets you create default %3$s and %4$s welcome email templates so your %5$s and %6$s do not need to.', 'learndash-groups-plus' ),
										'<strong>',
										'</strong>',
										learndash_get_custom_label( 'team_leader' ),
										learndash_get_custom_label( 'team_member' ),
										learndash_get_custom_label( 'lead_organizers' ),
										learndash_get_custom_label( 'team_leaders' ),
									); ?>
								</p>
								<p>
									<?php printf(
										esc_html__( 'If you create these default email templates and later your %1$s and %2$s create their own personal ones, those personal ones will override your default ones.', 'learndash-groups-plus' ),
										learndash_get_custom_label_lower( 'lead_organizers' ),
										learndash_get_custom_label_lower( 'team_leaders' ),
									); ?>
								</p>
							</div>
						</div> <!-- ld-modal-content-5 -->
					</div> <!-- ld-id05 -->

					<div id="ld-id06">
						<div class="ld-modal-content ld-modal-content-6 ld-animate-zoom ld-card-6">
							<header class="ld-container ld-teal">
								<span onclick="document.getElementById('ld-id06').style.display='none'"
									class="ld-display-topright"><img id="ld-adm-close"
										src="<?php echo LEARNDASH_GROUPS_PLUS_URL . './build/images/close-modal.png'; ?>" /></span>
								<center>
									<h3><?php esc_html_e( 'Show full quiz history and all users', 'learndash-groups-plus' ); ?></h3>
								</center>
							</header>
							<div class="ld-container">
								<p>
									<strong>
										<?php printf(
											esc_html__( '%1$sSet in the Settings tab above%2$s, these settings let you 1) Show the complete quiz history of %3$s in the detailed %4$s report, and 2) Allow %5$s and %6$s find all users that have the subscriber and group leader roles in the system.', 'learndash-groups-plus' ),
											'<strong>',
											'</strong>',
											learndash_get_custom_label_lower( 'team_members' ),
											learndash_get_custom_label_lower( 'team_member' ),
											learndash_get_custom_label( 'lead_organizers' ),
											learndash_get_custom_label( 'team_leaders' ),
										); ?>
									</strong>
								</p>
							</div>
						</div> <!-- ld-modal-content-6 -->
					</div> <!-- ld-id06 -->

					<div id="ld-id07">
						<div class="ld-modal-content ld-modal-content-7 ld-animate-zoom ld-card-7">
							<header class="ld-container ld-teal">
								<span onclick="document.getElementById('ld-id07').style.display='none'"
									class="ld-display-topright"><img id="ld-adm-close"
										src="<?php echo LEARNDASH_GROUPS_PLUS_URL . './build/images/close-modal.png'; ?>" /></span>
								<center>
									<h3><?php _e( 'Show all users', 'learndash-groups-plus' ); ?></h3>
								</center>
							</header>
							<div class="ld-container">
								<p>
									<?php printf(
										esc_html__( '%1$sSet in the Settings tab above%2$s, this setting lets %3$s and %4$s see all user that are group leaders (i.e. %5$s) and subscribers (i.e. %6$s) throughout the platform. These users will be shown in the Add %7$s and Add %8$s modals when you select the %9$s/%10$s already exists checkboxes.', 'learndash-groups-plus' ),
										'<strong>',
										'</strong>',
										learndash_get_custom_label_lower( 'lead_organizers' ),
										learndash_get_custom_label_lower( 'team_leaders' ),
										learndash_get_custom_label_lower( 'team_leaders' ),
										learndash_get_custom_label_lower( 'team_members' ),
										learndash_get_custom_label( 'team_leader' ),
										learndash_get_custom_label( 'team_member' ),
										learndash_get_custom_label( 'team_leader' ),
										learndash_get_custom_label( 'team_member' )
									); ?>
								</p>
							</div>
						</div> <!-- ld-modal-content-7 -->
					</div> <!-- ld-id07 -->

					<div id="ld-id08">
						<div class="ld-modal-content ld-modal-content-8 ld-animate-zoom ld-card-8">
							<header class="ld-container ld-teal">
								<span onclick="document.getElementById('ld-id08').style.display='none'"
									class="ld-display-topright"><img id="ld-adm-close"
										src="<?php echo LEARNDASH_GROUPS_PLUS_URL . './build/images/close-modal.png'; ?>" /></span>
								<center>
									<h3>
										<?php printf(
											esc_html__( 'Sell %s and seats using WooCommerce', 'learndash-groups-plus' ),
											learndash_get_custom_label_lower( 'organizations' )
										); ?>
									</h3>
								</center>
							</header>
							<div class="ld-container">
								<p>
									<?php printf(
										esc_html__( '%1$sSet in the Settings tab above%2$s, this setting enables you to sell %3$s and Seats in %4$s. This setting can work along side any other group sales plugins and does not require using any other plugins beyond LearnDash LMS, WooCommerce and LearnDash LMS - Groups Plus addon.', 'learndash-groups-plus' ),
										'<strong>',
										'</strong>',
										learndash_get_custom_label( 'organizations' ),
										learndash_get_custom_label( 'team' ),
									); ?>
								</p>
								<p>
									<?php printf(
										esc_html__( 'If you also sell %1$s and %2$s seats as subscriptions, this feature lets you do this too.', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'organizations' ),
										learndash_get_custom_label( 'team' ),
									); ?>
								</p>
							</div>
						</div> <!-- ld-modal-content-8 -->
					</div> <!-- ld-id08 -->
				</div><!-- panel -->
			</div><!-- options-panel -->
		</div><!-- ld-adm-container -->

		<div class="ld-adm-container" style="display:flex;flex-wrap:wrap;">
			<div class="ld-adm-item">
				<h2><?php esc_html_e( 'Step 1', 'learndash-groups-plus' ); ?></h2>
				<h3><?php esc_html_e( 'Create a Group Leader for clients', 'learndash-groups-plus' ); ?></h3>
				<p>
					<?php printf(
						esc_html__( 'For best results, %1$screate at least one Group Leader user for each Primary Group (%2$s)%3$s to act as the %4$s%5$s%6$s type user of that single %7$sPrimary Group (%8$s)%9$s and its %10$sChild Groups (%11$s)%12$s. If your %13$sChild Groups (%14$s)%15$s have group leaders, they will become %16$s%17$s%18$s.', 'learndash-groups-plus' ),
						'<strong>',
						learndash_get_custom_label( 'organization' ),
						'</strong>',
						'<strong>',
						learndash_get_custom_label( 'lead_organizer' ),
						'</strong>',
						'<strong>',
						learndash_get_custom_label( 'organization' ),
						'</strong>',
						'<strong>',
						learndash_get_custom_label( 'teams' ),
						'</strong>',
						'<strong>',
						learndash_get_custom_label( 'teams' ),
						'</strong>',
						'<strong>',
						learndash_get_custom_label( 'team_leaders' ),
						'</strong>'
					); ?>
				</p>
				<p>
					<?php printf(
						esc_html__( '%1$s%2$s%3$s can be assigned to any number of %4$s%5$s%6$s. If they are %7$s%8$s%9$s of multiple %10$s%11$s%12$s, a drop-down menu will be shown for them to select any of their %13$s%14$s%15$s, else the menu will not be present.', 'learndash-groups-plus' ),
						'<strong>',
						learndash_get_custom_label( 'lead_organizers' ),
						'</strong>',
						'<strong>',
						learndash_get_custom_label( 'organizations' ),
						'</strong>',
						'<strong>',
						learndash_get_custom_label( 'lead_organizers' ),
						'</strong>',
						'<strong>',
						learndash_get_custom_label( 'organizations' ),
						'</strong>',
						'<strong>',
						learndash_get_custom_label( 'organizations' ),
						'</strong>',
					); ?>
				</p>
				<p>
					<?php printf(
						esc_html__( '%1$sYour administrative users will be able to access all %2$s.%3$s', 	'learndash-groups-plus' ),
						'<strong>',
						learndash_get_custom_label( 'organizations' ),
						'</strong>'
					); ?>
				</p>
			</div> <!-- ld-adm-item -->

			<div class="ld-adm-item">
				<h2><?php esc_html_e( 'Step 2', 'learndash-groups-plus' ); ?></h2>
				<h3>
					<?php printf(
						esc_html__( 'Create/Edit a %s', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'group' )
					); ?>
				</h3>
				<p>
					<?php printf(
						esc_html__( 'Create or edit an existing %1$s in your dashboard under %2$sLearnDash LMS > %3$s%4$s page', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'group' ),
						'<strong>',
						learndash_get_custom_label( 'groups' ),
						'</strong>'
					); ?>
				</p>
				<p>
					<?php printf(
						esc_html__(
							'After activating this plugin, you have a new page created in your web site. You can visit this page at: %1$s%2$s%3$s',
							'learndash-groups-plus'
						),
						'<a href="',
						get_site_url() . '/learndash-groups-plus/',
						'" target="_blank">' . get_site_url() . '/learndash-groups-plus/' . '</a>'
					); ?>
				</p>
				<p>
					<?php printf(
						esc_html__( 'Refer to Step 4 for your %s admin and reporting shortcode options.', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'team' )
					); ?>
				</p>
			</div> <!-- ld-adm-item -->

			<div class="ld-adm-item">
				<h2><?php esc_html_e( 'Step 3', 'learndash-groups-plus' ); ?></h2>
				<h3><?php esc_html_e( 'Add Seats', 'learndash-groups-plus' ); ?></h3>
				<p>
					<?php printf(
						esc_html__( 'In the same tab you edit the group title, locate the %1$sNumber of %2$s%3$s metabox.', 'learndash-groups-plus' ),
						'<strong>',
						learndash_get_custom_label( 'team_members' ),
						'</strong>'
					) ?>
				</p>
				<p>
					<?php printf(
						esc_html__( 'Enter the number of %1$s you want to provide for all %2$s in this %3$s. Save the %4$s.', 'learndash-groups-plus' ),
						learndash_get_custom_label_lower( 'team_members' ),
						learndash_get_custom_label_lower( 'teams' ),
						learndash_get_custom_label_lower( 'group' ),
						learndash_get_custom_label_lower( 'group' )
					) ?>
				</p>
				<p>
					<?php printf(
						esc_html__( '%1$sIf the option "%2$s Use Seats" from Step 1 is selected%3$s, you will see the "Number of %4$s not using seats" field appear only in Primary %5$s. Enter the number of %6$s that will not consume a seat in the %7$s. Save the %8$s.', 'learndash-groups-plus' ),
						'<strong>',
						learndash_get_custom_label( 'team_leaders' ),
						'</strong>',
						learndash_get_custom_label( 'team_leaders' ),
						learndash_get_custom_label( 'groups' ),
						learndash_get_custom_label_lower( 'team_leaders' ),
						learndash_get_custom_label_lower( 'group' ),
						learndash_get_custom_label_lower( 'group' ),
					) ?>
				</p>
			</div> <!-- ld-adm-item -->

			<div class="ld-adm-item">
				<h2><?php esc_html_e( 'Step 4', 'learndash-groups-plus' ); ?></h2>
				<h3><?php esc_html_e( 'Use Shortcodes', 'learndash-groups-plus' ); ?></h3>
				<p>
					<?php printf(
						esc_html__( 'You can add the %1$s shortcode into any page or post in your site to have the full administration area for %2$s%3$s%4$s or %5$s%6$s%7$s.', 'learndash-groups-plus' ),
						'<div><center><code>[learndash_groups_plus]</code></center></div>',
						'<strong>',
						learndash_get_custom_label( 'organization' ),
						'</strong>',
						'<strong>',
						learndash_get_custom_label( 'teams' ),
						'</strong>'
					); ?>
				</p>
				<p>
					<?php printf(
						esc_html__( 'If you would like to use the %1$s reports in isolation of the %2$s/%3$s Administration area, simply add the %4$s and %5$s shortcode to any page or post.', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'team' ),
						learndash_get_custom_label( 'organization' ),
						learndash_get_custom_label( 'team' ),
						'<div><center><code>[learndash_groups_plus_primary_report]</code></center></div><div>',
						'</div><div><center><code>[learndash_groups_plus_report]</code></center></div>'
					);?>
				</p>
				<p>
					<?php printf(
						esc_html__( 'Optionally, add a message board for each %1$s to any page or post using the %2$s shortcode.', 'learndash-groups-plus' ),
						learndash_get_custom_label_lower( 'team' ),
						'<center><code>[learndash_groups_plus_message_board]</code></center>'
					); ?>
				</p>
			</div> <!-- ld-adm-item -->

			<div class="ld-adm-item break">
				<h2><?php esc_html_e( 'Step 5', 'learndash-groups-plus' ); ?></h2>
				<h3><?php esc_html_e( 'Add Courses and Leader', 'learndash-groups-plus' ); ?></h3>
				<p>
					<?php printf(
						esc_html__( 'Click on the %1$s tab and add the %2$s you want available for all of its Child %3$s (%4$s).', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'courses' ),
						learndash_get_custom_label_lower( 'courses' ),
						learndash_get_custom_label( 'groups' ),
						learndash_get_custom_label( 'teams' )
					); ?>
				</p>
				<p>
					<?php printf(
						esc_html__( 'Click on the Users tab and add %1$s (%2$s) to this one %3$s. Save the %4$s.', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'group_leader' ),
						learndash_get_custom_label( 'lead_organizers' ),
						learndash_get_custom_label_lower( 'group' ),
						learndash_get_custom_label_lower( 'group' )
					); ?>
				</p>
			</div> <!-- ld-adm-item -->

			<div class="ld-adm-item">
				<h2><?php esc_html_e( 'Finally', 'learndash-groups-plus' ); ?></h2>
				<h3>
					<?php printf(
						esc_html__( '%s Maintain Themselves', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'team' )
					); ?>
				</h3>
				<p>
					<?php printf(
						esc_html__( 'Now the Leader of the %1$s (%2$s) can add %3$s, create their own email templates, send ad-hoc emails, create %4$s, create %5$s, assign %6$s to %7$s and run reports.', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'group' ),
						learndash_get_custom_label( 'lead_organizer' ),
						learndash_get_custom_label_lower( 'teams' ),
						learndash_get_custom_label_lower( 'team_leaders' ),
						learndash_get_custom_label_lower( 'team_members' ),
						learndash_get_custom_label_lower( 'courses' ),
						learndash_get_custom_label_lower( 'teams' )
					); ?>
				</p>
				<p>
					<?php printf(
						esc_html__( 'Now %1$s can create their own email templates, send ad-hoc emails, create %2$s and run reports.', 'learndash-groups-plus' ),
						learndash_get_custom_label_lower( 'team_leaders' ),
						learndash_get_custom_label_lower( 'team_members' )
					); ?>
				</p>
				<center>
					<button onclick="document.getElementById('ld-id99').style.display='block'" class="ld-button"><img
							id="ld-adm-qmark"
							src="<?php echo LEARNDASH_GROUPS_PLUS_URL . './build/images/q-mark.png'; ?>" /></button>
				</center>
			</div> <!-- ld-adm-item -->

			<div id="ld-id99">
				<div class="ld-modal-content ld-modal-content-99 ld-animate-zoom ld-card-99">
					<header class="ld-container ld-teal">
						<span onclick="document.getElementById('ld-id99').style.display='none'" class="ld-display-topright"><img
								id="ld-adm-close"
								src="<?php echo LEARNDASH_GROUPS_PLUS_URL . './build/images/close-modal.png'; ?>" /></span>
						<center>
							<h3><?php _e( 'FERPA (U.S.) Compliance', 'learndash-groups-plus' ); ?></h3>
						</center>
					</header>

					<div class="ld-container">
						<?php printf(
							esc_html__( '%1$s Last Name and Emails are optional.%2$sRead More...%3$s', 'learndash-groups-plus' ),
							learndash_get_custom_label( 'team_members' ),
							'</br><center><a href="https://www2.ed.gov/policy/gen/guid/fpco/ferpa/index.html" target="_blank">',
							'</a></center>'
						); ?>
					</div>
				</div> <!-- ld-modal-content-99 -->
			</div> <!-- ld-id99 -->
		</div> <!-- ld-adm-container -->
		<?php
	}

	public static function general_setting_save_field() {
	}

	public static function general_setting() {
	}

	public static function sett_create_setting() {
		$team_leader_use_seat                         = get_site_option( 'team_leader_use_seat' );
		$allow_course_cert_publicly               = get_site_option( 'allow_course_cert_publicly' );
		$show_all_quiz_attempts                   = get_site_option( 'show_all_quiz_attempts' );
		$allow_leader_to_see_all_users            = get_site_option( 'allow_leader_to_see_all_users' );
		$enable_classrom_message_board            = get_site_option( 'enable_classrom_message_board' );
		$lock_team_leader_and_team_member_names           = get_site_option( 'lock_team_leader_and_team_member_names' );
		$allow_existing_users_to_be_updated       = get_site_option( 'allow_existing_users_to_be_updated' );
		$allow_team_leaders_to_add_subscription_seats = get_site_option( 'allow_team_leaders_to_add_subscription_seats' );
		?>
<div class="ld-adm-container">
	<form action="" enctype="multipart/form-data" id="setting_form" method="post" name="setting_form">
		<?php wp_nonce_field( 'learndash_groups_plus_setting_form' ); ?>
		<h3><?php _e( 'Settings', 'learndash-groups-plus' ); ?></h3>
		<table class="form-table">
			<tbody>
				<tr>
					<td colspan="3"><center><h3 class="ld-settings-banner"><?php _e( 'Communication', 'learndash-groups-plus' ); ?></h3></center></td>
				</tr>

				<tr valign="top">
					<th style="width:250px;vertical-align:middle;">
						<label for="enable_classrom_message_board">
							<?php printf(
								esc_html__( 'Enable %s message board:', 'learndash-groups-plus' ),
								learndash_get_custom_label_lower( 'team' )
							); ?>
						</label>
					</th>
					<td class="forminp forminp-text">
						<input id="enable_classrom_message_board" name="enable_classrom_message_board" type="checkbox" value="yes"
							<?php echo $enable_classrom_message_board == 'yes' ? 'checked' : ''; ?>>
					</td>
					<td>
						<?php printf(
							esc_html__( 'Select this to enable a %1$s message board for each %2$s. The %3$s can show a message to their %4$s %5$s by creating a message from the [Email %6$s] button, then selecting the Message Board tab in the modal.%7$sThe message will then be displayed using the %8$s shortcode. The %9$s can later, delete any message they post to their %10$s by selecting the Trashcan icon in the [Email %11$s] modal. The %12$s can leave multiple messages.%13$sIf a %14$s is in multiple %15$s, they will see messages from each of their %16$s.%17$sThe message board is not a two way communication tool. It is an on-screen message broadcast tool.', 'learndash-groups-plus' ),
							learndash_get_custom_label_lower( 'team' ),
							learndash_get_custom_label_lower( 'team' ),
							learndash_get_custom_label_lower( 'team_leader' ),
							learndash_get_custom_label_lower( 'team' ),
							learndash_get_custom_label_lower( 'team_members' ),
							learndash_get_custom_label( 'team' ),
							'<br/>',
							'<code>[learndash_groups_plus_message_board]</code>',
							learndash_get_custom_label_lower( 'team_leader' ),
							learndash_get_custom_label_lower( 'team' ),
							learndash_get_custom_label( 'team' ),
							learndash_get_custom_label_lower( 'team_leader' ),
							'<br/>',
							learndash_get_custom_label_lower( 'team_member' ),
							learndash_get_custom_label_lower( 'teams' ),
							learndash_get_custom_label_lower( 'team' ),
							'<br/>'
						); ?>
					</td>
				</tr>

				<?php
				if ( SharedFunctions::is_buddyboss_active() ) {
						$enable_bb_organization      = get_site_option( 'enable_bb_organization' );
						$enable_bb_groups_plus = get_site_option( 'enable_bb_groups_plus' );
					?>
				<tr valign="top">
					<th style="width:250px;vertical-align:middle;">
						<label
							for="allow_course_cert_publicly"><?php printf( esc_html__( 'Enable BuddyBoss groups for %s:', 'learndash-groups-plus' ), learndash_get_custom_label( 'organization' ) ); ?></label>
					</th>
					<td class="forminp forminp-text">
						<input id="enable_bb_organization" name="enable_bb_organization" type="checkbox" value="yes"
							<?php echo $enable_bb_organization == 'yes' ? 'checked' : ''; ?>>
					</td>
					<td>
						<?php printf( esc_html__( 'Enable BuddyBoss groups for %s', 'learndash-groups-plus' ), learndash_get_custom_label( 'organization' ) ); ?>
					</td>
				</tr>

				<tr valign="top">
					<th style="width:250px;vertical-align:middle;">
						<label for="allow_course_cert_publicly">
							<?php printf(
								esc_html__( 'Enable BuddyBoss groups for %s', 'learndash-groups-plus' ),
								learndash_get_custom_label( 'team' )
							); ?>:
						</label>
					</th>
					<td class="forminp forminp-text">
						<input id="enable_bb_groups_plus" name="enable_bb_groups_plus" type="checkbox" value="yes"
							<?php echo $enable_bb_groups_plus == 'yes' ? 'checked' : ''; ?>>
					</td>
					<td>
						<?php printf(
							esc_html__( 'Enable BuddyBoss groups for %s', 'learndash-groups-plus' ),
							learndash_get_custom_label( 'team' )
						); ?>:
					</td>
				</tr>

				<?php } ?>
				<?php
				if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
					?>
					<tr>
						<td colspan="3"><center><h3 class="ld-settings-banner"><?php _e( 'Assignments Approval using Gravity Forms', 'learndash-groups-plus' ); ?></h3></center></td>
					</tr>

					<tr valign="top">
						<th style="width:250px;vertical-align:middle;">
							<label><?php _e( 'Process Assignment Uploads using Gravity Forms:', 'learndash-groups-plus' ); ?></label>
						</th>
						<td class="forminp forminp-text">
							
						</td>
						<td>
							<p>
								<?php printf(
									esc_html__(
										'Using this integration, you can enhance the LearnDash Assignment approval process by using Gravity Forms in the workflow.%1$sYou do not need to use any other Gravity Forms related plugin for this process. Any other Gravity Forms related plugin can be used to enhance this process further but those plugins will be applicable to whatever you do in Gravity Forms alone.', 'learndash-groups-plus' ),
									'<br/>'
								); ?>
							</p>
							<p>
								<?php esc_html_e(
									'This provides the following 6 benefits', 'learndash-groups-plus' ); ?>:
							</p>
							<ol>
								<li>
									<?php printf(
										esc_html__(
											'%s have an approval / feedback process where their form inputs contribute to the information related to the assignment submission approval evidence.', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'group_leader' )
									); ?>
								</li>
								<li>
									<?php printf(
										esc_html__(
											'Adds new assignment statuses in the Detailed %s Report indicating the assignment needs to be graded, resubmitted, or is approved so no further action is required.', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'team_member' )
									); ?>
								</li>
								<li>
									<?php esc_html_e(
										'Adds color codes to the assignment status buttons for visual action queues.',
										'learndash-groups-plus'
									); ?>
								</li>
								<li>
									<?php esc_html_e(
										'Let\'s use use the Gravity Forms notifications system to email the team member when an assignment needs to be resubmitted or is approved. This can also be used in conjunction with the LearnDash Notifications system, but is specific to the Gravity Form.',
										'learndash-groups-plus'
									); ?>
								</li>
								<li>
									<?php printf(
										esc_html__(
											'Let\'s you create one form for all assignments or use a different form specific to each assignment in 
											different %1$s or %2$s.', 'learndash-groups-plus' ),
										learndash_get_custom_label_lower( 'lessons' ),
										learndash_get_custom_label_lower( 'topics' ),
									); ?>
								</li>
								<li>
									<?php printf(
										esc_html__(
											'Provides LearnDash specific Gravity Form fields that pick up the %1$s and assignment IDs. The %2$s field can be used in other Gravity Forms not related to assignments.', 'learndash-groups-plus'
										),
										learndash_get_custom_label_lower( 'team_member' ),
										learndash_get_custom_label_lower( 'team_member' )
									); ?>
								</li>
							</ol>
							<p>
								<?php esc_html_e(
									'To use this feature, perform the following', 'learndash-groups-plus' ); ?>
							</p>
							<ol>
								<li>
									<?php printf(
										esc_html__(
											'Download, then import the Gravity Forms %s template file.',
											'learndash-groups-plus'
										),
										'<a href="' . esc_url( LEARNDASH_GROUPS_PLUS_URL . 'src/resources/data/assignment-approval-gform-template.json' ) . '" download>assignment-approval-gform-template.json</a>'
									); ?>
								</li>
								<li>
									<?php printf(
										esc_html__(
											'Import the form and you will have a new form named %1$sLearnDash LMS - Groups Plus Assignment Fields - Template%2$s. This form contains the fields Groups Plus will use.',
											'learndash-groups-plus'
										),
										'<strong>',
										'</strong>'
									); ?>
								</li>
								<li>
									<?php esc_html_e(
										'You can now duplicate the form and add in your own fields or recreate the form fields provided in an existing form.',
										'learndash-groups-plus'
									); ?>
								</li>
								<li>
									<?php printf(
										esc_html__(
											'After completing your form development, note the field ID numbers and the Gravity Form ID number. These field IDs and the form ID will be used to configure your %1$s or %2$s.',
											'learndash-groups-plus'
										),
										learndash_get_custom_label_lower( 'lessons' ),
										learndash_get_custom_label_lower( 'topics' )
									); ?>
								</li>
								<li>
									<?php printf(
										esc_html__(
											'Add the Gravity Form to a page and capture the URL of that page. This URL will be used to configure your %1$s or %2$s.',
											'learndash-groups-plus'
										),
										learndash_get_custom_label_lower( 'lessons' ),
										learndash_get_custom_label_lower( 'topics' )
									); ?>
								</li>
								<li>
									<?php printf(
										esc_html__(
											'Edit the settings of any %1$s or %2$s that has an assignment upload requirements and enter the Gravity Form page URL, the Gravity Form ID number, the field ID used to indicate the uploaded assignment is satisfactory or competent.%3$sOptionally, if you already have a drop down menu created to select %4$s names (for bulk scoring purposes), enter that field ID, else leave it empty.%5$sWith these completed, you will now be able to process uploaded assignments using the Gravity Form(s) you have created.',
											'learndash-groups-plus'
										),
										learndash_get_custom_label_lower( 'lesson' ),
										learndash_get_custom_label_lower( 'topic' ),
										'<br/>',
										learndash_get_custom_label_lower( 'team_member' ),
										'<br/>'
									); ?>
								</li>
								<li>
									<?php printf(
										esc_html__(
											'With this feature, you also now have new Design controls in the "%1$s Detailed Report settings" section of your Design tab above.',
											'learndash-groups-plus'
										),
										learndash_get_custom_label( 'team_member' )
									); ?>

								</li>
							</ol>
						</td>
					</tr>
					<?php
				};
				?>

				<tr>
					<td colspan="3"><center><h3 class="ld-settings-banner"><?php _e( 'Users', 'learndash-groups-plus' ); ?></h3></center></td>
				</tr>

				<tr valign="top">
					<th style="width:250px;vertical-align:middle;">
						<label for="team_leader_use_seat">
							<?php printf(
								esc_html__( '%s use seats', 'learndash-groups-plus' ),
								learndash_get_custom_label( 'team_leaders' )
							); ?>:
						</label>
					</th>
					<td class="forminp forminp-text">
						<input id="team_leader_use_seat" name="team_leader_use_seat" type="checkbox" value="1"
							<?php echo $team_leader_use_seat ? 'checked' : ''; ?>>
					</td>
					<td>
						<?php printf(
							esc_html__( 'Selecting this will enable a LearnDash Group Field for you to enter a number of %1$s that you want to include who will not occupy any %2$s seats.%3$sThis setting is done at the Primary Group level and applies to all %4$s.%5$sYou can then visit each Primary Group and set a number of %6$s that will not take %7$s seats.%8$sThis leaves you with a very flexible and powerful feature that you tailor for each %9$s.%10$sThe result is that when the %11$s of that %12$s populates any %13$s with %14$s as %15$s, they are limited to the number of free %16$s.%17$sThe default value of free %18$s is 0 when this setting is turned on.%18$s', 'learndash-groups-plus' ),
							learndash_get_custom_label_lower( 'team_leaders' ),
							learndash_get_custom_label_lower( 'team_members' ),
							'<br/>',
							learndash_get_custom_label_lower( 'teams' ),
							'<br/>',
							learndash_get_custom_label_lower( 'team_leaders' ),
							learndash_get_custom_label_lower( 'team_members' ),
							'<br/>',
							learndash_get_custom_label_lower( 'organization' ),
							'<br/>',
							learndash_get_custom_label_lower( 'lead_organizer' ),
							learndash_get_custom_label_lower( 'organization' ),
							learndash_get_custom_label_lower( 'team' ),
							learndash_get_custom_label_lower( 'team_leaders' ),
							learndash_get_custom_label_lower( 'team_members' ),
							learndash_get_custom_label_lower( 'team_leaders' ),
							'<br/><strong>',
							learndash_get_custom_label_lower( 'team_leaders' ),
							'</strong>',
						); ?>
					</td>
				</tr>

				<tr valign="top">
					<th style="width:250px;vertical-align:middle;">
						<label for="lock_team_leader_and_team_member_names">
							<?php printf(
								esc_html__( 'Lock %1$s and %2$s Names', 'learndash-groups-plus' ),
								learndash_get_custom_label( 'team_leader' ),
								learndash_get_custom_label( 'team_member' )
							); ?>:
						</label>
					</th>
					<td class="forminp forminp-text">
						<input id="lock_team_leader_and_team_member_names" name="lock_team_leader_and_team_member_names" type="checkbox" value="yes"
							<?php echo $lock_team_leader_and_team_member_names == 'yes' ? 'checked' : ''; ?>>
					</td>
					<td>
						<?php printf(
							esc_html__( 'Check this box so %1$s and %2$s cannot change their names in the %3$s page. Administrators will need to edit WordPress profiles to change names in %4$s', 'learndash-groups-plus' ),
							learndash_get_custom_label( 'team_leaders' ),
							learndash_get_custom_label( 'team_members' ),
							learndash_get_custom_label( 'team' ),
							learndash_get_custom_label( 'team' )
						); ?>
					</td>
				</tr>

				<tr valign="top">
					<th style="width:250px;vertical-align:middle;">
						<label
							for="allow_existing_users_to_be_updated"><?php _e( 'Allow existing users to be updated:', 'learndash-groups-plus' ); ?></label>
					</th>
					<td class="forminp forminp-text">
						<input id="allow_existing_users_to_be_updated" name="allow_existing_users_to_be_updated" type="checkbox" value="yes"
							<?php echo $allow_existing_users_to_be_updated == 'yes' ? 'checked' : ''; ?>>
					</td>
					<td>
						<?php _e( 'Allow existing users to be updated during upload process.', 'learndash-groups-plus' ); ?>
					</td>
				</tr>
				
				<tr valign="top">
					<th style="width:250px;vertical-align:middle;">
						<label for="allow_course_cert_publicly">
							<?php printf(
								esc_html__( 'Allow %1$s to have %2$s certificate access', 'learndash-groups-plus' ),
								learndash_get_custom_label_lower( 'team_member' ),
								learndash_get_custom_label_lower( 'course' )
							); ?>:
						</label>
					</th>
					<td class="forminp forminp-text">
						<input id="allow_course_cert_publicly" name="allow_course_cert_publicly" type="checkbox"
							value="1" <?php echo $allow_course_cert_publicly ? 'checked' : ''; ?>>
					</td>
					<td>
						<?php printf(
							esc_html__( 'Select this to let %1$s open / download / print their own certificates.%2$sIf not selected, %3$s will not have certificate icons anywhere. %4$s will need to provide the %5$s with their certificates.', 'learndash-groups-plus' ),
							learndash_get_custom_label_lower( 'team_members' ),
							'<br/>',
							learndash_get_custom_label_lower( 'team_members' ),
							learndash_get_custom_label( 'team_leaders' ),
							learndash_get_custom_label_lower( 'team_members' )
						); ?>
					</td>
				</tr>

				<tr valign="top">
					<th style="width:250px;vertical-align:middle;">
						<label for="allow_course_cert_publicly">
							<?php printf(
								esc_html__( 'Show all quiz attempts in Detailed %s Report', 'learndash-groups-plus' ),
								learndash_get_custom_label( 'team_member' )
							); ?>:
						</label>
					</th>
					<td class="forminp forminp-text">
						<input id="show_all_quiz_attempts" name="show_all_quiz_attempts" type="checkbox"
							value="1" <?php echo $show_all_quiz_attempts ? 'checked' : ''; ?>>
					</td>
					<td>
						<?php printf(
							esc_html__( 'Select this to Show all quiz attempts in Detailed %s Report.', 'learndash-groups-plus' ),
							learndash_get_custom_label( 'team_member' )
						); ?>
					</td>
				</tr>

				<tr valign="top">
					<th style="width:250px;vertical-align:middle;">
						<label
							for="allow_leader_to_see_all_users"><?php _e( 'Allow lead organizers to see all users:', 'learndash-groups-plus' ); ?></label>
					</th>
					<td class="forminp forminp-text">
						<input id="allow_leader_to_see_all_users" name="allow_leader_to_see_all_users"
							type="checkbox" value="1" <?php echo $allow_leader_to_see_all_users ? 'checked' : ''; ?>>
					</td>
					<td>
						<?php _e( 'Allow lead organizers to see all users that are "subscribers" in the platform when selecting "User already exists".', 'learndash-groups-plus' ); ?>
					</td>
				</tr>

				<?php
				if ( SharedFunctions::is_woocommerce_active() ) {
					$enable_wc = get_site_option( 'enable_wc' );
					?>

				<tr>
					<td colspan="3"><center><h3 class="ld-settings-banner"><?php _e( 'WooCommerce', 'learndash-groups-plus' ); ?></h3></center></td>
				</tr>

				<tr valign="top">
					<th style="width:250px;vertical-align:middle;">
						<label
							for="allow_course_cert_publicly"><?php _e( 'Enable WooCommerce Integration:', 'learndash-groups-plus' ); ?></label>
					</th>
					<td class="forminp forminp-text">
						<input id="enable_wc" name="enable_wc" type="checkbox" value="yes"
							<?php echo $enable_wc == 'yes' ? 'checked' : ''; ?>>
					</td>
					<td>
						<?php printf(
							esc_html__( '%1$sTEST THIS ON YOUR STAGING SITE USING YOUR PAYMENT GATEWAY IN TEST MODE BEFORE GOING LIVE%2$sThis setting lets you create %3$s product and seat product. You can also set custom pricing per %4$s for %5$s. %6$s can also opt-in to selling seat access to their %7$s when they purchase their %8$s. If %9$s opts-out of selling seat access in their %10$s, their %11$s will not be listed in the public %12$s product listing.%13$sThese products are able to be sold as WooCommerce Subscriptions and can also be used within Variable Products.%14$sYou do not need to create multiple products as the %15$s product will do everything you need and the Seat product will only see any %16$s that the %17$s allow them to see.', 'learndash-groups-plus' ),
							'<strong><center>',
							'</center></strong>',
							learndash_get_custom_label( 'organization' ),
							learndash_get_custom_label_lower( 'course' ),
							learndash_get_custom_label_lower( 'organizations' ),
							learndash_get_custom_label( 'organization' ),
							learndash_get_custom_label_lower( 'team' ),
							learndash_get_custom_label_lower( 'organization' ),
							learndash_get_custom_label_lower( 'organizations' ),
							learndash_get_custom_label_lower( 'teams' ),
							learndash_get_custom_label_lower( 'organizations' ),
							learndash_get_custom_label_lower( 'organization' ),
							'<br/>',
							'<br/>',
							learndash_get_custom_label( 'organization' ),
							learndash_get_custom_label( 'team' ),
							learndash_get_custom_label_lower( 'organizations' ),
						); ?>
					</td>
				</tr>
				<?php } ?>

				<?php
				if ( SharedFunctions::is_woocommerce_active() && 'yes' === get_site_option( 'enable_wc', 'no' ) ) {
					$enable_add_seats = get_site_option( 'enable_add_seats' );
					?>
				<tr valign="top">
					<th style="width:250px;vertical-align:middle;">
						<label for="team_leader_use_seat">
							<?php printf(
								esc_html__( 'Enable Add Seats to %s', 'learndash-groups-plus' ),
								learndash_get_custom_label( 'team' )
							); ?>:
						</label>
					</th>
					<td class="forminp forminp-text">
						<input id="team_leader_use_seat" name="enable_add_seats" type="checkbox" value="1"
							<?php echo $enable_add_seats ? 'checked' : ''; ?>>
					</td>
					<td>
						<?php printf(
							esc_html__( 'Enable Add Seats to %s', 'learndash-groups-plus' ),
							learndash_get_custom_label( 'team' )
						); ?>
					</td>
				</tr>

				<tr valign="top">
					<th style="width:250px;vertical-align:middle;">
						<label for="allow_team_leaders_to_add_subscription_seats">
							<?php printf(
								esc_html__( 'Enable %1$s to add subscription-based seats to %2$s', 'learndash-groups-plus' ),
								learndash_get_custom_label_lower( 'team_leaders' ),
								learndash_get_custom_label_lower( 'team' )
							); ?>:
						</label>
					</th>
					<td class="forminp forminp-text">
						<input id="allow_team_leaders_to_add_subscription_seats" name="allow_team_leaders_to_add_subscription_seats" type="checkbox" value="yes"
							<?php echo $allow_team_leaders_to_add_subscription_seats == 'yes' ? 'checked' : ''; ?>>
					</td>
					<td>
						<?php printf(
							esc_html__( 'Enable %1$s to add subscription-based seats to %2$s is checked AND a subscription product is created to add seats to a %3$s.', 'learndash-groups-plus' ),
							learndash_get_custom_label_lower( 'team_leaders' ),
							learndash_get_custom_label_lower( 'team' ),
							learndash_get_custom_label_lower( 'team' )
						); ?>
					</td>
				</tr>
				<?php } ?>

				<?php
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
				if ( is_plugin_active( 'uncanny-learndash-groups/uncanny-learndash-groups.php' ) ) {
					// plugin is activated
					$use_uncanny_seat = get_site_option( 'use_uncanny_seat' );
					?>
				<tr valign="top">
					<th style="width:250px;vertical-align:middle;">
						<label
							for="team_leader_use_seat"><?php _e( 'Use uncanny group seats at will:', 'learndash-groups-plus' ); ?></label>
					</th>
					<td class="forminp forminp-text">
						<input id="team_leader_use_seat" name="use_uncanny_seat" type="checkbox" value="1"
						<?php echo $use_uncanny_seat ? 'checked' : ''; ?>>
					</td>
					<td>
						<?php printf(
							esc_html__( '%1$sYou are seeing this setting because you have UnCanny Groups installed in your site.%2$sPlease refer to your UnCanny Groups > Settings page to learn about Upgrading Groups before proceeding%3$sAfter selecting this checkbox, if any of your %4$s are configured in UnCanny Groups, you will not be asked to set any %5$s Seats in the %6$s.%7$sYou will also not need to manually create %8$s and assign %9$s users to them.%10$sYou will still be able to have up to X number of %11$s as %12$s.', 'learndash-groups-plus' ),
							'<strong>',
							'</strong><br/><strong>',
							'</strong><br/><br/>',
							learndash_get_custom_label_lower( 'groups' ),
							learndash_get_custom_label( 'team_member' ),
							learndash_get_custom_label( 'group' ),
							'<br/>',
							learndash_get_custom_label_lower( 'groups' ),
							learndash_get_custom_label_lower( 'lead_organizer' ),
							'<br/>',
							learndash_get_custom_label_lower( 'team_leaders' ),
							learndash_get_custom_label_lower( 'team_members' )
						); ?>
					</td>
				</tr>
					<?php
				}
				?>

			</tbody>
		</table>
		<p class="submit">
			<input class="button-primary" name="save_setting" type="submit"
				value="<?php esc_html_e( 'Save', 'learndash-groups-plus' ); ?>" />
		</p>
	</form>
	<hr />
	<form action="" enctype="multipart/form-data" id="migration_form" method="post" name="migration_form">
		<?php wp_nonce_field( 'learndash_groups_plus_migration_form' ); ?>
		<h3 class="ld-settings-banner">
			<?php printf(
				esc_html__( 'Upgrade LearnDash %1$s to %2$s', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'groups' ),
				learndash_get_custom_label( 'team' )
			); ?>
		</h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th style="width:250px;vertical-align:middle;">
						<label for="migrate_groups">
							<?php printf(
								esc_html__( 'Convert LearnDash %1$s to %2$s', 'learndash-groups-plus' ),
								learndash_get_custom_label( 'groups' ),
								learndash_get_custom_label( 'team' )
							); ?>:
						</label>
					</th>
					<td class="forminp forminp-text">
						<input id="migrate_groups" name="migrate_groups" type="checkbox" value="1"
							<?php // echo $migrate_groups ? "checked":""; ?>>
					</td>
					<td>
						<?php printf(
							esc_html__( 'Checking this box and clicking the %1$s[Convert]%2$s button below will run a site-wide process that will look for any LearnDash Primary %3$s that have no Child %4$s and convert them into %5$s.%6$sThe result of this process will have those %7$s that get converted with a child %8$s of the same name.%9$sThe %10$s(s), %11$s, and %12$s of the primary %13$s will also be copied, but %14$s will not use the %15$s in that primary %16$s.%17$sAt the end of this process your %18$s structure will be globally applied.%19$sIf you need to revert any LearnDash Primary %20$s%21$s back to its original state,%22$s simply delete the new child group%23$s and it will be restored while all other %24$s will remain in the new %25$s structure.', 'learndash-groups-plus' ),
							'<strong>',
							'</strong>',
							learndash_get_custom_label( 'groups' ),
							learndash_get_custom_label( 'groups' ),
							learndash_get_custom_label( 'team' ),
							'<br/><br/>',
							learndash_get_custom_label_lower( 'groups' ),
							learndash_get_custom_label_lower( 'group' ),
							'<br/><br/>',
							learndash_get_custom_label_lower( 'group_leader' ),
							learndash_get_custom_label_lower( 'courses' ),
							learndash_get_custom_label_lower( 'team_members' ),
							learndash_get_custom_label_lower( 'group' ),
							learndash_get_custom_label( 'team' ),
							learndash_get_custom_label_lower( 'team_members' ),
							learndash_get_custom_label_lower( 'group' ),
							'<br/><br/>',
							learndash_get_custom_label( 'team' ),
							'<br/><br/><strong>',
							learndash_get_custom_label( 'group' ),
							'</strong>',
							'<strong>',
							'</strong>',
							learndash_get_custom_label_lower( 'groups' ),
							learndash_get_custom_label( 'team' )
						); ?>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input class="button-primary" name="save_groups_setting" type="submit"
				value="<?php esc_html_e( 'Convert', 'learndash-groups-plus' ); ?>" />
		</p>
	</form>
	<hr />
	<form action="" enctype="" id="downgrade_form" method="post" name="downgrade_form">
		<?php wp_nonce_field( 'learndash_groups_plus_downgrade_form' ); ?>
		<h3 class="ld-settings-banner">
			<?php printf(
				esc_html__( 'Downgrade %1$s to Normal %2$s', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team' ),
				learndash_get_custom_label( 'groups' )
			); ?>
		</h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th style="width:250px;vertical-align:middle;">
						<label for="downgrade_groups">
							<?php printf(
								esc_html__( 'Downgrade %1$s to Normal %2$s', 'learndash-groups-plus' ),
								learndash_get_custom_label( 'team' ),
								learndash_get_custom_label( 'groups' )
							); ?>:
						</label>
					</th>
					<td class="forminp forminp-text">
						<input id="downgrade_groups" name="downgrade_groups" type="checkbox" value="1">
					</td>
					<td>
						<?php printf(
							esc_html__( 'Checking this box and clicking the %1$s[Downgrade]%2$s button below will run a site-wide process that will remove %3$s %4$s and return their parent %5$s to normal LearnDash %6$s.', 'learndash-groups-plus' ),
							'<strong>',
							'</strong>',
							learndash_get_custom_label( 'team' ),
							learndash_get_custom_label_lower( 'groups' ),
							learndash_get_custom_label_lower( 'groups' ),
							learndash_get_custom_label_lower( 'groups' )
						); ?>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input class="button-primary" name="save_downgrade_groups_setting" type="submit"
				value="<?php esc_html_e( 'Downgrade', 'learndash-groups-plus' ); ?>" />
		</p>
	</form>

	<div class="clear"></div>
	
</div><!-- ld-adm-container -->
		<?php
	}

	public static function sett_setting_admin_notice() {
		global $pagenow;
		if ( $pagenow == 'admin.php' && isset( $_GET['upgrade'] ) && $_GET['upgrade'] == 'success' ) {
			echo '<div class="notice notice-success is-dismissible">
	             <p>' . esc_html( 'LearnDash LMS - Groups Plus upgrade completed.', 'learndash-groups-plus' ) . '</p>
	        </div>';
		} elseif ( $pagenow == 'admin.php' && isset( $_GET['upgrade'] ) && $_GET['upgrade'] == 'fail' ) {
			echo '<div class="notice notice-error is-dismissible">
	             <p>' . esc_html( 'No groups were found that needed to be upgraded to team.', 'learndash-groups-plus' ) . '</p>
	        </div>';
		}
	}

	public static function register_globals() {
		global $obj_migrate_groups_batch;
		$obj_migrate_groups_batch = new Migrate_Groups_Batch();

		global $obj_downgrade_groups_batch;
		$obj_downgrade_groups_batch = new Downgrade_Groups_Batch();
	}

	public static function sett_setting_save_field() {
		if ( isset( $_POST['save_setting'] ) && ! empty( $_POST['save_setting'] ) ) {
			if ( ! current_user_can( LEARNDASH_ADMIN_CAPABILITY_CHECK ) ) {
				return;
			}

			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'learndash_groups_plus_setting_form' ) ) {
				return;
			}

			if ( isset( $_POST['team_leader_use_seat'] ) && $_POST['team_leader_use_seat'] == 1 ) {
				update_site_option( 'team_leader_use_seat', '1' );
			} else {
				update_site_option( 'team_leader_use_seat', '0' );
			}

			if ( isset( $_POST['use_uncanny_seat'] ) && $_POST['use_uncanny_seat'] == 1 ) {
				update_site_option( 'use_uncanny_seat', '1' );
			} else {
				update_site_option( 'use_uncanny_seat', '0' );
			}

			if ( isset( $_POST['allow_course_cert_publicly'] ) && $_POST['allow_course_cert_publicly'] == 1 ) {
				update_site_option( 'allow_course_cert_publicly', '1' );
			} else {
				update_site_option( 'allow_course_cert_publicly', '0' );
			}

			if ( isset( $_POST['show_all_quiz_attempts'] ) && $_POST['show_all_quiz_attempts'] == 1 ) {
				update_site_option( 'show_all_quiz_attempts', '1' );
			} else {
				update_site_option( 'show_all_quiz_attempts', '0' );
			}

			if ( isset( $_POST['allow_leader_to_see_all_users'] ) && $_POST['allow_leader_to_see_all_users'] == 1 ) {
				update_site_option( 'allow_leader_to_see_all_users', '1' );
			} else {
				update_site_option( 'allow_leader_to_see_all_users', '0' );
			}

			if ( isset( $_POST['enable_wc'] ) && $_POST['enable_wc'] == 'yes' ) {
				update_site_option( 'enable_wc', 'yes' );
			} else {
				update_site_option( 'enable_wc', 'no' );
			}

			if ( isset( $_POST['enable_bb_organization'] ) && $_POST['enable_bb_organization'] == 'yes' ) {
				update_site_option( 'enable_bb_organization', 'yes' );
			} else {
				update_site_option( 'enable_bb_organization', 'no' );
			}

			if ( isset( $_POST['enable_bb_groups_plus'] ) && $_POST['enable_bb_groups_plus'] == 'yes' ) {
				update_site_option( 'enable_bb_groups_plus', 'yes' );
			} else {
				update_site_option( 'enable_bb_groups_plus', 'no' );
			}

			if ( isset( $_POST['enable_add_seats'] ) && $_POST['enable_add_seats'] == 1 ) {
				update_site_option( 'enable_add_seats', '1' );
			} else {
				update_site_option( 'enable_add_seats', '0' );
			}

			if ( isset( $_POST['enable_classrom_message_board'] ) && $_POST['enable_classrom_message_board'] == 'yes' ) {
				Database::create_table_broadcast_message();
				update_site_option( 'enable_classrom_message_board', 'yes' );
			} else {
				update_site_option( 'enable_classrom_message_board', 'no' );
			}

			if ( isset( $_POST['lock_team_leader_and_team_member_names'] ) && $_POST['lock_team_leader_and_team_member_names'] == 'yes' ) {
				update_site_option( 'lock_team_leader_and_team_member_names', 'yes' );
			} else {
				update_site_option( 'lock_team_leader_and_team_member_names', 'no' );
			}

			if ( isset( $_POST['allow_existing_users_to_be_updated'] ) && $_POST['allow_existing_users_to_be_updated'] == 'yes' ) {
				update_site_option( 'allow_existing_users_to_be_updated', 'yes' );
			} else {
				update_site_option( 'allow_existing_users_to_be_updated', 'no' );
			}

			if ( isset( $_POST['allow_team_leaders_to_add_subscription_seats'] ) && $_POST['allow_team_leaders_to_add_subscription_seats'] == 'yes' ) {
				update_site_option( 'allow_team_leaders_to_add_subscription_seats', 'yes' );
			} else {
				update_site_option( 'allow_team_leaders_to_add_subscription_seats', 'no' );
			}
		}

		if ( isset( $_POST['save_groups_setting'] ) && ! empty( $_POST['save_groups_setting'] ) ) {
			if ( isset( $_POST['migrate_groups'] ) && ! empty( $_POST['migrate_groups'] ) ) {
				if ( ! current_user_can( LEARNDASH_ADMIN_CAPABILITY_CHECK ) ) {
					return;
				}
	
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'learndash_groups_plus_migration_form' ) ) {
					return;
				}

				$learndash_settings_groups_management_display = get_option( 'learndash_settings_groups_management_display' );

				global $obj_migrate_groups_batch;

				update_option( 'started-learndash-groups-plus-migrate-groups-process', true );

				// turn on the group hierarchy
				if ( ! isset( $learndash_settings_groups_management_display['group_hierarchical_enabled'] ) || empty( $learndash_settings_groups_management_display['group_hierarchical_enabled'] ) ) {
					$learndash_settings_groups_management_display['group_hierarchical_enabled'] = 'yes';
					update_option( 'learndash_settings_groups_management_display', $learndash_settings_groups_management_display );
				}

				$primary_groups_query_args = array(
					'post_type'   => 'groups',
					'nopaging'    => true,
					'post_parent' => 0,
					'post_status' => array( 'publish' ),
					'orderby'     => 'title',
				);

				$primary_groups_query = new \WP_Query( $primary_groups_query_args );
				$primary_groups       = $primary_groups_query->posts;
				$ld_groups            = array();

				foreach ( $primary_groups as $primary_group ) {
					$obj_migrate_groups_batch->push_to_queue( $primary_group );
				}

				// Dispatch the async request
				$obj_migrate_groups_batch->save()->dispatch();
			}
		}

		if ( isset( $_POST['save_downgrade_groups_setting'] ) && ! empty( $_POST['save_downgrade_groups_setting'] ) ) {
			if ( isset( $_POST['downgrade_groups'] ) && ! empty( $_POST['downgrade_groups'] ) ) {
				if ( ! current_user_can( LEARNDASH_ADMIN_CAPABILITY_CHECK ) ) {
					return;
				}
	
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'learndash_groups_plus_downgrade_form' ) ) {
					return;
				}

				global $obj_downgrade_groups_batch;

				update_option( 'started-learndash-groups-plus-downgrade-groups-process', true );

				$primary_groups_query_args = array(
					'post_type'   => 'groups',
					'nopaging'    => true,
					'post_parent' => 0,
					'post_status' => array( 'publish' ),
					'orderby'     => 'title',
				);

				$primary_groups_query = new \WP_Query( $primary_groups_query_args );
				$primary_groups       = $primary_groups_query->posts;

				foreach ( $primary_groups as $primary_group ) {
					$obj_downgrade_groups_batch->push_to_queue( $primary_group );
				}

				$obj_downgrade_groups_batch->save()->dispatch();
			}
		}
	}

	public static function sett_setting() {
	}

	/**
	 * Create the email settings tab.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function email_create_setting() {
		$email_tab = get_site_option( 'email_tab' );
		if ( ! is_array( $email_tab ) ) {
			$email_tab = array();
		}
		extract( $email_tab );
		?>

<div class="ld-adm-container">
	<form action="" enctype="multipart/form-data" id="setting_form" method="post" name="setting_form" class="email-setting-form">
		<?php wp_nonce_field( 'learndash_groups_plus_setting_form' ); ?>
		<h3>
			<?php printf(
				esc_html__( 'Configure Global %1$s and %2$s Welcome emails', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team_leader' ),
				learndash_get_custom_label( 'team_member' ),
			); ?>
		</h3>
		<p>
			<?php printf(
				esc_html__( 'Beyond the custom email merge-codes we provide below, you can also use the LearnDash shortcodes found using the toolbar icon.', 'learndash-groups-plus' )
			); ?>
		</p>
		<p>
			<?php printf(
				esc_html__( '%1$sConfiguring these global emails will make them the default email for all %2$s and %3$s and %4$s invitations.%5$sThese emails will be superseded by %6$s and %7$s welcome emails if each %8$s or %9$s creates their own personal emails.', 'learndash-groups-plus' ),
				'<strong>',
				learndash_get_custom_label_lower( 'team_member' ),
				learndash_get_custom_label_lower( 'team_leader' ),
				learndash_get_custom_label_lower( 'team' ),
				'</strong><br/>',
				learndash_get_custom_label( 'team_leader' ),
				learndash_get_custom_label( 'team_member' ),
				learndash_get_custom_label_lower( 'organization' ),
				learndash_get_custom_label_lower( 'team' ),
			); ?>
		</p>
		<h3 class="ld-settings-banner">
			<?php printf(
				esc_html__( '%s Invitation Email', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team_leader' )
			); ?>
		</h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th class="titledesc" scope="row"><label
						for="team_leader_subject"><?php esc_html_e( 'Subject', 'learndash-groups-plus' ); ?></label></th>
					<td class="forminp forminp-text"><input autocomplete="off" class="" id="team_leader_subject"
						name="team_leader_subject" type="text"
						value="<?php echo isset( $team_leader_subject ) ? esc_html( $team_leader_subject ) : ''; ?>"></td>
				</tr>

				<tr valign="top">
					<td>
						<label for="team_leader_body">
							<strong><?php _e( 'Body', 'learndash-groups-plus' ); ?></strong>
						</label>
						<h4><?php _e( 'Available merge codes', 'learndash-groups-plus' ); ?>:</h4>
						<p><code>{group_name}</code></p>
						<p><code>{childgroup_name}</code></p>
						<p><code>{team_leader_name}</code></p>
						<p><code>{team_leader_username}</code></p>
						<p><code>{password}</code></p>
						<p><code>{autologin}</code></p>
					</td>
					<td class="forminp forminp-text">
						<?php
							$content   = isset( $team_leader_body ) ? $team_leader_body : '';
							$editor_id = 'team_leader_body';
							$settings  = array(
								'media_buttons' => false,
								'wpautop'       => false,
							);
							wp_editor( $content, $editor_id, $settings );
							?>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<h3 class="ld-settings-banner">
			<?php printf(
				esc_html__( '%s Invitation Email', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team_member' )
			); ?>
		</h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th class="titledesc" scope="row"><label
						for="team_member_subject"><?php esc_html_e( 'Subject', 'learndash-groups-plus' ); ?></label></th>
					<td class="forminp forminp-text"><input autocomplete="off" class="" id="team_member_subject"
						name="team_member_subject" type="text"
						value="<?php echo isset( $team_member_subject ) ? esc_html( $team_member_subject ) : ''; ?>"></td>
				</tr>

				<tr>
					<td>
						<label for="team_member_body">
							<strong><?php _e( 'Body', 'learndash-groups-plus' ); ?></strong>
						</label>
						<h4><?php _e( 'Available merge codes', 'learndash-groups-plus' ); ?>:</h4>
						<p><code>{group_name}</code></p>
						<p><code>{childgroup_name}</code></p>
						<p><code>{team_member_name}</code></p>
						<p><code>{user_name}</code></p>
						<p><code>{password}</code></p>
						<p><code>{autologin}</code></p>
					</td>
					<td class="forminp forminp-text">
						<?php
							$content   = isset( $team_member_body ) ? $team_member_body : '';
							$editor_id = 'team_member_body';
							$settings  = array(
								'media_buttons' => false,
								'wpautop'       => false,
							);
							wp_editor( $content, $editor_id, $settings );
							?>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input class="button-primary" name="save_email_tab" type="submit"
				value="<?php esc_html_e( 'Save', 'learndash-groups-plus' ); ?>" />
		</p>
	</form>
</div><!-- ld-adm-container -->
		<?php
	}

	public static function email_setting_save_field() {
		if ( isset( $_POST['save_email_tab'] ) ) {
			if ( ! current_user_can( LEARNDASH_ADMIN_CAPABILITY_CHECK ) ) {
				return;
			}

			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'learndash_groups_plus_setting_form' ) ) {
				return;
			}

			$data = array();
			foreach ( $_POST as $key => $value ) {
				switch ( $key ) {
					case 'team_leader_subject':
					case 'team_member_subject':
						$data[ $key ] = wp_kses_post( stripslashes( $value ) );
						break;
					
					case 'team_leader_body':
					case 'team_member_body':
						$data[ $key ] = wp_kses_post( stripslashes( $value ) );
						break;
				}
			}

			update_site_option( 'email_tab', $data );
		}
	}

	public static function email_setting() {
	}

	public static function hooks_setting() {
		?>

<div class="ld-adm-container">
	<h3><?php esc_html_e( 'Developer Resources', 'learndash-groups-plus' ); ?></h3>
	<div>
		<p>
			<strong>
				<?php esc_html_e( 'This tab is dedicated to those developers needing to change the features, capabilities, and presentation of the Groups Plus plugin.', 'learndash-groups-plus' ); ?>
			</strong>
		</p>
	</div>
	<!-- Tab links -->
	<div class="tab">
	  	<button class="tablinks active" onclick="openCity(event, 'a')">
			<?php printf(
				esc_html__( 'Templating the %s page', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team' )
			); ?>
		</button>
	  	<button class="tablinks" onclick="openCity(event, 'b')">
			<?php _e( 'Creating custom actions through hooks', 'learndash-groups-plus' ); ?>
		</button>
	</div>

	<div id="a" class="tabcontent">
		<p>
			<strong>
				<?php esc_html_e( 'Team can be added to your website using the various shortcodes provided.', 'learndash-groups-plus' ); ?>	
			</strong>
			<?php printf(
				esc_html__( 'The shortcodes can be used to show the whole organization administration area or you can use the report related shortcodes to show only pieces of %s in any page or post you want.', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team' )
			); ?>
		</p>
		<p>
			<strong>
				<?php printf(
					esc_html__( 'You can also turn off sections, areas, and even specific buttons and tabs in %s using the Design tab settings provided above.', 'learndash-groups-plus' ),
					learndash_get_custom_label( 'team' )
				); ?>
			</strong>
		</p>
		<p>
			<?php printf(
				esc_html__( '%1$sIf you want to customize the /learndash-groups-plus/ template itself%2$s, here are the steps to getting that set up', 'learndash-groups-plus' ),
				'<strong>',
				'</strong>'
			); ?>:
		</p>
		<p>
			<?php esc_html_e(
				'You will first need to have a child-theme setup, activated, and properly functioning in your site. Once you have done this, you will want to use an FTP client software to upload your edited code to that child-theme sub-folder.',
				'learndash-groups-plus'
			); ?>
		</p>
		<ol>
			<li>
				<?php printf(
					esc_html__(
						'Create a folder named "learndash/groups-plus" at the root of the child-theme ( example: child-theme/%1$slearndash/groups-plus%2$s/ )',
						'learndash-groups-plus'
					),
					'<strong>',
					'</strong>'
				); ?>
			</li>
			<li>
				<?php printf(
					esc_html__(
						'Download: %s',
						'learndash-groups-plus'
					),
					'<a href="' . LEARNDASH_GROUPS_PLUS_URL . 'src/resources/data/template-learndash-groups-plus.php.txt" download="template-learndash-groups-plus.php">template-learndash-groups-plus.php</a>'
				); ?>
			</li>
			<li>
				<?php printf(
					esc_html__(
						'Edit this copy of the file on your local computer.',
						'learndash-groups-plus'
					)
				); ?>
			</li>
			<li>
				<?php printf(
					esc_html__(
						'Edit the "%1$stemplate-learndash-groups-plus.php%2$s" file to your liking.',
						'learndash-groups-plus'
					),
					'<strong>',
					'</strong>'
				); ?>
			</li>
			<li>
				<?php printf(
					esc_html__(
						'Upload the "%1$stemplate-learndash-groups-plus.php%2$s" file to your %3$schild-theme/learndash/groups-plus/%4$s folder when you have completed your code level changes.',
						'learndash-groups-plus'
					),
					'<strong>',
					'</strong>',
					'<strong>',
					'</strong>'
				); ?>
			</li>
		</ol>
		<p>
			<?php printf(
				esc_html__(
					'Visit the /learndash-groups-plus/ page in your website to view your changes.',
					'learndash-groups-plus'
				)
			); ?>
		</p>
		<p>
			<?php printf(
				esc_html__(
					'NOTE: Groups Plus contains CSS classes for most every aspect of the plugin and using your own custom CSS will allow you to target every aspect of the product.',
					'learndash-groups-plus'
				)
			); ?>
		</p>
		<p>
			<?php printf(
				esc_html__(
					'%1$sIf you want to customize the %2$s Detailed Report (located at the bottom of each %3$s List) template itself%4$s, here are the steps to getting that set up',
					'learndash-groups-plus'
				),
				'<strong>',
				learndash_get_custom_label( 'team_member' ),
				learndash_get_custom_label( 'team_member' ),
				'</strong>'
			); ?>:
		</p>
		<p>
			<?php printf(
				esc_html__(
					'As with the main page template file, you will need to place the shortcodes/team-member-report.php, and/or lesson-listing.php files into a new folder structure inside the /learndash/groups-plus/ folder in your child-theme.',
					'learndash-groups-plus'
				)
			); ?>
		</p>
		<ol>
			<li>
				<?php printf(
					esc_html__(
						'Create a folder named "learndash/groups-plus" at the root of the child-theme ( example: child-theme/%1$slearndash/groups-plus%2$s/ )',
						'learndash-groups-plus'
					),
					'<strong>',
					'</strong>'
				); ?>
			</li>
			<li>
				<?php printf(
					esc_html__(
						'Create a sub-folder named "templates" in the learndash/groups-plus folder of the child-theme ( example: child-theme/learndash/groups-plus/%1$stemplates%2$s/ )',
						'learndash-groups-plus'
					),
					'<strong>',
					'</strong>'
				); ?>
			</li>
			<li>
				<?php printf(
					esc_html__(
						'Create a sub-folder named "shortcodes" in the templates folder of the child-theme ( example: child-theme/learndash/groups-plus/templates/%1$sshortcodes%2$s/ )',
						'learndash-groups-plus'
					),
					'<strong>',
					'</strong>'
				); ?>
			</li>
			<li>
				<?php printf(
					esc_html__(
						'Create a sub-folder named "team-member" in the templates folder of the child-theme ( example: child-theme/learndash/groups-plus/templates/%1$steam-member%2$s/ )',
						'learndash-groups-plus'
					),
					'<strong>',
					'</strong>'
				); ?>
			</li>
			<li>
				<?php printf(
					esc_html__(
						'In the plugin folder ( named: learndash-groups-plus ), locate the same folders and copy/download the files you want to template.',
						'learndash-groups-plus'
					)
				); ?>
			</li>
			<li>
				<?php printf(
					esc_html__(
						'You can download these files (%1$sand create copies of them for backup purposes%2$s) to edit them on your local computer, then upload them into your child-theme/learndash/groups-plus/ sub-folder structure when you have completed your changes.',
						'learndash-groups-plus'
					),
					'<strong>',
					'</strong>'
				); ?>
			</li>
		</ol>
		<p>
			<?php printf(
				esc_html__(
					'Visit the /learndash-groups-plus/ page or any page with the Groups Plus shortcodes, in your website to view your changes.',
					'learndash-groups-plus'
				),
				'<strong>',
				'</strong>'
			); ?>
		</p>
		<p>
			<?php printf(
				esc_html__(
					'Groups Plus contains CSS classes for most every aspect of the plugin and using your own custom CSS will allow you to target every aspect of the product.',
					'learndash-groups-plus'
				)
			); ?>
		</p>
		<p>
			<?php esc_html_e(
				'Groups Plus addon is also 100% translatable.',
				'learndash-groups-plus'
			); ?>
		</p>
		<p>
			<?php printf(
				esc_html__(
					'%1$sIf you want to customize the Team Modals%2$s, here are the steps to getting that set up.',
					'learndash-groups-plus'
				),
				'<strong>',
				'</strong>'
			); ?>
		</p>
		<p>
			<?php esc_html_e(
				'As with the main page template file, you will need to place the add-team-leader.php and/or the add-team-member.php files into a new folder structure inside the /learndash/groups-plus/ folder in your child-theme.',
				'learndash-groups-plus'
			); ?>
		</p>
		<ol>
			<li>
				<?php printf(
					esc_html__(
						'Create a folder named "learndash/groups-plus" at the root of the child-theme ( example: child-theme/%1$slearndash/groups-plus%2$s/ )',
						'learndash-groups-plus'
					),
					'<strong>',
					'</strong>'
				); ?>
			</li>
			<li>
				<?php printf(
					esc_html__(
						'Download the "template-learndash-groups-plus-group.php" file below and, using your FTP client, upload it to your child-theme/%1$slearndash/groups-plus%2$s/ folder ensuring you save it as the same filename in that folder. This file contains code that will direct team to use the files in your child-theme folders.',
						'learndash-groups-plus'
					),
					'<strong>',
					'</strong>'
				); ?>
				<br>
				<?php printf(
					esc_html__(
						'Download: %s',
						'learndash-groups-plus'
					),
					'<a href="' . esc_url( LEARNDASH_GROUPS_PLUS_URL . 'src/resources/data/template-learndash-groups-plus-group.php.txt' ) .'" download="template-learndash-groups-plus-group.php">template-learndash-groups-plus-group.php</a>'
				); ?>
			</li>
			<li>
				<?php printf(
					esc_html__(
						'To customize the Add Team Leader modal, create a folder named "team" inside the "learndash/groups-plus" folder in the child-theme ( example: child-theme/%1$slearndash/groups-plus/team%2$s/ ). This folder will contain modal files used at the team level.',
						'learndash-groups-plus'
					),
					'<strong>',
					'</strong>'
				); ?>
				<br>
				<?php printf(
					esc_html__(
						'Download: %s',
						'learndash-groups-plus'
					),
					'<a href="' . esc_url( LEARNDASH_GROUPS_PLUS_URL . 'src/resources/data/modal.php.txt' ) . '" download="modal.php">modal.php</a>'
				); ?>
			</li>
			<li>
				<?php printf(
					esc_html__(
						'To customize the Add Team Member modal, create a folder named "team-member" inside the "learndash/groups-plus" folder in the child-theme ( example: child-theme/%1$slearndash/groups-plus/team-member%2$s/ ). This folder will contain modal files used at the team level.',
						'learndash-groups-plus'
					),
					'<strong>',
					'</strong>'
				); ?>
				<br>
				<?php printf(
					esc_html__(
						'Download: %s',
						'learndash-groups-plus'
					),
					'<a href="' . esc_url( LEARNDASH_GROUPS_PLUS_URL . 'src/resources/data/add-team-member.php.txt' ) . '" download="add-team-member.php">add-team-member.php</a>'
				); ?>
			</li>
		</ol>
	</div>
	<div id="b" class="tabcontent">
		<p>
			<strong>
				<?php esc_html_e( 'What are hooks?', 'learndash-groups-plus' ); ?>
			</strong>
		</p>
		<p>
			<?php esc_html_e( 'Hooks are PHP or other specially developed code added into a product as a defined part in the code that can pass control to other custom code.', 'learndash-groups-plus' ); ?>
		</p>
		<p>
			<?php esc_html_e( 'Groups Plus contain hooks that can be used to share information with other products. As new versions of Groups Plus are released, we will publish new hooks below.', 'learndash-groups-plus' ); ?>
		</p>
		<p>
			<?php esc_html_e( 'Here is the list of current hooks available in Groups Plus along with working examples you can copy / paste to capture data or view results:', 'learndash-groups-plus' ); ?>
		</p>
		<hr/>
		<h4>
			<?php printf(
				esc_html__( '%s Actions', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team_leader' )
			); ?>
		</h4>
		<div class="hook-left">
			<?php printf(
				esc_html__( 'Hook fired when a %s is created', 'learndash-groups-plus' ),
				learndash_get_custom_label_lower( 'team_leader' )
			); ?>: <br />
			<code>do_action( 'create_team_leader', $team_leader_user_id, $userdata );</code>
			<div class="hook-right">
				<?php esc_html_e( 'Example usage', 'learndash-groups-plus' ); ?>: <br/>
				<code>
					function my_create_team_leader($team_leader_id, $data){<br/>
						error_log($team_leader_id);<br/>
						error_log(print_r($data, true));<br/>
					}<br/>
					add_action('create_team_leader', "my_create_team_leader",10,2);
				</code>
			</div>
		</div>
		<div class="hook-left">
			<?php printf(
				esc_html__( 'Hook fired when a %s is deleted', 'learndash-groups-plus' ),
				learndash_get_custom_label_lower( 'team_leader' )
			); ?>: <br />
			<code>do_action( 'delete_team_leader', $team_leader_user_id, $userdata );</code>
			<div class="hook-right">
			<?php esc_html_e( 'Example usage', 'learndash-groups-plus' ); ?>: <br/>
				<code>
					function my_delete_team_leader($team_leader_id, $data){<br/>
						error_log($team_leader_id);<br/>
						error_log(print_r($data, true));<br/>
					}<br/>
					add_action('delete_team_leader', "my_delete_team_leader",10,2);
				</code>
			</div>
		</div>
		<hr/>
		<h4>
			<?php printf(
				esc_html__( '%s Actions', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team_member' )
			); ?>
		</h4>
		<div class="hook-left">
			<?php printf(
				esc_html__( 'Hook fired when a %s is created', 'learndash-groups-plus' ),
				learndash_get_custom_label_lower( 'team_member' )
			); ?>: <br />
			<code>do_action( 'create_team_member', $user_id, $userdata );</code>
			<div class="hook-right">
				<?php esc_html_e( 'Example usage', 'learndash-groups-plus' ); ?>: <br/>
				<code>
					function my_create_team_member($team_member_id, $data){<br/>
						error_log($team_member_id);<br/>
						error_log(print_r($data, true));<br/>
					}<br/>
					add_action('create_team_member', "my_create_team_member",10,2);
				</code>
			</div>
		</div>
		<div class="hook-left">
			<?php printf(
				esc_html__( 'Hook fired when a %s is deleted', 'learndash-groups-plus' ),
				learndash_get_custom_label_lower( 'team_member' )
			); ?>:<br />
			<code>do_action( 'delete_team_member', $user_id, $userdata );</code>
			<div class="hook-right">
				<?php esc_html_e( 'Example usage', 'learndash-groups-plus' ); ?>: <br/>
				<code>
					function my_delete_team_member($team_member_id, $data){<br/>
						error_log($team_member_id);<br/>
						error_log(print_r($data, true));<br/>
					}<br/>
					add_action('delete_team_member', "my_delete_team_member",10,2);
				</code>
			</div>
		</div>
		<hr/>
		<h4>
			<?php _e( 'Groups Plus Actions', 'learndash-groups-plus' ); ?>
		</h4>
		<div class="hook-left">
			<?php printf(
				esc_html__( 'Hook fired when a %s is created', 'learndash-groups-plus' ),
				learndash_get_custom_label_lower( 'team' )
			); ?>: <br/>
			<code>do_action( 'create_groups_plus', $group_id );</code>
			<div class="hook-right">
				<?php esc_html_e( 'Example usage', 'learndash-groups-plus' ); ?>: <br/>
				<code>
					function my_create_groups_plus($groups_plus_id, $data){<br/>
						error_log($groups_plus_id);<br/>
						error_log(print_r($data, true));<br/>
					}<br/>
					add_action('create_groups_plus', "my_create_groups_plus",10,2);
				</code>
			</div>
		</div>
		<div class="hook-left">
			<?php printf(
				esc_html__( 'Hook fired when a %s is deleted', 'learndash-groups-plus' ),
				learndash_get_custom_label_lower( 'team' )
			); ?>: <br/>
			<code>do_action( 'delete_groups_plus', $group_id );</code>
			<div class="hook-right">
				<?php esc_html_e( 'Example usage', 'learndash-groups-plus' ); ?>: <br/>
				<code>
					function my_delete_groups_plus($groups_plus_id, $data){<br/>
						error_log($groups_plus_id);<br/>
						error_log(print_r($data, true));<br/>
					}<br/>
					add_action('delete_groups_plus', "my_delete_groups_plus",10,2);
				</code>
			</div>
		</div>
		<h4>
			<?php printf(
				esc_html__( 'Creating your own buttons on the Edit %s modal before the default buttons', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team_member' )
			); ?>
		</h4>
		<div class="hook-left">
			<?php printf(
				esc_html__( 'A hook to create your own button before default buttons:%1$sNote: %2$s would be %3$s %4$s ID (child %5$s of LearnDash)', 'learndash-groups-plus' ),
				'<br/>',
				'$group_id',
				learndash_get_custom_label_lower( 'team' ),
				learndash_get_custom_label_lower( 'group' ),
				learndash_get_custom_label_lower( 'group' )
			); ?> <br/>
			<code>do_action('before_edit_team_member_button', $user_id, $group_id);</code>
			<div class="hook-right">
				<?php esc_html_e( 'Example usage', 'learndash-groups-plus' ); ?>: <br/>
				<code>
					function my_before_edit_team_member_button($user_id, $group_id){<br/>
						echo "< button class="btn button" onclick="myFunction()">Do something</button >";<br/>
					}<br/>
					add_action('before_edit_team_member_button', "my_before_edit_team_member_button",10,2);
				</code>
			</div>
		</div>
		<h4>
			<?php printf(
				esc_html__( 'Creating your own buttons on the Edit %s modal after the default buttons', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team_member' )
			); ?>
		</h4>
		<div class="hook-left">
			<?php printf(
				esc_html__( 'A hook to create your own button after default buttons:%1$sNote: %2$s would be %3$s %4$s ID (child %5$s of LearnDash)', 'learndash-groups-plus' ),
				'<br/>',
				'$group_id',
				learndash_get_custom_label_lower( 'team' ),
				learndash_get_custom_label_lower( 'group' ),
				learndash_get_custom_label_lower( 'group' )
			); ?><br />
			<code>do_action('after_edit_team_member_button', $user_id, $group_id);</code>
			<div class="hook-right">
				<?php esc_html_e( 'Example usage', 'learndash-groups-plus' ); ?>: <br/>
				<code>
					function my_after_edit_team_member_button($user_id, $group_id){<br/>
						echo "< button class="btn button" onclick="myFunction()">Do something</button >";<br/>
					}<br/>
					add_action('after_edit_team_member_button', "my_after_edit_team_member_button",10,2);
				</code>
			</div>
		</div>
		<h4>
			<?php printf(
				esc_html__( 'Hard-code a %1$s to be automatically locked for all %2$s throughout your site', 'learndash-groups-plus' ),
				learndash_get_custom_label_lower( 'lesson' ),
				learndash_get_custom_label_lower( 'team_members' )
			); ?>
		</h4>
		<div class="hook-left">
			<?php printf(
				esc_html__( 'Function to automatically lock a %1$s in a %2$s for all users in your site:%3$sWARNING:%4$s This function must be disabled in order to release the locked %5$s%6$sTo disable this function, you can comment out the  "add_filter" line by adding%7$s" // " to the left of it (" // add_filter ")', 'learndash-groups-plus' ),
				learndash_get_custom_label_lower( 'lesson' ),
				learndash_get_custom_label_lower( 'course' ),
				'<br/><strong>',
				'</strong>',
				learndash_get_custom_label_lower( 'lesson' ),
				'<br/>',
				'<br/>'
			);
			?>
			<div class="hook-right">
				<?php esc_html_e( 'Example usage', 'learndash-groups-plus' ); ?>: <br/>
				<code>
					/**<br/>
					 * @param bool $setting true if course progression is enabled.<br/>
					 * @param int  $course_id Course ID.<br/>
					**/<br/>
					<br/>
					function learndash_course_progression_enabled($setting, $course_id) {<br/>
						global $post;<br/>
						$lock_course_id = 101; // The course ID in linear mode<br/>
						$lock_lesson_id = 202; // The lesson to be locked<br/>
						if( $lock_course_id === $course_id && $lock_lesson_id === $post->ID ) {<br/>
							return true;<br/>
						}<br/>
						return $setting;<br/>
					}<br/>
					add_filter( "learndash_course_progression_enabled", "my_learndash_course_progression_enabled", 10, 2 );
				</code>
			</div>
		</div>
		<hr/>
		<h4>
			<?php esc_html_e( 'Change wording of WooCommerce product page message', 'learndash-groups-plus' ); ?>
		</h4>
		<div class="hook-left">
			<p>
				<?php esc_html_e( 'A hook to change the "Exclude from publicly sold seats" WooCommerce text in the product page.', 'learndash-groups-plus' ); ?>
			</p>
			<div class="hook-right">
				<?php esc_html_e( 'Example usage', 'learndash-groups-plus' ); ?>: <br/>
				<code>
					add_filter( 'change_label_of_exclude_from_publicly_sold_seats', function() {<br/>
						return 'Custom label';<br/>
					} );
				</code>
			</div>
		</div>
	</div>
</div><!-- ld-adm-container -->

<script type="text/javascript">
function openCity(evt, cityName) {
  // Declare all variables
  var i, tabcontent, tablinks;

  // Get all elements with class="tabcontent" and hide them
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
	tabcontent[i].style.display = "none";
  }

  // Get all elements with class="tablinks" and remove the class "active"
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
	tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Show the current tab, and add an "active" class to the button that opened the tab
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>

		<?php
	}

	public static function design_create_setting() {
		$hide_exclude_from_publicly_sold_seats_checkbox = get_site_option( 'hide_exclude_from_publicly_sold_seats_checkbox' );
		$hide_groups_plus_header                        = get_site_option( 'hide_groups_plus_header', 'no' );
		$change_the_used_seats_icon_color               = get_site_option( 'change_the_used_seats_icon_color', '#000' );
		$hide_seats_used_text                           = get_site_option( 'hide_seats_used_text', 'no' );
		$change_seats_remaining_icon_color              = get_site_option( 'change_seats_remaining_icon_color', '#000' );
		$hide_seats_ramaining_text                      = get_site_option( 'hide_seats_ramaining_text', 'no' );

		$header_background_color = get_site_option( 'header_background_color', '#EAEAEA' );
		$header_border_color     = get_site_option( 'header_border_color', '#231F20' );

		$hide_organization_email_button        = get_site_option( 'hide_organization_email_button', 'no' );
		$hide_organization_broadcast_email_tab = get_site_option( 'hide_organization_broadcast_email_tab', 'no' );
		$hide_organization_welcome_email_tab   = get_site_option( 'hide_organization_welcome_email_tab', 'no' );

		$hide_manage_organization_button     = get_site_option( 'hide_manage_organization_button', 'no' );
		$hide_organization_manage_groups_plus_tab = get_site_option( 'hide_organization_manage_groups_plus_tab', 'no' );
		$hide_organization_add_team_tab    = get_site_option( 'hide_organization_add_team_tab', 'no' );

		$hide_organization_delete_groups_plus_icon = get_site_option( 'hide_organization_delete_groups_plus_icon', 'no' );
		$hide_organization_export_csv_button       = get_site_option( 'hide_organization_export_csv_button', 'no' );

		$hide_organization_manage_team_leaders_tab        = get_site_option( 'hide_organization_manage_team_leaders_tab', 'no' );
		$hide_organization_add_team_leaders_tab           = get_site_option( 'hide_organization_add_team_leaders_tab', 'no' );
		$hide_organization_add_team_leader_as_team_member_tab = get_site_option( 'hide_organization_add_team_leader_as_team_member_tab', 'no' );

		$hide_organization_delete_team_leader_trashcan_icon = get_site_option( 'hide_organization_delete_team_leader_trashcan_icon', 'no' );
		$hide_organization_delete_team_leader_person_x_icon = get_site_option( 'hide_organization_delete_team_leader_person_x_icon', 'no' );

		$hide_organization_manage_team_leaders_button = get_site_option( 'hide_organization_manage_team_leaders_button', 'no' );
		$hide_organization_manage_course_button   = get_site_option( 'hide_organization_manage_course_button', 'no' );

		$hide_groups_plus_add_team_member_button = get_site_option( 'hide_groups_plus_add_team_member_button', 'no' );

		$hide_groups_plus_import_list_button       = get_site_option( 'hide_groups_plus_import_list_button', 'no' );
		$hide_groups_plus_email_groups_plus_button = get_site_option( 'hide_groups_plus_email_groups_plus_button', 'no' );

		$hide_groups_plus_broadcast_email_tab       = get_site_option( 'hide_groups_plus_broadcast_email_tab', 'no' );
		$hide_groups_plus_team_member_welcome_email_tab = get_site_option( 'hide_groups_plus_team_member_welcome_email_tab', 'no' );
		$hide_groups_plus_edit_team_member_button       = get_site_option( 'hide_groups_plus_edit_team_member_button', 'no' );
		$hide_change_password_team_member_button        = get_site_option( 'hide_change_password_team_member_button', 'no' );

		$hide_groups_plus_edit_team_member_remove_icon_button = get_site_option( 'hide_groups_plus_edit_team_member_remove_icon_button', 'no' );
		$hide_groups_plus_edit_team_member_delete_icon_button = get_site_option( 'hide_groups_plus_edit_team_member_delete_icon_button', 'no' );

		$hide_groups_plus_export_csv_button = get_site_option( 'hide_groups_plus_export_csv_button', 'no' );

		$hide_groups_plus_lock_column_in_detail_team_member_report = get_site_option( 'hide_groups_plus_lock_column_in_detail_team_member_report', 'no' );

		$design_setting                        = get_site_option( 'design_setting' );
		$assignment_grade_button_bg_color      = get_site_option( 'assignment_grade_button_bg_color', '#FF0000' );
		$assignment_grade_button_text_color    = get_site_option( 'assignment_grade_button_text_color', '#FFFFFF' );
		$assignment_resubmit_button_bg_color   = get_site_option( 'assignment_resubmit_button_bg_color', '#FFA500' );
		$assignment_resubmit_button_text_color = get_site_option( 'assignment_resubmit_button_text_color', '#FFFFFF' );
		$assignment_approved_button_bg_color   = get_site_option( 'assignment_approved_button_bg_color', '#008000' );
		$assignment_approved_button_text_color = get_site_option( 'assignment_approved_button_text_color', '#FFFFFF' );
		$assignment_buttons_border_radius      = get_site_option( 'assignment_buttons_border_radius', '20px' );

		if ( ! is_array( $design_setting ) ) {
			$design_setting = array( 'color_codes' => array() );
		}

		extract( $design_setting['color_codes'] );

		// Order By Settings
		$group_orderby = ! empty( get_site_option( 'group_orderby' ) ) ? get_site_option( 'group_orderby' ) : 'title';
		$group_order   = ! empty( get_site_option( 'group_order' ) ) ? get_site_option( 'group_order' ) : 'ASC';

		$course_orderby = ! empty( get_site_option( 'course_orderby' ) ) ? get_site_option( 'course_orderby' ) : 'title';
		$course_order   = ! empty( get_site_option( 'course_order' ) ) ? get_site_option( 'course_order' ) : 'ASC';

		$user_orderby = ! empty( get_site_option( 'user_orderby' ) ) ? get_site_option( 'user_orderby' ) : 'display_name';
		$user_order   = ! empty( get_site_option( 'user_order' ) ) ? get_site_option( 'user_order' ) : 'ASC';
		?>
	<div class="ld-adm-container">    
		<form action="" enctype="multipart/form-data" id="design_form" method="post" name="design_form">
			<?php wp_nonce_field( 'learndash_groups_plus_setting_form' ); ?>
			<h3><?php _e( 'Design Settings', 'learndash-groups-plus' ); ?></h3>
			<table class="ld-form-table design">
				<tbody>
					<tr valign="top">
						<td>
							<h3 class="ld-settings-table-header"><?php _e( 'Handy Links', 'learndash-groups-plus' ); ?></h3>
						</td>
					</tr>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<p><?php _e( 'If you cannot find the colors you want to use throughout this page, you can find your colors codes using <a href="https://www.w3.org/wiki/CSS/Properties/color/keywords" target="_blank">Hexadecimal, RGB, RGBA, or Color Keywords</a>.<br/>Examples: HEX: #123456, Keyword: red, RGB: rgb(100,10,200), RGBA: rgba(100,10,200,0.5).', 'learndash-groups-plus' ); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="ld-form-table design">
				<tbody>
					<tr valign="top">
						<td>
							<h3 class="ld-settings-table-header"><?php _e( 'WooCommerce Product Page', 'learndash-groups-plus' ); ?></h3>
						</td>
					</tr>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label
								for="hide_exclude_from_publicly_sold_seats_checkbox"><b><?php _e( 'Exclude from publicly sold seats:', 'learndash-groups-plus' ); ?></b></label>
							<label class="ld-switch">
								<input id="hide_exclude_from_publicly_sold_seats_checkbox" name="hide_exclude_from_publicly_sold_seats_checkbox" type="checkbox" value="yes"
									<?php echo $hide_exclude_from_publicly_sold_seats_checkbox === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description"><?php _e( 'Remove the "Exclude from publicly sold seats" table row from the Woo Product pages it is used in.', 'learndash-groups-plus' ); ?></div>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="ld-form-table design">
				<tbody>
					<tr valign="top">
						<td colspan="3">
							<h3 class="ld-settings-table-header">
								<?php
									esc_html_e( 'Groups Plus Header', 'learndash-groups-plus' );
								?>
							</h3>
							<center>
								<img src="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'build/images/organization-header.png'; ?>"/>
							</center>
						</td>
					</tr>
					<tr>
						<td class="ld-design-block forminp forminp-text">
							<label for="hide_groups_plus_header">
								<strong>
									<?php esc_html_e( 'Disable Groups Plus Header', 'learndash-groups-plus' ); ?>:
								</strong>
							</label>
							<label class="ld-switch">
								<input id="hide_groups_plus_header" name="hide_groups_plus_header" type="checkbox" value="yes"
									<?php echo $hide_groups_plus_header === 'yes' ? 'checked' : ''; ?> onclick="document.getElementById('ClassHeaderOn').style.display=(this.checked)?'none':'block';">
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php
									esc_html_e( 'Completely remove the groups plus header.', 'learndash-groups-plus' );
								?>
							</div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label
								for="change_the_used_seats_icon_color"><b><?php _e( 'Change the Used Seats icon or icon color:', 'learndash-groups-plus' ); ?></b></label>
							<div class="setting-description"><?php _e( 'Replace the Seats Used icon with your own icon.', 'learndash-groups-plus' ); ?></div>
							<dd>
								<a href="#" class="learndash-groups-plus-uploader ld-design-input"><?php _e( 'Upload image', 'learndash-groups-plus' ); ?></a>
								<?php
									$change_the_used_seats_attachment_id = get_site_option( 'change_the_used_seats_attachment_id' );
								if ( isset( $change_the_used_seats_attachment_id ) && ! empty( $change_the_used_seats_attachment_id ) ) {
									echo '<a href="#" class="learndash-groups-plus-uploader-remove-image"><i class="fas fa-times"></i></a>';
								}
								?>
								<input class="ld-design-input" type="hidden" name="change_the_used_seats_attachment_id" value="<?php echo $change_the_used_seats_attachment_id ?? ''; ?>">
								
							</dd>
							<div class="setting-description"><?php _e( 'Leave the icon and change the icon color.', 'learndash-groups-plus' ); ?></div>
							<dd>
								<input autocomplete="off" class="" id="change_the_used_seats_icon_color" name="change_the_used_seats_icon_color" style=""
								type="text" value="<?php echo isset( $change_the_used_seats_icon_color ) ? esc_html( $change_the_used_seats_icon_color ) : ''; ?>">
							</dd>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label
								for="hide_seats_used_text"><b><?php _e( 'Remove Seats Used text:', 'learndash-groups-plus' ); ?></b></label>
							<label class="ld-switch">
								<input id="hide_seats_used_text" name="hide_seats_used_text" type="checkbox" value="yes"
									<?php echo $hide_seats_used_text === 'yes' ? 'checked' : ''; ?> onclick="document.getElementById('SeatsUsedText').style.display=(this.checked)?'none':'block';">
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php
									esc_html_e( 'Remove Seats Used text from groups plus header.', 'learndash-groups-plus' );
								?>
							</div>
						</td>
					</tr>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label
								for="change_seats_remaining_icon_color"><b><?php _e( 'Change the Seats Remaining icon:', 'learndash-groups-plus' ); ?></b></label>
							<div class="setting-description"><?php _e( 'Replace the Seats Remaining icon with your own icon.', 'learndash-groups-plus' ); ?></div>
							<dd>
								<a href="#" class="learndash-groups-plus-uploader ld-design-input"><?php _e( 'Upload image', 'learndash-groups-plus' ); ?></a>
								<?php
									$change_seats_remaining_attachment_id = get_site_option( 'change_seats_remaining_attachment_id' );
								if ( isset( $change_seats_remaining_attachment_id ) && ! empty( $change_seats_remaining_attachment_id ) ) {
									echo '<a href="#" class="learndash-groups-plus-uploader-remove-image"><i class="fas fa-times"></i></a>';
								}
								?>
								
								<input type="hidden" name="change_seats_remaining_attachment_id" value="<?php echo $change_seats_remaining_attachment_id ?? ''; ?>">
							</dd>
							<div class="setting-description"><?php _e( 'Leave the icon and change the icon color.', 'learndash-groups-plus' ); ?></div>
							<dd>
								<input autocomplete="off" class="" id="change_seats_remaining_icon_color" name="change_seats_remaining_icon_color" style=""
								type="text" value="<?php echo isset( $change_seats_remaining_icon_color ) ? esc_html( $change_seats_remaining_icon_color ) : ''; ?>">
							</dd>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label
								for="hide_seats_ramaining_text"><b><?php _e( 'Remove Seats Remaining text:', 'learndash-groups-plus' ); ?></b></label>
							<label class="ld-switch">
								<input id="hide_seats_ramaining_text" name="hide_seats_ramaining_text" type="checkbox" value="yes"
									<?php echo $hide_seats_ramaining_text === 'yes' ? 'checked' : ''; ?> onclick="document.getElementById('SeatsRemainingText').style.display=(this.checked)?'none':'block';">
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php
									esc_html_e( 'Remove Seats Remaining text from groups plus header.', 'learndash-groups-plus' );	
								?>
							</div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<div style="margin-bottom:20px;">
								<label
									for="header_background_color"><b><?php _e( 'Header Background color:', 'learndash-groups-plus' ); ?></b></label>
								<dd class="ld-design-input">
									<input autocomplete="off" class="" id="header_background_color" name="header_background_color" style=""
									type="text" value="<?php echo isset( $header_background_color ) ? esc_html( $header_background_color ) : ''; ?>">
								</dd>
							</div>
							<div>
								<label
									for="header_border_color"><b><?php _e( 'Header Border color:', 'learndash-groups-plus' ); ?></b></label>
								<dd class="ld-design-input">
									<input autocomplete="off" class="" id="header_border_color" name="header_border_color" style=""
									type="text" value="<?php echo isset( $header_border_color ) ? esc_html( $header_border_color ) : ''; ?>">
								</dd>
							</div>
							<div class="setting-description">
								<?php
									esc_html_e( 'Change groups plus header background and border colors. Delete to reset.', 'learndash-groups-plus' );	
								?>
							</div>
						</td>
					</tr>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label for="hide_organization_email_button">
								<strong><?php printf( esc_html__( 'Disable %s level Email button:', 'learndash-groups-plus' ), learndash_get_custom_label( 'organization' ) ); ?></strong>
							</label>
							<label class="ld-switch">
								<input id="hide_organization_email_button" name="hide_organization_email_button" type="checkbox" value="yes"
									<?php echo $hide_organization_email_button === 'yes' ? 'checked' : ''; ?> onclick="document.getElementById('OrganizationEmailButton').style.display=(this.checked)?'none':'block';">
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf( esc_html__( 'Turn %s level Email button On or Off', 'learndash-groups-plus' ), learndash_get_custom_label_lower( 'organization' ) ); ?>
							</div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label for="hide_manage_organization_button">
								<strong><?php printf(
									esc_html__( 'Disable Manage %s button:', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'organization' )
								); ?></strong>
							</label>
							<label class="ld-switch">
								<input id="hide_manage_organization_button" name="hide_manage_organization_button" type="checkbox" value="yes"
									<?php echo $hide_manage_organization_button === 'yes' ? 'checked' : ''; ?> onclick="document.getElementById('ManageClassButton').style.display=(this.checked)?'none':'block';">
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf( esc_html__( 'Turn Manage %s button On or Off', 'learndash-groups-plus' ), learndash_get_custom_label( 'organization' ) ); ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="ld-form-table design">
				<tbody>
					<tr valign="top">
						<td colspan="3">
							<h3 class="ld-settings-table-header">
								<?php printf( esc_html__( '%s Email Modal', 'learndash-groups-plus' ), learndash_get_custom_label( 'organization' ) ); ?>
							</h3>
							<center>
								<img src="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'build/images/organization-email-modal.png'; ?>"/>
							</center>
						</td>
					</tr>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label for="hide_organization_broadcast_email_tab">
								<strong><?php printf( esc_html__( 'Disable %s level Broadcast Email tab:', 'learndash-groups-plus' ), learndash_get_custom_label( 'organization' ) ); ?></strong>
							</label>
							<label class="ld-switch">
								<input id="hide_organization_broadcast_email_tab" name="hide_organization_broadcast_email_tab" type="checkbox" value="yes"
									<?php echo $hide_organization_broadcast_email_tab === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf(
									esc_html__( 'Turn %s level Broadcast Email tab On or Off', 	'learndash-groups-plus' ),
									learndash_get_custom_label( 'organization' )
								); ?>
							</div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label for="hide_organization_welcome_email_tab">
								<strong>
									<?php printf(
										esc_html__( 'Disable %s level Welcome Email tab:', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'organization' )
									); ?>
								</strong>
							</label>
							<label class="ld-switch">
								<input id="hide_organization_welcome_email_tab" name="hide_organization_welcome_email_tab" type="checkbox" value="yes"
									<?php echo $hide_organization_welcome_email_tab === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf( esc_html__( 'Turn %s level Welcome Email tab On or Off', 'learndash-groups-plus' ), learndash_get_custom_label( 'organization' ) ); ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="ld-form-table design">
				<tbody>
					<tr valign="top">
						<td colspan="3">
							<h3 class="ld-settings-table-header">
								<?php printf(
									esc_html__( '%1$s Manage %2$s Modal', 	'learndash-groups-plus' ),
									learndash_get_custom_label( 'organization' ),
									learndash_get_custom_label( 'team' ),
								); ?>
							</h3>
							<center>
								<img src="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'build/images/organization-manage-team-modal.png'; ?>"/>
							</center>
						</td>
					</tr>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label for="hide_organization_manage_groups_plus_tab">
								<strong>
									<?php printf(
										esc_html__( 'Disable Manage %s tab:', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'team' )
									); ?>:
								</strong>
							</label>
							<label class="ld-switch">
								<input id="hide_organization_manage_groups_plus_tab" name="hide_organization_manage_groups_plus_tab" type="checkbox" value="yes"
									<?php echo $hide_organization_manage_groups_plus_tab === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf(
									esc_html__( 'Turn Manage %s tab On or Off', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team' )
								); ?>
							</div>
						</td>
						<td class="ld-design-block forminp forminp-text"> 
							<label for="hide_organization_add_team_tab">
								<b>
									<?php printf(
										esc_html__( 'Disable Add %s tab', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'team' )
									); ?>:
								</b>
							</label>
							<label class="ld-switch">
								<input id="hide_organization_add_team_tab" name="hide_organization_add_team_tab" type="checkbox" value="yes"
									<?php echo $hide_organization_add_team_tab === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf(
									esc_html__( 'Turn Add %s tab On or Off', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team' )
								); ?>
							</div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label for="hide_organization_delete_groups_plus_icon">
								<strong>
									<?php printf(
										esc_html__( 'Disable Delete %s icon', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'team' )
									); ?>:
								</strong>
							</label>
							<label class="ld-switch">
								<input id="hide_organization_delete_groups_plus_icon" name="hide_organization_delete_groups_plus_icon" type="checkbox" value="yes"
									<?php echo $hide_organization_delete_groups_plus_icon === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf(
									esc_html__( 'Turn Delete %s icon On or Off', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team' )
								); ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="ld-form-table design">
				<tbody>
					<tr valign="top">
						<td colspan="3">
							<h3 class="ld-settings-table-header">
								<?php printf(
									esc_html__( '%1$s %2$s List', 	'learndash-groups-plus' ),
									learndash_get_custom_label( 'organization' ),
									learndash_get_custom_label( 'team' )
								); ?>
							</h3>
							<center>
								<img src="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'build/images/organization-team-list.png'; ?>"/>
							</center>
						</td>
					</tr>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label for="hide_organization_manage_team_leaders_button">
								<strong>
									<?php printf(
										esc_html__( 'Disable Manage %s button', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'team_leaders' )
									); ?>:
								</strong>
							</label>
							<label class="ld-switch">
								<input id="hide_organization_manage_team_leaders_button" name="hide_organization_manage_team_leaders_button" type="checkbox" value="yes"
									<?php echo $hide_organization_manage_team_leaders_button === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf(
									esc_html__( 'Turn Manage %s button On or Off', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team_leaders' )
								); ?>
							</div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label
								for="hide_organization_manage_course_button"><b><?php _e( 'Disable Manage Courses button:', 'learndash-groups-plus' ); ?></b></label>
							<label class="ld-switch">
								<input id="hide_organization_manage_course_button" name="hide_organization_manage_course_button" type="checkbox" value="yes"
									<?php echo $hide_organization_manage_course_button === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description"><?php _e( 'Turn Manage Courses button On or Off', 'learndash-groups-plus' ); ?></div>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="ld-form-table design">
				<tbody>
					<tr valign="top">
						<td colspan="3">
							<h3 class="ld-settings-table-header">
								<?php printf(
									esc_html__( 'Manage %s Modal', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team_leaders' )
								); ?>
							</h3>
							<center>
								<img src="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'build/images/organization-manage-team-leaders-modal.png'; ?>"/>
							</center>
						</td>
					</tr>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label for="hide_organization_add_team_leaders_tab">
								<strong>
									<?php printf(
										esc_html__( 'Disable Add %s tab', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'team_leaders' )
									); ?>:
								</strong>
							</label>
							<label class="ld-switch">
								<input id="hide_organization_add_team_leaders_tab" name="hide_organization_add_team_leaders_tab" type="checkbox" value="yes"
									<?php echo $hide_organization_add_team_leaders_tab === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf(
									esc_html__( 'Turn Add %s tab On or Off', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team_leaders' )
								); ?>
							</div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label for="hide_organization_add_team_leader_as_team_member_tab">
								<b>
									<?php printf(
										esc_html__( 'Disable Add %1$s as %2$s tab', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'team_leader' ),
										learndash_get_custom_label( 'team_member' )
									); ?>:
								</b>
							</label>
							<label class="ld-switch">
								<input id="hide_organization_add_team_leader_as_team_member_tab" name="hide_organization_add_team_leader_as_team_member_tab" type="checkbox" value="yes"
									<?php echo $hide_organization_add_team_leader_as_team_member_tab === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf(
									esc_html__( 'Turn Add %1$s as %2$s tab On or Off', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team_leader' ),
									learndash_get_custom_label( 'team_member' )
								); ?>
							</div>
						</td>
					</tr>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label for="hide_organization_delete_team_leader_trashcan_icon">
								<strong>
									<?php printf(
										esc_html__( 'Disable Delete %s trashcan icon', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'team_leader' )
									); ?>:
								</strong>
							</label>
							<label class="ld-switch">
								<input id="hide_organization_delete_team_leader_trashcan_icon" name="hide_organization_delete_team_leader_trashcan_icon" type="checkbox" value="yes"
									<?php echo $hide_organization_delete_team_leader_trashcan_icon === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf(
									esc_html__( 'Turn Delete %s trashcan icon On or Off', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team_leader' )
								); ?>
							</div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label for="hide_organization_delete_team_leader_person_x_icon">
								<strong>
									<?php printf(
										esc_html__( 'Disable Permanently Delete %s person-X icon:', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'team_leader' )
									); ?>:
								</strong>
							</label>
							<label class="ld-switch">
								<input id="hide_organization_delete_team_leader_person_x_icon" name="hide_organization_delete_team_leader_person_x_icon" type="checkbox" value="yes"
									<?php echo $hide_organization_delete_team_leader_person_x_icon === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf(
									esc_html__( 'Turn Permanently Delete %s person-X icon On or Off', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team_leader' )
								); ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="ld-form-table design">
				<tbody>
					<tr valign="top">
						<td colspan="3">
							<h3 class="ld-settings-table-header">
								<?php printf(
									esc_html__( 'Manage %s Report', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'organization' )
								); ?>
							</h3>
							<center>
								<img src="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'build/images/organization-report.png'; ?>"/>
							</center>
						</td>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label
								for="hide_organization_export_csv_button"><b><?php _e( 'Disable Export CSV button:', 'learndash-groups-plus' ); ?></b></label>
							<label class="ld-switch">
								<input id="hide_organization_export_csv_button" name="hide_organization_export_csv_button" type="checkbox" value="yes"
									<?php echo $hide_organization_export_csv_button === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf(
									esc_html__( 'Turn Export %s level CSV button On or Off', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'organization' )
								); ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="ld-form-table design">
				<tbody>
					<tr valign="top">
						<td colspan="3">
							<h3 class="ld-settings-table-header">
								<?php printf(
									esc_html__( 'Manage %s Header', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team' )
								); ?>
							</h3>
							<center>
								<img src="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'build/images/team-header.png'; ?>"/>
							</center>
						</td>
					</tr>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label for="hide_groups_plus_add_team_member_button">
								<strong>
									<?php printf(
										esc_html__( 'Disable Add %s button', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'team_member' )
									); ?>:
								</strong>
							</label>
							<label class="ld-switch">
								<input id="hide_groups_plus_add_team_member_button" name="hide_groups_plus_add_team_member_button" type="checkbox" value="yes"
									<?php echo $hide_groups_plus_add_team_member_button === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf(
									esc_html__( 'Turn Add %s button On or Off', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team_member' )
								); ?>
							</div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label
								for="hide_groups_plus_import_list_button"><b><?php _e( 'Disable Import List button:', 'learndash-groups-plus' ); ?></b></label>
							<label class="ld-switch">
								<input id="hide_groups_plus_import_list_button" name="hide_groups_plus_import_list_button" type="checkbox" value="yes"
									<?php echo $hide_groups_plus_import_list_button === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description"><?php _e( 'Turn Import List button On or Off', 'learndash-groups-plus' ); ?></div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label for="hide_groups_plus_email_groups_plus_button">
								<strong>
									<?php printf(
										esc_html__( 'Disable Email %s button', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'team' )
									); ?>:
								</strong>
							</label>
							<label class="ld-switch">
								<input id="hide_groups_plus_email_groups_plus_button" name="hide_groups_plus_email_groups_plus_button" type="checkbox" value="yes"
									<?php echo $hide_groups_plus_email_groups_plus_button === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf(
									esc_html__( 'Turn Email %s button On or Off', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team' )
								); ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="ld-form-table design">
				<tbody>
					<tr valign="top">
						<td colspan="3">
							<h3 class="ld-settings-table-header">
								<?php printf(
									esc_html__( 'Manage Email %s Modal', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team' )
								); ?>
							</h3>
							<center>
								<img src="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'build/images/email-team-modal.png'; ?>"/>
							</center>
						</td>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label
								for="hide_groups_plus_broadcast_email_tab"><b><?php _e( 'Disable Broadcast Email tab:', 'learndash-groups-plus' ); ?></b></label>
							<label class="ld-switch">
								<input id="hide_groups_plus_broadcast_email_tab" name="hide_groups_plus_broadcast_email_tab" type="checkbox" value="yes"
									<?php echo $hide_groups_plus_broadcast_email_tab === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description"><?php _e( 'Turn Broadcast Email tab On or Off', 'learndash-groups-plus' ); ?></div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label for="hide_groups_plus_team_member_welcome_email_tab">
								<strong>
									<?php printf(
										esc_html__( 'Disable %s Welcome Email tab', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'team_member' )
									); ?>:
								</strong>
							</label>
							<label class="ld-switch">
								<input id="hide_groups_plus_team_member_welcome_email_tab" name="hide_groups_plus_team_member_welcome_email_tab" type="checkbox" value="yes"
									<?php echo $hide_groups_plus_team_member_welcome_email_tab === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf(
									esc_html__( 'Turn %s Welcome Email tab On or Off', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team_member' )
								); ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="ld-form-table design">
				<tbody>
					<tr valign="top">
						<td colspan="3">
							<h3 class="ld-settings-table-header">
								<?php printf(
									esc_html__( '%s Roster', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team' )
								); ?>
							</h3>
							<center>
								<img src="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'build/images/team-roster.png'; ?>"/>
							</center>
						</td>
					</tr>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label for="hide_groups_plus_edit_team_member_button">
								<strong>
									<?php printf(
										esc_html__( 'Disable Edit %s button', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'team_member' )
									); ?>:
								</strong>
							</label>
							<label class="ld-switch">
								<input id="hide_groups_plus_edit_team_member_button" name="hide_groups_plus_edit_team_member_button" type="checkbox" value="yes"
									<?php echo $hide_groups_plus_edit_team_member_button === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf(
									esc_html__( 'Turn Edit %s button On or Off', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team_member' )
								); ?>
							</div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label
								for="hide_change_password_team_member_button"><b><?php _e( 'Disable Change Password button:', 'learndash-groups-plus' ); ?></b></label>
							<label class="ld-switch">
								<input id="hide_change_password_team_member_button" name="hide_change_password_team_member_button" type="checkbox" value="yes"
									<?php echo $hide_change_password_team_member_button === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description"><?php _e( 'Turn change Password button On or Off', 'learndash-groups-plus' ); ?></div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label
								for="hide_groups_plus_export_csv_button"><b><?php _e( 'Disable Export CSV button:', 'learndash-groups-plus' ); ?></b></label>
							<label class="ld-switch">
								<input id="hide_groups_plus_export_csv_button" name="hide_groups_plus_export_csv_button" type="checkbox" value="yes"
									<?php echo $hide_groups_plus_export_csv_button === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf(
									esc_html__( 'Turn Export %s level CSV button On or Off', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team' )
								); ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="ld-form-table design">
				<tbody>
					<tr valign="top">
						<td colspan="3">
							<h3 class="ld-settings-table-header">
								<?php printf(
									esc_html__( 'Edit %s Modal', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team_member' )
								); ?>
							</h3>
							<center>
								<img src="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'build/images/edit-team-member-modal.png'; ?>"/>
							</center>
						</td>
					</tr>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label
								for="hide_groups_plus_edit_team_member_remove_icon_button"><b><?php _e( 'Disable Remove icon:', 'learndash-groups-plus' ); ?></b></label>
							<label class="ld-switch">
								<input id="hide_groups_plus_edit_team_member_remove_icon_button" name="hide_groups_plus_edit_team_member_remove_icon_button" type="checkbox" value="yes"
									<?php echo $hide_groups_plus_edit_team_member_remove_icon_button === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf(
									esc_html__( 'Turn Remove %s icon On or Off', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team_member' )
								); ?>
							</div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label
								for="hide_groups_plus_edit_team_member_delete_icon_button"><b><?php _e( 'Disable Delete icon:', 'learndash-groups-plus' ); ?></b></label>
							<label class="ld-switch">
								<input id="hide_groups_plus_edit_team_member_delete_icon_button" name="hide_groups_plus_edit_team_member_delete_icon_button" type="checkbox" value="yes"
									<?php echo $hide_groups_plus_edit_team_member_delete_icon_button === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf(
									esc_html__( 'Turn Delete %s icon On or Off', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team_member' )
								); ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="ld-form-table design">
				<tbody>
					<tr valign="top">
						<td colspan="3">
							<h3 class="ld-settings-table-header">
								<?php printf(
									esc_html__( '%s Detailed Report', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team_member' )
								); ?>
							</h3>
							<center>
								<img src="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'build/images/team-report.png'; ?>"/>
							</center>
						</td>
					</tr>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label for="hide_groups_plus_lock_column_in_detail_team_member_report">
								<strong>
									<?php printf(
										esc_html__( 'Disable Lock column in Detailed %s Report', 'learndash-groups-plus' ),
										learndash_get_custom_label( 'team_member' )
									); ?>
								</strong>
							</label>
							<label class="ld-switch">
								<input id="hide_groups_plus_lock_column_in_detail_team_member_report" name="hide_groups_plus_lock_column_in_detail_team_member_report" type="checkbox" value="yes"
									<?php echo $hide_groups_plus_lock_column_in_detail_team_member_report === 'yes' ? 'checked' : ''; ?>>
								<span class="ld-slider round"></span>
							</label>
							<div class="setting-description">
								<?php printf(
									esc_html__( 'Turn Lock column in Detailed %s Report On or Off', 'learndash-groups-plus' ),
									learndash_get_custom_label( 'team_member' )
								); ?>
							</div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label
								for="not_start"><b><?php _e( 'Reporting Color - Not started:', 'learndash-groups-plus' ); ?></b></label>
							<dd class="ld-design-input">
								<input autocomplete="off" class="" id="not_start" name="color_codes[not_start]" style=""
								type="text" value="<?php echo isset( $not_start ) ? esc_html( $not_start ) : ''; ?>">
							</dd>
							<div class="setting-description"><?php _e( 'Change Not started label color', 'learndash-groups-plus' ); ?></div>
						</td>
					</tr>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label
								for="in_progress"><b><?php _e( 'Reporting Color - In Progress:', 'learndash-groups-plus' ); ?></b></label>
							<dd class="ld-design-input">
								<input autocomplete="off" class="" id="in_progress" name="color_codes[in_progress]" style=""
								type="text" value="<?php echo isset( $in_progress ) ? esc_html( $in_progress ) : ''; ?>">
							</dd>
							<div class="setting-description"><?php _e( 'Change In Progress label color', 'learndash-groups-plus' ); ?></div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label
								for="completed"><b><?php _e( 'Reporting Color - Completed:', 'learndash-groups-plus' ); ?></b></label>
							<dd class="ld-design-input">
								<input autocomplete="off" class="" id="completed" name="color_codes[completed]" style=""
								type="text" value="<?php echo isset( $completed ) ? esc_html( $completed ) : ''; ?>">
							</dd>
							<div class="setting-description"><?php _e( 'Change Completed label color', 'learndash-groups-plus' ); ?></div>
						</td>
					</tr>
					<?php
					if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
						?>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label
								for="in_progress"><b><?php _e( 'Assignment Grade Button Background Color:', 'learndash-groups-plus' ); ?></b></label>
							<dd class="ld-design-input">
								<input autocomplete="off" class="" id="assignment_grade_button_bg_color" name="assignment_grade_button_bg_color" style=""
								type="text" value="<?php echo isset( $assignment_grade_button_bg_color ) ? esc_html( $assignment_grade_button_bg_color ) : ''; ?>">
							</dd>
							<div class="setting-description"><?php _e( 'Change assignment grade button background color', 'learndash-groups-plus' ); ?></div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label
								for="in_progress"><b><?php _e( 'Assignment Grade Button Text Color:', 'learndash-groups-plus' ); ?></b></label>
							<dd class="ld-design-input">
								<input autocomplete="off" class="" id="assignment_grade_button_text_color" name="assignment_grade_button_text_color" style=""
								type="text" value="<?php echo isset( $assignment_grade_button_text_color ) ? esc_html( $assignment_grade_button_text_color ) : ''; ?>">
							</dd>
							<div class="setting-description"><?php _e( 'Change assignment grade button text color', 'learndash-groups-plus' ); ?></div>
						</td>
					</tr>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label
								for="in_progress"><b><?php _e( 'Assignment Resubmit Button Background Color:', 'learndash-groups-plus' ); ?></b></label>
							<dd class="ld-design-input">
								<input autocomplete="off" class="" id="assignment_resubmit_button_bg_color" name="assignment_resubmit_button_bg_color" style=""
								type="text" value="<?php echo isset( $assignment_resubmit_button_bg_color ) ? esc_html( $assignment_resubmit_button_bg_color ) : ''; ?>">
							</dd>
							<div class="setting-description"><?php _e( 'Change assignment resubmit button background color', 'learndash-groups-plus' ); ?></div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label
								for="in_progress"><b><?php _e( 'Assignment Resubmit Button Text Color:', 'learndash-groups-plus' ); ?></b></label>
							<dd class="ld-design-input">
								<input autocomplete="off" class="" id="assignment_resubmit_button_text_color" name="assignment_resubmit_button_text_color" style=""
								type="text" value="<?php echo isset( $assignment_resubmit_button_text_color ) ? esc_html( $assignment_resubmit_button_text_color ) : ''; ?>">
							</dd>
							<div class="setting-description"><?php _e( 'Change assignment resubmit button text color', 'learndash-groups-plus' ); ?></div>
						</td>
					</tr>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label
								for="in_progress"><b><?php _e( 'Assignment Approved Button Background Color:', 'learndash-groups-plus' ); ?></b></label>
							<dd class="ld-design-input">
								<input autocomplete="off" class="" id="assignment_approved_button_bg_color" name="assignment_approved_button_bg_color" style=""
								type="text" value="<?php echo isset( $assignment_approved_button_bg_color ) ? esc_html( $assignment_approved_button_bg_color ) : ''; ?>">
							</dd>
							<div class="setting-description"><?php _e( 'Change assignment approved button background color', 'learndash-groups-plus' ); ?></div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label
								for="in_progress"><b><?php _e( 'Assignment Approved Button Text Color:', 'learndash-groups-plus' ); ?></b></label>
							<dd class="ld-design-input">
								<input autocomplete="off" class="" id="assignment_approved_button_text_color" name="assignment_approved_button_text_color" style=""
								type="text" value="<?php echo isset( $assignment_approved_button_text_color ) ? esc_html( $assignment_approved_button_text_color ) : ''; ?>">
							</dd>
							<div class="setting-description"><?php _e( 'Change assignment approved button text color', 'learndash-groups-plus' ); ?></div>
						</td>
					</tr>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label
								for="in_progress"><b><?php _e( 'Assignment Buttons Border Radius:', 'learndash-groups-plus' ); ?></b></label>
							<dd class="ld-design-input">
								<input autocomplete="off" class="" id="assignment_buttons_border_radius" name="assignment_buttons_border_radius" style=""
								type="text" value="<?php echo isset( $assignment_buttons_border_radius ) ? esc_html( $assignment_buttons_border_radius ) : ''; ?>">
							</dd>
							<div class="setting-description"><?php _e( 'Change assignment buttons border radius', 'learndash-groups-plus' ); ?></div>
						</td>
					</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			<table class="ld-form-table design">
				<tbody>
					<tr valign="top">
						<td colspan="3">
							<h3 class="ld-settings-table-header"><?php _e( 'Global Menu Order', 'learndash-groups-plus' ); ?></h3>
						</td>
					</tr>
					<tr valign="top">
						<td class="ld-design-block forminp forminp-text">
							<label
								for="completed"><b><?php _e( 'Default order of Groups:', 'learndash-groups-plus' ); ?></b></label>
							<dd class="ld-design-input">
								<select name="group_orderby" class="learndash-groups-plus-admin-select" id="group_orderby">
									<option value="title" <?php echo ( $group_orderby === 'title' ? 'selected' : '' ); ?> ><?php _e( 'Title', 'learndash-groups-plus' ); ?></option>
									<option value="ID" <?php echo ( $group_orderby === 'ID' ? 'selected' : '' ); ?> >ID</option>
									<option value="menu_order" <?php echo ( $group_orderby === 'menu_order' ? 'selected' : '' ); ?> ><?php _e( 'Menu Order', 'learndash-groups-plus' ); ?></option>
								</select>

								<select name="group_order" class="learndash-groups-plus-admin-select" id="group_order">
									<option value="ASC" <?php echo ( $group_order === 'ASC' ? 'selected' : '' ); ?> >ASC</option>
									<option value="DESC" <?php echo ( $group_order === 'DESC' ? 'selected' : '' ); ?> >DESC</option>
								</select>
							</dd>
							<div class="setting-description"><?php _e( 'Change for All the group menus', 'learndash-groups-plus' ); ?></div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label
								for="completed"><b><?php _e( 'Default order of Courses:', 'learndash-groups-plus' ); ?></b></label>
							<dd class="ld-design-input">
								<select name="course_orderby" class="learndash-groups-plus-admin-select" id="course_orderby">
									<option value="title" <?php echo ( $course_orderby === 'title' ? 'selected' : '' ); ?> ><?php _e( 'Title', 'learndash-groups-plus' ); ?></option>
									<option value="ID" <?php echo ( $course_orderby === 'ID' ? 'selected' : '' ); ?> >ID</option>
									<option value="menu_order" <?php echo ( $course_orderby === 'menu_order' ? 'selected' : '' ); ?> ><?php _e( 'Menu Order', 'learndash-groups-plus' ); ?></option>
								</select>

								<select name="course_order" class="learndash-groups-plus-admin-select" id="course_order">
									<option value="ASC" <?php echo ( $course_order === 'ASC' ? 'selected' : '' ); ?> >ASC</option>
									<option value="DESC" <?php echo ( $course_order === 'DESC' ? 'selected' : '' ); ?> >DESC</option>
								</select>
							</dd>
							<div class="setting-description"><?php _e( 'Change for All the course menus', 'learndash-groups-plus' ); ?></div>
						</td>
						<td class="ld-design-block forminp forminp-text">
							<label
								for="completed"><b><?php _e( 'Default Order of Users:', 'learndash-groups-plus' ); ?></b></label>
							<dd class="ld-design-input">
								<select name="user_orderby" class="learndash-groups-plus-admin-select" id="user_orderby">
									<option value="display_name" <?php echo ( $user_orderby === 'display_name' ? 'selected' : '' ); ?> ><?php _e( 'Display Name', 'learndash-groups-plus' ); ?></option>
									<option value="ID" <?php echo ( $user_orderby === 'ID' ? 'selected' : '' ); ?> >ID</option>
								</select>

								<select name="user_order" class="learndash-groups-plus-admin-select" id="user_order">
									<option value="ASC" <?php echo ( $user_order === 'ASC' ? 'selected' : '' ); ?> >ASC</option>
									<option value="DESC" <?php echo ( $user_order === 'DESC' ? 'selected' : '' ); ?> >DESC</option>
								</select>
							</dd>
							<div class="setting-description"><?php _e( 'Change for All the user menus', 'learndash-groups-plus' ); ?></div>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
			<div style="width:100%;">
				<input class="button-primary" name="save_design" type="submit"
					value="<?php esc_html_e( 'Save', 'learndash-groups-plus' ); ?>" />
				<div>
			</p>
		</form>
	</div>
	<script>
		jQuery(function($){
			// on upload button click
			$('body').on( 'click', '.learndash-groups-plus-uploader', function(e){

				e.preventDefault();

				var button = $(this),
				custom_uploader = wp.media({
					title: 'Insert image',
					library : {
						// uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
						type : 'image'
					},
					button: {
						text: 'Use this image' // button label text
					},
					multiple: false
				}).on('select', function() { // it also has "open" and "close" events
					var attachment = custom_uploader.state().get('selection').first().toJSON();
					// button.html('<img src="' + attachment.url + '">').next().show().next().val(attachment.id);
					button.next().val(attachment.id);
				}).open();

			});

			// on remove button click
			$('body').on('click', '.learndash-groups-plus-uploader-remove-image', function(e){

				e.preventDefault();

				var button = $(this);
				button.next().val(''); // emptying the hidden field
				//button.hide().prev().html('Upload image');
			});
		});
	</script>
		<?php
	}

	public static function design_setting_save_field() {
		if ( isset( $_POST['save_design'] ) && ! empty( $_POST['save_design'] ) ) {
			if ( ! current_user_can( LEARNDASH_ADMIN_CAPABILITY_CHECK ) ) {
				return;
			}

			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'learndash_groups_plus_setting_form' ) ) {
				return;
			}

			unset( $_POST['save_design'] );

			$design_setting = isset( $_POST ) ? (array) $_POST : array();

			if ( empty( $design_setting['color_codes']['not_start'] ) ) {
				$design_setting['color_codes']['not_start'] = '#FF0000';
			}
			if ( empty( $design_setting['color_codes']['in_progress'] ) ) {
				$design_setting['color_codes']['in_progress'] = '#FFA500';
			}
			if ( empty( $design_setting['color_codes']['completed'] ) ) {
				$design_setting['color_codes']['completed'] = '#008000';
			}
			update_site_option( 'design_setting', $design_setting );

			if ( isset( $_POST['hide_exclude_from_publicly_sold_seats_checkbox'] ) && $_POST['hide_exclude_from_publicly_sold_seats_checkbox'] == 'yes' ) {
				update_site_option( 'hide_exclude_from_publicly_sold_seats_checkbox', 'yes' );
			} else {
				update_site_option( 'hide_exclude_from_publicly_sold_seats_checkbox', 'no' );
			}

			if ( isset( $_POST['hide_groups_plus_header'] ) && $_POST['hide_groups_plus_header'] == 'yes' ) {
				update_site_option( 'hide_groups_plus_header', 'yes' );
			} else {
				update_site_option( 'hide_groups_plus_header', 'no' );
			}

			if ( isset( $_POST['change_the_used_seats_icon_color'] ) && ! empty( $_POST['change_the_used_seats_icon_color'] ) ) {
				update_site_option( 'change_the_used_seats_icon_color', stripslashes( $_POST['change_the_used_seats_icon_color'] ) );
			} else {
				delete_site_option( 'change_the_used_seats_icon_color' );
			}

			// Assignments setting save
			if ( isset( $_POST['assignment_grade_button_bg_color'] ) && ! empty( $_POST['assignment_grade_button_bg_color'] ) ) {
				update_site_option( 'assignment_grade_button_bg_color', stripslashes( $_POST['assignment_grade_button_bg_color'] ) );
			} else {
				delete_site_option( 'assignment_grade_button_bg_color' );
			}

			if ( isset( $_POST['assignment_grade_button_text_color'] ) && ! empty( $_POST['assignment_grade_button_text_color'] ) ) {
				update_site_option( 'assignment_grade_button_text_color', stripslashes( $_POST['assignment_grade_button_text_color'] ) );
			} else {
				delete_site_option( 'assignment_grade_button_text_color' );
			}

			if ( isset( $_POST['assignment_resubmit_button_bg_color'] ) && ! empty( $_POST['assignment_resubmit_button_bg_color'] ) ) {
				update_site_option( 'assignment_resubmit_button_bg_color', stripslashes( $_POST['assignment_resubmit_button_bg_color'] ) );
			} else {
				delete_site_option( 'assignment_resubmit_button_bg_color' );
			}

			if ( isset( $_POST['assignment_resubmit_button_text_color'] ) && ! empty( $_POST['assignment_resubmit_button_text_color'] ) ) {
				update_site_option( 'assignment_resubmit_button_text_color', stripslashes( $_POST['assignment_resubmit_button_text_color'] ) );
			} else {
				delete_site_option( 'assignment_resubmit_button_text_color' );
			}

			if ( isset( $_POST['assignment_approved_button_bg_color'] ) && ! empty( $_POST['assignment_approved_button_bg_color'] ) ) {
				update_site_option( 'assignment_approved_button_bg_color', stripslashes( $_POST['assignment_approved_button_bg_color'] ) );
			} else {
				delete_site_option( 'assignment_approved_button_bg_color' );
			}

			if ( isset( $_POST['assignment_approved_button_text_color'] ) && ! empty( $_POST['assignment_approved_button_text_color'] ) ) {
				update_site_option( 'assignment_approved_button_text_color', stripslashes( $_POST['assignment_approved_button_text_color'] ) );
			} else {
				delete_site_option( 'assignment_approved_button_text_color' );
			}

			if ( isset( $_POST['assignment_buttons_border_radius'] ) && ! empty( $_POST['assignment_buttons_border_radius'] ) ) {
				update_site_option( 'assignment_buttons_border_radius', stripslashes( $_POST['assignment_buttons_border_radius'] ) );
			} else {
				delete_site_option( 'assignment_buttons_border_radius' );
			}

			if ( isset( $_POST['change_the_used_seats_attachment_id'] ) && ! empty( $_POST['change_the_used_seats_attachment_id'] ) ) {
				update_site_option( 'change_the_used_seats_attachment_id', $_POST['change_the_used_seats_attachment_id'] );
			} else {
				delete_site_option( 'change_the_used_seats_attachment_id' );
			}

			if ( isset( $_POST['hide_seats_used_text'] ) && $_POST['hide_seats_used_text'] == 'yes' ) {
				update_site_option( 'hide_seats_used_text', 'yes' );
			} else {
				update_site_option( 'hide_seats_used_text', 'no' );
			}

			if ( isset( $_POST['change_seats_remaining_icon_color'] ) && ! empty( $_POST['change_seats_remaining_icon_color'] ) ) {
				update_site_option( 'change_seats_remaining_icon_color', stripslashes( $_POST['change_seats_remaining_icon_color'] ) );
			} else {
				delete_site_option( 'change_seats_remaining_icon_color' );
			}

			if ( isset( $_POST['change_seats_remaining_attachment_id'] ) && ! empty( $_POST['change_seats_remaining_attachment_id'] ) ) {
				update_site_option( 'change_seats_remaining_attachment_id', stripslashes( $_POST['change_seats_remaining_attachment_id'] ) );
			} else {
				delete_site_option( 'change_seats_remaining_attachment_id' );
			}

			if ( isset( $_POST['hide_seats_ramaining_text'] ) && $_POST['hide_seats_ramaining_text'] == 'yes' ) {
				update_site_option( 'hide_seats_ramaining_text', 'yes' );
			} else {
				update_site_option( 'hide_seats_ramaining_text', 'no' );
			}

			if ( isset( $_POST['header_background_color'] ) && ! empty( $_POST['header_background_color'] ) ) {
				update_site_option( 'header_background_color', $_POST['header_background_color'] );
			} else {
				update_site_option( 'header_background_color', '#EAEAEA' );
			}

			if ( isset( $_POST['header_border_color'] ) && ! empty( $_POST['header_border_color'] ) ) {
				update_site_option( 'header_border_color', $_POST['header_border_color'] );
			} else {
				update_site_option( 'header_border_color', '#231F20' );
			}

			if ( isset( $_POST['hide_organization_email_button'] ) && $_POST['hide_organization_email_button'] == 'yes' ) {
				update_site_option( 'hide_organization_email_button', 'yes' );
			} else {
				update_site_option( 'hide_organization_email_button', 'no' );
			}

			if ( isset( $_POST['hide_organization_broadcast_email_tab'] ) && $_POST['hide_organization_broadcast_email_tab'] == 'yes' ) {
				update_site_option( 'hide_organization_broadcast_email_tab', 'yes' );
			} else {
				update_site_option( 'hide_organization_broadcast_email_tab', 'no' );
			}

			if ( isset( $_POST['hide_organization_welcome_email_tab'] ) && $_POST['hide_organization_welcome_email_tab'] == 'yes' ) {
				update_site_option( 'hide_organization_welcome_email_tab', 'yes' );
			} else {
				update_site_option( 'hide_organization_welcome_email_tab', 'no' );
			}

			if ( isset( $_POST['hide_manage_organization_button'] ) && $_POST['hide_manage_organization_button'] == 'yes' ) {
				update_site_option( 'hide_manage_organization_button', 'yes' );
			} else {
				update_site_option( 'hide_manage_organization_button', 'no' );
			}

			if ( isset( $_POST['hide_organization_manage_groups_plus_tab'] ) && $_POST['hide_organization_manage_groups_plus_tab'] == 'yes' ) {
				update_site_option( 'hide_organization_manage_groups_plus_tab', 'yes' );
			} else {
				update_site_option( 'hide_organization_manage_groups_plus_tab', 'no' );
			}

			if ( isset( $_POST['hide_organization_add_team_tab'] ) && $_POST['hide_organization_add_team_tab'] == 'yes' ) {
				update_site_option( 'hide_organization_add_team_tab', 'yes' );
			} else {
				update_site_option( 'hide_organization_add_team_tab', 'no' );
			}

			if ( isset( $_POST['hide_organization_delete_groups_plus_icon'] ) && $_POST['hide_organization_delete_groups_plus_icon'] == 'yes' ) {
				update_site_option( 'hide_organization_delete_groups_plus_icon', 'yes' );
			} else {
				update_site_option( 'hide_organization_delete_groups_plus_icon', 'no' );
			}

			if ( isset( $_POST['hide_organization_export_csv_button'] ) && $_POST['hide_organization_export_csv_button'] == 'yes' ) {
				update_site_option( 'hide_organization_export_csv_button', 'yes' );
			} else {
				update_site_option( 'hide_organization_export_csv_button', 'no' );
			}

			if ( isset( $_POST['hide_organization_manage_team_leaders_tab'] ) && $_POST['hide_organization_manage_team_leaders_tab'] == 'yes' ) {
				update_site_option( 'hide_organization_manage_team_leaders_tab', 'yes' );
			} else {
				update_site_option( 'hide_organization_manage_team_leaders_tab', 'no' );
			}

			if ( isset( $_POST['hide_organization_add_team_leaders_tab'] ) && $_POST['hide_organization_add_team_leaders_tab'] == 'yes' ) {
				update_site_option( 'hide_organization_add_team_leaders_tab', 'yes' );
			} else {
				update_site_option( 'hide_organization_add_team_leaders_tab', 'no' );
			}

			if ( isset( $_POST['hide_organization_add_team_leader_as_team_member_tab'] ) && $_POST['hide_organization_add_team_leader_as_team_member_tab'] == 'yes' ) {
				update_site_option( 'hide_organization_add_team_leader_as_team_member_tab', 'yes' );
			} else {
				update_site_option( 'hide_organization_add_team_leader_as_team_member_tab', 'no' );
			}

			if ( isset( $_POST['hide_organization_delete_team_leader_trashcan_icon'] ) && $_POST['hide_organization_delete_team_leader_trashcan_icon'] == 'yes' ) {
				update_site_option( 'hide_organization_delete_team_leader_trashcan_icon', 'yes' );
			} else {
				update_site_option( 'hide_organization_delete_team_leader_trashcan_icon', 'no' );
			}

			if ( isset( $_POST['hide_organization_delete_team_leader_person_x_icon'] ) && $_POST['hide_organization_delete_team_leader_person_x_icon'] == 'yes' ) {
				update_site_option( 'hide_organization_delete_team_leader_person_x_icon', 'yes' );
			} else {
				update_site_option( 'hide_organization_delete_team_leader_person_x_icon', 'no' );
			}

			if ( isset( $_POST['hide_organization_manage_team_leaders_button'] ) && $_POST['hide_organization_manage_team_leaders_button'] == 'yes' ) {
				update_site_option( 'hide_organization_manage_team_leaders_button', 'yes' );
			} else {
				update_site_option( 'hide_organization_manage_team_leaders_button', 'no' );
			}

			if ( isset( $_POST['hide_organization_manage_course_button'] ) && $_POST['hide_organization_manage_course_button'] == 'yes' ) {
				update_site_option( 'hide_organization_manage_course_button', 'yes' );
			} else {
				update_site_option( 'hide_organization_manage_course_button', 'no' );
			}

			if ( isset( $_POST['hide_groups_plus_add_team_member_button'] ) && $_POST['hide_groups_plus_add_team_member_button'] == 'yes' ) {
				update_site_option( 'hide_groups_plus_add_team_member_button', 'yes' );
			} else {
				update_site_option( 'hide_groups_plus_add_team_member_button', 'no' );
			}

			if ( isset( $_POST['hide_groups_plus_import_list_button'] ) && $_POST['hide_groups_plus_import_list_button'] == 'yes' ) {
				update_site_option( 'hide_groups_plus_import_list_button', 'yes' );
			} else {
				update_site_option( 'hide_groups_plus_import_list_button', 'no' );
			}

			if ( isset( $_POST['hide_groups_plus_email_groups_plus_button'] ) && $_POST['hide_groups_plus_email_groups_plus_button'] == 'yes' ) {
				update_site_option( 'hide_groups_plus_email_groups_plus_button', 'yes' );
			} else {
				update_site_option( 'hide_groups_plus_email_groups_plus_button', 'no' );
			}

			if ( isset( $_POST['hide_groups_plus_broadcast_email_tab'] ) && $_POST['hide_groups_plus_broadcast_email_tab'] == 'yes' ) {
				update_site_option( 'hide_groups_plus_broadcast_email_tab', 'yes' );
			} else {
				update_site_option( 'hide_groups_plus_broadcast_email_tab', 'no' );
			}

			if ( isset( $_POST['hide_groups_plus_team_member_welcome_email_tab'] ) && $_POST['hide_groups_plus_team_member_welcome_email_tab'] == 'yes' ) {
				update_site_option( 'hide_groups_plus_team_member_welcome_email_tab', 'yes' );
			} else {
				update_site_option( 'hide_groups_plus_team_member_welcome_email_tab', 'no' );
			}

			if ( isset( $_POST['hide_groups_plus_edit_team_member_button'] ) && $_POST['hide_groups_plus_edit_team_member_button'] == 'yes' ) {
				update_site_option( 'hide_groups_plus_edit_team_member_button', 'yes' );
			} else {
				update_site_option( 'hide_groups_plus_edit_team_member_button', 'no' );
			}

			if ( isset( $_POST['hide_change_password_team_member_button'] ) && $_POST['hide_change_password_team_member_button'] == 'yes' ) {
				update_site_option( 'hide_change_password_team_member_button', 'yes' );
			} else {
				update_site_option( 'hide_change_password_team_member_button', 'no' );
			}

			if ( isset( $_POST['hide_groups_plus_edit_team_member_remove_icon_button'] ) && $_POST['hide_groups_plus_edit_team_member_remove_icon_button'] == 'yes' ) {
				update_site_option( 'hide_groups_plus_edit_team_member_remove_icon_button', 'yes' );
			} else {
				update_site_option( 'hide_groups_plus_edit_team_member_remove_icon_button', 'no' );
			}

			if ( isset( $_POST['hide_groups_plus_edit_team_member_delete_icon_button'] ) && $_POST['hide_groups_plus_edit_team_member_delete_icon_button'] == 'yes' ) {
				update_site_option( 'hide_groups_plus_edit_team_member_delete_icon_button', 'yes' );
			} else {
				update_site_option( 'hide_groups_plus_edit_team_member_delete_icon_button', 'no' );
			}

			if ( isset( $_POST['hide_groups_plus_export_csv_button'] ) && $_POST['hide_groups_plus_export_csv_button'] == 'yes' ) {
				update_site_option( 'hide_groups_plus_export_csv_button', 'yes' );
			} else {
				update_site_option( 'hide_groups_plus_export_csv_button', 'no' );
			}

			if ( isset( $_POST['hide_groups_plus_lock_column_in_detail_team_member_report'] ) && $_POST['hide_groups_plus_lock_column_in_detail_team_member_report'] == 'yes' ) {
				update_site_option( 'hide_groups_plus_lock_column_in_detail_team_member_report', 'yes' );
			} else {
				update_site_option( 'hide_groups_plus_lock_column_in_detail_team_member_report', 'no' );
			}

			// Order by setting save
			if ( isset( $_POST['group_orderby'] ) ) {
				update_site_option( 'group_orderby', $_POST['group_orderby'] );
			} else {
				update_site_option( 'group_orderby', 'name' );
			}
			if ( isset( $_POST['group_order'] ) ) {
				update_site_option( 'group_order', $_POST['group_order'] );
			} else {
				update_site_option( 'group_order', 'ASC' );
			}

			if ( isset( $_POST['course_orderby'] ) ) {
				update_site_option( 'course_orderby', $_POST['course_orderby'] );
			} else {
				update_site_option( 'course_orderby', 'name' );
			}
			if ( isset( $_POST['course_order'] ) ) {
				update_site_option( 'course_order', $_POST['course_order'] );
			} else {
				update_site_option( 'course_order', 'ASC' );
			}

			if ( isset( $_POST['user_orderby'] ) ) {
				update_site_option( 'user_orderby', $_POST['user_orderby'] );
			} else {
				update_site_option( 'user_orderby', 'display_name' );
			}
			if ( isset( $_POST['user_order'] ) ) {
				update_site_option( 'user_order', $_POST['user_order'] );
			} else {
				update_site_option( 'user_order', 'ASC' );
			}
		}
	}

	public static function design_setting() {
	}

	public static function media_lib_uploader_enqueue() {
		// I recommend to add additional conditions just to not to load the scripts on each page.
		if ( ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}
	}
}
