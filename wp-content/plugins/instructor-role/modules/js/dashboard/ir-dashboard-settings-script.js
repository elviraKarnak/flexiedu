(function ($) {
	$('#ir_dashboard_header').on('change', function () {
		if ('image' === $(this).val()) {
			$('.ir-dashboard-image-field.ir-layout-1').show();
			$('.ir-dashboard-text-field.ir-layout-1').hide();
		} else if ('text' === $(this).val()) {
			$('.ir-dashboard-text-field.ir-layout-1').show();
			$('.ir-dashboard-image-field.ir-layout-1').hide();
		} else {
			$('.ir-dashboard-image-field.ir-layout-1').hide();
			$('.ir-dashboard-text-field.ir-layout-1').hide();
		}
	});

	if ($('.ir-color-picker').length) {
		$('.ir-color-picker').wpColorPicker();
	}

	$('.ir_upload_image').on('click', function (event) {
		event.preventDefault();

		var button = $(this),
			custom_uploader = wp.media({
				title: ir_loc_data.media_title,
				library: {
					type: 'image'
				},
				button: {
					text: ir_loc_data.media_button
				},
				multiple: false
			}).on('select', function () { // it also has "open" and "close" events
				var attachment = custom_uploader.state().get('selection').first().toJSON();
				button.html('<img src="' + attachment.url + '">').next().removeClass('ir-hide').next().val(attachment.id).next().val(attachment.id);
			}).open();
	});

	$('.ir_remove_image').on('click', function (event) {
		event.preventDefault();

		var button = $(this);
		button.next().val(''); // emptying the hidden field
		button.addClass('ir-hide').prev().html('Upload');
	})

	$('.ir-color-scheme .preset').on('click', function () {
		let radio = $(this).find('.preset-radio');
		let layout = $('.ir_dashboard_layout:checked').val();

		if (!radio.prop('checked')) {
			radio.prop('checked', true);
			$('.ir-' + layout + ' .preset-selected').removeClass('preset-selected');
			$(this).addClass('preset-selected');
			if ('layout-2' === layout) {
				let font = $(this).data('font');
				$('p.ir-help-text span').text(font);
			}
		}

		if ('custom' == radio.val()) {
			$('.ir-preset-advanced.ir-' + layout).removeClass('ir-hide');
		} else {
			$('.ir-preset-advanced.ir-' + layout).addClass('ir-hide');
		}
	});

	$('#ir_custom_preset').on('change', function () {
		let checked = $(this).prop('checked');
		let layout = $('.ir_dashboard_layout:checked').val();

		if (checked) {
			$('.ir-preset-advanced.ir-' + layout).removeClass('ir-hide');
		} else {
			$('.ir-preset-advanced.ir' + layout).addClass('ir-hide');

		}
	});

	// Switch color presets on changing layouts.
	$('.ir_dashboard_layout').on('change', function () {
		let layout = $(this).val();

		if ('layout-2' === layout) {
			let preset = $('input[name="ir_color_preset_2"]:checked').val();

			if ( 'undefined' === typeof( preset ) ) {
				preset = 'default';
			}

			$('.ir-logo-settings.ir-layout-1').addClass('ir-hide');
			$('.ir-logo-settings.ir-layout-2').removeClass('ir-hide');
			$('.ir-preset-settings.ir-layout-1').addClass('ir-hide');
			$('.ir-preset-advanced.ir-layout-1').addClass('ir-hide');
			$('.ir-preset-settings.ir-layout-2').removeClass('ir-hide');
			if ('custom' === preset) {
				$('.ir-preset-advanced.ir-layout-2').removeClass('ir-hide');
			}
			$('p.ir-help-text').show();
		} else {
			let preset = $('input[name="ir_color_preset"]:checked').val();

			$('.ir-logo-settings.ir-layout-2').addClass('ir-hide');
			$('.ir-logo-settings.ir-layout-1').removeClass('ir-hide');
			$('.ir-preset-settings.ir-layout-2').addClass('ir-hide');
			$('.ir-preset-advanced.ir-layout-2').addClass('ir-hide');
			$('.ir-preset-settings.ir-layout-1').removeClass('ir-hide');
			if ('custom' === preset) {
				$('.ir-preset-advanced.ir-layout-1').removeClass('ir-hide');
			}
			$('p.ir-help-text').hide();
		}
	});
})(jQuery);
