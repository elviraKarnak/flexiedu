<?php
/**
 * Email Team Modal.
 *
 * @since 1.0.0
 * @version 2.1.5
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore classrom childgroup autologin
 */

?>
<?php $form_id = 'add_team_member_email_groups_plus'; ?>
<div id="<?php echo $form_id; ?>_modal" class="groups-plus-modal">
	<!-- Modal content -->
	<div class="groups-plus-modal-content">
		<?php
		$current_user_id        = get_current_user_id();
		$team_member_email_data = get_user_meta( $current_user_id, 'team_member_email_data', true );
		if ( empty( $team_member_email_data ) ) {
			$team_member_email_data = array();
		}
		extract( $team_member_email_data );
		?>
		<div class="groups-plus-modal-nav">
			<ul class="tabs">
				<?php
				$hide_groups_plus_broadcast_email_tab = get_site_option( 'hide_groups_plus_broadcast_email_tab', 'no' );
				if ( $hide_groups_plus_broadcast_email_tab === 'no' ) : ?>
					<li class="tab-link current" data-tab="tab-2">
						<?php esc_html_e( 'Broadcast Email', 'learndash-groups-plus' ); ?>
					</li>
				<?php endif; ?>

				<?php
				$hide_groups_plus_team_member_welcome_email_tab = get_site_option( 'hide_groups_plus_team_member_welcome_email_tab', 'no' );
				if ( $hide_groups_plus_team_member_welcome_email_tab === 'no' ) : ?>
					<li class="tab-link" data-tab="tab-1">
						<?php printf(
							esc_html__( '%s Welcome Email', 'learndash-groups-plus' ),
							learndash_get_custom_label( 'team_member' )
						); ?>
					</li>
				<?php endif ?>
				<?php
				$enable_classrom_message_board = get_site_option( 'enable_classrom_message_board' );
				if ( $enable_classrom_message_board == 'yes' ) : ?>
					<li class="tab-link" data-tab="tab-3">
						<?php esc_html_e( 'Broadcast Messages', 'learndash-groups-plus' ); ?>
					</li>
				<?php endif; ?>
			</ul>
			<button class="tab-link groups-plus-close">&times;</button>
		</div>

		<div class="groups-plus-modal-container with-tab">
			<div id="tab-1" class="tab-content ">
				<h5>
					<?php printf(
						esc_html__( 'Configure %s welcome email', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'team_member' )
					); ?>
				</h5>
				<p class="para">
					<?php printf(
						esc_html__( 'This email template will be sent when you add new %s', 'learndash-groups-plus' ),
						learndash_get_custom_label_lower( 'team_members' )
					); ?>
				</p>
				<br />
				<h6><?php _e( 'Available merge codes', 'learndash-groups-plus' ); ?>:</h6>
				<table class="modal-table">
					<tr>
						<td class="para italic">
							<?php _e( 'Insert name of Parent Group', 'learndash-groups-plus' ); ?>:<code>{group_name}</code>
						</td>
						<td class="para italic">
							<?php _e( 'Insert name of child-group', 'learndash-groups-plus' ); ?>:<code>{childgroup_name}</code>
						</td>
						<td class="para italic">
							<?php printf(
								esc_html__( 'Insert name of %s', 'learndash-groups-plus' ),
								learndash_get_custom_label( 'team_member' )
							); ?> :<code>{team_member_name}</code>
						</td>
						<td class="para italic">
							<?php printf(
								esc_html__( 'Insert username of %s', 'learndash-groups-plus' ),
								learndash_get_custom_label( 'team_member' )
							); ?> :<code>{user_name}</code>
						</td>
						<td class="para italic">
							<?php printf(
								esc_html__( 'Insert password of %s', 'learndash-groups-plus' ),
								learndash_get_custom_label( 'team_member' )
							); ?> :<code>{password}</code>
						</td>
						<td class="para italic">
							<?php printf(
								esc_html__( 'Autologin link of %s', 'learndash-groups-plus' ),
								learndash_get_custom_label( 'team_member' )
							); ?> :<code>{autologin}</code>
						</td>
					</tr>
				</table>
				<br />
				<h5>
					<?php printf(
						esc_html__( '%s Invitation Email', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'team_member' )
					); ?>:
				</h5>
				<form action="" method="POST" id="<?php echo $form_id; ?>" class="from-modal" data-nonce="<?php echo wp_create_nonce( 'learndash-groups-plus-add-team-member-email-groups-plus' ); ?>">
					<div class="form_message"></div>
					<div class="form-group">
						<label class="">
							<?php esc_html_e( 'Subject', 'learndash-groups-plus' ); ?>:
						</label>
						<input type="text" name="team_member_email_subject" value="<?php echo isset( $team_member_email_subject ) ? esc_attr( $team_member_email_subject ) : ''; ?>" placeholder="<?php esc_html_e( 'Subject', 'learndash-groups-plus' ); ?>" autocomplete="off" />

						<label class=""><?php esc_html_e( 'Body', 'learndash-groups-plus' ); ?>:</label>
						<?php
						$content   = isset( $team_member_email_body ) ? $team_member_email_body : '';
						$editor_id = 'team_member_email_body';
						$settings  = array(
							'media_buttons' => false,
							'wpautop'       => false,
						);
						wp_editor( $content, $editor_id, $settings );
						?>
					</div>
					<div class="form-action text-right">
						<button type="submit" class="btn_groups_plus" id="<?php echo $form_id; ?>_btn"><?php esc_html_e( 'Save', 'learndash-groups-plus' ); ?>
							<i class="fa fa-angle-right"></i></button>
						<!-- button type="submit" class="btn_groups_plus"
							id="<?php echo $form_id; ?>_btn_save_and_send"><?php esc_html_e( 'Save & Send', 'learndash-groups-plus' ); ?>
							<i class="fa fa-angle-right"></i></button -->
					</div>
				</form>
			</div>
			<div id="tab-2" class="tab-content current">
				<?php $form_id = 'send_broadcast_email_to_team_members'; ?>
				<h5>
					<?php printf(
						esc_html__( 'Configure %s broadcast email', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'team' )
					); ?>
				</h5>
				<p class="para">
					<?php printf(
						esc_html__( 'This email will be sent immediately to all %s', 'learndash-groups-plus' ),
						learndash_get_custom_label_lower( 'team_members' )
					); ?>
				</p>
				<br />
				<h6><?php _e( 'Available merge codes', 'learndash-groups-plus' ); ?>:</h6>
				<table class="modal-table">
					<tr>
						<td class="para italic">
							<?php _e( 'Insert name of Parent Group', 'learndash-groups-plus' ); ?>:<code>{group_name}</code>
						</td>
						<td class="para italic">
							<?php _e( 'Insert name of child-group', 'learndash-groups-plus' ); ?>:<code>{childgroup_name}</code>
						</td>
						<td class="para italic">
							<?php printf(
								esc_html__( 'Insert name of %s', 'learndash-groups-plus' ),
								learndash_get_custom_label( 'team_member' )
							); ?>: <code>{team_member_name}</code>
						</td>
						<td class="para italic">
							<?php printf(
								esc_html__( 'Insert username of %s', 'learndash-groups-plus' ),
								learndash_get_custom_label( 'team_member' )
							); ?>: <code>{user_name}</code>
						</td>
					</tr>
				</table>
				<br />
				<h5>
					<?php printf(
						esc_html__( '%s Broadcast Email', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'team' )
					); ?>:
				</h5>
				<form action="" method="POST" id="<?php echo $form_id; ?>" class="from-modal" data-nonce="<?php echo wp_create_nonce( 'learndash-groups-plus-send-broadcast-email-to-team-members' ); ?>">
					<div class="form_message"></div>
					<div class="form-group">
						<label class=""><?php esc_html_e( 'Subject', 'learndash-groups-plus' ); ?>:</label>
						<input type="text" name="team_member_b_email_subject" value="" placeholder="<?php esc_html_e( 'Subject', 'learndash-groups-plus' ); ?>" autocomplete="off" />

						<label class=""><?php esc_html_e( 'Body', 'learndash-groups-plus' ); ?>:</label>
						<?php
						$content   = '';
						$editor_id = 'team_member_b_email_body';
						$settings  = array(
							'media_buttons' => false,
							'wpautop'       => false,
						);
						wp_editor( $content, $editor_id, $settings );
						?>
					</div>
					<div class="form-action text-right">
						<button type="submit" class="btn_groups_plus" id="<?php echo $form_id; ?>_btn"><?php esc_html_e( 'Send', 'learndash-groups-plus' ); ?>
							<i class="fa fa-angle-right"></i></button>
					</div>
				</form>
			</div>
			<?php if ( $enable_classrom_message_board == 'yes' ) { ?>
				<div id="tab-3" class="tab-content ">
					<?php $form_id = 'send_broadcast_message_to_team_members'; ?>
					<h5>
						<?php printf(
							esc_html__( '%s Broadcast Message', 'learndash-groups-plus' ),
							learndash_get_custom_label( 'team' )
						); ?>:
					</h5>
					<form action="" method="POST" id="<?php echo $form_id; ?>" class="from-modal" data-nonce="<?php echo wp_create_nonce( 'learndash-groups-plus-send-broadcast-message-to-team-members' ); ?>">
						<div class="form_message"></div>
						<div class="form-group">
							<!-- label class=""><?php esc_html_e( 'Message', 'learndash-groups-plus' ); ?>:</label -->
							<?php
							$content   = '';
							$editor_id = 'team_member_b_message_body';
							$settings  = array(
								'media_buttons' => false,
								'wpautop'       => false,
							);
							wp_editor( $content, $editor_id, $settings );
							?>
						</div>
						<div class="form-action text-right">
							<button type="submit" class="btn_groups_plus" id="<?php echo $form_id; ?>_btn"><?php esc_html_e( 'Send', 'learndash-groups-plus' ); ?>
								<i class="fa fa-angle-right"></i></button>
						</div>
					</form>

					<h6><?php _e( 'Message List:', 'learndash-groups-plus' ); ?></h6>
					<table id="table-broadcast-messages-list" data-nonce="<?php echo wp_create_nonce( 'learndash-groups-plus-manage-broadcast-messages' ); ?>">
						<thead>
							<tr>
								<th width="70%"><?php _e( 'Message', 'learndash-groups-plus' ); ?></th>
								<th width="20%"><?php _e( 'Date', 'learndash-groups-plus' ); ?></th>
								<th width="10%"><?php _e( 'Action', 'learndash-groups-plus' ); ?></th>
							</tr>
						</thead>
						<tbody id="tbody-broadcast-messages-list">
						</tbody>
					</table>
				</div>
			<?php } ?>
		</div>

	</div>
</div>
