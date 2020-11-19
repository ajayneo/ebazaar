<?php
class Neo_Affiliatelimit_Model_Observer
{

	public function orderPlaceAfterAffiliate(Varien_Event_Observer $observer)
	{
		$post = Mage::app()->getRequest()->getPost('payment');//$post['method'] 
		$order = $observer->getOrder();
		$customer = $order->getCustomer();
		$order->getBaseGrandTotal();

        $credit = Mage::getModel("affiliatelimit/limit")->getCollection()->addFieldToFilter('email',$customer->getEntityId())->getFirstItem()->getData();
        $credidObj = Mage::getModel("affiliatelimit/limit")->load($credit['id']);

		if($order->getPayment()->getMethodInstance()->getCode() == 'banktransfer' && $order->getBaseGrandTotal() <= $credit['credit'])
		{
			$credidObj->setCredit( $credit['credit'] - $order->getBaseGrandTotal());
			$credidObj->save();  

			/*$order->getPayment()->setSkipTransactionCreation(false);
			$invoice = $order->prepareInvoice();
            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
            $invoice->setStockLocation('Kurla Warehouse');
            $invoice->register();
            Mage::getModel('core/resource_transaction') 
                   ->addObject($invoice)
                   ->addObject($order)
                   ->save(); */
			
 
			$order->setData('state', 'financeapproved');
			$order->setStatus('financeapproved');       
			$order->save();
		}
	}
		   
}
  