(function ($) {
    $('.wrld-custom-popup-modal').fadeIn(500);
    body = document.querySelector("body").style.overflow = "hidden";

    $('.modal-button-later').on('click', function (event) {
        event.preventDefault();
        $.ajax({
            url: ir_new_loc.ajax_url,
            method: 'post',
            dataType: 'json',
            timeout: 30000,
            data: {
                action: 'check_layouts_later',
                nonce: ir_new_loc.nonce
            },
            success: function (response) {
                if ('success' === response.status) {
                    $('.wrld-custom-popup-modal').fadeOut(500);
                    body = document.querySelector("body").style.overflow = "";
                }
            }
        });
    });

    $('.modal-button-configure').on('click', function (event) {
        event.preventDefault();
        $.ajax({
            url: ir_new_loc.ajax_url,
            method: 'post',
            dataType: 'json',
            timeout: 30000,
            data: {
                action: 'fdb_introduced',
                nonce: ir_new_loc.nonce
            },
            success: function (response) {
                if ('success' === response.status) {
                    location.href = response.redirect;
                }
            }
        });
    });

    $('#ir_close_modal').on('click', function( event){
        document.getElementById("wrld-custom-modal").remove();
        $.ajax({
            url: ir_new_loc.ajax_url,
            method: 'post',
            dataType: 'json',
            timeout: 30000,
            data: {
                action: 'new_features_popup_dismissed',
                nonce: ir_new_loc.nonce
            },
            success: function (response) {
                if ('success' === response.status) {
                    $('.wrld-custom-popup-modal').fadeOut(500);
                    body = document.querySelector("body").style.overflow = "";
                }
            }
        }); 
    })
})(jQuery);