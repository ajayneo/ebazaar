<?php
    class Neo_CustomFields_Model_Selectoptions extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {
    	
		public function getAllOptions($withEmpty = true)
	    {
	    	$countryList = Mage::getResourceModel('directory/country_collection')
                    ->loadData()
                    ->toOptionArray(false);
				$options= array();
				
				foreach($countryList as $country){
					 array_push($options,array('label' => $country['label'],'value' => $country['value']));
				}
	        
	        if ($withEmpty) {
	            array_unshift($options, array('label' => '', 'value' => ''));
	        }
	
	        return $options;
	    }
    	
	}
?>