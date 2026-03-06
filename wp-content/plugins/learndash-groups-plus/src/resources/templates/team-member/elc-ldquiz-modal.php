<?php
/**
 * Quiz Notification Modal.
 *
 * @since 1.0.0
 * @version 2.1.2
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore ldquiz
 */

?>
<?php $form_id = 'elc_ldquiz_notification'; ?>
<!-- The Modal for quiz modal groups-plus-->
<div id="<?php echo $form_id; ?>_modal" class="groups-plus-modal">
	<!-- Modal content -->
	<div class="groups-plus-modal-content">
		<div class="groups-plus-modal-header text-center"><?php esc_html_e( 'Quiz Notification', 'learndash-groups-plus' ); ?>
			<span class="groups-plus-close">&times;</span>
		</div>
		<div class="groups-plus-modal-container">
			<p><?php _e( 'Please wait to load...', 'learndash-groups-plus' ); ?></p>
		</div>
	</div>
</div>
