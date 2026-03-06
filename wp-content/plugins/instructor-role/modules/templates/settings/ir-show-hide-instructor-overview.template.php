<?php
/**
 * Hide show menu template
 *
 * @since 4.3.1
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;
?>
<div>
	<div class="ir-inline-flex align-center ir-primary-color-setting ir-back-settings ir-hide-appearance">
		<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-narrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l4 4" /><path d="M5 12l4 -4" /></svg>
		<span class="">Back</span>
	</div>
	<h1 style="margin-bottom: 8px;">
		<?php esc_html_e( 'Instructor Overview Settings', 'wdm_instructor_role' ); ?>
	</h1>
	<!-- Alternate Approach -->
	<form method="post">
		<span class="title-desc" style="color: #ADB5BD; font-size: 12px;">
			<?php esc_html_e( 'This settings help to hide and show blocks on the instructor overview page.', 'wdm_instructor_role' ); ?>
		</span>
		<table class="ir-overview-action-table">
			<tr>
				<td>
					<label class="wdm-switch">
					<input type="checkbox" name="ir_overview_settings[course_block]" <?php checked( isset( $overview_settings['course_block'] ) ? $overview_settings['course_block'] : '', 'on' ); ?>>
						<span class="wdm-slider round"></span>
					</label>
				</td>
				<td><?php esc_html_e( 'Course block', 'wdm_instructor_role' ); ?></td>
			</tr>
			<tr>
				<td>
					<label class="wdm-switch">
						<input type="checkbox" name="ir_overview_settings[student_block]" <?php checked( isset( $overview_settings['student_block'] ) ? $overview_settings['student_block'] : '', 'on' ); ?>>
						<span class="wdm-slider round"></span>
					</label>
				</td>
				<td><?php esc_html_e( 'Student block', 'wdm_instructor_role' ); ?></td>
			</tr>
			<tr>
				<td>
					<label class="wdm-switch">
						<input type="checkbox" name="ir_overview_settings[product_block]" <?php checked( isset( $overview_settings['product_block'] ) ? $overview_settings['product_block'] : '', 'on' ); ?>>
						<span class="wdm-slider round"></span>
					</label>
				</td>
				<td><?php esc_html_e( 'Products block', 'wdm_instructor_role' ); ?>
					<?php if ( ! wdmCheckWooDependency() || ! class_exists( 'WooCommerce' ) ) : ?>
						<div class="tooltip">
							<span style="color:red;float:right;padding-right:5px;" class="dashicons dashicons-hidden"></span>
							<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'Learndash woocommerce plugin is inactive.', 'wdm_instructor_role' ); ?></a></span>
						</div>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>
					<label class="wdm-switch">
						<input type="checkbox" name="ir_overview_settings[earnings_block]" <?php checked( isset( $overview_settings['earnings_block'] ) ? $overview_settings['earnings_block'] : '', 'on' ); ?>>
						<span class="wdm-slider round"></span>
					</label>
				</td>
				<td><?php esc_html_e( 'Earnings block', 'wdm_instructor_role' ); ?></td>
			</tr>
			<tr>
				<td>
					<label class="wdm-switch">
						<input type="checkbox" name="ir_overview_settings[reports_block]" <?php checked( isset( $overview_settings['reports_block'] ) ? $overview_settings['reports_block'] : '', 'on' ); ?>>
						<span class="wdm-slider round"></span>
					</label>
				</td>
				<td><?php esc_html_e( 'Course Reports block', 'wdm_instructor_role' ); ?></td>
			</tr>
			<tr>
				<td>
					<label class="wdm-switch">
						<input type="checkbox" name="ir_overview_settings[submission_block]" <?php checked( isset( $overview_settings['submission_block'] ) ? $overview_settings['submission_block'] : '', 'on' ); ?>>
						<span class="wdm-slider round"></span>
					</label>
				</td>
				<td><?php esc_html_e( 'Submissions block', 'wdm_instructor_role' ); ?></td>
			</tr>

		</table>
		<div>
				<h3 style="margin-top: 20px;"><?php esc_html_e( 'All Block Hidden Message settings', 'wdm_instructor_role' ); ?></h3>
				<span class="title-desc" style="color: #ADB5BD; font-size: 12px;"><?php esc_html_e( 'Configure the message that will be displayed when all blocks are hidden.', 'wdm_instructor_role' ); ?></span>
					</div>
			<div style="margin-top: 20px;">
				<?php $data = isset( $overview_settings['no_blocks_prompt_message'] ) ? $overview_settings['no_blocks_prompt_message'] : ''; ?>
				<td colspan='2'><input style='width:500px; border: 1px solid #ddd; min-height: 40px; border-radius: 8px; padding: 0 10px'  placeholder='<?php esc_html_e( 'No content to display', 'wdm_instructor_role' ); ?>' type="textarea" name="ir_overview_settings[no_blocks_prompt_message]" value="<?php echo $data; ?>" ></td>
					</div>
		<div style="padding-top:25px;">
					<span><?php submit_button( __( 'Save Settings', 'wdm_instructor_role' ), 'primary ir-primary-btn', 'ir-save-overview-settings', false ); ?></span>
					<span><?php submit_button( __( 'Reset Settings', 'wdm_instructor_role' ), 'secondary ir-btn-outline', 'ir-reset-overview-settings', false ); ?></span>
		</div>

	</form>

</div>

