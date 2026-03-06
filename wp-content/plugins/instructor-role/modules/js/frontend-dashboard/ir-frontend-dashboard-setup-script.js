(function ($) {
    if (ir_setup_data.launch_popup_html && ! ir_setup_data.is_launch_complete ) {
        if (document.getElementsByClassName("ir-notice").length == 0) {
            (function (wp) {
                wp.data.dispatch('core/notices').createNotice(
                    'info', // Can be one of: success, info, warning, error.
                    '<div class="ir-notice"><span>' + ir_setup_data.launch_popup_html + '</div>',
                    {
                        __unstableHTML: true,
                        isDismissible: true, // Whether the user can dismiss the notice.
                    }
                );

                const { isSavingPost } = wp.data.select('core/editor');
                if (typeof isSavingPost !== 'undefined') {
                    var checked = true, checked2 = true; // Start in a checked state.
                    wp.data.subscribe(() => {
                        if (isSavingPost()) {
                            checked = false;
                        } else {
                            if (!checked) {
                                if ('publish' == wp.data.select('core/editor').getEditedPostAttribute('status')) {
                                    window.location.href = wp.data.select('core/editor').getEditedPostAttribute('link') + '?action=ir_launch_setup';
                                }
                                checked = true;
                            }

                        }
                    });
                }

            })(window.wp);
        }
    }

})(jQuery);