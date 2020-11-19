<?php
class Neo_Navisionexport_Model_Cron{	
	public function exportnavisionorders(){
		
		$currentDate = date('Y-m-d');

		$collection = Mage::getResourceModel('sales/order_grid_collection')->getData();

		$orderexport = array();
	  
		foreach($collection as $order)
		{ 
			if(strpos($order['created_at'], $currentDate) !== false || strpos($order['updated_at'], $currentDate) !== false)
			{
				$orderexport[] = $order['entity_id'];
			}
		}
		$orderexport[] = 26336;
		$fromDate = date('Y-m-d 00:00:00');
		$toDate = date('Y-m-d 00:00:00', strtotime($fromDate . ' +1 day'));  

		Mage::getModel('bluejalappeno_orderexport/export_csvnavweb')->exportOrders($orderexport,str_replace(' ', '_', $fromDate),str_replace(' ', '_', $toDate));
   
	} 

	public function exportnavisionordersreturn(){
		

		$currentDate = date('Y-m-d');

		$collection = Mage::getResourceModel('sales/order_grid_collection')->getData();

		$orderexport = array();
	  
		foreach($collection as $order)
		{ 
			if(strpos($order['created_at'], $currentDate) !== false || strpos($order['updated_at'], $currentDate) !== false)
			{
				if($order['status'] == 'canceled' || $order['status'] == 'closed' || $order['status'] == 'returned_and_refunded')
				{
					$orderexport[] = $order['entity_id']; 
				}
			}
		} 

		$orderexport[] = 26336;
		$fromDate = date('Y-m-d 00:00:00');
		$toDate = date('Y-m-d 00:00:00', strtotime($fromDate . ' +1 day'));

		Mage::getModel('bluejalappeno_orderexport/export_csvnavwebret')->exportOrders($orderexport,str_replace(' ', '_', $fromDate),str_replace(' ', '_', $toDate));
   
	}

	public function salesreport(){
		Mage::log("Navisionexport order details report initiated ",null,"navisionexport.log",true);
		date_default_timezone_set('Asia/Calcutta');

		$curr_time = Mage::getModel('core/date')->gmtDate();
		//$past_two_hours = date('Y-m-d h:i:s', strtotime($curr_time . '-2 hours'));
		$three_am = date('Y-m-d 03:00:00');
		$now_time = date('ha',strtotime($curr_time));
		$collection = Mage::getModel('sales/order')->getCollection()
		//->addFieldToFilter('created_at',array('gt' => $three_am))
		//->addFieldToFilter('created_at',array('lt' => $curr_time));
		->addFieldToFilter('created_at',array('from' => $three_am, 'to' => $curr_time));

		$grandtotal = $collection->getColumnValues('base_grand_total');
		$grand_total = array_sum($grandtotal);
		$collection = $collection->getData();

		$orderexport = array();

		foreach($collection as $order)
		{ 
		    $orderexport[] = $order['entity_id']; 
		} 

		if(count($orderexport) >= 1 ){

		    $filename = Mage::getModel('bluejalappeno_orderexport/export_hourlyexport')->exportOrders($orderexport,$past_two_hours,$curr_time);

		    Mage::log("Navisionexport order details report csv created ".$filename." on ".$now_time,null,"navisionexport.log",true);
		    $subject = "Order export till ".$now_time;

		    $totalSum = Mage::helper('core')->currency($grand_total, true, false);

		    // $msg = "Total ".count($orderexport)." orders placed between ".date('ha', strtotime($curr_time . '-2 hours'))." and ".$now_time;

		    $msg = "Totol order ".count($orderexport)." placed till ".$now_time."<br>";
		    $msg .="Grand total of all orders <b>".$totalSum."<b>";
		    $reciepient = array('web.maheshgurav@gmail.com','rekha@compuindia.com','rajeev@electronicsbazaar.com');
		    // $reciepient = array('web.maheshgurav@gmail.com');
		    $mail = $this->_sendCSV($filename, $subject, $msg, $reciepient);
		    if($mail){
		    Mage::log("Navisionexport order details report mail sent ".$filename." on ".$now_time,null,"navisionexport.log",true);
		    }
		}else{
			Mage::log("Navisionexport order details null ",null,"navisionexport.log",true);
		}
	}

	public function ordersummary(){
		ini_set('max_execution_time', 0);
		$openbox = array(60,94);
		$new_cats = array(76,89);
		$prewoned_cats = array(11,97);
		$accessories = array(7);
		
	    $label_array = array('Openbox' => $openbox, 'Preowned'=>$prewoned_cats, 'New'=>$new_cats, 'Accessories'=>$accessories); 
		try{

		
		date_default_timezone_set('Asia/Calcutta');
		$time_start = microtime();
		$curr_time = Mage::getModel('core/date')->gmtDate();
		Mage::log("Navisionexport order summary cron start @ ".$curr_time, null, "navisionexport.log", true);
		
		$now_time = date('ha',strtotime($curr_time));
		// $now_date = date('Y_m_d',strtotime($curr_time));
		// $past_two_hours = date('Y-m-d h:i:s', strtotime($curr_time . '-23 hours'));
		// $three_am = date('Y-m-d 03:00:00');
		$day_start = date('Y-m-d 00:00:00');

		//$eleven_morning = date('2017-04-11 00:00:00');
		//$eleven_midnight = date('2017-04-11 23:59:59');
		$past_day_start = date('Y-m-d 00:00:00', strtotime('-1 day')); 
		$past_day_end = date('Y-m-d 23:59:59', strtotime('-1 day')); 
		
		$last_order_id = Mage::getStoreConfig('sidebanner_section/dashboard/dailysales');

		$collection = Mage::getModel('sales/order')->getCollection();

		if($last_order_id){
			$collection->addFieldToFilter('increment_id', array('gt'=>$last_order_id));			
		}
		//->addFieldToFilter('created_at',array('gteq' => $day_start));
		// ->addFieldToFilter('created_at',array('from' => $past_day_start, 'to' => $past_day_end));
		
		//->addFieldToFilter('created_at',array('gt' => $past_two_hours))
		//->addFieldToFilter('created_at',array('lt' => $curr_time));
		// ->addFieldToFilter('created_at',array('from' => $eleven_morning, 'to' => $eleven_midnight));
		//$grandtotal = $collection->getColumnValues('base_grand_total');
		//$grand_total = array_sum($grandtotal);
		
		ini_set('memory_limit', '-1');

		$collection = $collection->getData();
		
		$orderexport = array();
		$partners = array();
		$count = 0;
		foreach($collection as $order)
		{ 
			// if($order['increment_id'] > Mage::getStoreConfig('sidebanner_section/dashboard/dailysales')){
			    $orderexport[] = $order['entity_id']; 
			    if(!in_array($order['customer_email'], $partners)){ 
				    $partners[] = $order['customer_email']; 
			    }
			    $count++;
			// }
		}
		$orderexport1 = array_unique($orderexport);
		$data = Mage::getModel('bluejalappeno_orderexport/export_hourlyexport')->exportOrders($orderexport1,$day_start,$curr_time);
		// $data = Mage::getModel('bluejalappeno_orderexport/export_hourlyexport')->exportOrders($orderexport,$day_start,$past_day_end);
		$filename = $data['filename'];
		$products_cat = $data['cats'];

	            
		// $products_cat = array();
		// foreach ($orderexport as $order_id) {
		// 	$order = Mage::getModel('sales/order')->load($order_id);
		// 	$items = $order->getItemsCollection();

		// 	foreach ($items as $key => $item){
		// 		$item_id = $item->getItemId();
		// 		$_product = Mage::getModel('catalog/product')->load($item->getProductId());
		// 		$categories = $_product->getCategoryIds();
		// 		foreach ($label_array as $key => $value) {
		//             $inter = array_intersect($categories, $value);
		            
		//             if(count($inter) >= 1){
		//                $products_cat[$key][$order_id][$item_id]['product_id'] = $item->getProductId();
		//                $products_cat[$key][$order_id][$item_id]['qty'] = $item->getQtyOrdered();
		//                $products_cat[$key][$order_id][$item_id]['price'] = $item->getBaseRowTotalInclTax();
		//             }

		//         }
		// 	}
		// }

		$table_str = '';
		$labels = array_keys($label_array);

		$table_str = 'Total number of Partners billed '.count($partners).'<br/>';
		$table_str .='<table style="border-top:1px solid #ddd; border-left:1px solid #ddd; margin:10px 0 0 0;">';
		$table_str .= '<tr><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Category</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Orders</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Qty Ordered</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Item Total</th></tr>';
		$fp_array[] = array('Total Orders','Category','Item Total','Total Qty Ordered');
		$item_grand_total = 0;
		$item_grand_qty = 0;
		foreach ($labels as $label) {
			$total_orders = count($products_cat[$label]);
			$total_qty = 0;
			$total_price = 0;

			foreach ($products_cat[$label] as $key => $value) {
				foreach ($value as $item_id => $value) {
					$total_qty += $value['qty'];
					$total_price += $value['price'];
				}
			}
			$table_str .= '<tr>';
			$table_str .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$label.'</td>';
			$table_str .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$total_orders.'</td>';
			$table_str .='<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$total_qty.'</td>';
			$table_str .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$total_price.'</td>';
			$table_str .='</tr>';
			$incurrency_format = Mage::helper('core')->currency($total_price, true, false);
			$fp_array[] = array($total_orders,$label,$incurrency_format,$total_qty);
			$item_grand_total += $total_price; 
			$item_grand_qty += $total_qty;
		}
		
		$grand_total = Mage::helper('core')->currency($item_grand_total, true, false);
		
			$table_str .= '<tr>';
			$table_str .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Total</td>';
			$table_str .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.count($orderexport).'</td>';
			$table_str .='<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$item_grand_qty.'</td>';
			$table_str .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$grand_total.'</td>';
			$table_str .='</tr>';
		$table_str .='</table>';
		// print_r($fp_array);
		/*$filename = 'hourly_summary_'.$now_time.'_'.$now_date.'.csv';
		$file_test = Mage::getBaseDir() . DS .'kaw17842knlpREGdasp9045'. DS .'navision'. DS .'export'. DS .$filename;
		
		$fp = fopen($file_test, 'w');
		foreach ($fp_array as $key => $fields) {
		    fputcsv($fp, $fields);
		}
		fclose($fp);*/




		$time_end = microtime();
		$time = $time_end - $time_start;
		Mage::log("Navisionexport summary exceution took ".$time." secs for ".$filename,"navisionexport.log",true);	

		$subject = "Order summary for orders placed till ".$now_time;
		// $subject = "Order summary for orders placed yesterday on ".date('Y-m-d', strtotime('-1 day'));
		$msg = '';
		$msg .= $table_str;
		
		//send email
		
	      
	    //$mail = $this->_sendCSV($filename, $subject, $msg, $reciepient);
	    if($mail){
	    	$end_time = Mage::getModel('core/date')->gmtDate();
			Mage::log("Navisionexport order summary cron start @ ".$end_time, null, "navisionexport.log", true);
			exit;
	    }

		}catch(Exception $e){
			Mage::log("Navisionexport ordersummary error ".$e->getMessage(), null, "navisionexport.log", true);			
		}

		exit;
	}

	protected function _sendCSV($filename, $subject, $msg, $reciepient) {
	    $template_id = "navisionexport_hourly_email_template";
	    $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
	    $template_variables = array(
	        'filename' => $filename,
	        'msg' =>$msg,
	        );
	    $storeId = Mage::app()->getStore()->getId();
	    $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);

	    $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');

	    $file = Mage::getBaseDir() . DS .'kaw17842knlpREGdasp9045'. DS .'navision'. DS .'export'. DS .$filename;
	    $attachment = file_get_contents($file);
	    $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
	    $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
	    //$reciepient = array('web.maheshgurav@gmail.com'); 
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
	        return $z_mail;
	        Mage::log("Navisionexport ordersummary email sent",null,"navisionexport.log",true);
	        exit;
	    } catch (Exception $e) {
	        Mage::log("Navisionexport ordersummary email error".$e->getMessage(),null,"navisionexport.log",true);
	        exit;
	    }
	    exit;
	}

	public function newsummary(){
		
		date_default_timezone_set('Asia/Calcutta');
		$midnight = date('Y-m-d 00:00:00');
		$curr_time = Mage::getModel('core/date')->gmtDate();
		//orders collection
		$collection = Mage::getModel('sales/order')->getCollection()
		->addFieldToFilter('created_at',array('gteq' => $midnight));
		$products_cat = array();
		$orderexport = array();
		$partners = array();
		foreach ($collection as $order) {
			if(!in_array($order['customer_email'], $partners)){
			    $partners[] = $order['customer_email']; 
		    }
		    $order_id = $order['entity_id'];
		    $orderexport[] = $order_id;
		}//collection foreach

		//get file
		$data = Mage::getModel('bluejalappeno_orderexport/export_hourlyexport')->exportOrders($orderexport,$midnight,$curr_time);
		$filename = $data['filename'];
		$products_cat = $data['cats'];
		$email_variables = $this->summarize($products_cat);
		Mage::log("Navisionexport newsummary file and summary ".$filename,null,"navisionexport.log",true);
		$partners_count = count($partners);
		$partner_array = array('partners'=>$partners_count);
		$vars = array_merge($partner_array,$email_variables);
		$subject = "Order Summary for today till ".date('ha');
		$reciepient = array('web.maheshgurav@gmail.com');
		Mage::log("Navisionexport newsummary vars ready to mail",null,"navisionexport.log",true);
		$mail = $this->_sendnewCSV($filename, $subject, $vars, $reciepient);
		Mage::log("Navisionexport newsummary exit ",null,"navisionexport.log",true);
	}


	public function summarize($products_cat){
		$total_qty = 0;
		$total_price = 0;
		$total_orders = 0;
		$no_order = 0;
		$variables = array();
		foreach ($products_cat as $category => $order) {
			$qty = 0;
			$price = 0;
			foreach ($order as $items) {
		        foreach ($items as $item) {
		            $qty += $item['qty'];
		            $price += $item['price'];
		        }//items foreach
		    }//orders foreach
		    $no_order = count($order);
		    $variables[strtolower($category).'_qty'] = $qty;
		    $variables[strtolower($category).'_orders'] = $no_order;
		    $variables[strtolower($category).'_price'] = Mage::helper('core')->currency($price, true, false);
			$total_qty += $qty;
			$total_price += $price;
			$total_orders += $no_order;
		}

		$variables['total_orders'] = $total_orders;	
		$variables['total_qty'] = $total_qty;	
		$variables['total_price'] = Mage::helper('core')->currency($total_price, true, false);
		Mage::log("Navisionexport newsummary variables return ",null,"navisionexport.log",true);
		return $variables;
	}

	protected function _sendnewCSV($filename, $subject, $vars, $reciepient) {
	    $template_id = "newsummary_template";
	    $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
	    
	    $template_variables = $vars;
	    $storeId = Mage::app()->getStore()->getId();
	    $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);

	    $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');

	    $file = Mage::getBaseDir() . DS .'kaw17842knlpREGdasp9045'. DS .'navision'. DS .'export'. DS .$filename;
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
	        Mage::log("Navisionexport newsummary senmail OK",null,"navisionexport.log",true);
	        
	    } catch (Exception $e) {
	        Mage::log("Navisionexport newsummary email error".$e->getMessage(),null,"navisionexport.log",true);
	    }
	    return;
	}

	
} 