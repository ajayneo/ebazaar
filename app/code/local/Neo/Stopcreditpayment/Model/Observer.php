<?php
class Neo_Stopcreditpayment_Model_Observer
{

	public function filterpaymentmethod(Varien_Event_Observer $observer)
    {   
        $configValue = Mage::getStoreConfig('payment/banktransfer/active',Mage::app()->getStore()); 

        $bankTransferEnabled = Mage::getStoreConfig('');
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $method = $observer->getEvent()->getMethodInstance();
        if($method->getCode() == 'banktransfer' && $configValue == 1)
        { 
        	//$customer->getAsmMap()
        	//echo '<pre>';print_r($customer->getAsmMap());exit; put the logic for live  whats app campaign as referral 
            //if (Mage::getSingleton('customer/session')->isLoggedIn() && $customer->getGroupId()=='4' || $customer->getEmail() == 'amit@sahivalue.com') {

            if (Mage::getSingleton('customer/session')->isLoggedIn() && $customer->getAsmMap()=='54') { 
            //if (false) {              
                $result = $observer->getEvent()->getResult();
                $result->isAvailable = true;
                return;
            }else{
                $result = $observer->getEvent()->getResult();
                $result->isAvailable = true;
                return;  
            }          
        }       
    }     
		
}
