<?php
    class Neo_CustomFields_Model_Selectstates extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {
    	
		public function getAllOptions($withEmpty = true)
	    {
	    	$collection = Mage::getModel('directory/region')->getResourceCollection()
				->addCountryFilter('IN')
				->load()
				->toOptionArray(false);
				
				$options= array();
				
				foreach($collection as $states){
					 array_push($options,array('label' => $states['label'],'value' => $states['value']));
				}
	        
	        if ($withEmpty) {
	            array_unshift($options, array('label' => '', 'value' => ''));
	        }
	
	        return $options;
	    }
    	
	}
?>