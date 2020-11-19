<?php
	class Neo_Customblocks_Block_Customer extends Mage_Core_Block_Template{
		
		public function getCountries(){
			$countryList = Mage::getResourceModel('directory/country_collection')
							->loadData()
                    		->toOptionArray(false);
							
			return $countryList;
		}
		
		public function getCountryRegions(){
           $collection = Mage::getModel('directory/region')->getResourceCollection()
				->addCountryFilter('IN')
                //->addRegionIdFilter($states_ids)

				->load()
                //->addFieldToFilter('region_id', array('in' => $states_ids))
				->toOptionArray(false);
							
			return $collection;
		}
	}
?>