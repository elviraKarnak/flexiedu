
(function ($) {

  //Reset prevent logic
  
  $('#ir-reset-overview-settings').on('click', function ( event ) {
    
    var question = ir_overview_settings_deps_object.delete_confirm; 
    
    let result = window.confirm(question);
    if ( result ) {
      return true;
    }else{
      event.preventDefault();
    }

});

})(jQuery);
