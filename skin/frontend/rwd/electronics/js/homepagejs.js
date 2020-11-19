jQuery(document).ready(function(){
    // Shop Gadgets - Next and Previous
    //	Variable number of visible items with variable sizes
    /*jQuery('#gadgets-list').carouFredSel({
	prev: '#prev3',
	next: '#next3',
	auto: false,
	circular:true,
	infinite: true,
	scroll : {
            items           : 1,
            duration        : 1000    
            //pauseOnHover    : true
        }           
    });*/
    
    jQuery('div').find('.std').removeClass('std');
    
    jQuery('#homeproslider1, #homeproslider2, .gadgets').bxSlider({
        auto: false,
	pager: false,
	slideWidth: 170,
	slideMargin:28,
	controls: true,
	infiniteLoop:true,
	preloadImages:'all',
	touchEnabled:false,
	//preventDefaultSwipeY:false,
	minSlides: 1,
	maxSlides: 4,
	moveSlides: 4
    });
	//jQuery('#homeproslider1, #homeproslider2, .gadgets').swipe();
    //shop gadgets - Tabs//
    jQuery(".tab_content").hide();
    jQuery(".tab_content:first").show(); 
    jQuery("ul.tabs li").first().addClass("active");
    jQuery("ul.tabs li").click(function() {
	jQuery("ul.tabs li").removeClass("active");
        jQuery(this).addClass("active");
	jQuery(".tab_content").hide();
	var activeTab = jQuery(this).attr("rel"); 
	jQuery("#"+activeTab).fadeIn(); 
    });
    
    jQuery('#gadgets-list li').first().addClass("active");
    jQuery('#gadgets-list li').click(function(){
	jQuery('#gadgets-list li').removeClass("active");
	jQuery(this).addClass("active");
    });
    
    /*Feedback Button
     */
    jQuery(".feedback-btn").click(function(){
	jQuery(".feedback-content").toggle( "slow");
	jQuery('.feedback-btn').toggleClass('feedback-btn-1');
    });
});

/*jQuery(window).load(function(){
    jQuery('.flexslider').flexslider({
	animation: "slide",
	start: function(slider){
	    jQuery('body').removeClass('loading');
    	}
    });
    
    jQuery(".feedback-btn").click(function(){
	jQuery(".feedback-content").toggle( "slow");
	jQuery('.feedback-btn').toggleClass('feedback-btn-1');
    });
});*/