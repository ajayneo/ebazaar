<?php
require_once('../app/Mage.php');
umask(0);
Mage::app();
date_default_timezone_set('Asia/Calcutta');
$customerModel = Mage::getModel('customer/customer');
$customer = $customerModel->getCollection();
$customer->addAttributeToFilter('gstin',array('neq'=>''));
$customer->addAttributeToFilter('group_id',array('eq'=>4));
//$data = $customer->getData();
 $filename = "customers_repcode.csv";
$file = Mage::getBaseDir() . DS .'var'.  DS .'export'. DS .$filename;

$filepath = fopen($file,"w");
$header = array('PARTNER STORE NAME','AFFLIATE STORE NAME','REP_CODE','GST IN'); 
fputcsv($filepath, $header);
foreach ($customer as $_customer) {
	// print_r($_customer);
	// echo $_customer->getEntityId(); 
	$modCustomer = $customerModel->load($_customer->getEntityId());

	if(strlen($modCustomer->getGstin()) == 15 && $modCustomer->getEmail() !== 'web.maheshgurav@gmail.com'){
			fputcsv($filepath, array($modCustomer->getFirstname(),$modCustomer->getAffiliateStore(),$modCustomer->getRepcode(),$modCustomer->getGstin()));
	}
}
fclose($filepath);
// echo $file;

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($file)); 
echo readfile($file);
exit;
?>