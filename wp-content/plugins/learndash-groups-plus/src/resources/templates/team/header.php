<?php
/**
 * Outputs the Header for Teams views.
 *
 * TODO: Refactor to remove logic from the template
 *
 * @since 1.0.0
 * @version 2.1.2
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore ramaining
 */

use LearnDash\Groups_Plus\Module\Group;
use LearnDash\Groups_Plus\Utility\SharedFunctions;

?>
<?php
	$header_background_color           = get_site_option( 'header_background_color', '#EAEAEA' );
	$header_border_color               = get_site_option( 'header_border_color', '#231F20' );
	$change_the_used_seats_icon_color  = get_site_option( 'change_the_used_seats_icon_color', '#000' );
	$change_seats_remaining_icon_color = get_site_option( 'change_seats_remaining_icon_color', '#000' );
	$header_background_color           = get_site_option( 'header_background_color', '#EAEAEA' );
	$header_border_color               = get_site_option( 'header_border_color', '#231F20' );
?>
<style>
	.groups_plus_header { background: <?php echo $header_background_color; ?>; border-color:<?php echo $header_border_color; ?>; }
	.learndash-groups-plus-user-licenses::before { color: <?php echo $change_the_used_seats_icon_color; ?>; }
	.learndash-groups-plus-user-licenses-remaining::before { color: <?php echo $change_seats_remaining_icon_color; ?>; }
	.groups_plus_header { background: <?php echo $header_background_color; ?>; border-color:<?php echo $header_border_color; ?>;}
</style>
<div class="groups_plus_header" data-primary-group-id="<?php echo Group::$parent_group_id; ?>">

	<div class="groups_plus_licenses">
		<?php
			$change_the_used_seats_attachment_id = get_site_option( 'change_the_used_seats_attachment_id' );

		if ( isset( $change_the_used_seats_attachment_id ) && $used_seats_attachment_id = wp_get_attachment_image_src( $change_the_used_seats_attachment_id ) ) {
			echo '<img src="' . $used_seats_attachment_id[0] . '"  class="custom_header_icon_image" />';
		} else {
			?>
		<span class="learndash-groups-plus-user-licenses"></span>
		<?php } ?>


		<span class="groups-plus-license-text">
			<?php
			$hide_seats_used_text = get_site_option( 'hide_seats_used_text', 'no' );
			if ( $hide_seats_used_text === 'no' ) {
				esc_html_e( 'Total Seats Used:', 'learndash-groups-plus' );
			}
			?>
			<?php echo $license_info['licenses_used']; ?>
		</span>

	</div>



	<div class="groups_plus_remaining_licenses">
		<?php
			$change_seats_remaining_attachment_id = get_site_option( 'change_seats_remaining_attachment_id' );

		if ( isset( $change_seats_remaining_attachment_id ) && $seats_remaining_attachment_id = wp_get_attachment_image_src( $change_seats_remaining_attachment_id ) ) {
			echo '<img src="' . $seats_remaining_attachment_id[0] . '"  class="custom_header_icon_image" />';
		} else {
			?>
		<span class="learndash-groups-plus-user-licenses-remaining"></span>
		<?php } ?>


		<span class="groups-plus-license-text">
			<?php
			$hide_seats_ramaining_text = get_site_option( 'hide_seats_ramaining_text', 'no' );
			if ( $hide_seats_ramaining_text === 'no' ) {
				esc_html_e( 'Seats Remaining:', 'learndash-groups-plus' );
			}
			?>
			<?php echo $license_info['licenses_remaining']; ?>
		</span>

	</div>


	<?php
	if ( get_query_var( 'group_id' ) == false && ! isset( $_GET['group'] ) && Group::is_admin_or_primary_group_leader( Group::$parent_group_id ) ) {

		$encrypt_group_id = general_encrypt_decrypt( 'encrypt', 0 );
		$groups_plus      = array();
		foreach ( $groups as $group ) {
			$user_query_args = array(
				'orderby'    => 'display_name',
				'order'      => 'ASC',
				'meta_query' => array(
					array(
						'key'     => 'learndash_group_users_' . intval( $group->ID ),
						'compare' => 'EXISTS',
					),
				),
			);
			$user_query      = new \WP_User_Query( $user_query_args );

			$groups_plus[] = array(
				'id'             => general_encrypt_decrypt( 'encrypt', $group->ID ),
				'title'          => $group->post_title,
				'total_team_members' => $user_query->total_users,
			);
		}
		?>

		<?php
		$hide_manage_organization_button = get_site_option( 'hide_manage_organization_button', 'no' );
		if ( $hide_manage_organization_button === 'no' ) {
			?>

	<div class="groups_plus_manage">
		<a href="#" class="btn_groups_plus init_groups_plus_group" data-group="<?php echo $encrypt_group_id; ?>"
			data-group-name=""
			data-groups="<?php echo esc_attr( json_encode( $groups_plus ) ); ?>"><?php printf( esc_html__( 'Manage %s', 'learndash-groups-plus' ), learndash_get_custom_label( 'organization' ) ); ?>
			<i class="fa fa-angle-right"></i></a>
	</div>
	<?php } ?>

		<?php
		$hide_organization_email_button = get_site_option( 'hide_organization_email_button', 'no' );
		if ( $hide_organization_email_button === 'no' ) {
			?>
	<div class="groups_plus_email">
		<a href="#" class="btn_groups_plus init-groups-plus-team-leader-email" data-group="<?php echo $encrypt_group_id; ?>"
			data-group-name="" data-group-courses=""><?php esc_html_e( 'Email', 'learndash-groups-plus' ); ?> <i
				class="fa fa-angle-right"></i></a>
	</div>
	<?php } ?>

		<?php
	} elseif (
		get_query_var( 'group_id', false ) !== false
		|| isset( $_GET['group'] )
	) {
		if ( get_query_var( 'group_id', false ) !== false ) {
			$group_id = get_query_var( 'group_id', false );
		} else {
			$get_data = wp_unslash( $_GET );
			$group_id = sanitize_text_field( $get_data['group'] );
		}

		$group_id = general_encrypt_decrypt( 'decrypt', $group_id );

		if ( ! get_post_meta( $group_id, SharedFunctions::$is_non_organization_team, true ) ) :
			$url = home_url( 'learndash-groups-plus' );
			if ( get_query_var( 'group_id', false ) === false ) {
				$url = get_the_permalink();
			}

			$url = add_query_arg(
				[
					'parent-group-id' => Group::$parent_group_id,
				],
				$url
			);

			?>
			<div class="groups_plus_back_to_groups_plus">
				<a href="<?php echo esc_url( $url ); ?>"
					class="btn_groups_plus btn_groups_plus_black btn_back_to_groups_plus"
				>
					<i class="fa fa-angle-left"></i>
					<?php
					echo esc_html(
						sprintf(
							// translators: Organization.
							__( 'Back to %s', 'learndash-groups-plus' ),
							learndash_get_custom_label( 'organization' )
						)
					);
					?>
				</a>
			</div>
			<?php
		else :
			?>

			<div class="team-select">
				<label class="title">
					<?php
					echo esc_html(
						sprintf(
							// translators: Organization.
							__( 'Change %s:', 'learndash-groups-plus' ),
							learndash_get_custom_label( 'organization' )
						)
					);
					?>
				</label>
				<select name="primary_group_id">
					<option value="">
						<?php
						echo esc_html(
							sprintf(
								// translators: organization.
								__( '-- Select %s --', 'learndash-groups-plus' ),
								learndash_get_custom_label_lower( 'organization' )
							)
						);
						?>
					</option>
					<?php foreach ( Group::get_all_primary_groups() as $single_primary_group ) : ?>
						<option value="<?php echo esc_attr( $single_primary_group->ID ); ?>" <?php selected( $group_id, $single_primary_group->ID ); ?>>
							<?php echo esc_html( $single_primary_group->post_title ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

		<?php endif; ?>
	<?php } ?>
</div>
