<?php
/**
 * Student Communication Settings Template
 *
 * @since 3.6.0
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<div class="wrap">
	<h2><?php esc_html_e( 'Student Communication Settings', 'wdm_instructor_role' ); ?></h2>

	<form method="post" id="ir-student-communication-settings-form">
		<div class="ir-row">
			<div class="ir-column">
				<table class="form-table">
					<tbody>
					<tr>
						<th scope="row">
							<label for="ir_st_comm_editor_accent_color">
								<?php esc_html_e( 'Accent Color', 'wdm_instructor_role' ); ?>
							</label>
						</th>
						<td>
						<input type="text" name="ir_st_comm_editor_accent_color" id="ir_st_comm_editor_accent_color" value="<?php echo esc_attr( $ir_st_comm_editor_accent_color ); ?>" data-default-color="#00ACD3" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="ir_st_comm_editor_set_button">
								<?php esc_html_e( 'Set button text', 'wdm_instructor_role' ); ?>
							</label>
						</th>
						<td>
						<input placeholder="<?php esc_html_e( 'send your doubts', 'wdm_instructor_role' ); ?>" type="text" name="ir_st_comm_editor_set_button" id="ir_st_comm_editor_set_button" value="<?php echo esc_attr( $ir_st_comm_editor_set_button ); ?>" data-default-color="#00ACD3" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="ir_st_comm_editor_set_popup">
								<?php esc_html_e( 'Set popup text', 'wdm_instructor_role' ); ?>
							</label>
						</th>
						<td>
							<input placeholder="<?php esc_html_e( 'If you have any doubts, feel free to message me.', 'wdm_instructor_role' ); ?>" type="text" name="ir_st_comm_editor_set_popup" id="ir_st_comm_editor_set_popup" value="<?php echo esc_attr( $ir_st_comm_editor_set_popup ); ?>" data-default-color="#00ACD3" />
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			<div style="width:10% !important" class="ir-column">
				<img src="<?php echo plugins_url( 'instructor-role/modules/images/student_teacher_screenshot.png' ); ?>" id="zoom-ir-img"/>
			</div>
		</div>
		<input id="ir_student_communication_settings_save" type="submit" class="button button-primary" name="ir_student_communication_settings_save" value="<?php esc_html_e( 'Save', 'wdm_instructor_role' ); ?>">
		<?php wp_nonce_field( 'ir_stu_com_settings_nonce', 'ir_nonce' ); ?>
	</form>
</div>
