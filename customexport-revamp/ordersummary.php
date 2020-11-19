<?php
require_once('../app/Mage.php');
umask(0);
Mage::app();

//order summary revamp
Mage::log("Ordersummary start",null,"customexport.log",true);
date_default_timezone_set('Asia/Calcutta');
$filename = 'ordersummary_'.date('Ymd').'.csv';
$file_path = Mage::getBaseDir() . DS .'customexport'. DS .$filename; 

//read csv to get last row
$rows = file($file_path);
$last_row = array_pop($rows);
$data = str_getcsv($last_row);

//initialize conditions
$last_order_id = 0;
$condition = '';
if(!empty($data[0])){
	//last_order_id
	$last_order_id = $data[0];
	$condition .=" o.increment_id > $last_order_id";
	$fp = fopen($file_path, 'a');
	Mage::log("Ordersummary lastorderid $last_order_id",null,"customexport.log",true);
}else{
	// echo "empty file";
	$date_from = date('Y-m-d 00:00:00');
	$gm_from = gmdate('Y-m-d H:i:s',strtotime($date_from));
	$date_to = date('Y-m-d 23:59:59');
	$gm_to = gmdate('Y-m-d H:i:s',strtotime($date_to));
	$condition .=" o.created_at BETWEEN '$gm_from' AND '$gm_to'";
	$fp = fopen($file_path, 'w');
	Mage::log("Ordersummary new file",null,"customexport.log",true);
}

//query for orders
$query = "SELECT o.increment_id as 'order_id', i.sku, i.name, o.customer_firstname as 'cust_name', c0.city as 'City',  c0.parent_id as 'parent_id' , o.customer_email, i.row_total_incl_tax as total, i.qty_ordered as qty, c1.category_id as Openbox, c2.category_id as New, c3.category_id as Preowned, c4.category_id as Accessories FROM sales_flat_order as o 
LEFT JOIN sales_flat_order_item as i ON i.order_id = o.entity_id
LEFT JOIN sales_flat_order_address as c0 ON c0.parent_id = o.entity_id
LEFT JOIN catalog_category_product as c1 ON c1.product_id = i.product_id AND c1.category_id IN (60,94)
LEFT JOIN catalog_category_product as c2 ON c2.product_id = i.product_id AND c2.category_id IN (76,14)
LEFT JOIN catalog_category_product as c3 ON c3.product_id = i.product_id AND c3.category_id IN (11,97)
LEFT JOIN catalog_category_product as c4 ON c4.product_id = i.product_id AND c4.category_id IN (7)";
if($condition){
	$query .=" WHERE".$condition." GROUP BY c0.parent_id,i.sku;";
}

//run query on database tables
try{
	$read = Mage::getSingleton('core/resource')->getConnection('core_read');
	$results = $read->query($query);
	$results = $results->fetchAll();
}catch(Exception $e){
	Mage::log("Ordersummary DB Error ".$e->getMessage(),null,"customexport.log",true);
}


//create csv from records found
$count = 0;
if($results){
	Mage::log("Ordersummary items ".count($results)." ".date('Ymd h:ia'),null,"customexport.log",true);
	//echo  "results found <br>";
	foreach($results as $key => $o){
		if($count == 0 && $last_order_id == 0){
			fputcsv($fp, array('Order Id','SKU','Product Name','Store Name','Item Total','Qty','Category','City','Email'));
		}
		
		if($o['Openbox']){
			$cat = 'Openbox';
		}else if($o['New']){
			$cat = 'New';
		}else if($o['Preowned']){
			$cat = 'Preowned';
		}else if($o['Accessories']){
			$cat = 'Accessories';
		}
		// else if($o['Brand_Refurbished']){
		// 	$cat = 'Brand Refurbished';
		// }
		

		fputcsv($fp, array($o['order_id'],$o['sku'],$o['name'],$o['cust_name'],$o['total'],$o['qty'],$cat,$o['City'],$o['customer_email']));
		$count++;
	}
	fclose();
}


//read csv to get records and make summary
Mage::log("Ordersummary create variables for email",null,"customexport.log",true);
$row = 1;
if (($handle = fopen($file_path, "r")) !== FALSE) {
	$variables = array();
    $temp = array();
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        // echo "<p> $num fields in line $row: <br /></p>\n";
        if($row !== 1){
        	$temp[strtolower($data[6]).'_partner'][] = $data[7];
        	$temp[strtolower($data[6]).'_order'][] = $data[0];
        	$variables[strtolower($data[6]).'_qty'] += $data[5];
        	$variables[strtolower($data[6]).'_price'] += $data[4];
        	$variables['total_qty'] += $data[5];
        	$variables['total_price'] += $data[4];
        	$temp['total_order'][] = $data[0];
        	$temp['total_customer'][] = $data[8];
        	$variables[strtolower($data[6]).'_orders'] += count(array_unique($orders[strtolower($data[6]).'_order']));
        	//$variables['partners'] += count(array_unique($customers[strtolower($data[6]).'_partner']));
        	//$variables['total_orders'] += count(array_unique($orders[strtolower($data[6]).'_order']));
        }
        $row++;
        //for ($c=0; $c < $num; $c++) {
            //echo $data[$c] . "<br />\n";
        //}
    }
    fclose($handle);
}
// echo "<pre>";
$cat_array = array('openbox','new','preowned','accessories','brand_refurbished');
foreach ($cat_array as $cat) {
	$variables[$cat.'_orders'] += count(array_unique($temp[$cat.'_order']));
}

$variables['partners'] = count(array_unique($temp['total_customer']));
$variables['total_orders'] = count(array_unique($temp['total_order']));

// print_r($variables);
// exit;

$subject = "Order summary for orders placed till ".date('Y-m-d ha');
// $reciepient = array('web.maheshgurav@gmail.com','sandeepmukherjee0488@gmail.com');
$reciepient = array('rekha@compuindia.com','rajeev@electronicsbazaar.com','akhilesh@electronicsbazaar.com','kushlesh@electronicsbazaar.com','rajkumarvarma@kaykay.co.in','sharad@compuindia.com','vivek.c@kaykay.co.in','web.maheshgurav@gmail.com','sathish.vittal@electronicsbazaar.com','praveen@electronicsbazaar.com','somsubhra.b@electronicsbazaar.com','chhitij@electronicsbazaar.com','sandeepmukherjee0488@gmail.com','vipul.g@electronicsbazaar.com','keval@electronicsbazaar.com');
$template_id = "newsummary_template";
$emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);

$template_variables = $variables;
$storeId = Mage::app()->getStore()->getId();
$processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);

$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');

//$file = Mage::getBaseDir() . DS .'kaw17842knlpREGdasp9045'. DS .'navision'. DS .'export'. DS .$filename;
$file = $file_path;
$filename = 'ordersummary_'.date('Ymd').'.csv';
$attachment = file_get_contents($file);
$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
try {
    $z_mail = new Zend_Mail('utf-8');

    $z_mail->setBodyHtml($processedTemplate)
        ->setSubject($subject)
        ->addTo($reciepient)
        ->setFrom($senderEmail, $senderName);

    $attach = new Zend_Mime_Part($attachment);
    $attach->type = 'application/csv';
    $attach->disposition = Zend_Mime::DISPOSITION_INLINE;
    $attach->encoding    = Zend_Mime::ENCODING_8BIT;
    $attach->filename    = $filename;

    $z_mail->addAttachment($attach);
    $z_mail->send();
    Mage::log("Ordersummary email sent ",null,"customexport.log",true);
} catch (Exception $e) {
	Mage::log("Ordersummary email error ".$e->getMessage(),null,"customexport.log",true);
}
	Mage::log("Ordersummary stop ",null,"customexport.log",true);