(function($){
    
    $('#ir_course_steps').on('click', function(){
        let course_id = parseInt( $(this).data('id') );
        let url = ir_fd_data.rest_url.shared_steps;

        url = url.replace("{course_id}", course_id );
        jQuery.ajax({
            method: 'GET',
            url: url,
            data:{
                _wpnonce: ir_fd_data.rest_nonce
            },
            success: function (response) {
                console.log("Shared Steps for Course ::"+course_id);
                console.log(response);
            }
        });
    }); 
    /* 
    $('#course_steps').on('click', function(){
        let course_id = parseInt( $(this).data('id') );
        let url = ir_fd_data.rest_url.shared_steps;

        url = url.replace("{course_id}", course_id );
        jQuery.ajax({
            method: 'POST',
            url: ir_fd_data.ajax_url,
            data:{
                action: 'ir_get_course_data',
                course_id: course_id,
                nonce: ir_fd_data.ajax_nonce
            },
            success: function (response) {
                console.log("Course Data ::"+course_id);
                console.log(response);
            }
        });
    });
    $('#all_courses').on('click', function(){
        let url = ir_fd_data.site_url + ir_fd_data.rest_url.course_list;
        jQuery.ajax({
            method: 'GET',
            url: url,
            data:{
                _wpnonce: ir_fd_data.rest_nonce
            },
            success: function (response) {
                console.log("Course List::");
                console.log(response);
            }
        });
    }); */

    
})(jQuery);
