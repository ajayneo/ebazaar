<?php
class Neo_Replace_Model_Observer extends Mage_Payment_Model_Method_Abstract
{
	public function orderPlaceAfter($observer)
    { 
        $data = $observer->getEvent()->getOrder();
        
        $post = Mage::app()->getRequest()->getPost('payment');

        //if($post['method']=='replace'){
        if(false){
        	//$post['order_no'] cancle this order by code
        	//Mage::throwException('sandeep');exit;

        	$order_num = Mage::getModel('sales/order')->loadByIncrementID($post['order_no']);
			$order_num->setData('state', 'canceled');
			$order_num->setStatus('canceled'); 

			$history = $order_num->addStatusHistoryComment('', false);
			$history->setIsCustomerNotified(false);   
			$order_num->save();
        }
    }


    public function filterpaymentmethod(Varien_Event_Observer $observer)
    {
        $configValue = Mage::getStoreConfig('payment/replace/active',Mage::app()->getStore()); 

        $bankTransferEnabled = Mage::getStoreConfig('');
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $method = $observer->getEvent()->getMethodInstance();
        if($method->getCode() == 'replace' && $configValue == 1)
        { 
            //if (Mage::getSingleton('customer/session')->isLoggedIn() && $customer->getGroupId()=='4' || $customer->getEmail() == 'amit@sahivalue.com') {
            if (Mage::getSingleton('customer/session')->isLoggedIn() && $customer->getGroupId()=='4') {           
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
?>
