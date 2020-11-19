jQuery(document).ready(function(){
    // Recently Viewed Products on the product detail page
    jQuery('#recently-viewed-items').bxSlider({
	auto: false,
	pager: false,
	slideWidth: 170,
	slideMargin:28,
	controls: true,
	infiniteLoop:true,
	minSlides: 1,
	maxSlides: 4,
	moveSlides: 1,
	preloadImages:'all'
    });
});