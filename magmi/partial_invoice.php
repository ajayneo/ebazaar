<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../app/Mage.php';
Mage::app();
$increment_id = 200055932;
$invoice_items = array('REF/PRXO/M13'=>0,'REF/PRXO/MOB08-FKBB000150'=> 1);
$order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);

	$itemsarray = array();
   foreach($order->getAllItems() as $item) {
   		$sku = $item->getSku();
		$item_id = $item->getItemId(); //order_item_id
		$qty = $item->getQtyOrdered();   //qty ordered for that item
		$itemsarray[$item_id] =  $invoice_items[$sku];
	}

// 	print_r($itemsarray);
// exit;
if($order->canInvoice()) {          
    //$invoiceId = Mage::getModel('sales/order_invoice_api')->create($order->getIncrementId(), $itemsarray ,'Automatic Partial Invoice Mahesh',0,0);
}
echo $invoiceId;
$invoice_inc_id = '500038272';

$invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoice_inc_id);

echo $invoiceId = $invoice->getId();
$invoice_id = '42187';