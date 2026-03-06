<?php
/**
 * Team Member Login History section when viewing a Team.
 *
 * @since 1.0.0
 * @version 2.1.2
 *
 * @package LearnDash\Groups_Plus
 */

$ck_login_time = get_user_meta( $filter_team_member_id, 'current_login', true );
if ( ! is_array( $ck_login_time ) ) {
	$ck_login_time = array();
} else {
	$ck_login_time = array_reverse( $ck_login_time );
}
?>
<table cellspacing="0" class="groups_user_table groups_plus_table history-table">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Date / Time', 'learndash-groups-plus' ); ?></th>
			<th><?php esc_html_e( 'Event', 'learndash-groups-plus' ); ?></th>
		</tr>
	</thead>
	<tbody class="history-table">
		<?php
		foreach ( $ck_login_time as $loginTime ) {
			?>
		<tr>
			<td><?php echo date( 'M j, Y H:i a', strtotime( $loginTime ) ); ?></td>
			<td><?php esc_html_e( 'Logged In', 'learndash-groups-plus' ); ?></td>
		</tr>
			<?php
		};
		?>

		<?php
			$user_course_from_ids = learndash_get_user_courses_from_meta( $filter_team_member_id );
		foreach ( $user_course_from_ids as $user_course_from_id ) {
			?>
		<tr>
			<td><?php echo date( 'M j, Y H:i a', get_user_meta( $filter_team_member_id, 'course_' . $user_course_from_id . '_access_from', true ) ); ?>
			</td>
			<td><?php echo sprintf( esc_html( 'Accessed %s', 'learndash-groups-plus' ), get_the_title( $user_course_from_id ) ); ?>
			</td>
		</tr>
		<?php } ?>

		<?php if ( empty( $ck_login_time ) && empty( $user_course_from_ids ) ) { ?>
		<tr>
			<td colspan="2"><?php esc_html_e( 'No record found.', 'learndash-groups-plus' ); ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
