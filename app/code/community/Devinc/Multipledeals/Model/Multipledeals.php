<?php

class Devinc_Multipledeals_Model_Multipledeals extends Mage_Core_Model_Abstract
{
	const STATUS_RUNNING = Devinc_Multipledeals_Model_Source_Status::STATUS_RUNNING;
	const STATUS_DISABLED = Devinc_Multipledeals_Model_Source_Status::STATUS_DISABLED;
	const STATUS_ENDED = Devinc_Multipledeals_Model_Source_Status::STATUS_ENDED;
	const STATUS_QUEUED = Devinc_Multipledeals_Model_Source_Status::STATUS_QUEUED;
    private $_products = array();
    private $_refreshAll = false;
    private $_refreshCache = false;
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('multipledeals/multipledeals');
    }

	public function refreshDealsCron() {
		if (Mage::getStoreConfig('multipledeals/configuration/refresh_rate')==0) {
			$this->refreshDeals();
		}
	}
	
	//refresh the status of all the deals
	public function refreshDeals($adminStore = false) {
		// if ($adminStore) Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

		$productIds = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', array('in' => array(self::STATUS_RUNNING, self::STATUS_QUEUED)))->getColumnValues('product_id');
		$dealProducts = Mage::getResourceModel('catalog/product_collection')->addAttributeToFilter('entity_id', array('in' => $productIds));
        
		foreach ($dealProducts as $product) {		
			$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);	    		
			$inStock = ($stockItem->getIsInStock()) ? true : false;	
			$this->_products[$product->getId()]['in_stock'] = $inStock;
			$this->_products[$product->getId()]['type_id'] = $product->getTypeId();
		}

		$dealCollection = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', array('in' => array(self::STATUS_RUNNING, self::STATUS_QUEUED)))->setOrder('multipledeals_id', 'DESC');
		if (count($dealCollection)>0) {		
			$this->_refreshAll = true;
			foreach ($dealCollection as $deal) {
			    $this->refreshDeal($deal);
			}		
		}

		if (Mage::getConfig()->getModuleConfig('Enterprise_Enterprise') && $this->_refreshCache) {
			Mage::getSingleton('enterprise_pagecache/cache')->getCacheInstance()->cleanType(Enterprise_PageCache_Model_Processor::CACHE_TAG);
		}
	}	
	
	//updates deal status
	public function refreshDeal(Varien_Object $_deal) {			
		//verify if product is enabled for at least one website
		$productStatus = 2;
		$eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();
	    $statusId = $eavAttribute->getIdByCode('catalog_product', 'status');

	    $resource = Mage::getSingleton('core/resource');
	    $connection = $resource->getConnection('core_read');

	    $select = $connection->select()
		->from($resource->getTableName('catalog_product_entity_int'))
	    ->where('store_id IN (?)', '0,'.$_deal->getStores())
	    ->where('attribute_id = ?', $statusId)
	    ->where('value = ?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
	    ->where('entity_id = ?', $_deal->getProductId());

		$rows = $connection->fetchAll($select);

		if (count($rows)>0) {
			$productStatus = 1;
		}
		
		//verify if product is in stock and if deal qty is higher than 0
		if (!isset($this->_products[$_deal->getProductId()])) {
			$product = Mage::getModel('catalog/product')->load($_deal->getProductId());
			$stockItem = $product->getStockItem();		    		
			$inStock = ($stockItem->getIsInStock()) ? true : false;				
			$typeId = $product->getTypeId();	
		} else {    	
			$inStock = $this->_products[$_deal->getProductId()]['in_stock'];				
			$typeId = $this->_products[$_deal->getProductId()]['type_id'];
		}
		
		if ($attributesArray = $this->checkDealStatus($_deal, $productStatus, $inStock, $typeId)) {	
			if ($attributesArray['disable_product']) {
				//foreach ($storeIds as $storeId) {
				if (Mage::helper('multipledeals')->getMagentoVersion()>=1324) {	
			    	Mage::getSingleton('catalog/product_action')->updateAttributes(array($_deal->getProductId()), array('status' => 2), 0);
			    } else {			    	
					Mage::getModel('catalog/product_status')->updateProductStatus($_deal->getProductId(), 0, 2);
			    }
			    //}
			}			
			$_deal->setStatus($attributesArray['status'])->save();
			$this->_refreshCache = true;
		}	

		if (Mage::getConfig()->getModuleConfig('Enterprise_Enterprise') && !$this->_refreshAll && ($this->_refreshCache || $_deal->getDealJustSaved())) {
			Mage::getSingleton('enterprise_pagecache/cache')->getCacheInstance()->cleanType(Enterprise_PageCache_Model_Processor::CACHE_TAG);
		}
	}
	
	//compare original deal status with current to see if it requires refresh
	//note: a deal's status changes depending on the deal's datetime from/to, product's status and stock
	public function checkDealStatus(Varien_Object $_deal, $_productStatus, $_inStock, $_productTypeId) {
		$origDealStatus = $_deal->getStatus();
					
		// get store datetime
		$helper = Mage::helper('multipledeals');
		$currentDateTime = $helper->getCurrentDateTime(0);
		
		$dealStatus = self::STATUS_ENDED;
		//check if disabled
		if ($_productStatus!=2 && $_inStock && $_deal->getStatus()!=self::STATUS_DISABLED) {
		    //check if running && if deal is still in stock
		    if ($currentDateTime>=$_deal->getDatetimeFrom() && $currentDateTime<=$_deal->getDatetimeTo()) {
				$dealQtyValidationTypes = array('simple', 'virtual', 'downloadable');	
					
		    	if (in_array($_productTypeId, $dealQtyValidationTypes) && $_deal->getDealQty()>0) {					
		    		$dealStatus = self::STATUS_RUNNING;
		    	} else if (!in_array($_productTypeId, $dealQtyValidationTypes)) {
		    		$dealStatus = self::STATUS_RUNNING;
		    	} else {
		    		$dealStatus = self::STATUS_ENDED;
		    	}
		    //check if queued
		    } elseif ($currentDateTime<=$_deal->getDatetimeFrom()) {							
		    	$dealStatus = self::STATUS_QUEUED;
		    //check if ended
		    } elseif ($currentDateTime>=$_deal->getDatetimeTo()) {
		    	$dealStatus = self::STATUS_ENDED;
		    } 
		} else {
		    $dealStatus = self::STATUS_DISABLED;
		}	
		
		//verify if product needs to be disabled
		$disableProduct = false;
		if (($currentDateTime>=$_deal->getDatetimeTo() || $_deal->getStatus()==self::STATUS_DISABLED) && $_deal->getDisable()==2 && $_productStatus==1) {
			$disableProduct = true;
			$dealStatus = self::STATUS_DISABLED;
		}
		
		if ($origDealStatus==$dealStatus && !$disableProduct) {
			return false;
		} else {
			return array('status' => $dealStatus, 'disable_product' => $disableProduct);
		}
	}
	
	//returns the main store id of each website
	public function getMainStoreIds($includeAdmin = true) {	
		$storeIds = array();
		if ($includeAdmin) {
			$storeIds = array(0);
		}
		
		$websiteCollection = Mage::getModel('core/website')->getCollection();				
		if (count($websiteCollection)) {	
		    foreach ($websiteCollection as $website) 
		    {	
		    	$storeId = Mage::getModel('core/store')->getCollection()->addFieldToFilter('website_id', $website->getId())->addFieldToFilter('is_active', 1)->setOrder('store_id', 'asc')->getFirstItem()->getId();
		    	//verify if at least one store is active from the website
		    	if ($storeId) {
			    	$storeIds[] = $storeId;
			    }
		    }		
		}
		
		return $storeIds;
	}
	
	//returns a list of all the active store views
	public function getStoreViews() {		
		$storeViews = array();
		$websiteCollection = Mage::getModel('core/website')->getCollection();	
		$i = 0;
			
		if (count($websiteCollection)) {	
		    foreach ($websiteCollection as $website) 
		    {	
		    	$storeCollection = Mage::getModel('core/store')->getCollection()->addFieldToFilter('website_id', $website->getId())->addFieldToFilter('is_active', 1)->setOrder('store_id', 'desc');
		    	if (count($storeCollection)) {	
		    		foreach ($storeCollection as $store) 
		    		{
		        		$storeViews[$i]['value'] = $store->getId();
		        		$storeViews[$i]['label'] = $store->getName();	
		        		$i++;		    		
		        	}
		        }
		    }		
		}		
		
		return $storeViews;
	}
}