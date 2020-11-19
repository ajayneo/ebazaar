<?php

class Neo_Gadget_Model_Gadget extends Mage_Core_Model_Abstract
{
    protected function _construct(){

       $this->_init("gadget/gadget");

    }

    public function getPromoPrice($promo_code, $processor){
        $promo_500  = 'gadget/promocode/promo_code_500';
        $promo_1000  = 'gadget/promocode/promo_code_1000';
    	
        switch ($promo_code) {
    		case Mage::getStoreConfig($promo_500):
    			if(!in_array($processor, array('Core i3','Core i5','Core i7'))){
    				$price = 500;
    			}
    			break;
    		
    		case Mage::getStoreConfig($promo_1000):
    			if(in_array($processor, array('Core i3','Core i5','Core i7'))){
    				$price = 1000;
    			}
    			break;

    		default:
    			$price = 0;
    			break;
    	}

        return $price;
    }
}
	 