<?php
class SSTech_CancelOrder_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_CANCEL_ORDER    = 'cancelorder/customer_general/enabled';

    public function cancelOrderUrl()
    {
        return $this->_getUrl('cancelorder/index');
    }

    public function checkGuestUser()
    {
    	$enabled = Mage::getStoreConfigFlag(self::XML_PATH_CANCEL_ORDER, Mage::app()->getStore()->getStoreId());
        if($enabled){
            if(Mage::getSingleton('customer/session')->isLoggedIn()){
            	return true;
            }else{
            	return false;
            }
        }else{
        	return false;
        }
    }
}
