<?php
class SSTech_CancelOrder_Model_Observer
{
    public function checkstatus(Varien_Event_Observer $observer) {
        $params = Mage::app()->getRequest()->getParam('groups');
        $configValue = $params['customer_general']['fields']['enabled']['value'];
        if ($configValue == 0) {
            $trackOrderConfig = new Mage_Core_Model_Config();
            $trackOrderConfig->saveConfig('cancelorder/customer_general/toplinks', "0");
        }        
    }
}
