<?php
class Sunarc_Splitorder_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'splitorder/options/enable';
    public function isEnabled($storeId = null)
    {
        if (!$storeId) {
            $storeId = Mage::app()->getStore()->getId();
        }
        return Mage::getStoreConfig(self::XML_PATH_ENABLED, $storeId);
    }
}
     
