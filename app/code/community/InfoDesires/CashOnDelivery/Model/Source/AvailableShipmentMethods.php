<?php
class InfoDesires_CashOnDelivery_Model_Source_AvailableShipmentMethods
{
    public function toOptionArray()
    {
        $options =  array();

        foreach (Mage::app()->getStore()->getConfig('carriers') as $code => $carrier) {            
            if ($carrier['active'] && isset($carrier['title'])){
                $options[] = array(
                    'value' => $code,
                    'label' => $carrier['title']
                );
            }
        }
        return $options;
    }
}