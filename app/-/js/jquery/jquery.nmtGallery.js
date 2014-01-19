(function($) {
	$.fn.extend({
		nmtGallerySlider: function(options) {

			var defaults = {
				active: false,
				imageLink: '.galleryImageContainer a',
				galleryArea: '#slidingpanelsheet',
				nextArrow: '#slidingbuttonnextpanel',
				prevArrow: '#slidingbuttonpreviouspane',
				enableModal: true,
				maxWidth: -1356,
				imageWidth: 169,
				delay: 1000
			};
			
			var options = $.extend(defaults, options);		
			
			return this.each(function() {
				new $.nmtGallerySlider(this, options);
			});
		}
	});
	
	$.nmtGallerySlider = function(element, options) {	
		$this = $(element);
		
		if (options.enableModal === true) {
			$this.find(options.imageLink).nmtModal({});
		}
	    
	    $(options.nextArrow).click(function(){
	    	moveToNext();
	    	return false;
	    });
	    
	    $(options.prevArrow).click(function(){
	    	moveToPrev();
	    	return false;
	    });
	    
	    function moveToNext(){
	    	if (!options.active && $(options.galleryArea).position().left > options.maxWidth) {
	    		activateSlider();
	    		$(options.galleryArea).animate({
	    				left: $(options.galleryArea).position().left - options.imageWidth
		    		},
		    		options.delay,
		    		function(){
		    			deActivateSlider();
		    		}
	    		);
	    	}
	    }
	    
	    function moveToPrev(){
	    	if (!options.active && $(options.galleryArea).position().left < 0) {
	    		activateSlider();
	    		$(options.galleryArea).animate({
	    				left:$(options.galleryArea).position().left + options.imageWidth
	    			},
		    		options.delay,
		    		function(){
		    			deActivateSlider();
		    		}
	    		);
	    	}
	    }
	    
	    function activateSlider(){
	    	options.activate = true;
	    }
	    
	    function deActivateSlider(){
	    	options.activate = false;
	    }
	};
	
})(jQuery);