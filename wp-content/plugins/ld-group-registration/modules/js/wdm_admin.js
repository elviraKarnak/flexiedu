jQuery(document).ready(function ($) {
	var wdm_datatable = jQuery('#wdm_admin').DataTable({
		columnDefs: [wdm_ajax.datatable.column_defs],
		lengthMenu: wdm_ajax.datatable.length_menu,
	});

	var group_ids = [];

	jQuery(document).on(
		'click',
		'thead input[name="select_all"]',
		function (e) {
			if (this.checked) {
				jQuery(
					'#wdm_admin tbody input[type="checkbox"]:not(:checked)'
				).trigger('click');
			} else {
				jQuery(
					'#wdm_admin tbody input[type="checkbox"]:checked'
				).trigger('click');
			}
		}
	);

	// Function which performs action on all successful ajax responses
	function ldgr_on_success(response, id, status, selectedRow, group_id) {
		jQuery.each(response, function (status, message) {
			switch (status) {
				case 'success':
					snackbar(message);
					jQuery('#wdm_admin tr td a[data-user_id = "' + id + '"]')
						.siblings('img#wdm_ajax_loader')
						.remove();
					if (
						jQuery(
							'#wdm_admin tr td.select_action input[data-user_id = "' +
								id +
								'"]'
						).length
					) {
						jQuery(
							'#wdm_admin tr td.select_action input[data-user_id = "' +
								id +
								'"]'
						).prop('checked', false); // uncheck the selected checkbox
					}
					jQuery(
						'#learndash_group_users-' +
							group_id +
							' > table > tbody > tr > td.learndash-binary-selector-section-right > select > option[value="' +
							id +
							'"]'
					).remove();
					break;
				case 'group_limit':
					// Changes the count for left user enrollment
					let current_seat_count = parseInt(
						jQuery('.ldgr_seats_left').val()
					);
					if (current_seat_count < parseInt(message)) {
						jQuery('[name="ldgr_seats_left"]').attr(
							'value',
							message
						);
						let seats_left_msg = wdm_ajax.seats_left_text.replace(
							'{seat_count}',
							message
						);
						jQuery('.ldgr-seats-left-count')
							.data('seat-count', message)
							.text(seats_left_msg);
					}
					break;

				case 'total_limit':
					// Update total seats if fix group limit is on.
					if ('on' === wdm_ajax.is_fix_group_limit) {
						let current_total_count = parseInt(
							jQuery('.ldgr_total_seats').val()
						);
						if (current_total_count > parseInt(message)) {
							jQuery('.ldgr_total_seats').attr('value', message);
						}
					}
					break;
				case 'error':
					snackbar(message);
					jQuery('#wdm_admin tr td a[data-user_id = "' + id + '"]')
						.siblings('img#wdm_ajax_loader')
						.remove();
					break;
			}
			selectedRow.addClass('selected');
			wdm_datatable.row('.selected').remove().draw(false); // Remove the row
		});
	}

	// Code to accept bulk user removal request
	jQuery('body').on('click', '#bulk_accept', function (e) {
		e.preventDefault();

		// Informs user that nothing is selected
		if (
			jQuery('#wdm_admin tbody input[type="checkbox"]:checked').length ==
			0
		) {
			alert(wdm_ajax.no_user_selected);
			return false;
		}

		var group_id = '';
		var user_ids = [];
		var selectedRow = []; //holds the pointers to selected rows

		// fetches user and group id for all selected rows and adds ajax loader to selected rows
		jQuery('#wdm_admin tbody input[type="checkbox"]:checked').each(
			function () {
				jQuery(this)
					.parent()
					.parent()
					.find('td:last-child center')
					.append(
						'<img id="wdm_ajax_loader" src="' +
							wdm_ajax.ajax_loader +
							'">'
					);
				var user_id = jQuery(this).data('user_id');
				selectedRow[user_id] = jQuery(this).parent().parent();
				user_ids.push(user_id);
				group_id = jQuery(this).data('group_id');
			}
		);

		jQuery.ajax({
			url: wdm_ajax.ajax_url,
			type: 'post',
			dataType: 'JSON',
			data: {
				action: 'bulk_group_request_accept',
				group_id: group_id,
				user_ids: user_ids,
			},
			timeout: 30000,
			success: function (response) {
				jQuery.each(response, function (id, value) {
					ldgr_on_success(
						value,
						id,
						status,
						selectedRow[id],
						group_id
					);
				});
				jQuery('#wdm_admin thead input[name="select_all"]').attr(
					'checked',
					false
				);
			},
			beforeSend: function () {
				jQuery('.ldgr_total_seats').attr('readonly', true);
			},
			complete: function () {
				jQuery('.ldgr_total_seats').attr('readonly', false);
			},
		});
	});

	// Code to reject bulk user removal request
	jQuery('body').on('click', '#bulk_reject', function (e) {
		e.preventDefault();

		if (
			jQuery('#wdm_admin tbody input[type="checkbox"]:checked').length ==
			0
		) {
			alert(wdm_ajax.no_user_selected);
			return false;
		}

		var group_id = '';
		var user_ids = [];
		var selectedRow = []; //holds the pointers to selected rows

		// fetches user and group id for all selected rows and adds ajax loader to selected rows
		jQuery('#wdm_admin tbody input[type="checkbox"]:checked').each(
			function () {
				jQuery(this)
					.parent()
					.parent()
					.find('td:last-child center')
					.append(
						'<img id="wdm_ajax_loader" src="' +
							wdm_ajax.ajax_loader +
							'">'
					);
				var user_id = jQuery(this).data('user_id');
				selectedRow[user_id] = jQuery(this).parent().parent();
				user_ids.push(user_id);
				group_id = jQuery(this).data('group_id');
			}
		);

		// ajax request to bulk rejection
		jQuery.ajax({
			url: wdm_ajax.ajax_url,
			type: 'post',
			dataType: 'JSON',
			data: {
				action: 'bulk_group_request_reject',
				group_id: group_id,
				user_ids: user_ids,
			},
			timeout: 30000,
			success: function (response) {
				jQuery.each(response, function (id, value) {
					ldgr_on_success(
						value,
						id,
						status,
						selectedRow[id],
						group_id
					);
				});
				jQuery('#wdm_admin thead input[name="select_all"]').attr(
					'checked',
					false
				);
			},
			beforeSend: function () {
				jQuery('.ldgr_total_seats').attr('readonly', true);
			},
			complete: function () {
				jQuery('.ldgr_total_seats').attr('readonly', false);
			},
		});
	});

	// Code to accept a single user removal request
	jQuery('body').on('click', '.wdm_accept', function (e) {
		e.preventDefault();
		var temp = jQuery(this);
		jQuery(this)
			.parent()
			.append(
				'<img id="wdm_ajax_loader" src="' + wdm_ajax.ajax_loader + '">'
			);
		var group_id = jQuery(this).data('group_id');
		var user_id = jQuery(this).data('user_id');
		var selectedRow = jQuery(this).parent().parent().parent();
		jQuery.ajax({
			url: wdm_ajax.ajax_url,
			type: 'post',
			dataType: 'JSON',
			data: {
				action: 'wdm_ld_group_request_accept',
				group_id: group_id,
				user_id: user_id,
			},
			timeout: 30000,
			success: function (response) {
				ldgr_on_success(
					response,
					user_id,
					status,
					selectedRow,
					group_id
				);
			},
			beforeSend: function () {
				jQuery('.ldgr_total_seats').attr('readonly', true);
			},
			complete: function () {
				jQuery('.ldgr_total_seats').attr('readonly', false);
			},
		});
	});

	// Code to reject a single user removal request
	jQuery('body').on('click', '.wdm_reject', function (e) {
		e.preventDefault();
		var temp = jQuery(this);
		jQuery(this)
			.parent()
			.append(
				'<img id="wdm_ajax_loader" src="' + wdm_ajax.ajax_loader + '">'
			);
		var group_id = jQuery(this).data('group_id');
		var user_id = jQuery(this).data('user_id');
		var parent = jQuery(this).parent().parent().parent();
		var selectedRow = jQuery(this).parent().parent().parent();

		jQuery.ajax({
			url: wdm_ajax.ajax_url,
			type: 'post',
			dataType: 'JSON',
			data: {
				action: 'wdm_ld_group_request_reject',
				group_id: group_id,
				user_id: user_id,
			},
			timeout: 30000,
			success: function (response) {
				ldgr_on_success(
					response,
					user_id,
					status,
					selectedRow,
					group_id
				);
			},
			beforeSend: function () {
				jQuery('.ldgr_total_seats').attr('readonly', true);
			},
			complete: function () {
				jQuery('.ldgr_total_seats').attr('readonly', false);
			},
		});
	});

	$('table tr').each(function () {
		$(this).find('th').first().addClass('first');
		$(this).find('th').last().addClass('last');
		$(this).find('td').first().addClass('first');
		$(this).find('td').last().addClass('last');
	});

	$('table tr').first().addClass('row-first');
	$('table tr').last().addClass('row-last');

	let original_total_seats = parseInt(wdm_ajax.original_total_seats);

	$('.ldgr_total_seats').on('focusout', function (event) {
		let seats_left = parseInt(
			$('.ldgr-seats-left-count').data('seat-count')
		);
		let total_seats = parseInt($(this).val());
		let diff = 0;

		// If total seats decreased.
		if (total_seats < original_total_seats) {
			diff = original_total_seats - total_seats;
			// If difference greater than than overall seats left, then show error message.
			if (diff > seats_left) {
				alert(wdm_ajax.invalid_seat_update_msg);
				$(this).val(original_total_seats);
				// Update the original count;
				// original_total_seats = seats_left;
				return;
			} else {
				seats_left -= diff;
				jQuery('.ldgr_seats_left').attr('value', seats_left);
				let seats_left_msg = wdm_ajax.seats_left_text.replace(
					'{seat_count}',
					seats_left
				);
				jQuery('.ldgr-seats-left-count')
					.data('seat-count', seats_left)
					.text(seats_left_msg);
				// Update the original count;
				original_total_seats = total_seats;
			}
		}

		// If total seats increased.
		if (total_seats > original_total_seats) {
			diff = total_seats - original_total_seats;
			seats_left += diff;
			jQuery('.ldgr_seats_left').attr('value', seats_left);
			let seats_left_msg = wdm_ajax.seats_left_text.replace(
				'{seat_count}',
				seats_left
			);
			jQuery('.ldgr-seats-left-count')
				.data('seat-count', seats_left)
				.text(seats_left_msg);
			// Update the original count;
			original_total_seats = total_seats;
		}
	});

	// Reset seat counts
	$('.ldgr-reset-seats').on('click', function () {
		let total_seats = parseInt($('.ldgr_total_seats').val());
		let original_total = parseInt(wdm_ajax.original_total_seats);
		if (total_seats === original_total) {
			snackbar(wdm_ajax.no_change_reset_msg);
			return;
		}
		$('.ldgr_total_seats')
			.val(wdm_ajax.original_total_seats)
			.trigger('focusout');
		$(this).find('.dashicons-update').addClass('spin');
		setTimeout(function () {
			$('.ldgr-reset-seats')
				.find('.dashicons-update')
				.removeClass('spin');
			snackbar(wdm_ajax.update_reset_msg);
		}, 500);
	});

	if (wp) {
		let intervalCheckPostIsSaved;
		let ajaxRequest;

		wp.data.subscribe(function () {
			let editor = wp.data.select('core/editor');

			if (
				editor.isSavingPost() &&
				!editor.isAutosavingPost() &&
				editor.didPostSaveRequestSucceed()
			) {
				if (!intervalCheckPostIsSaved) {
					intervalCheckPostIsSaved = setInterval(function () {
						if (!wp.data.select('core/editor').isSavingPost()) {
							// if (ajaxRequest) {
							//     ajaxRequest.abort();
							// }

							// ajaxRequest = $.ajax({
							//     url: wdm_ajax.ajax_url,
							//     type: 'post',
							//     dataType: 'JSON',
							//     data: {
							//         action: 'group_users_seat_recalculate',
							//         group_id: wp.data.select("core/editor").getCurrentPostId(),
							//         nonce: wdm_ajax.ldgr_nonce
							//     },
							//     timeout: 30000,
							//     success: function (response) {
							//         if ('success' === response.status) {
							//             // Change the count for total seats.
							//             let total_seats = parseInt(response.total_seats);
							//             let seats_left = parseInt(response.seats_left);
							//             if (total_seats > 0) {
							//                 original_total_seats = total_seats;
							//                 $('.ldgr_total_seats').val(total_seats).trigger('focusout');
							//             }

							//             // Change the count for left seats.
							//             if (seats_left >= 0) {
							//                 $('.ldgr-seats-left-count').data('seat-count', seats_left);
							//                 $('[name="ldgr_seats_left"]').attr('value', seats_left);
							//                 let seats_left_msg = wdm_ajax.seats_left_text.replace('{seat_count}', seats_left);
							//                 $('.ldgr-seats-left-count').data('seat-count', seats_left).text(seats_left_msg);
							//             }
							//             $('.ldgr_total_seats').trigger('focusout');
							//         }
							//         ajaxRequest = null;
							//     }
							// });
							location.reload();

							clearInterval(intervalCheckPostIsSaved);
							intervalCheckPostIsSaved = null;
						}
					}, 800);
				}
			}
		});
	}
});
