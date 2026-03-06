var el                   = wp.element.createElement;
var __                   = wp.i18n.__;
var registerPlugin       = wp.plugins.registerPlugin;
var PluginPostStatusInfo = wp.editPost.PluginPostStatusInfo;
var Button               = wp.components.Button;

registerPlugin(
	'instructor-role',
	{
		render: add_button_to_approve
	}
);

function add_button_to_approve() {
	return el(
		PluginPostStatusInfo,
		{
			className: 'approve_instructor_update'
			},
		el(
			Button,
			{
				isDefault: true,
				target: '#',
				onClick: alert_to_approve
				},
			test_object.button_text
		)
	);
}
function alert_to_approve(event)
{
	var txt;
	var r = confirm( test_object.confirmation_message );
	if (r == true) {
		send_ajax();
	}
}
function send_ajax()
{
	jQuery.ajax(
		{
			url: test_object.ajax_url,
			type: 'POST',
			data: {"action":"approve_instructor_update_ajax", "post_id":test_object.post_id},
			success: function(response, textStatus, jqXHR) {
				if (response == 'approved') {
					jQuery( "div.approve_instructor_update" ).remove();
					alert( test_object.successfull_message ); // cspell:disable-line
					wp.data.dispatch('core/editor').unlockPostSaving();
				} else {
					alert( response );
				}
			}
		}
	);
}
( function( wp ) {
	jQuery( '#editor' ).on( 'click', '.editor-post-publish-button', function() {
	// jQuery( '.editor-post-publish-button').on('click', function(){
		var post_lock = jQuery('.editor-post-publish-button').attr('aria-disabled');

		if ( 'true' === post_lock ) {
			alert( test_object.request_approval_message );
		}
	});

    if ( test_object.post_id.length ) {
		wp.data.dispatch('core/editor').lockPostSaving();
	}

} )( window.wp );
