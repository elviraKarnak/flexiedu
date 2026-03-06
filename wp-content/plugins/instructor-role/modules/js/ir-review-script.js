( function( wp ) {

    const { subscribe } = wp.data;
    let displayNotice = false;

    const unssubscribe = subscribe( () => { // cspell:disable-line
        if ( wp.data.select('core/editor').isSavingPost()) {
            // Watch for the publish event.
            const currentPostStatus = wp.data.select( 'core/editor' ).getEditedPostAttribute( 'status' );
            if ( 'publish' === currentPostStatus && ! displayNotice) {
                displayNotice = true;
                wp.data.dispatch( 'core/notices' ).createNotice(
                    ir_review_data.review_notice_type, // Can be one of: success, info, warning, error.
                    ir_review_data.review_notice, // Text string to display.
                    {
                        isDismissible: true, // Whether the user can dismiss the notice.
                    }
                );
            }
        }
    } );

} )( window.wp );