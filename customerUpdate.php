<?php

require_once('app/Mage.php');
Mage::app();

$customers = Mage::getModel('customer/customer')->getCollection();//->addFieldToSelect('entity_id');
		$customers = $customers->addFieldToFilter('entity_id',39580);
		$customers = $customers->getColumnValues('entity_id');
		$chucked_customers = array_chunk($customers,1000);
		//print_r($chucked_customers);die;
		foreach ($chucked_customers as $chucked_customer) {
			$start = microtime(true);
			foreach ($chucked_customer as $key => $customerId) {
				$customer = Mage::getModel('customer/customer')->load($customerId);
				//if($customer->getAllAddresses())


				//$allAddress = Mage::getModel('customer/address')->getCollection()->setCustomerFilter($customer);

				if ( ! $customer->getDefaultBillingAddress() ) {
					foreach ($customer->getAddresses() as $address) {
						$address->setIsDefaultBilling('1')->save();
						continue; // we only want to set first address of the customer as default billing address
					}
				}
				if ( ! $customer->getDefaultShippingAddress() ) {
					foreach ($customer->getAddresses() as $address) {
						$address->setIsDefaultShipping('1')->save();
						continue; // we only want to set first address of the customer as default shipping address
					}
				}
				$customer = Mage::getModel('customer/customer')->load($customerId);
				if($customer->getDefaultShippingAddress()->getPostcode()){
					
					$shippingPincode = $customer->getDefaultShippingAddress()->getPostcode();
					$data = Mage::getModel('operations/serviceablepincodes')->getPincodeData($shippingPincode);
					if($data){

						$customer->setPincode($data['pincode']);
						$customer->setCusCity($data['city']);
						$customer->setCusState($data['region_id']);
						$customer->setCusCountry($data['country']);
						try{
							$customer->save();
							Mage::log("Customer updated : #".$customerId,null,'customer_update.log',true);
						}catch(exceptions $e){
							Mage::log('error in updating customers data for customer :'.$customerId,null,'customer_update_error.log',true);
						}
						
					}else{
						Mage::log('error in updating customers data for customer :'.$customerId,null,'customer_update_error.log',true);
					}
					
				}
			}
			
			$time_elapsed_secs = microtime(true) - $start;
			$first = reset($chucked_customer);
		    $last = end($chucked_customer);
			Mage::log("Time Taken for 1000 - from ".$first." to ".$last." : ".$time_elapsed_secs,null,'customer_update.log',true);
			
		}
		die;

$websiteId = Mage::app()->getWebsite()->getId();
$store = Mage::app()->getStore();
 
$customer = Mage::getModel("customer/customer");
$customer   ->setWebsiteId($websiteId)
            ->setStore($store)
            ->setFirstname('John')
            ->setLastname('Doe')
            ->setEmail('jd1@ex.com')
            ->setPassword('somepassword');
 
try{
    $customer->save();
    echo "Done";
}
catch (Exception $e) {
    Zend_Debug::dump($e->getMessage());
}

?>