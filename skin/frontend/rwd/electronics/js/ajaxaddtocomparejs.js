jQuery(document).ready(function () {
    var product;
    jQuery(document.body).on("click", ".remove-product", function () {
        //jQuery('.remove-product').on( "click", function() {
        var a = jQuery(this).parent('li');
        product = a.attr('class');
        removeCompareProduct(a, product);

    });
    jQuery(document.body).on("click", ".remove-compare", function () {
//jQuery('.remove-compare').on( "click", function() {
        jQuery('.compare-section').fadeOut('slow');
    });


});

function removeCompareProduct(a, product) {

    a.remove();
    var product_id = '#products_' + product;
    jQuery(product_id).attr('checked', false);

    jQuery.ajax({
        url: '/customblocks/index/removepro',
        type: 'POST',
        dataType: 'json',
        data: {product: product},
        success: function (data) {
            //console.log(data);
            if (data.status == 'empty') {
                jQuery('.compare-section').fadeOut('slow');
            }
        }
    })
}

function ajaxCompare(url, id, current_elm) {


    var product_id = '#products_' + id;
    var with_check = true;
    var add_to_compare = false;
    var hasPopup = false;
    //console.log(product_id);
    if (typeof current_elm.data('haspopup') === 'undefined') {
        hasPopup = false;
    }
    else {
        hasPopup = true;
    }

    if (typeof jQuery(product_id) === 'undefined') {
        //alert(jQuery(product_id).attr('checked'));
        with_check = false;
    }

    if (with_check) {
        if (jQuery(product_id).attr('checked')) {
            jQuery(product_id).prop('checked', false);
            jQuery(product_id).removeAttr('checked');
            add_to_compare = false;
            if (hasPopup)
                current_elm.html('Add To Compare');
        }
        else {
            jQuery(product_id).attr('checked', true);
            jQuery(product_id).prop('checked', true);

            add_to_compare = true;
        }
    }
    else {
        add_to_compare = true;
    }
//alert(add_to_compare);
//alert(jQuery(product_id).attr('checked'));
    if (add_to_compare) {

        url = url.replace("catalog/product_compare/add", "customblocks/index/compare");
        url += 'isAjax/1/';
        jQuery('.opc-ajax-loader').css('display', 'block');
        jQuery.ajax({
            url: url,
            dataType: 'json',
            success: function (data) {
                jQuery('.opc-ajax-loader').css('display', 'none');
                if (data.status == 'ERROR') {
                    jQuery(product_id).attr('checked', false);
                    jQuery(product_id).removeAttr('checked');
                    alert(data.message);
                } else if (data.status == 'SameCatError') {
                    jQuery(product_id).attr('checked', false);
                    jQuery(product_id).removeAttr('checked');
                    alert(data.message);
                } else {
                    if (jQuery('.ajaxcompare').length) {
                        jQuery('.ajaxcompare').replaceWith(data.sidebar);
                        jQuery(".compare-section").addClass('compare-fixed');
                        if (hasPopup) {
                            current_elm.html('Remove From Compare');
                            alert(data.message);
                        }
                    }

                }
            }
        });
    }
    else {
        var a = jQuery("." + id);
        var product = id;
        removeCompareProduct(a, product);
    }
}
