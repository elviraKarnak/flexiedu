<?php
/**
 * Edit Team Member Modal.
 *
 * @since 1.0.0
 * @version 2.1.2
 *
 * @package LearnDash\Groups_Plus
 */

?>
<form action="" method="POST" id="<?php _e( self::$form_id, 'learndash-groups-plus' ); ?>"
	data-nonce="<?php echo wp_create_nonce( 'learndash-groups-plus-manage-team-edit-team-member' ); ?>">
	<div class="form_message"></div>
	<div class="form-group section">
		<?php $lock_team_leader_and_team_member_names = get_site_option( 'lock_team_leader_and_team_member_names' ); ?>
		<div class="col grid_1_of_4">
			<input type="hidden" name="user" value="<?php _e( $user_key, 'learndash-groups-plus' ); ?>" />
			<input type="hidden" name="group" value="<?php _e( $group_key, 'learndash-groups-plus' ); ?>" />
			<input type="text" name="firstname" value="<?php _e( $user_info->first_name, 'learndash-groups-plus' ); ?>"
				placeholder="<?php esc_html_e( 'First Name *', 'learndash-groups-plus' ); ?>"
				<?php echo $lock_team_leader_and_team_member_names == 'yes' ? 'readonly' : ''; ?> />
		</div>
		<div class="col grid_1_of_4">
			<input type="text" name="lastname" value="<?php _e( $user_info->last_name, 'learndash-groups-plus' ); ?>"
				placeholder="<?php esc_html_e( 'Last Name', 'learndash-groups-plus' ); ?>"
				<?php echo $lock_team_leader_and_team_member_names == 'yes' ? 'readonly' : ''; ?>/>
		</div>
		<div class="col grid_1_of_4">
			<input type="text" name="username" value="<?php _e( $user_info->user_login, 'learndash-groups-plus' ); ?>"
				placeholder="<?php esc_html_e( 'Username *', 'learndash-groups-plus' ); ?>" />
		</div>
		<div class="col grid_1_of_4">
			<input type="email" name="email" value="<?php _e( $user_info->user_email, 'learndash-groups-plus' ); ?>"
				placeholder="<?php esc_html_e( 'Email', 'learndash-groups-plus' ); ?>" />
		</div>
	</div>
	<div class="form-action section text-right action-buttons">
		<div class="col grid_1_of_4 pull-left text-left">
			<button type="submit" class="btn_groups_plus"
				id="<?php _e( self::$form_id, 'learndash-groups-plus' ); ?>_btn"><?php esc_html_e( 'Save', 'learndash-groups-plus' ); ?> <i
					class="fa fa-angle-right"></i></button>
		</div>
		<div class="col grid_3_of_4 pull-right text-right">
			<?php do_action( 'before_edit_team_member_button', $user_id, $group_id ); ?>

			<?php
				$hide_groups_plus_edit_team_member_remove_icon_button = get_site_option( 'hide_groups_plus_edit_team_member_remove_icon_button', 'no' );
			if ( $hide_groups_plus_edit_team_member_remove_icon_button === 'no' ) {
				?>

			<button type="button" class="btn_groups_plus" title="Remove team member from group plus"
				id="<?php _e( self::$form_id, 'learndash-groups-plus' ); ?>_btn_delete"><?php esc_html_e( 'Remove', 'learndash-groups-plus' ); ?>
				<i class="fas fa-trash"></i><span></button>
			<?php } ?>

			<?php if ( $is_primary_team_leader ) { ?>
				<?php
					$hide_groups_plus_edit_team_member_delete_icon_button = get_site_option( 'hide_groups_plus_edit_team_member_delete_icon_button', 'no' );
				if ( $hide_groups_plus_edit_team_member_delete_icon_button === 'no' ) {
					?>

			<button type="button" class="btn_groups_plus" title="Delete  from group plus permanently"
				id="<?php _e( self::$form_id, 'learndash-groups-plus' ); ?>_btn_delete_permanently"><?php esc_html_e( 'Delete', 'learndash-groups-plus' ); ?>
				<i class="fas fa-user-times"></i><span></button>
				<?php } ?>
			<?php } ?>
			<?php do_action( 'after_edit_team_member_button', $user_id, $group_id ); ?>
		</div>
	</div>
	<div class="form-action section text-right">
	</div>
</form>
