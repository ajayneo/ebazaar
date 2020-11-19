/**
 * @author webwerks
 */

// select box code
jQuery(document).ready(function(){

	// Shop Gadgets - Next and Previous
	//	Variable number of visible items with variable sizes
	/*jQuery('#gadgets-list').carouFredSel({
		prev: '#prev3',
		next: '#next3',
		auto: false,
		circular:false,
		infinite: false,
		scroll : {
        items           : 1,
        easing          : "elastic",
        duration        : 1000    
        //pauseOnHover    : true
        }           
	});*/
	/*jQuery('div').find('.std').removeClass('std');*/
	
	/*jQuery('#homeproslider1, #homeproslider2, .gadgets').bxSlider({
	auto: false,
	pager: false,
	slideWidth: 170,
	slideMargin:28,
	controls: true,
	infiniteLoop:true,
	preloadImages:'all'
	});*/
	
	//shop gadgets - Tabs//
	/*jQuery(".tab_content").hide();
	jQuery(".tab_content:first").show(); 
	jQuery("ul.tabs li").first().addClass("active");
	jQuery("ul.tabs li").click(function() {
		jQuery("ul.tabs li").removeClass("active");
		jQuery(this).addClass("active");
		jQuery(".tab_content").hide();
		var activeTab = jQuery(this).attr("rel"); 
		jQuery("#"+activeTab).fadeIn(); 
	});*/
	
	// share your purchase Jquery
	//jQuery("#fancybox").fancybox();

	jQuery("#slider").slider({
		range: true,
		values: [ 17, 67 ]
	});

	//Main home slider//	
	SyntaxHighlighter.all();

	/*jQuery('.select').each(function(){
		var title = jQuery(this).attr('title');
		if( jQuery('option:selected', this).val() != ''  ) title = jQuery('option:selected',this).text();
			jQuery(this).css({'z-index':10,'opacity':0,'-khtml-appearance':'none'}).after('<span class="select">' + title + '</span>').change(function(){
			val = jQuery('option:selected',this).text();
			jQuery(this).next().text(val);
		})
	});*/
	
	//Product Details//
	/*jQuery(".specification-container h2").click(function() {
		jQuery(this).toggleClass('current');
		jQuery(this).parent("div").children("table").toggleClass("current");
		//$(this).parent("div").children("ul").toggleClass("current");
	});*/
	
	// select element styling
	/*jQuery('select.select').each(function(){
	var title = $(this).attr('title');
	if(jQuery('option:selected', this).val() != ''  ) title = jQuery('option:selected',this).text();
	jQuery(this)
		.css({'z-index':10,'opacity':0,'-khtml-appearance':'none'})
		.after('<span class="select">' + title + '</span>')
		.change(function(){
			val = jQuery('option:selected',this).text();
			jQuery(this).next().text(val);
		})
	});*/
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