<?php
class Neo_Custom_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }

	public function checkPincodeStateAction(){
		
		$response = array();
		$availableflag = false;		

		$data = 'Available in this location.';
		
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$pincode = $this->getRequest()->getParam('pincode');
		
		$query = 'SELECT entity_id,state_code FROM city_pincodes WHERE pincode="'.$pincode.'"';
		$result = $connection->fetchRow($query);		
		if(isset($result['entity_id'])){
			$availableflag = true;
		}

		if($availableflag){				
			$query_region = 'SELECT region_id,default_name FROM directory_country_region WHERE code="'.$result['state_code'].'"';		
			$result_region = $connection->fetchRow($query_region);		
			$response['status'] = $result_region['region_id'].'$$$$'.$result['state_code'].'$$$$'.$result_region['default_name'];			
		}else{
			$response['status'] = 'ERROR';
		}	
		/* Return State Id & State Code */
		echo $response['status'];
    }
	
	public function checkPincodeStateCitiesAction(){
		$response = array();
		$availableflag = false;		

		$data = 'Available in this location.';
		
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$pincode = $this->getRequest()->getParam('pincode');
		
		$query = 'SELECT city FROM city_pincodes WHERE pincode="'.$pincode.'"';
		$result = $connection->fetchRow($query);			
		
		if(isset($result['city'])){
			$availableflag = true;
		}

		if($availableflag){				
			$response['status'] = $result['city'];
		}else{
			$response['status'] = 'ERROR';
		}	
		/* Return City Name */
		echo $response['status'];
	}    
}