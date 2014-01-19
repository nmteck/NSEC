(function($) {
	$.fn.extend({
		nmtGallery: function(options) {

			var defaults = {
				galleryArea: '.galleryImagesContainer',
				nextArrow: '#estorebuttonnextthumbset',
				prevArrow: '#estorebuttonpreviousthumb',
				selectedClass: '.selected',
				imageLink: '.galleryImageContainer a',
				mainImage: '.galleryImagePlaceholder',
				imageTitleContainer: '.galleryPlaceholder .title',
				enableModal: false,
				maxWidth: -477,
				imageWidth: 159
			};
			
			var options = $.extend(defaults, options);		
			
			return this.each(function() {
				new $.nmtGallery(this, options);
			});
		}
	});
	
	$.nmtGallery = function(element, options) 
	{	
		$this = $(element);
		
		transition = $.nmtGallery.transitions(options, select);
		
		$this.find(options.imageLink).bind('click', function(event){
			transition.select($(this));
			event.preventDefault();
			return false;			
		});
		
	    $this.find(".galleryNavigation").nmtGallerySlider(options);
	    
	    function select(){
	    	selected = $(options.selectedClass).parent();
			$(options.mainImage)
				.css('background-image', 'url(\'' + selected.attr('href') + '\')')
				.attr('href', selected.attr('href'));
		
			$(options.imageTitleContainer).html(selected.attr('title'));
	    }
	    
	    $.nmtGallery.init(options, transition);
	};
	
	$.nmtGallery.init = function(options, transition) 
	{
		transition.select($(options.imageLink + ':first'));
	};
	
	$.nmtGallery.transitions = function(options, select) 
	{	
		function selectThis(element){
			element.find('img').addClass('selected');
			select();
		}
		
		function clearAll() {
			$('.selected').removeClass('selected');
		}
		
		return {
			select: function(element){
				clearAll();
				selectThis(element);
			},
			clearAll: function(){
				clearAll();
			}
		}
	}
	
})(jQuery);