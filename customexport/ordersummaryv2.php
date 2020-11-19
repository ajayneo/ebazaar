<?php //Mahesh gurav revised on 16th Mar 2018
require_once('/home/electronicsbazaa/public_html/app/Mage.php');

umask(0);

Mage::app();



//order summary revamp

date_default_timezone_set('Asia/Calcutta');

$current_hour  = date('H');

// skip order summary till 6 AM
// if($current_hour < 6){
// 	exit;
// }


$filename = 'orderssummary_'.date('Ymd').'.csv';

// $filename = 'test_ordersummary_'.date('Ymd').'.csv';

// $file_path = Mage::getBaseDir() . DS .'customexport'. DS .$filename; 

$file_path = Mage::getBaseDir() . DS .'var/export'. DS .$filename; 



//read csv to get last row

$rows = file($file_path);

$last_row = array_pop($rows);

$data = str_getcsv($last_row);



//initialize conditions

/*$last_order_id = 0;

$condition = '';

if(!empty($data[0])){



	$last_order_id = $data[0];

	$condition .=" o.increment_id > $last_order_id";

	$fp = fopen($file_path, 'a');

}else{



	$date_from = date('Y-m-d 00:00:00');

	$gm_from = gmdate('Y-m-d H:i:s',strtotime($date_from));

	$date_to = date('Y-m-d 23:59:59');

	$gm_to = gmdate('Y-m-d H:i:s',strtotime($date_to));

	$condition .=" o.created_at BETWEEN '$gm_from' AND '$gm_to'";

	$fp = fopen($file_path, 'w');

}*/



//revised condition on 17th Feb 2018

$date_from = date('Y-m-d 00:00:00');

$gm_from = gmdate('Y-m-d H:i:s',strtotime($date_from));

$date_to = date('Y-m-d 23:59:59');

$gm_to = gmdate('Y-m-d H:i:s',strtotime($date_to));

$condition .=" o.created_at BETWEEN '$gm_from' AND '$gm_to'";

$fp = fopen($file_path, 'w');





$condition .=" AND o.status != 'canceled'";

//query for orders

$query = "SELECT o.increment_id as 'order_id', i.sku, i.name, o.customer_firstname as 'cust_name', c0.city as 'City', c0.region as 'State', c0.parent_id as 'parent_id' , o.customer_email, i.row_total_incl_tax as amount_total, i.qty_ordered as qty, i.base_discount_amount as discount, i.row_total_incl_tax-i.base_discount_amount as net_sale, c1.category_id as Openbox, c2.category_id as New, c3.category_id as Preowned, c4.category_id as Accessories, c5.category_id as 'asismobiles', c6.category_id as 'refurbishedlaptops', c7.category_id as 'featurephones', c8.category_id as 'tablets', c9.category_id as 'refurbishedmobiles', c10.category_id as 'openbox-b', p.method FROM sales_flat_order as o 

LEFT JOIN sales_flat_order_item as i ON i.order_id = o.entity_id

LEFT JOIN sales_flat_order_address as c0 ON c0.parent_id = o.entity_id

LEFT JOIN catalog_category_product as c1 ON c1.product_id = i.product_id AND c1.category_id IN (60,94)

LEFT JOIN catalog_category_product as c2 ON c2.product_id = i.product_id AND c2.category_id IN (76,14)

LEFT JOIN catalog_category_product as c3 ON c3.product_id = i.product_id AND c3.category_id IN (11,97,148,117)

LEFT JOIN catalog_category_product as c5 ON c5.product_id = i.product_id AND c5.category_id IN (112)

LEFT JOIN catalog_category_product as c6 ON c6.product_id = i.product_id AND c6.category_id IN (99)

LEFT JOIN catalog_category_product as c7 ON c7.product_id = i.product_id AND c7.category_id IN (24)

LEFT JOIN catalog_category_product as c4 ON c4.product_id = i.product_id AND c4.category_id IN (7)

LEFT JOIN catalog_category_product as c8 ON c8.product_id = i.product_id AND c8.category_id IN (5)

LEFT JOIN catalog_category_product as c9 ON c9.product_id = i.product_id AND c9.category_id IN (142)
LEFT JOIN catalog_category_product as c10 ON c10.product_id = i.product_id AND c10.category_id = 144

LEFT JOIN sales_flat_order_payment as p ON p.parent_id = o.entity_id";

if($condition){

	$query .=" WHERE".$condition." GROUP BY c0.parent_id,i.sku;";

}



// echo $query; exit;

//run query on database tables

try{

	$read = Mage::getSingleton('core/resource')->getConnection('core_read');

	$results = $read->query($query);

	$results = $results->fetchAll();

}catch(Exception $e){

	// echo $e->getMessage();

}



// print_r($results); die('check');

//create csv from records found

$count = 0;

if($results){



    // $customer->setWebsiteId(1);

    $objAffiliatescontacts = new Neo_Affiliate_Model_Eav_Entity_Attribute_Source_Affiliatescontacts();

	foreach($results as $key => $o){


		if($count == 0 && $last_order_id == 0){


			fputcsv($fp, array('Order Id','SKU','Product Name','Store Name','Amount Total','Qty','Discount','Net Sale','Category','City','State','ASM Name','RSM Name','Payment Method','Customer Email'));

		}

		

		if($o['Openbox']){

			$cat = 'Openbox';

		}else if($o['New']){

			$cat = 'New';

		}else if($o['Preowned']){

			$cat = 'Preowned';

		}else if($o['Accessories']){

			$cat = 'Accessories';

		}else if($o['asismobiles']){

			$cat = 'ASISMobiles';

		}else if($o['refurbishedlaptops']){

			$cat = 'RefurbishedLaptops';

		}else if($o['featurephones']){

			$cat = 'featurephones';

		}else if($o['tablets']){

			$cat = 'tablets';

		}else if($o['refurbishedmobiles']){

			$cat = 'RefurbishedMobiles';

		}else if($o['openbox-b']){

            $cat = 'OpenboxGradeB';

        }else{

			$cat = 'Other';

		}

		$customer_email = $o['customer_email']; 

		 

		$customer = Mage::getModel("customer/customer"); 
    	$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $customer->loadByEmail($customer_email);


        $asm_map = $customer->getAsmMap();

		$asm_name = $objAffiliatescontacts->getOptionText($asm_map);

		

		$rsm_name = $objAffiliatescontacts->getRsmbyasm($asm_map);



		fputcsv($fp, array($o['order_id'],$o['sku'],$o['name'],$o['cust_name'],$o['amount_total'],$o['qty'],$o['discount'],$o['net_sale'],$cat,$o['City'],$o['State'],$asm_name,$rsm_name,$o['method'],$customer_email));

		$count++;

	}

	fclose();

}





//read csv to get records and make summary

$row = 1;

if (($handle = fopen($file_path, "r")) !== FALSE) {

	$variables = array();

    $temp = array();

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        $num = count($data);

        if($row !== 1){

        	$category_name = $data[8];
        	

        	$temp[strtolower($category_name).'_partner'][] = $data[14];

        	$temp[strtolower($category_name).'_order'][] = $data[0];

        	$variables[strtolower($category_name).'_qty'] += $data[5];

        	$variables[strtolower($category_name).'_price'] += $data[4];

        	$variables[strtolower($category_name).'_discount'] += $data[6];

        	$variables[strtolower($category_name).'_net_sale'] += $data[7];

        	$variables['total_qty'] += $data[5];

        	$variables['total_price'] += $data[4];

        	$variables['total_discount'] += $data[6];

        	$variables['total_net_sale'] += $data[7];

        	$temp['total_order'][] = $data[0];

        	$temp['total_customer'][] = $data[14];

        	$variables[strtolower($category_name).'_orders'] += count(array_unique($orders[strtolower($category_name).'_order']));

        	//$variables['partners'] += count(array_unique($customers[strtolower($data[6]).'_partner']));

        	//$variables['total_orders'] += count(array_unique($orders[strtolower($data[6]).'_order']));
        	if(strrpos($data[2], 'VOW') >= 0 && $category_name == 'featurephones'){
        		$temp['vow_order'][] = $data[0];
	        	$variables['vow_net_sale'] += $data[7];
	        	$variables['vow_discount'] += $data[6];
	        	$variables['vow_qty'] += $data[5];
	        	$variables['vow_price'] += $data[4];
        	}

        }

        $row++;

    }

    fclose($handle);

}



$cat_array = array('openbox','new','preowned','accessories','refurbishedlaptops','asismobiles','featurephones','vow','tablets','refurbishedmobiles','openboxgradeb');

foreach ($cat_array as $cat) {

	$variables[$cat.'_orders'] += count(array_unique($temp[$cat.'_order']));

}

$variables['partners'] = count(array_unique($temp['total_customer']));

$variables['total_orders'] = count(array_unique($temp['total_order']));

$subject = "Orders summary for orders placed till ".date('Y-m-d ha');
//if current hour is midnight change the subject
if($current_hour == 23){
	$subject = "Orders summary for orders placed on ".date('Y-m-d');
}
$current_hour  = date('H');

//few emails set on 16th Mar 2018 on Sharad Sir's email
// $few_emails = array();
$few_emails = array('akhilesh@electronicsbazaar.com','archana.r@electronicsbazaar.com','chanchal@kaykay.co.in','divjyot.a@electronicsbazaar.com','hari@electronicsbazaar.com','jayesh.b@kaykay.co.in','ketaki@electronicsbazaar.com','kushlesh@electronicsbazaar.com','omkar.g@kaykay.co.in','prakash@kaykay.co.in','prasad.k@kaykay.co.in','prashantmotwani@kaykay.co.in','raakesh@electronicsbazaar.com','rajiv.p@electronicsbazaar.com','rajkumarvarma@kaykay.co.in','sagar.s@kaykay.co.in','vipul.g@electronicsbazaar.com','yash.r@electronicsbazaar.com');
// $few_emails[] = 'web.maheshgurav@gmail.com';
if($current_hour < 23){
}else{
	$few_emails[] = 'sharad@compuindia.com';
	$few_emails[] = 'web.maheshgurav@gmail.com';
}

// $bcc_email = array('web.maheshgurav@gmail.com');
if($current_hour < 6){
 $few_emails = array('web.maheshgurav@gmail.com');
 exit();
}

//$few_emails = array('web.maheshgurav@gmail.com');

$template_id = "ordersummary_v3_template";

$emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);



$template_variables = $variables;

$storeId = Mage::app()->getStore()->getId();

$processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);

// echo $processedTemplate; exit;

$senderName = Mage::getStoreConfig('trans_email/ident_sales/name');

$senderEmail = Mage::getStoreConfig('trans_email/ident_sales/email');



//$file = Mage::getBaseDir() . DS .'kaw17842knlpREGdasp9045'. DS .'navision'. DS .'export'. DS .$filename;

$file = $file_path;

$filename = 'ordersummary_'.date('Ymd').'.csv';

// $filename = 'test_ordersummary_'.date('Ymd').'.csv';

$attachment = file_get_contents($file);
try {

    $z_mail = new Zend_Mail('utf-8');



    $z_mail->setBodyHtml($processedTemplate)
        ->setSubject($subject)
        ->addTo($few_emails)
        // ->addBcc($bcc_email)
        ->setFrom($senderEmail, $senderName);



    $attach = new Zend_Mime_Part($attachment);

    $attach->type = 'application/csv';

    $attach->disposition = Zend_Mime::DISPOSITION_INLINE;

    $attach->encoding    = Zend_Mime::ENCODING_8BIT;

    $attach->filename    = $filename;



    $z_mail->addAttachment($attach);

    $z_mail->send();

    // Mage::log(date('ha ')."Ordersummary v2 email sent ",null,"customexport.log",true);
    //send arm crm summary on 23 March 2018 Mahesh Gurav
	Mage::getModel('ordersummary/cron')->Armorders();
	Mage::getModel('ordersummary/cron')->Crmorders();

    exit();

} catch (Exception $e) {

	// Mage::log(date('ha ')."Ordersummary v2 email error ".$e->getMessage(),null,"customexport.log",true);

	exit();

}

