jQuery(document).ready(function(){
    //shop gadgets//
    if(jQuery(window).width() > '800')
    {     

	  	
	jQuery('.shop-gadgets .products-grid li.item').mouseover(function () {
	    //jQuery(this).find( ".rating" ).hide();
		jQuery(this).find( ".product-view" ).show();
	    //jQuery(this).find( ".share" ).show();	
		//jQuery(this).find( ".actions" ).show();
	});
	
	/*jQuery('.products_homeslider2 .products-grid li.item').mouseover(function () {
	    jQuery(this).find( ".rating" ).hide();
		jQuery(this).find( ".product-view" ).show();
	    jQuery(this).find( ".share" ).show();	
		jQuery(this).find( ".actions" ).show();
	});*/
	

	
	jQuery('.inner-products-category .products-grid li.item').mouseover(function () {
	    //jQuery(this).find( ".rating" ).hide();
		jQuery(this).find( ".product-view" ).show();
	    //jQuery(this).find( ".share" ).show();	
		//jQuery(this).find( ".actions" ).show();
	});

	jQuery('.shop-gadgets .products-grid li.item').mouseout(function () {
	    //jQuery( this ).find( ".rating" ).show();
		jQuery( this ).find( ".product-view" ).hide();
	    //jQuery( this ).find( ".share" ).hide();	
		//jQuery( this ).find( ".actions" ).hide();	
	});
	
	/*jQuery('.products_homeslider2 .products-grid li.item').mouseout(function () {
	    jQuery( this ).find( ".rating" ).show();
		jQuery( this ).find( ".product-view" ).hide();
	    jQuery( this ).find( ".share" ).hide();	
		jQuery( this ).find( ".actions" ).hide();	
	});*/
	

	
	jQuery('.inner-products-category .products-grid li.item').mouseout(function () {
	    //jQuery( this ).find( ".rating" ).show();
		jQuery( this ).find( ".product-view" ).hide();
	    //jQuery( this ).find( ".share" ).hide();	
		//jQuery( this ).find( ".actions" ).hide();	
	});
    }
    /*jQuery('.shop-gadgets .products-grid li.item').click(function () {
        jQuery( this ).find( ".rating" ).toggle();
            jQuery( this ).find( ".product-view" ).toggle();
        jQuery( this ).find( ".share" ).toggle();	
            jQuery( this ).find( ".actions" ).toggle();
    });*/
    
    jQuery('.products-grid li.item').click(function () {
        //jQuery( this ).find( ".rating" ).toggle();
            jQuery( this ).find( ".product-view" ).toggle();
        //jQuery( this ).find( ".share" ).toggle();	
            //jQuery( this ).find( ".actions" ).toggle();
    });
    
    //jQuery('.product-view').click(function(){
    jQuery(document).on('click', '.product-view', function () {
	var pid = jQuery(this).data("pid");
	jQuery('#ewquickview_overlay').attr('pid',pid);
	//document.getElementById("ewquickview_overlay").click()
	jQuery('#ewquickview_overlay').trigger('click');
    });
    
    /* start of share your popup task code on the list view pages */
    /*jQuery(".link-share").click(function(){
	alert('hello');
      jQuery(this).parents().eq(3).siblings('.share-you-purchase-popup').show();
    });*/
  	
    /*jQuery(".close-icon").click(function(){
      jQuery('.share-you-purchase-popup').hide();
    });*/
    /* end of share your popup task code on the list view pages */


    setTimeout('initialize_recently()', 2000);
});
function initialize_recently() {
  //jQuery('.products_homeslider .products-grid li.item .actions').css('display', 'none'); 
	
	jQuery('.products_homeslider .products-grid li.item').mouseover(function () {
	    //jQuery(this).find( ".rating" ).hide();
		jQuery(this).find( ".product-view" ).show();
	    //jQuery(this).find( ".share" ).show();	
		//jQuery(this).find( ".actions" ).show();
	});  

	jQuery('.products_homeslider .products-grid li.item').mouseout(function () {
	    //jQuery( this ).find( ".rating" ).show();
		jQuery( this ).find( ".product-view" ).hide();
	    //jQuery( this ).find( ".share" ).hide();	
		//jQuery( this ).find( ".actions" ).hide();	
	});	
}