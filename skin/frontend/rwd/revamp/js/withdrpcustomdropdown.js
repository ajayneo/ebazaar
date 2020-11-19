jQuery(document).ready(function($) {
selectboxcode();
function selectboxcode(){
	/* Select box */
        // Iterate over each select element
		$('select').each(function() {
		// Cache the number of options
		var id = $(this).attr('id');
		if(id != 'billing:country_id'  && id != 'billing:region_id' && id != 'shipping:region_id' && id != 'shipping:country_id')
		{
			var $this = $(this),
			numberOfOptions = $(this).children('option').length;

			// Hides the select element
			$this.addClass('s-hidden');

			// Wrap the select element in a div
			//$this.wrap('<div class="select seldata"></div>');

			// Insert a styled div to sit over the top of the hidden select element
			$this.after('<div class="styledSelect"></div>');

			// Cache the styled div
			var $styledSelect = $this.next('div.styledSelect');

			// Show the first select option in the styled div
			$styledSelect.text($this.children('option').eq(0).text());

			// Insert an unordered list after the styled div and also cache the list
			var $list = $('<ul />', {
				'class': 'options'
			}).insertAfter($styledSelect);

			// Insert a list item into the unordered list for each select option
			for (var i = 0; i < numberOfOptions; i++) {
				$('<li />', {
					text: $this.children('option').eq(i).text(),
					rel: $this.children('option').eq(i).val()
				}).appendTo($list);
			}

			// Cache the list items
			var $listItems = $list.children('li');

			 // Show the unordered list when the styled div is clicked (also hides it if the div is clicked again)
			$styledSelect.click(function(e) {
				e.stopPropagation();
				$('div.styledSelect.active').each(function() {
					$(this).removeClass('active').next('ul.options').hide();
				});
				$(this).toggleClass('active').next('ul.options').toggle();
			});

			// Hides the unordered list when a list item is clicked and updates the styled div to show the selected list item
			// Updates the select element to have the value of the equivalent option
			$listItems.click(function(e) {
				e.stopPropagation();
				$styledSelect.text($(this).text()).removeClass('active');
				$this.val($(this).attr('rel'));
				$list.hide();
				/* alert($this.val()); Uncomment this for demonstration! */
			});

			// Hides the unordered list when clicking outside of it
			$(document).click(function() {
				$styledSelect.removeClass('active');
				$list.hide();
			});
			}
    });
}
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


jQuery(window).scroll(function(){
   var scroll = jQuery(window).scrollTop();
   if(scroll > 53){
    jQuery(".page-header").addClass('header-fixed');
    jQuery(".header-language-background").fadeOut(100);
	jQuery(".nav-inner").fadeOut(100);

	jQuery(".compare-section").addClass('compare-fixed');
   }else if(scroll == 0){
    //jQuery(".page-header").css("display","block").fadeOut();
	jQuery(".page-header").removeClass('header-fixed');
    jQuery(".header-language-background").fadeIn();
	jQuery(".nav-inner").fadeIn();

	jQuery(".compare-section").removeClass('compare-fixed');
   }
});