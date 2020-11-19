<?php
class Neo_Vowdelight_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function createOrder($customer_id,$product_id,$qty,$oderNumber){
		try{
			$session = Mage::getSingleton('core/session');
			$vow_mobile_order_flag = 'Yes';
			$session = $session->setData('vow_mobile_order_flag',$vow_mobile_order_flag);
			$method = 'cashondelivery';
			$_order = Mage::getModel('sales/order')->loadByIncrementId($oderNumber);
			
			$websiteId = Mage::app()->getWebsite()->getId();
			$store = Mage::app()->getStore();
			// Start New Sales Order Quote
			$quote = Mage::getModel('sales/quote')->setStoreId($store->getId());
			 
			// Set Sales Order Quote Currency
			$quote->setCurrency($order->AdjustmentAmount->currencyID);
			
			$customer = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->load($customer_id); //static value
			
			// Assign Customer To Sales Order Quote
			$quote->assignCustomer($customer);
			 
			// Configure Notification
			// $quote->setSendCconfirmation(1);
			$quote->setSendConfirmation(1);			
			
			//Add product to order quote
			$product=Mage::getModel('catalog/product')->load($product_id); //static value
			$buyInfo = array('qty'   => $qty);
			$quote->addProduct($product,new Varien_Object($buyInfo)); //static value
			// $quote->addProduct($product, new Varien_Object($buyInfo))->setOriginalCustomPrice(0); /*after change */
			//apply coupon
			$couponCode = 'VOW-DELIGHT-M7295-7R3CB-N0'; //'VOW-DELIGHT-D58RA-WMNQ9-WZ';
			$quote->setCouponCode($couponCode)->save();

			$billingAddress = $_order->getBillingAddress()->getData();
			$shippingAddress = $_order->getShippingAddress()->getData();

			$billingAddress = $quote->getBillingAddress()->addData($billingAddress);
			$shippingAddress = $quote->getShippingAddress()->addData($shippingAddress);
			 
			 // Collect Rates and Set Shipping & Payment Method
			 $shippingAddress->setCollectShippingRates(true)
			                 ->collectShippingRates()
			                 ->setShippingMethod('freeshipping_freeshipping')
			                 ->setPaymentMethod($method);

			 $shippingAddress->setPaymentMethod($method);
			 
			 // Set Sales Order Payment
			 $quote->getPayment()->importData(array('method' => $method));
			 // Collect Totals & Save Quote
			 $quote->collectTotals()->save();
			 
			 // print_r($quote->getData()); exit;

			 // Create Order From Quote
			 $service = Mage::getModel('sales/service_quote', $quote);
			 $service->submitAll();
			 $increment_id = $service->getOrder()->getRealOrderId();

			 //clear quote.			 
             $quote->setIsActive(0);
             $quote->save();			 
			 
			 // Resource Clean-Up
			 $quote = $customer = $service = null;
			 $session->unsetData('vow_mobile_order_flag');
		 
			 // Finished
			 return $increment_id;
		}catch(Exception $e){
			//error			
			return $e->getMessage();
		}
	}
}
	 