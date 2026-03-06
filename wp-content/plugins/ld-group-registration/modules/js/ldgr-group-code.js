// cspell:ignore cngc

jQuery(document).ready(function () {
	// From date datepicker
	var group_code_from = jQuery('.ldgr-code-date-range-from').datepicker({
		dateFormat: 'dd-mm-yy',
		minDate: 0,
	});

	// To date datepicker
	jQuery('.ldgr-code-date-range-to').datepicker({
		dateFormat: 'dd-mm-yy',
		minDate: 0,
	});

	// Group code validation settings
	jQuery('.ldgr-validate-group-code .empty-bg').on(
		'checkboxToggle',
		function () {
			var $form = jQuery(this).parents('.ldgr-form');
			if (jQuery(this).parent().hasClass('enabled')) {
				$form.find('.ldgr-validation-rules').slideDown();
				$form.find('.ldgr-code-validation-check').val('on');
			} else {
				$form.find('.ldgr-validation-rules').slideUp();
				$form.find('.ldgr-code-validation-check').val('off');
			}
		}
	);
	// Group code validation settings
	jQuery('.ldgr-code-validation-check').on('change', function () {
		if (jQuery(this).prop('checked')) {
			jQuery('.ldgr-code-validation').slideDown();
		} else {
			jQuery('.ldgr-code-validation').slideUp();
		}
	});

	// Submit form actions.
	jQuery('.ldgr-submit-form').on('click', function () {
		var $form = jQuery(this).parents('.ldgr-form');
		$form.trigger('submit');
	});

	// Create group code form
	jQuery('#ldgr-group-code-create-form').on('submit', function (event) {
		event.preventDefault();
		var $black_screen = jQuery('.ldgr-black-screen');
		jQuery.ajax({
			url: group_code_loc.ajax_url,
			method: 'post',
			dataType: 'json',
			data: {
				action: 'ldgr-create-group-code',
				nonce: jQuery('#ldgr_nonce').val(),
				form: jQuery(this).serialize(),
			},
			beforeSend: function () {
				$black_screen.css('display', 'flex');
			},
			complete: function () {
				$black_screen.css('display', 'none');
			},
			success: function (response) {
				jQuery('.ldgr-group-code-messages .ldgr-message-text').html(
					response.msg
				);

				if ('error' == response.type) {
					jQuery('.ldgr-group-code-messages')
						.removeClass('success')
						.addClass('error')
						.show();
				} else {
					jQuery('.ldgr-group-code-messages')
						.removeClass('error')
						.addClass('success')
						.show();

					if (
						jQuery('.ldgr-group-code-items .ldgr-group-code-item')
							.length
					) {
						jQuery('.ldgr-group-code-items .ldgr-group-code-item')
							.first()
							.before(response.row_html);
					} else {
						jQuery(
							'.ldgr-group-code-items .ldgr-no-group-codes'
						).remove();
						jQuery('.ldgr-group-code-items').append(
							response.row_html
						);
					}
					jQuery('#ldgr-group-code-create-form')
						.find('.gcs-cancel')
						.trigger('click');
				}
				jQuery('html, body').animate({
					scrollTop: jQuery('.ldgr-group-code-tab').offset().top - 20,
				});
				setTimeout(() => {
					ldgr_close_message();
				}, 3000);
			},
		});
	});

	jQuery('.ldgr-cngc-btn').on('click', function () {
		// Reset form data
		var $group_code_create_form = jQuery('#ldgr-group-code-create-form');
		ldgr_reset_form($group_code_create_form);
	});
	// Close messages
	jQuery('.ldgr-message-close').on('click', function () {
		ldgr_close_message();
	});

	// Generate random group code
	jQuery('.ldgr-gen-group-code .empty-bg').on('checkboxToggle', function () {
		if (jQuery(this).parent().hasClass('enabled')) {
			var $form = jQuery(this).parents('.ldgr-form');
			var $loading = $form.find('.dashicons-update');
			var $code_string = $form.find('.ldgr-code-string');
			jQuery.ajax({
				url: group_code_loc.ajax_url,
				type: 'post',
				dataType: 'json',
				data: {
					action: 'ldgr-generate-group-code',
				},
				beforeSend: function () {
					$loading.addClass('spin').css('opacity', 1);
					$code_string.attr('readonly', true);
				},
				success: function (response) {
					if ('success' == response.type) {
						$code_string.val(response.unique_id);
					} else {
						jQuery(
							'.ldgr-group-code-messages .ldgr-message-text'
						).html(response.msg);
						jQuery('.ldgr-group-code-messages')
							.removeClass('success')
							.addClass('error')
							.show();
						setTimeout(() => {
							ldgr_close_message();
						}, 3000);
					}
				},
				complete: function () {
					$loading.removeClass('spin').css('opacity', 0);
					$code_string.attr('readonly', false);
				},
			});
		}
	});

	// Group code status toggle
	jQuery('.ldgr-group-code-items').on(
		'checkboxToggle',
		'.ldgr-gr-code-status-wrap .ldgr-toggle-wrap .empty-bg',
		function () {
			var checked = false;
			if (jQuery(this).parent().hasClass('enabled')) {
				checked = true;
			}
			var $switch = jQuery(this).parent();
			var $loading = $switch.siblings('.dashicons-update');
			var $input = $switch.siblings('.ldgr-code-status-toggle');
			jQuery.ajax({
				url: group_code_loc.ajax_url,
				method: 'post',
				dataType: 'json',
				data: {
					action: 'ldgr-group-code-status-toggle',
					group_code: $input.data('id'),
					nonce: $input.data('nonce'),
					checked: checked,
				},
				beforeSend: function () {
					$loading.addClass('spin').css('opacity', 1);
					$switch.addClass('ldgr-disable');
				},
				success: function (response) {
					if ('error' == response.type) {
						jQuery(
							'.ldgr-group-code-messages .ldgr-message-text'
						).html(response.msg);
						jQuery('.ldgr-group-code-messages')
							.removeClass('success')
							.addClass('error')
							.show();
						jQuery('html, body').animate({
							scrollTop:
								jQuery('.ldgr-group-code-tab').offset().top -
								20,
						});
						setTimeout(() => {
							ldgr_close_message();
						}, 3000);
					}
				},
				complete: function () {
					$loading.css('opacity', 0).removeClass('spin');
					$switch.removeClass('ldgr-disable');
				},
			});
		}
	);

	/* jQuery('#ldgr-group-code-table').on('change', '.ldgr-code-status-toggle', function() {
        var checked = jQuery(this).prop('checked');
        var $switch = jQuery(this).parents('.ldgr-switch');
        var $loading = $switch.siblings('.dashicons-update');
        jQuery.ajax({
            url: group_code_loc.ajax_url,
            method: 'post',
            dataType: 'json',
            data: {
                action: 'ldgr-group-code-status-toggle',
                group_code: jQuery(this).data('id'),
                nonce: jQuery(this).data('nonce'),
                checked: checked
            },
            beforeSend: function() {
                $loading.addClass('spin').css('opacity', 1);
                $switch.addClass('ldgr-disable');
            },
            success: function( response ) {
                if ('error' == response.type) {
                    jQuery('.ldgr-group-code-messages .ldgr-message-text').html( response.msg );
                    jQuery('.ldgr-group-code-messages' ).removeClass( 'success' ).addClass( 'error' ).show();
                    jQuery('html, body').animate({
                        scrollTop: jQuery('.ldgr-group-code-tab').offset().top - 20
                    });
                    setTimeout(() => {
                        ldgr_close_message();
                    }, 3000);
                }
            },
            complete: function() {
                $loading.css('opacity', 0).removeClass('spin');
                $switch.removeClass('ldgr-disable');
            }
        });
    }); */

	// Group code edit
	jQuery('.ldgr-group-code-items').on(
		'click',
		'.ldgr-edit-code',
		function () {
			// Reset form data
			var $group_code_edit_form = jQuery('#ldgr-group-code-edit-form');
			ldgr_reset_form($group_code_edit_form);

			// Fetch group code details via ajax
			var nonce = jQuery(this).data('nonce');
			var group_code_id = jQuery(this).data('id');

			// Set group code id
			jQuery('#ldgr-edit-group-code-id').val(group_code_id);

			var $black_screen = jQuery('.ldgr-black-screen');
			jQuery.ajax({
				url: group_code_loc.ajax_url,
				method: 'post',
				dataType: 'json',
				data: {
					action: 'ldgr-fetch-group-code-details',
					group_code: group_code_id,
					nonce: nonce,
				},
				beforeSend: function () {
					$black_screen.css('display', 'flex');
				},
				success: function (response) {
					if ('error' == response.type) {
						jQuery(
							'.ldgr-group-code-messages .ldgr-message-text'
						).html(response.msg);
						jQuery('.ldgr-group-code-messages')
							.removeClass('success')
							.addClass('error')
							.show();
						jQuery('html, body').animate({
							scrollTop:
								jQuery('.ldgr-group-code-tab').offset().top -
								20,
						});
						setTimeout(() => {
							ldgr_close_message();
						}, 3000);
					} else {
						$group_code_edit_form
							.find('.ldgr-code-string')
							.val(response.data.title);
						$group_code_edit_form
							.find('.ldgr-code-date-range-from')
							.val(response.data.date_from);
						$group_code_edit_form
							.find('.ldgr-code-date-range-to')
							.val(response.data.date_to);
						$group_code_edit_form
							.find('.ldgr-code-limit')
							.val(response.data.enrollment_count);
						$group_code_edit_form
							.find('.ldgr-code-groups')
							.val(response.data.related_group);
						$group_code_edit_form
							.find('.ldgr-code-enrolled-users-count')
							.text(response.data.enrolled_users_count);

						if ('on' == response.data.validation_check) {
							$group_code_edit_form
								.find('.ldgr-code-validation-check')
								.val('on');
							$group_code_edit_form
								.find('.ldgr-validate-group-code')
								.addClass('enabled');
							$group_code_edit_form
								.find('.ldgr-code-ip-validation')
								.val(response.data.validation_ip);
							$group_code_edit_form
								.find('.ldgr-code-domain-validation')
								.val(response.data.validation_domain);
							$group_code_edit_form
								.find('.ldgr-validation-rules')
								.slideDown();
						}

						if (response.data.status) {
							$group_code_edit_form
								.find('.ldgr-code-status')
								.val('on');
						}
					}
				},
				complete: function () {
					$black_screen.css('display', 'none');
				},
			});
		}
	);

	// Edit group code form
	jQuery('#ldgr-group-code-edit-form').on('submit', function (event) {
		var $black_screen = jQuery('.ldgr-black-screen');
		event.preventDefault();
		jQuery.ajax({
			url: group_code_loc.ajax_url,
			method: 'post',
			dataType: 'json',
			data: {
				action: 'ldgr-update-group-code',
				nonce: jQuery('#ldgr_edit_nonce').val(),
				form: jQuery(this).serialize(),
			},
			beforeSend: function () {
				$black_screen.css('display', 'flex');
			},
			success: function (response) {
				jQuery('.ldgr-group-code-messages .ldgr-message-text').html(
					response.msg
				);

				if ('error' == response.type) {
					jQuery('.ldgr-group-code-messages')
						.removeClass('success')
						.addClass('error')
						.show();
				} else {
					jQuery('.ldgr-group-code-messages')
						.removeClass('error')
						.addClass('success')
						.show();
					jQuery(
						'table#ldgr-group-code-table #' + response.row_id
					).replaceWith(response.row_html);
					jQuery('.ldgr-group-code-items')
						.find('#' + response.row_id)
						.remove();
					jQuery('.ldgr-group-code-items .ldgr-group-code-item')
						.first()
						.before(response.row_html);
					jQuery('table#ldgr-group-code-table #' + response.row_id)
						.find('td.group-code-title ')
						.html(response.title);
					// jQuery('table#ldgr-group-code-table #' + response.row_id).find( '.ldgr-code-status-toggle' ).prop( 'checked', response.status );
					setTimeout(() => {
						jQuery('#ldgr-group-code-edit-form')
							.find('.gcs-cancel')
							.trigger('click');
					}, 4000);
				}
				jQuery('html, body').animate({
					scrollTop: jQuery('.ldgr-group-code-tab').offset().top - 20,
				});
				setTimeout(() => {
					ldgr_close_message();
					jQuery('#ldgr-group-code-edit-form')
						.find('.gcs-cancel')
						.trigger('click');
				}, 3000);
			},
			complete: function () {
				$black_screen.css('display', 'none');
			},
		});
	});

	// Group Code Delete
	jQuery('.ldgr-group-code-items').on(
		'click',
		'.ldgr-delete-code',
		function () {
			var confirm_del = confirm(group_code_loc.lang.delete_msg);

			if (confirm_del) {
				// Delete group code via ajax
				var nonce = jQuery(this).data('nonce');
				var group_code_id = jQuery(this).data('id');
				var $black_screen = jQuery('.ldgr-black-screen');
				jQuery.ajax({
					url: group_code_loc.ajax_url,
					method: 'post',
					dataType: 'json',
					data: {
						action: 'ldgr-delete-group-code',
						group_code: group_code_id,
						nonce: nonce,
					},
					beforeSend: function () {
						$black_screen.css('display', 'flex');
					},
					complete: function () {
						$black_screen.css('display', 'none');
					},
					success: function (response) {
						jQuery(
							'.ldgr-group-code-messages .ldgr-message-text'
						).html(response.msg);
						if ('error' == response.type) {
							jQuery('.ldgr-group-code-messages')
								.removeClass('success')
								.addClass('error')
								.show();
						} else {
							jQuery('.ldgr-group-code-messages')
								.removeClass('error')
								.addClass('success')
								.show();
							jQuery(
								'div.ldgr-group-code-items ' + response.row_id
							).fadeOut('slow', function () {
								jQuery(this).remove();
							});
						}
						jQuery('html, body').animate({
							scrollTop:
								jQuery('.ldgr-group-code-tab').offset().top -
								20,
						});
						setTimeout(() => {
							ldgr_close_message();
						}, 3000);
					},
				});
			}
		}
	);

	// Group Code Copy
	jQuery('.ldgr-group-code-items').on('click', '.ldgr-cp-code', function () {
		var group_code_id = jQuery(this).data('id');
		var group_code = jQuery(
			'#ldgr-group-code-row-' +
				group_code_id +
				' .ldgr-group-code-info .ldgr-group-code'
		);
		copyToClipboard(group_code);
		var msg = group_code_loc.lang.clipboard_copy;
		msg = msg.replace('__ldgr_code__', group_code.text());
		alert(msg);
	});
	// Group Code Copy
	jQuery('.ldgr-group-code-items').on(
		'click',
		'.ldgr-cp-code-url',
		function () {
			var group_code_id = jQuery(this).data('id');
			var group_code = group_code_id;
			copyURLToClipboard(group_code);
			var msg = group_code_loc.lang.clipboard_url_copy;
			alert(msg);
		}
	);

	jQuery('.ldgr-code-ip-validation').on('focusout', function () {
		if (!ldgr_validate_ip(jQuery(this).val())) {
			jQuery(this).val('');
		}
	});
});

function ldgr_reset_form($form) {
	if (!$form.length) {
		return;
	}

	$form.find('.ldgr-code-string').val('');
	$form.find('.ldgr-code-date-range-from').val('');
	$form.find('.ldgr-code-date-range-to').val('');
	$form.find('.ldgr-code-limit').val(0);
	$form.find('.ldgr-code-enrolled-users-count').text(0);
	$form.find('.ldgr-code-groups').val(-1);
	$form.find('.ldgr-code-validation-check').val('off');
	$form.find('.ldgr-validation-rules').slideUp();
	$form.find('.ldgr-code-status').val('off');
}

function ldgr_close_message() {
	jQuery('.ldgr-group-code-messages .ldgr-message-text').html('');
	jQuery('.ldgr-group-code-messages').removeClass('success error').fadeOut();
}

function ldgr_validate_ip(ipaddress) {
	if (
		/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(
			ipaddress
		)
	) {
		return true;
	}
	return false;
}

function copyToClipboard(element) {
	var $temp = jQuery('<input>');
	jQuery('body').append($temp);
	$temp.val(jQuery(element).text()).select();
	document.execCommand('copy');
	$temp.remove();
}

function copyURLToClipboard(element) {
	var dummy = document.createElement('textarea');
	document.body.appendChild(dummy);
	dummy.value = element;
	dummy.select();
	document.execCommand('copy');
	document.body.removeChild(dummy);
}
