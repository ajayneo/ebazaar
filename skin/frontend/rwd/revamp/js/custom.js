var vj = jQuery.noConflict();
vj(document).ready(function(){
			vj('.main-slider').bxSlider({
				speed: 2000,
    			pause: 7000,
			  	auto: true,
			  	slideMargin: 0
			});
		  	vj('.featured-slider').bxSlider({
				minSlides: 3,
				maxSlides: 3,
				slideWidth: 256,
				slideMargin: 30,
				moveSlides:1
			});
			vj('.recently-viewed').bxSlider({
				minSlides: 1,
				maxSlides: 4,
				slideWidth: 256,
				slideMargin: 15,
                pager: false,
                moveSlides:1
            });
            vj('.brands-slider').bxSlider({
                minSlides: 1,
                maxSlides: 3,
                slideWidth: 256,
                slideMargin: 10,
                pager: false,
                moveSlides:1
            });
            vj('.recently-viewed1').bxSlider({
               minSlides: 1,
               maxSlides: 4,
               slideWidth: 240,
               slideMargin: 15,
               pager: false,
               moveSlides:1
            });
			// vj('.top-brands-content').bxSlider({
			// 	slideWidth: 152,
		 //        auto: true,
		 //        speed: 9000, 
		 //        pager: false,
		 //        controls: false,
		 //        pause: 1, 
		 //        maxSlides: 7,
		 //        moveSlides:1,
		 //        slideMargin:10, 
		 //        infiniteLoop: true
			// });
});

vj(document).ready(function(){	
	// vj(".login a").click(function(){
 //        vj(".popup-container").fadeIn("slow");
 //    });
 //    vj(".popup-container .close-button a").click(function(){
 //        vj(".popup-container").fadeOut("slow");
 //    });
    // vj(".gsti-container a").click(function(){
    //     vj(".gst-popup-contanier").fadeIn("slow");
    // });
    // vj(".gst-popup-contanier .close-button a").click(function(){
    //     vj(".gst-popup-contanier").fadeOut("slow");
    // });
	vj(".more-links").click(function(){
        vj(".top-links").fadeToggle("slow");
    });
    vj(".responsive-menu-icon").click(function(){
        vj("#header-nav").fadeIn("slow");
    });
    vj("#nav .close-button").click(function(){
        vj("#header-nav").fadeOut("slow");
    });
    vj(".responsive-menu-icon").click(function(){
        vj("#nav").addClass("active");
    });
    vj("#nav .close-button").click(function(){
        vj("#nav").removeClass("active");
    });
    vj(".search-links").click(function(){
        vj("#header-search").toggleClass("active");
    });
    vj(".specification-title").click(function(){
        vj(".specification-details-container").slideToggle();
        vj(".specification-title").toggleClass("active");
    });

    // Sell your gadget
    vj(".gadget-tabs li.gadget-laptop").click(function(){
    	vj(".gadget-tabs li.gadget-laptop").addClass("active");
    	vj(".gadget-tabs li.gadget-mobile").removeClass("active");
        vj(".new-gadget-laptop").addClass("active");
        vj(".new-gadget-mobile").removeClass("active");
    });
    vj(".gadget-tabs li.gadget-mobile").click(function(){
    	vj(".gadget-tabs li.gadget-mobile").addClass("active");
    	vj(".gadget-tabs li.gadget-laptop").removeClass("active");
        vj(".new-gadget-mobile").addClass("active");
        vj(".new-gadget-laptop").removeClass("active");
    });
    vj(".gadget-brand-list .laptop-apple").click(function(){
        vj(".laptop-apple").addClass("active");
        vj(".gadget-brand-apple").addClass("active");
        vj(".gadget-brand-dell").removeClass("active");
        vj(".gadget-brand-hp").removeClass("active");
        vj(".gadget-brand-lenovo").removeClass("active");
        vj(".gadget-brand-samsung").removeClass("active");
        vj(".laptop-dell").removeClass("active");
        vj(".laptop-hp").removeClass("active");
        vj(".laptop-lenovo").removeClass("active");
        vj(".laptop-other").removeClass("active");
        vj(".laptop-samsung").removeClass("active");
    });
    vj(".gadget-brand-list .laptop-dell").click(function(){
        vj(".laptop-dell").addClass("active");
        vj(".gadget-brand-dell").addClass("active");
        vj(".gadget-brand-apple").removeClass("active");
        vj(".gadget-brand-hp").removeClass("active");
        vj(".gadget-brand-lenovo").removeClass("active");
        vj(".laptop-apple").removeClass("active");
        vj(".laptop-hp").removeClass("active");
        vj(".laptop-lenovo").removeClass("active");
        vj(".laptop-other").removeClass("active");
    });
    vj(".gadget-brand-list .laptop-hp").click(function(){
        vj(".laptop-hp").addClass("active");
        vj(".gadget-brand-hp").addClass("active");
        vj(".gadget-brand-apple").removeClass("active");
        vj(".gadget-brand-dell").removeClass("active");
        vj(".gadget-brand-lenovo").removeClass("active");
        vj(".laptop-apple").removeClass("active");
        vj(".laptop-dell").removeClass("active");
        vj(".laptop-lenovo").removeClass("active");
        vj(".laptop-other").removeClass("active");
    });
    vj(".gadget-brand-list .laptop-lenovo").click(function(){
        vj(".laptop-lenovo").addClass("active");
        vj(".gadget-brand-lenovo").addClass("active");
        vj(".gadget-brand-apple").removeClass("active");
        vj(".gadget-brand-dell").removeClass("active");
        vj(".gadget-brand-hp").removeClass("active");
        vj(".laptop-apple").removeClass("active");
        vj(".laptop-dell").removeClass("active");
        vj(".laptop-hp").removeClass("active");
        vj(".laptop-other").removeClass("active");
    });
    vj(".gadget-brand-list .laptop-samsung").click(function(){
        vj(".laptop-samsung").addClass("active");
        vj(".gadget-brand-samsung").addClass("active");
        vj(".gadget-brand-apple").removeClass("active");
        vj(".gadget-brand-dell").removeClass("active");
        vj(".gadget-brand-hp").removeClass("active");
        vj(".laptop-apple").removeClass("active");
        vj(".laptop-dell").removeClass("active");
        vj(".laptop-hp").removeClass("active");
        vj(".laptop-other").removeClass("active");
    });

   	vj(".help-laptop").click(function(){
        vj(".popup-laptop").fadeIn("slow");
    });
    vj(".new-gadget-close-popup").click(function(){
        vj(".popup-laptop").fadeOut("slow");
    });
    vj(".help-mobile").click(function(){
        vj(".popup-mobile").fadeIn("slow");
    });
    vj(".new-gadget-close-popup").click(function(){
        vj(".popup-mobile").fadeOut("slow");
    });
    vj(".gadget-btn-proceed").click(function(){
        vj(".address-field").addClass("active");
        vj(".address-field textarea").focus();
        vj(".gadget-btn-submit").show();
        vj(".gadget-btn-proceed").hide();
    });

 //    vj(".nav-inner nav > ul > li").hover(
	// 	function () {
	// 		vj(this).addClass("active");
	// 	},
	// 	function () {
	// 		vj(this).removeClass("active");
	// 	}
	// );
	// vj(".nav-inner nav > ul > li > ul > li").hover(
	// 	function () {
	// 		vj(this).addClass("active");
	// 	},
	// 	function () {
	// 		vj(this).removeClass("active");
	// 	}
	// );

	vj(".nav-inner nav > ul > li").mouseover(function(){
	    vj(this).addClass("active");
	    vj(this).children("ul").addClass("active");
	    //vj(".nav-inner nav > ul > li > ul").addClass("active");
	});
	vj(".nav-inner nav > ul > li").mouseleave(function(){
	    vj(this).removeClass("active");
	    vj(this).children("ul").removeClass("active");
	    //vj(".nav-inner nav > ul > li > ul").removeClass("active");
	});
	
});	

vj(document).ready(function(){
	vj( "#tabs" ).tabs();
});
vj(document).ready(function(){
	vj( "#loginregister" ).tabs();
});


	/*vj(document).ready(function(){
		// var time1 = new Date;
		var li = vj("#top-brnads-slider1").find("li");
		var w = li.size() * li.outerWidth();
		//console.log(w);
		vj(".top-brnads-slider").css("width",w*2);
		var _html = vj("#top-brnads-slider1").html();
		vj("#top-brands-slider2").html(_html);
		function autoScroll(){
			var s = vj(".top-brands-container").scrollLeft();
			console.log(s);
			console.log(w);
			if(s >= w){
				vj(".top-brands-container").scrollLeft(0);
			}else{
				vj(".top-brands-container").scrollLeft(s + 1);
			}
		}
		var _scrolling = setInterval(autoScroll, 10);
		vj(".top-brands-container").hover(function(){
			//clearInterval(_scrolling);
			_scrolling = setInterval(autoScroll,10);
		},function(){
			_scrolling = setInterval(autoScroll,10);
		})
	});
	*/
	//auto fill city at sign up added by Mahesh Gurav on 23/08/2017
	/*vj(document).ready(function(){
		vj("[name=pincode]").keyup( function() {
	   		if( this.value.length == 6 ){
	   			var pincode = this.value;
	   			var xhr;
			        if(xhr && xhr.readyState != 4){
			            xhr.abort();
			        }
			        xhr = vj.ajax({
                    'url': "https://www.electronicsbazaar.com/deliveryvalidator/index/checkcity",
                    'type': "POST",
                    'dataType': 'json',
                    'data': {postcode: pincode},
                    success: function (data) {
                        if (data.status == 'ERROR') {
                        } else if (data.status == 'SUCCESS') {
                            vj("#cus_city").val(data.message);
                        }
                    }
                });
	   		} 
		});
	});*/	
