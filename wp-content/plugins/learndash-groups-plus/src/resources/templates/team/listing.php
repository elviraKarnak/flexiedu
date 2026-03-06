<?php
/**
 * Team list output for [learndash_groups_plus].
 *
 * @since 1.0.0
 * @version 2.1.0
 *
 * @package LearnDash\Groups_Plus
 */

use LearnDash\Groups_Plus\Module\Group;

?>
<div>
	<table cellspacing="0" class="groups_user_table groups_plus_table">
		<thead>
			<tr>
				<th>
					<?php printf(
						esc_html__( '%s Name', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'team' )
					); ?>
				</th>
				<th class="pull-center">
					<?php printf(
						esc_html__( '%s Total', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'team_member' )
					); ?>
				</th>
				<th class="pull-center">
					<?php
					echo esc_html(
						sprintf(
							// translators: placeholder: Teams label.
							__( 'Manage %s', 'learndash-groups-plus' ),
							learndash_get_custom_label( 'teams' )
						)
					);
					?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $groups ) ) { ?>
			<tr>
				<td colspan="3">
					<?php _e( 'No record found.', 'learndash-groups-plus' ); ?>
				</td>
			</tr>
			<?php } ?>
			<?php
			foreach ( $groups as $group ) {
						$user_query_args  = array(
							'orderby'    => 'display_name',
							'order'      => 'ASC',
							'meta_query' => array(
								array(
									'key'     => 'learndash_group_users_' . intval( $group->ID ),
									'compare' => 'EXISTS',
								),
							),
						);
						$user_query       = new \WP_User_Query( $user_query_args );
						$encrypt_group_id = general_encrypt_decrypt( 'encrypt', $group->ID );

						$courses 	   = learndash_group_enrolled_courses( $group->ID );
						$group_leaders = learndash_get_groups_administrators( $group->ID );
						$group_leader  = array();
						$team_leader_first_name  = $team_leader_last_name = '';
						$list_groups_leaders = array();

						if ( ! empty( $group_leaders ) ) {
							foreach ( $group_leaders as $group_leader ) {
								$group_leader_meta     = get_userdata( $group_leader->ID );
								$list_groups_leaders[] = array(
									'id'         => $group_leader->ID,
									'email'      => $group_leader->user_email,
									'username'   => $group_leader->user_login,
									'first_name' => get_user_meta( $group_leader->ID, 'first_name', true ),
									'last_name'  => get_user_meta( $group_leader->ID, 'last_name', true ),
									'is_admin'   => ( in_array( 'administrator', $group_leader_meta->roles ) ? true : false ),
								);
							}
							$group_leader       = $group_leaders[0];
							$team_leader_first_name = get_user_meta( $group_leader->ID, 'first_name', true );
							$team_leader_last_name  = get_user_meta( $group_leader->ID, 'last_name', true );
						}


						$group_team_member_ids           = learndash_get_groups_user_ids( $group->ID );
						$list_team_member_groups_leaders = array();
						foreach ( $group_team_member_ids as $group_team_member_id ) {
							$team_member      = get_user_by( 'ID', $group_team_member_id );
							$team_member_meta = get_userdata( $group_team_member_id );
							if ( in_array( 'group_leader', $team_member_meta->roles ) ) {
								$list_team_member_groups_leaders[] = array(
									'id'         => $group_team_member_id,
									'email'      => $team_member->user_email,
									'username'   => $team_member->user_login,
									'first_name' => get_user_meta( $group_team_member_id, 'first_name', true ),
									'last_name'  => get_user_meta( $group_team_member_id, 'last_name', true ),
								);
							}
						}
						?>
			<tr>
				<td>
					<h5><?php echo esc_html( $group->post_title ); ?></h5>
				</td>
				<td align="center" class="groups_plus_total_team_member">
					<?php echo esc_html( $user_query->total_users ); ?>
				</td>
				<td align="center">
					<div class="manage-buttons">
						<?php
						$url = "?group={$encrypt_group_id}";

						if ( isset( $wp->query_vars['learndash-groups-plus'] ) ) {
							$url = home_url( '/learndash-groups-plus/group/' ) . $encrypt_group_id;
						}
						?>
						<a href="<?php echo esc_attr( $url ); ?>" class="btn_groups_plus">
							<?php echo esc_html( learndash_get_custom_label( 'team_members' ) ); ?>
							<i class="fa fa-angle-right"></i>
						</a>

						<?php
						$hide_organization_manage_team_leaders_button = get_site_option(
							'hide_organization_manage_team_leaders_button',
							'no'
						);

						if ( $hide_organization_manage_team_leaders_button === 'no' ) :
							?>

						<a href="#" data-group="<?php echo esc_attr( $encrypt_group_id ); ?>"
							data-group-name="<?php echo esc_attr( $group->post_title ); ?>"
							data-team-leader-id="<?php echo isset( $group_leader->ID ) ? esc_attr( $group_leader->ID ) : '0'; ?>"
							data-team-leader-firstname="<?php echo isset( $team_leader_first_name ) ? esc_attr( $team_leader_first_name ) : ''; ?>"
							data-team-leader-lastname="<?php echo isset( $team_leader_last_name ) ? esc_attr( $team_leader_last_name ) : ''; ?>"
							data-team-leader-username="<?php echo isset( $group_leader->user_login ) ? esc_attr( $group_leader->user_login ) : ''; ?>"
							data-team-leader-email="<?php echo isset( $group_leader->user_email ) ? esc_attr( $group_leader->user_email ) : ''; ?>"
							data-group-leaders="<?php echo esc_attr( json_encode( $list_groups_leaders ) ); ?>"
							data-team-member-group-leaders="<?php echo esc_attr( json_encode( $list_team_member_groups_leaders ) ); ?>"
							class="btn_groups_plus btn_groups_plus_black manage_groups_plus_team_leaders">
							<?php echo esc_html( learndash_get_custom_label( 'team_leaders' ) ); ?>
							<i class="fa fa-angle-right"></i>
						</a>
							<?php
						endif;
						?>

						<?php
						if ( Group::is_admin_or_primary_group_leader( Group::$parent_group_id ) ) :
							?>
							<?php
							$hide_organization_manage_course_button = get_site_option( 'hide_organization_manage_course_button', 'no' );

							$created_thru_wc = (bool) get_post_meta( Group::$parent_group_id, 'has_group_created_thru_wc', true );

							if (
								$hide_organization_manage_course_button === 'no'
								&& (
									current_user_can( 'manage_options' )
									|| $created_thru_wc !== true
								)
							) :
								?>
							<a href="#" data-group="<?php echo esc_attr( $encrypt_group_id ); ?>"
								data-group-name="<?php echo esc_attr( $group->post_title ); ?>"
								data-group-courses="<?php echo esc_attr( '[' . implode( ',', $courses ) . ']' ); ?>"
								class="btn_groups_plus btn_groups_plus_black manage_groups_plus_courses">
								<?php
								echo esc_html( learndash_get_custom_label( 'courses' ) );
								?>
								<i class="fa fa-angle-right"></i>
							</a>
								<?php
							endif;
						endif;
						?>
					</div>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
