<?php
/**
 * Sub groups tab content template.
 *
 * @since 4.2.0
 * @version 4.3.15
 *
 * @package LearnDash\Seats_Plus
 *
 * cspell:ignore serachbox cnsg subgr
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="tab-<?php echo esc_attr( $content['id'] ); ?>" class="tab-content ldgr-sub-group-tab <?php echo ( learndash_is_groups_hierarchical_enabled() && false !== $is_sub_group ) ? '' : 'current'; ?>">
	<div class="ldgr-sub-groups-content">
		<div class="ldgr-sub-groups-items">
		<?php if ( ! count( $sub_groups ) ) : ?>
				<span>
				<?php
				printf(
					// translators: sub group label, group label.
					esc_html__( 'No %1$s exist in this %2$s', 'wdm_ld_group' ),
					\LearnDash_Custom_Label::label_to_lower( 'subgroups' ),
					\LearnDash_Custom_Label::label_to_lower( 'group' )
				);
				?>
				<span>
		<?php endif; ?>
		<?php foreach ( $sub_groups as $sub_group ) : ?>
			<?php
			$leader_name          = '';
			$sub_group_leader_ids = learndash_get_groups_administrator_ids( $sub_group );
			if ( $sub_group_leader_ids && count( $sub_group_leader_ids ) ) {
				$leader_name = get_userdata( $sub_group_leader_ids[0] )->display_name;
			}
			?>
			<div class="ldgr-sub-groups-item">
				<h2 class="ldgr-sub-gr-title" title="<?php echo esc_html( get_the_title( $sub_group ) ); ?>"><?php echo esc_html( mb_strimwidth( get_the_title( $sub_group ), 0, apply_filters( 'ldgr_course_character_truncate_limit', 50 ), '...' ) ); ?></h2>
				<div class="ldgr-sub-gr-info">
					<div class="ldgr-gr-leader">
						<?php
						printf(
							// translators: Sub Group Label.
							esc_html__( '%s Leader:', 'wdm_ld_group' ),
							\LearnDash_Custom_Label::get_label( 'subgroup' )
						);
						?>
						<?php echo esc_html( $leader_name ); ?>
					</div>
					<div class="">
						<!-- <form class="wdm_search_submit" method='post'> -->
							<!-- <input name="wdm_group_id" type="hidden" value=""> -->
							<button class="ldgr-edit-subgr" style="background: transparent;box-shadow: none;color: #333;padding: 0;" data-sub_group_id="<?php echo esc_html( $sub_group ); ?>">
								<i class="ldgr-icon-Edit"></i>
								<span class="ldgr-color">
									<?php
									printf(
										// translators: sub group label.
										esc_html__( 'Edit %s', 'wdm_ld_group' ),
										\LearnDash_Custom_Label::label_to_lower( 'subgroup' )
									);
									?>
								</span>
							</button>
						<!-- </form> -->
					</div>
				</div>
			</div>
		<?php endforeach; ?>
		</div>
		<span class="ldgr-btn cnsg-btn">
		<?php
		printf(
			// translators: Sub Group label.
			esc_html__( 'Create New %s', 'wdm_ld_group' ),
			\LearnDash_Custom_Label::get_label( 'subgroup' )
		);
		?>
		</span>
	</div>


	<div class="ldgr-create-new-sg">
			<h2 class="ldgr-new-sg-heading">
				<?php
				printf(
					// translators: Sub Group label.
					esc_html__( 'New %s', 'wdm_ld_group' ),
					\LearnDash_Custom_Label::get_label( 'subgroup' )
				);
				?>
			</h2>
			<div class="ldgr-field">
				<label>
				<?php
				printf(
					// translators: Sub Group label.
					esc_html__( '%s name', 'wdm_ld_group' ),
					\LearnDash_Custom_Label::get_label( 'subgroup' )
				);
				?>
				</label>
				<input type="text" class="ldgr-textbox cnsg-name create-sub-group-name" name="create_sub_group_name">
			</div>
			<div class="ldgr-field">
				<label><?php esc_html_e( 'Number of Seats', 'wdm_ld_group' ); ?></label>
				<input type="number" min=0 class="ldgr-textbox cnsg-nos create-sub-group-seat" name="create-sub-group-seat">
			</div>
			<div class="ldgr-field">
				<label>
					<?php
					printf(
						// translators: Sub Group label.
						esc_html__( '%s Leader', 'wdm_ld_group' ),
						\LearnDash_Custom_Label::get_label( 'subgroup' )
					);
					?>
				</label>
				<div class="ldgr-search-list-wrap">
					<div class="ldgr-serachbox-wrap">
						<input type="search" placeholder="Type a name to search or add from the list below" class="ldgr-search">
					</div>
					<div class="ldgr-listing-alphabets">
						<div class="ldgr-alphabets">
							<?php
							foreach ( range( 'A', 'Z' ) as $char ) {
								echo '<span>' . $char . '</span>';
							}
							?>
						</div>
						<div class="ldgr-list ldgr-gl-list">
							<?php foreach ( $users as $user ) : ?>
							<div class="ldgr-chk-item">
								<input type="checkbox" class="create-sub-group-leader" id="leader-<?php echo esc_html( $user ); ?>" name="create-sub-group-leader[]" data-name="<?php echo esc_html( get_userdata( $user )->display_name ); ?>" value="<?php echo esc_html( $user ); ?>">
								<label for="leader-<?php echo esc_html( $user ); ?>"><?php echo esc_html( get_userdata( $user )->display_name ); ?></label>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="ldgr-field">
				<label><?php esc_html_e( 'Add Users', 'wdm_ld_group' ); ?></label>
				<div class="ldgr-search-list-wrap">
					<div class="ldgr-serachbox-wrap">
						<input type="search" placeholder="Select users from the enrolled users list below" class="ldgr-search">
					</div>
					<div class="ldgr-listing-alphabets">
						<div class="ldgr-alphabets">
							<?php
							foreach ( range( 'A', 'Z' ) as $char ) {
								echo '<span>' . $char . '</span>';
							}
							?>
						</div>
						<div class="ldgr-list ldgr-usr-list">
							<?php foreach ( $users as $user_id ) : ?>
							<div class="ldgr-chk-item">
								<input type="checkbox" class="create-sub-group-user" id="usr-<?php echo esc_html( $user_id ); ?>" name="create-sub-group-user[]" value="<?php echo esc_html( $user_id ); ?>" data-name="<?php echo esc_html( get_userdata( $user_id )->display_name ); ?>">
								<label for="usr-<?php echo esc_html( $user_id ); ?>"><?php echo esc_html( get_userdata( $user_id )->display_name ); ?></label>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="ldgr-field">
				<label><?php echo esc_html( \LearnDash_Custom_Label::get_label( 'courses' ) ); ?></label>
				<select class="create-sub-group-courses" name="create-sub-group-courses[]" multiple="multiple">
					<?php foreach ( $sub_group_courses as $course_id ) : ?>
						<option value="<?php echo esc_html( $course_id ); ?>"><?php echo esc_html( get_the_title( $course_id ) ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="ldgr-eg-actions">
				<input class="parent-group-id" name="parent-group-id" type="hidden" value="<?php echo esc_html( $group_id ); ?>">
				<input class="is-unlimited-seats" name="is-unlimited-seats" type="hidden" value="<?php echo esc_html( $is_unlimited ); ?>">
				<input class="parent-group-limit" name="parent-group-limit" type="hidden" value="<?php echo esc_html( $grp_limit_count ); ?>">
				<span class="ldgr-btn create-sg-cancel"><?php esc_html_e( 'Cancel', 'wdm_ld_group' ); ?></span>
				<span class="ldgr-btn ldgr-bg-color solid create-sub-group-submit"><?php esc_html_e( 'Submit', 'wdm_ld_group' ); ?></span>
				<?php wp_nonce_field( 'add-sub_group_' . get_current_user_id(), 'ldgr-add-sub_group' ); ?>
			</div>
			<form method="post" class="create-sub-group-parent-form">
				<input type="hidden" name="wdm_group_id" value="<?php echo esc_html( $group_id ); ?>">
			</form>
	</div>


	<div class="ldgr-edit-sg">
		<h2 class="ldgr-edit-sg-heading">
			<?php
			printf(
				// translators: Sub Group label.
				esc_html__( 'Edit %s', 'wdm_ld_group' ),
				\LearnDash_Custom_Label::get_label( 'subgroup' )
			);
			?>
		</h2>
		<div class="ldgr-field">
			<label>
			<?php
			printf(
				// translators: Sub Group label.
				esc_html__( '%s name', 'wdm_ld_group' ),
				\LearnDash_Custom_Label::get_label( 'subgroup' )
			);
			?>
			</label>
			<input type="text" class="ldgr-textbox cnsg-name edit-sub-group-name" name="edit_sub_group_name">
		</div>
		<div class="ldgr-field">
			<label><?php esc_html_e( 'Number of Seats', 'wdm_ld_group' ); ?></label>
			<input type="number" min=0 class="ldgr-textbox cnsg-nos edit-sub-group-seat" name="edit-sub-group-seat">
			<input type="hidden" name="previous-edit-sub-group-seat"><!-- This field is to compare previous and updated seat count. -->
		</div>
		<div class="ldgr-field">
			<label>
				<?php
				printf(
					// translators: Sub Group label.
					esc_html__( '%s Leader', 'wdm_ld_group' ),
					\LearnDash_Custom_Label::get_label( 'subgroup' )
				);
				?>
			</label>
			<div class="ldgr-search-list-wrap">
				<div class="ldgr-serachbox-wrap">
					<input type="search" placeholder="Type a name to search or add from the list below" class="ldgr-search">
				</div>
				<div class="ldgr-listing-alphabets">
					<div class="ldgr-alphabets">
						<?php
						foreach ( range( 'A', 'Z' ) as $char ) {
							echo '<span>' . $char . '</span>';
						}
						?>
					</div>
					<div class="ldgr-list ldgr-gl-list">
						<?php foreach ( $users as $user ) : ?>
						<div class="ldgr-chk-item">
							<input type="checkbox" class="edit-sub-group-leader" id="leader-<?php echo esc_html( $user ); ?>" name="edit-sub-group-leader[]" data-name="<?php echo esc_html( get_userdata( $user )->display_name ); ?>" value="<?php echo esc_html( $user ); ?>">
							<label for="leader-<?php echo esc_html( $user ); ?>"><?php echo esc_html( get_userdata( $user )->display_name ); ?></label>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="ldgr-field">
			<label><?php esc_html_e( 'Add Users', 'wdm_ld_group' ); ?></label>
			<div class="ldgr-search-list-wrap">
				<div class="ldgr-serachbox-wrap">
					<input type="search" placeholder="Select users from the enrolled users list below" class="ldgr-search">
				</div>
				<div class="ldgr-listing-alphabets">
					<div class="ldgr-alphabets">
						<?php
						foreach ( range( 'A', 'Z' ) as $char ) {
							echo '<span>' . $char . '</span>';
						}
						?>
					</div>
					<div class="ldgr-list ldgr-usr-list">
						<?php foreach ( $users as $user_id ) : ?>
						<div class="ldgr-chk-item">
							<input type="checkbox" class="edit-sub-group-user" id="usr-<?php echo esc_html( $user_id ); ?>" name="edit-sub-group-user[]" value="<?php echo esc_html( $user_id ); ?>" data-name="<?php echo esc_html( get_userdata( $user_id )->display_name ); ?>">
							<label for="usr-<?php echo esc_html( $user_id ); ?>"><?php echo esc_html( get_userdata( $user_id )->display_name ); ?></label>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="ldgr-field">
			<label><?php echo esc_html( \LearnDash_Custom_Label::get_label( 'courses' ) ); ?></label>
			<select class="edit-sub-group-courses" name="edit-sub-group-courses[]" multiple="multiple">
				<?php foreach ( $sub_group_courses as $course_id ) : ?>
					<option value="<?php echo esc_html( $course_id ); ?>"><?php echo esc_html( get_the_title( $course_id ) ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="ldgr-eg-actions">
			<input class="sub-group-id" name="sub-group-id" type="hidden" value="">
			<input class="parent-group-id" name="parent-group-id" type="hidden" value="<?php echo esc_html( $group_id ); ?>">
			<input class="is-unlimited-seats" name="is-unlimited-seats" type="hidden" value="<?php echo esc_html( $is_unlimited ); ?>">
			<input class="parent-group-limit" name="parent-group-limit" type="hidden" value="<?php echo esc_html( $grp_limit_count ); ?>">
			<span class="ldgr-btn edit-sg-cancel"><?php esc_html_e( 'Cancel', 'wdm_ld_group' ); ?></span>
			<span class="ldgr-btn ldgr-bg-color solid edit-sub-group-submit"><?php esc_html_e( 'Submit', 'wdm_ld_group' ); ?></span>
			<?php wp_nonce_field( 'edit-sub_group_' . get_current_user_id(), 'ldgr-edit-sub_group' ); ?>
		</div>
		<form method="post" class="edit-sub-group-parent-form">
			<input type="hidden" name="wdm_group_id" value="<?php echo esc_html( $group_id ); ?>">
		</form>
	</div>
</div>
