export default class irProfile{

	irTabs(){
		jQuery('.irp-tabs span').click(function(){
		    jQuery('.irp-tabs span').removeClass('irp-active');
		    jQuery(this).addClass('irp-active');
		    var current = jQuery(this).attr('data-id');
		    jQuery('.irp-tabs-content > div[data-id]').css('display', 'none');
		    jQuery('.irp-tabs-content > div[data-id="'+current+'"]').css('display', 'block');
	  });
	}

	addReadMore() {

		var element = jQuery('.irp-tab-content > p');
		if (element.length == 0){
			return;
		}
	  	var contentHeight = element[0].scrollHeight;
	  	var readmorePos = 130;
	  	if(jQuery(window).width() < 588){
	  		readmorePos = 396;
	  	}
	  	if (contentHeight > readmorePos){
	  		jQuery('<span class="irp-readmore">Read More</span>').insertAfter(element);
	  	}
	  	this.readMore(element, contentHeight);	
	  	this.readLess(element);	
	}

	readMore(element, contentHeight) {
		jQuery(document).on('click', '.irp-readmore', function(){
			jQuery(this).removeClass('irp-readmore');
			jQuery(this).addClass('irp-readless');
			jQuery(this).text('Read Less');
	  		element.css('max-height', contentHeight+'px');
	  	});
	}

	readLess(element) {
		var readmorePos = 130;
	  	if(jQuery(window).width() < 588){
	  		readmorePos = 396;
	  	}
		jQuery(document).on('click', '.irp-readless', function(){
			jQuery(this).removeClass('irp-readless');
			jQuery(this).addClass('irp-readmore');
			jQuery(this).text('Read More')
	  		element.css('max-height', readmorePos+'px');
	  	});
	}

}