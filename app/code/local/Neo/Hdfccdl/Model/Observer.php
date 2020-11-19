<?php
class Neo_Hdfccdl_Model_Observer
{
    public function process_hdfc_order_request($observer)
    {
		$order = $observer->getEvent()->getOrder();
		$payment_method_code = $order->getPayment()->getMethodInstance()->getCode();
		if($payment_method_code == "hdfc_cdl"){

			$merchant_id = Mage::getStoreConfig('payment/hdfc_cdl/merchant_id');
			$currency_type = Mage::getStoreConfig('payment/hdfc_cdl/currency_type');
			$user_id = Mage::getStoreConfig('payment/hdfc_cdl/user_id');
			$return_url = Mage::getStoreConfig('payment/hdfc_cdl/return_url');
			$checksum_key = Mage::getStoreConfig('payment/hdfc_cdl/checksum_key');

			$order_inc_id = $order->getIncrementId();
			$customer_id = $order->getCustomerId();
			$order_grand_total = $order->getGrandTotal();
			
			$order_model = Mage::getModel('sales/order')->load($order->getId());
			$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
			
			$customer_mobile = $customer->getMobile();
			$customer_emailid = $customer->getEmail();

			$assetDetails_array = array();
			foreach($order_model->getAllVisibleItems() as $item){
				$assetDetails_array[] = $item->getSku();
			}

			while(count($assetDetails_array) < 4){
				$assetDetails_array[] = "NA";
		    }

		    $assetDetails_array = array_slice($assetDetails_array, 0, 4);
			
			$assetDetails = implode('!',$assetDetails_array);

			$str = "$merchant_id|$order_inc_id|$assetDetails|$order_grand_total|NA|NA|NA|$currency_type|DIRECT|R|$user_id|NA|NA|F|$customer_emailid|$customer_mobile|NA|NA|NA|NA|NA|$return_url";
			
	    	$checksum = hash_hmac('sha256',$str,$checksum_key,false); 
	    	$checksum = strtoupper($checksum);
	    	$dataWithCheckSumValue = $str."|".$checksum;
	    	$msg = trim($dataWithCheckSumValue);

	    	Mage::log("Message as Request",null,'Neo_HDFC.log');
	    	Mage::log($msg,null,'Neo_HDFC.log');
			
	    	Mage::getSingleton('core/session')->setChecksum($checksum);
	    	Mage::getSingleton('core/session')->setMsg($msg);
		}
    }
}
?>