<?php
/**
 * Team Member Import Modal.
 *
 * @since 1.0.0
 * @version 2.1.2
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore classrrom
 */

?>
<?php $form_id = 'import_groups_plus_team_member'; ?>
<div id="<?php echo $form_id; ?>_modal" class="groups-plus-modal">
	<!-- Modal content -->
	<div class="groups-plus-modal-content">
		<div class="groups-plus-modal-nav">
			<ul class="tabs">
				<li class="link-tab current">
					<?php printf(
						esc_html__( 'Import New %s - Multiple Entries', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'team_members' )
					); ?>
				</li>
			</ul>
			<button class="groups-plus-close import-close">&times;</button>
		</div>
		<div class="groups-plus-modal-container">
			<form action="" method="POST" enctype="multipart/form-data" id="<?php echo $form_id; ?>"
				data-nonce="<?php echo wp_create_nonce( 'learndash-groups-plus-import-team-members' ); ?>">
				<input type="hidden" name="group"
					value="<?php echo get_query_var( 'group_id', false ) != '' ? get_query_var( 'group_id', false ) : $_GET['group']; ?>" />
				<div class="form_message"></div>
				<div class="import_classrrom_description">
					<?php esc_html_e( 'Set up your CSV by following instructions in links below:', 'learndash-groups-plus' ); ?>
				</div>
				<div class="import_classrrom_template">
					<a href="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'src/resources/data/GroupsPlusUsers.csv'; ?>">
						<img src="<?php echo LEARNDASH_GROUPS_PLUS_URL . 'build/images/csv_icon.png'; ?>" /><br />
						<?php esc_html_e( 'Download CSV template', 'learndash-groups-plus' ); ?>
					</a>
				</div>
				<div class="import_classrrom_upload_file form-group">
					<div class="import_classrrom_upload_label">
						<?php esc_html_e( 'Upload File:', 'learndash-groups-plus' ); ?> </div>
					<div class="import_classrrom_upload_csv">
						<div class="upload-import-wrapper">
							<button class="btn_groups_plus"><?php esc_html_e( 'BROWSE', 'learndash-groups-plus' ); ?> <i
									class="fa fa-angle-right"></i></button>
							<input type="file" name="groups_plus_csv" id="groups_plus_file_import" />
						</div>
					</div>
					<div class="import_classrrom_upload_filename">
						<?php esc_html_e( 'File name will appear here', 'learndash-groups-plus' ); ?></div>
				</div>
				<div class="form-action" style="margin-bottom:-20px;">
					<button type="submit" class="btn_groups_plus"
						id="<?php echo $form_id; ?>_btn"><?php esc_html_e( 'SAVE', 'learndash-groups-plus' ); ?><i
							class="fa fa-angle-right"></i></button>
				</div>
			</form>
		</div>
	</div>
</div>
