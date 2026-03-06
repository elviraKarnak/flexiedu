jQuery(document).ready(function(){
	// cspell:disable-next-line .
	var course_timer, group_timer , timespent_timer;

	/**
	 * Callback method for migrating course enrollments.
	 *
	 * @since 3.0.0
	 *
	 * @param {Object} self     The jQuery object for the button.
	 * @param {Object} span     The jQuery object for the span element.
	 * @param {Object} progress The jQuery object for the progress element.
	 *
	 * @return {void}
	 */
	// eslint-disable-next-line camelcase
	const migration_course_callback = function ( self, span, progress ) {
		jQuery.ajax( {
			// eslint-disable-next-line camelcase, no-undef
			url: admin_url.url,
			type: 'POST',
			data: {
				action: 'upgrade_course_enrollments'
			},
			success( response ) {
				progress.val( response.data.percentage );
				progress.text( response.data.percentage + '%' );
				span.text( response.data.percentage + '%' );
				if ( ! response.data.next ) {
					self.removeAttr( 'disabled' );
				} else {
					migration_course_callback( self, span, progress );
				}
			},
		} );
	};

	jQuery( '.start-course-migration' ).on( 'click', function ( event ) {
		event.preventDefault();
		const self = jQuery( this );

		if ( self.attr( 'disabled' ) ) {
			return;
		}

		const span = self.next();
		const progress = span.next();

		span.removeClass( 'hidden' );
		progress.removeClass( 'hidden' );
		self.attr( 'disabled', 'disabled' );

		migration_course_callback( self, span, progress );
	} );

	/**
	 * Callback method for migrating group enrollments.
	 *
	 * @since 3.0.0
	 *
	 * @param {Object} self     The jQuery object for the button.
	 * @param {Object} span     The jQuery object for the span element.
	 * @param {Object} progress The jQuery object for the progress element.
	 *
	 * @return {void}
	 */
	// eslint-disable-next-line camelcase
	const migration_group_callback = function ( self, span, progress ) {
		jQuery.post(
			// eslint-disable-next-line camelcase, no-undef
			admin_url.url,
			{
				action: 'upgrade_group_enrollments',
			},
			function ( response ) {
				progress.val( response.data.percentage );
				progress.text( response.data.percentage + '%' );
				span.text( response.data.percentage + '%' );

				if ( ! response.data.next ) {
					self.removeAttr( 'disabled' );
				} else {
					migration_group_callback( self, span, progress );
				}
			}
		);
	};

	jQuery( '.start-group-migration' ).on( 'click', function ( event ) {
		event.preventDefault();
		const self = jQuery( this );

		if ( self.attr( 'disabled' ) ) {
			return;
		}

		const span = self.next();
		const progress = span.next();

		span.removeClass( 'hidden' );
		progress.removeClass( 'hidden' );
		self.attr( 'disabled', 'disabled' );

		migration_group_callback( self, span, progress );
	} );

	//time spent data migration

	// cspell:disable-next-line .
	var migration_timespent_callback = function(self, span, progress) {
		jQuery.post(
			admin_url.url,
			{
				action: 'upgrade_course_time_spent'
			},
			function( response ) {
				progress.val(response.data.percentage);
				console.log(response.data.percentage);
				console.log(response.data.percentage === 100);
				if(response.data.percentage === 100){
					console.log("Done");
					progress.text(response.data.percentage + '%');
					span.text(response.data.percentage + '%');

					jQuery('.wrld_done_class').show();
				}else{
					progress.text(response.data.percentage + '%');
					span.text(response.data.percentage + '%');
				}

				if ( ! response.data.next ) {
					// cspell:disable-next-line .
					clearInterval(timespent_timer);
					//self.removeAttr('disabled');
				}
			}
		);
	};


	// cspell:disable-next-line
	jQuery('.start-timespent-migration').on('click', function(event) {
		event.preventDefault();
		var self = jQuery(this);
		var span = self.next();
		var progress = span.next();
		span.removeClass('hidden');
		progress.removeClass('hidden');
		self.attr('disabled', 'disabled');
		// var looper = true;
		// while ( looper ) {
			// jQuery.ajaxSetup({async: false});

		// }

		// cspell:disable-next-line .
		timespent_timer = setInterval(function(){
			// cspell:disable-next-line .
			migration_timespent_callback(self, span, progress);
		}, 2000);
	});
});
