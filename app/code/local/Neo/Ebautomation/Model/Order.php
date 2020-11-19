<?php
class Neo_Ebautomation_Model_Order extends Mage_Core_Model_Abstract
{
	
	

	/*
	* @Author : Sonali Kosrabe
	* @Date : 02-nov-2017
	* @Purpose : To Map Orders in Navision
	*/

	public function orderImport($order){

		
		$orderData = array();				
		//foreach ($orders as $order) {
			$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
			$customerGroupName = Mage::getModel('customer/group')->load($order->getCustomerGroupId())->getCustomerGroupCode();
			/*$billingAddressId = $customer->getDefaultBilling();
			$shippingAddressId = $customer->getDefaultShipping();
			$customerBillingAddress = Mage::getModel('customer/address')->load($billingAddressId);
			$customerShippingAddress = Mage::getModel('customer/address')->load($shippingAddressId);*/


			$customerBillingAddress = $order->getBillingAddress();
			$customerShippingAddress = $order->getShippingAddress();

			$shippingStateCode = $stateCodes[$customerShippingAddress->getRegion()];
			$billingStateCode = $stateCodes[$customerBillingAddress->getRegion()];
			$code = Mage::helper('ebautomation')->stateCodeMapping($customerBillingAddress->getRegionCode()); 
			$code1 = Mage::helper('ebautomation')->stateCodeMapping($customerShippingAddress->getRegionCode());

			if(strlen($order->getRemoteIp()) > 0) { 
	            $userAgent = 'Website';
	        } else {
	            $userAgent = 'Application'; //Application
	        }

	        $dueDate = ''; $creditDays = '';
	        if($order->getPayment()->getMethod() == 'banktransfer'){

	            $collectionDelivery = Mage::getModel("banktransferdelivery/delivery")->getCollection()->addFieldToSelect('delivery')->addFieldToFilter('order_num',$order->getRealOrderId())->getFirstItem()->getData();

	            $da = $collectionDelivery['delivery'] + 5;
	            $date = explode(' ',$order->getCreatedAt());
	            $NewDays = date ("d-M-Y", strtotime ($date[0] ."+".$da." days")); 

	            $creditDays = $collectionDelivery['delivery'];
	            $dueDate    = $NewDays;
	        }
	        
			foreach ($order->getItemsCollection() as $orderItem){

				if($orderItem->getQtyInvoiced() > 0){
					$orderItemInvoice = Mage::helper('ebautomation')->getOrderInvoiceDetails($orderItem->getId());
					$invoiceId = $orderItemInvoice->getId();
				}else{
					$orderItemInvoice = NULL;
					$invoiceId = NULL;
				}
				
				
				$product = Mage::getModel('catalog/product')->load($orderItem->getProductId());
				$categoryName =array();
				foreach ($product->getCategoryIds() as $Id) {
					$_cat = Mage::getModel('catalog/category')->load($Id);
					$categoryName[] = $_cat->getName();
				}

				$store = Mage::getModel('core/store')->load($order->getStoreId());
				$storeName = $store->getName();

				$warehouseLocation = Mage::helper('stocklocation')->getStockLocation($order->getId(),$invoiceId);
				if($warehouseLocation == 'Bhiwandi Warehouse'){

				}
				$orderSku = explode("/", $orderItem->getSku());
				$original_price = $orderItem->getOriginalPrice();
				if($orderItem->getSku() == "FREEVOW103"){
					$original_price = 0;
				}

				$orderData[] = array(
						'Order_Number' => $order->getIncrementId(),
						'Order_Date'=> date('dmY',strtotime($order->getCreatedAt())),
						'Order_Time'=> date('H:i:s',strtotime($order->getCreatedAt())),
						'Order_Status'=> $order->getStatusLabel(),
						'Order_Purchased_From'=> $storeName,
						'Order_Payment_Method'=> $order->getPayment()->getMethod(),
						'Credit_Card_Type'=> $order->getPayment()->getCcType(),
						'Order_Shipping_Method'=> $order->getShippingDescription(),
						'Order_Subtotal'=> $order->getSubtotal(),
						'Order_Tax'=> $order->getTaxAmount(),
						'Order_Shipping'=> $order->getShippingAmount(),
						'Order_Discount'=> $order->getDiscountAmount(),
						'Order_Grand_Total'=> $order->getGrandTotal(),
						'Order_Base_Grand_Total'=> $order->getBaseGrandTotal(),
						'Order_Paid'=> $order->getTotalPaid(),
						'Order_Refunded'=> $order->getTotalRefunded(),
						'Order_Due'=> $order->getTotalDue(),
						'Total_Qty_Items_Ordered'=>$order->getTotalQtyOrdered(),
						'Tracking_Number'=> $this->getTrackingInformation($order->getId(),$invoiceId,'number'),
						'Tracking_Partner_Name'=> $this->getTrackingInformation($order->getId(),$invoiceId,'title'),
						'Customer_Name'=> $customer->getFirstname().' '.$customer->getLastname(),
						'Customer_Group'=> $customerGroupName,
						'Customer_Email'=> $customer->getEmail(),
						'Repcode'=> $customer->getRepcode(),
						'Asm_Map'=> $customer->getAsmMap(),
						'Shipping_Name'=> str_replace('&','and',implode(' ',$customerShippingAddress->getFirstname().' '.$customerShippingAddress->getLastname())),
						'Shipping_Company'=> $customerShippingAddress->getCompany(),
						'Shipping_Street'=> str_replace('&','-',implode(' ',$customerShippingAddress->getStreet())),
						'Shipping_Zip'=> $customerShippingAddress->getPostcode(),
						'Shipping_City'=> $customerShippingAddress->getCity(),
						'Shipping_State'=> $code1,
						'Shipping_State_Name'=> $customerShippingAddress->getRegion(),
						'Shipping_Country'=> $customerShippingAddress->getCountry(),
						'Shipping_Phone_Number'=> $customerShippingAddress->getTelephone(),
						'FAX_Number'=> $customerShippingAddress->getFax(),
						'Billing_Name'=> str_replace('&','and',implode(' ',$customerBillingAddress->getFirstname()." ".$customerBillingAddress->getLastname())),
						'Billing_Company'=> $customerBillingAddress->getCompany(),
						'Billing_Street'=> str_replace('&','-',implode(' ',$customerBillingAddress->getStreet())),
						'Billing_Zip'=> $customerBillingAddress->getPostcode(),
						'Billing_City'=> $customerBillingAddress->getCity(),
						'Billing_State'=> $code,
						'Billing_State_Name'=> $customerBillingAddress->getRegion(),
						'Billing_Phone_Number'=> $customerBillingAddress->getTelephone(),
						'FAX_Number'=> $customerBillingAddress->getFax(),
						'Order_Item_Increment'=> $orderItem->getId(),
						'Item_Name'=> preg_replace('/[^\w ]+/', ' ',substr(trim($orderItem->getName()),0,20)),
						'Item_Description'=> preg_replace('/[^\w ]+/', ' ',substr(trim($orderItem->getName()),0,20)),
						'Item_Status'=> $orderItem->getStatus(),
						'Item_SKU'=> $orderSku[0], //$orderItem->getSku(),
						'Item_Options'=> $this->getItemOptions($orderItem), //check
						'Item_Original_Price'=> number_format($original_price, 2, '.', ''),
						'Item_Price'=> $orderItem->getPrice(),
						'Item_Qty_Ordered'=> $orderItem->getQtyOrdered(),
						'Item_Qty_Invoiced'=> $orderItem->getQtyInvoiced(),
						'Item_Qty_Shipped'=> $orderItem->getQtyShipped(),
						'Item_Qty_Canceled'=> $orderItem->getQtyCanceled(),
						'Item_Qty_Refunded'=> $orderItem->getQtyRefunded(),
						'Item_Tax'=> $orderItem->getTaxAmount(),
						'Item_Discount'=> $orderItem->getDiscountAmount(),
						'Item_Total'=> $this->getItemTotal($orderItem),
						'Item_Category1'=>strtoupper($product->getAttributeText('product_category_type')), //$itemCategoryCode,
						'Item_Category2'=>strtoupper($product->getAttributeText('brands')),
						//'Item_Category1'=> $categoryName[0],
						//'Item_Category2'=> $categoryName[1],
						'Item_Category3'=> $categoryName[3],
						'Item_Category4'=> $categoryName[4],
						'Invoice_Number'=> $orderItemInvoice ? $orderItemInvoice->getIncrementId() : '',
						'Invoice_Date'=> date('dmY'), //$orderItemInvoice ? date('dmY',strtotime($orderItemInvoice->getCreatedAt())) : '',
						'Invoice_Time'=> $orderItemInvoice ? date('H:i:s',strtotime($orderItemInvoice->getCreatedAt())) : '',
						// 'Warehouse_Location'=> 'BHI-21/09', //$warehouseLocation, //LIVE : BHI-21/09
						'Warehouse_Location'=> 'NERUL-WH', //$warehouseLocation, //LIVE : BHI-21/09
						'Brand'=> $product->getAttributeText('brands'),
						'Shipped_Value'=> $this->getItemTotal($orderItem),
						'User_Agent'=> $userAgent,
						'Store_Name'=> $storeName,
						'Serial_No'=> '',
						'Credit_Days'=> $credit,
						'Payment_Due_On'=> $dueDate,
						'Is_CST_Used'=> '',
						'GST_NO'=> $customer->getGstin(),
						'BILL_TO_GST_NO'=> $customerBillingAddress->getGstin(),
						'SHIP_TO_GST_NO'=> $customerShippingAddress->getGstin()
						
						
					);
			}
			
			
			$apiXml = '';
			for($i=0;$i<count($orderData);$i++) {
				$apiXml .= '<ord:Order_Api>
							<ord:Document_Type>Invoice</ord:Document_Type>
							<ord:Order_No>'.$orderData[$i]['Invoice_Number'].'</ord:Order_No>
			                <ord:Order_Date_Text>'.$orderData[$i]['Order_Date'].'</ord:Order_Date_Text>
                			<ord:Quantity>'.$orderData[$i]['Item_Qty_Ordered'].'</ord:Quantity>
							<ord:Location>'.$orderData[$i]['Warehouse_Location'].'</ord:Location>
							<ord:Billing_Company_Name>'.$orderData['Billing_Company'].'</ord:Billing_Company_Name>
							<ord:Sell_to_Customer_Name>'.$orderData[$i]['Billing_Name'].'</ord:Sell_to_Customer_Name>
							<ord:Sell_to_Customer_Address>'.substr($orderData[$i]['Billing_Street'], 0,50).'</ord:Sell_to_Customer_Address>
							<ord:Sell_to_Customer_Address1>'.substr($orderData[$i]['Billing_Street'], 50,100).'</ord:Sell_to_Customer_Address1>
			                <ord:Ship_to_Name>'.$orderData[$i]['Shipping_Name'].'</ord:Ship_to_Name>
			                <ord:Ship_to_Name_2>'.$orderData[$i]['Shipping_Name'].'</ord:Ship_to_Name_2>
			                <ord:Ship_to_Address>'.substr($orderData[$i]['Shipping_Street'], 0,50).'</ord:Ship_to_Address>
			                <ord:Ship_to_Address_2>'.substr($orderData[$i]['Shipping_Street'], 50,100).'</ord:Ship_to_Address_2>
			                <ord:Ship_to_Address_3>'.substr($orderData[$i]['Shipping_Street'], 100,100).'</ord:Ship_to_Address_3>
			                <ord:Ship_to_City>'.$orderData[$i]['Shipping_City'].'</ord:Ship_to_City>
			                <ord:Ship_to_State_Code>'.$orderData[$i]['Shipping_State'].'</ord:Ship_to_State_Code>
			                <ord:Ship_to_Post_Code>'.$orderData[$i]['Shipping_Zip'].'</ord:Ship_to_Post_Code>
			                <ord:Ship_to_County>'.$orderData[$i]['Shipping_Country'].'</ord:Ship_to_County>
			                <ord:State>'.$orderData[$i]['Shipping_State'].'</ord:State>
			                <ord:Ship_to_Phone_No_1>'.$orderData[$i]['Shipping_Phone_Number'].'</ord:Ship_to_Phone_No_1>
			                <ord:Ship_to_Phone_No_2>'.$orderData[$i]['Shipping_Phone_Number'].'</ord:Ship_to_Phone_No_2>
			                <ord:StateCode>'.$orderData[$i]['Shipping_State'].'</ord:StateCode>
			                <ord:Store_Name>'.$orderData[$i]['Customer_Name'].'</ord:Store_Name>
			                <ord:Product_Type>'.$orderData[$i]['Brand'].'</ord:Product_Type>
			                <ord:EB_Category>'.$orderData[$i]['Item_Category2'].'</ord:EB_Category>
			                <ord:EB_Product>'.$orderData[$i]['Item_Category1'].'</ord:EB_Product>
			                <ord:Item_No>'.$orderData[$i]['Item_SKU'].'</ord:Item_No>
							<ord:Customer_Item_Name>'.str_replace('&nbsp;', '', $orderData[$i]['Item_Name']).'</ord:Customer_Item_Name>
			                <ord:Item_Description>'.str_replace('&nbsp;', '', $orderData[$i]['Item_Name']).'</ord:Item_Description>
			                <ord:Customer_Item_SKU>'.$orderData[$i]['Item_SKU'].'</ord:Customer_Item_SKU>
			                <ord:Unit_Price_incl_tax>'.$orderData[$i]['Item_Original_Price'].'</ord:Unit_Price_incl_tax>
			                <ord:Tax_Amount>'.$orderData[$i]['Item_Tax'].'</ord:Tax_Amount>
			                <ord:Discount_Amount>'.$orderData[$i]['Item_Discount'].'</ord:Discount_Amount>
			                <ord:AWB_No>'.$orderData[$i]['Tracking_Number'].'</ord:AWB_No>
			                <ord:Tracking_Partner_Name>'.$orderData[$i]['Tracking_Partner_Name'].'</ord:Tracking_Partner_Name>
			                <ord:Order_Payment_Method>'.$orderData[$i]['Order_Payment_Method'].'</ord:Order_Payment_Method>
			                <ord:Invoice_No>'.$orderData[$i]['Order_Number'].'</ord:Invoice_No>
			                <ord:Invoice_Posting_Date_Text>'.$orderData[$i]['Invoice_Date'].'</ord:Invoice_Posting_Date_Text>
							<ord:Customer_Group>'.$orderData[$i]['Customer_Group'].'</ord:Customer_Group>
			                <ord:Repcode>'.$orderData[$i]['Repcode'].'</ord:Repcode>
			                <ord:GST_No>'.$orderData[$i]['GST_NO'].'</ord:GST_No>
			                <ord:Bill_to_Customer_No>'.$orderData[$i]['customer_id'].'</ord:Bill_to_Customer_No>
			                <ord:Bill_To_GST_No>'.$orderData[$i]['BILL_TO_GST_NO'].'</ord:Bill_To_GST_No>
			                <ord:Ship_To_GST_No>'.$orderData[$i]['SHIP_TO_GST_NO'].'</ord:Ship_To_GST_No>
			            </ord:Order_Api>';
			}

			//echo "<pre>".$apiXml."<br>";die;
			$datetime = gmdate("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())); 
			
			try{
			
				$action = 'Create';
				$params = '<x:Envelope xmlns:x="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ord="urn:microsoft-dynamics-schemas/page/order_api">
				    <x:Header/>
				    <x:Body>
				        <ord:CreateMultiple>
				        	<ord:Order_Api_List>
				        		'.$apiXml.'
				        	</ord:Order_Api_List>
				        </ord:CreateMultiple>
				    </x:Body>
				</x:Envelope>';
				$apiUrl = Mage::getStoreConfig('ebautomation/ebautomation_credentials/nav_order_url');
				
				$result = Mage::helper('ebautomation')->callNavisionSOAPApi($apiUrl, $action, $params);
				
				//print_r($result);//die;
				if($result['sBody']->sFault){
					if (strpos($result['sBody']->sFault->faultstring, 'Order already exists.') !== false){
						$response = $datetime." Navision Response : ".$result['sBody']->sFault->faultstring;
						$order->setmapped_status(1);
						$order->setmapped_details($response);
						$order->addStatusHistoryComment($response, false);
						$order->save();
						sleep(5);
						return;
					}else{
    					$response = $datetime." Not Mapped - Navision Response : ".$result['sBody']->sFault->faultstring;
						$order->setmapped_details($response);
						$order->addStatusHistoryComment($response, false);
						$order->save();
						sleep(5);
						return;
    				}
    				
					
				}else{
					//$resultKeys = null;
					if(empty($result['SoapBody'])){
						$response = $datetime." Not Mapped - Navision Response : NULL";
						$order->setmapped_details($response);
						$order->addStatusHistoryComment($response, false);
						$order->save();
						sleep(5);
						return;
					}
					foreach ($result['SoapBody']->CreateMultiple_Result->Order_Api_List->Order_Api as $key => $value) {
						$resultKeys[implode((array)$value->Customer_Item_SKU)] = implode((array)$value->Key);
					}
					if(json_encode($resultKeys) != 'null'){ //json_encode($resultKeys) != null && 
						$response = $datetime.": Order Mapped with these keys: ".json_encode($resultKeys);
						$order->setmapped_status(1);
						$order->setmapped_details("Mapped - ".$response);
						$order->addStatusHistoryComment($response, false);
	 					$order->save();
						sleep(5);
					}else{
						$order->setmapped_details($datetime." Not Mapped - Navision Response : ".json_encode($resultKeys));
						$order->setmapped_status(0);
						$order->addStatusHistoryComment($response, false);
						$order->save();
						sleep(5);
					}
					return;
				}

			}catch(Exception $e){
				$order->setmapped_details($datetime."Not Mapped - ".$e->getMessage());
				$order->addStatusHistoryComment($datetime." Not Mapped - ".$e->getMessage(), false);
 				$order->save();
 				sleep(5);
 				return;
			}
		//}

	 
	}

	function getTrackingInformation($order_id,$invoice_id,$returnValue){
            $track=Mage::getModel('sales/order_shipment_track')->getCollection()
            ->addFieldToFilter('order_id',$order_id)->addFieldToFilter('invoice_id',$invoice_id)->getData();
            
			if($returnValue == 'title'){
				return $track[0]['title'];
			}
			if($returnValue == 'number'){
				return $track[0]['track_number'];
			}
    }



	protected function getItemTotal($item)
    {
        return $item->getRowTotal() - $item->getDiscountAmount() + $item->getTaxAmount() + $item->getWeeeTaxAppliedRowAmount();
    }

	/**
     * Returns the options of the given item separated by comma(s) like this:
     * option1: value1, option2: value2
     *
     * @param Mage_Sales_Model_Order_Item $item The item to return info from
     * @return String The item options
     */

	protected function getItemOptions($item)
    {
        $options = '';
        if ($orderOptions = $this->getItemOrderOptions($item)) {
            foreach ($orderOptions as $_option) {
                if (strlen($options) > 0) {
                    $options .= ', ';
                }
                $options .= $_option['label'].': '.$_option['value'];
            }
        }
        return $options;
    }

    /**
     * Returns all the product options of the given item including additional_options and
     * attributes_info.
     *
     * @param Mage_Sales_Model_Order_Item $item The item to return info from
     * @return Array The item options
     */
    protected function getItemOrderOptions($item)
    {
        $result = array();
        if ($options = $item->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }
        return $result;
    }
	


}
?>
		