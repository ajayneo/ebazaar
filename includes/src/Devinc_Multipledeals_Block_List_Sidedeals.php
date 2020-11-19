<?php
class Devinc_Multipledeals_Block_List_Sidedeals extends Mage_Catalog_Block_Product_Abstract
{	    
	const STATUS_RUNNING = Devinc_Multipledeals_Model_Source_Status::STATUS_RUNNING;
	const STATUS_DISABLED = Devinc_Multipledeals_Model_Source_Status::STATUS_DISABLED;
	const STATUS_ENDED = Devinc_Multipledeals_Model_Source_Status::STATUS_ENDED;
	const STATUS_QUEUED = Devinc_Multipledeals_Model_Source_Status::STATUS_QUEUED;
	
	//returns the product of the featured deal
    public function getFeaturedDeal() {   	
		if (Mage::getStoreConfig('multipledeals/sidebar_configuration/maindeal_featured')) {
			$currentProduct = Mage::registry('product');		
			$currentProductId = (!isset($currentProduct)) ? 0: $currentProduct->getId();
			$excludeProductIds = array($currentProductId);
			
			$deal = Mage::helper('multipledeals')->getDeal($excludeProductIds);
			if ($deal) {
		    	$product = Mage::getModel('catalog/product')->load($deal->getProductId());				
    	    	$product->setDoNotUseCategoryId(true);			
    	    	$product->setDealQty($deal->getDealQty());
    	    
    	    	return $product;
    	    }
		}
		
		return false;		
    }
    
    public function getItems($loadRecent = false)
    {     	
    	$featuredDealProductId = ($product = $this->getFeaturedDeal()) ? $product->getId() : 0;
		$currentProduct = Mage::registry('product');		
		$currentProductId = (!isset($currentProduct)) ? 0: $currentProduct->getId();
		$excludeProductIds = array($currentProductId, $featuredDealProductId);
		
		//load active deal ids
		$dealCollection = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', array('eq'=>self::STATUS_RUNNING))->addFieldToFilter('product_id', array('nin'=>$excludeProductIds))->setOrder('position', 'ASC')->setOrder('multipledeals_id', 'DESC');	
		$dealIdsString = '';  
		$productIds = array(0);   
		
		if (count($dealCollection)) {    
			$dealIds = array();   
 			foreach ($dealCollection as $deal) {
 				if (Mage::helper('multipledeals')->runOnStore($deal) && !array_key_exists($deal->getProductId(), $dealIds)) {
 					$dealIds[$deal->getProductId()] = $deal->getId();
 					$productIds[] = $deal->getProductId();
 				}
        	}
        	
        	if (count($dealIds)) {
	        	$dealIdsString = implode(',', $dealIds);
	        } else {
		        $dealIdsString = 0;		        
	        }
        } 
        
        if ($dealIdsString!='') {
        	//load product collection
        	$resource = Mage::getSingleton('core/resource');
        	$collection = Mage::getResourceModel('catalog/product_collection')
     		    ->addAttributeToSelect('*')
     	    	->addAttributeToFilter('entity_id', array('in', $productIds))
        	    ->addStoreFilter()
            	->joinTable($resource->getTableName('multipledeals'),'product_id=entity_id', array('multipledeals_id' => 'multipledeals_id', 'position' => 'position', 'deal_qty' => 'deal_qty'), '{{table}}.multipledeals_id IN ('.$dealIdsString.')','left');
        	
        	//set collection order        
    		$collection->setOrder('position', 'ASC')->setOrder('multipledeals_id', 'DESC');
		// ->load() removed
			
        	Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        	Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
			
			foreach ($collection as $product) {
        	   	$product->setDoNotUseCategoryId(true);
        	}
        	 
        	return $collection;
        } else {
        	return array();
        }
    }
	
	//refresh the deal statuses
    public function updateDeals()
    {		
		//Mage::getModel('multipledeals/multipledeals')->refreshDeals();
	}
	
}