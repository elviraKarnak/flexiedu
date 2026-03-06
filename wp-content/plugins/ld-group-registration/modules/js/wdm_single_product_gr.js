jQuery(document).ready(function () {
	jQuery('#wdm_enroll_help_btn').on('click', function () {
		jQuery('.wdm_enroll_me_help_text').toggle();
	});

	jQuery('body').on('click', '#wdm_course_help_btn', function () {
		jQuery('.wdm_course_help_text').toggle();
	});

	jQuery('body').on('click', '#wdm_add_course_help_btn', function () {
		jQuery('.wdm_add_course_help_text').toggle();
	});

	var count_add_courses = 0;

	var quantity_min = jQuery('.quantity .qty').attr('min');

	//Store base price of product in variable
	/* var cache = jQuery(".price .woocommerce-Price-amount.amount bdi").children();
    jQuery(".price .woocommerce-Price-amount.amount bdi .woocommerce-Price-currencySymbol").remove();
    var price_on_load = parseFloat(jQuery(".price .woocommerce-Price-amount.amount bdi").text());
    jQuery(".price .woocommerce-Price-amount.amount bdi").text('').append(cache).append(parseFloat(price_on_load)); */

	//Store base price of unlimited users in variable
	var cache = jQuery(
		'.ldgr-unlimited-member-options .woocommerce-Price-amount.amount bdi'
	).children();
	jQuery(
		'.ldgr-unlimited-member-options .woocommerce-Price-amount.amount bdi .woocommerce-Price-currencySymbol'
	).remove();
	var unlimited_price_on_load = parseFloat(
		jQuery('input[name="ldgr_unlimited_member_price"]').val()
	);
	jQuery(
		'.ldgr-unlimited-member-options .woocommerce-Price-amount.amount bdi'
	)
		.text('')
		.append(cache)
		.append(parseFloat(unlimited_price_on_load));

	if ('front' == wdm_gr_data.default_script) {
		if (
			'on' === jQuery("input[name='wdm_ld_group_active']:checked").val()
		) {
			jQuery('.quantity').show();
			jQuery('.wdm-enroll-me-div').show();
		} else {
			update_quantity(quantity_min);
			jQuery('.quantity').hide();
			jQuery('.wdm-enroll-me-div').hide();
		}

		// @todo: Better solution for future.
		// var quantity_display = 'block';
		// setTimeout(function(){
		//     quantity_display = jQuery('.quantity').css('display');
		// }, 500 );

		jQuery("input[name='wdm_ld_group_active']").on('click', function () {
			clearVariations();
			jQuery('button[name="add-to-cart"]').removeAttr('disabled');
			if ('on' === jQuery(this).val()) {
				jQuery('.quantity').show();
				jQuery('.quantity .qty').attr('min', quantity_min);
				update_quantity(quantity_min);
				jQuery('div.ldgr_dynamic_options').slideDown();
				jQuery('div.ldgr_dynamic_courses').slideDown();

				if (jQuery('.ldgr_dynamic_options_select').length) {
					if (
						'create_new' ===
						jQuery('.ldgr_dynamic_options_select').val()
					) {
						jQuery('.wdm-enroll-me-div').show();
						jQuery('.ldgr-unlimited-member-options').show();
						jQuery('div.ldgr_group_name')
							.slideDown()
							.find('input')
							.focus();
						toggleLabel();
					} else {
						jQuery('div.ldgr_dynamic_values').slideDown();
					}

					if (
						'increase_seats' ===
						jQuery('.ldgr_dynamic_options_select').val()
					) {
						jQuery('div.ldgr_dynamic_courses').slideUp();
						jQuery('.quantity .qty').attr('min', 1);
						update_quantity('1');
					}
					if (
						'add_courses' ===
						jQuery('.ldgr_dynamic_options_select').val()
					) {
						count = 0;
						jQuery('.wdm-dynamic-course-checkbox').each(
							function () {
								if (
									this.checked &&
									'disabled' !== jQuery(this).attr('disabled')
								) {
									count++;
								}
							}
						);
						var users = jQuery(
							'option:selected',
							jQuery('.ldgr_dynamic_values_select')
						).attr('data-users');
						update_quantity(users);
						if (0 === count) {
							jQuery('button[name="add-to-cart"]').prop(
								'disabled',
								true
							);
						}
					}
				} else {
					jQuery('.wdm-enroll-me-div').show();
					jQuery('.ldgr-unlimited-member-options').show();
					jQuery('div.ldgr_group_name')
						.slideDown()
						.find('input')
						.focus();
				}
			}
			if ('on' !== jQuery(this).val()) {
				jQuery('.quantity .qty').attr('min', 1);
				update_quantity('1');
				jQuery('.quantity').hide();
				jQuery('.wdm-enroll-me-div').hide();
				jQuery('div.ldgr_dynamic_options').slideUp();
				jQuery('div.ldgr_dynamic_values').slideUp();
				jQuery('div.ldgr_dynamic_courses').slideUp();
				jQuery('div.ldgr_group_name').slideUp();
				jQuery('.ldgr-unlimited-member-options').hide();
			}
			toggleLabel();
		});

		jQuery('body').on('change', '.variations select', function () {
			if (
				'on' ===
				jQuery("input[name='wdm_ld_group_active']:checked").val()
			) {
				jQuery('.quantity .qty').attr('min', quantity_min);
				update_quantity(quantity_min);
				jQuery('.quantity').show();
				jQuery('.wdm-enroll-me-div').show();
			} else {
				jQuery('.quantity .qty').attr('min', 1);
				update_quantity('1');
				jQuery('.quantity').hide();
				jQuery('.wdm-enroll-me-div').hide();
			}
		});
	}
	if ('package' == wdm_gr_data.default_script) {
		// jQuery("div.quantity .qty").val(1);
		// jQuery("div.quantity").hide();
		// jQuery("body").on("change",".variations select",function(){
		//     jQuery("div.quantity").hide();
		//     jQuery("div.quantity .qty").val(1);
		// });
	}
	if (wdm_gr_data.cal_enroll) {
		jQuery('.variation_id').on('change paste keyup', function (e) {
			// e.preventDefault();
			// alert(jQuery(".variation_id").val());
			var cur_var = jQuery('.variation_id').val();
			if ('' == cur_var || 0 == cur_var) {
				return false;
			}
			// variations
			jQuery('.variations').append(
				'<img id="wdm_ajax_loader" src="' +
					wdm_gr_data.ajax_loader +
					'">'
			);
			jQuery.ajax({
				url: wdm_gr_data.ajax_url,
				type: 'post',
				data: {
					action: 'wdm_show_enroll_option',
					cur_var: cur_var,
					type: 'wc',
					enroll_option_nonce: wdm_gr_data.enroll_option_nonce,
				},
				success: function (response) {
					if (true == response || 1 == response) {
						jQuery('.wdm-enroll-me-div').hide();
					} else {
						jQuery('.wdm-enroll-me-div').show();
					}
					jQuery('#wdm_ajax_loader').remove();

					jQuery('.quantity .qty').attr('min', quantity_min);
				},
			});
			// alert(cur_var);
		});
	}

	if (jQuery('.variations_form').length) {
		var product_variations =
			jQuery('.variations_form').data('product_variations');
		if (!product_variations.length) {
			return;
		}

		var attribute_name = '';
		var attribute_value = '';
		jQuery('body').on('change', '.variations select', function () {
			attribute_name = jQuery(this).data('attribute_name');
			attribute_value = jQuery(this).val();

			if (
				jQuery("input[name='wdm_ld_group_active']").length &&
				'on' !==
					jQuery("input[name='wdm_ld_group_active']:checked").val()
			) {
				return;
			}

			product_variations.forEach((variation) => {
				// Check for same attributes
				if (attribute_value == variation.attributes[attribute_name]) {
					if (
						'hidden' !=
						jQuery(
							'#ldgr_variation_' + variation.variation_id
						).attr('type')
					) {
						jQuery('.ldgr_variation_group_options')
							.hide()
							.removeClass('active-variation');
						jQuery('#ldgr_variation_' + variation.variation_id)
							.show()
							.addClass('active-variation');
						jQuery('.ldgr_variations').show();
						jQuery('.ldgr_group_name').show();
					} else {
						jQuery('.ldgr_variations').hide();
						jQuery('.ldgr_group_name').hide();
					}

					// Update price on variation change.
					jQuery('#ldgr-unlimited-member-check').prop(
						'checked',
						false
					);
					jQuery('#ldgr-unlimited-member-check').trigger('change', [
						{ origin: 'code' },
					]);
					update_price(variation.display_price);
				}
			});

			// For variable products.
			if ('variable' === wdm_gr_data.product_type) {
				// Reset all input text.
				if (wdm_gr_data.autofill == 'off') {
					jQuery('.ldgr-g-name .ldgr-g-val').text('');
				}
				jQuery('.ldgr_variation_group_options ').each(function () {
					if (wdm_gr_data.autofill == 'off') {
						jQuery(this).val('');
					}
				});
				// Add listener to appropriate input.
				jQuery('.ldgr_variation_group_options.active-variation').on(
					'change keyup',
					function () {
						jQuery('.ldgr-g-name .ldgr-g-val').text(
							jQuery(this).val()
						);
					}
				);
				// Update groups related to the active variable product.
				ldgr_update_active_variation_groups();

				// Update list of courses related to the product.
				ldgr_update_active_variation_course_list();

				// Reset dynamic actions dropdown.
				jQuery('.ldgr_dynamic_options_select option')
					.prop('selected', function () {
						return this.defaultSelected;
					})
					.trigger('change');

				// Reset additional course checkboxes.
			}
		});
	}

	jQuery('#ldgr-unlimited-member-check').on('change', function (e, data) {
		var checked = jQuery(this).is(':checked');
		unselect_courses();
		update_price(unlimited_price_on_load, true);
		update_price(parseFloat(wdm_gr_data.price));
		if (undefined == data || 'code' !== data.origin) {
			jQuery('.ldgr_dynamic_options_select').change();
		}
		if (checked) {
			jQuery('.quantity .qty').hide();
			jQuery("input[name='wdm_ld_group_active']").prop('checked', true);
			jQuery('.wdm_group_registration').hide();
			jQuery('.ldgr_group_name').show();
			jQuery('div.ldgr_dynamic_options').slideDown();
			jQuery('div.ldgr_dynamic_courses').slideDown();
			jQuery('.quantity .qty').attr('min', 1);
			update_quantity('1');
		} else {
			jQuery('.quantity .qty').show();
			jQuery('.wdm_group_registration').show();
			jQuery('.quantity .qty').attr('min', quantity_min);
			update_quantity(quantity_min);
		}
	});

	function get_unlimited_course_value(elem) {
		if ('multiple' == wdm_gr_data.ldgr_dynamic_unlimited_price) {
			var value =
				parseFloat(elem.val()) *
				parseFloat(wdm_gr_data.ldgr_unlimited_members_dynamic_price);
		} else if ('fixed' == wdm_gr_data.ldgr_dynamic_unlimited_price) {
			var value = parseFloat(
				wdm_gr_data.ldgr_unlimited_members_dynamic_price
			);
		} else {
			var value = parseFloat(elem.val());
		}

		return value;
	}

	jQuery('.wdm-dynamic-course-checkbox').change(function () {
		if (
			jQuery('#ldgr-unlimited-member-check:checked').length > 0 ||
			jQuery(
				'option:selected',
				jQuery('.ldgr_dynamic_values_select')
			).attr('data-users') == 9999999
		) {
			var cache = jQuery(
				'.ldgr-unlimited-member-options .woocommerce-Price-amount.amount bdi'
			).children();
			jQuery(
				'.ldgr-unlimited-member-options .woocommerce-Price-amount.amount bdi .woocommerce-Price-currencySymbol'
			).remove();
			$old_price = parseFloat(
				jQuery('input[name="ldgr_unlimited_member_price"]').val()
			);

			value = get_unlimited_course_value(jQuery(this));

			if (this.checked) {
				count_add_courses++;
				$new_price = $old_price + value;
			} else {
				count_add_courses--;
				$new_price = $old_price - value;
			}

			jQuery(
				'.ldgr-unlimited-member-options .woocommerce-Price-amount.amount bdi'
			)
				.text('')
				.append(cache)
				.append(parseFloat($new_price));
			jQuery('input[name="ldgr_unlimited_member_price"]')
				.val($new_price)
				.change();
			if (
				jQuery(
					'option:selected',
					jQuery('.ldgr_dynamic_values_select')
				).attr('data-users') == 9999999
			) {
				update_price($new_price);
				// jQuery(".ldgr_new_price").val(parseFloat($new_price));
				// jQuery(".ldgr_new_price").trigger('change');
			}
		} else {
			$old_price = parseFloat(jQuery('.ldgr_new_price').val());
			if (this.checked) {
				count_add_courses++;
				$new_price = $old_price + parseFloat(jQuery(this).val());
			} else {
				count_add_courses--;
				$new_price = $old_price - parseFloat(jQuery(this).val());
			}
			update_price($new_price);
			// jQuery(".ldgr_new_price").val(parseFloat($new_price));
			// jQuery(".ldgr_new_price").trigger('change');
		}

		// if (jQuery('#ldgr-unlimited-member-check:checked').length > 0) {
		// } else {
		//     /* jQuery(".price .woocommerce-Price-amount.amount bdi").text('').append(cache).append(parseFloat($new_price)); */
		// }

		if ('add_courses' === jQuery('.ldgr_dynamic_options_select').val()) {
			if (count_add_courses) {
				jQuery('button[name="add-to-cart"]').removeAttr('disabled');
				return;
			}
			jQuery('button[name="add-to-cart"]').prop('disabled', true);
		}
	});

	/**
	 * Show and hide various controls based on dropdown selected.
	 */
	jQuery('.ldgr_dynamic_options_select').change(function (e) {
		count_add_courses = 0;
		unselect_courses();
		if (wdm_gr_data.autofill == 'off') {
			jQuery('div.ldgr_group_name > input[type=text]').val('');
		}
		if (e.originalEvent !== undefined) {
			jQuery('.ldgr_dynamic_values_select').prop('selectedIndex', 0);
		}

		jQuery('.quantity .qty').attr('min', quantity_min);
		if (jQuery('#ldgr-unlimited-member-check').is(':checked')) {
			jQuery('.quantity .qty').attr('min', 1);
		}

		// Reset hidden Unlimited options
		jQuery('.ldgr_dynamic_values_select option').each(function () {
			jQuery(this).show();
		});
		if ('create_new' == jQuery(this).val()) {
			jQuery('button[name="add-to-cart"]').removeAttr('disabled');
			jQuery('div.ldgr_dynamic_values').slideUp();
			jQuery('div.ldgr_group_name').slideDown();
			jQuery('div.ldgr_group_name > input[type=text]').prop(
				'readonly',
				false
			);
			if (wdm_gr_data.autofill == 'off') {
				jQuery('div.ldgr_group_name > input[type=text]').val('');
			} else {
				setGroupName();
			}
			update_quantity(quantity_min);
			jQuery('.quantity .qty').show();
			jQuery('div.ldgr_dynamic_courses').slideDown();
			jQuery('.ldgr-unlimited-member-options').show();
			jQuery('.wdm-enroll-me-div').show();

			// Set default price if user is creating new group
			update_price(unlimited_price_on_load, true);
			update_price(parseFloat(wdm_gr_data.price));
			if ('variable' === wdm_gr_data.product_type) {
				let variation_price = ldgr_get_active_variation();

				if (false !== variation_price) {
					update_price(variation_price.display_price);
				}
				// ldgr_preselect_existing_courses();
			}
		} else if ('add_courses' == jQuery(this).val()) {
			jQuery('div.ldgr_dynamic_values').slideDown();
			jQuery('div.ldgr_group_name').slideUp();
			jQuery('div.ldgr_group_name > input[type=text]').prop(
				'readonly',
				true
			);
			jQuery('div.ldgr_dynamic_courses').slideDown();
			jQuery('button[name="add-to-cart"]').prop('disabled', true);
			jQuery('#ldgr-unlimited-member-check').prop('checked', false);
			jQuery('#ldgr-unlimited-member-check').trigger('change', [
				{ origin: 'code' },
			]);
			jQuery('.ldgr-unlimited-member-options').hide();
			jQuery('.ldgr_dynamic_values_select').trigger('change', [
				{ origin: 'code' },
			]);
			jQuery('.quantity .qty').attr('min', 1);
			jQuery('.quantity .qty').hide();
			jQuery('.wdm-enroll-me-div').hide();
			// If variable product
			if ('variable' === wdm_gr_data.product_type && !e.isTrigger) {
				// Update groups related to the active variable product.
				ldgr_update_active_variation_groups();
			}

			// make price as 0 if user is adding course to existing group
			update_price(0);
			update_price(0, true);
		} else if ('increase_seats' == jQuery(this).val()) {
			jQuery('button[name="add-to-cart"]').removeAttr('disabled');
			jQuery('div.ldgr_dynamic_values').slideDown();
			jQuery('div.ldgr_group_name').slideUp();
			jQuery('div.ldgr_group_name > input[type=text]').prop(
				'readonly',
				true
			);
			jQuery('div.ldgr_dynamic_courses').slideUp();
			jQuery('#ldgr-unlimited-member-check').prop('checked', false);
			jQuery('#ldgr-unlimited-member-check').trigger('change', [
				{ origin: 'code' },
			]);
			jQuery('.ldgr-unlimited-member-options').hide();
			jQuery('.ldgr_dynamic_values_select').trigger('change', [
				{ origin: 'code' },
			]);
			jQuery('.wdm-enroll-me-div').hide();

			jQuery('.quantity .qty').attr('min', 1);
			update_quantity('1');
			jQuery('.quantity .qty').show();

			// set total price of courses if user is adding seats to existing group
			var price = parseFloat(wdm_gr_data.price);
			var unlimited_price = unlimited_price_on_load;

			if ('variable' === wdm_gr_data.product_type) {
				let variation_price = ldgr_get_active_variation();
				if (false !== variation_price) {
					price = variation_price.display_price;
				}
			}

			// Remove unlimited members group from dynamic values
			jQuery('.ldgr_dynamic_values_select option').each(function () {
				if (jQuery(this).attr('data-users') === '9999999') {
					jQuery(this).hide();
				}
			});

			// Add course price to base price.
			var product_courses = jQuery(
				'.ldgr_group_courses .ldgr-course-tile-row'
			).data('courses');
			jQuery('.wdm-dynamic-course-checkbox:checked').each(function () {
				var current_id = jQuery(this).attr('id');
				current_id = parseInt(current_id.split('_')[1]);
				// If course not already in product courses, add course price to base price.
				if (-1 === jQuery.inArray(current_id, product_courses)) {
					price = price + parseFloat(jQuery(this).val());
					unlimited_price =
						unlimited_price +
						parseFloat(get_unlimited_course_value(jQuery(this)));
				}
			});

			update_price(unlimited_price, true);
			update_price(price);

			if ('variable' === wdm_gr_data.product_type && !e.isTrigger) {
				// Update groups related to the active variable product.
				ldgr_update_active_variation_groups(true);
			}
		}
	});

	jQuery('.ldgr_dynamic_values_select').on('change', function (e, data) {
		if ('' === jQuery('option:selected', this).val()) {
			return;
		}

		var courses = JSON.parse(
			jQuery('option:selected', this).attr('data-courses')
		);

		ldgr_preselect_existing_courses();

		jQuery('div.ldgr_group_name > input[type=text]').val(
			jQuery('option:selected', this).text()
		);

		jQuery('.wdm-courses-checkbox .wdm-dynamic-course-checkbox').each(
			function (index) {
				var current_id = jQuery(this).attr('id');
				current_id = parseInt(current_id.split('_')[1]);

				if (-1 !== jQuery.inArray(current_id, courses)) {
					jQuery(this).prop('checked', true);
					jQuery(this).attr('disabled', true);
				} else {
					jQuery(this).prop('checked', false);
					jQuery(this).removeAttr('disabled');
				}
			}
		);

		var users = jQuery('option:selected', this).attr('data-users');
		if (users == 9999999) {
			jQuery('.quantity .qty').attr('min', 1);
			users = 1;
		}

		update_quantity(users);

		if (
			'add_courses' ===
			jQuery('.ldgr_dynamic_options_select option:selected').val()
		) {
			scroll_to_additional_courses();
		}

		if (undefined == data || 'code' !== data.origin) {
			jQuery('.ldgr_dynamic_options_select').change();
		}
	});

	if ('create_new' == jQuery('.ldgr_dynamic_options_select').val()) {
		jQuery('div.ldgr_group_name > input[type=text]').prop(
			'readonly',
			false
		);
		if (wdm_gr_data.autofill == 'off') {
			jQuery('div.ldgr_group_name > input[type=text]').val('');
		}
	}

	function update_price(price, is_unlimited = false) {
		if (is_unlimited) {
			var cache = jQuery(
				'.ldgr-unlimited-member-options .woocommerce-Price-amount.amount bdi'
			).children();
			jQuery(
				'.ldgr-unlimited-member-options .woocommerce-Price-amount.amount bdi .woocommerce-Price-currencySymbol'
			).remove();
			jQuery(
				'.ldgr-unlimited-member-options .woocommerce-Price-amount.amount bdi'
			)
				.text('')
				.append(cache)
				.append(parseFloat(price));
			jQuery('input[name="ldgr_unlimited_member_price"]')
				.val(price)
				.change();
			return;
		}

		/* var cache = jQuery(".price .woocommerce-Price-amount.amount bdi").children();
        jQuery(".price .woocommerce-Price-amount.amount bdi .woocommerce-Price-currencySymbol").remove();
        jQuery(".price .woocommerce-Price-amount.amount bdi").text('').append(cache).append(parseFloat(price)); */

		jQuery('.ldgr_new_price').val(parseFloat(price));
		update_total_price();
		// jQuery(".ldgr_new_price").trigger('change');
	}

	function update_total_price() {
		$price = jQuery('.ldgr_new_price').val();
		$qty = jQuery('.qty').val();
		$discount = 0;
		$actual_price = $price * $qty;
		$is_discount_available = false;
		//checking for empty bulk discount data
		if (wdm_gr_data.bulk_discount_data.length != 0) {
			//sorting based on quantity min quantity first

			wdm_gr_data.bulk_discount_data.sort((a, b) =>
				parseInt(a.quantity) > parseInt(b.quantity)
					? 1
					: parseInt(b.quantity) > parseInt(a.quantity)
						? -1
						: 0
			);

			//Iterating through each items
			wdm_gr_data.bulk_discount_data.forEach(function (item, index) {
				if (parseInt($qty) >= parseInt(item['quantity'])) {
					if (item['type'] == 'Percentage') {
						if (item['value'] != null) {
							$discount =
								(parseInt(item['value']) / 100) * $actual_price;
						}
					} else {
						$discount = item['value'];
					}
					$is_discount_available = true;
				}
			});
		}

		if ($is_discount_available) {
			let discounted_price = $actual_price - $discount;
			if (discounted_price < 0) {
				discounted_price = 0;
			}
			jQuery('.ldgr-value').addClass('js-ldgr-value');
			jQuery('.ldgr-discounted-value').addClass(
				'js-ldgr-discounted-value'
			);
			jQuery('.ldgr-discounted-value').text(
				WcFormatPrice(discounted_price)
			);
			jQuery('.ldgr-g-discount-lbl').addClass('js-ldgr-g-discount-lbl');
		} else {
			if (jQuery('.ldgr-value').hasClass('js-ldgr-value')) {
				jQuery('.ldgr-value').removeClass('js-ldgr-value');
				jQuery('.ldgr-discounted-value').removeClass(
					'js-ldgr-discounted-value'
				);
				jQuery('.ldgr-g-discount-lbl').removeClass(
					'js-ldgr-g-discount-lbl'
				);
			}
		}

		jQuery('.ldgr_total_price').val($actual_price);
		jQuery('.ldgr_total_price').trigger('change');
	}

	function update_quantity(quantity) {
		jQuery('.quantity .qty').val(quantity);
		jQuery('.quantity .qty').trigger('change');
		update_total_price();
	}

	function unselect_courses() {
		jQuery('.wdm-courses-checkbox .wdm-dynamic-course-checkbox').each(
			function () {
				this.checked = false;
				jQuery(this).removeAttr('disabled');
			}
		);
	}

	jQuery(".ldgr_total_price, input[name='ldgr_unlimited_member_price']").on(
		'change',
		function () {
			jQuery('.ldgr-g-price .ldgr-value').text(
				WcFormatPrice(jQuery(this).val())
			);
		}
	);

	jQuery('.qty').on('change', function () {
		users = jQuery('.ldgr_dynamic_values_select option:selected').attr(
			'data-users'
		);
		if ('9999999' === users) {
			jQuery('.ldgr-seats .ldgr-g-val').text(
				wdm_gr_data.ldgr_unlimited_text
			);
		} else {
			jQuery('.ldgr-seats .ldgr-g-val').text(jQuery(this).val());
		}

		update_total_price();
	});

	jQuery('input[name=ldgr_group_name]').on('change keyup', function () {
		jQuery('.ldgr-g-name .ldgr-g-val').text(jQuery(this).val());
	});

	jQuery('.ldgr_dynamic_options_select').on('change', function () {
		var grp_name = jQuery('.ldgr_dynamic_options_select :selected').val();
		if (grp_name == 'add_courses' || grp_name == 'increase_seats') {
			jQuery('.ldgr-g-name .ldgr-g-val').text(
				jQuery('.ldgr_dynamic_values_select :selected').text()
			);
		} else {
			jQuery('.ldgr-g-name .ldgr-g-val').text(
				jQuery('input[name=ldgr_group_name]').val()
			);

			jQuery('.variations_form').each(function () {
				var variations = jQuery(this).data('product_variations');
				for (var key in variations) {
					var variation = variations[key];
					// do something with the variation here
					if (
						jQuery(
							'input[name=ldgr_group_name_' +
								variation.variation_id +
								']'
						).is(':visible')
					) {
						jQuery('.ldgr-g-name .ldgr-g-val').text(
							jQuery(
								'input[name=ldgr_group_name_' +
									variation.variation_id +
									']'
							).val()
						);
					}
				}
			});
		}
	});

	jQuery('#ldgr-unlimited-member-check').on('change', function () {
		if (this.checked) {
			jQuery('.ldgr-seats .ldgr-g-val').text(
				wdm_gr_data.ldgr_unlimited_text
			);
			update_price(unlimited_price_on_load);
		} else {
			jQuery('.ldgr-seats .ldgr-g-val').text(jQuery('.qty').val());
			update_price(wdm_gr_data.price);
			if ('variable' === wdm_gr_data.product_type) {
				let variation_price = ldgr_get_active_variation();

				if (false !== variation_price) {
					update_price(variation_price.display_price);
				}
			}
		}
	});

	function showHideFooter() {
		// cspell:disable-next-line .
		if (jQuery('#wdm_gr_signle').is(':checked')) {
			jQuery('.ldgr-cal').addClass('ldgr-hide');
		} else {
			jQuery('.ldgr-cal').removeClass('ldgr-hide');
			//Logic to show hide group label
			var $i = 0;
			jQuery('.ldgr_variations')
				.children()
				.each(function () {
					if ($i === 0) {
						$i++;
						return;
					}
					//Logic
					if (jQuery(this).is(':visible')) {
						jQuery('.ldgr_group_name_switch').css(
							'display',
							'revert'
						);
					}
				});
		}
	}

	jQuery('.wdm_group_registration input[type=radio]').change(function () {
		showHideFooter();
	});

	// Display price and seats on page load.
	let qty = jQuery(".qty").attr('min') > jQuery(".qty").val() 
		? jQuery(".qty").attr('min')
		: jQuery(".qty").val();
	jQuery('.ldgr-seats .ldgr-g-val').text(qty);
	jQuery('.ldgr-g-price .ldgr-value').text(WcFormatPrice(wdm_functions_data.price_on_load * qty));

	function scroll_to_additional_courses() {
		jQuery('html, body').animate({
			scrollTop: jQuery('.ldgr_dynamic_courses').offset().top,
		});
	}

	/**
	 * Update the groups list with groups related with the active variable product
	 */
	function ldgr_update_active_variation_groups(hide_unlimited = false) {
		let active_variation = ldgr_get_active_variation();
		if (false !== active_variation) {
			jQuery.ajax({
				url: wdm_gr_data.ajax_url,
				type: 'post',
				dataType: 'json',
				data: {
					action: 'ldgr_get_variation_groups',
					variation_id: active_variation.variation_id,
					ldgr_nonce: jQuery('.ldgr_dynamic_values_select').data(
						'nonce'
					),
				},
				timeout: 30000,
				beforeSend: function () {
					jQuery('.ldgr_dynamic_values_select').prop(
						'disabled',
						true
					);
				},
				complete: function () {
					jQuery('.ldgr_dynamic_values_select').removeAttr(
						'disabled'
					);
				},
				success: function (response) {
					if (response.status) {
						jQuery('.ldgr_dynamic_values_select').empty();
						jQuery('.ldgr_dynamic_values_select').append(
							response.data
						);
						if (hide_unlimited) {
							// Remove unlimited members group from dynamic values
							jQuery('.ldgr_dynamic_values_select option').each(
								function () {
									if (
										jQuery(this).attr('data-users') ===
										'9999999'
									) {
										jQuery(this).hide();
									}
								}
							);
						}
					}
				},
			});
		}
	}

	/**
	 * Get the active variation for the product.
	 *
	 * @returns bool    Variation object if set, else false.
	 */
	function ldgr_get_active_variation() {
		let active_variation = false;
		let product_variations =
			jQuery('.variations_form').data('product_variations');
		if (!product_variations.length) {
			return false;
		}
		let attribute_name =
			jQuery('.variations select').data('attribute_name');
		let attribute_value = jQuery('.variations select').val();
		product_variations.forEach((variation) => {
			if (false !== active_variation) {
				return;
			}
			if (attribute_value == variation.attributes[attribute_name]) {
				active_variation = variation;
			}
		});

		return active_variation;
	}

	/**
	 * Update the list of courses associated to the variation.
	 */
	function ldgr_update_active_variation_course_list() {
		let active_variation = ldgr_get_active_variation();
		if (false !== active_variation) {
			jQuery.ajax({
				url: wdm_gr_data.ajax_url,
				type: 'post',
				dataType: 'json',
				data: {
					action: 'ldgr_update_variation_course_list',
					variation_id: active_variation.variation_id,
					ldgr_nonce: jQuery('.ldgr_dynamic_values_select').data(
						'nonce'
					),
				},
				timeout: 30000,
				beforeSend: function () {
					jQuery('.ldgr_dynamic_values_select').prop(
						'disabled',
						true
					);
				},
				complete: function () {
					jQuery('.ldgr_dynamic_values_select').removeAttr(
						'disabled'
					);
				},
				success: function (response) {
					if (response.status) {
						jQuery('.ldgr_group_courses').replaceWith(
							response.data
						);
						// Pre-select existing courses from add course list.
						ldgr_preselect_existing_courses();
					}
				},
			});
		}
	}

	/**
	 * Pre-select courses already included within the base product.
	 */
	function ldgr_preselect_existing_courses() {
		if (
			!jQuery('.wdm-courses-checkbox .wdm-dynamic-course-checkbox').length
		) {
			return;
		}

		var courses = jQuery('.ldgr_group_courses .ldgr-course-tile-row').data(
			'courses'
		);
		jQuery('.wdm-courses-checkbox .wdm-dynamic-course-checkbox').each(
			function (index) {
				var current_id = jQuery(this).attr('id');
				current_id = parseInt(current_id.split('_')[1]);

				if (-1 !== jQuery.inArray(current_id, courses)) {
					jQuery(this).prop('checked', false).parent().hide();
				} else {
					jQuery(this).parent().show();
				}
			}
		);
	}
	// Handle if variation pre-selected on page-load
	if ('variable' === wdm_gr_data.product_type) {
		jQuery('.variations select').trigger('change');
	}

	//Single product Group name
	var $i = 0;
	jQuery('.ldgr_group_name')
		.children()
		.each(function () {
			if ($i === 0) {
				$i++;
				return;
			}
			//Logic
			if (jQuery(this).is(':visible')) {
				jQuery('.ldgr_group_name_switch').css('display', 'revert');
			}
		});

	//Load Group name on variations select
	jQuery('body').on('change', '.variations select', function () {
		clickProductRadio();
		//Toggle Label
		toggleLabel();
	});

	/**
	 * Function to toggle the group name label
	 */
	function toggleLabel() {
		var $escape_variable = 0;
		jQuery('.ldgr_group_name')
			.children()
			.each(function () {
				if ($escape_variable === 0) {
					$escape_variable++;
					return;
				}
				if (jQuery(this).is(':visible')) {
					jQuery('.ldgr_group_name_switch').css('display', 'revert');
				}
			});
	}
	if (document.getElementsByClassName('variations_form').length > 0) {
		var $prev_selected = jQuery(
			'input[name="wdm_ld_group_active"]:checked'
		).filter(':checked')[0].id;
	}
	/**
	 * Helper function to add listening events on individual and group radio buttons.
	 */
	function clickProductRadio() {
		if (document.getElementsByClassName('variations_form').length > 0) {
			//Click on the radio button to fire the input load logic
			var $selected = jQuery(
				'input[name="wdm_ld_group_active"]:checked'
			).filter(':checked')[0].id;
			jQuery('#' + $selected).trigger('click');
			clearVariations();
		}
	}

	/**
	 * Helper function to clear variations if the product type is switched.
	 * @returns null
	 */
	function clearVariations() {
		if (document.getElementsByClassName('variations_form').length > 0) {
			var $selected = jQuery(
				'input[name="wdm_ld_group_active"]:checked'
			).filter(':checked')[0].id;
			if ($prev_selected !== $selected) {
				$prev_selected = $selected;
				if ($selected == 'wdm_gr_group') {
					jQuery('.reset_variations').trigger('click');
					showHideFooter();
				}
				return;
			}
		}
	}

	/**
	 * Helper function to ser group name.
	 */
	function setGroupName() {
		if (wdm_gr_data.autofill == 'on') {
			var today = new Date();
			var dd = String(today.getDate()).padStart(2, '0');
			var mm = String(today.getMonth() + 1).padStart(2, '0');
			var yyyy = today.getFullYear();
			today = mm + '/' + dd + '/' + yyyy;
			gname = jQuery('.product_title').html() + ' | ' + today;
			jQuery(this)
				.trigger('keypress')
				.val(function (i, val) {
					return gname;
				});
			jQuery('.ldgr-g-name .ldgr-g-val').html(gname);
			jQuery('input[name=ldgr_group_name]').val(gname);
		} else {
			jQuery('input[name=ldgr_group_name]').val('');
		}
	}

	//Calling functions on init
	clickProductRadio();
	toggleLabel();
	showHideFooter();
	setGroupName();

	//Renders value for sticky footer name as that of the group name field input during on page load.
	jQuery('.ldgr-g-name .ldgr-g-val').text(
		jQuery('input[name=ldgr_group_name]').val()
	);
});
