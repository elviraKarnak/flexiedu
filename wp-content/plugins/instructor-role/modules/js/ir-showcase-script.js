jQuery( document ).ready(
	function(){
		jQuery( 'span.ir-after' ).on(
			'click',
			function(){
				if (jQuery( 'div.ir-active' ).length) {
					var $current_page = jQuery( 'div.ir-active' );
					if ($current_page.next().length) {
						$current_page.removeClass( 'ir-active' );
						$current_page.next().addClass( 'ir-active' );
					}
				} else {
					jQuery( 'div.ir-page' ).first().addClass( 'ir-active' );
				}
			}
		);

		jQuery( 'span.ir-previous' ).on(
			'click',
			function(){
				if (jQuery( 'div.ir-active' ).length) {
					var $current_page = jQuery( 'div.ir-active' );
					if ($current_page.prev().length) {
						$current_page.removeClass( 'ir-active' );
						$current_page.prev().addClass( 'ir-active' );
					}
				} else {
					jQuery( 'div.ir-page' ).last().addClass( 'ir-active' );
				}
			}
		);
	}
)
