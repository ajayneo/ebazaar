<?php
class Neo_Customapiv6_ServiceController extends Neo_Customapiv6_Controller_HttpAuthController
{
	//function to show service centres
	public function getservicedataAction(){
		try{
			$city = $this->getRequest()->getParam('city');
			$return = array();
			
			$collection = Mage::getModel('showservicedata/servicecentre')->getCollection();
	        $collection->addFieldToFilter("city",array("like"=>"%".$city."%"));
	       	foreach ($collection as $centre) {

	        	$return[] = $centre->getData();
	        }

	        echo json_encode(array('status' => 1, 'data' => $return));  
			exit;
		}catch(Exception $ex){
			echo json_encode(array('status' => 0, 'message' => $ex->getMessage()));
			exit;
		}
	}
}