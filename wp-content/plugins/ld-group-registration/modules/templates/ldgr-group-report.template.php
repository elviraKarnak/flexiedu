<?php
/**
 * Template: LDGR Group Report Template.
 *
 * Show group reports for specific selected courses to group leader.
 *
 * @since 4.0.0
 * @version 4.3.15
 *
 * @param int $group_id     ID of the LearnDash Group.
 * @param int $tab_id       ID of the tab on groups dashboard.
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;
?>

<div id="tab-<?php echo esc_attr( $tab_id ); ?>" class="tab-content">
	<?php
		global $learndash_assets_loaded;
	if ( ! isset( $learndash_assets_loaded['scripts']['learndash_template_script_js'] ) ) {
		$filepath = SFWD_LMS::get_template( 'learndash_template_script.js', null, null, true );
		if ( ! empty( $filepath ) ) {
			wp_enqueue_script(
				'learndash_template_script_js',
				learndash_template_url_from_path( $filepath ),
				[ 'jquery' ],
				LEARNDASH_SCRIPT_VERSION_TOKEN,
				true
			);
			$learndash_assets_loaded['scripts']['learndash_template_script_js'] = __FUNCTION__;

			$data            = [];
			$data['ajaxurl'] = admin_url( 'admin-ajax.php' );
			$data            = [ 'json' => json_encode( $data ) ];
			wp_localize_script( 'learndash_template_script_js', 'sfwd_data', $data );
		}
	}
		LD_QuizPro::showModalWindow();
	?>
	<?php
	// check if any course associated with group, any user enrolled.
	$group_courses = learndash_group_enrolled_courses( $group_id );

	/**
	 * Filter the list of courses in the group on groups dashboard.
	 *
	 * @param array $group_courses  List of courses in the group.
	 * @param int $group_id         ID of the group.
	 *
	 * @since 4.1.5
	 */
	$group_courses = apply_filters( 'ldgr_filter_group_course_list', $group_courses, $group_id );

	if ( empty( $group_courses ) ) {
		echo esc_html(
			sprintf(
				// translators: Course label.
				__( 'No %s associated with selected group!', 'wdm_ld_group' ),
				\LearnDash_Custom_Label::get_label( 'course' )
			)
		);
		echo '</div>';
		return;
	}
	?>

	<div class="ldgr-black-screen" style="display:none">
		<span style="margin-bottom:10px;"><?php esc_html_e( 'Loading...', 'wdm_ld_group' ); ?></span>
		<span class="dashicons dashicons-update spin"></span>
	</div>
	<div class="wdm-select-wrapper">
		<h6>
			<?php
			echo esc_html(
				apply_filters(
					'wdm_ldgr_course_selection_dropdown_label',
					// translators: Course label.
					sprintf( __( 'Select %s', 'wdm_ld_group' ), \LearnDash_Custom_Label::get_label( 'Course' ) )
				)
			);
			?>
		</h6>
		<select id="wdm_ldgr_course_id" name="wdm_ldgr_course_id">
			<!-- <option value="">
				<?php
				echo esc_html(
					apply_filters(
						'wdm_ldgr_course_selection_dropdown_label',
						// translators: Course label.
						sprintf( __( 'Select %s', 'wdm_ld_group' ), \LearnDash_Custom_Label::get_label( 'Course' ) )
					)
				);
				?>
			</option> -->
				<?php
				foreach ( $group_courses as $group_course ) {
					$demo_title   = get_post( $group_course );
					$course_title = $demo_title->post_title;
					?>
					<option value="<?php echo esc_html( $group_course ); ?>" title="<?php echo esc_attr( $course_title ); ?>">
						<?php echo esc_html( mb_strimwidth( esc_html( $course_title ), 0, apply_filters( 'ldgr_course_character_truncate_limit', 50 ), '...' ) ); ?>
					</option>
					<?php
				}
				?>
		</select>
		<input
			type="button"
			id="wdm_ldgr_show_report"
			class="ldgr-bg-color"
			name="wdm_ldgr_show_report"
			value="
			<?php
			echo esc_html(
				apply_filters(
					'wdm_ldgr_show_report_button_label',
					__( 'Show Report', 'wdm_ld_group' )
				)
			);
			?>
			" />
	</div>
	<?php
	// check if the course has a certificate associated with it.
	$rewards        = false;
	$course_id      = $group_courses[0];
	$certificate_id = learndash_get_setting( $course_id, 'certificate' );
	if ( ! empty( $certificate_id ) && 0 !== $certificate_id ) {
		$rewards = true;
	}
	$rewards = apply_filters( 'wdm_ldgr_report_show_rewards_column', $rewards, $course_id, $group_id );

	ldgr_get_template(
		WDM_LDGR_PLUGIN_DIR . '/modules/templates/ldgr-group-report-table.template.php',
		[
			'course_id' => $course_id,
			'group_id'  => $group_id,
			'rewards'   => $rewards,
		]
	);
	?>
</div>
