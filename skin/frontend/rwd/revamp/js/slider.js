var intervalId;
var slidetime = 7000; // milliseconds between automatic transitions

jQuery(document).ready(function() {	

  // Comment out this line to disable auto-play
	intervalID = setInterval(cycleImage, slidetime);

	//$(".main_image .desc").show(); // Show Banner
	jQuery(".main_image .block").animate({ opacity: 0.85 }, 1 ); // Set Opacity

	// Click and Hover events for thumbnail list
	jQuery(".image_thumb ul li:first").addClass('active'); 
	jQuery(".image_thumb ul li").click(function(){ 
		// Set Variables
		var imgAlt = jQuery(this).find('img').attr("alt"); //  Get Alt Tag of Image
		var imgTitle = jQuery(this).find('a').attr("href"); // Get Main Image URL
		var imgTarget = jQuery(this).find('a').attr("data-target"); // Get Main URL
		var imgDesc = jQuery(this).find('.block').html(); 	//  Get HTML of block
		var imgDescHeight = jQuery(".main_image").find('.block').height();	// Calculate height of block	
		//alert(imgDescHeight);
		if (jQuery(this).is(".active")) {  // If it's already active, then...
			return false; // Don't click through
		} else {
			// Animate the Teaser				
			jQuery(".main_image .block").animate({ opacity: 0, marginBottom: -imgDescHeight }, 250 , function() {
				jQuery(".main_image .block").html(imgDesc).animate({ opacity: 0.85,	marginBottom: "0" }, 250 );
				jQuery(".main_image img").attr({ src: imgTitle , alt: imgAlt});
				jQuery(".main_image a").attr({ "href": imgTarget});
			});
		}
		
		jQuery(".image_thumb ul li").removeClass('active'); // Remove class of 'active' on all lists
		jQuery(this).addClass('active');  // add class of 'active' on this list only
		return false;
		
	}) .hover(function(){
		jQuery(this).addClass('hover');
		}, function() {
		jQuery(this).removeClass('hover');
	});
			
	// Toggle Teaser
	jQuery("a.collapse").click(function(){
		jQuery(".main_image .block").slideToggle();
		jQuery("a.collapse").toggleClass("show");
	});
	
	// Function to autoplay cycling of images
	// Source: http://stackoverflow.com/a/9259171/477958
	function cycleImage(){
    var onLastLi = jQuery(".image_thumb ul li:last").hasClass("active");       
    var currentImage = jQuery(".image_thumb ul li.active");
    
    
    if(onLastLi){
      var nextImage = jQuery(".image_thumb ul li:first");
    } else {
      var nextImage = jQuery(".image_thumb ul li.active").next();
    }
    
    jQuery(currentImage).removeClass("active");
    jQuery(nextImage).addClass("active");
    
		// Duplicate code for animation
		var imgAlt = jQuery(nextImage).find('img').attr("alt");
		var imgTitle = jQuery(nextImage).find('a').attr("href");
		var imgTarget = jQuery(nextImage).find('a').attr("data-target"); // Get Main URL
		var imgDesc = jQuery(nextImage).find('.block').html();
		var imgDescHeight = jQuery(".main_image").find('.block').height();
					
		jQuery(".main_image .block").animate({ opacity: 0, marginBottom: -imgDescHeight }, 250 , function() {
      jQuery(".main_image .block").html(imgDesc).animate({ opacity: 0.85,	marginBottom: "0" }, 250 );
      jQuery(".main_image img").attr({ src: imgTitle , alt: imgAlt});
	  jQuery(".main_image a").attr({ "href": imgTarget});
		});
  };
	
});// Close Function