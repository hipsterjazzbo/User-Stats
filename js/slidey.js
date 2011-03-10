jQuery(document).ready(function($) {
    
    $('.expand_button').toggle(function(){
	    $(this).text('Click to hide')
	    	   .next('.expand_list')
	    	   .slideDown();
    
	    return false;
	}, function(){
		$(this).text('Click to view')
		   	   .next('.expand_list')
		   	   .slideUp();
		
		return false;
	});
    
});
