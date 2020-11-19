<?php

class Wyomind_Pointofsale_Block_Adminhtml_Manage_Edit_Tab_Informations extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();

        $this->setForm($form);

        $fieldset = $form->addFieldset('informations_store', array('legend' => Mage::helper('pointofsale')->__('General Informations')));

        $model = Mage::getModel('pointofsale/pointofsale');
        $model->load($this->getRequest()->getParam('place_id'));



        if ($this->getRequest()->getParam('place_id')) {

            $fieldset->addField('place_id', 'hidden', array(
                'name' => 'place_id',
            ));
        }

        $fieldset->addField('store_code', 'text', array(
            'label' => Mage::helper('pointofsale')->__('Code (internal use)'),
            'name' => 'store_code',
            'class' => 'required-entry',
            'required' => true,
        ));

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('pointofsale')->__('Name'),
            'name' => 'name',
            'class' => 'required-entry',
            'required' => true,
        ));


        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('pointofsale')->__('Type of display'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('pointofsale')->__('Warehouse (not visible on the Gmap/checkout)'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('pointofsale')->__('Point of Sales (visible on the Gmap/checkout)'),
                ),
            ),
        ));
        $fieldset->addField('order', 'text', array(
            'label' => Mage::helper('pointofsale')->__('Order of display'),
            'name' => 'order',
            'class' => 'required-entry validate-number',
            'required' => true,
        ));



        $fieldset->addField('latitude', 'text', array(
            'label' => Mage::helper('pointofsale')->__('Latitude'),
            'class' => 'validate-number',
            'name' => 'latitude',
            'class' => 'required-entry validate-number',
            'required' => true,
        ));

        $fieldset->addField('longitude', 'text', array(
            'label' => Mage::helper('pointofsale')->__('Longitude'),
            'class' => 'validate-number',
            'name' => 'longitude',
            'class' => 'required-entry validate-number',
            'required' => true,
            'after_element_html' => '  
                <div style="margin:10px  0px ;"><b>Find the coordinates with Google Map:</b> </div>
                <div id="map" style="margin:10px  0px ; height:500px; width:700px; border :1px solid grey; padding:1px; z-index:1000000;background:#efefef">
                    
                  
                </div>

                <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true"></script>
                <script>
                    function initialize() {

                        var latitude = document.getElementById(\'latitude\').value;
                        var longitude = document.getElementById(\'longitude\').value;
                        if(latitude=="")latitude="48.856951";
                        if(longitude=="") longitude="2.346868";
                       
                        
                        var zoom = 10;

                        var LatLng = new google.maps.LatLng(latitude, longitude);

                        var mapOptions = {
                            zoom: zoom,
                            center: LatLng,
                            panControl: false,
                            scaleControl: true,
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        }

                        var map = new google.maps.Map(document.getElementById(\'map\'), mapOptions);

                        var marker = new google.maps.Marker({
                            position: LatLng,
                            map: map,
                            title: \'Drag Me!\',
                            draggable: true
                        });

                        google.maps.event.addListener(marker, \'dragend\', function(marker) {
                            var latLng = marker.latLng;

                           document.getElementById(\'longitude\').value=latLng.lng().toFixed(6);
                           document.getElementById(\'latitude\').value=latLng.lat().toFixed(6);
                        });

                    }
                    document.observe("dom:loaded", setTimeout(function(){initialize()},1500));
                </script>'
                // 'after_element_html' => "<a href='http://www.wyomind.com/services/point-of-sales.html' target='_blank'>" . Mage::helper('pointofsale')->__('Find the latitude and longitude of a point on a map.') . "</a>"
        ));


        if (Mage::getSingleton('adminhtml/session')->getPointofsalePlaceData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPointofsalePlaceData());
            Mage::getSingleton('adminhtml/session')->getPointofsalePlaceData(null);
        } elseif (Mage::registry('pointofsale_data') && $this->getRequest()->getParam('place_id')) {
            $form->setValues($model);
            $collection = Mage::getModel('pointofsale/pointofsale')->getPlace($this->getRequest()->getParam('place_id'));
        }

        return parent::_prepareForm();
    }

}
