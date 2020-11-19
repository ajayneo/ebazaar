PickupAtStore = {
    debug: false,
    resetDays: function() {
        if (PickupAtStore.debug)
            if (PickupAtStore.debug)
                console.log("resetDays")
        $("pickupatstore_days").selectedIndex = 0;
        $("pickupatstore_days").select('OPTION').each(function(o) {
            if (o.value)
                o.hide();
        })
        PickupAtStore.date = null;


    },
    resetHours: function() {
        if (PickupAtStore.debug)
            console.log("resetHours")
        if (PickupAtStore.timeEnabled) {
            $("pickupatstore_hours").selectedIndex = 0;
            $("pickupatstore_hours").select('OPTION').each(function(o) {
                if (o.value)
                    o.hide();
            })
            PickupAtStore.time = null;
        }
    },
    resetAll: function() {
        if (PickupAtStore.debug)
            console.log("resetAll")
        if (PickupAtStore.dateEnabled) {
            PickupAtStore.resetDays();
            if (PickupAtStore.timeEnabled) {
                PickupAtStore.resetHours();
            }
        }
    },
    getStoreIndex: function(id) {
        i = 0;
        places.each(function(p) {

            if (p.id == id) {
                output = i;

            }
            i++;

        })
        return output;
    },
    observer: function() {
        if (PickupAtStore.debug)
            console.log("observer")
        if (PickupAtStore.dropdownEnabled) {
            $('pickupatstore').observe("change", function() {
                if (PickupAtStore.gmapEnabled) {
                    if (PickupAtStore.getStoreId()) {
                        displayStore(PickupAtStore.getStoreIndex(PickupAtStore.getStoreId()))

                    }
                }
                PickupAtStore.update();
            })
        }

        if (PickupAtStore.dateEnabled) {
            $('pickupatstore_days').observe("change", function() {
                if (PickupAtStore.timeEnabled) {
                    PickupAtStore.resetHours();

                    if ($('pickupatstore_days').value) {
                        PickupAtStore.updateHours()
                    }
                   
                }
                else {
                    if (PickupAtStore.getStoreId() == '' || $('pickupatstore_days').value == '') {
                        $$('input[type="radio"][name="shipping_method"]').each(function(el) {
                            el.checked = false;
                        })

                    }
                    else {
                        $('s_method_pickupatstore_' + PickupAtStore.getStoreId()).checked = true;
                    }
                }

            })
            if (PickupAtStore.timeEnabled) {
                $('pickupatstore_hours').observe("change", function(el) {
                    if (PickupAtStore.getStoreId() == '' || $('pickupatstore_hours').getValue() == '' || $('pickupatstore_days').getValue() == '') {
                        $$('input[type="radio"][name="shipping_method"]').each(function(el) {
                            el.checked = false;
                        })
                    }
                    else {
                        $('s_method_pickupatstore_' + PickupAtStore.getStoreId()).checked = true;
                    }
                })
            }

        }


    },
    getStoreId: function() {
        if (PickupAtStore.debug)
            console.log("getStoreId")
        return $('pickupatstore').getValue().replace('pickupatstore_', '');
    },
    setStoreId: function(id) {
        if (PickupAtStore.debug)
            console.log("setStoreId", PickupAtStore)
        $('pickupatstore').setValue('pickupatstore_' + id);
    },
    getData: function() {
        if (PickupAtStore.debug)
            console.log("getData")
        eval("PickupAtStore.datetime =PickupAtStore.store_" + PickupAtStore.getStoreId() + ".datetime");
    },
    update: function() {
        if (PickupAtStore.debug)
            console.log("update")
        $$('input[type="radio"][name="shipping_method"]').each(function(el) {
            el.checked = false;
        })
        PickupAtStore.resetAll();

        if (PickupAtStore.dateEnabled) {

            if (PickupAtStore.getStoreId())
                PickupAtStore.updateDays();

        } else {

            if (PickupAtStore.getStoreId()) {
                $('s_method_pickupatstore_' + PickupAtStore.getStoreId()).checked = true;
            }

        }
    },
    updateDays: function(reset) {
        if (PickupAtStore.debug)
            console.log("updateDays", PickupAtStore)

        if (typeof reset == 'undefined') {
            $$('input[type="radio"][name="shipping_method"]').each(function(el) {
                el.checked = false;
            })
        }
        PickupAtStore.getData()
        $("pickupatstore_days").select('OPTION').each(function(o) {
            if (o.value) {
                if (PickupAtStore.datetime[o.value] && PickupAtStore.datetime != false) {
                    o.show();
                    if (PickupAtStore.debug)
                        console.log(PickupAtStore.date + "=?=" + o.value)
                    if (PickupAtStore.date == o.value)
                        o.selected = true;
                }
                else
                    o.hide();
            }
        })

    },
    updateHours: function(reset) {
        if (PickupAtStore.debug)
            console.log("updateHours")

        if (typeof reset == 'undefined') {
            $$('input[type="radio"][name="shipping_method"]').each(function(el) {
                el.checked = false;
            })
        }
        PickupAtStore.getData()
        if ($('pickupatstore_days').value != '') {
            $("pickupatstore_hours").select('OPTION').each(function(o) {
                if (o.value) {
                    if (PickupAtStore.datetime[ $('pickupatstore_days').value][0] <= o.value && o.value <= PickupAtStore.datetime[ $('pickupatstore_days').value][1]) {
                        o.show();
                        if (PickupAtStore.time == o.value)
                            o.selected = true;
                    }
                    else
                        o.hide();
                }

            })
        }
    }
}

