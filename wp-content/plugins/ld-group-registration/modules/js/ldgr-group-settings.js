// cspell:ignore subgr

jQuery(document).on('ready', function () {
	jQuery('.ldgr-group-settings-icon').on('click', function () {
		if (!jQuery(this).hasClass('gear-open')) {
			jQuery(this).addClass('gear-open');
			jQuery('.ldgr-group-actions').slideDown();
		} else {
			jQuery(this).removeClass('gear-open');
			jQuery('.ldgr-group-actions').slideUp();
		}
	});

	jQuery('#ldgr-update-group-details').on('click', function (e) {
		e.preventDefault();
		var updated_group_name = jQuery(
			'input[name="ldgr-edit-group-name"]'
		).val();
		var updated_group_image_id = jQuery('#ldgr-edit-group-image').val();
		var group_id = parseInt(
			jQuery('input[name="ldgr-edit-group-name"]').data('group_id')
		);

		if (0 == updated_group_name.length || 100 < updated_group_name.length) {
			alert(ldgr_loc.invalid_group_name);
			return;
		}

		if (isNaN(group_id) || 0 > group_id) {
			alert(ldgr_loc.invalid_group_id);
			return;
		}
		jQuery.ajax({
			url: ldgr_loc.ajax_url,
			type: 'post',
			dataType: 'JSON',
			data: {
				action: 'ldgr_update_group_details',
				group_id: group_id,
				group_name: updated_group_name,
				group_image_id: updated_group_image_id,
			},
			success: function (response) {
				if ('error' == response.status) {
					alert(ldgr_loc.common_error + ' : ' + response.message);
				} else {
					// Update Group Name
					alert(response.message);
					jQuery('.update_group_details_group_form').submit();
					//jQuery('.wdm-select-wrapper-content select[name="wdm_group_id"] option[value="'+group_id+'"]').text(updated_group_name);
				}
			},
		});
	});

	jQuery('.create-sub-group-courses').select2({});

	jQuery('.edit-sub-group-courses').select2({});

	jQuery('.create-sub-group-submit').on('click', function (e) {
		e.preventDefault();
		var groupLeaders = jQuery('.create-sub-group-leader:checkbox:checked')
			.map(function () {
				return this.value;
			})
			.get();

		var groupUsers = jQuery('.create-sub-group-user:checkbox:checked')
			.map(function () {
				return this.value;
			})
			.get();

		var groupName = jQuery('.create-sub-group-name').val();
		var groupSeats = jQuery('.create-sub-group-seat').val();
		var groupCourses = jQuery('.create-sub-group-courses').val();
		var parentGroupId = jQuery('.parent-group-id').val();
		var parentIsUnlimited = jQuery('.is-unlimited-seats').val();
		var parentGroupLimit = jQuery('.parent-group-limit').val();
		var addNonce = jQuery('#ldgr-add-sub_group').val();

		// Check if valid group name entered.
		if (0 === groupName.length || 100 < groupName.length) {
			alert(ldgr_loc.invalid_sub_group_name);
			return;
		}

		// Check if courses selected.
		if (0 === groupCourses.length) {
			alert(ldgr_loc.invalid_sub_group_courses);
			return;
		}

		// Check if group seats not 0.
		if (groupSeats <= 0) {
			alert(ldgr_loc.empty_group_limit);
			return;
		}

		if (parseInt(groupSeats) > parentGroupLimit) {
			alert(ldgr_loc.group_limit);
		} else {
			jQuery.ajax({
				url: ldgr_loc.ajax_url,
				type: 'post',
				dataType: 'JSON',
				data: {
					action: 'ldgr_create_sub_group',
					groupName: groupName,
					groupSeats: groupSeats,
					groupCourses: groupCourses,
					groupUsers: groupUsers,
					groupLeaders: groupLeaders,
					parentGroupId: parentGroupId,
					parentGroupLimit: parentGroupLimit,
					nonce: addNonce,
				},
				success: function (response) {
					if ('error' == response.status) {
						alert(ldgr_loc.common_error + ' : ' + response.message);
					} else {
						alert(response.message);
						jQuery('.create-sub-group-parent-form').submit();
					}
				},
			});
		}
	});

	jQuery('.edit-sub-group-submit').on('click', function (e) {
		e.preventDefault();
		var groupLeaders = jQuery('.edit-sub-group-leader:checkbox:checked')
			.map(function () {
				return this.value;
			})
			.get();

		var groupUsers = jQuery('.edit-sub-group-user:checkbox:checked')
			.map(function () {
				return this.value;
			})
			.get();

		var groupName = jQuery('.edit-sub-group-name').val();
		var groupSeats = parseInt(jQuery('.edit-sub-group-seat').val());
		var groupCourses = jQuery('.edit-sub-group-courses').val();
		var parentGroupId = jQuery('.parent-group-id').val();
		var parentIsUnlimited = jQuery('.is-unlimited-seats').val();
		var parentGroupLimit = jQuery('.parent-group-limit').val();
		var subGroupId = jQuery('.sub-group-id').val();
		var editNonce = jQuery('#ldgr-edit-sub_group').val();
		var prevGroupSeats = jQuery(
			'input[name="previous-edit-sub-group-seat"]'
		).val();

		// Check if valid group name entered.
		if (0 === groupName.length || 100 < groupName.length) {
			alert(ldgr_loc.invalid_sub_group_name);
			return;
		}

		// Check if courses selected.
		if (0 === groupCourses.length) {
			alert(ldgr_loc.invalid_sub_group_courses);
			return;
		}

		// Check if group seats not 0.
		if (groupSeats <= 0 || isNaN(groupSeats)) {
			alert(ldgr_loc.empty_group_limit);
			return;
		}

		if (
			parseInt(groupSeats) - parseInt(prevGroupSeats) >
			parentGroupLimit
		) {
			alert(ldgr_loc.group_limit);
		} else {
			jQuery.ajax({
				url: ldgr_loc.ajax_url,
				type: 'post',
				dataType: 'JSON',
				data: {
					action: 'ldgr_edit_sub_group',
					groupName: groupName,
					groupSeats: groupSeats,
					groupCourses: groupCourses,
					groupUsers: groupUsers,
					groupLeaders: groupLeaders,
					parentGroupId: parentGroupId,
					parentGroupLimit: parentGroupLimit,
					subGroupId: subGroupId,
					nonce: editNonce,
				},
				success: function (response) {
					if ('error' == response.status) {
						alert(ldgr_loc.common_error + ' : ' + response.message);
					} else {
						alert(response.message);
						jQuery('.edit-sub-group-parent-form').submit();
					}
				},
			});
		}
	});

	jQuery('.ldgr-edit-subgr').on('click', function (e) {
		var sub_group_id = jQuery(this).data('sub_group_id');
		var $black_screen = jQuery('.ldgr-black-screen');
		jQuery.ajax({
			url: ldgr_loc.ajax_url,
			type: 'post',
			dataType: 'JSON',
			data: {
				action: 'ldgr_show_edit_sub_group',
				subGroupId: sub_group_id,
			},
			beforeSend: function () {
				$black_screen.css('display', 'flex');
			},
			complete: function () {
				$black_screen.css('display', 'none');
			},
			success: function (response) {
				if ('error' == response.status) {
					alert(ldgr_loc.common_error + ' : ' + response.message);
				} else {
					jQuery('.sub-group-id').val(sub_group_id);
					jQuery('.edit-sub-group-name').val(response.sub_group_name);
					jQuery('.edit-sub-group-seat').val(
						response.sub_group_limit
					);
					jQuery('input[name="previous-edit-sub-group-seat"]').val(
						response.sub_group_limit
					);
					jQuery('.edit-sub-group-leader:checkbox').each(function () {
						if (
							jQuery.inArray(
								parseInt(jQuery(this).val()),
								response.sub_group_leaders
							) != -1
						) {
							jQuery(this).prop('checked', true);
						}
					});
					jQuery('.edit-sub-group-user:checkbox').each(function () {
						if (
							jQuery.inArray(
								parseInt(jQuery(this).val()),
								response.sub_group_users
							) != -1
						) {
							jQuery(this).prop('checked', true);
						}
					});
					var selectedCourses = [];
					jQuery('.edit-sub-group-courses > option').each(
						function () {
							if (
								jQuery.inArray(
									parseInt(jQuery(this).val()),
									response.sub_group_courses
								) != -1
							) {
								selectedCourses.push(jQuery(this).val());
								jQuery(this).prop('selected', true);
							}
						}
					);
					jQuery('.edit-sub-group-courses').val(selectedCourses);
					jQuery('.edit-sub-group-courses').trigger('change');
					jQuery('.ldgr-sub-groups-content').hide();
					jQuery('.ldgr-edit-sg').show();
				}
			},
		});

		// e.preventDefault();
		// var groupLeaders = jQuery('.create-sub-group-leader:checkbox:checked').map(function() {
		//     return this.value;
		// }).get();

		// var groupUsers = jQuery('.create-sub-group-user:checkbox:checked').map(function() {
		//     return this.value;
		// }).get();

		// var groupName = jQuery('.create-sub-group-name').val();
		// var groupSeats = jQuery('.create-sub-group-seat').val();
		// var groupCourses = jQuery('.create-sub-group-courses').val();
		// var parentGroupId = jQuery('.parent-group-id').val();
		// var parentIsUnlimited = jQuery('.is-unlimited-seats').val();
		// var parentGroupLimit = jQuery('.parent-group-limit').val();

		// console.log(parentIsUnlimited);
		// console.log(parentGroupLimit);

		// if(parseInt(groupSeats) > parentGroupLimit) {
		//     alert(ldgr_loc.group_limit);
		// } else {
		//     jQuery.ajax({
		//         url: ldgr_loc.ajax_url,
		//         type: 'post',
		//         dataType: 'JSON',
		//         data: {
		//             action: 'ldgr_create_sub_group',
		//             groupName: groupName,
		//             groupSeats: groupSeats,
		//             groupCourses: groupCourses,
		//             groupUsers: groupUsers,
		//             groupLeaders: groupLeaders,
		//             parentGroupId: parentGroupId,
		//             parentGroupLimit: parentGroupLimit
		//         },
		//         success : function(response){
		//             if ('error' == response.status) {
		//                 alert(ldgr_loc.common_error + ' : '+response.message);
		//             } else {
		//                 alert(response.message);
		//                 jQuery('.create-sub-group-parent-form').submit();
		//             }
		//         }
		//     })
		// }
	});

	jQuery('.ldgr-rm-icon').on('click', function () {
		if (confirm('Are you sure ?')) {
			var group_id = parseInt(
				jQuery('input[name="ldgr-edit-group-name"]').data('group_id')
			);

			if (isNaN(group_id) || 0 > group_id) {
				alert(ldgr_loc.invalid_group_id);
				return;
			}

			jQuery.ajax({
				url: ldgr_loc.ajax_url,
				type: 'post',
				dataType: 'JSON',
				data: {
					action: 'ldgr_remove_group_image',
					group_id: group_id,
				},
				success: function (response) {
					if ('error' == response.status) {
						alert(ldgr_loc.common_error + ' : ' + response.message);
					} else {
						alert(response.message);
						jQuery('.update_group_details_group_form').submit();
					}
				},
			});
		}
	});
});
