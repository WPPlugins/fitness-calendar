jQuery(document).ready(function(){
 jQuery(".video_description").hide();
 
   jQuery("#calendar_description").click(function(){
        jQuery(".video_description").show();
    });
		
	jQuery("#calendar_description").click(function(){
        jQuery(".calendar_extra_videos").hide();
    });
	
    jQuery("#calendar_extra_videos").click(function(){
        jQuery(".calendar_extra_videos").show();
    });
    jQuery("#calendar_extra_videos").click(function(){
        jQuery(".video_description").hide();
    });
	
   				jQuery( ".iframe" ).click(function() { 
				var check = jQuery(this).closest('.iframe').children('.video_iframe').attr( "src");
				jQuery('#video_iframe_main').attr('src',check);
				jQuery('html, body').animate({
        scrollTop: jQuery("#video_iframe_main").offset().top
    }, 1000);
				//alert (frame);
});

	
});