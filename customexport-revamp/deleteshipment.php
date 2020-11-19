<?php require_once('../app/Mage.php');
// Varien_Profiler::enable();
// Mage::setIsDeveloperMode(true);
// ini_set('display_errors', 1);
	umask(0);
	Mage::app();
	Mage::register('isSecureArea', 1);
// ob_start();
	//Update Order idd
	$orderId = 200055797;
	$orderArray = array();
	// $orderArray = array('200066419','200067299','200067328','200067329','200067331','200067335','200067336','200067338');
	// $orderArray = array('200067299','200067328','200067329','200067331','200067335','200067336','200067338');
	$orderArray = array('200067328','200067329','200067331','200067335','200067336','200067338');
	
	$sql = '';
	foreach ($orderArray as $key=>$increment_id) {
		// try{
			# code...
			// echo $increment_id;
			// exit;
			$order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);
			// check if has shipmentsd
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
			//Reset order state
			$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Undo Shipment');
			$order->save();
			Mage::log($increment_id,false,'deleteShipment.log',true);
			// echo 'Done';

		// }catch(Exception $e){
		// 	echo $e->getMessage();
		// }

	}
// ob_clean();
// ob_exit();

?>