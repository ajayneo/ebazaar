<?php
class Devinc_Multipledeals_Block_List extends Mage_Catalog_Block_Product_List
{
	const STATUS_RUNNING = Devinc_Multipledeals_Model_Source_Status::STATUS_RUNNING;
	const STATUS_DISABLED = Devinc_Multipledeals_Model_Source_Status::STATUS_DISABLED;
	const STATUS_ENDED = Devinc_Multipledeals_Model_Source_Status::STATUS_ENDED;
	const STATUS_QUEUED = Devinc_Multipledeals_Model_Source_Status::STATUS_QUEUED;
    protected $_dealsCollection;
	
    protected function _prepareLayout()
    {        
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home', array(
                'label'=>Mage::helper('catalog')->__('Home'),
                'title'=>Mage::helper('catalog')->__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
            ));
            
			$breadcrumbsBlock->addCrumb('deals',
			array('label'=>Mage::helper('multipledeals')->__('Active Deals'),
				'title'=>Mage::helper('multipledeals')->__('Active Deals'),)
			);
            
            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle(Mage::helper('multipledeals')->__('Active Deals'));
            }
        }
        
        return parent::_prepareLayout();
    }
    
    public function getLoadedProductCollection($loadRecent = false)
    { 
        if (!$this->_dealsCollection) {
        	$dealStatus = ($loadRecent) ? self::STATUS_ENDED : self::STATUS_RUNNING;    	
        	
        	//load active deal ids
    		$dealCollection = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', array('eq'=>$dealStatus))->setOrder('position', 'ASC')->setOrder('multipledeals_id', 'DESC');
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
            } else {
            	$dealIdsString = 0;
            }
            
            //load product collection + deals info
            $resource = Mage::getSingleton('core/resource');
            $collection = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
         	    ->addAttributeToFilter('entity_id', array('in', $productIds))
                ->addAttributeToFilter('status', array('in'=>array(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)))
                ->addStoreFilter()
                ->joinTable($resource->getTableName('multipledeals'),'product_id=entity_id', array('multipledeals_id' => 'multipledeals_id', 'position' => 'position', 'deal_qty' => 'deal_qty', 'datetime_to' => 'datetime_to'), '{{table}}.multipledeals_id IN ('.$dealIdsString.')','left');
                
            //set collection order        
        	if ($loadRecent) {
        		$collection->setOrder('datetime_to', 'DESC');
        	} else {
            	$collection->setOrder('position', 'ASC')->setOrder('multipledeals_id', 'DESC');
            }   	
    		
    		Mage::getModel('review/review')->appendSummary($collection); 
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
		      
            $this->_dealsCollection = $collection;
        }
        return $this->_dealsCollection;
    }

    protected function _getProductCollection() {
        return $this->getLoadedProductCollection();
    }
    
}