/* JS for Sorting the menus */
(function ($) {
  $('div.ir-sidebar-menu-container #ir-parent-menu').sortable({
    axis: 'y',
    items: "> li:not(.ir-menu-item-sort-disabled)",
  });

  $('div.ir-sidebar-menu-container .ir-sidebar-custom-submenu').sortable({
    axis: 'y'
  });

  $('ul.ir-parent-menu ul.ir-sub-menu').sortable({
    items: "> li:not(.ir-submenu-item-sort-disabled)",
  });

  //Menu Dropdown
  $('.ir-parent-menu .dashicons.dashicons-arrow-down-alt2').on('click', function () {    
   
    var display = $(this).parent().find('.ir-sub-menu').css('display');
    //clear other elements
    $(this).parents().find('.ir-sub-menu').css('display','none');
    $(this).parent().find('.ir-sub-menu').css( 'display', display );

    $(this).parents().find('.ir-action-menu').hide();
    $(this).parents().find('.ir-action-menu-add').hide();
    $(this).parents().find('.ir-action-menu-edit').hide();
    $(this).parents().find('.dashicons-arrow-down-alt2').css( 'background' , 'unset' );
    $(this).parents().find('.dashicons-plus').css( 'background' , 'unset' );

      $(this).parent().find('.ir-sub-menu').animate({
        height: "toggle"
      }, 50, function() {
        // Animation complete.
        if( $(this).parent().find('.ir-sub-menu').css('display') != 'none' ) {
          //clear other highlights
          $(this).parent().find('.dashicons-ellipsis').css( 'background' , 'unset' );
          $(this).parent().find('.dashicons-edit').css( 'background' , 'unset' );
          $(this).parent().find('.dashicons-plus').css( 'background' , 'unset' );

          $(this).parent().find('.dashicons-arrow-down-alt2').css( 'background' , '#ddd' );
          $(this).parent().find('.dashicons-arrow-down-alt2').css( 'border-radius' , '50%' );
        } else {
          $(this).parent().find('.dashicons-arrow-down-alt2').css( 'background' , 'unset' );
        }
      });
  });

  //Menu action options 
  $('.ir-parent-menu .dashicons.parent-ellipsis').on('click', function () {
    
    var display = $(this).parent().find('.ir-action-menu').css('display');
    //clear other elements
    $(this).parents().find('.ir-action-menu').css('display','none');
    $(this).parent().find('.ir-action-menu').css( 'display', display );

    //clear other highlights
    $(this).parents().find('.dashicons-ellipsis').css( 'background' , 'unset' );
    
    $(this).parent().find('.ir-action-menu').animate({
      height: "toggle"
    }, 50, function() {
      // Animation complete.
      if( $(this).parent().find('.ir-action-menu').css('display') != 'none' ) {
        $(this).parent().find('.ir-action-menu').css( 'display' , 'grid' );
        $(this).parent().find('.ir-action-menu').css( 'position' , 'absolute' );
        $(this).parent().find('.dashicons-ellipsis').css( 'background' , '#ddd' );
        $(this).parent().find('.dashicons-ellipsis').css( 'border-radius' , '50%' );
      } else {
        $(this).parent().find('.dashicons-ellipsis').css( 'background' , 'unset' );
      }
    });
  });

  //menu add option
  $('.ir-parent-menu .dashicons.dashicons-plus').on('click', function () {
    var display = $(this).parent().find('.ir-action-menu-add').css('display');
    //clear other elements
    $(this).parents().find('.ir-action-menu-add').css('display','none');
    $(this).parent().find('.ir-action-menu-add').css( 'display', display );
   
    $(this).parents().find('.ir-action-menu').hide();
    $(this).parents().find('.ir-action-menu-edit').hide();
    $(this).parents().find('.ir-sub-menu').hide();

    //clear other highlights
    $(this).parents().find('.dashicons-arrow-down-alt2').css( 'background' , 'unset' );
    $(this).parents().find('.dashicons-ellipsis').css( 'background' , 'unset' );
    $(this).parents().find('.dashicons-edit').css( 'background' , 'unset' );
    $(this).parents().find('.dashicons-plus').css( 'background' , 'unset' );
    
    $(this).parent().find('.ir-action-menu-add').animate({
      height: "toggle"
    }, 50, function() {
      // Animation complete.
      if( $(this).parent().find('.ir-action-menu-add').css('display') != 'none' ) {
        $(this).parent().find('.dashicons-plus').css( 'background' , '#ddd' );
        $(this).parent().find('.dashicons-plus').css( 'border-radius' , '50%' );
      } else {
        $(this).parent().find('.dashicons-plus').css( 'background' , 'unset' );
      }
    });
  });

  //Menu edit option
  $('.ir-parent-menu .dashicons.parent-edit').on('click', function () {
    var display = $(this).parent().find('.ir-action-menu-edit').css('display');
    //clear other elements
    $(this).parents().find('.ir-action-menu-edit').css('display','none');
    $(this).parent().find('.ir-action-menu-edit').css( 'display', display );
    
    //hide all boxes
    $(this).parents().find('.ir-action-menu').hide();
    $(this).parents().find('.ir-action-menu-add').hide();
    $(this).parents().find('.ir-sub-menu').hide();

    //clear other highlights
    $(this).parents().find('.dashicons-arrow-down-alt2').css( 'background' , 'unset' );
    $(this).parents().find('.dashicons-ellipsis').css( 'background' , 'unset' );
    $(this).parents().find('.dashicons-plus').css( 'background' , 'unset' );
    $(this).parents().find('.dashicons-edit').css( 'background' , 'unset' );

    $(this).parent().find('.ir-action-menu-edit').animate({
      height: "toggle"
    }, 50, function() {
      // Animation complete.
      if( $(this).parent().find('.ir-action-menu-edit').css('display') != 'none' ) {
        $(this).parent().find('.ir-action-menu-edit').css( 'display' , 'grid' );
        $(this).parent().find('.dashicons-edit').css( 'background' , '#ddd' );
        $(this).parent().find('.dashicons-edit').css( 'border-radius' , '50%' );
      } else {
        $(this).parent().find('.dashicons-edit').css( 'background' , 'unset' );
      }
    });
  });

  //menu submenu delete option
  $('.ir-parent-menu .dashicons.dashicons-trash').on('click', function () {
    var question = ir_sortable_deps_object.custom_menu_delete_confirm; 
    let result = window.confirm(question);
    if ( result ) {
      $(this).parent().remove();
    }
  }); 

  //Submenu action options
  $('.ir-parent-menu ul.ir-sub-menu span.dashicons.dashicons-ellipsis').on('click', function () {
    var display = $(this).parent().find('.ir-action-submenu').css('display');
    
    //clear other elements
    $(this).parents().find('.ir-action-submenu').css('display','none');
    $(this).parent().find('.ir-action-submenu ').css( 'display', display );

    //clear other highlights
    $(this).parent().parent().find('.dashicons-ellipsis').css( 'background' , 'unset' );
    $(this).parent().parent().find('.dashicons-edit').css( 'background' , 'unset' );
    
    $(this).parent().find('.ir-action-submenu').animate({
      height: "toggle"
    }, 50, function() {
      // Animation complete.
      if( $(this).parent().find('.ir-action-submenu').css('display') != 'none' ) { 
        $(this).parent().find('.dashicons-ellipsis').css( 'background' , '#ddd' );
        $(this).parent().find('.dashicons-ellipsis').css( 'border-radius' , '50%' );
      } else {
        $(this).parent().find('.dashicons-ellipsis').css( 'background' , 'unset' );
        $(this).parent().find('.ir-action-submenu ').css('display','none');
      }
    });
  });

  //Submenu edit options
  $('.ir-parent-menu ul.ir-sub-menu span.dashicons.dashicons-edit').on('click', function () {
    var display = $(this).parent().find('.ir-action-submenu-edit').css('display');
    //clear other elements
    $(this).parents().find('.ir-action-submenu-edit').css('display','none');
    $(this).parent().find('.ir-action-submenu-edit').css( 'display', display );
    $(this).parent().find('.ir-action-submenu').hide();
   
    //clear other highlights
    $(this).parents().find('.dashicons-ellipsis').css( 'background' , 'unset' );
    $(this).parents().find('.dashicons-edit').css( 'background' , 'unset' );
    
    $(this).parent().find('.ir-action-submenu-edit').animate({
      height: "toggle"
    }, 50, function() {
      // Animation complete.
      if( $(this).parent().find('.ir-action-submenu-edit').css('display') != 'none' ) { 
        $(this).parent().find('.dashicons-edit').css( 'background' , '#ddd' );
        $(this).parent().find('.dashicons-edit').css( 'border-radius' , '50%' );
      } else {
        $(this).parent().find('.dashicons-edit').css( 'background' , 'unset' );
      }
    });
  });

  //Reset prevent logic
  $('#ir-menu-reset-settings').on('click', function ( event ) {
    
    var question = ir_sortable_deps_object.delete_confirm; 
    
    let result = window.confirm(question);
    if ( result ) {
      return true;
    }else{
      event.preventDefault();
    }

  });

  //Validation
  $('#ir-save-custom-menu, #ir-update-custom-menu, #ir-save-custom-sub-menu, #ir-update-custom-sub-menu').on('click', function ( event ) {
    
    jQuery('.ir-error-message').remove();
    var validate_input = $(this);
    jQuery.each(  jQuery(validate_input).parents('table').find('.ir-validate-custom-menu'), function(){
      if( jQuery(this).val() == '' ){
        event.preventDefault();
        jQuery(this).parent().append('<div class="ir-error-message" style="color:red;">'+ir_sortable_deps_object.empty_string+'</div>');
        jQuery(this).focus();
        return false;
      }
    } );
  });


})(jQuery);
