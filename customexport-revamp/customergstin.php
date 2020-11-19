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
 $filename = "customers_gstin.csv";
$file = Mage::getBaseDir() . DS .'var'.  DS .'export'. DS .$filename;

$filepath = fopen($file,"w");
$header = array('CUSTOMER ID','STORE NAME','EMAIL','MOBILE','GST IN'); 
fputcsv($filepath, $header);
$store_name = '';
foreach ($customer as $_customer) {
	// print_r($_customer);
	// echo $_customer->getEntityId(); 
	$modCustomer = $customerModel->load($_customer->getEntityId());

	if(strlen($modCustomer->getGstin()) == 15 && $modCustomer->getEmail() !== 'web.maheshgurav@gmail.com'){
		if($modCustomer->getAffiliateStore()){
			$store_name = $modCustomer->getAffiliateStore();
		}else{
			$store_name = $modCustomer->getFirstname();
		}

			// fputcsv($filepath, array($modCustomer->getEntityId(),$modCustomer->getAffiliateStore(),$modCustomer->getEmail(),$modCustomer->getMobile(), $modCustomer->getGstin()));
			fputcsv($filepath, array($modCustomer->getEntityId(),$store_name,$modCustomer->getEmail(),$modCustomer->getMobile(), $modCustomer->getGstin()));
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