<?php
require_once('app/Mage.php');
Mage::app();
echo "Updating Trackers ";
$filepath = Mage::getBaseDir().'/tracking.csv';
$file = fopen($filepath, 'r');
	$trackmodel = Mage::getModel('sales/order_shipment_api');
while (($data = fgetcsv($file)) !== FALSE) {
   $shipmentId = trim($data[0]); 
   echo "<br/>".$shipmentId;
   $title = trim($data[1]); 
   $carrier = trim($data[2]); 
   $awb = trim($data[3]);
   try{
   	// $shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentId);
   	// echo "<pre>";
   	// print_r($shipment->getData());
   	$trackmodel->addTrack($shipmentId,$carrier,$title,$awb);
   }catch(Exception $e){
	echo $e->getMessage(); 
	exit;  	
   }
   // exit;
}
fclose($file);
exit;