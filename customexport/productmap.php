<?php
require_once('../app/Mage.php');
umask(0);
Mage::app();
ini_set('max_execution_time','1000');           
ini_set('memory_limit', '-1');
$result = Mage::getModel('ebautomation/product')->allProductMapping();
exit;
?>