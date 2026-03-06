jQuery(document).ready(function ($) {
	jQuery('.nav-tab-learndash-lms_page_learndash_lms_addons').parent().hide();

	jQuery('input[name="ldgr_user_redirects"]').on('change', function () {
		var $this = jQuery(this);
		if ($this.prop('checked')) {
			jQuery('.ldgr-user-redirects-settings').slideDown('fast');
		} else {
			jQuery('.ldgr-user-redirects-settings').slideUp('fast');
		}
	});

	jQuery('input[name="ldgr_bulk_discounts"]').on('change', function () {
		var $this = jQuery(this);
		if ($this.prop('checked')) {
			jQuery('.ldgr_bulk_discount_setting_data').slideDown('fast');
		} else {
			jQuery('.ldgr_bulk_discount_setting_data').slideUp('fast');
		}
	});

	jQuery('input[name="ldgr_group_code_enable_recaptcha"]').on(
		'change',
		function () {
			var $this = jQuery(this);
			if ($this.prop('checked')) {
				jQuery('.ldgr-recaptcha-settings').slideDown('fast');
			} else {
				jQuery('.ldgr-recaptcha-settings').slideUp('fast');
			}
		}
	);

	jQuery('input[name="ldgr_logo_enabled"]').on('change', function () {
		var $this = jQuery(this);
		if ($this.prop('checked')) {
			jQuery('.ldgr-group-logo-settings').slideDown('fast');
		} else {
			jQuery('.ldgr-group-logo-settings').slideUp('fast');
		}
	});

	// Upload leader logo
	var file_frame; // variable for the wp.media file_frame

	// attach a click event (or whatever you want) to some element on your page
	$('#ldgr-upload-logo-btn').on('click', function (event) {
		event.preventDefault();

		// if the file_frame has already been created, just reuse it
		if (file_frame) {
			file_frame.open();
			return;
		}

		file_frame = wp.media.frames.file_frame = wp.media({
			// title: $( this ).data( 'uploader_title' ),
			// button: {
			// 	text: $( this ).data( 'uploader_button_text' ),
			// },
			// multiple: false // set this to true for multiple file selection
		});

		file_frame.on('select', function () {
			attachment = file_frame.state().get('selection').first().toJSON();

			// do something with the file here
			$('#ldgr-leader-logo-url').val(attachment.url);
			$('#ldgr-leader-logo-image').attr('src', attachment.url);
		});

		file_frame.open();
	});

	// Toggle between option to display message or redirect on successful group code enrollment.
	jQuery('input[name="ldgr_group_code_redirect"]').on('change', function () {
		var $this = jQuery(this);
		if ($this.prop('checked')) {
			jQuery('.ldgr-enrollment-message-div').hide().addClass('ldgr-hide');
			jQuery('.ldgr-enrollment-redirect-tip')
				.hide()
				.addClass('ldgr-hide');
			jQuery('.ldgr-enrollment-redirect-div')
				.fadeIn('slow')
				.removeClass('ldgr-hide');
			jQuery('.ldgr-enrollment-message-tip')
				.fadeIn('slow')
				.removeClass('ldgr-hide');
		} else {
			jQuery('.ldgr-enrollment-redirect-div')
				.hide()
				.addClass('ldgr-hide');
			jQuery('.ldgr-enrollment-message-tip').hide().addClass('ldgr-hide');
			jQuery('.ldgr-enrollment-message-div')
				.fadeIn('slow')
				.removeClass('ldgr-hide');
			jQuery('.ldgr-enrollment-redirect-tip')
				.fadeIn('slow')
				.removeClass('ldgr-hide');
		}
	});

	jQuery('input[name="ldgr_enable_gdpr"]').on('change', function () {
		var $this = jQuery(this);
		if ($this.prop('checked')) {
			jQuery('.ldgr-gdpr-checkbox-div').slideDown('fast');
		} else {
			jQuery('.ldgr-gdpr-checkbox-div').slideUp('fast');
		}
	});

	if (jQuery('.addel-container').length) {
		jQuery('.addel-container').addel({
			events: {
				added: function (event) {
					//reset the radio buttons and select after adding new row
					var rows = document.getElementsByClassName('addel-target');
					rows[
						rows.length - 1
					].childNodes[3].childNodes[7].childNodes[1].options[0].selected =
						true;
					rows[
						rows.length - 1
					].childNodes[3].childNodes[15].childNodes[1].firstElementChild.checked =
						false;
					rows[
						rows.length - 1
					].childNodes[3].childNodes[19].childNodes[1].firstElementChild.checked =
						false;
					event.preventDefault();
				},
			},
		});
	}

	jQuery('.ldgr_bulk_discount_table').on(
		'change',
		'.ldgr_bulk_discount_value_validate',
		function () {
			var $this = jQuery(this);
			var changedValue = $this.val();
			var count = 0;
			jQuery('.ldgr_bulk_discount_value_validate').each(
				function (i, obj) {
					if (changedValue == jQuery(this).val()) {
						count++;
					}
				}
			);
			if (count >= 2) {
				jQuery('.ldgr_duplicate_row_rule_error').show();
			} else {
				jQuery('.ldgr_duplicate_row_rule_error').hide();
			}
		}
	);

	//Required addel class to add dynamic elements through addel
	jQuery('.addel-add').on('click', function (e) {
		e.preventDefault();
	});

	// Confirmation popup for removing dynamic field option in the dynamic setting page
	jQuery('.addel-delete').on('click', function (e) {
		e.preventDefault();
	});

	//Meta Override JQuery value handle
	jQuery('body').on('click', '.ldgr_override', function () {
		if (jQuery(this).is(':checked')) {
			jQuery(this)
				.closest('.divTableCell')
				.find('.ldgr_override_hidden')
				.val('yes');
		} else {
			jQuery(this)
				.closest('.divTableCell')
				.find('.ldgr_override_hidden')
				.val('no');
		}
	});

	//Meta Required JQuery value handle
	jQuery('body').on('click', '.ldgr_required', function () {
		if (jQuery(this).is(':checked')) {
			jQuery(this)
				.closest('.divTableCell')
				.find('.ldgr_required_hidden')
				.val('yes');
		} else {
			jQuery(this)
				.closest('.divTableCell')
				.find('.ldgr_required_hidden')
				.val('no');
		}
	});

	//Dynamic form data handling
	jQuery('#ldgr-dynamic-fields-settings-form').submit(function (e) {
		final_obj = {};
		count = 0;
		jQuery('.ldgr_field_type_select').each(function () {
			data = {};
			data.key = [];
			data.value = [];
			final_obj[count] = data;
			count++;
		});
		$option_data = JSON.stringify(final_obj);
		jQuery(this).append(
			"<input type='hidden' name='ldgr_dynamic_field[options]' value='" +
				$option_data +
				"' /> "
		);
		return true;
	});

	//Extra JS

	if (jQuery('#ldgr_dashboard_banner_color').length) {
		jQuery('#ldgr_dashboard_banner_color').wpColorPicker();
	}

	if (jQuery('#ldgr_dashboard_accent_color').length) {
		jQuery('#ldgr_dashboard_accent_color').wpColorPicker();
	}

	if (jQuery('#ldgr_dashboard_footer_color').length) {
		jQuery('#ldgr_dashboard_footer_color').wpColorPicker();
	}

	jQuery('.ldgr_upload_image').on('click', function (event) {
		event.preventDefault();

		var button = jQuery(this),
			custom_uploader = wp
				.media({
					title: 'Insert image',
					library: {
						// uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
						type: 'image',
					},
					button: {
						text: 'Use this image', // button label text
					},
					multiple: false,
				})
				.on('select', function () {
					// it also has "open" and "close" events
					var attachment = custom_uploader
						.state()
						.get('selection')
						.first()
						.toJSON();
					button
						.html('<img src="' + attachment.url + '">')
						.next()
						.removeClass('ldgr-hide')
						.next()
						.val(attachment.id)
						.next()
						.val(attachment.id);
				})
				.open();
	});

	jQuery('.ldgr_remove_image').on('click', function (event) {
		event.preventDefault();

		var button = $(this);
		button.next().val(''); // emptying the hidden field
		button.addClass('ldgr-hide').prev().html('Upload');
	});

	jQuery('form[name="frm_ld_gr_setting"]').on('submit', function (event) {
		let flag = true;
		jQuery('table.ldgr_bulk_discount_table tr.addel-target').each(
			function (ind, obj) {
				let quantity = jQuery(obj).find('td input').first().val();
				let value = jQuery(obj).find('td input').last().val();

				// If both not set, continue.
				if (!quantity && !value) {
					return;
				}

				// If either set, set flag false.
				if (!(quantity && value)) {
					flag = false;
				}
			}
		);

		if (!flag) {
			alert(ldgr_loc.invalid_discount_setting_msg);
			event.preventDefault();
		}
	});
});
