jQuery(
	function(){
		jQuery( '.ir-profile-list-container' ).on(
			'click',
			'.ir-profile-add-input',
			function(){
				var $list  = jQuery( this ).siblings( '.ir-profile-list' );
				var $input = $list.find( '.ir-profile-input' ).last();
				$input.val( '' ).clone().appendTo( $list );
			}
		);

		jQuery( '.ir-profile-list-container' ).on(
			'click',
			'.ir-profile-remove-input',
			function() {
				var $input    = jQuery( this ).parent();
				var container = jQuery( this ).parents( '.ir-profile-list-container' );
				if ( container.find( '.ir-profile-input' ).length > 1 ) {
					$input.remove();
				}
			}
		);
	}
);
