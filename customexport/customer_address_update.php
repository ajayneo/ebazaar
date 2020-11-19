<?php

require_once('../app/Mage.php');
Mage::app();
ini_set('memory_limit', '-1');
$size = 1; 
$limit = 1000;
$start = 1;
for ($i=$start; $i <= $start+1000; $i= $i+$limit) {
	
	Mage::getModel('operations/reports')->customerUpdates($size,$limit,$start);
	$size++;
}
?>