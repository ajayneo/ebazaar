jQuery(document).ready(function($) {
	jQuery(".feedback-btn").on('click',function(e){
        e.stopImmediatePropagation();
		jQuery(".feedback-content").toggle("slow");
        jQuery(".feedback-btn").toggleClass("feedback-btn-1");
/*        var $fc = $('.feedback-content');
        var $fcBtn = $('.feedback-btn');
        if($fc.is(':visible')) {
            $fc.hide();
            $fcBtn.addClass('feedback-btn-1');
        } else {
            $fc.show();
            $fcBtn.removeClass('feedback-btn-1');
        }*/
    });

	jQuery(".mobile-nav").on('click',function(e){
		jQuery(".wrapper").toggleClass('body_no_scroll');
	});
	
	
	// hide #back-top first
	jQuery("#back-top").hide();
	
	// fade in #back-top
	jQuery(function () {
		jQuery(window).scroll(function () {
			if (jQuery(this).scrollTop() > 100) {
				jQuery('#back-top').fadeIn();
			} else {
				jQuery('#back-top').fadeOut();
			}
		});

		// scroll body to 0px on click
		jQuery('#back-top a').click(function () {
			jQuery('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	});
});
jQuery('ol.nav-primary li.level0').last().addClass('deals');
var window_width = jQuery(window).width();
//console.log(window_width);
                if(window_width >= 768 ){
                                                
                jQuery(window).scroll(function(){
                var scroll = jQuery(window).scrollTop();
				//console.log(scroll);
                if(scroll > 53){
                 jQuery(".page-header").addClass('header-fixed');
                 jQuery(".header-language-background").fadeOut(100);
				 jQuery(".nav-inner").addClass('nav-fixed');
                     //jQuery(".nav-inner").fadeOut(100);
             
                     jQuery(".compare-section").addClass('compare-fixed');
                }else if(scroll == 0){
                 //jQuery(".page-header").css("display","block").fadeOut();
                     jQuery(".page-header").removeClass('header-fixed');
                 jQuery(".header-language-background").fadeIn();
				 jQuery(".nav-inner").removeClass('nav-fixed');
                     //jQuery(".nav-inner").fadeIn();
             
                     jQuery(".compare-section").removeClass('compare-fixed');
                }
             });
                
                }
                
//var window_width = jQuery(window).width();
                if(window_width <= 768 ){				
                jQuery(window).scroll(function(){
                var scroll = jQuery(window).scrollTop();
                if(scroll){
					//jQuery(".page-header").addClass('header-fixed');
					//jQuery(".header-language-background").fadeOut(100);
					jQuery(".compare-section").addClass('compare-fixed');
					//jQuery("#header-nav").addClass('nav-fixed');
                }else if(scroll == 0){
					//jQuery(".page-header").removeClass('header-fixed');
					//jQuery(".header-language-background").fadeIn();
					jQuery(".compare-section").removeClass('compare-fixed');
					//jQuery("#header-nav").removeClass('nav-fixed');
                }
             });
                
                }
