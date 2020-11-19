<?php
require_once('../app/Mage.php');
umask(0);
Mage::app();


$utc_date = Mage::getModel('core/date')->date('Y-m-d H:i:s');
$april16 = Mage::getModel('core/date')->date('2016-04-01 00:00:00');
$april17 = Mage::getModel('core/date')->date('2017-04-01 00:00:00');
$novl17 = Mage::getModel('core/date')->date('2017-11-01 00:00:00');
// echo $gm_date = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');

$year_back = date("Y-m-d H:i:s", strtotime("-365 days", strtotime($utc_date)));
$april16_date = date("Y-m-d H:i:s", strtotime($april16));



	/**
	 * Get the resource model
	 */
	$resource = Mage::getSingleton('core/resource');
	
	/**
	 * Retrieve the read connection
	 */
	$readConnection = $resource->getConnection('core_read');
	
	//$query = 'SELECT * FROM ' . $resource->getTableName('catalog/product');
	$query = "SELECT o.increment_id as order_number, o.customer_email, a.region as State, a.city as City, o.grand_total as order_total, o.created_at, MONTH(o.created_at) as month, YEAR(o.created_at) as Year, a.postcode as postcode FROM sales_flat_order as o LEFT JOIN sales_flat_order_address as a ON a.parent_id = o.entity_id AND a.address_type = 'shipping' WHERE (o.`created_at` >= '".$year_back."') ORDER BY o.created_at DESC";
	
	/**
	 * Execute the query and store the results in $results
	 */
	$results = $readConnection->fetchAll($query);
	
	/**
	 * Print out the results
	 */
	 // var_dump($results);
// echo "<pre>";
	$customer_orders = array();
	 foreach ($results as $key => $value) {
	 	# code...
	 	// print_r($value);
	 	$email_id = $value['customer_email'];
	 	// $customer_orders[$email_id][] = $value;
	 	if($email_id !== ''){
		 	if(!in_array($value['month'], $customer_orders[$email_id]['months'])){
		 		$customer_orders[$email_id]['months'][] = $value['month'];
		 	}

		 	$customer_orders[$email_id]['state'] = $value['State'];
		 	$customer_orders[$email_id]['city'] = $value['City'];
		 	$customer_orders[$email_id]['postcode'] = $value['postcode'];
		 	$customer_orders[$email_id]['order_total'] += $value['order_total'];
	 	}
	 	// exit;
	 }

	 // print_r($customer_orders);	
$qtr1 = array(1, 2, 3);
$qtr2 = array(4, 5, 6);
$qtr3 = array(7, 8, 9);
$qtr4 = array(10, 11, 12);

$filename = "revenue.csv";

// $file_path = Mage::getBaseDir() . DS .'var'.  DS .'export'. DS .$filename; 
$file = Mage::getBaseDir() . DS .'var'.  DS .'export'. DS .$filename;

$filepath = fopen($file, 'w');

// var_dump($fp);

fputcsv($filepath, array('Email','State','City','Postcode','Order Total','Customer Status'));


	 foreach ($customer_orders as $email => $orders) {
	 	$all_months = $orders['months'];
	 	$list_months = implode(",", $all_months);
	 	$count_months = count($all_months);
	 	if($count_months >= 10){
	 		$status = 'Active';
	 	}elseif($count_months <= 10 && $count_months >= 3 && array_intersect($all_months, $qtr1) && array_intersect($all_months, $qtr2) && array_intersect($all_months, $qtr3) && array_intersect($all_months, $qtr4)){
	 		$status = 'Semi Active';
	 	}else{
	 		$status = 'Inactive';
	 	}
	 	// echo $status;
	 	if($email !== ''){
	 		fputcsv($filepath, array($email,$orders['state'],$orders['city'],$orders['order_total'],$orders['postcode'],$status,'"'.$list_months.'"'));
	 	}
	 }

fclose($filepath);
// echo $filepath;

// header('Content-Type: application/octet-stream');
// header('Content-Disposition: attachment; filename="' . $filename . '"');
// header('Content-Length: ' . filesize($filepath)); 
// echo readfile($filepath);
exit;
