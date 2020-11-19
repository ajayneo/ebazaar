<?php
require_once('../app/Mage.php');
umask(0);
Mage::app();
ini_set('max_execution_time','1000');           
ini_set('memory_limit', '-1');
$reports = Mage::getModel("productpricereport/productpricereport")->getCollection();
foreach ($reports as $report) {
	# code...
	$product = Mage::getModel('catalog/product')->load($report->getProductId());
	echo $report->getProductId()."-";
	$report->setName($product->getName())->save();
	echo $report->getProductId().'-'.$report->getName()."<br>";
	
}
exit;
?>