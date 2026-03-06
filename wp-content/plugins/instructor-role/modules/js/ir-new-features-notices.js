(function ($) {
    $('.ir-admin-notice-container-actions .dashicons-no-alt').on('click', function(){
        $(this).parent().parent().fadeOut(300);
    });
    $('.ir-admin-notice-button').on('click', function (event) {
        event.preventDefault();
        $.ajax({
            url: ir_new_loc.ajax_url,
            method: 'post',
            dataType: 'json',
            timeout: 30000,
            data: {
                action: 'new_layouts_suggested',
                nonce: ir_new_loc.nonce
            },
            success: function (response) {
                if ('success' === response.status) {
                    location.href = response.redirect;
                }
            }
        });
    });
})(jQuery);