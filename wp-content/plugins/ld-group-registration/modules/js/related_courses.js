jQuery(document).ready(function(){
   // jQuery('.show_if_course').each(function(){
   //                  jQuery(this).addClass('show_if_simple');
   //                  jQuery(this).show();
   //              });

   jQuery("#wdm_show_front_option").on("change",function(){
  	if(jQuery("#wdm_show_front_option").is(":checked")){
    		jQuery(".wdm-default-front-option").show();
  	}
  	else{
  		jQuery(".wdm-default-front-option").hide();
  	}
   });

   jQuery("#wdm_show_front_option").trigger('change');

  jQuery("#wdm_ld_group_registration").on("change",function(){
    if(jQuery("#wdm_ld_group_registration").is(":checked")){
        jQuery(".wdm_show_other_option").show();
		jQuery('select[name="ldgr_type_bulk_discount_for_product_setting"]').trigger('change');
    }
    else{
      jQuery(".wdm_show_other_option").hide();
    }
  });
   jQuery("#wdm_ld_group_registration").trigger('change');


   // alert(jQuery("#wdm_show_front_option").is(":checked"));

    jQuery("#ldgr_enable_unlimited_members").on("change",function(){
		if(jQuery(this).is(":checked")){
			jQuery(".ldgr-unlimited-group-members-settings").show();
			jQuery(".ldgr-unlimited-group-members-settings").find('#ldgr_unlimited_members_option_price').prop('required', true);
			if ( jQuery("#ldgr_enable_dynamic_group").is(":checked") ){
				jQuery(".ldgr-unlimited-price-type-setting").show();
			}
		}
		else{
			jQuery(".ldgr-unlimited-group-members-settings").hide();
			jQuery(".ldgr-unlimited-group-members-settings").find('#ldgr_unlimited_members_option_price').prop('required', false);
			jQuery(".ldgr-unlimited-price-type-setting").hide();
		}
 	});

	 jQuery("#ldgr_enable_dynamic_group").on("change",function(){
		 jQuery('select[name="ldgr_dynamic_unlimited_price"]').trigger('change');
		 if(jQuery(this).is(":checked")){
			 jQuery(".ldgr-dynamic-group-settings").show();
			 if (!jQuery("#ldgr_enable_unlimited_members").is(":checked")) {
				 jQuery(".ldgr-unlimited-price-type-setting").hide();
			 }
		}
		else{
			jQuery(".ldgr-dynamic-group-settings").hide();
			jQuery('.ldgr-dynamic-unlimited-member-value').hide();
		}
 	});

  jQuery('.addel-container').addel({
    events: {
        added: function (event) {
            event.preventDefault();
        }
    }
  });

  jQuery('select[name="ldgr_type_bulk_discount_for_product_setting"]').on('change', function(){
		var $this = jQuery(this);
		if ($this.val() == 'Product') {
			jQuery('.ldgr_bulk_discount_setting_data').show();
		} else {
			jQuery('.ldgr_bulk_discount_setting_data').hide();
		}
	});

  jQuery('select[name="ldgr_type_bulk_discount_for_product_setting"]').trigger('change');

	// Hide and show minimum quantity
	jQuery('#ldgr_bulk_discount_min_qty_check').on('change', function () {
		var checked = jQuery(this).is(':checked');
		if (checked) {
			jQuery('.ldgr_bulk_discount_min_qty_details').show();
			jQuery('.ldgr_bulk_discount_min_qty_details').find('#ldgr_bulk_discount_min_qty_value').prop('required', true);
		} else {
			jQuery('.ldgr_bulk_discount_min_qty_details').hide();
			jQuery('.ldgr_bulk_discount_min_qty_details').find('#ldgr_bulk_discount_min_qty_value').prop('required', false);
		}
	});

  jQuery('select[name="ldgr_dynamic_unlimited_price"]').on('change', function(){
		var $this = jQuery(this);
		if ($this.val() == 'default') {
			jQuery('.ldgr-dynamic-unlimited-member-value').hide();
			jQuery('.ldgr-dynamic-unlimited-member-value').find('#ldgr_unlimited_members_dynamic_price').prop('required', false);
		} else {
			jQuery('.ldgr-dynamic-unlimited-member-value').show();
			jQuery('.ldgr-dynamic-unlimited-member-value').find('#ldgr_unlimited_members_dynamic_price').prop('required', true);
		}
	});

	jQuery('select[name="ldgr_dynamic_unlimited_price"]').trigger('change');


    jQuery('.ldgr_bulk_discount_table').on('change', '.ldgr_bulk_discount_value_validate', function(){
		var $this = jQuery(this);
		var changedValue = $this.val();
		var count = 0;
		jQuery('.ldgr_bulk_discount_value_validate').each(function(i, obj){
      		if(changedValue == jQuery(this).val()) {
				count++;
			}
    	});
		if(count >= 2) {
			jQuery('.ldgr_duplicate_row_rule_error').show();
		} else {
			jQuery('.ldgr_duplicate_row_rule_error').hide();
		}
	});

	if ( typeof ldgr_setup_wizard !== 'undefined' ) {
		if ( ldgr_setup_wizard.enable_group_product ) {
			jQuery( '#wdm_ld_group_registration' ).trigger('click');
			jQuery('html, body').animate({
				scrollTop: jQuery("#wdm_ld_group_registration").offset().top
			}, 'fast');
		}
	}
  
	jQuery('.addel-add').on('click', function(e){
		e.preventDefault();
	})

	/**
	 * Remove course from other selector when selected in one
	 */
	 jQuery('.ld_related_courses, .ld_dynamic_courses').on('select2:select', function (e) {
		  var value = e.params.data.id;
		  var is_related_course = jQuery(this).hasClass('ld_related_courses');
		  var change_class = '.ld_dynamic_courses';
		  if (!is_related_course) {
			change_class = '.ld_related_courses';
		  }
		  jQuery(change_class + " option[value='"+ value +"']").remove();
	 });

	 /**
	 * Add course from other selector when removed in one
	 */
	 jQuery('.ld_related_courses, .ld_dynamic_courses').on('select2:unselect', function (e) {
		var value = e.params.data.id;
		var text = e.params.data.text;
		var is_related_course = jQuery(this).hasClass('ld_related_courses');
		var change_class = '.ld_dynamic_courses';
		if (!is_related_course) {
		  change_class = '.ld_related_courses';
		}

		// Create a DOM Option
    	var newOption = new Option(text, value, false, false);
    	// Append it to the select
    	jQuery(change_class).append(newOption).trigger('change');
   });

   /**
	* Remove courses from related course if selected in dynamic course on page load
    */
	jQuery.each(jQuery(".ld_dynamic_courses").val(), function( index, value ) {
		jQuery(".ld_related_courses option[value='"+ value +"']").remove();
	});

	/**
	 * 
	*/
	jQuery("table.ldgr_bulk_discount_table .ldgr_enable_bulk_discount_for_product_setting").on( 'change', function () {
		if ('Percentage' === jQuery('option:selected', this).val()) {
			jQuery(this).closest('td').next().find('input').val(0).attr('max', '100');
			return;
		}

		if ('Fixed' === jQuery('option:selected', this).val()) {
			jQuery(this).closest('td').next().find('input').val(0).removeAttr('max');
			return;
		}
	});

	jQuery(".ldgr_enable_bulk_discount_for_product_setting").each(function(){
		if ('Percentage' === jQuery('option:selected', this).val()) {
			jQuery(this).closest('td').next().find('input').attr('max', '100');
			return;
		}

		if ('Fixed' === jQuery('option:selected', this).val()) {
			jQuery(this).closest('td').next().find('input').removeAttr('max');
			return;
		}
	});
});
