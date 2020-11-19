<?php
class Neo_IndiaGst_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getStateCodes(){
		return array('Andaman and Nicobar Islands'=>'35','Andhra Pradesh'=>'28','Andhra Pradesh'=>'37','Arunachal Pradesh'=>'12','Assam'=>'18','Bihar'=>'10','Chandigarh'=>'4','Chattisgarh'=>'22','Dadra and Nagar Haveli'=>'26','Daman and Diu'=>'25','Delhi'=>'7','Goa'=>'30','Gujarat'=>'24','Haryana'=>'6','Himachal Pradesh'=>'2','Jammu and Kashmir'=>'1','Jharkhand'=>'20','Karnataka'=>'29','Kerala'=>'32','Lakshadweep Islands'=>'31','Madhya Pradesh'=>'23','Maharashtra'=>'27','Manipur'=>'14','Meghalaya'=>'17','Mizoram'=>'15','Nagaland'=>'13','Odisha'=>'21','Pondicherry'=>'34','Punjab'=>'3','Rajasthan'=>'8','Sikkim'=>'11','Tamil Nadu'=>'33','Telangana'=>'36','Tripura'=>'16','Uttar Pradesh'=>'9','Uttarakhand'=>'5','West Bengal'=>'19','West Bengal'=>'19','Uttaranchal'=>'5');
	}

	public function getHsnBySku($_sku){
			$_hsnCode = '';
		if($_sku){
			$_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$_sku);
			if($_product){
				$_attribute_set_id = $_product->getAttributeSetId();
			 	$attributeSetModel = Mage::getModel("eav/entity_attribute_set");
				$attributeSetModel->load($_attribute_set_id);
				$attributeSetName = $attributeSetModel->getAttributeSetName();

				$_hsn_code_array = $this->getHsnCodes();
				if($_hsn_code_array[$attributeSetName]){
					$_hsnCode = $_hsn_code_array[$attributeSetName]; 
				}
			}

		}

		return $_hsnCode;
	}

	public function getHsnCodesOld(){
		return array('Laptops'=>'84713010',
			'Mobiles'=>'85171290',
			'Tablets'=>'84713090',
			'Bluetooth'=>'85176290',
			'Headsets'=>'85183000',
			'Power Bank'=>'85076000',
			'ear S3/ Fit series -Galaxy Gear -[Wearable Device - Bluetooth]'=>'85176290',
			'Gear 360 / Gear Watch Sm-R360'=>'85176290',
			'Battery'=>'85076000',
			'Travel Adapter  28%'=>'85044090',
			'Chargers'=>'85044090',
			'Covers and cases'=>'39269099/42022190',
			'Gear VR'=>'85437039',
			'IconX Earbuds (SM-R150)'=>'85198990',
			'AIO & Desktop' => '847130',
			'Cables' => '85444999',
			'Laptop Bags' => '42029900'
			);
	}

	public function getHsnCodes(){
		return array('AIO & Desktop'=>'84715000','Bluetooth'=>'85176290','Cables'=>'85444999','Chargers'=>'85044090','Default'=>'','Headsets'=>'85183000','Laptop Bags'=>'42029900','Laptops'=>'84715000','Microsoft'=>'84715000','Mobiles'=>'85171290','Power Bank'=>'85078000','Sell Your Gadget'=>'','Speaker'=>'85182900','Tablets'=>'84713090');
	}

	public function isInterStateGst($_invoice_id){
		$divide_tax = false;
		if($_invoice_id){
			$invoice = Mage::getModel('sales/order_invoice')->load($_invoice_id);
			$order = $invoice->getOrder();
			$billing_state = $order->getBillingAddress()->getRegion();
			$shipping_state = $order->getShippingAddress()->getRegion();

			$stockLocationModelRaw = Mage::getModel('stocklocation/location');

			 $stockLocationModel = $stockLocationModelRaw->getCollection()->addFieldToSelect(array('stock_location', 'id'))->addFieldToFilter('order_id', $invoice->getorder_id())->getFirstItem()->getData();
			if($stockLocationModel['stock_location']=='Kurla Warehouse' || $stockLocationModel['stock_location']=='Bhiwandi Warehouse' || $stockLocationModel['stock_location']=='Andheri HO' || $stockLocationModel['stock_location']=='Bhiwandi EB-REPAIR CENTRE' || $stockLocationModel['stock_location']=='Bhiwandi EB-SERVICE CENTRE' || $stockLocationModel['stock_location']=='Bhiwandi EB-Open Units' || $stockLocationModel['stock_location']=='Bhiwandi EB-Warranty stock' || $stockLocationModel['stock_location']=='Bhiwandi EB-Refurbished' || $stockLocationModel['stock_location']=='Amazon BB – ASIS-Bhiwandi'|| $stockLocationModel['stock_location']=='Amazon BB – QC-Bhiwandi'|| $stockLocationModel['stock_location']=='FlipKart Prexo – ASIS- Bhiwandi'|| $stockLocationModel['stock_location']=='FlipKart Prexo – QC-Bhiwandi'){
                // $seller_gstin = '27AAKCA6979M1ZR';
                $state = 'Maharashtra';
            }elseif($stockLocationModel['stock_location']=='Bangalore YCH HUB' || $stockLocationModel['stock_location']== 'Bangalore Proconnect HUB' || $stockLocationModel['stock_location']== 'Amazon BB – ASIS-Bangalore'|| $stockLocationModel['stock_location']== 'Amazon BB – QC-Bangalore'|| $stockLocationModel['stock_location']== 'FlipKart Prexo – QC-Bangalore'|| $stockLocationModel['stock_location']== 'FlipKart Prexo – ASIS-Bangalore'){
                // $seller_gstin = '29AAKCA6979M1ZN';
                $state = 'Karnataka';
            }elseif($stockLocationModel['stock_location']=='Tamilnadu Warehouse'){
                // $seller_gstin = '33AAKCA6979M1ZY';
                $state = 'Tamil Nadu';
            }else{
            	//
            } 

			if(!empty($billing_state) && !empty($shipping_state)){
				$state_codes = $this->getStateCodes();
				$buyer_state_code = (integer) $state_codes[$billing_state];
				$seller_state_code = (integer) $state_codes[$state];
				if($buyer_state_code === $seller_state_code){
					$divide_tax = true;
				} 
			}
		}
		return $divide_tax;
	}

	public function isInterStateGst2($_order_id){
		$divide_tax = false;
		if($_order_id){
			$order = Mage::getModel('sales/order')->load($_order_id);
			$billing_state = $order->getBillingAddress()->getRegion();
			$shipping_state = $order->getShippingAddress()->getRegion();

			if(!empty($billing_state) && !empty($shipping_state)){
				$state_codes = $this->getStateCodes();
				$buyer_state_code = (integer) $state_codes[$billing_state];

				if($buyer_state_code === 27){
					$divide_tax = true;
				} 
			}
		}
		return $divide_tax;
	}

	public function sellerGstIn(){
		// return Mage::getStoreConfig('general/store_information/gstin');
		$gstin = '';
		$stockLocationModelRaw = Mage::getModel('stocklocation/location');
        $stockLocationModel = $stockLocationModelRaw->getCollection()->addFieldToSelect(array('stock_location', 'id'))->addFieldToFilter('order_id', $orderId)->getFirstItem()->getData();
        if($stockLocationModel['stock_location']=='Kurla Warehouse' || $stockLocationModel['stock_location']=='Bhiwandi Warehouse' || $stockLocationModel['stock_location']=='Andheri HO' || $stockLocationModel['stock_location']=='Bhiwandi EB-REPAIR CENTRE' || $stockLocationModel['stock_location']=='Bhiwandi EB-SERVICE CENTRE' || $stockLocationModel['stock_location']=='Bhiwandi EB-Open Units' || $stockLocationModel['stock_location']=='Bhiwandi EB-Warranty stock' || $stockLocationModel['stock_location']=='Bhiwandi EB-Refurbished' || $stockLocationModel['stock_location']=='Amazon BB – ASIS-Bhiwandi'|| $stockLocationModel['stock_location']=='Amazon BB – QC-Bhiwandi'|| $stockLocationModel['stock_location']=='FlipKart Prexo – ASIS- Bhiwandi'|| $stockLocationModel['stock_location']=='FlipKart Prexo – QC-Bhiwandi'){
        	$gstin = '27AAKCA6979M1ZR';
        }elseif($stockLocationModel['stock_location']=='Tamilnadu Warehouse'){
        	$gstin = '33AAKCA6979M1ZY';

        }else{
        	$gstin = '27AAKCA6979M1ZR';
        } 

        return $gstin;
	}

	public function buyerGstIn($customer_id){
		$gstin = '';
		try{
			if($customer_id){
				$_customer = Mage::getModel('customer/customer')->load($customer_id);
				if($_customer->getGstin() !== ''){
					$gstin = $_customer->getGstin();
				}
			}
		}catch(Exception $e){
			echo $e->getMessage();
			exit;
		}

		return $gstin;
	}

	public function validateGstin($request){
		$status = array();
		$message = array();
		$error = false;
		if(!empty($request)){
			$gstin = $request['gstin'];
			$postcode = $request['postcode'];
			$pan = $request['pan'];
			$gstVal = preg_match("/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9]{1}[A-Z]{1}[0-9A-Z]{1}$/", $gstin);
			$postcodeVal = preg_match("/^[0-9]{6}$/", $postcode);
			$panVal = preg_match("/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/", $pan);
			
			if(!$gstVal){
				$error = true;
				$message[] = 'GST Identification Number is not valid. It should be in this 11AAAAA1111Z1A1 format ';
			}

			if(!$postcodeVal){
				$error = true;
				$message[] = 'PIN CODE is not valid. It should be 6 digits format ';
			}
			
			if(!$panVal){
				$error = true;
				$message[] = 'PAN Number is not valid. It should be in this AAAAA1111A format ';
			}
		}
		if($error){
			$error_msg ='';
			foreach ($message as $key => $msg) {
				$error_msg .= $msg.'& ';
			}
			// var_dump($)$error_msg;
			$error_msg = trim($error_msg, ' ');
			$error_msg = trim($error_msg,'&');
			$status['error'] = 1;
			$status['message'] = $error_msg;
		}

		return $status;

	}

	public function getGstDetails($customer_id){
		$status = 0;
		if($customer_id){
			$collection = Mage::getResourceModel('indiagst/gstdetails_collection');
			$collection->addFieldToFilter('customer_id',array('eq'=>$customer_id));
			if($collection->getData()){
				$status = 1;
			}
		}

		return $status;
	}

	public function getEcomGstDetails($invoice_id){
		// $invoice_id = '42219';
		// $incrementId = '200056168';
		$invoice = Mage::getModel('sales/order_invoice')->load($invoice_id);
		$invoice_date = $invoice->getCreatedAt();
		$invoice_number = $invoice->getIncrementId();
		$order_id = $invoice->getorder_id();
		
		$seller_gst = Mage::helper('indiagst')->sellerGstIn($order_id);
		$warehouseAddress = Mage::helper('shippinge')->getWarehouseAddress($order_id);
		$invoice_formated_date = date('d-M-Y',strtotime($invoice_date));
		// $order = Mage::getModel("sales/order")->load($order_id);
		// $shipping_address = $order->getShippingAddress();
		
		$divideTax = Mage::helper('indiagst')->isInterStateGst($invoice_id);
		$invoice_items = array();
		$cgst = $sgst = $igst = 0;
		foreach ($invoice->getAllItems() as $item) {
			$hsn = Mage::helper('indiagst')->getHsnBySku($item->getSku());
			$price = (int)$item->getPriceInclTax();
			$tax_percent = (int)$item->getOrderItem()->getTaxPercent();
			$total_tax = (int) $item->getTaxAmount();
			if($divideTax){
				$cgst = $sgst = $total_tax / 2;
				$cgst_rate = $sgst_rate = $tax_percent / 2;
				$igst = 0;
				$igst_rate = 0;
			}else{
				$cgst = $sgst =  0;
				$cgst_rate = $sgst_rate =  0;
				$igst = $total_tax;
				$igst_rate = $tax_percent;
			}

		$item_description = $item->getName();
		$item_description = str_replace('"', '', $item_description);
			$item_description = str_replace('(', '', $item_description);
			$item_description = str_replace(')', '', $item_description);

		$invoice_items[] = '{
		"ITEM_DESCRIPTION": "'.$item_description.'",
		"ITEM_VALUE": '.$price.',
		"ITEM_CATEGORY": "ELECTRONICS",
		"SELLER_NAME": "'.$warehouseAddress[0].'",
		"SELLER_ADDRESS": "'.$warehouseAddress[1].', '.$warehouseAddress[2].' ,'.$warehouseAddress[3].', '.$warehouseAddress[5].', '.$warehouseAddress[4].'",
		"SELLER_STATE": "'.$warehouseAddress[5].'",
		"SELLER_PINCODE": "'.$warehouseAddress[4].'",
		"SELLER_TIN": "",
		"INVOICE_NUMBER": "'.$invoice_number.'",
		"INVOICE_DATE": "'.$invoice_formated_date.'",
		"ESUGAM_NUMBER": "",
		"SELLER_GSTIN": "'.$seller_gst.'",
		"GST_HSN": "'.$hsn.'",
		"GST_ERN": "URP",
		"GST_TAX_NAME": "DELHI GST",
		"GST_TAX_BASE": '.number_format((float)$total_tax, 2, '.', '').',
		"DISCOUNT": 0.0,
		"GST_TAX_RATE_CGSTN": '.number_format((float)$cgst_rate, 1, '.', '').',
		"GST_TAX_RATE_SGSTN": '.number_format((float)$sgst_rate, 1, '.', '').',
		"GST_TAX_RATE_IGSTN": '.number_format((float)$igst_rate, 1, '.', '').',
		"GST_TAX_TOTAL": '.number_format((float)$total_tax, 2, '.', '').',
		"GST_TAX_CGSTN": '.number_format((float)$cgst, 2, '.', '').',
		"GST_TAX_SGSTN": '.number_format((float)$sgst, 2, '.', '').',
		"GST_TAX_IGSTN": '.number_format((float)$igst, 2, '.', '').'
		}';
		}

		return $invoice_items;
	}


	public function getEcomSingleItem($invoice_id){

		$invoice = Mage::getModel('sales/order_invoice')->load($invoice_id);
		$invoice_date = $invoice->getCreatedAt();
		$invoice_number = $invoice->getIncrementId();
		$order_id = $invoice->getorder_id();
		
		$seller_gst = Mage::helper('indiagst')->sellerGstIn($order_id);
		$warehouseAddress = Mage::helper('shippinge')->getWarehouseAddress($order_id);
		$invoice_formated_date = date('d-M-Y',strtotime($invoice_date));

		// $order = Mage::getModel("sales/order")->load($order_id);
		// $shipping_address = $order->getShippingAddress();
		
		$divideTax = Mage::helper('indiagst')->isInterStateGst($invoice_id);
		$invoice_items = array();
		$cgst = $sgst = $igst = 0;
		foreach ($invoice->getAllItems() as $item) {
			$hsn = Mage::helper('indiagst')->getHsnBySku($item->getSku());
			$price = (int)$item->getPriceInclTax();
			$tax_percent = (int)$item->getOrderItem()->getTaxPercent();
			$total_tax = (int) $item->getTaxAmount();
			if($divideTax){
				$cgst = $sgst = $total_tax / 2;
				$cgst_rate = $sgst_rate = $tax_percent / 2;
				$igst = 0;
				$igst_rate = 0;
			}else{
				$cgst = $sgst =  0;
				$cgst_rate = $sgst_rate =  0;
				$igst = $total_tax;
				$igst_rate = $tax_percent;
			}
			break;
		}	
		$additional_info = '';
		$additional_info = '{
				"SELLER_TIN":"",
				"INVOICE_NUMBER": "'.$invoice_number.'",
				"INVOICE_DATE": "'.$invoice_formated_date.'",
				"ESUGAM_NUMBER": "",
				"ITEM_CATEGORY": "ELECTRONICS",
				"PACKING_TYPE": "Box",
				"PICKUP_TYPE": "WH",
				"RETURN_TYPE": "WH",
				"PICKUP_LOCATION_CODE": "",
				"SELLER_GSTIN": "'.$sellergstin.'",
				"GST_HSN": "'.$hsn.'",
				"GST_ERN": "URP",
				"GST_TAX_NAME": "DELHI GST",
				"GST_TAX_BASE": '.number_format((float)$total_tax, 2, '.', '').',
				"DISCOUNT": 0.0,
				"GST_TAX_RATE_CGSTN": '.number_format((float)$cgst_rate, 1, '.', '').',
				"GST_TAX_RATE_SGSTN": '.number_format((float)$sgst_rate, 1, '.', '').',
				"GST_TAX_RATE_IGSTN": '.number_format((float)$igst_rate, 1, '.', '').',
				"GST_TAX_TOTAL": '.number_format((float)$total_tax, 2, '.', '').',
				"GST_TAX_CGSTN": '.number_format((float)$cgst, 2, '.', '').',
				"GST_TAX_SGSTN": '.number_format((float)$sgst, 2, '.', '').',
				"GST_TAX_IGSTN": '.number_format((float)$igst, 2, '.', '').'}';

			return $additional_info;	
	}
}
	 