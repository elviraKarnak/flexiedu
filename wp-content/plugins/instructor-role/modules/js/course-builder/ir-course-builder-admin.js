(function($){
	$(function(){
		$(".ir-fcb-create-new").on('click', function(){
			$(this).prop('disabled', true);
			$(this).find('.dashicons-update').css('display', 'inline-block');
			$.ajax({
				url: ir_fcb_loc.ajax_url,
				method: 'POST',
				data: {
					action: "ir_fcb_new_course",
					nonce: ir_fcb_loc.nonce
				},
				dataType: 'JSON',
				timeout: 30000,
				success: function(response){
					if ( 'success' === response.status ) {
						$('#ir-fcb-create-new .dashicons-update').hide();
						window.location.replace(response.course_url);
					} else {
						console.log(response);
					}
				},
				complete: function(){
					$(this).prop('disabled', false);
					$(this).find('.dashicons-update').hide();
				}
			})
		});
	});
})(jQuery)

