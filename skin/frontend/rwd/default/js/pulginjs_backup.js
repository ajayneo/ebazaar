/**
 * @author webwerks
 */

// select box code
jQuery(document).ready(function(){
	jQuery('.select').each(function(){
		var title = jQuery(this).attr('title');
		if( jQuery('option:selected', this).val() != ''  ) title = jQuery('option:selected',this).text();
			jQuery(this).css({'z-index':10,'opacity':0,'-khtml-appearance':'none'}).after('<span class="select">' + title + '</span>').change(function(){
			val = jQuery('option:selected',this).text();
			jQuery(this).next().text(val);
		})
	});
});

jQuery(document).ready(function(){
	// Shop Gadgets - Next and Previous
	//	Variable number of visible items with variable sizes
	jQuery('#gadgets-list').carouFredSel({
		prev: '#prev3',
		next: '#next3',
		auto: false,
		infinite: true,
		scroll : {
        items           : 1,
        easing          : "elastic",
        duration        : 1000    
        //pauseOnHover    : true
        }           
	});
	jQuery('div').find('.std').removeClass('std');

// home product slider
jQuery('#homeproslider1, #homeproslider2, .gadgets ,#feaproslider1').bxSlider({
	auto: false,
	pager: false,
	//minSlides:1,
    // maxSlides:5,
	slideWidth: 170,
	slideMargin:28,
	controls: true,
	infiniteLoop:true,
	preloadImages:'all'
});	  

//shop gadgets - Tabs//
jQuery(".tab_content").hide();
	jQuery(".tab_content:first").show(); 
	jQuery("ul.tabs li").click(function() {
		jQuery("ul.tabs li").removeClass("active");
		jQuery(this).addClass("active");
		jQuery(".tab_content").hide();
		var activeTab = jQuery(this).attr("rel"); 
		jQuery("#"+activeTab).fadeIn(); 
	});

// share your purchase Jquery
jQuery("#fancybox").fancybox();

//Product Details//
jQuery(".specification-container h2").click(function() {
	jQuery(this).toggleClass('current');
	jQuery(this).parent("div").children("table").toggleClass("current");
	//$(this).parent("div").children("ul").toggleClass("current");
});

jQuery("#slider").slider({
	range: true,
	values: [ 17, 67 ]
});	

// select element styling
jQuery('select.select').each(function(){
	var title = $(this).attr('title');
	if(jQuery('option:selected', this).val() != ''  ) title = jQuery('option:selected',this).text();
	jQuery(this)
		.css({'z-index':10,'opacity':0,'-khtml-appearance':'none'})
		.after('<span class="select">' + title + '</span>')
		.change(function(){
			val = jQuery('option:selected',this).text();
			jQuery(this).next().text(val);
		})
	});
});
    
//Main home slider//
jQuery(function(){
	SyntaxHighlighter.all();
});

jQuery(window).load(function(){
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
});