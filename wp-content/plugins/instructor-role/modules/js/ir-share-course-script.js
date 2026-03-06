jQuery( document ).ready(
	function(){
		if (jQuery.fn.select2) {
			function formatInstructor (instructor) {
				if ( ! instructor.id) {
					return instructor.text;
				}

				var $instructor = jQuery(
					'<div class="ir-instructor-img-div">\
                    <div>\
                        <img src="' + instructor.element.dataset["avatar"] + '" class="ir-instructor-img" />\
                    </div><span>' + instructor.text + '</span>\
                </div>'
				);
				return $instructor;
			};

			function selectInstructor(instructor) {
				if ( ! instructor.id) {
					return instructor.text;
				}

				var $instructor = jQuery(
					'<div class="sel-instructor-img-div"><div><img class="sel-instructor-img" /></div> <span></span></div>'
				);

				  // Use .text() instead of HTML string concatenation to avoid script injection issues
				  $instructor.find( "span" ).text( instructor.text );
				  $instructor.find( "img" ).attr( "src", instructor.element.dataset["avatar"] );

				  return $instructor;
			}

			jQuery( "#ir-shared-instructors" ).select2(
				{
					templateResult: formatInstructor,
					templateSelection: selectInstructor,
					placeholder: ir_loc.placeholder
				}
			);
		}
	}
);
