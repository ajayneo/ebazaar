<?php
    class Neo_Affiliate_Model_Observer
	{
	    public function checkoutTypeOnepageSaveOrder(Varien_Event_Observer $observer)
	    {
	    	$param = Mage::app()->getRequest()->getParam('repcode', '');
			if(!empty($param))
			{
				$aff_customer_name = Mage::app()->getRequest()->getParam('aff_customer_name', '');
				$aff_customer_email = Mage::app()->getRequest()->getParam('aff_customer_email', '');
				$aff_customer_mobile = Mage::app()->getRequest()->getParam('aff_customer_mobile', '');
				$order = $observer->getEvent()->getOrder();

				$quoteId = $order->getQuoteId();
				$quoteObject = Mage::getModel('sales/quote')->load($quoteId);
				$iscformused = $quoteObject->getCustomerCform();

				$i = 0;
				foreach($order->getAllVisibleItems() as $item){
					$product = Mage::getModel('catalog/product')->load($item->getProductId());
					$cats = $product->getCategoryIds();
					$category_name = array();
					foreach ($cats as $category_id){
    					$_cat = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($category_id);
						$category_name[] = $_cat->getName();
    				}
					//$categoryname = implode(',',$category_name);
					$categoryname = end($category_name);
					$affiliate_model = Mage::getModel('neoaffiliate/affiliate');
					$affiliate_model->unset()->setCustomerId($order->getCustomerId())
							->setOrderId($order->getId())
							->setAffiliateName($order->getCustomerName())
			        		->setAffCustomerName($aff_customer_name)
			        		->setAffCustomerEmail($aff_customer_email)
							->setAffCustomerMobile($aff_customer_mobile)
			        		->setOrderNo($order->getIncrementId())
			        		->setRepcode($param)
							->setItemPriceExclTax(Mage::helper('core')->currency($item->getBasePrice(), true, false))
							->setTaxAmount(Mage::helper('core')->currency($item->getTaxAmount(), true, false))
							->setRowTotal(Mage::helper('core')->currency($item->getRowTotalInclTax(), true, false))
							->setCategoryName($categoryname)
							->setProductName($item->getName())
							->setQtyOrdered($item->getQtyOrdered());
					if(!$i){
						$affiliate_model->setOrderTotal(Mage::helper('core')->currency($order->getGrandTotal(), true, false));
						$i++;
					}
					if($iscformused == "true"){
						$affiliate_model->setIscstused('Yes');
					}
					$affiliate_model->save();
				}
			}
	    }

		public function addTrackingDetails(Varien_Event_Observer $observer)
		{
			Mage::log('Strated',null,'Tracking_Details.log');
			Mage::log("Corn run at:-".Mage::getModel('core/date')->date('Y-m-d H:i:s'),null,'Tracking_Details.log');
			$records = Mage::getModel('neoaffiliate/affiliate')->getCollection()->addFieldToFilter('istrackingadded','0');
			Mage::log(count($records),null,'Tracking_Details.log');
		    foreach($records as $record)
		    {
		    	Mage::log($record->getOrderNo(),null,'Tracking_Details.log');
		    	$order_detail = Mage::getModel('sales/order')->load($record->getOrderId());
		    	foreach($order_detail->getShipmentsCollection() as $shipment){
					foreach($shipment->getAllTracks() as $tracking_number){
						$title = $tracking_number->getTitle();
						$track_number = $tracking_number->getTrackNumber();
						if(!empty($track_number)){
							$record->setTrackingTitle($title);
							$record->setTrackingNo($track_number);
							$record->setIstrackingadded('1');
							$record->save();
						}
						Mage::log($title.'--'.$track_number,null,'Tracking_Details.log');
					}
				}
		    }
			Mage::log('Completed',null,'Tracking_Details.log');
		}
	}
?>