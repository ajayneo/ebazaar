<?php //automation for order data to navision in dsr format csv files
//Mahesh Gurav
//16th August 2017
	require_once('../app/Mage.php');
	umask(0);
	Mage::app();
	// date_default_timezone_set('Asia/Calcutta');
$current_hour = date('H', time()); 
if($current_hour >= '10' && $current_hour <= '18'){	
	$to = Mage::getSingleton('core/date')->gmtDate();
	// $to = Mage::getModel('core/date')->date('Y-m-d H:i:s'); // time();
	$lastTime = strtotime($to) - 3600;
	$from = date('Y-m-d H:i:s', $lastTime);
	$orders = Mage::getModel('sales/invoice')->getCollection()
	    ->addAttributeToSelect('order_id')
	    ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to));
	$order_ids = array();
	foreach ($orders as $order) {
		$order_ids[] = $order->getOrderId();
	}
	// print_r($order_ids);
	try{
	  $navexport = Mage::getModel('navisionexport/navexport');
	  	$filename = $navexport->exportOrders($order_ids);
		if($filename){
		  	$ftp_server = "111.119.217.101";
			$ftp_conn = ftp_connect($ftp_server) or exit("Could not connect to $ftp_server");
			$login = ftp_login($ftp_conn, 'mahesh.neo', 'neo@456');   
			$file = Mage::getBaseDir()."/var/export/".$filename;
			// upload file
			if (ftp_put($ftp_conn, "/EB-SALES/sales/".$filename, $file, FTP_ASCII))
			{ 
				// echo "Successfully uploaded $file.";
				Mage::log($filename.' sent to '.$ftp_server,null,'ebautomation.log',true);
			}
			else
			{
				Mage::log("Error uploading ".$filename." to ".$ftp_server,null,'ebautomation.log',true);
			//echo "Error uploading $file.";
			}

 			// close connection
 			ftp_close($ftp_conn);
		}
	}catch(Exception $e){
	    Mage::log($e->getMessage(),null,'ebautomation.log',true);
	}
}	
?>