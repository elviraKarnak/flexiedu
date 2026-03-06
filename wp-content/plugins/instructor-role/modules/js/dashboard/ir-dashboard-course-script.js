;( function($){
    $('button#ir-save-and-continue').on('click', function(){
        // Get active tab
        var active_tab = parseInt( $('.ld-tab-buttons button.is-primary').data('index') ) + 1;
        var total_tabs = parseInt( $('.ld-tab-buttons button' ).length ) - 1;
        var next_tab = 0;
        var settings_text = ir_dashboard_loc.settings_text;
        // Update course progress bar
        if ( total_tabs && active_tab <= total_tabs ) {
            next_tab = active_tab + 1;
            // Set next tab as active
            $( `.ld-tab-buttons > button:nth-of-type( ${next_tab} )`).trigger('click');
            // $( `.irb-progress-text`).text( settings_text.replace( '_count_', next_tab ) );
        }
    });

    setTimeout(() => {
        $( '.ld-tab-buttons > button').on( 'click', function(){
            // Set active dot
            var active_tab = parseInt( $(this).data('index') ) + 1;
            $( '.irb-dots span').each(function( ind, obj){
                if ( ind < active_tab ) {
                    $( obj ).addClass( 'ir-active-dot' );
                } else {
                    $( obj ).removeClass( 'ir-active-dot' );
                }
            });

            // Update course progress bar text
            var settings_text = ir_dashboard_loc.settings_text;
            $( `.irb-progress-text`).text( settings_text.replace( '_count_', active_tab ) );

            // Update progress bar.
            jQuery('.ir-progress-bar').css('width', ( ( active_tab - 1 ) * ir_dashboard_loc.step_width ) + '%' );
        });
    }, 300);
} )(jQuery);

