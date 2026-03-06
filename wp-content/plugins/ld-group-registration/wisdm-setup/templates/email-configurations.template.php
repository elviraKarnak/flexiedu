<?php
/**
 * Setup Wizard: Email Configurations View Template.
 *
 * @since 4.2.0
 * @version 4.3.15
 *
 * @var object  $wizard_handler                         Setup wizard handler object.
 * @var mixed   $ldgr_create_user_admin
 * @var mixed   $ldgr_remove_user_admin
 * @var mixed   $ldgr_remove_user_accept_group_leader
 * @var mixed   $ldgr_remove_user_reject_group_leader
 * @var mixed   $ldgr_user_created_member
 * @var mixed   $ldgr_user_added_member
 * @var mixed   $ldgr_reinvite_user_member
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;
?>
<form method="post" action="<?php echo esc_url( $wizard_handler->get_next_step_link() ); ?>">
	<table class="form-table">
		<tbody>
			<tr>
				<th colspan="2" class="ldgr-heading-cell">
					<label><?php esc_html_e( 'Administrator', 'wdm_ld_group' ); ?></label>
					<span class="description"><?php esc_html_e( 'Manage emails that are sent to the administrator', 'wdm_ld_group' ); ?></span>
				</th>
			</tr>
			<tr class="ldgr-admin-emails">
				<th scope="row">
					<label for="ldgr_create_user_admin">
						<?php esc_html_e( 'User Account Creation', 'wdm_ld_group' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="ldgr_create_user_admin" id="ldgr_create_user_admin" class="switch-input" <?php echo ( 'off' != $ldgr_create_user_admin ) ? 'checked' : ''; ?>>
					<label for="ldgr_create_user_admin" class="switch-label">
						<span class="toggle--on"><?php esc_html_e( 'On', 'wdm_ld_group' ); ?></span>
						<span class="toggle--off"><?php esc_html_e( 'Off', 'wdm_ld_group' ); ?></span>
					</label>
					<span class="description"><?php esc_html_e( 'This email is sent when a new user account is created.', 'wdm_ld_group' ); ?></span>
				</td>
			</tr>
			<tr class="ldgr-admin-emails">
				<th scope="row">
					<label for="ldgr_remove_user_admin">
						<?php esc_html_e( 'Remove User', 'wdm_ld_group' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="ldgr_remove_user_admin" id="ldgr_remove_user_admin" class="switch-input" <?php echo ( 'off' != $ldgr_remove_user_admin ) ? 'checked' : ''; ?>>
					<label for="ldgr_remove_user_admin" class="switch-label">
						<span class="toggle--on"><?php esc_html_e( 'On', 'wdm_ld_group' ); ?></span>
						<span class="toggle--off"><?php esc_html_e( 'Off', 'wdm_ld_group' ); ?></span>
					</label>
					<span class="description"><?php esc_html_e( 'This email is sent when group leaders request to remove a user.', 'wdm_ld_group' ); ?></span>
				</td>
			</tr>
			<tr>
				<th colspan="2" class="ldgr-heading-cell">
					<label>
						<?php esc_html_e( 'Group Leader', 'wdm_ld_group' ); ?>
					</label>
					<span class="description"><?php esc_html_e( 'Manage emails that are sent to the group leaders', 'wdm_ld_group' ); ?></span>
				</th>
			</tr>
			<tr class="ldgr-group-leader-emails">
				<th scope="row">
					<label for="ldgr_remove_user_accept_group_leader">
						<?php esc_html_e( 'Accept User Removal', 'wdm_ld_group' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="ldgr_remove_user_accept_group_leader" id="ldgr_remove_user_accept_group_leader" class="switch-input" <?php echo ( 'off' !== $ldgr_remove_user_accept_group_leader ) ? 'checked' : ''; ?>>
					<label for="ldgr_remove_user_accept_group_leader" class="switch-label">
						<span class="toggle--on"><?php esc_html_e( 'On', 'wdm_ld_group' ); ?></span>
						<span class="toggle--off"><?php esc_html_e( 'Off', 'wdm_ld_group' ); ?></span>
					</label>
					<span class="description"><?php esc_html_e( 'This email is sent when admin accepts a user removal request ', 'wdm_ld_group' ); ?></span>
				</td>
			</tr>
			<tr class="ldgr-group-leader-emails">
				<th scope="row">
					<label for="ldgr_remove_user_reject_group_leader">
						<?php esc_html_e( 'Reject User Removal', 'wdm_ld_group' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="ldgr_remove_user_reject_group_leader" id="ldgr_remove_user_reject_group_leader" class="switch-input" <?php echo ( 'off' !== $ldgr_remove_user_reject_group_leader ) ? 'checked' : ''; ?>>
					<label for="ldgr_remove_user_reject_group_leader" class="switch-label">
						<span class="toggle--on"><?php esc_html_e( 'On', 'wdm_ld_group' ); ?></span>
						<span class="toggle--off"><?php esc_html_e( 'Off', 'wdm_ld_group' ); ?></span>
					</label>
					<span class="description"><?php esc_html_e( 'This email is sent when admin rejects a user removal request.', 'wdm_ld_group' ); ?></span>
				</td>
			</tr>
			<tr>
				<th colspan="2" class="ldgr-heading-cell">
					<label>
						<?php esc_html_e( 'Member', 'wdm_ld_group' ); ?>
					</label>
					<span class="description"><?php esc_html_e( 'Manage emails that are sent to the group members', 'wdm_ld_group' ); ?></span>
				</th>
			</tr>
			<tr class="ldgr-member-emails">
				<th scope="row">
					<label for="ldgr_user_created_member">
						<?php esc_html_e( 'User Created', 'wdm_ld_group' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="ldgr_user_created_member" id="ldgr_user_created_member" class="switch-input" <?php echo ( 'off' !== $ldgr_user_created_member ) ? 'checked' : ''; ?>>
					<label for="ldgr_user_created_member" class="switch-label">
						<span class="toggle--on"><?php esc_html_e( 'On', 'wdm_ld_group' ); ?></span>
						<span class="toggle--off"><?php esc_html_e( 'Off', 'wdm_ld_group' ); ?></span>
					</label>
					<span class="description"><?php esc_html_e( 'This email is sent when a new user account is created and is added into a group. ', 'wdm_ld_group' ); ?></span>
				</td>
			</tr>
			<tr class="ldgr-member-emails">
				<th scope="row">
					<label for="ldgr_user_added_member">
						<?php esc_html_e( 'User Added', 'wdm_ld_group' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="ldgr_user_added_member" id="ldgr_user_added_member" class="switch-input" <?php echo ( 'off' != $ldgr_user_added_member ) ? 'checked' : ''; ?>>
					<label for="ldgr_user_added_member" class="switch-label">
						<span class="toggle--on"><?php esc_html_e( 'On', 'wdm_ld_group' ); ?></span>
						<span class="toggle--off"><?php esc_html_e( 'Off', 'wdm_ld_group' ); ?></span>
					</label>
					<span class="description"><?php esc_html_e( 'This email is sent when a user is added into a group. ', 'wdm_ld_group' ); ?></span>
				</td>
			</tr>
			<tr class="ldgr-member-emails">
				<th scope="row">
					<label for="ldgr_reinvite_user_member">
						<?php esc_html_e( 'Reinvite User', 'wdm_ld_group' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="ldgr_reinvite_user_member" id="ldgr_reinvite_user_member" class="switch-input" <?php echo ( 'off' !== $ldgr_reinvite_user_member ) ? 'checked' : ''; ?>>
					<label for="ldgr_reinvite_user_member" class="switch-label">
						<span class="toggle--on"><?php esc_html_e( 'On', 'wdm_ld_group' ); ?></span>
						<span class="toggle--off"><?php esc_html_e( 'Off', 'wdm_ld_group' ); ?></span>
					</label>
					<span class="description"><?php esc_html_e( 'This email is sent when group leader sends a reinvite email to a group user.', 'wdm_ld_group' ); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	<p class="wc-setup-actions step">
		<input type="submit" class="button-primary button button-large button-next" value="<?php esc_html_e( 'Continue', 'wdm_ld_group' ); ?>" name="save_step">
		<input type="hidden" name="wisdm_setup_step" value="<?php echo esc_attr( $wizard_handler->get_current_step_slug() ); ?>" />
		<a href="<?php echo esc_url( $wizard_handler->get_next_step_link() ); ?>" class="button button-large button-next"><?php esc_html_e( 'Skip this step', 'wdm_ld_group' ); ?></a>
	<?php wp_nonce_field( 'setup_email_configurations', 'wisdm_setup_nonce' ); ?>
	</p>
</form>
