<?php
class Neo_Custom_Model_Observer
{
    public function handle_adminSystemConfigChangedSection()
    {
    	$isEnabled  = Mage::getStoreConfig('shipmentdelete_section/shipmentdelete_group/shipmentdelete_enabled',Mage::app()->getStore());
    	if($isEnabled){

    		
    		$ordernum  = Mage::getStoreConfig('shipmentdelete_section/shipmentdelete_group/shipmentdelete_orderno',Mage::app()->getStore());
    		if($ordernum != ''){
    			$order = Mage::getModel('sales/order')->loadByIncrementId($ordernum);

				// check if has shipments
				if(!$order->hasShipments()){
				    die('No Shipments');
				}

				//delete shipment
				$shipments = $order->getShipmentsCollection();
				foreach ($shipments as $shipment){
				    $shipment->delete();
				}

				// Reset item shipment qty
				// see Mage_Sales_Model_Order_Item::getSimpleQtyToShip()
				$items = $order->getAllVisibleItems();
				foreach($items as $i){
				   $i->setQtyShipped(0);
				   $i->save();
				}

				Mage::log($ordernum,false,'deleteShipment.log',true);

				//Reset order state
				$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Undo Shipment');
				$order->save();

				//echo 'Done-'.$orderId;	
    		}else{
				Mage::throwException("Please enter order ID");
    		}
    	}else{
			Mage::throwException("Delete is disabled");
    	}
        //Mage::getModel('core/config')->saveConfig('shipmentdelete_section/shipmentdelete_group/shipmentdelete_orderno', '');
    }
}