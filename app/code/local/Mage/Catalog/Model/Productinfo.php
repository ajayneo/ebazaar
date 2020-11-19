<?php
class Mage_Catalog_Model_Productinfo extends Mage_Catalog_Model_Abstract
{
    public function getProductSoldQty($product){
        $orderItems = Mage::getResourceModel('sales/order_item_collection')
        ->addFieldToSelect(array('sku'))
        ->addFieldToFilter('sku', $product->getSku());
        $orderItems->getSelect()->columns('SUM(qty_ordered) as total_qty_sold');
        return $orderItems->getData();
    }

    //validate customer for view IBM category and product
    public function validateIBMCustomer(){
		//check if customer is IBM or not
		if(!Mage::getSingleton('customer/session')->isLoggedIn()){
        	// exit('not logged in');
        	Mage::getSingleton('core/session')->addNotice('Customer needs to Login for viewing IBM products');

	        $url = Mage::getUrl('customer/ibm/login');//eg to redirect to cart page
			//get the http response object

			$response = Mage::app()->getFrontController()->getResponse();
			//set the redirect header to the response object

			$response->setRedirect($url);
			//send the response immediately and exit since there is nothing more to do with the current execution
			$response->sendResponse();
			
	    }else{
	    	// exit('logged in');
	    	$group_id = Mage::getSingleton('customer/session')->getCustomerGroupId();
	    	if($group_id !== "10"){
	    		// Mage::getSingleton('core/session')->addNotice('Loggedin Customer of Group IBM only allowed to view IBM products');

		        $url = Mage::getUrl('/');//eg to redirect to cart page
				//get the http response object

				$response = Mage::app()->getFrontController()->getResponse();
				//set the redirect header to the response object

				$response->setRedirect($url);
				//send the response immediately and exit since there is nothing more to do with the current execution
				$response->sendResponse();
	    	}
	    }

    }
}
