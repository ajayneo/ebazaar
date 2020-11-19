<?php

class Neo_Operations_Model_Serviceablepincodes extends Mage_Core_Model_Abstract
{
    protected function _construct(){
       $this->_init("operations/serviceablepincodes");

    }

    public function getPincodeData($pincode = NULL){
		try{
			$picodeData = Mage::getModel('operations/serviceablepincodes')->getCollection()->addFieldToFilter('pincode',$pincode)->getData();
			
			$state_code = $picodeData[0]['state_code'];
			$directory = Mage::getModel('directory/region')->load($state_code,'code');
			
			$directory = $directory->getData();
			
			$stateName = $directory['default_name'];

			$data = array('pincode' => $pincode,
					'city' => $picodeData[0]['city'],
					'region_id' => $directory['region_id'],
					'region' => $directory['name'],
					'country' => $directory['country_id'],
					'ecom_qc' => $picodeData[0]['ecom_qc']
					);

			return $data;
		}catch(exceptions $e){
			 Mage::log($e->getMessage(),null,'pincode_issues.log');
			 return false;
		}
		
		//
	}

	/*public function getProductDeliveryOnDetailsPage($pincode = NULL){
		$picodeData = Mage::getModel('operations/serviceablepincodes')->getCollection()->addFieldToFilter('pincode',$pincode);
		
		if($picodeData->count() > 0){
			$deliveryDays = array();
			$deliveryDays[] = implode(',',$picodeData->getColumnValues('ecom'));
			$deliveryDays[] = implode(',',$picodeData->getColumnValues('dhl_bluedart'));

			
			if($deliveryDays[0] == '#N/A' && $deliveryDays[1] == '#N/A'){
				$response['status'] = 0;
				$response['message'] = "Delivery not available in your location yet.";
				return $response;
			}

			$currentTime = gmdate("H:i", Mage::getModel('core/date')->timestamp(time()));

			$buffertime = 0;
			if($currentTime > '14:00'){
				$buffertime = 1;
			}
			
			$deliveryDays = array_unique($deliveryDays);
			$response = array();
			if(count($deliveryDays) > 1){
				$data['min'] = min($deliveryDays);
				
				if($data['min'] != '#N/A'){
					$estimatedDelivery = $data['min']+$buffertime.' - ';
				}
				$estimatedDelivery .= max($deliveryDays)+$buffertime;
				$response['status'] = 1;
				$response['message'] = "Product will be delivered within ".$estimatedDelivery." working days.";
				return $response;
			}else{
				$days = ($deliveryDays[0]+$buffertime) == 1 ? 'day' : 'days';
				$estimatedDelivery = $deliveryDays[0]+$buffertime;
				$response['status'] = 1;
				$response['message'] = "Product will be delivered within ".$estimatedDelivery." ".$days;
				return $response;
			}
			
		}else{
			$response['status'] = 0;
			$response['message'] = "Delivery not available in your location yet.";
			return $response;
		}

	}*/

	public function array_except($array, $keys) {
		return array_diff_key($array, array_flip((array) $keys));   
	} 

	public function getProductDeliveryOnDetailsPage($pincode = NULL,$paymentMethod = NULL){
		$picodeData = Mage::getModel('operations/serviceablepincodes')->getCollection()->addFieldToFilter('pincode',$pincode);
		//print_r($picodeData->getData());die;
		if($picodeData->count() > 0){
			$deliveryDays = array();
			$deliveryDays['ecom'] = implode(',',$picodeData->getColumnValues('ecom'));
			$deliveryDays['bluedart'] = implode(',',$picodeData->getColumnValues('dhl_bluedart'));
			$deliveryDays['dhl'] = implode(',',$picodeData->getColumnValues('dhl'));

			//$deliveryDays['vexpress'] = implode(',',$picodeData->getColumnValues('vexpress')); // Not delivering shipment with vexpress as requested by Naga on 28-03-2018 
			// if(!empty($deliveryDays[0]) && $deliveryDays[0] == '#N/A' && $deliveryDays[1] == '#N/A' && $deliveryDays[2] == '#N/A'){
			if($deliveryDays['ecom'] == NULL && $deliveryDays['bluedart'] == NULL && $deliveryDays['dhl'] == NULL){
				$response['status'] = 0;
				$response['message'] = "Delivery not available in your location yet.";
				return $response;
			}
			
			$currentTime = gmdate("H:i", Mage::getModel('core/date')->timestamp(time()));

			$buffertime = 0;
			if($currentTime > '14:00'){
				$buffertime = 1;
			}
			
			foreach ($deliveryDays as $key => $value) {
					if($value == '#N/A'){
						continue;
					}
					if($value == 'ND'){
						$value = 0;
					}
					if((int) $value > 0){
						$deliveryFrom[$key] = $value + $buffertime; 
					}
			}
			$deliveryValuesCount = count(array_unique(array_values($deliveryFrom)));
			
			$serviceType = '';
			$estimatedDelivery = '';
			//print_r($deliveryFrom);die;

			if($paymentMethod){
				
				if($paymentMethod == 'cashondelivery' && in_array('ecom', array_keys($deliveryFrom))){
					$deliveryFrom = $this->array_except($deliveryFrom, ['bluedart']);
					$deliveryFrom = $this->array_except($deliveryFrom, ['dhl']);
				}

				if($paymentMethod != 'cashondelivery' && array_intersect(array('dhl','bluedart'), array_keys($deliveryFrom))){
					$deliveryFrom = $this->array_except($deliveryFrom, ['ecom']);
				}
				//if DHL and Bluedart both present then remove Bluedart
				if(in_array('dhl', array_keys($deliveryFrom)) && in_array('bluedart', array_keys($deliveryFrom))){
					$deliveryFrom = $this->array_except($deliveryFrom, ['bluedart']);
				}

				if($paymentMethod == 'cashondelivery' && in_array('ecom', array_keys($deliveryFrom)) && ($deliveryValuesCount ==1 || $deliveryValuesCount >=1)){
					$serviceType = 'ecom';
					$estimatedDelivery = $deliveryFrom['ecom'];
				}elseif($paymentMethod == 'cashondelivery' && $deliveryValuesCount >= 1){
					// $deliveryFrom = $this->array_except($deliveryFrom, ['bluedart']);
					// $minDelivery = min($deliveryFrom);
					// $deliveryFrom = array_flip($deliveryFrom);
					// $serviceType = $deliveryFrom[$minDelivery];
					// $estimatedDelivery = $minDelivery;
					$serviceType = 'ecom';
					$estimatedDelivery = $deliveryFrom['ecom'];
				}elseif($paymentMethod != 'cashondelivery' && in_array('bluedart', array_keys($deliveryFrom))){
					$serviceType = 'bluedart';
					$estimatedDelivery = $deliveryFrom['bluedart'];
				}elseif($paymentMethod != 'cashondelivery' && in_array('dhl', array_keys($deliveryFrom))){
					//remove ecom from non COD orders
					// $deliveryFrom = $this->array_except($deliveryFrom, ['ecom']);
					$serviceType = 'dhl';
					$estimatedDelivery = $deliveryFrom['dhl'];
				}
				
			}else{
				$minDelivery = min($deliveryFrom);
				$maxDelivery = max($deliveryFrom);
				$deliveryFrom = array_flip($deliveryFrom);
				$serviceType = $deliveryFrom[$minDelivery];
				if($minDelivery == $maxDelivery)
					$estimatedDelivery = $minDelivery;
				else
					$estimatedDelivery = $minDelivery.'-'.$maxDelivery;	
				
			}
			
			$response = array();
			if($estimatedDelivery == ''){
				$response['status'] = 0;
				$response['serviceType'] = $serviceType;
				$response['estimatedDelivery'] = $estimatedDelivery;
				$response['message'] = "Delivery not available in your location yet.";
			}else{
				$response['status'] = 1;
				$response['serviceType'] = $serviceType;
				$response['estimatedDelivery'] = $estimatedDelivery;
				$response['message'] = "Product will be delivered within ".$estimatedDelivery." working days.";

			}

			
		}else{
			$response['status'] = 0;
			$response['message'] = "Delivery not available in your location yet.";
			
		}
		return $response;
	}

	public function orderCancelBackToStock($order, $posts = NULL){
		try{
			
			//if($order->canCancel()) {
			$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save(); 
			//$order->cancel()->save();
			//}   
			 $i = 1; $qty_comment = '';

			 foreach ($order->getAllItems() as $item) {
        		
	            $productId  = $item->getProductId();
			    $qty = (int)$item->getQtyOrdered();
			    $product = Mage::getModel('catalog/product')->load($productId);
			    $stock_obj = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
			    $stockData = $stock_obj->getData();
			    $product_qty_before = (int)$stock_obj->getQty();
			    $product_qty_after = (int)($product_qty_before + $qty); 
			    $stockData['qty'] = $product_qty_after;
			    
			    if($product_qty_after != 0) {
			        $stockData['is_in_stock'] = 1;
			    }else{
			        $stockData['is_in_stock'] = 0;
			    }

			    $qty_comment .= $i.") Sku : ".$product->getSku()."  Qty Changed From: ".$product_qty_before." => ".$product_qty_after."\r\n";

			    $productInfoData = $product->getData();
			    $productInfoData['updated_at'] = $curr_date;
			    $product->setData($productInfoData);
			    $product->setStockData($stockData);
			    $product->setQtyUpdateFlag(1);
			    $product->setQtyUpdateReason('Product restocked after order cancellation (order: '.$order->getIncrementId().'). Reason: '.$posts['remarks']);
			    try{
			    	$product->save();
			    	sleep(0.5); /// Sleep for 0.5 seconds
				}catch(Exception $e){
					Mage::log("Product #".$product->getSku()." : ".$e->getMessage(),null,'cancel_order.log');
				}
			    $i++;
	        }

	   		
            $adminUserName = Mage::getSingleton('admin/session')->getUser()->getUsername();//add the class to the body.
        	$order->addStatusHistoryComment($qty_comment.'. Remarks added : '.$posts['remarks'].'. Order canceled by '.$adminUserName, false);
		    
		    $order->save();
			$order->sendOrderUpdateEmail($notify=true, $comment='Cancelled by Electronicsbazaar.com<br>Reason : '.$posts['remarks']);
			Mage::getSingleton("adminhtml/session")->addSuccess("Order Id #".$order->getIncrementId()." canceled successfully.");
			return true;
		}catch(Exception $e){
			$order->addStatusHistoryComment($e->getMessage(), false);
			$order->save();
			Mage::log("order #".$order->getIncrementId()." : ".$e->getMessage(),null,'cancel_order.log');
			return false;
		}
	}

	/*
	* @author : Sonali Kosrabe
	* @purpose : To update orders city, state from pincodes
	* @dates : 26-oct-2017
	*/
	public function orderAddressUpdate($orderId){
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
			
		if($order->getBillingAddress()){
			
			$billingPincode = $order->getBillingAddress()->getPostcode();
			$data = $this->getPincodeData($billingPincode);
			//print_r($data);die;
			if($data){
				$billingAddress = Mage::getModel('sales/order_address')->load($order->getBillingAddress()->getId());
				$billingAddress->setPostcode($data['pincode']);
				$billingAddress->setCity($data['city']);
				$billingAddress->setRegion($data['region'])->setRegionId($data['region_id']);
				$billingAddress->setCountryId($data['country']);
				try{
					$billingAddress->save();
					Mage::log('Billing Address Updated for Order #'.$orderId,null,'order_address_update.log',true);
				}catch(exceptions $e){
					Mage::log('Error in updating orders billing address of order #'.$orderId,null,'order_address_update_error.log',true);
				}
				
			}else{
				Mage::log('Error in updating orders billing address of order #'.$orderId,null,'order_address_update_error.log',true);
			}
			
		}
		if($order->getShippingAddress()){
			
			$shippingPincode = $order->getShippingAddress()->getPostcode();
			$data = $this->getPincodeData($shippingPincode);
			
			if($data){
				$shippingAddress = Mage::getModel('sales/order_address')->load($order->getShippingAddress()->getId());
				$shippingAddress->setPostcode($data['pincode']);
				$shippingAddress->setCity($data['city']);
				$shippingAddress->setRegion($data['region'])->setRegionId($data['region_id']);
				$shippingAddress->setCountryId($data['country']);
				try{
					$shippingAddress->save();
					Mage::log('Shipping Address Updated for Order #'.$orderId,null,'order_address_update.log',true);
				}catch(exceptions $e){
					Mage::log('error in updating orders billing address of order #'.$orderId,null,'order_address_update_error.log',true);
				}
				
			}else{
				Mage::log('error in updating orders billing address of order #'.$orderId,null,'order_address_update_error.log',true);
			}
			
		}
	}

}
	 