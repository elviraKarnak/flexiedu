/**
 * @deprecated 1.8.2 This file is no longer in use.
 */
jQuery(document).ready(function () {
    jQuery("#send_data").change(function () {
        var checkStatus = jQuery("#send_data").is(':checked');
        if ( checkStatus ) {
            checkStatus = 'yes';
        } else {
            checkStatus = 'no';
        }
        var data = {
            'action': 'save_send_data', //Action to store quotation in database
            'checkStatus': checkStatus,
        };

        jQuery.post(license_data.ajax_url, data, function ( response ) {

        });
    });
})
