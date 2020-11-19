<?php require_once('/home/electronicsbazaa/public_html/app/Mage.php');
Mage::app();
umask(0);
ini_set('memory_limit', '-1');
ini_set('max_execution_time','1000');           

// echo "fdssf";


$result = Mage::getModel('ebautomation/customer')->allCustomerMapping();

echo '**********************************done*********************************************';

exit;
?>