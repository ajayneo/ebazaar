<?php
class Neo_Multipledeals_Block_List extends Devinc_Multipledeals_Block_List{
    
    public function getDailyDealsCollection($loadRecent = false)
    {
        //if (!$this->_dealsCollection) {
        	$dealStatus = ($loadRecent) ? self::STATUS_ENDED : self::STATUS_RUNNING;    	
        	
        	//load active deal ids
		$currentdate = Mage::helper('multipledeals')->getCurrentDateTime();
                
		$toDate = date('Y-m-d H:i:s', strtotime("tomorrow 00:00:00"));
		    
            $dealCollection = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', array('eq'=>$dealStatus))->addFieldToFilter('datetime_to',array('lteq'=>$toDate))->setOrder('position', 'ASC')->setOrder('multipledeals_id', 'DESC');
    		//$dealCollection = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', array('eq'=>$dealStatus))->addFieldToFilter('datetime_to',$toDate)->setOrder('position', 'ASC')->setOrder('multipledeals_id', 'DESC');
            //echo "<pre>"; print_r($dealCollection->getData());
            //return count($dealCollection);
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
        //}
        return $this->_dealsCollection;
    }
    
    public function getCountDownfortheDailyDeals(){
        $toDate = date('Y-m-d H:i:s', strtotime("tomorrow 00:00:00"));
        $countdown = Mage::helper('multipledeals')->getCountdown($toDate);
        return $countdown;
    }
    
    public function getCountDownfortheWeeklyDeals(){
        //$toDate = date("Y-m-d H:i:s", strtotime("next sunday", time()));
        $toDate = date("Y-m-d H:i:s", strtotime($this->getFinalCountDown()));;
        $countdown = Mage::helper('multipledeals')->getCountdown($toDate);
        return $countdown;
    }
    
    public function getFinalCountDown(){
        $datetime_final = array();
        $r = $this->getLoadedProductCollection();
        foreach($r as $rq){
            $datetime_final[] = $rq->getDatetimeTo();
        }
        $date_time = array_unique($datetime_final);
        return $date_time['0'];
    }

    public function getWeeklyDealsCollection($loadRecent = false)
    {
echo 'prade'; exit;
        //echo date("Y-m-d H:i:s", strtotime("last sunday", time())); 
        //echo date("Y-m-d H:i:s", strtotime("next sunday", time())); exit;
        //if (!$this->_dealsCollection) {

        	$dealStatus = ($loadRecent) ? self::STATUS_ENDED : self::STATUS_RUNNING;    	
        	
        	//load active deal ids
                $lastmon = date("Y-m-d H:i:s", strtotime("last monday", time()));
                $nextsun = date("Y-m-d H:i:s", strtotime("next sunday", time()));
		$yesterday = date("Y-m-d H:i:s", strtotime("yesterday", time()));
		$tomorrow = date("Y-m-d H:i:s", strtotime("tomorrow", time()));
		$currentdate = Mage::helper('multipledeals')->getCurrentDateTime();
		
		//echo $tomorrow; exit;
                
		//$toDate = date('Y-m-d H:i:s', strtotime("tomorrow 00:00:00"));
    		//$dealCollection = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', array('eq'=>$dealStatus))->addFieldToFilter('datetime_from',array('gteq'=>$lastmon))->addFieldToFilter('datetime_to',array('lteq'=>$nextsun))->setOrder('position', 'ASC')->setOrder('multipledeals_id', 'DESC'); (changed)
            $dealCollection = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', array('eq'=>$dealStatus))->setOrder('position', 'ASC')->setOrder('multipledeals_id', 'DESC');
			//$dealCollection = $dealCollection->addFieldToFilter('datetime_from',array('nlike'=>$currentdate))->addFieldToFilter('datetime_to',array('neq'=>$currentdate));
			
		/*$dealCollection_one = Mage::getModel('multipledeals/multipledeals')->getCollection()
				    ->addFieldToFilter('status', array('eq'=>$dealStatus))
				    ->addFieldToFilter('datetime_from',array('from'=>$lastmon,'date' => true))
				    ->addFieldToFilter('datetime_to',array('to'=>$yesterday,'date' => true))
				    //->addFieldToFilter('datetime_to',array('lteq'=>$yesterday))
				    ->setOrder('position', 'ASC')
				    ->setOrder('multipledeals_id','DESC');
				    //echo $dealCollection_one->getSelect();
		//echo "<pre>"; print_r($dealCollection_one->getData()); exit;
			//echo count($dealCollection_one); exit;
		$dealCollection_two = Mage::getModel('multipledeals/multipledeals')->getCollection()
				    ->addFieldToFilter('status', array('eq'=>$dealStatus))
				    //->addFieldToFilter('datetime_from',array('from'=>$tomorrow,'date' => true))
				    ->addFieldToFilter('datetime_to',array('to'=>$nextsun,'date' => true))
				    ->setOrder('position', 'ASC')
				    ->setOrder('multipledeals_id','DESC');
				    
		$collection1Select = $dealCollection_one->getSelect();
		$collection2Select= $dealCollection_two->getSelect();
		
		$mergedCollection = $collection1Select->union(array($collection2Select));
		
		//echo count($mergedCollection); exit;
		*/		    
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
        //}
        return $this->_dealsCollection;
    }
}
?>
