<?php
class Neo_Dob_Model_Observer{
	
	public function sendDobWishEmail($observer){
		$currentdate = Mage::getModel('core/date')->date('Y-m-d');
		$date=date_create($currentdate);
		$current_formated_date = date_format($date,"d/m/Y");

		$customers = Mage::getResourceModel('customer/customer_collection')->joinAttribute('dob','customer/dob', 'entity_id');
		$customers = $customers->addAttributeToFilter('dob',array('eq',$currentdate));

		$customer_mobile = array();
        foreach($customers as $customer){
			$cus_dob = $customer->getDob();
			$dob_date=date_create($cus_dob);
			$cus_dob_formated_date = date_format($dob_date,"d/m/Y");
			if($cus_dob_formated_date == $current_formated_date){
				$customer_id = $customer->getId();
                $customerData = Mage::getModel('customer/customer')->load($customer_id);
                $customer_email = $customerData->getEmail();

                $store_name = Mage::getStoreConfig('trans_email/ident_general/name');
                $store_email = Mage::getStoreConfig('trans_email/ident_general/email');
                $customer_name = $customerData->getFirstname().' '.$customerData->getLastname();

                Mage::log('Birthday Wishes Sent to '.$customer_name.' on '.$current_formated_date,null,'neo_dob_wish_email.log');

                $body = "Dear ". $customer_name .",<br/>";
                $body .= "Electronics Bazaar Wishes You a Very Happy Birthday !";
			
				$mail = Mage::getModel('core/email');
				$mail->setToName($customer_name);
				$mail->setToEmail($customer_email);
				$mail->setBody($body);
				$mail->setSubject('Happy Birthday');
				$mail->setFromEmail($store_email);
				$mail->setFromName($store_name);
				$mail->setType('html');
				$mail->send();
                //$customer_mobile[] = $customerData->getEmail();
            }
        }
		
			
	} 
	
}
?>