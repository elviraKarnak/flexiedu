(function ($) {
    $('.ir-welcome-popup-modal').fadeIn(500);

    $('button.modal-button-view-dashboard').on('click', function () {
        event.preventDefault();

        $.ajax({
            url: ir_fd_data.ajax_url,
            type: 'post',
            data: {
                'action': 'complete_dashboard_launch',
                'ir_nonce': ir_fd_data.nonce,
                'close_modal': true
            },
            timeout: 30000,
            success: function (response) {
                //do nothing
            },
            error: function (eventData) {
            },
        });
        $('.ir-welcome-popup-modal').hide();
    });

    $('button.modal-button-resume-setup').on('click', function () {
        event.preventDefault();

        $.ajax({
            url: ir_fd_data.ajax_url,
            type: 'post',
            data: {
                'action': 'complete_dashboard_launch',
                'ir_nonce': ir_fd_data.nonce,
                'close_modal': true
            },
            timeout: 30000,
            success: function (response) {
                //do nothing
            },
            error: function (eventData) {
            },
        });
        $('.ir-welcome-popup-modal').hide();
    });

    $('button.edit_frontend_dashboard_page').on('click', function () {
        event.preventDefault();

        $.ajax({
            url: ir_fd_data.ajax_url,
            type: 'post',
            data: {
                'action': 'edit_page_onboarding',
                'ir_nonce': ir_fd_data.nonce,
                'close_modal': true
            },
            timeout: 30000,
            success: function (response) {
                window.location.href = ir_fd_data.frontend_dashboard_edit_link;
                //do nothing
            },
            error: function (eventData) {
            },
        });
        $('.ir-welcome-popup-modal').hide();
        $('.ir-onboarding-popup-modal').hide();      
    });
})(jQuery)