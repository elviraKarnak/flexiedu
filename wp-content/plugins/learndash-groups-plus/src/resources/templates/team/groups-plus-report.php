<?php
/**
 * Organization Report view for [learndash_groups_plus].
 *
 * @since 1.0.0
 * @version 2.1.2
 *
 * @package LearnDash\Groups_Plus
 */

use LearnDash\Groups_Plus\Module\Group;

?>

<div class="groups_plus_report_list">
	<h3><?php printf( esc_html__( '%s Report', 'learndash-groups-plus' ), learndash_get_custom_label( 'organization' ) ); ?></h3>
	<div class="div-table-container">
		<div class="div-table groups-plus-report-table"
			data-nonce="<?php echo wp_create_nonce( 'learndash-groups-plus-fetch-team-report' ); ?>"
			data-parent-group-id="<?php _e( Group::$parent_group_id, 'learndash-groups-plus' ); ?>">
			<div class="div-table-row-header">
				<div class="div-table-col" align="left">
					<?php esc_html_e( 'Table is loading...', 'learndash-groups-plus' ); ?>
				</div>
				<div class="div-table-col">&nbsp;</div>
				<div class="div-table-col">&nbsp;</div>
			</div>
		</div>
	</div>
</div>
<?php
	$hide_organization_export_csv_button = get_site_option( 'hide_organization_export_csv_button', 'no' );

if ( $hide_organization_export_csv_button === 'no' ) {
	?>
<div class="div_export_section">
	<a href="" class="btn_groups_plus"
		id="btn_download_report_csv"><?php esc_html_e( 'Export CSV', 'learndash-groups-plus' ); ?> <i
			class="fa fa-angle-right"></i></a>
</div>
<?php } ?>
