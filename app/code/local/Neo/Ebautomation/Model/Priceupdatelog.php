<?php

class Neo_Ebautomation_Model_Priceupdatelog extends Mage_Core_Model_Abstract
{
    protected function _construct(){

       $this->_init("ebautomation/priceupdatelog");

    }


    public function savePriceUpdateLog(Varien_Event_Observer $observer){
    	$event = $observer->getEvent();
		$product = $event->getProduct();
    	$datetime = Varien_Date::formatDate(time());
		$priceUpdate = array('product_id'=>$product->getId(),
							'user'=>$this->_getUsername(),
							'user_id'=>$this->_getUserId(),
							'price'=>$product->getPrice(),
							'created_at'=>$datetime);

		if ($product && $product->hasDataChanges()) {
			$priceUpdateLog = Mage::getModel('ebautomation/priceupdatelog');
			$priceUpdateLog->setData($priceUpdate);
			$priceUpdateLog->save();
		}
    }


    protected function _getUserId()
    {
        $userId = null;
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $userId = Mage::getSingleton('customer/session')->getCustomerId();
        } elseif (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $userId = Mage::getSingleton('admin/session')->getUser()->getId();
        }

        return $userId;
    }

    protected function _getUsername()
    {
        $username = '-';
        if (Mage::getSingleton('api/session')->isLoggedIn()) {
            $username = Mage::getSingleton('api/session')->getUser()->getUsername();
        } elseif (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $username = Mage::getSingleton('customer/session')->getCustomer()->getName();
        } elseif (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $username = Mage::getSingleton('admin/session')->getUser()->getUsername();
        }

        return $username;
    }

}
	 