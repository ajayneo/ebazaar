<?php
class Neo_Multipledeals_Model_Observer extends Devinc_Multipledeals_Model_Observer {
	
	public function getPrice($observer)
    {
    	$product = $observer->getEvent()->getProduct();
    	$qty = $observer->getEvent()->getQty();
		$helper = Mage::helper('multipledeals');
		$deal = $helper->getDealByProduct($product);
		$currentDateTime = Mage::helper('multipledeals')->getCurrentDateTime(0);
		
		if ($helper->isEnabled() && $deal) {	
			$setPriceForType = array('simple', 'virtual', 'downloadable', 'configurable');	
			if ($currentDateTime>=$deal->getDatetimeFrom() && $currentDateTime<=$deal->getDatetimeTo()) {
				if (in_array($product->getTypeId(), $setPriceForType)) {
					#$price = $this->_applyTierPrice($product, $qty, $deal->getDealPrice());
					$mrp_price = $deal->getDealMrpprice();
					$product->setPrice($mrp_price);	
				} 
			} else {
				Mage::getModel('multipledeals/multipledeals')->refreshDeal($deal);				
			}
		}
	}
	
	public function setCollectionFinalPrice($observer)
    {
    	$products = $observer->getEvent()->getCollection();
		$currentDateTime = Mage::helper('multipledeals')->getCurrentDateTime(0);
		
    	foreach ($products as $product) {
			$helper = Mage::helper('multipledeals');
			$deal = $helper->getDealByProduct($product);
			
			if ($helper->isEnabled() && $deal) {	
				$setPriceForType = array('simple', 'virtual', 'downloadable', 'configurable');	
				if ($currentDateTime>=$deal->getDatetimeFrom() && $currentDateTime<=$deal->getDatetimeTo()) {
					if (in_array($product->getTypeId(), $setPriceForType)) {
						$price = $this->_applyTierPrice($product, null, $deal->getDealPrice());
						$product->setFinalPrice($price);
						$mrp_price = $deal->getDealMrpprice();
						$product->setPrice($mrp_price);	
					}
				} else {
					Mage::getModel('multipledeals/multipledeals')->refreshDeal($deal);				
				}
			}
		}
	}	
}
?>