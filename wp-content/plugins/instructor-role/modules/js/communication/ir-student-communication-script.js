(function($){

    $( document ).on( 'click', '.ir-msg-item', function( event ){
        // Disable message item
        var $this = $(this);
        var doubt_thread_id = $(this).data('doubt-id');

        // Remove unread class for this thread.
        $('.ir-msg-item[data-doubt-id='+doubt_thread_id+']').removeClass('ir-unread-thread');

        // Do not send ajax for header menu actions.
        if ($this.parents('.ir-message-menu').length) {
            return;
        }

        $this.css('pointer-events', 'none');
        // Clear any old thread messages.
        $('.ir-thread-loader').css('display', 'flex');
        // $('.ir-thread .ir-received-msg-wrap').html('');
        $('.ir-thread .ir-msg-new-doubts').remove();

        // Get thread messages
        $.ajax({
            url: ir_communication_loc.ajax_url,
            type: 'post',
            data:{
                action : 'ir_get_doubt_messages',
                post_id : ir_communication_loc.post_id,
                ir_get_doubt_messages_nonce: $('#ir_doubt_thread_nonce_' + doubt_thread_id).val(),
                thread_id: doubt_thread_id,
            },
            dataType: 'json',
            timeout: 10000,
            beforeSend: function(){
                // $('.ir-thread-loader').css('display', 'flex');
            },
            success: function( response ){
                if ( 'success' === response.data.type ) {
                    $('.ir-thread')
                    .addClass('type-thread')
                    .append(response.data.new_message_button_html)
                    .data('thread-id', doubt_thread_id)
                    .find('.ir-received-msg-wrap').html( response.data.thread_history_html);

                    $('.ir-message-doubts button').trigger('click');

                    // Disable lesson, topic and subject fields.
                    $('.ir-lh').prop('disabled', true );
                    $('.ir-th').prop('disabled', true );

                    // Hide caret icons for lesson and topic menus.
                    $('.ir-lh-wrap').addClass('ir-no-dropdown');
                    $('.ir-th-wrap').addClass('ir-no-dropdown');

                    $('.ir-sub').val( response.data.doubt_subject ).prop('readonly', true );

                    // Update unread messages count
                    if ( response.data.read_messages ) {
                        update_unread_messages_count( parseInt( response.data.read_messages ) );
                    }
                    // Scroll to last unread message
                    $last_unread_message = get_last_unread_message( $('.ir-received-msg-item:not(.irc-self'), parseInt( response.data.read_messages ) );
                    $last_unread_message.addClass('ir-last-unread-msg');

                    var myContainer = $('.ir-thread');
                    if($('.ir-last-unread-msg').length){
                        myContainer.animate({
                            scrollTop: $('.ir-last-unread-msg').offset().top - myContainer.offset().top + myContainer.scrollTop()
                        });
                    } else {
                        myContainer.animate({
                            scrollTop: $('.ir-received-msg-item').last().offset().top - myContainer.offset().top + myContainer.scrollTop()
                        });
                    }
                    
                }
                $('.ir-thread-loader').css('display', 'none');
                $this.css('pointer-events', 'initial').removeClass('ir-unread-thread');
            },
            complete: function() {
            }
        });
    });

    $( document ).on( 'change', '.ir-lh', function( event ) {

        var lesson_id = parseInt( $('.ir-lh').val() );
        // Check if placeholder selected.
        if ( 0 == lesson_id ) {
            return;
        }

        // Get list of topics
        $.ajax({
            url: ir_communication_loc.ajax_url,
            type: 'post',
            data:{
                action : 'ir_get_lesson_topics',
                lesson_id: lesson_id,
                course_id: ir_communication_loc.course_id,
                ir_nonce: $('.ir-lh').data('nonce'),
            },
            dataType: 'json',
            timeout: 10000,
            beforeSend: function(){
            },
            success: function( response ){
                if ( 'success' === response.data.type ) {
                    $('.ir-th').html(response.data.topic_dropdown);
                }
            },
            complete: function() {
            }
        })
    });

    $( document ).on( 'heartbeat-send', function( event, data ) {
        data.ir_student_communication_data = {
            'post_id' : ir_communication_loc.post_id,
            'doubt_threads' : ir_communication_loc.doubt_threads,
            'ir_nonce' : ir_communication_loc.nonce,
        };
    });

    $( document ).on( 'heartbeat-tick', function ( event, data ) {
        // Check for our data, and use it.
        if ( ! data.ir_unread_thread_count ) {
            return;
        }
     
        // if ( ! $('.ir-msg-count').length ) {
        //     $('div.ir-question-mark > span').
        // }
    });

    function update_unread_messages_count( marked_read_count ) {
        var old_count = parseInt( $('.ir-question-mark .ir-msg-count').data('count') );
        var new_count = old_count - marked_read_count;

        if ( 0 == new_count ) {
            $('.ir-question-mark .ir-msg-count').remove();
        } else {
            $('.ir-question-mark .ir-msg-count').data('count', new_count).text(new_count);
        }

        var total_old_count = parseInt( $('.ir-message-menu .ir-msg-count').data('count') );
        var total_new_count = total_old_count - marked_read_count;

        if ( 0 == total_new_count ) {
            $('.ir-message-menu .ir-msg-count').remove();
        } else {
            $('.ir-message-menu .ir-msg-count').data('count', new_count).text(new_count);
        }
    }

    function get_last_unread_message( $message_list, read_messages_count ) {
        var length = $message_list.length;
        var index = length - read_messages_count;

        return $message_list.eq(index);
    }
})(jQuery);
