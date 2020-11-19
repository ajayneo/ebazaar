<?php //Change Invoice Increment Id Programmatically
require_once('app/Mage.php');
Mage::app();
$old_invoice = '500081612';
// $old_invoice = '500081655';
$new_invoice = '500081658';
// $new_invoice = '500081509';

$inv = Mage::getModel('sales/order_invoice')->loadByIncrementId($old_invoice);
if($inv->getData()){
	$inv->setData('increment_id', $new_invoice);
	$inv->save();
}