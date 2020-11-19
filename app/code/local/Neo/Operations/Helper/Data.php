<?php class Neo_Operations_Helper_Data extends Mage_Core_Helper_Abstract{
	/**
	*@function: auto cancel orders due pending payments
	*@auth: Mahesh Gurav
	*@date: 19th Jul 2018
	**/

	public function cancelorder($increment_id){
		try{
			Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
			$i = 1; $qty_comment = '';
			$order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);
			$update_reason = 'Product restocked after order auto cancellation (order# '.$increment_id.')';
			foreach ($order->getAllItems() as $item) {
				$productId  = $item->getProductId();
				$qty = (int)$item->getQtyOrdered();
				$product = Mage::getModel('catalog/product')->load($productId);
				
				$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
				if ($stockItem->getId() > 0 and $stockItem->getManageStock()) {
					$product_qty_before = (int)$stockItem->getQty();
					$qty = (int)($product_qty_before + $qty);
					$stockItem->setQty($qty);
					$stockItem->setIsInStock((int)($qty > 0));
					$qty_comment .= $i.") Sku : ".$product->getSku()."  Qty Changed From: ".$product_qty_before." => ".$qty."\r\n";
					
				}

				$product->setQtyUpdateFlag(1);
				$product->setQtyUpdateReason($update_reason);
				try{
					$product->save();
					$stockItem->save();
				}catch(Exception $e){
					Mage::log("Product #".$product->getSku()." : ".$e->getMessage(),null,'auto_cancel_order.log');
					exit;
				}
				$i++;
			}

			$order->addStatusHistoryComment($qty_comment.' '.$update_reason, false);
			$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true); 
			$order->save();
		}catch(Exception $e){
			Mage::log($e->getMessage(),null,'auto_cancel_order.log');
			exit;
		}
		return true;
	}
}
	 