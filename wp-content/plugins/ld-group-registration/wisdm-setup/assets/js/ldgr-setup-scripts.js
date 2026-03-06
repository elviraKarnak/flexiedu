jQuery(function(){
    jQuery('#ldgr_user_redirects').on('change', function(){
        if ( jQuery(this).prop('checked') ) {
            jQuery('.ldgr-user-redirects-settings').removeClass('hide');
        } else {
            jQuery('.ldgr-user-redirects-settings').addClass('hide');
        }
    });
});