// cspell:ignore irCommunication

export default class irCommunication{

	constructor(msg_received){
		this.msg_received = msg_received;
	}

	toggleMaximize(){
		jQuery(document).on('click', '.ir-maximize', function(){
			jQuery(this).parents('.ir-msg-box').toggleClass('ir-full-screen');
			jQuery(this).parents('.ir-msg-box').removeClass('ir-minimized-box');
		})
	}

	closeElement(triggerElement, element){
		var self = this;
		jQuery(document).on('click', triggerElement, function(){
			if(triggerElement == '.ir-close'){
				self.reset();
			}
			jQuery(element).hide();
		})
		
	}

	reset(){
		jQuery('.ir-msg-box').removeClass('ir-minimized-box');
		jQuery('.ir-msg-box').removeClass('ir-full-screen');

		// Clear any old thread messages.
		jQuery('.ir-thread .ir-received-msg-wrap').html('');
		jQuery('.ir-thread .ir-msg-new-doubts').remove();
		jQuery('.ir-thread').removeClass('type-thread').data('thread-id', 0);

		// Enable lesson and topic fields
		jQuery('.ir-lh').prop('disabled', false );
		jQuery('.ir-th').prop('disabled', false );

		// Show caret icons for lesson and topic menus.
		jQuery('.ir-lh-wrap').removeClass('ir-no-dropdown');
		jQuery('.ir-th-wrap').removeClass('ir-no-dropdown');

		jQuery('.ir-sub').prop('readonly', false ).val('');
		// jQuery('.ir-thread .ir-msg-new-doubts').remove();
	}

	showElement(triggerElement, element, display, msg_data){
		var self = this;
		jQuery(document).on('click', triggerElement, function(){
			if(triggerElement == '.ir-question-mark'){
				if(msg_data.msg_received){
					jQuery('.ir-ask-doubts + .ir-message-notification').css('display', 'block');
				}
				else{
					jQuery(element).css('display', display);
				}
			}
			else{
				jQuery(element).css('display', display);
			}

			if ( triggerElement == '.ir-msg-new-doubts button' ) {
				self.reset();
			}
		})
	}

	toggleMinimize(){
		jQuery(document).on('click', '.ir-minimize', function(){
			jQuery(this).parents('.ir-msg-box').toggleClass('ir-minimized-box');
			jQuery(this).parents('.ir-msg-box').removeClass('ir-full-screen');
		})
	}

	showMsgPopup(msg_data){
		jQuery(document).ready(function(){
			if (!msg_data.msg_received){
				setTimeout(function(){
					if(jQuery('.ir-ask-doubts').attr('style')){
						
					}
					else{
						jQuery('.ir-ask-doubts').css('display', 'block');
					}
				}, 20000);	
			}
		})
	}

	disableButton(){
		jQuery(window).on('load', function(){
			jQuery('#ir_communication_ifr').contents().find('#tinymce').on('keyup', function() {
				var $editor = tinyMCE.get('ir_communication');
				// var empty = jQuery('#ir_communication_ifr').contents().find('[data-mce-bogus]');
				if( '' == $editor.getContent({format:'text'}).trim() ){
				  	jQuery('.ir-doubt-send-btn').removeClass('ir-valid');
				}
				else{
				  	jQuery('.ir-doubt-send-btn').addClass('ir-valid');
				}
			});
		});
	}
}
