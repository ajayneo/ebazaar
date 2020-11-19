<?php /** Order export from a heavy module daily 6:30 AM for 1 month's orders CSV file mailing to 20 ids Mahesh Gurav 27th Mar 2018 **/
$start = microtime(true); 
require_once('/home/electronicsbazaa/public_html/app/Mage.php');
umask(0);
Mage::app();
try{
	//date for filter orders
	$date_from = date('Y-m-01 00:00:00');
	$gm_from = gmdate('Y-m-d H:i:s',strtotime($date_from));
		
	//order collection for getting orders from 1st of current month
	$_orders = Mage::getResourceModel('sales/order_collection')->addFieldToSelect('entity_id')->addFieldToFilter('created_at',array('gteq'=>$gm_from))->setOrder('entity_id');

	$order_numbers = $_orders->getColumnValues('entity_id');
	//get CSV
	$filename = Mage::getModel('bluejalappeno_orderexport/export_csv')->exportOrders($order_numbers);
	if(strlen($filename) > 0){
		
	$file_path = Mage::getBaseDir() . DS .'var/export'. DS .$filename;
	
	//date for email body
	$month_end = strtotime('last day of this month', time());
	$month_start = strtotime('first day of this month', time());
	$first_date = date('dS M\'y', $month_start);
	//$last_date = date('dS M\'y', $month_end);
	date_default_timezone_set('Asia/Calcutta');
	$last_date = date('dS M\'y h:ia');
	Mage::log('order export file done at '.date('d-m-Y h:i:s'),null,'cron.log',true);

	//set mail variables
	$subject = "Order export from ".$first_date." to ".$last_date;
	$body = "PFA orders export from ".$first_date." till now.";
	$senderEmail = 'sales@electronicsbazaar.com';
	$senderName = 'Sales';
	$bcc_email = array('web.maheshgurav@gmail.com');
	// $few_emails = array('web.maheshgurav@gmail.com');
	$few_emails = array('prakash@kaykay.co.in','rajeshsoni@kaykay.co.in','robin.r@electronicsbazaar.com','deepti.g@electronicsbazaar.com','ketaki@electronicsbazaar.com','siddhi@electronicsbazaar.com','mahesh.c@electronicsbazaar.com','royas@kaykay.co.in','kushlesh@electronicsbazaar.com','chandan.p@electronicsbazaar.com','archana.r@electronicsbazaar.com','parth@electronicsbazaar.com','rajiv.p@electronicsbazaar.com','taha.b@electronicsbazaar.com','daniel.d@electronicsbazaar.com');
	
	//generate email to send
	$z_mail = new Zend_Mail('utf-8');
	$z_mail->setBodyHtml($body)
	    ->setSubject($subject)
	    ->addTo($few_emails)
	    ->addBcc($bcc_email)
	    ->setFrom($senderEmail, $senderName);
	$attachment = file_get_contents($file_path);
	$attach = new Zend_Mime_Part($attachment);
	$attach->type = 'application/csv';
	$attach->disposition = Zend_Mime::DISPOSITION_INLINE;
	$attach->encoding    = Zend_Mime::ENCODING_8BIT;
	$attach->filename    = $filename;
	$z_mail->addAttachment($attach);
	$z_mail->send();
	$end = microtime(true);
	$execution_time = $end - $start;
	Mage::log("Order export sent with exe time in secs ".$execution_time,null,'cron.log',true);
	}
}catch(Exception $e){
	Mage::log($e->getMessage(),null,'cron.log',true);
}