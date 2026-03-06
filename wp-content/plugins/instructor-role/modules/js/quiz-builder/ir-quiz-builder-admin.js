(function ($) {
    $(function () {
        $(".ir-fqb-create-new-quiz").on('click', function () {
            $(this).prop('disabled', true);
            $(this).find('.dashicons-update').css('display', 'inline-block');
            $.ajax({
                url: ir_fqb_loc.ajax_url,
                method: 'POST',
                data: {
                    action: "ir_fqb_new_quiz",
                    nonce: ir_fqb_loc.nonce
                },
                dataType: 'JSON',
                timeout: 30000,
                success: function (response) {
                    if ('success' === response.status) {
                        $('#ir-fqb-create-new-quiz .dashicons-update').hide();
                        window.location.replace(response.quiz_url);
                    } else {
                        console.log(response);
                    }
                },
                complete: function () {
                    $(this).prop('disabled', false);
                    $(this).find('.dashicons-update').hide();
                }
            })
        })
    });
})(jQuery)

