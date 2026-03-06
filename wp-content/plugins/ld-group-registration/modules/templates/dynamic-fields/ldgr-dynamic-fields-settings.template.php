<?php
/**
 * Dynamic Fields settings template
 *
 * @since 4.3.1
 * @version 4.3.15
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;

?>
<!-- Heading -->
<h2><?php esc_html_e( 'Custom Form Fields', 'wdm_ld_group' ); ?></h2>
<!-- Main Class DIV -->
<div class="ldgr-settings ldgr-settings-fields">
	<!-- Dynamic Form -->
	<form method="post" id="ldgr-dynamic-fields-settings-form">
		<!-- Container -->
		<div class="addel-container addel-container-dynamic-field">
			<!-- Table Start -->
			<div class="divTable" >

				<!-- Header Start -->
					<div class="divTableRow" style="background:#ccc;">

						<div class="divTableCell">
							<?php
							esc_html_e(
								'Name',
								'wdm_ld_group'
							);
							?>
						</div>

						<div class="divTableCell">
							<?php
							esc_html_e(
								'Type',
								'wdm_ld_group'
							);
							?>
						</div>

						<div class="divTableCell">
							<?php
							esc_html_e(
								'Meta Key',
								'wdm_ld_group'
							);
							?>
							<?php
							$meta_key_tooltip = esc_html__(
								'This key is maintained in the user meta and may be used to display the value for a custom field in other places, such as Profile Pages or Certificates.',
								'wdm_ld_group'
							);
							?>
							<span class="dashicons dashicons-editor-help" title="<?php echo esc_attr( $meta_key_tooltip ); ?>"></span>

						</div>

						<div class="divTableCell">
							<?php
							esc_html_e(
								'Required',
								'wdm_ld_group'
							);
							?>
						</div>

						<div class="divTableCell">
							<?php
							esc_html_e(
								'Freeze First Input',
								'wdm_ld_group'
							);
							?>
							<?php
							$override_tooltip = esc_html__(
								'Once switched on, this custom field will not be replaced if re-entered via any of the registration forms.',
								'wdm_ld_group'
							);
							?>
							<span class="dashicons dashicons-editor-help" title="<?php echo esc_attr( $override_tooltip ); ?>"></span>
						</div>

						<div class="divTableCell">
						<?php
							esc_html_e(
								'Action',
								'wdm_ld_group'
							);
							?>
						</div>

					</div>
					<!-- Header End -->

					<?php
					// If dynamic fields exist go inside the loop to iterate through each of them
					if ( $ldgr_dynamic_fields_setting ) {
						foreach ( $ldgr_dynamic_fields_setting as $single_settings ) {
							?>
								<!-- Addel ele start -->
								<div class="addel-target">
									<!-- Table start -->
									<div class="divTableRow">

										<!-- Name element -->
										<div class="divTableCell">
											<input class="ldgr_dynamic_field_name" type="text" value="<?php echo esc_attr( $single_settings['name'] ); ?>" name="ldgr_dynamic_field[name][]">
										</div>

										<!--  select type field -->
										<div class="divTableCell">
											<select class="ldgr_field_type_select" name="ldgr_dynamic_field[field_type][]">
												<option value="text" <?php echo ( 'text' === $single_settings['field_type'] ) ? 'selected' : ''; ?>><?php esc_html_e( 'Text', 'wdm_ld_group' ); ?></option>
												<option value="number" <?php echo ( 'number' === $single_settings['field_type'] ) ? 'selected' : ''; ?>><?php esc_html_e( 'Number', 'wdm_ld_group' ); ?></option>
												<option value="checkbox" <?php echo ( 'checkbox' === $single_settings['field_type'] ) ? 'selected' : ''; ?>><?php esc_html_e( 'Checkbox', 'wdm_ld_group' ); ?></option>
												<option value="textarea" <?php echo ( 'textarea' === $single_settings['field_type'] ) ? 'selected' : ''; ?>><?php esc_html_e( 'Textarea', 'wdm_ld_group' ); ?></option>
											</select>
										</div>

										<!-- Meta Key text -->
										<div class="divTableCell">
											<input class="ldgr_dynamic_meta_key" type="text" value="<?php echo esc_attr( $single_settings['key'] ); ?>" name="ldgr_dynamic_field[key][]">
										</div>

										<!-- Required Checkbox switch -->
										<div class="divTableCell">
											<label class="wdm-switch" style="position:relative">
												<input type="checkbox" class="ldgr_required" name="ldgr_dynamic_field[required_checkbox][]" value="yes" <?php echo ( 'yes' === $single_settings['required'] ) ? 'checked' : ''; ?>>
												<span class="wdm-slider round"></span>
											</label>
											<input type="hidden" class="ldgr_required_hidden" name="ldgr_dynamic_field[required][]" value=<?php echo ( 'yes' === $single_settings['required'] ) ? 'yes' : 'no'; ?>>
										</div>

										<!-- Override Checkbox switch -->
										<div class="divTableCell">
											<label class="wdm-switch" style="position:relative">
												<input type="checkbox" class="ldgr_override" name="ldgr_dynamic_field[override_checkbox][]" value="yes" <?php echo ( 'yes' === $single_settings['override'] ) ? 'checked' : ''; ?>>
												<span class="wdm-slider round"></span>
											</label>
											<input type="hidden" class="ldgr_override_hidden" name="ldgr_dynamic_field[override][]" value=<?php echo ( 'yes' === $single_settings['override'] ) ? 'yes' : 'no'; ?>>
										</div>

										<!-- Delete action button -->
										<div class="divTableCell">
											<div class="addel-delete"><b><?php esc_html_e( 'Remove', 'wdm_ld_group' ); ?><span class="dashicons dashicons-no"></span></b></div>
										</div>

									</div><!-- Table end -->
								</div><!-- Addel ele end -->
								<?php
						}
					} else {
						// If Dynamic fields are not present this else block will execute to render empty inputs
						?>
								<!-- target element -->
								<div class="addel-target">
									<!-- Table row -->
									<div class="divTableRow">

										<!-- Name Field -->
										<div class="divTableCell">
											<input type="text" name="ldgr_dynamic_field[name][]">
										</div>

										<!-- Select Type field -->
										<div class="divTableCell">
											<select class="ldgr_field_type_select" name="ldgr_dynamic_field[field_type][]">
												<option value="text"><?php esc_html_e( 'Text', 'wdm_ld_group' ); ?></option>
												<option value="number"><?php esc_html_e( 'Number', 'wdm_ld_group' ); ?></option>
												<option value="checkbox"><?php esc_html_e( 'Checkbox', 'wdm_ld_group' ); ?></option>
												<option value="textarea" ><?php esc_html_e( 'Textarea', 'wdm_ld_group' ); ?></option>
											</select>
										</div>

										<!-- Meta Text field -->
										<div class="divTableCell">
											<input type="text" name="ldgr_dynamic_field[key][]">
										</div>

										<!-- Required checkbox switch -->
										<div class="divTableCell">
											<label class="wdm-switch" style="position:relative;">
												<input type="checkbox" class="ldgr_required" name="ldgr_dynamic_field[required_checkbox][]" value="yes">
												<span class="wdm-slider round"></span>
											</label>
											<input type="hidden" class="ldgr_required_hidden" name="ldgr_dynamic_field[required][]" value="no">
										</div>

										<!-- Override checkbox switch -->
										<div class="divTableCell">
											<label class="wdm-switch" style="position:relative;">
												<input type="checkbox" class="ldgr_override" name="ldgr_dynamic_field[override_checkbox][]" value="yes">
												<span class="wdm-slider round"></span>
											</label>
											<input type="hidden" class="ldgr_override_hidden" name="ldgr_dynamic_field[override][]" value="no">
										</div>

										<!-- Action field -->
										<div class="divTableCell">
											<div class="addel-delete"><?php esc_html_e( 'Remove', 'wdm_ld_group' ); ?><b><span class="dashicons dashicons-no"></span></b></div>
										</div>

									</div><!-- Table row End-->

								</div><!-- target ele End-->
							<?php
					}
					?>

			<!-- Add Element button -->
			<button class="addel-add"><?php esc_html_e( 'Add', 'wdm_ld_group' ); ?></button>

		</div><!-- Container End --> <?php esc_html_e( 'Note: Meta key\'s cannot be the same.', 'wdm_ld_group' ); ?>
		<?php wp_nonce_field( 'ldgr_save_dynamic_fields_settings', 'ldgr_nonce' ); ?>
		<?php submit_button( __( 'Save', 'wdm_ld_group' ) ); ?>

	</form> <!-- Dynamic Form End -->

</div><!-- Main Class DIV End -->
<?php
