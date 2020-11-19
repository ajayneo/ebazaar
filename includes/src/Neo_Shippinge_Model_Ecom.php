<?php
class Neo_Shippinge_Model_Ecom extends Mage_Core_Model_Abstract
{
	public function register($post)
	{
		$incrementId = $post["incrementid"];
		$weight = (float)$post["weight"];
		$invoice_id     = $post["invoice_id"];

		if($incrementId)
		{
			$order = Mage::getModel("sales/order")->loadByIncrementId($incrementId);
			$invoice = Mage::getModel('sales/order_invoice')->load($invoice_id);
			$shipping_address = $order->getShippingAddress();
            $customer_name = $order->getCustomerFirstname().' '.$order->getCustomerLastname();
            $amount = (int)$invoice->getGrandTotal();
			$payment_method = $order->getPayment()->getMethodInstance()->getCode();
			$product = 'PPD';
			if($payment_method == "cashondelivery") {
				$product = 'COD';
			}
			$username = "compuindiabom";
			$password = "c2omp345uindib4om";
			 $xml      =
			"<?xml version='1.0' encoding='UTF-8'?> <ECOMEXPRESS-OBJECTS>   
			<SHIPMENT> <AWB_NUMBER></AWB_NUMBER>
			<ORDER_NUMBER>".$incrementId."</ORDER_NUMBER>  
			<PRODUCT>".$product."</PRODUCT>
			<CONSIGNEE>".$customer_name."</CONSIGNEE>
			<CONSIGNEE_ADDRESS1>".$shipping_address->getStreet()."</CONSIGNEE_ADDRESS1>
			<CONSIGNEE_ADDRESS2></CONSIGNEE_ADDRESS2>
			<CONSIGNEE_ADDRESS3></CONSIGNEE_ADDRESS3>
			<DESTINATION_CITY>".$shipping_address->getCity()."</DESTINATION_CITY>
			<PINCODE>".$shipping_address->getPostcode()."</PINCODE>
			<STATE>".$shipping_address->getRegion()."</STATE>  
			<MOBILE></MOBILE>
			<TELEPHONE>".$shipping_address->getTelephone()."</TELEPHONE>  
			<ITEM_DESCRIPTION></ITEM_DESCRIPTION>  
			<PIECES>1</PIECES>  
			<COLLECTABLE_VALUE>".$amount."</COLLECTABLE_VALUE>  
			<DECLARED_VALUE>".$amount."</DECLARED_VALUE>
			<ACTUAL_WEIGHT>".$weight."</ACTUAL_WEIGHT>
			<VOLUMETRIC_WEIGHT>0</VOLUMETRIC_WEIGHT>  <LENGTH> 10</LENGTH>
			<BREADTH>10</BREADTH>  <HEIGHT>10</HEIGHT> <VENDOR_ID></VENDOR_ID>
			<PICKUP_NAME>Electronics Bazaar</PICKUP_NAME>
			<PICKUP_ADDRESS_LINE1>Building No.D, Gala NO.4/15</PICKUP_ADDRESS_LINE1>
			<PICKUP_ADDRESS_LINE2>Harihar Corporation, Dapode</PICKUP_ADDRESS_LINE2>
			<PICKUP_PINCODE>421302</PICKUP_PINCODE>
			<PICKUP_PHONE>18002660786</PICKUP_PHONE>
			<PICKUP_MOBILE>18002660786</PICKUP_MOBILE>  </SHIPMENT>
			</ECOMEXPRESS-OBJECTS>";
			$postfields["username"] = $username; 
			$postfields["password"] = $password; 
			$postfields["xml_input"]      = $xml;
			$postdata = "username=$username&password=$password&xml_input=".urlencode($xml);
			#print $postdata;
			$url = "http://api.ecomexpress.in/api/api_create_order_to_awb_xml/";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url); 
			curl_setopt($ch, CURLOPT_POST, 1); 
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata); 
			$xml = curl_exec($ch);
			$res = simplexml_load_string($xml);
			$awb1 = (array)$res;
                        $awb2 = (array)$awb1['AIRWAYBILL-OBJECTS'];
                        $awb3 = (array)$awb2['AIRWAYBILL'];
                        $awb = (array)$awb3['airwaybill_number'];
                       
			if($awb[0]) {
			$response = array();
			$response[] = array(
					"error" => false,
					"awb"   =>  $awb[0],
					"destination"   => $shipping_address->getRegion().' '.$shipping_address->getCity()
				);
			} else {
				$response = array();
				$response[] = array(
					"error" => true,
					"message"   =>  $xml
				);
			}
			return $response;
		}
	}
}
?>
