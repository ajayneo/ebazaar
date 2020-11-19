<?php //Change Orders State Programmatically
require_once('app/Mage.php');
Mage::app();
// $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
// $order->setData('state', "complete");
// $order->setStatus("complete");       
// $history = $order->addStatusHistoryComment('Order was set to Complete by our automation tool.', false);
// $history->setIsCustomerNotified(false);
// $order->save();

//Change payment method programmatically
// $orderId = '200111822'; // Incremented Order Id
// $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
// $payment = $order->getPayment();
// $payment->setMethod('cashondelivery'); // Assuming 'test' is updated payment method
// $payment->save();
// $order->save();

$filepath = Mage::getBaseDir().'/orders_state.csv';
$file = fopen($filepath, 'r');
$flag = true;
$row = 0;
while (($data = fgetcsv($file)) !== FALSE) {
	if($flag) { $flag = false; continue; }
	echo "<pre>";
	$order_id = $data[0]; 
	$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);

	echo "Order Id ".$order_id." Status ".$data[1]." State ".$data[2]." Method".$data[3];
	$order->setData('state', $data[2]);
	$order->setStatus($data[1]); 
	$payment = $order->getPayment();
	$payment->setMethod($data[3]);
	$order->save();

	// foreach($order->getShipmentsCollection() as $shipment)
	// {
	//     // print_r($shipment->getData()); //get each shipment data here...
	//     // $shipmentId = $shipment->getIncrementId();
	// }

	// if($shipmentId){
	// 	// print_r($shipment)
	// 	// echo $shipmentId;
	// 	// print_r($shipment);
	// 	//set payment
		
	// 	//$payment->save();
	// 	//create invoice createInvoiceByOrder
	// 	// var_dump($order->canInvoice());
	// 	if($order->canInvoice() && $data[2] !== 'new'){
	// 		// Mage::getModel('ebautomation/ebautomation')->createInvoiceByOrder($order_id);
	// 		// echo "Invoice Added for Order#".$data[0];
	// 	}
	// 	//add shipment tracking
	// 	//set order status
	// }
	// print_r($data);
	

	echo "</pre>";
	// if($row == 9) exit('10 rows done');
	$row++;
}
fclose($file);