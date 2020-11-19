<?php
class Neo_Ebautomation_NavisionController extends Mage_Core_Controller_Front_Action
{


	
	public function productAllMtoNAction(){
		$products = Mage::getModel("catalog/product")->getCollection()->addFieldToFilter('automation_status','');
		$products->getSelect()->limit(5);
		//echo $products->count();
		$productsData = array();
		foreach($products as $product) {
			$_cat = array();
			$categoryName = array();

			foreach ($product->getCategoryIds() as $Id) {
				$_cat = Mage::getModel('catalog/category')->load($Id);
				$categoryName[] = $_cat->getName();
			}

			$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);

			$attributeSetModel = Mage::getModel("eav/entity_attribute_set");
			$attributeSetModel->load($product->getAttributeSetId());
			$attributeSetName = $attributeSetModel->getAttributeSetName();
			
			$product = Mage::getModel("catalog/product")->load($product->getId());
			$hsnCode = Mage::helper('indiagst')->getHsnBySku($product->getSku());
			//$category_ids = implode("|", $categoryName);
			$category = $categoryName[0];
			$subcategory = $categoryName[1];
			$website_ids = implode(',',$product->getWebsiteIds());
			$taxClassId = $product->getTaxClassId();
		    $taxClass = Mage::getModel('tax/class')->load($taxClassId);
		    $taxClassName = $taxClass->getClassName();
			
			$productsData[] = array('sku'=>$product->getSku(),
								'nav_item_no'=>$product->getSku(),
								//'website'=>implode(',',$product->getWebsiteIds()),
								//'store'=>$product->getStoreId(),
								'type'=>$product->getTypeId(),
								'category'=>$category,
								'subcategory'=>$subcategory,
								'name'=>$product->getName(),
								//'qty'=>(int)$stock->getQty(),
								//'price'=>$product->getPrice(),
								'short_description'=>$product->getShortDescription(),
								'hsn_code'=>$hsnCode,
								'status'=>$product->getStatus(),
								'brand'=>$product->getAttributeText('brands'),
								//'category_ids'=>$category_ids,
								'tax_class_id'=>'GST18'//$taxClassName
							);

			

		}
		$allProducts['allproducts'] = $productsData;
		$this->getResponse()->setHeader('Content-type', 'application/json', true);
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($allProducts));
	}


	
	
	

	public function setProductPriceAndQtyAction(){
		$_params = $this->getRequest()->getParams();
		
		$qty = $_params['qty'];
		$price = str_replace(',', '', $_params['price']);
		
		if(!isset($_params['sku']) || empty($_params['sku'])){
			echo json_encode(array('status' => 0, 'message' => "SKU Not Defined."));
			exit;
		}

		if(isset($_params['price']) && (!is_numeric($price) || $price < 0)) { // || (!is_numeric($qty) || $qty < 0)){
			echo json_encode(array('status' => 0, 'message' => "Data Invalid."));
			exit;
		}
		if(isset($_params['qty']) && (!is_numeric($qty))){
			echo json_encode(array('status' => 0, 'message' => "Data Invalid."));
			exit;
		}


		$reason = $_params['reason']; 
		
		$statusFlag = 2; //Disable
		Mage::app()->getStore()->setConfig('catalog/frontend/flat_catalog_product', 0);

		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$_params['sku']);
		$product->load($product->getId());
		if($product){
			$isPriceUpdate = false;
			$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());

			if(isset($_params['price'])){		// update only price
				$product->setData('price',$price);
				if($stockItem->getQty() > 0){
					$statusFlag = 1;
				}
				
				
			}
			
			if (isset($_params['qty']) && $stockItem->getId() > 0 and $stockItem->getManageStock()) { // update only qty
				$changedQty = $stockItem->getQty() + (int)$qty; 
				$stockItem->setQty($changedQty);
				$stockItem->setIsInStock((int)($changedQty > 0));

				$product->setQtyUpdateReason($reason);
				$product->setQtyUpdateFlag(1);
				

				if($product->getPrice() != NULL && $product->getPrice() > 0){
					$statusFlag = 1;
				}
			}

			try{
				
				$product->setStatus($statusFlag);
				if($product->save() && $stockItem->save()){
					echo json_encode(array('status' => 1, 'message' => "Product updated succesfully."));
					exit;
				}else{
					echo json_encode(array('status' => 0, 'message' => "Unable to save status."));
					exit;
				}
			}catch(Exception $ex){
				echo json_encode(array('status' => 0, 'message' => $ex->getMessage()));
				exit;
			}
		}else{
			echo json_encode(array('status' => 0, 'message' => "Sku not found."));
				exit;
		}
		
	}

	


	public function orderDSRAction(){
		$orders = Mage::getModel('sales/order')->getCollection()
				->addFieldToFilter('status','complete')
				->addFieldToFilter('customer_id','26764');
		$orders->getSelect()->order('entity_id DESC')->limit(5);
		$orderData = array();				
		foreach ($orders as $order) {
			
			$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
			$customerGroupName = Mage::getModel('customer/group')->load($order->getCustomerGroupId())->getCustomerGroupCode();
			$billingAddressId = $customer->getDefaultBilling();
			$shippingAddressId = $customer->getDefaultShipping();
			$customerBillingAddress = Mage::getModel('customer/address')->load($billingAddressId);
			$customerShippingAddress = Mage::getModel('customer/address')->load($shippingAddressId);

			$shippingStateCode = $stateCodes[$customerShippingAddress->getRegion()];
			$billingStateCode = $stateCodes[$customerBillingAddress->getRegion()];
			$code = Mage::helper('ebautomation')->stateCodeMapping($customerBillingAddress->getRegionCode()); 
			$code1 = Mage::helper('ebautomation')->stateCodeMapping($customerShippingAddress->getRegionCode());

			if(strlen($order->getRemoteIp()) > 0) { 
	            $userAgent = 'Website';
	        } else {
	            $userAgent = 'Application';
	        }

	        $dueDate = '';
	        if($order->getPayment()->getMethod() == 'banktransfer'){

	            $collectionDelivery = Mage::getModel("banktransferdelivery/delivery")->getCollection()->addFieldToSelect('delivery')->addFieldToFilter('order_num',$order->getRealOrderId())->getFirstItem()->getData();

	            $da = $collectionDelivery['delivery'] + 5;
	                    
	            $date = explode(' ',$order->getCreatedAt());
	            $NewDays = date ("d-M-Y", strtotime ($date[0] ."+".$da." days")); 

	            $credit     = $collectionDelivery['delivery'];
	            $dueDate    = $NewDays;
	        }


			foreach ($order->getItemsCollection() as $orderItem) {

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


								
				$orderData[] = array(
						'Order Number' => $order->getIncrementId(),
						'Order Date'=> date('d/m/Y',strtotime($order->getCreatedAt())),
						'Order Time'=> date('H:i:s',strtotime($order->getCreatedAt())),
						'Order Status'=> $order->getStatusLabel(),
						'Order Purchased From'=> $storeName,
						'Order Payment Method'=> $order->getPayment()->getMethodInstance()->getTitle(),
						'Credit Card Type'=> $order->getPayment()->getCcType(),
						'Order Shipping Method'=> $order->getShippingDescription(),
						'Order Subtotal'=> $order->getSubtotal(),
						'Order Tax'=> $order->getTaxAmount(),
						'Order Shipping'=> $order->getShippingAmount(),
						'Order Discount'=> $order->getDiscountAmount(),
						'Order Grand Total'=> $order->getGrandTotal(),
						'Order Base Grand Total'=> $order->getBaseGrandTotal(),
						'Order Paid'=> $order->getTotalPaid(),
						'Order Refunded'=> $order->getTotalRefunded(),
						'Order Due'=> $order->getTotalDue(),
						'Total Qty Items Ordered'=>$order->getTotalQtyOrdered(),
						'Tracking Number'=> $this->getTrackingInformation($order->getId(),$invoiceId,'number'),
						'Tracking Partner Name'=> $this->getTrackingInformation($order->getId(),$invoiceId,'title'),
						'Customer Name'=> $customer->getFirstname().' '.$customer->getLastname(),
						'Customer Group'=> $customerGroupName,
						'Customer Email'=> $customer->getEmail(),
						'Repcode'=> $customer->getRepcode(),
						'Asm Map'=> $customer->getAsmMap(),
						'Shipping Name'=> $customerShippingAddress->getFirstname()." ".$customerBillingAddress->getLastname(),
						'Shipping Company'=> $customerShippingAddress->getCompany(),
						'Shipping Street'=> implode(' ',$customerShippingAddress->getStreet()),
						'Shipping Zip'=> $customerShippingAddress->getPostcode(),
						'Shipping City'=> $customerShippingAddress->getCity(),
						'Shipping State'=> $code1,
						'Shipping State Name'=> $customerShippingAddress->getRegion(),
						'Shipping Phone Number'=> $customerShippingAddress->getTelephone(),
						'FAX Number'=> $customerShippingAddress->getFax(),
						'Billing Name'=> $customerBillingAddress->getFirstname()." ".$customerBillingAddress->getLastname(),
						'Billing Company'=> $customerBillingAddress->getCompany(),
						'Billing Street'=> implode(' ',$customerBillingAddress->getStreet()),
						'Billing Zip'=> $customerBillingAddress->getPostcode(),
						'Billing City'=> $customerBillingAddress->getCity(),
						'Billing State'=> $code,
						'Billing State Name'=> $customerBillingAddress->getRegion(),
						'Billing Phone Number'=> $customerBillingAddress->getTelephone(),
						'FAX Number'=> $customerBillingAddress->getFax(),
						'Order Item Increment'=> $orderItem->getId(),
						'Item Name'=> $orderItem->getName(),
						'Item Status'=> $orderItem->getStatus(),
						'Item SKU'=> $orderItem->getSku(),
						'Item Options'=> $this->getItemOptions($orderItem), //check
						'Item Original Price'=> $orderItem->getOriginalPrice(),
						'Item Price'=> $orderItem->getPrice(),
						'Item Qty Ordered'=> $orderItem->getQtyOrdered(),
						'Item Qty Invoiced'=> $orderItem->getQtyInvoiced(),
						'Item Qty Shipped'=> $orderItem->getQtyShipped(),
						'Item Qty Canceled'=> $orderItem->getQtyCanceled(),
						'Item Qty Refunded'=> $orderItem->getQtyRefunded(),
						'Item Tax'=> $orderItem->getTaxAmount(),
						'Item Discount'=> $orderItem->getDiscountAmount(),
						'Item Total'=> $this->getItemTotal($orderItem),
						'Item Category1'=> $categoryName[0],
						'Item Category2'=> $categoryName[1],
						'Item Category3'=> $categoryName[3],
						'Item Category4'=> $categoryName[4],
						'Invoice Number'=> $orderItemInvoice ? $orderItemInvoice->getIncrementId() : '',
						'Invoice Date'=> $orderItemInvoice ? date('d/m/Y',strtotime($orderItemInvoice->getCreatedAt())) : '',
						'Invoice Time'=> $orderItemInvoice ? date('H:i:s',strtotime($orderItemInvoice->getCreatedAt())) : '',
						'Warehouse Location'=> $warehouseLocation,
						'Brand'=> $product->getAttributeText('brands'),
						'Shipped Value'=> $this->getItemTotal($orderItem),
						'User Agent'=> $userAgent,
						'Serial No'=> '',//$orderItemInvoice ? $orderItemInvoice->getSerial() : '',
						'Credit Days'=> '',
						'Payment Due On'=> '',
						'Is CST Used'=> '',
						'GST IN'=> $customer->getGstin(),
						'Bill to GST IN'=> $customerBillingAddress->getGstin(),
						'Ship to GST IN'=> $customerShippingAddress->getGstin()
						
						
					);
			}
		}
		
		$allOrders['order'] = $orderData;
		$this->getResponse()->setHeader('Content-type', 'application/json', true);
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($allOrders));
		
	}

	function getTrackingInformation($order_id,$invoice_id,$returnValue){
        //$shipmentCollection = $order->getShipmentsCollection();
        //foreach($shipmentCollection as $shipment){
            //$shipmentIncrementId = $shipment->getIncrementId();
            $track=Mage::getModel('sales/order_shipment_track')->getCollection()
            ->addFieldToFilter('order_id',$order_id)->addFieldToFilter('invoice_id',$invoice_id)->getData();
            
             
             if($returnValue == 'title'){
                return $track[0]['title'];
             }
             if($returnValue == 'number'){
                return $track[0]['track_number'];
             }
            
        //}
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

	public function allCustomerMappingAction($page,$limit){
		$customerModel = Mage::getModel('customer/customer');
		$customers = $customerModel->getCollection();
		//$customers->joinAttribute('gstin', 'customer_address/gstin', 'default_shipping', null, 'left');
		$customers->addFieldToFilter('gstin',array('notnull'=>true));
		$allCustomers = array();
		foreach ($customers as $customer) {
			$customerData = Mage::getModel('customer/customer')->load($customer->getId());
			/*$billingAddressId = $customerData->getDefaultBilling();
			$shippingAddressId = $customerData->getDefaultShipping();
			$customerBillingAddress = Mage::getModel('customer/address')->load($billingAddressId);
			$customerShippingAddress = Mage::getModel('customer/address')->load($shippingAddressId);

			
			$shippingStateCode = $stateCodes[$customerShippingAddress->getRegion()];
			$billingStateCode = $stateCodes[$customerBillingAddress->getRegion()];
			$code = Mage::helper('ebautomation')->stateCodeMapping($customerBillingAddress->getRegionCode()); 
			$code1 = Mage::helper('ebautomation')->stateCodeMapping($customerShippingAddress->getRegionCode()); */
			$groupName = Mage::getModel('customer/group')->load($customerData->getGroupId())->getCustomerGroupCode();

			$stateCodes = Mage::helper('indiagst')->getStateCodes();
			$reg = Mage::getModel('directory/region')->load($customerData->getCusState());
			
			$cusStateCode = Mage::helper('ebautomation')->stateCodeMapping($reg->getCode());

			$asm_map = $customer->getResource()->getAttribute('asm_map')->getFrontend()->getValue($customerData);
			
			$allCustomers[] = array('id'=>$customerData->getId(),
						'repcode'=>$customerData->getRepcode(),
						'firstname'=>$customerData->getFirstname(),
						'lastname'=>$customerData->getLastname(),
						'email'=>$customerData->getEmail(),
						'group'=>$groupName,
						'asm_map'=>$asm_map,
						'mobile'=>$customerData->getMobile(),
						'taxvat'=>$customerData->getTaxvat(),
						'gstin' =>$customerData->getGstin(),
						'pincode'=>$customerData->getPincode(),
						'city'=>$customerData->getCusCity(),
						'state'=>$cusStateCode,
						'country'=>$customerData->getCusCountry(),
						'affiliate_store'=>$customerData->getAffiliateStore()
						/*'billing_address' => array(
										'firstname'=>$customerBillingAddress->getFirstname(),
										'lastname'=>$customerBillingAddress->getLastname(),
										'company'=>$customerBillingAddress->getCompany(),
										'street'=>implode(' ',$customerBillingAddress->getStreet()),
										'city'=>$customerBillingAddress->getCity(),
										'country'=>Mage::getModel('directory/country')->load($customerBillingAddress->getCountry())->getName(),
										'region'=>$code,
										'state_code'=>$billingStateCode,
										'postcode'=>$customerBillingAddress->getPostcode(),
										'telephone'=>$customerBillingAddress->getTelephone(),
										'fax'=>$customerBillingAddress->getFax()
							),
						'shipping_address' => array(
										'firstname'=>$customerShippingAddress->getFirstname(),
										'lastname'=>$customerShippingAddress->getLastname(),
										'company'=>$customerShippingAddress->getCompany(),
										'street'=>implode(' ',$customerShippingAddress->getStreet()),
										'city'=>$customerShippingAddress->getCity(),
										'country'=>Mage::getModel('directory/country')->load($customerShippingAddress->getCountry())->getName(),
										'region'=>$code1,
										'state_code'=>$shippingStateCode,
										'postcode'=>$customerShippingAddress->getPostcode(),
										'telephone'=>$customerShippingAddress->getTelephone(),
										'fax'=>$customerShippingAddress->getFax(),
										'gstin'=>$customerShippingAddress->getGstin()
							)*/
						);
		}
		$customerJson['allCustomers'] = $allCustomers;
		$this->getResponse()->setHeader('Content-type', 'application/json', true);
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($customerJson));
	}

	public function soapAction(){
		echo "Hello";
		$url = "http://111.119.217.101:8013/EBWebAutomation/WS/Amiable%20Electronics%20Pvt.%20Ltd./Page/ItemCard";
		$client = new SoapClient($url);

		$client->__getTypes();      
		$client->__getFunctions();  

		$result = $client->functionName();  
	}


}?>	