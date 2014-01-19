(function($) {
	$.fn.extend({
		nmtModal: function(options) {

			var defaults = {
				parent: 'body',
				windowId: 'nmtModal',
				closeWindow: 'close-window',
				modalWindow: 'modal-window',
				modalOverlay: 'modal-overlay',
				source: null,
				width: 480,
				height: 405
			};
			
			var options = $.extend(defaults, options);		
			
			return this.each(function() {
				new $.nmtModal(this, options);
			});
		}
	});
	
	$.nmtModal = function(element, options) {		
		$this = $(element);
		
		$this.bind('click', function(event){
			open();
			
			event.preventDefault();
			return false;
		});
		
		function close() {		
			$('.' + options.modalWindow).remove();	
			$('.' + options.modalOverlay).remove();
		};
		
		function open() {				
			modalOverlay = $('<div />').addClass(options.modalOverlay);
			
			windowHeight = $(window).height();
			windowWidth = $(window).width();
			
			if ($this.is('a') && options.source === null) {
				options.source = $this.attr('href');
			}
			
			iFrame = $('<iframe />')
			.attr('frameborder', 0)
			.attr('scrolling', 'no')
			.attr('allowtransparency', 'true')
			.attr('src', options.source)
			.attr('width', options.width)
			.attr('height', options.height);
			
			modalWindow = $('<div />')
				.attr('id', options.windowId)
				.addClass(options.modalWindow)
				.css('width', options.width)
				.css('height', options.height)
				.css('position', 'fixed')
				.css('top', (windowHeight - options.height) / 2 + 'px')
				.css('left', (windowWidth - options.width) / 2 + 'px')
				.html(iFrame);
			
			modalWindow.append(
				$('<a />').attr('class', options.closeWindow)
			);
			
			$(options.parent).append(modalOverlay.after(modalWindow));
			
			$('.' + options.closeWindow + ',' + '.' + options.modalOverlay).click(function(){
				close();
			});
		};
	};
		
	
})(jQuery);
