var states_ids = [485, 486, 487, 488, 489, 490, 491, 492, 493, 494, 496, 497, 498, 499, 500, 501,
    502, 503, 504, 505, 506, 507, 508, 509, 510, 511, 512, 513, 514, 515, 516, 517, 518, 519, 520];
jQuery(document).ready(function () {
    show_states_customer();
});

function show_states_customer() {
    jQuery("#_accountcus_state option").each(function () {
        if (jQuery.inArray(parseInt(jQuery(this).val()), states_ids) == -1 && jQuery(this).val() != '') {
            jQuery(this).remove();
        }

    });
}