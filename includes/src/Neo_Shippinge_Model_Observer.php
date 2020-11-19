<?php
class Neo_Shippinge_Model_Observer
{
	public function core_block_abstract_prepare_layout_after($observer)
	{
		$block = $observer->getBlock();
		if($block instanceof Mage_Adminhtml_Block_Sales_Order_Shipment_View) {
			$shipment = $block->getShipment();
			$order_id = $shipment->getOrder()->getIncrementId();
			$fil1_name = 'CompletedPackageDetails_order_'.$order_id.'_shipment.png';
			$fil2_name = 'AssociatedShipments_order_'.$order_id.'_shipment.png';
			$dir_path = Mage::getBaseDir('var').'/fadex_shipment/';
			$url1 = Mage::helper('adminhtml')->getUrl('adminhtml/shippinge/download', array('file_path' => $fil1_name));
			$url2 = Mage::helper('adminhtml')->getUrl('adminhtml/shippinge/download', array('file_path' => $fil2_name));
			
			$methods = Mage::getModel('shippinge/fedex')->init();
			$fedex_methods = array();
			foreach($methods as $method) {
				$fedex_methods[] = $method['service'];
			}
			foreach($shipment->getTracksCollection() as $tracks) {
				if(in_array($tracks->getTitle(),$fedex_methods)) {
					if(file_exists($dir_path.$fil1_name)) {
						$block->addButton('download_main', array(
							'label'     => Mage::helper('sales')->__('Download Main Label'),
							'class'     => 'save',
							'onclick'   => 'setLocation(\''.$url1.'\')'
						));
					}
					if(file_exists($dir_path.$fil2_name)) {
						$block->addButton('download_return', array(
							'label'     => Mage::helper('sales')->__('Download Return Label'),
							'class'     => 'save',
							'onclick'   => 'setLocation(\''.$url2.'\')'
						));
					}
					break;
				}
			}
		}
	}
	
	public function sales_order_shipment_save_after($observer)
	{
		Mage::log(1,null,'bhargav.log',true);
		$shipment = $observer->getShipment();
		$order = $shipment->getOrder();
		$payment_code = $order->getPayment()->getMethodInstance()->getCode();
		$order_status = $order->getStatus();
		
		if($payment_code == "cashondelivery" && $order_status != 'codpaymentpending' ) {
			Mage::getSingleton('core/session')->setCODStatus('codpaymentpending');
			Mage::log(2,null,'bhargav.log',true);
			//$order->setStatus('codpaymentpending')->save();
			//$order->save();
		}
	}

	public function sales_order_invoice_save_after($observer)
	{
		Mage::log(3,null,'bhargav.log',true);
		$invoice = $observer->getInvoice();
		$order = $invoice->getOrder();
		$payment_code = $order->getPayment()->getMethodInstance()->getCode();
		$order_status = $order->getStatus();
		$state = $order->getState();
		if($payment_code == "cashondelivery" && $order_status != 'codprocessing' ) {
			Mage::log(4,null,'bhargav.log',true);
			Mage::getSingleton('core/session')->setCODStatus('codprocessing');
			//$order->setStatus('codprocessing')->save();
			//$order->save();
		}
	}
	
	public function sales_order_save_before($observer)
	{
		$order = $observer->getOrder();
		$payment_code = $order->getPayment()->getMethodInstance()->getCode();
		$order_status = $order->getStatus();
		$state = $order->getState();
		if($payment_code == "cashondelivery") {
			$CODStatus = Mage::getSingleton('core/session')->getCODStatus();
			if(isset($CODStatus) && !empty($CODStatus)) {
				Mage::log($CODStatus,null,'bhargav.log',true);
				$order->setStatus($CODStatus);
			}
		}
		Mage::getSingleton('core/session')->unsCODStatus();
	}
}
?>
