<?php
/**
 * Migration module class file.
 *
 * Migration class for legacy LearnDash classroom addon migration to the new LearnDash Groups Plus.
 *
 * @since 1.1.0
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore migratable ramaining classrom WPINC
 */

namespace LearnDash\Groups_Plus\Module;

if ( ! defined( 'WPINC' ) ) {
	die;
}

use LearnDash\Groups_Plus\lucatume\DI52\App;
use LearnDash\Groups_Plus\Module\Base as Module_Base;
use LearnDash\Groups_Plus\Module\Module_Interface;

/**
 * Migration module class.
 *
 * @since 1.1.0
 */
class Migration extends Module_Base implements Module_Interface {
	/**
	 * Setting keys map in legacy key => new key pair.
	 *
	 * @var array
	 */
	private $settings_keys_map = array(
		'ldc_allow_course_cert_publicly'                          => 'allow_course_cert_publicly',
		'ldc_allow_existing_users_to_be_updated'                  => 'allow_existing_users_to_be_updated',
		'ldc_allow_leader_to_see_all_users'                       => 'allow_leader_to_see_all_users',
		'ldc_allow_teachers_to_add_subscription_seats'            => 'allow_team_leaders_to_add_subscription_seats',
		'ldc_change_seats_remaining_icon_color'                   => 'change_seats_remaining_icon_color',
		'ldc_change_the_used_seats_icon_color'                    => 'change_the_used_seats_icon_color',
		'ldc_course_order'                                        => 'course_order',
		'ldc_course_orderby'                                      => 'course_orderby',
		'ldc_design_setting'                                      => 'design_setting',
		'ldc_email_tab'                                           => 'email_tab',
		'ldc_enable_add_seats'                                    => 'enable_add_seats',
		'ldc_enable_bb_classrooms'                                => 'enable_bb_groups_plus',
		'ldc_enable_bb_school'                                    => 'enable_bb_organization',
		'ldc_enable_classrom_message_board'                       => 'enable_classrom_message_board',
		'ldc_enable_wc'                                           => 'enable_wc',
		'ldc_group_order'                                         => 'group_order',
		'ldc_group_orderby'                                       => 'group_orderby',
		'ldc_header_background_color'                             => 'header_background_color',
		'ldc_header_border_color'                                 => 'header_border_color',
		'ldc_hide_change_password_student_button'                 => 'hide_change_password_team_member_button',
		'ldc_hide_classroom_add_student_button'                   => 'hide_groups_plus_add_team_member_button',
		'ldc_hide_classroom_broadcast_email_tab'                  => 'hide_groups_plus_broadcast_email_tab',
		'ldc_hide_classroom_edit_student_button'                  => 'hide_groups_plus_edit_team_member_button',
		'ldc_hide_classroom_edit_student_delete_icon_button'      => 'hide_groups_plus_edit_team_member_delete_icon_button',
		'ldc_hide_classroom_edit_student_remove_icon_button'      => 'hide_groups_plus_edit_team_member_remove_icon_button',
		'ldc_hide_classroom_email_classroom_button'               => 'hide_groups_plus_email_groups_plus_button',
		'ldc_hide_classroom_export_csv_button'                    => 'hide_groups_plus_export_csv_button',
		'ldc_hide_classroom_import_list_button'                   => 'hide_groups_plus_import_list_button',
		'ldc_hide_classroom_lock_column_in_detail_student_report' => 'hide_groups_plus_lock_column_in_detail_team_member_report',
		'ldc_hide_classroom_student_welcome_email_tab'            => 'hide_groups_plus_team_member_welcome_email_tab',
		'ldc_hide_classrooms_header'                              => 'hide_groups_plus_header',
		'ldc_hide_exclude_from_publicly_sold_seats_checkbox'      => 'hide_exclude_from_publicly_sold_seats_checkbox',
		'ldc_hide_manage_classroom_button'                        => 'hide_manage_organization_button',
		'ldc_hide_school_add_classroom_tab'                       => 'hide_organization_add_team_tab',
		'ldc_hide_school_add_teacher_as_student_tab'              => 'hide_organization_add_team_leader_as_team_member_tab',
		'ldc_hide_school_add_teachers_tab'                        => 'hide_organization_add_team_leaders_tab',
		'ldc_hide_school_broadcast_email_tab'                     => 'hide_organization_broadcast_email_tab',
		'ldc_hide_school_delete_classroom_icon'                   => 'hide_organization_delete_groups_plus_icon',
		'ldc_hide_school_delete_teacher_person_x_icon'            => 'hide_organization_delete_team_leader_person_x_icon',
		'ldc_hide_school_delete_teacher_trashcan_icon'            => 'hide_organization_delete_team_leader_trashcan_icon',
		'ldc_hide_school_email_button'                            => 'hide_organization_email_button',
		'ldc_hide_school_export_csv_button'                       => 'hide_organization_export_csv_button',
		'ldc_hide_school_manage_classroom_tab'                    => 'hide_organization_manage_groups_plus_tab',
		'ldc_hide_school_manage_course_button'                    => 'hide_organization_manage_course_button',
		'ldc_hide_school_manage_teachers_button'                  => 'hide_organization_manage_team_leaders_button',
		'ldc_hide_school_manage_teachers_tab'                     => 'hide_organization_manage_team_leaders_tab',
		'ldc_hide_school_welcome_email_tab'                       => 'hide_organization_welcome_email_tab',
		'ldc_hide_seats_ramaining_text'                           => 'hide_seats_ramaining_text',
		'ldc_hide_seats_used_text'                                => 'hide_seats_used_text',
		'ldc_lock_teacher_and_student_names'                      => 'lock_team_leader_and_team_member_names',
		'ldc_show_all_quiz_attempts'                              => 'show_all_quiz_attempts',
		'ldc_teacher_use_seat'                                    => 'team_leader_use_seat',
		'ldc_use_uncanny_seat'                                    => 'use_uncanny_seat',
		'ldc_user_order'                                          => 'user_order',
		'ldc_user_orderby'                                        => 'user_orderby',
		'ldc_change_the_used_seats_attachment_id'				  => 'change_the_used_seats_attachment_id',
		'ldc_change_seats_remaining_attachment_id'				  => 'change_seats_remaining_attachment_id',
	);

	/**
	 * Existing legacy settings.
	 *
	 * @var array
	 */
	private $legacy_settings = array();

	/**
	 * Total legacy settings count.
	 *
	 * @var int
	 */
	private $total_legacy_settings;

	/**
	 * Total processed items per batch.
	 *
	 * @var int
	 */
	private $total_items_per_batch = 10;

	/**
	 * Total processed batches.
	 *
	 * @var int
	 */
	private $total_batches;

	/**
	 * Is migration completed.
	 *
	 * @var bool
	 */
	private $migration_completed;

	/**
	 * Migration completed option key.
	 *
	 * @var string
	 */
	private $migration_completed_option_key = 'learndash_groups_plus_migration_completed';

	/**
	 * Legacy settings transient key to store legacy settings.
	 *
	 * @var string
	 */
	private $legacy_settings_transient_key = 'learndash_groups_plus_legacy_settings';

	/**
	 * Migration action page and AJAX slug.
	 *
	 * @var string
	 */
	private $action_slug = 'learndash-groups-plus-migration';

	/**
	 * AJAX action nonce key.
	 *
	 * @var string
	 */
	private $nonce_key = 'learndash_groups_plus_migration_nonce';

	/**
	 * Construct method.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		$this->migration_completed = get_option( $this->migration_completed_option_key, false );

		if ( ! $this->migration_completed ) {
			$this->legacy_settings       = $this->get_legacy_settings();
			$this->total_legacy_settings = count( $this->legacy_settings );
			$this->total_batches         = ceil( $this->total_legacy_settings / $this->total_items_per_batch );
		}
	}

	/**
	 * Action hooks.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	protected function hook_actions(): void {
		add_action( 'admin_head', App::callback( self::class, 'style' ) );
		add_action( 'admin_menu', App::callback( self::class, 'register_page' ) );
		add_action( 'admin_notices', App::callback( self::class, 'show_notice' ) );
	}

	/**
	 * AJAX hooks.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	protected function hook_ajax(): void {
		add_action( 'wp_ajax_' . $this->action_slug, App::callback( self::class, 'init' ) );
	}

	/**
	 * Register setting page.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function register_page(): void {
		add_submenu_page( 'learndash-lms', __( 'LearnDash Groups Plus Migration', 'learndash-groups-plus' ), null, LEARNDASH_ADMIN_CAPABILITY_CHECK, $this->action_slug, App::callback( self::class, 'output_migration_page' ), 30 );
	}

	/**
	 * Map legacy setting key to its new key counterpart.
	 *
	 * @since 1.1.0
	 *
	 * @param string $key Legacy setting key name.
	 * @return ?string
	 */
	private function map_setting_key( $key ): ?string {
		return $this->settings_keys_map[ $key ] ?? null;
	}

	/**
	 * Check if site has legacy settings.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	private function has_legacy_settings(): bool {
		return ! empty( $this->legacy_settings );
	}

	/**
	 * Get legacy settings.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	private function get_legacy_settings(): array {
		$settings = get_transient( $this->legacy_settings_transient_key );

		if ( $settings === false ) {
			global $wpdb;

			$settings = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}options WHERE option_name LIKE %s", 'ldc_%%' ) );

			if ( ! empty( $settings ) ) {
				set_transient( $this->legacy_settings_transient_key, $settings, HOUR_IN_SECONDS );
			} else {
				$this->complete();
			}
		}

		return $settings;
	}

	/**
	 * Check if the legacy settings can be migrated to new settings.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	private function is_migratable(): bool {
		return ! $this->migration_completed && $this->has_legacy_settings();
	}

	/**
	 * Output message notice to run migration.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function show_notice(): void {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! $this->is_migratable() || ( isset( $_GET['page'] ) && $_GET['page'] === $this->action_slug ) ) {
			return;
		}
		?>
		<div class="notice notice-warning">
			<p>
				<?php
				printf(
					// translators: HTML markup for link.
					esc_html__( 'We\'ve detected the site used LearnDash Classroom addon. %1$sClick here%2$s to start the migration process. Make sure to back up the site first because the process is irreversible.', 'learndash-groups-plus' ),
					'<a href="' . esc_url(
						add_query_arg(
							array(
								'nonce' => wp_create_nonce( $this->nonce_key ),
							),
							menu_page_url( $this->action_slug, false )
						)
					) . '">',
					'</a>'
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Output migration page.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function output_migration_page(): void {
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! current_user_can( LEARNDASH_ADMIN_CAPABILITY_CHECK ) || ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], $this->nonce_key ) ) {
			return;
		}

		?>
		<div class="wrap">
			<?php $this->script(); ?>
			<h1><?php esc_html_e( 'LearnDash Groups Plus Migration', 'learndash-groups-plus' ); ?></h1>
			<p><?php esc_html_e( 'Starting migration.', 'learndash-groups-plus' ); ?></p>
			<div class="updates">
				<p><?php esc_html_e( 'Please don\'t close this page until the process is finished.', 'learndash-groups-plus' ); ?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * Output style tag.
	 *
	 * @return void
	 */
	public function style(): void {
		?>
		<style>
			#adminmenu a[href="admin.php?page=learndash-groups-plus-migration"] {
				display: none;
			}
		</style>
		<?php
	}

	/**
	 * Output migration javascript.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function script(): void {
		?>
		<script type="text/javascript">
			(function() {
				jQuery( function( $ ) {
					const initMigration = function( batch = 1 ) {
						return $.ajax( {
							url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
							type: 'POST',
							data: {
								action: '<?php echo esc_attr( $this->action_slug ); ?>',
                                <?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized ?>
								nonce : '<?php echo esc_attr( $_GET['nonce'] ?? '' ); ?>',
								batch : batch,
							}
						} )
						.done( function( response ) {
							if ( response.success ) {
								$( '.updates' ).append( '<p>' + response.data.message + '</p>' );
								if ( ! response.data.completed ) {
									initMigration( response.data.batch + 1 );
								}
							}
						} );
					};

					initMigration();
				} );
			})();
		</script>
		<?php
	}

	/**
	 * Handle AJAX process.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function init(): void {
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], $this->nonce_key ) ) {
			wp_die( 'Cheatin\' huh?' );
		}

		if ( ! current_user_can( LEARNDASH_ADMIN_CAPABILITY_CHECK ) ) {
			wp_die( 'Cheatin\' huh?' );
		}

		$batch = isset( $_POST['batch'] ) ? intval( $_POST['batch'] ) : 1;

		$processed = $this->process( $batch );
		$completed = empty( $processed );
		// translators: current step of the total step.
		$message = $completed ? esc_html__( 'The migration has been completed. You can close this page.', 'learndash-groups-plus' ) : sprintf( esc_html__( 'Processed step #%1$d of %2$d steps.', 'learndash-groups-plus' ), $batch, $this->total_batches );

		if ( $completed ) {
			$this->complete();
		}

		wp_send_json_success(
			array(
				'completed' => $completed,
				'message'   => $message,
				'batch'     => $batch,
			)
		);
		wp_die();
	}

	/**
	 * Process migration batch.
	 *
	 * @since 1.1.0
	 *
	 * @param int $batch Current processed batch.
	 * @return array
	 */
	private function process( $batch = 1 ): array {
		$offset = ( $batch - 1 ) * $this->total_items_per_batch;

		$processed_settings = array_slice( $this->legacy_settings, $offset, $this->total_items_per_batch );

		foreach ( $processed_settings as $legacy_setting ) {
			$new_key = $this->map_setting_key( $legacy_setting->option_name );

			if ( $new_key ) {
				if ( in_array( $new_key, array( 'email_tab', 'design_setting' ) ) ) {
					$option_value = maybe_unserialize( $legacy_setting->option_value );

					if ( is_array( $option_value ) ) {
						$new_option = array();
						foreach ( $option_value as $key => $value ) {
							$key = str_replace(
								array(
									'classroom',
									'classrooms',
									'school',
									'teacher',
									'student',
								),
								array(
									'groups_plus',
									'groups_plus',
									'organization',
									'team_leader', 
									'team_member',
								),
								$key
							);

							$new_option[ $key ] = $value;
						}

						$legacy_setting->option_value = $new_option;
					}
				}

				$legacy_setting->option_value = maybe_unserialize( $legacy_setting->option_value );

				update_option( $new_key, $legacy_setting->option_value, $legacy_setting->autoload );

				delete_option( $legacy_setting->option_name );
			}
		}

		return $processed_settings;
	}

	/**
	 * Run migration complete process.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	private function complete(): void {
		update_option( $this->migration_completed_option_key, true, true );
	}
}
