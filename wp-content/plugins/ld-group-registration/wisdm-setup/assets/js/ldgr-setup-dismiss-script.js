jQuery(function(){
    jQuery( 'div.ldgr-setup-wizard-notice').on('click', 'button', function(){
        jQuery.ajax({
            url: ldgr_setup_loc.ajax_url,
            method: 'post',
            dataType: 'json',
            data: {
                action: 'ldgr-setup-wizard-dismiss',
                nonce: jQuery('#ldgr_wizard_nonce').val()
            },
            success: function( response ) {
            }
        });
    });
});
