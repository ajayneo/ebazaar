<?php
class Neo_CustomBestseller_Block_Loginprice extends Mage_Core_Block_Template
{
    public function __construct(){
    	$app = '';
    	if(Mage::getSingleton('customer/session')->isLoggedIn()){
			$this->app =  "in".$this->getData('itemid');
		}else{
			$this->app.$this->getItemid();
		}
		return $this->$app;
    }

    public function setProductId($id)
    {
    	return $this->getData('itemid');
    	// $this->$id = $id;
    }

    public function getProductId()
    {
    	return $this->getData('itemid');
    	// return $this->$id;
    }
}