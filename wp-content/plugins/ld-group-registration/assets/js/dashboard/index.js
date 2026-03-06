export default class Dashboard {
	tabs() {
		jQuery(document).on('click', '.ldgr-tabs li', function () {
			var attr = jQuery(this).attr('data-name');
			jQuery(this).parent().find('li').removeClass('current');
			jQuery(this).addClass('current');
			jQuery('.ldgr-tabs-content > div').removeClass('current');
			jQuery('.ldgr-tabs-content')
				.find('[data-name=' + attr + ']')
				.addClass('current');
		});
	}

	toggleCheckbox() {
		jQuery(document).on('click', '.empty-bg', function () {
			jQuery(this).parent().toggleClass('enabled');
			jQuery(this).trigger('checkboxToggle');
		});
	}

	// cspell:disable-next-line
	searchfromList() {
		jQuery('.ldgr-search').on('keyup', function () {
			var value = jQuery(this).val().toLowerCase();
			jQuery(this)
				.parents('.ldgr-search-list-wrap')
				.find('.ldgr-chk-item')
				.filter(function () {
					jQuery(this).toggle(
						jQuery(this).text().toLowerCase().indexOf(value) > -1
					);
				});
		});
	}

	searchGroups() {
		var self = this;
		jQuery('.ldgr-search-groups .ldgr-search').on('keyup', function () {
			var value = jQuery(this).val().toLowerCase();
			jQuery(this)
				.parents('.ldgr-group-listing')
				.find('.ldgr-group-item')
				.find('.gr-title')
				.filter(function () {
					jQuery(this)
						.parents('.ldgr-group-item')
						.toggle(
							jQuery(this).text().toLowerCase().indexOf(value) >
								-1
						);
					if (jQuery(this).text().toLowerCase().indexOf(value) > -1) {
						jQuery(this)
							.parents('.ldgr-group-item')
							.addClass('ldgr-per-page');
					} else {
						jQuery(this)
							.parents('.ldgr-group-item')
							.removeClass('ldgr-per-page');
					}
				});
			self.pagination();
		});
	}

	// cspell:disable-next-line
	scrolToElement() {
		jQuery(document).on('click', '.ldgr-alphabets span', function () {
			var key = jQuery(this).text();
			var list = jQuery(this)
				.parents('.ldgr-search-list-wrap')
				.find('.ldgr-list');
			var element = list.find(
				'input[data-name^=' + key.toLowerCase() + ']'
			);
			if (element.length) {
				var elementWrap = element.parents('.ldgr-chk-item')[0];
				var offset =
					elementWrap.offsetTop - elementWrap.parentNode.offsetTop;
				list.animate({ scrollTop: offset }, 200);
			}
		});
	}

	replaceContent(trigger_element, hide_element, show_element) {
		jQuery(document).on('click', trigger_element, function () {
			jQuery(hide_element).hide();
			jQuery(show_element).show();
		});
	}

	openLightbox(trigger_element, show_element) {
		jQuery(trigger_element).on('click', function () {
			jQuery(show_element).css('display', 'flex');
		});
	}

	closeLightbox(trigger_element, hide_element) {
		jQuery(trigger_element).on('click', function () {
			jQuery(hide_element).hide();
		});
	}

	closePopupOutsideClick() {
		jQuery('.ldgr-lightbox').on('click', function (e) {
			if (!jQuery(e.target).closest('.ldgr-popup').length) {
				jQuery('.ldgr-lightbox').hide();
			}
		});
	}

	addMoreUsers() {
		jQuery(document).on('click', '.ldgr-add-more-users', function () {
			jQuery('.ldgr-tabs-content .ldgr-add-users').append(
				ldgr_dashboard_loc.row_html
			);
		});
	}

	removeUsers() {
		jQuery(document).on('click', '.remove-user', function () {
			jQuery(this).parent().remove();
		});
	}

	pagination() {
		var self = this;
		var total_items =
			jQuery('.ldgr-group-items').find('.ldgr-per-page').length;
		var per_page = 10;
		var no_of_pages = Math.floor(total_items / per_page);
		if (total_items % per_page != 0) {
			no_of_pages = no_of_pages + 1;
		}
		jQuery('.ldgr-pagination').html('');
		if (no_of_pages > 1) {
			var numbers = '';
			for (var i = 1; i <= no_of_pages; i++) {
				numbers += '<li>' + i + '</li>';
			}
			jQuery('.ldgr-pagination').html(numbers);
		}
		jQuery('.ldgr-group-items .ldgr-per-page').hide();
		jQuery('.ldgr-group-items .ldgr-per-page').each(function (index) {
			if (index < 10) {
				jQuery(this).show();
			}
		});
		jQuery('.ldgr-pagination > li:nth-child(1)').addClass('ldgr-active');

		jQuery(document).on('click', '.ldgr-pagination li', function () {
			jQuery('.ldgr-pagination > li:nth-child(1)');
			jQuery('.ldgr-pagination > li').removeClass('ldgr-active');
			jQuery(this).addClass('ldgr-active');
			var current_page = parseInt(jQuery(this).text());
			var end = current_page * per_page;
			var start = current_page * per_page - per_page;
			jQuery('.ldgr-group-items > .ldgr-group-item').hide();
			for (var i = start + 1; i <= end; i++) {
				jQuery(
					'.ldgr-group-items > .ldgr-group-item.ldgr-per-page:nth-child(' +
						i +
						')'
				).show();
			}
		});
	}
}
