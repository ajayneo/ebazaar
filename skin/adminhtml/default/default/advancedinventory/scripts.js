var InventoryManager = {
    /* @changeAssignation
     
     * Changer l'assignation des commandes
     
     *  orderid (int) id de la commande
     
     *  to (int) id de du nouvel inventaire
     
     *  url (string) url de traitement
     
     */

    changeAssignation: function(orderId, to, url) {

        new Ajax.Request(url, {
            method: 'post',
            parameters: {
                id: orderId,
                to: to

            },
            onFailure: function() {

                alert('An error occurred while saving the data.');

            },
            onSuccess: function(response) {



            }



        });

    },
    /* @save
     
     * Enregistrement des stocks depuis la grid pour un produit ou pour tous
     
     * Renvoie l'id du stock local de chaque produit
     
     * url (string)  url de traitement
     
     * [id (int)] id du produit à traiter
     
     */



    save: function(url, id) {

        data = $('localStocks_form').serialize(true);

        data.id = id;



        if (id != "all")
            this.resetActions(id)



        new Ajax.Request(url, {
            method: 'post',
            parameters: data,
            onFailure: function() {

                alert('An error occurred while saving the data.');



            },
            onSuccess: function(response) {



                data = response.responseText.evalJSON();



                if (typeof data != 'object')
                    alert('An error occurred while saving the data.');

                data.each(function(d) {



                    $('inventory_' + d.product_id + '_local_stock_id').value = d.stock_id;

                    d.inventories.each(function(s) {

                        $('inventory_' + d.product_id + '_local_stock_' + s.place_id + '_stock_id').value = s.stock_id;

                    })

                })

            }



        });





    },
    /* @recalculate
     
     * Recalcule le stock onlin à partir de la somme des stock locaux
     
     * [id (int)] id du produit à traiter
     
     */

    recalculate: function(id) {

        if (typeof id != 'undefined') {

            localstock_qty = this.getLocalQty(id)

            this.setOnlineQty(id, localstock_qty);

            this.updateStocks(id, false)

            this.resetActions(id);

        }

        else {
            $("stocksGrid_table").select('TBODY input[type=checkbox]').each(function(s) {
                if (s.checked) {

                    id = s.value;

                    if ($$('.local_stock_qty_' + id + ':enabled').length > 0) {

                        localstock_qty = InventoryManager.getLocalQty(id)

                        InventoryManager.setOnlineQty(id, localstock_qty);

                        InventoryManager.updateStocks(id, false)

                    }

                }



            })

        }

    },
    /* @resetActions
     
     * Remmetre à zéro le select des actions du produit courant
     
     * id (int) id du produit à traiter
     
     */

    resetActions: function(id) {

        if (this.getActions(id)) {

            action = this.getActions(id);

            action.selectedIndex = 0;

        }



    },
    /* @getActions
     
     * récupérer le select des actions
     
     * id (int) id du produit à traiter
     
     */

    getActions: function(id) {

        return $("online_stock_qty_" + id).ancestors()[1].select('.last SELECT')[0];

    },
    /* @shareLocalStock
     
     * Distribuer le stock online dans les stocks locaux
     
     * id (int) id du produit à traiter
     
     * input (object) object inputhtml du champs courant
     
     */

    shareLocalStock: function(id) {

        count = $$('.local_stock_qty_' + id).length;

        value = $("online_stock_qty_" + id).value / count;

        i = 0;

        total = 0;

        $$('.local_stock_qty_' + id).each(function(e) {

            e.value = Math.floor(value);

            if (i == count - 1)
                e.value = $("online_stock_qty_" + id).value - total

            total += Math.floor(value);

            i++;

        })

    },
    /* @displayMultiInventory
     
     * Appliquer les local stocks dans la fiche article
     
     * id (int) id du produit à traiter
     
     */

    displayMultiInventory: function(id, load) {

        if ($("inventory_manage_local_stock").value != 1) {

            $$('.local-inventory').each(function(s) {

                s.addClassName("hidden")

            })

            $$('.local_stock_qty_' + id).each(function(s) {

                s.disabled = true;

            })

            $('difference').hide()

            $("online_stock_qty_" + id).disabled = false;

        }

        else {

            $$('.local-inventory').each(function(s) {

                s.removeClassName("hidden")

            })

            $$('.local_stock_qty_' + id).each(function(s) {

                s.disabled = false;

            })

            if (InventoryManager.config.AutoRecalculate && InventoryManager.config.OnlineStockfrozen && !InventoryManager.config.LocalStockActive)
                this.shareLocalStock(id)

            $('difference').show()

            if (InventoryManager.config.OnlineStockfrozen)
                $("online_stock_qty_" + id).disabled = true;

            this.updateStocks(id, load)

        }



    },
    /* @enableLocalStocks
     
     * Activation automatique de tous les stock locaux
     
     */

    enableAllLocalStocks: function() {
        $("stocksGrid_table").select('TBODY input[type=checkbox]').each(function(s) {
            if (s.checked) {
                InventoryManager.displayLocalStocks(s.value, true)
            }
        })
    },
    /* @displayLocalStocks
     
     * Activer le suivi des stock locaux pour un produit
     
     * id (int) id du produit à traiter
     
     * enable (bool) acivation du suivi de stock local
     
     */

    displayLocalStocks: function(id, enable) {



        this.enableOnlineStock(id, enable)

        this.enableLocalStocks(id, enable)

        this.updateStocks(id, false)



        if (!enable)
            action = "true";
        else
            action = "false";

        if (!enable)
            msg = this.enableMsg;
        else
            msg = this.disableMsg;



        actionEnable = this.getOption(this.getActions(id), 'enable');



        if (actionEnable) {



            actionEnable.value = '{"id":"enable","href":"javascript:InventoryManager.displayLocalStocks(' + id + ',' + action + ')"}';

            actionEnable.update(msg);

        }

        this.resetActions(id)

        this.enableSynchronize(id, enable);

    },
    getOption: function(select, option) {

        rtn = false;

        select.select('OPTION').each(function(o) {



            if (o.value != '' && o.value.evalJSON().id == option) {



                rtn = o;

            }

        });

        return rtn;

    },
    /* @displayLocalStocks
     
     * Activer le suivi des stock locaux pour u produit
     
     * id (int) id du produit à traiter
     
     * enable (bool) acivation du suivi de stock local
     
     */

    enableSynchronize: function(id, enable) {



        synchronize = this.getOption(this.getActions(id), 'synchronize');

        if (synchronize) {

            if (!enable)
                synchronize.hide();

            else
                synchronize.show();

        }

    },
    /* @enableOnlineStock
     
     * Activer la synchronisation du stock onlin et stocks locaux
     
     * id (int) id du produit à traiter
     
     * enable (bool) acivation du suivi de stock local
     
     */

    enableOnlineStock: function(id, enable) {

        if (InventoryManager.config.AutoRecalculate && InventoryManager.config.OnlineStockfrozen)
            this.shareLocalStock(id)

        if (enable && this.config.OnlineStockfrozen) {

            $("online_stock_qty_" + id).hide();

            $("foo_online_stock_qty_" + id).show();

        }

        else {

            $("online_stock_qty_" + id).show();

            $("foo_online_stock_qty_" + id).hide();

        }

    },
    /* @enableLocalStocks
     
     * Affichage du stock local
     
     * id (int) id du produit à traiter
     
     * enable (bool) acivation du suivi de stock local
     
     */

    enableLocalStocks: function(id, enable) {

        if (enable) {

            $$('.local_stock_qty_' + id).each(function(e) {

                e.show();

                if (!e.hasClassName('disabled'))
                    e.disabled = false

            })

            $$('.foo_local_stock_qty_' + id).each(function(e) {

                e.hide()

            })



        }

        else {

            $$('.local_stock_qty_' + id).each(function(e) {

                e.hide();

                e.disabled = true

            })

            $$('.foo_local_stock_qty_' + id).each(function(e) {

                e.show()

            })



        }

        if (enable)
            enable = '1';

        else
            enable = '0';

        $('manage_local_stock_' + id).value = enable

    },
    /* @updateStocks
     
     * Mettre à jour les valeurs des différent stock suite à une modification
     
     * id (int) id du produit à traiter
     
     * input (object) object inputhtml du champs courant
     
     */

    updateStocks: function(id, load) {

        if ($$('.local_stock_qty_' + id + ':enabled').length > 0)
            enable = true;

        else
            enable = false;


        if (this.config.AutoRecalculate && this.config.OnlineStockfrozen && enable && !load) {

            this.setOnlineQty(id, this.getLocalQty(id));

        }

        this.updateDifference(this.getOnlineQty(id), this.getLocalQty(id), id, enable)

        if ($('foo_total_local_stock_qty_' + id)) {

            if (enable)
                $('foo_total_local_stock_qty_' + id).update(this.getLocalQty(id));

            else
                $('foo_total_local_stock_qty_' + id).update('-');

        }

    },
    /* @setLocalQty
     
     * Obtenir la somme des stock locaux
     
     * id (int) id du produit
     
     */

    getLocalQty: function(id) {

        stock = 0;

        $$('.local_stock_qty_' + id).each(function(e) {

            stock += parseNumber(parseNumber(e.value));

        })

        return stock;



    },
    /* @setOnlineQty
     
     * Modifier la qty du stock online
     
     * id (int) id du produit
     
     * value (float) valeur à affecter
     
     */

    setOnlineQty: function(id, value) {

        $('online_stock_qty_' + id).value = value;

        $('foo_online_stock_qty_' + id).update(value);



    },
    /* @getLocalQty
     
     * Obtenir la somme des stock locaux
     
     * id (int) id du produit
     
     */

    getOnlineQty: function(id) {

        return $('online_stock_qty_' + id).value;

    },
    /* @evalQty
     
     * Evaluer si une quantité saisie est valide ou non
     
     * Stockage de la dernière valeur valide
     
     * input (object) inputhtml object
     
     */

    evalQty: function(input) {



        input.value = eval('parseNumber(input.value)');

        if (isNaN(input.value) && input.value != '') {

            input.value = input.retrieve('ini');

            return false

        }

        else {



            input.store('ini', input.value);

            return true

        }

    },
    /* @updateDifference
     
     * récupérer la difference enter le onlin et le local stock
     
     * Stockage de la dernière valeur valide
     
     * onlinestock_qty (float) valeur du stock online
     
     * localstock_qty (float) valeur du stock local
     
     * id (int) id du produit
     
     */

    updateDifference: function(onlinestock_qty, localstock_qty, id, enable) {

        if (enable) {

            difference = parseNumber(onlinestock_qty) - parseNumber(localstock_qty);



            if (difference > 0) {

                $("stock_difference_" + id).setStyle({
                    color: 'red'

                });

                difference = "<b style='font-size:16px;'>> </b>  (+" + difference + ")";

            }

            else if (difference == 0) {

                $("stock_difference_" + id).setStyle({
                    color: 'green'

                });

                difference = '==';

            }



            else {

                $("stock_difference_" + id).setStyle({
                    color: 'orange'

                });

                difference = "<b style='font-size:16px;'>< </b>  (" + difference + ")";

            }

        }

        else {

            $("stock_difference_" + id).setStyle({
                color: 'black'

            });

            difference = '-';

        }

        $("stock_difference_" + id).update(difference);

    },
    /* @keydown
     
     * increment decrement d'une valeur de stock
     
     * input (e) inputhtml object
     
     */

    keydown: function(e) {



        if (e.findElement('INPUT.keydown')) {

            if (e.keyCode == 38)
                e.findElement('INPUT').value = parseNumber(e.findElement('INPUT').value) + 1;

            if (e.keyCode == 40)
                e.findElement('INPUT').value = parseNumber(e.findElement('INPUT').value) - 1;
            if (e.keyCode == 13) {
                select = e.findElement('TR').select('SELECT.action-select')[0];
                action = InventoryManager.getOption(select, 'save').value.evalJSON();
                eval(action.href.replace('javascript:', ''))

            }

            if (e.findElement('INPUT').readAttribute('onChange'))
                this.evalEvent(e.findElement('INPUT'), 'onChange');

        }

    },
    /* @evalEvent
     
     * increment decrement d'une valeur de stock
     
     * object (elt) html element object
     
     * string (event) déclencheur
     
     */

    evalEvent: function(elt, event) {

        eval(elt.readAttribute(event).replace('this', "elt"))

    }

}



document.observe('dom:loaded', function() {



    if ($("stocksGrid_table")) {

        $("stocksGrid_table").select('.online_stock_qty').each(function(s) {

            if (InventoryManager.config.AutoRecalculate && InventoryManager.config.OnlineStockfrozen)
                enable = false;

            else {



                if ($$('.local_stock_qty_' + s.readAttribute('productid') + ':not(:disabled)').length > 0)
                    enable = true;

                else
                    enable = false;



            }

            InventoryManager.enableSynchronize(s.readAttribute('productid'), enable)

        })

    }

    else {

        $('inventory_qty').addClassName('keydown')



        $('inventory_qty').writeAttribute('onchange', "InventoryManager.updateStocks(" + InventoryManager.config.ProductId + "),false");



        foo = Builder.node("span", {
            id: "foo_online_stock_qty_" + InventoryManager.config.ProductId,
            style: "visibility:hidden"

        })

        $('inventory_qty').insert({
            after: foo

        })

        $('inventory_qty').id = "online_stock_qty_" + InventoryManager.config.ProductId

        InventoryManager.displayMultiInventory(InventoryManager.config.ProductId, true)





    }

    document.observe('click', function(e) {

        if (e.findElement('BUTTON.save')) {

            $("online_stock_qty_" + InventoryManager.config.ProductId).disabled = false

        }

    })

    document.observe('keydown', function(e) {

        if (e.findElement('INPUT.keydown')) {

            InventoryManager.keydown(e)

        }



    });

    document.observe('keyup', function(e) {

        if (e.findElement('INPUT.keydown')) {

            if (e.findElement('INPUT').readAttribute('onChange'))
                InventoryManager.evalEvent(e.findElement('INPUT'), 'onChange');



        }

    });

})



