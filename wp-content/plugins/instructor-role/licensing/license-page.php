<?php
/**
 * This file is template for licensing page.
 * 
 * @deprecated 5.9.1 This file is no longer in use.
 *
 * @package wisdmlabs-licensing
 */

?>
<div class="wrap">
	<h2>
		<?php echo __( 'WisdmLabs License Options', $this->plugin_text_domain ); ?></h2>

	<form method="post" action="">
		<table class="wdm-license-table">
			<tbody>
				<tr>
					<td class="product-name-head">Product Name</td>
					<td class="license-key-head">License Key</td>
					<td class="license-status-head">License Status</td>
					<td class="actions-head">Actions</td>
				</tr>
				<!-- Text field to enter license key -->
				<?php do_action( 'wdm_display_licensing_options' ); ?>
			</tbody>
		</table>
	</form>
	<div style="margin: 15px 0px;">
		<div style="margin: 24px 0; border: 1px solid #eee; padding: 20px; border-radius: 4px; overflow: hidden;background-color: #ffffff;">
			<?php
			$checked        = '';
			$current_status = get_option( 'edd_license_send_data_status' );
			if ( 'yes' === $current_status ) {
				$checked = 'checked';
			}
			?>
			<input id="send_data" type="checkbox" name="send_data" value="yes" <?php echo $checked; ?>>
			<label for="send_data"><?php _e( 'Allow WisdmLabs to collect non-sensitive diagnostic data and usage information.', $this->plugin_text_domain ); ?></label>
			<p>
				<?php echo \Licensing\WdmSendDataToServer::get_data_tracking_message( 'page' ); ?>
			</p>

		</div>
	</div>
</div>
