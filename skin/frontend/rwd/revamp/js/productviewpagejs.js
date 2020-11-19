jQuery(document).ready(function(){

    /*jQuery(".product-img-box").sticky({
        topSpacing: 212,
        zIndex:2,
        stopper: "#footer"
    });*/
    
    //Product Details//
    jQuery(".specification-container h2").click(function() {
            jQuery(this).toggleClass('current');
            jQuery(this).parent("div").children("table").toggleClass("current");
            //$(this).parent("div").children("ul").toggleClass("current");
    });
    
    // cash back offer
    //jQuery('.info-icon').tooltip();
    jQuery('.info-text').tooltip();

    // video fancy box
    jQuery(".fancybox-media").click(function(){
        jQuery.fancybox({
            'padding'       : 0,
            'autoScale'     : false,
            'transitionIn'  : 'none',
            'transitionOut' : 'none',
            'title'         : this.title,
            'width'         : 640,
            'height'        : 385,
            'href'          : this.href.replace(new RegExp("watch\\?v=", "i"), 'v/'),
            'type'          : 'swf',
            'swf'           : {
            'wmode'             : 'transparent',
            'allowfullscreen'   : 'true'
            }
        });

        return false;
    });
    
    jQuery('.review_form').click(function(){
	jQuery('.form-add').css('display','block');
    });

    jQuery('#combo-items').bxSlider({
	auto: false,
	pager: false,
	slideWidth: 624,
	slideMargin:0,
	controls: true,
	infiniteLoop:false,
	minSlides: 1,
	maxSlides: 1,
	moveSlides: 1,
	preloadImages:'all'
    });
    
    //Product Page Related Products Slider
    var ul_count = jQuery('.related-products-slider').find('ul').size();
    if(ul_count > 1){
	jQuery('.related-products-slider').bxSlider({
	    auto: false,
	    pager: false,
	    //minSlides:1,
	    // maxSlides:5,
	    slideWidth: 170,
	    slideMargin:28,
	    controls: true,
	    infiniteLoop:false
	});
    }
    
    // media more view images slider
    var more_view_count = jQuery(".product-image-thumbs li").size();
    if(more_view_count > 4){
        jQuery('#recently').bxSlider({
	    auto: false,
	    pager: false,
        slideMargin:10,
	    controls: true,
	    infiniteLoop:false,
	    minSlides: 5,
	    maxSlides: 5,
	    moveSlides: 1,
	    preloadImages:'all'
        });
    }
    
    if(window.location.hash == '#review-form'){
	jQuery('.review_form').trigger('click');
	//jQuery('.form-add').css('display','block');
    }
	
	jQuery('.link-share').click(function(){
      jQuery('.opc-ajax-loader').css('display','block');
		  var a = jQuery(this).data('ppid');
      jQuery.ajax({
        url:'customblocks/index/setproinregistry',
        type:'POST',
        dataType : 'json',
        data:{a:a},
        success:function(data){
          jQuery('.opc-ajax-loader').css('display','none');
				  jQuery('.share-you-purchase-popup').replaceWith(data.sidebar);
				  jQuery('.share-you-purchase-popup').show();
        }
		  })
    });
	
    jQuery(document).on('click','.close-icon',function(){ 
      jQuery('.share-you-purchase-popup').hide();
    });
});