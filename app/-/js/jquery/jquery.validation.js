(function($) {
	$.fn.extend({
		validaton: function(options) {

			var defaults = {
				initialized: false
			};
			
			var options = $.extend(defaults, options);		
			
			return this.each(function() {
				new $.validaton(this, options);
			});
			
		}
	});

	$.validaton = function(div, options) {
		options.initialized = true;
		
		console.log($(div + ' *').hasAttr('validation'));
		
	};
	
	$.validaton.validator = function(div, options) {

		var rules = {
	
	        email : {
	           check: function(value) {
	
	               if(value) {
	                   return testPattern(value,"[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])");
	               }
	               return true;
	           },
	           msg : "Enter a valid e-mail address."
	        },
	        required : {
	
	           check: function(value) {
	
	               if(value) {
	                   return true;
	               }
	               else {
	                   return false;
	               }
	           },
	           msg : "This field is required."
	        }
	    };
		
	    return { // Public methods
	
	        addRule : function(name, rule) {
	
	            rules[name] = rule;
	        },
	        getRule : function(name) {
	
	            return rules[name];
	        }
	    };
	};
})(jQuery);
// Again, we're passing jQuery into the function 
// so we can use $ without potential conflicts.