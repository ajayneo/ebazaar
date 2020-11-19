<?php
class Neo_AffilateBankTransfer_Model_Observer
{

    public function filterpaymentmethod(Varien_Event_Observer $observer)
    {
        Mage::log('sandeep filterpaymentmethods',null,'filterpaymentmethods.log');
        
        $configValue = Mage::getStoreConfig('payment/banktransfer/active',Mage::app()->getStore()); 

        $bankTransferEnabled = Mage::getStoreConfig('');
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $method = $observer->getEvent()->getMethodInstance();
        if($method->getCode() == 'banktransfer' && $configValue == 1)
        { 
            //if (Mage::getSingleton('customer/session')->isLoggedIn() && $customer->getGroupId()=='4' || $customer->getEmail() == 'amit@sahivalue.com') {
            if (Mage::getSingleton('customer/session')->isLoggedIn() && $customer->getGroupId()=='4') {
            //if (false) {              
                $result = $observer->getEvent()->getResult();
                $result->isAvailable = true;
                return;
            }else{
                $result = $observer->getEvent()->getResult();
                $result->isAvailable = false;
                return;  
            }          
        }       
    }     
}
       