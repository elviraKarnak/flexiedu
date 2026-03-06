<?php
/**
 * Team Member Edit modal container.
 *
 * @since 1.0.0
 * @version 2.1.2
 *
 * @package LearnDash\Groups_Plus
 */

?>
<?php $form_id = 'edit_groups_plus_team_member'; ?>
<!-- The Modal for edit team member groups-plus-->
<div id="<?php echo $form_id; ?>_modal" class="groups-plus-modal">
	<!-- Modal content -->
	<div class="groups-plus-modal-content">
		<div class="groups-plus-modal-nav">
			<ul class="tabs">
				<li class="tab-link current">
					<?php printf(
						esc_html__( 'Update %s', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'team_member' )
					); ?>
				</li>
			</ul>
			<span class="groups-plus-close">&times;</span>
		</div>
		<div class="groups-plus-modal-container">
			<p><?php _e( 'Please wait to load...', 'learndash-groups-plus' ); ?></p>
		</div>
	</div>
</div>
