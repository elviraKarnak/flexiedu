jQuery( function () {
	jQuery( '.wrld-welcome-popup-modal' ).fadeIn( 500 );

	jQuery( 'button.modal-button-reports' ).on( 'click', function ( event ) {
		event.preventDefault();
		jQuery( '.wrld-welcome-popup-modal' ).hide();
	} );
} );
