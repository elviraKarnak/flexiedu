<?php
/**
 * Team Member list while viewing a Team.
 *
 * @since 1.0.0
 * @version 2.1.2
 *
 * @package LearnDash\Groups_Plus
 */

use LearnDash\Groups_Plus\Module\Group;

?>

<table class="groups_user_table groups_plus_table team_member_list_table"
	data-nonce="<?php echo wp_create_nonce( 'learndash-groups-plus-team-member-list-table' ); ?>">
	<thead>
		<tr>
			<th>
				<?php printf(
					esc_html__( '%s Name', 'learndash-groups-plus' ),
					learndash_get_custom_label( 'team_member' )
				); ?>
			</th>
			<th>
				<?php esc_html_e( 'Username', 'learndash-groups-plus' ); ?>
			</th>
			<th>
				<span class="email_icon"></span>
			</th>
			<th>
				<?php printf(
					esc_html__( 'Edit %s Information', 'learndash-groups-plus' ),
					learndash_get_custom_label( 'team_member' )
				); ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if ( empty( $user_group_users ) ) {
			?>
		<tr>
			<td colspan="4">
				<?php _e( 'No record found.', 'learndash-groups-plus' ); ?>
			</td>
		</tr>
			<?php
		}

		foreach ( $user_group_users as $user ) {
			$user_key = user_encrypt_decrypt( 'encrypt', $user->ID );
			?>
		<tr class="<?php esc_attr_e( $user->style_classes, 'learndash-groups-plus' ); ?>">
			<td><?php echo $user->first_name . ' ' . $user->last_name; ?></td>
			<td align="center"><span class="groups_plus_info"><?php echo $user->user_login; ?></span></td>
			<td>
			<?php
			if ( ! empty( $user->user_email ) ) {
				?>
 <a href="mailto:<?php esc_attr_e( $user->user_email, 'learndash-groups-plus' ); ?>"><span
						class="email_icon"></span></a><?php }; ?></td>
			<td align="center" class="learndash-groups-plus-manage-team-edit-team-member">
				<?php do_action( 'before_team_member_action_button' ); ?>
				<?php
					$hide_groups_plus_edit_team_member_button = get_site_option( 'hide_groups_plus_edit_team_member_button', 'no' );

				if ( Group::can_edit_user( $user->ID, get_current_user_id() ) ) :

					if ( $hide_groups_plus_edit_team_member_button === 'no' ) :
						?>

						<a href="#" data-user="<?php echo $user_key; ?>" data-firstname="<?php echo $user->first_name; ?>"
							data-lastname="<?php echo $user->last_name; ?>" data-username="<?php echo $user->user_login; ?>"
							data-email="<?php echo $user->user_email; ?>"
							class="btn_groups_plus btn_groups_plus_black edit_groups_plus_team_member">
							<?php esc_html_e( 'Edit', 'learndash-groups-plus' ); ?>
							<i class="fa fa-angle-right"></i></a>

					<?php endif; ?>

					<?php if ( get_site_option( 'hide_change_password_team_member_button', 'no' ) === 'no' ) : ?>

						<a href="#" data-user="<?php echo $user_key; ?>" class="btn_groups_plus groups-plus-change-password">
							<?php esc_html_e( 'Change Password', 'learndash-groups-plus' ); ?> <i
								class="fa fa-angle-right"></i>
						</a>

					<?php endif; ?>

					<?php
					do_action( 'after_team_member_action_button' );

				endif;

				?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
	<?php if ( get_site_option( 'hide_groups_plus_export_csv_button', 'no' ) === 'no' ) : ?>
		<tfoot>
			<tr>
				<td colspan="4">
					<a href="" class="btn_groups_plus"
						id="btn_download_team_member_report_csv"><?php esc_html_e( 'Export CSV', 'learndash-groups-plus' ); ?>
						<i class="fa fa-angle-right"></i></a>
				</td>
			</tr>
		</tfoot>
	<?php endif; ?>
</table>
