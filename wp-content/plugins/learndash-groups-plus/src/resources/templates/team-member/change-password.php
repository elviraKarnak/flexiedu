<?php
/**
 * Password change modal.
 *
 * @since 1.0.0
 * @version 2.1.2
 *
 * @package LearnDash\Groups_Plus
 */

$form_id = 'save_groups_plus_team_member_password';
?>
<div id="<?php echo $form_id; ?>_modal" class="groups-plus-modal">
	<!-- Modal content -->
	<div class="groups-plus-modal-content">
		<div class="groups-plus-modal-nav">
			<ul class="tabs">
				<li class="link-tab current">
					<?php esc_html_e( 'Change Password', 'learndash-groups-plus' ); ?>
				</li>
			</ul>
			<button class="groups-plus-close">&times;</button>
		</div>
		<div class="groups-plus-modal-container">
			<form action="" method="POST" id="<?php echo $form_id; ?>"
				data-nonce="<?php echo wp_create_nonce( 'learndash-groups-plus-change-team-member-password' ); ?>">
				<div class="form_message"></div>
				<div class="form-group section">
					<div class="col grid_1_of_4">
						<input type="hidden" name="user" />
						<input type="password" name="new_password"
							placeholder="<?php esc_html_e( 'New password', 'learndash-groups-plus' ); ?>">
					</div>
					<div class="col grid_1_of_4" style="margin-left:50px;">
						<input type="password" name="confirm_password"
							placeholder="<?php esc_html_e( 'Confirm password', 'learndash-groups-plus' ); ?>">
					</div>
				</div>
				<div class="form-action section text-right pull-left action-buttons">
					<div class="col grid_1_of_4">
						<button type="submit" class="btn_groups_plus"
							id="<?php echo $form_id; ?>_btn"><?php esc_html_e( 'Save', 'learndash-groups-plus' ); ?><i
								class="fa fa-angle-right"></i></button>
					</div>
				</div>
				<div class="form-action section text-right"></div>
			</form>
		</div>
	</div>
</div>
