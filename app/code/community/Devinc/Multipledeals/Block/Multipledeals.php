<?php
class Devinc_Multipledeals_Block_Multipledeals extends Mage_Core_Block_Template
{	        
	//returns the current product if it's a deal
    public function getDealProduct()
	{
		//Mage::getModel('multipledeals/multipledeals')->refreshDeals();
		$product = Mage::registry('product');		
		$deal = Mage::helper('multipledeals')->getDealByProduct($product);	
		if ($deal) {
			//increase deal number of views
			$nrViews = $deal->getNrViews();
			$nrViews++;
			$deal->setNrViews($nrViews)->save();
			
			$product->setDealQty($deal->getDealQty());			
			return $product;		
		} else {
			return false;
		}
	}
	
	//returns the product of the main deal
    public function getMainDealProduct() {  
		//Mage::getModel('multipledeals/multipledeals')->refreshDeals(); 
		$mainDeal = Mage::helper('multipledeals')->getDeal();
		
		if ($mainDeal) {
		    $product = Mage::getModel('catalog/product')->load($mainDeal->getProductId());				
    	    $product->setDoNotUseCategoryId(true);
		    return $product;
		} else {
			return false;
		}	
    }
	
	//to bypass Full Page Cache of Magento Enterprise
    public function getCacheKeyInfo()
	{
	    $info = parent::getCacheKeyInfo();
	    if (Mage::registry('product'))
	    {
	        $info['product_id'] = Mage::registry('product')->getId();
	    }
	    return $info;
	}
	
}