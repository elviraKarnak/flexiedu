<?php
/**
 * Fired during plugin activation
 *
 * @since 1.0.0
 *
 * @package Ld_Group_Registration
 * @subpackage Ld_Group_Registration/includes
 */

namespace LdGroupRegistration\Includes;

defined( 'ABSPATH' ) || exit;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since 1.0.0
 * @package Ld_Group_Registration
 * @subpackage Ld_Group_Registration/includes
 */
class Ld_Group_Registration_Activator {
	/**
	 * Activation Sequence
	 *
	 * Perform necessary actions such as creating Group Dashboard page, and saving meta for the same.
	 *
	 * @since 4.0
	 */
	public function activate() {
		global $wdm_grp_plugin_data;

		$ldgr_group_users_page = get_option( 'wdm_group_users_page' );

		if ( '' == $ldgr_group_users_page ) {
			$course_create_page = [
				'post_title'   => __( 'Groups Dashboard', 'wdm_ld_group' ),
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '[wdm_group_users]',
				'post_author'  => get_current_user_id(),
			];

			$group_users_page_id = wp_insert_post( $course_create_page );
			update_option( 'wdm_group_users_page', $group_users_page_id );
		}
	}

	/**
	 * This function is used to migrate old data in new format for 4.3.0 update.
	 *
	 * @since 4.3.0
	 */
	public function migrate_old_data() {
		$is_migrated = get_option( 'ldgr_usermeta_migrated', false );
		if ( $is_migrated ) {
			return;
		}

		global $wpdb;
		$table = $wpdb->prefix . 'usermeta';

		$old_meta = $wpdb->get_results(
			"SELECT * FROM {$table} WHERE meta_key LIKE 'wdm_group_product_%'"
		);

		foreach ( $old_meta as $key => $value ) {
			// $group_id = $value->meta_key;
			$group_id = trim( str_replace( 'wdm_group_product_', '', $value->meta_key ) );

			$meta_key = 'ldgr_group_product_' . $value->meta_value;

			add_user_meta( $value->user_id, $meta_key, [ $group_id ] );
		}

		$groups_query_args = [
			'post_type'   => 'groups',
			'nopaging'    => true,
			'post_status' => [ 'publish', 'pending', 'draft', 'future', 'private' ],
			'fields'      => 'ids',
		];

		$groups_query = new \WP_Query( $groups_query_args );

		foreach ( $groups_query->posts as $group_id ) {
			// migrate total users count of group
			$limit          = (int) get_post_meta( $group_id, 'wdm_group_users_limit_' . $group_id, true );
			$enrolled_users = (int) count( learndash_get_groups_user_ids( $group_id ) );

			$limit += $enrolled_users;
			update_post_meta( $group_id, 'wdm_group_total_users_limit_' . $group_id, $limit );
		}

		update_option( 'ldgr_usermeta_migrated', true );
	}

	/**
	 * Admin Activation Sequence
	 *
	 * Check for plugin dependencies on plugin activation.
	 *
	 * @since 4.0
	 */
	public function admin_activate() {
		if ( ! class_exists( 'SFWD_LMS' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			unset( $_GET['activate'] );
			add_action( 'admin_notices', [ $this, 'handle_admin_notices' ] );
		} else {
			// Update group leader privileges.
			$role = get_role( 'group_leader' );

			// Check if upload file privileges already provided.
			if ( null !== $role && ! $role->has_cap( 'upload_files' ) ) {
				$role->add_cap( 'upload_files' );
			}
		}
	}

	/**
	 * Handle admin notices
	 */
	public function handle_admin_notices() {
		if ( ! class_exists( 'SFWD_LMS' ) ) {
			?>
		<div class='error'><p>
				<?php
				echo esc_html( __( "LearnDash LMS plugin is not active. In order to make the 'LearnDash Group Registration' plugin work, you need to install and activate LearnDash LMS first.", 'wdm_ld_group' ) );
				?>
			</p></div>

			<?php

		}
	}

	/**
	 * Handle upgrade notices if any
	 *
	 * @param array $data
	 * @param array $response
	 *
	 * @since 4.1.0
	 */
	public function handle_update_notices( $data, $response ) {
		if ( isset( $data['upgrade_notice'] ) ) {
			printf(
				'<div class="update-message">%s</div>',
				wpautop( $data['upgrade_notice'] )
			);
		}
	}
}
