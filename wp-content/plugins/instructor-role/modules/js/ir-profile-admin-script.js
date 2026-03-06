jQuery(function(){
    // Allow sorting to sections
    jQuery('.ir-profile-settings-table tbody').sortable({
        axis: 'y',
        cursor: 'move',
        handle: '.dashicons-sort',
        // placeholder: 'ir-section-placeholder'
    });

    // Edit Section
    jQuery('.ir-profile-settings-table').on('click', '.ir-profile-setting-edit', function(){
        var data_id = jQuery(this).data('id');
        var settings = JSON.parse( jQuery('#ir-profile-section-data-'+data_id).val() );
        
        ir_reset_update_modal();
        jQuery( '#ir-profile-update-save').show();

        if ( settings.title.length ) {
            jQuery('input[name="ir-profile-update-setting[title]"').val(settings.title);
        }
        if ( settings.image.length ) {
            jQuery('select[name="ir-profile-update-setting[image]"').val(settings.image);
        }
        if ( settings.meta_key.length ) {
            jQuery('input[name="ir-profile-update-setting[meta_key]"').val(settings.meta_key);
        }
        if ( settings.data_type.length ) {
            jQuery('select[name="ir-profile-update-setting[data_type]"').val(settings.data_type);
        }
        if ( settings.icon.length ) {
            jQuery('select[name="ir-profile-update-setting[icon]"').val(settings.icon);
        }

        if ('irp-custom' === settings.image ) {
            jQuery('#ir-profile-update-custom-image').show();
            jQuery('#ir-profile-view-img-url').attr('href', settings.custom_image_url);
            jQuery('#ir-profile-view-img-url').show();
        }

        if ('dashicon' === settings.icon) {
            jQuery('#ir-profile-update-custom-dashicon').show();
            jQuery('#ir-profile-update-custom-dashicon').val(settings.custom_dashicon);
        }

        jQuery('#ir-profile-update-id').val(data_id);

        // Show modal
        ir_open_settings_modal();

    });

    // Add new section
    jQuery('#ir-profile-add-setting-section').on('click', function(){
        ir_reset_update_modal();
        jQuery( '#ir-profile-update-add').show();
        jQuery('#ir-profile-update-id').val(jQuery('.ir-profile-settings-row').length);
        // Show modal
        ir_open_settings_modal();
    });

    // Change Section Image
    jQuery('#ir-profile-update-image').on('change', function(){
        var href = '';
        if ( 'irp-custom' === jQuery(this).val() ) {
            href = jQuery('#ir-profile-view-img-url' ).attr('href');
            jQuery('#ir-profile-update-custom-image').show();
            if ( href.length && 'undefined' !== href && '#' !== href) {
                jQuery('#ir-profile-view-img-url').show();
            }
        } else {
            jQuery('#ir-profile-update-custom-image').hide();
            jQuery('#ir-profile-view-img-url').hide();
        }
    });

    // Upload custom image
	var file_frame; // variable for the wp.media file_frame

	// Attach a click event (or whatever you want) to some element on your page
	jQuery('#ir-profile-update-custom-image' ).on( 'click', function( event ) {
		event.preventDefault();

		// if the file_frame has already been created, just reuse it
		if ( file_frame ) {
			file_frame.open();
			return;
		}

		file_frame = wp.media.frames.file_frame = wp.media({
			// title: jQuery( this ).data( 'uploader_title' ),
			// button: {
			// 	text: jQuery( this ).data( 'uploader_button_text' ),
			// },
			multiple: false // set this to true for multiple file selection
		});

		file_frame.on('select', function() {
			attachment = file_frame.state().get('selection').first().toJSON();
			// do something with the file here
			jQuery('input[name="ir-profile-update-setting[custom_image_url]"' ).val( attachment.url );
            jQuery('#ir-profile-view-img-url' ).attr('href', attachment.url);
            jQuery('#ir-profile-view-img-url').show();
		});

        // file_frame.on('open',function() {
        //     var selection = file_frame.state().get('selection');
        //     var selected_image = jQuery('#ir-profile-view-img-url').attr('src');
          
        //     if(selected_image.length > 0) {
        //       var ids = ids_value.split(',');
          
        //       ids.forEach(function(id) {
        //         attachment = wp.media.attachment(id);
        //         attachment.fetch();
        //         selection.add(attachment ? [attachment] : []);
        //       });
        //     }
        // });

		file_frame.open();
	});

    // Change Section Icon
    jQuery('#ir-profile-update-icon').on('change', function(){
        if ( 'dashicon' === jQuery(this).val() ) {
            jQuery('#ir-profile-update-custom-dashicon').show();
        } else {
            jQuery('#ir-profile-update-custom-dashicon').hide();
        }
    });

    // Save section settings
    jQuery('#ir-profile-update-save').on('click', function(){
        var data = ir_get_section_data();
        var data_id = jQuery('#ir-profile-update-id').val();
        jQuery('#ir-profile-section-data-'+data_id).val( JSON.stringify( data ) );
        jQuery('#ir-profile-section-title-'+data_id).html(data.title);
        var is_valid = is_valid_section_data( data );
        jQuery('#ir-profile-section-actions-'+data_id+' span.dashicons-warning').remove();
        if ( ! is_valid ) {
            jQuery('#ir-profile-section-actions-'+data_id).append(ir_loc.warning_span);
        }
        ir_close_settings_modal();
    });

    // Add section
    jQuery('#ir-profile-update-add').on('click', function(){
        var data = ir_get_section_data();
        var data_id = jQuery('#ir-profile-update-id').val();
        ir_add_section(data, data_id);
        var is_valid = is_valid_section_data( data );
        jQuery('#ir-profile-section-actions-'+data_id+' span.dashicons-warning').remove();
        if ( ! is_valid ) {
            jQuery('#ir-profile-section-actions-'+data_id).append(ir_loc.warning_span);
        }
        ir_close_settings_modal();
    });

    // Delete section
    jQuery('.ir-profile-settings-table').on('click', '.ir-profile-delete-section', function(){
        jQuery(this).parents('.ir-profile-settings-row').remove();
    });

    // Validation
    jQuery( '#ir-profile-settings-form' ).on( 'submit', function(event) {
        // If warning then do not submit form and display warning
        if ( jQuery( '.ir-profile-settings-table span.dashicons-warning' ).length ) {
            event.stopPropagation();
            alert(ir_loc.invalid_data_warning_msg);
            return false;
        }
    })

    // Close modal
    jQuery( '#ir-profile-settings-form').on('click', '.close', function(){
        ir_close_settings_modal();
    });

    jQuery('#ir_enable_frontend_dashboard').on('change', function(){
        if ( jQuery(this).prop('checked') ) {
            jQuery('.ir-disable-ld-links').show();
            jQuery('.ir-custom-color-settings').show();
            jQuery('.ir-additional-settings').show();
            jQuery('.ir-setting-title-2').show();
            jQuery('.ir-col-2').show();
            jQuery('.ir-fcc-menu-setting').show();
            jQuery('.ir-fcc-tab-desc-note').show();
        } else {
            jQuery('.ir-custom-color-settings').hide();
            jQuery('.ir-disable-ld-links').hide();
            jQuery('.ir-additional-settings').hide();
            jQuery('.ir-setting-title-2').hide();
            jQuery('.ir-col-2').hide();
            jQuery('.ir-fcc-menu-setting').hide();
            jQuery('.ir-fcc-tab-desc-note').hide();
        }
    });

    jQuery('#ir_enable_sync').on('change', function(){
        if ( jQuery(this).prop('checked') ) {
            jQuery('.ir-fcc-sync').css( {
                        'pointer-events' : 'none',
                        'opacity'       : 0.4,
                    } );
        } else {
            jQuery('.ir-fcc-sync').css( {
                        'pointer-events' : '',
                        'opacity'       : '',
                    } );
        }
    });

    jQuery('#ir_disable_ld_links').on('change', function(){
        if ( jQuery(this).prop('checked') ) {
            jQuery('.ir-col-2 > img').prop('src', ir_fcb_loc.image_url);
        } else {
            jQuery('.ir-col-2 > img').prop('src', ir_fcb_loc.admin_image_url);
        }
    });
});

/**
 * Reset modal form data
 */
function ir_reset_update_modal() {
    jQuery('input[name="ir-profile-update-setting[title]"').val('');
    jQuery('#ir-profile-view-img-url' ).attr('href', '#');
    jQuery('#ir-profile-view-img-url').hide();
    jQuery('#ir-profile-update-custom-image').hide();
    jQuery('#ir-profile-update-custom-dashicon').val('');
    jQuery('#ir-profile-update-custom-dashicon').hide();
    jQuery('select[name="ir-profile-update-setting[image]"').val('-1');
    jQuery('input[name="ir-profile-update-setting[meta_key]"').val('');
    jQuery('select[name="ir-profile-update-setting[data_type]"').val('-1');
    jQuery('select[name="ir-profile-update-setting[icon]"').val('none');
    jQuery('#ir-profile-update-id').val(-1);
    jQuery('.ir-profile-update-button').hide();
}

/**
 * Get the section data
 */
function ir_get_section_data() {
    var title = jQuery('#ir-profile-update-title').val();
    var metakey = jQuery('input[name="ir-profile-update-setting[meta_key]"').val();
    var datatype = jQuery('select[name="ir-profile-update-setting[data_type]"').val();
    var image = jQuery('select[name="ir-profile-update-setting[image]"').val();
    var icon = jQuery('select[name="ir-profile-update-setting[icon]"').val();
    var data = {
        'title': title,
        'image': image,
        'meta_key': metakey,
        'data_type': datatype,
        'icon': icon
    };

    if ('irp-custom' === image ) {
        data.custom_image_url = jQuery('#ir-profile-view-img-url').attr('href');
    }
    if ('dashicon' === icon) {
        data.custom_dashicon = jQuery('#ir-profile-update-custom-dashicon').val();
    }
    return data;
}

/**
 * Add new section to the sections list
 * 
 * @param {array} data Data of the section
 * @param {int} data_id ID of the section.
 */
function ir_add_section(data, data_id) {
    var html = ir_loc.add_section_html;
    html = html.replace( '{title}', data.title );
    while( -1 != html.indexOf('{data_id}')) {
        html = html.replace( '{data_id}', data_id );
    }
    html = html.replace( '{section_data}', JSON.stringify( data ) );

    jQuery('table.ir-profile-settings-table tbody').append(html);
}

/**
 * Validate section details
 *
 * @param {array} data Data of the section
 */
function is_valid_section_data( data ) {
    var is_valid = true;
    // Check if any data empty
    if ( ! data.title.length || ! data.meta_key.length || ! data.data_type.length || '-1' == data.data_type ) {
        is_valid = false;
    }
    return is_valid;
}

/**
 * Open modal to configure settings
 */
function ir_open_settings_modal() {
    jQuery('#ir-profile-add-setting-container').css({
        'opacity': '1',
        'visibility': 'visible'
    });
}

/**
 * Open modal to configure settings
 */
function ir_close_settings_modal() {
    jQuery('#ir-profile-add-setting-container').css({
        'opacity': '0',
        'visibility': 'hidden'
    });
}


function show_redirect_pages(){
    if (!jQuery("#wdm_login_redirect:checkbox").is(":checked")) {
    jQuery(".login-page-row").slideUp();
    } else {  
        jQuery(".login-page-row").slideDown();
      }
}
jQuery(document).ready(show_redirect_pages);
