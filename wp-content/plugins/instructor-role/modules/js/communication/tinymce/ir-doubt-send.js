(function($) {
    tinymce.PluginManager.add( 'ir_doubt_send_button', function( editor, url ) {
         // Add Button to Visual Editor Toolbar
        editor.addButton('ir_doubt_send_button', {
            title: ir_communication_loc.send_text,
            text: ir_communication_loc.send_text,
            cmd: 'ir_doubt_send_button_cmd',
            onpostrender: function(){ // cspell:disable-line
                var btn = this;
                btn.$el.addClass('ir-doubt-send-btn');
            }
        });

        // Add command
        editor.addCommand('ir_doubt_send_button_cmd', function(){
            // Get editor object.
            var $editor = tinyMCE.get('ir_communication');

            // Check if new thread or existing thread
            var thread_id = $('.ir-thread').data('thread-id');

            // Disable send button again
            $('.ir-doubt-send-btn').removeClass('ir-valid');


            // Send message
            $.ajax({
                url: ir_communication_loc.ajax_url,
                type: 'post',
                data:{
                    action : 'ir_send_doubt_to_instructor',
                    lesson_id : $('.ir-lh').val(),
                    topic_id : $('.ir-th').val(),
                    subject : $('.ir-sub').val(),
                    post_id : ir_communication_loc.post_id,
                    instructor_id : $('#ir_instructor_id').val(),
                    message : $editor.getContent(),
                    thread_id: thread_id,
                    ir_communication_nonce: $('#ir_communication_nonce').val()
                },
                dataType: 'json',
                timeout: 10000,
                beforeSend: function(){
                    // $('.ir-thread-loader').css('display', 'flex');
                    $('.ir-doubt-send-btn').find('.mce-txt').html(ir_communication_loc.sending_text);
                },
                success: function( response ){
                    // Clear content
                    $editor.setContent('');
                    // Show error/success notification.
                    if ( 'success' === response.data.type ) {
                        $('.ir-msg-toast .ir-msg-sent .ir-type').removeClass('irc-icon-Error-fill').addClass('irc-icon-Correct-fill');
                        $('.ir-msg-toast').removeClass('irc-error').addClass('irc-success');
                    } else {
                        $('.ir-msg-toast .ir-msg-sent .ir-type').removeClass('irc-icon-Correct-fill').addClass('irc-icon-Error-fill');
                        $('.ir-msg-toast').removeClass('irc-success').addClass('irc-error');
                    }
                    $('.ir-msg-toast .ir-msg-sent span').html(response.data.message);
                    $('.ir-msg-toast').show();
                    $('html, body').animate({
                        scrollTop: $(".ir-msg-toast").offset().top  -   200
                    }, 1500);
                    setTimeout(function(){$('.ir-msg-toast').hide();}, 5000);
                },
                complete: function() {
                    // $('.ir-thread-loader').css('display', 'none');
                    $('.ir-doubt-send-btn').find('.mce-txt').html(ir_communication_loc.send_text);
                    $('.ir-close').trigger('click');
                }
            });
        })
    });
})(jQuery);
