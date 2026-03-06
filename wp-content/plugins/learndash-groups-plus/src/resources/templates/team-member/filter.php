<?php
/**
 * Team Member Filter form.
 *
 * @since 1.0.0
 * @version 2.1.1
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore numberposts
 */

namespace LearnDash\Groups_Plus;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use LearnDash\Groups_Plus\Module\Group;
use LearnDash\Core\Utilities\Cast;

global $wpdb;
?>
<div class="row groups_plus_team_member_filter text-left">
	<?php
		$filter_team_member_id = $filter_course_id = 0;

	if ( isset( $_GET ) && ( isset( $_GET['filter_team_member_id'] ) && ! empty( $_GET['filter_team_member_id'] ) ) ) {
		$filter_team_member_id = $_GET['filter_team_member_id'];
	}
	if ( isset( $_GET ) && ( isset( $_GET['filter_course_id'] ) && ! empty( $_GET['filter_course_id'] ) ) ) {
		$filter_course_id = $_GET['filter_course_id'];
	}

		$selected_team_member_id = $filter_team_member_id = ( isset( $_GET['filter_team_member_id'] ) && ! empty( $_GET['filter_team_member_id'] ) ? $_GET['filter_team_member_id'] : ( ! empty( $user_group_users ) ? $user_group_users[0]->ID : 0 ) );

		// Group Course
		$group_courses = learndash_group_enrolled_courses( $group_id );

		$course_orderby = get_site_option( 'course_orderby' ) ?? 'title';
		$course_order   = get_site_option( 'course_order' ) ?? 'ASC';

		$args = [
			'numberposts' => -1,
			'post_type'   => 'sfwd-courses',
			'orderby'     => Cast::to_string( $course_orderby ),
			'post__in'    => [ 0 ],
			'order'       => Cast::to_string( $course_order ),
		];
		if ( ! empty( $group_courses ) ) {
			$args['post__in'] = $group_courses;
		}
		$result_courses = get_posts( $args );

		$filter_course_id = $filter_course_id != 0 ? $filter_course_id : ( ! empty( $result_courses ) ? $result_courses[0]->ID : 0 );
		?>
	<form name="frm_team_member_course_filter" method="get" id="frm_team_member_course_filter">
		<div class="section">
			<?php
			$add_grid_class = false;

			if ( isset( $shortcode_name ) && in_array( $shortcode_name, array( 'learndash_groups_plus' ) ) && isset( $_GET['group'] ) && $_GET['group'] != '' ) {
				?>

			<input type="hidden" name="group" value="<?php echo $_GET['group']; ?>" />

				<?php
			} elseif ( isset( $shortcode_name ) && in_array( $shortcode_name, array( 'learndash_groups_plus_report' ) ) ) {
				$groups_id_array = learndash_get_administrators_group_ids( get_current_user_id() );

				$args      = array(
					'numberposts' => -1,
					'post_type'   => 'groups',
					'post__in'    => $groups_id_array,
					'orderby'     => 'menu_order',
					'order'       => 'DESC',
					'post_parent' => Group::$parent_group_id,
				);
				$dd_groups = get_posts( $args );
				if ( empty( $dd_groups ) ) {
					// $dd_groups = $parent_group;
				}
				$filter_group_id = isset( $_GET['group'] ) ? $_GET['group'] : ( ! empty( $dd_groups ) ? general_encrypt_decrypt( 'encrypt', $dd_groups[0]->ID ) : 0 );

				$group_id = general_encrypt_decrypt( 'decrypt', $filter_group_id );
				if ( $group_id && is_numeric( $group_id ) ) {
					$user_group_users = Group::get_group_users( $group_id );
				}
				$add_grid_class = true;
				?>
			<div class="col grid_1_of_3 <?php echo ( $add_grid_class ? 'grid_1_of_4' : '' ); ?> ">
				<label>
					<strong>
						<?php printf(
							esc_html__( '%s Name', 'learndash-groups-plus' ),
							learndash_get_custom_label( 'team' )
						); ?>
					</strong>
				</label>
				<select name="group" id="filter_group_id">
					<?php
					foreach ( $dd_groups as $dd_group ) {
						$encrypt_group_id = general_encrypt_decrypt( 'encrypt', $dd_group->ID );
						echo '<option value="' . $encrypt_group_id . '" ' . ( $encrypt_group_id == $filter_group_id ? 'selected=selected' : '' ) . '>' . $dd_group->post_title . '</option>';
					}
					?>
				</select>
			</div>
				<?php
			}
			?>

			<div class="col grid_1_of_3 <?php echo ( $add_grid_class ? 'grid_1_of_4' : '' ); ?>">
				<label>
					<strong>
						<?php printf(
							esc_html__( '%s Name', 'learndash-groups-plus' ),
							learndash_get_custom_label( 'team_member' )
						); ?>
					</strong>
				</label>
				<select name="filter_team_member_id" id="filter_team_member_id">
					<?php
					if ( isset( $user_group_users ) && is_array( $user_group_users ) ) {
						foreach ( $user_group_users as $user ) {
							echo '<option value="' . $user->ID . '" ' . ( $user->ID == $filter_team_member_id ? 'selected=selected' : '' ) . '>' . $user->first_name . ' ' . $user->last_name . ' (' . $user->user_login . ')</option>';
						}
					}
					?>
				</select>
			</div>
			<div class="col grid_1_of_3 <?php echo ( $add_grid_class ? 'grid_1_of_4' : '' ); ?> course-selector">
				<label><strong><?php esc_html_e( 'Course Name', 'learndash-groups-plus' ); ?></strong></label>
				<select name="filter_course_id" id="filter_course_id">
					<?php
					foreach ( $result_courses as $course ) {
						echo '<option value="' . $course->ID . '" ' . ( $course->ID == $filter_course_id ? 'selected=selected' : '' ) . '>' . esc_html( $course->post_title ) . '</option>';
					}
					?>
				</select>
			</div>
			<div class="col grid_1_of_5" style="float:right;">
				<label><strong>&nbsp;</strong></label>
				<button class="btn_groups_plus"><?php esc_html_e( 'Filter', 'learndash-groups-plus' ); ?><i
						class="fa fa-angle-right"></i></button>
			</div>
		</div>
		<div class="section last-login-section">
			<?php
			$last_login_detail = get_user_meta( $filter_team_member_id, 'learndash-last-login' );
			if ( ! empty( $last_login_detail ) ) {
				$last_login_detail = $last_login_detail[0];
				$last_login_date   = date( 'M j, Y', $last_login_detail );
			} else {
				$last_login_date = esc_html( 'No Record Found', 'learndash-groups-plus' );
			}
			?>
			<div class="col grid_2_of_2">

				<p style="">
					<label><strong><?php esc_html_e( 'Last Login Date', 'learndash-groups-plus' ); ?>:
						</strong></label><?php echo $last_login_date; ?>
				</p>
			</div>
		</div>
	</form>
</div>
