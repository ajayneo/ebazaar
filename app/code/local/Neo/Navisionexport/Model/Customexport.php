<?php
class Neo_Navisionexport_Model_Customexport{

	public function bestsellers(){
		date_default_timezone_set('Asia/Calcutta');
		$from = date('Y-m-d 00:00:00');
		$to = date('Y-m-d 23:59:59');

	 	// number of products to display
	    $productCount = 25; 
	    
	    // store ID
	    $storeId    = Mage::app()->getStore()->getId();

	 	$products = Mage::getResourceModel('reports/product_collection')
	                    // ->addAttributeToSelect('sku')
	                    ->addOrderedQty($from, $to)
	                    ->setStoreId($storeId)
	                    ->addStoreFilter($storeId)                    
	                    ->setOrder('ordered_qty', 'desc')
	                    ->setPageSize($productCount); 
	    
	    Mage::getSingleton('catalog/product_status')
	            ->addVisibleFilterToCollection($products);
	    Mage::getSingleton('catalog/product_visibility')
	            ->addVisibleInCatalogFilterToCollection($products);
	    // echo $products->getSelect();
	    $ordered_items = $products->getData();
	    // echo "<pre>"; print_r($ordered_items); echo "</pre>"; exit;
	    $print_array = array();
	    $table_html = '';
	    $table_html = '<table style="border-top:1px solid #ddd; border-left:1px solid #ddd; margin:10px 0 0 0;">';
	    $table_html .= '<tr><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">SKU</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Product Name</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Total Price(incl tax)</th></tr>';
	    foreach ($ordered_items as $key => $item) {
	    	$print_array[$key]['sku'] = $sku = $item['sku']; 
	    	$print_array[$key]['order_items_name'] = $name = $item['order_items_name']; 
	    	// $print_array[$key]['ordered_qty'] = $qty = $item['ordered_qty'];
	    	$print_array[$key]['order_items_price'] = $price = (int)$item['order_items_total'];
	    	
	    	$table_html .= '<tr>';
	        $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$sku.'</td>';
	        
	        $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$name.'</td>';
	        
	        //$table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$qty.'</td>';
	        $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$price.'</td>';
	        $table_html .= '</tr>'; 
	    }
	    $table_html .= '<table>';

	    $filename = "best_sellers_".date('Y_m_d').".csv";
	    $filepath = Mage::getBaseDir() . DS .'kaw17842knlpREGdasp9045'. DS .'navision'. DS .'export'. DS .$filename;
	    
	    $file = fopen($filepath,"w");
	    $header = array('SKU','Product Name','Total Sale(incl tax)');
	    fputcsv($file, $header);
	    
	    foreach ($print_array as $key => $value) {
	         fputcsv($file, $value);
	    }
	    fclose($file);


	    $vars = array();
	    $subject = "Bestsellers for today ".date('Y-m-d');
	    $reciepient = array('web.maheshgurav@gmail.com');
	    //$mail = $this->_bestsellerCSV($filename, $subject, $table_html, $reciepient);
	    if($mail){
	    	// $end_time = Mage::getModel('core/date')->gmtDate();
			Mage::log("Navisionexport customexport bestsellers end ", null, "navisionexport.log", true);
			exit;
	    }
	}

	public function _bestsellerCSV($filename, $subject, $table_html, $reciepient){
		$template_id = "navisionexport_hourly_email_template";
	    $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
	    $template_variables = array(
	        'filename' => $filename,
	        'msg' =>$table_html,
	        );
	    $storeId = Mage::app()->getStore()->getId();
	    $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);
	    // print_r($processedTemplate); exit;
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
		}catch(Exception $e){

		}
	}

	public function citytargets(){
		Mage::log("Navisionexport citytargets start",null,"navisionexport.log",true);
		$read = Mage::getSingleton('core/resource')->getConnection('core_read'); 
		$last_order_id = Mage::getStoreConfig('sidebanner_section/dashboard/dailysales');
		$from = date('Y-m-d 00:00:00');
		$gm_from = gmdate('Y-m-d H:i:s',strtotime($from));
		$query = "SELECT a.city, SUM(i.row_total_incl_tax) as amount FROM sales_flat_order_item as i INNER JOIN catalog_category_product as c ON c.product_id = i.product_id LEFT JOIN sales_flat_order as o ON o.entity_id = i.order_id INNER JOIN sales_flat_order_address as a ON a.parent_id = o.entity_id AND a.city IN ('Mumbai','Chennai','Hyderabad','Bangalore','Calcutta','Pune','Ahmedabad','Jaipur','Surat','Lucknow','Delhi-NCR','Patna','Bhopal','Indore','Kanpur') AND a.address_type='shipping' WHERE o.created_at > '".$gm_from."' GROUP BY a.city";

		$results = $read->fetchAll($query); 
		$cat_array = array();
		$target = 5000000;
		foreach($results as $r)
		{
		    $cat_array[$r['city']]['Item Total'] = $r['amount'];
		    $cat_array[$r['city']]['Target'] = $target;
		    $percent = round(($r['amount']/$target * 100),2);   
		    $cat_array[$r['city']]['Percent'] = $percent."%";
		}
		Mage::log("Navisionexport citytargets array ready",null,"navisionexport.log",true);

		$header = array_keys($cat_array);
		$table_html ='';
		$table_html .= '<table style="border-top:1px solid #ddd; border-left:1px solid #ddd; margin:10px 0 0 0;">';
		$table_html .= '<tr>';
		$table_html .= '<th style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;"></th>';
		foreach ($header as $key => $value) {
		    $table_html .= '<th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$value.'</th>';
		}
		$table_html .= '</tr>';

		$rows = array('Item Total','Target','Percent');

		foreach ($rows as $row) {
			$table_html .= '<tr>';
			$table_html .='<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$row.'</td>';
			    foreach ($cat_array as $key => $value) {
			        if($row == 'Percent'){
			            $number = $value[$row];
			        }else{
			            $number = (int)$value[$row];
			        }
			    $table_html .='<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$number.'</td>';
			    }
			$table_html .= '</tr>';    
		}

		$table_html .= '</table>';
		Mage::log("Navisionexport citytargets table ready",null,"navisionexport.log",true);
		$reciepient = array('web.maheshgurav@gmail.com');
		$subject = "City sales target summary for today ".date('Y-m-d');
		$this->_sendCityTargets($table_html,$reciepient,$subject);
		Mage::log("Navisionexport citytargets mail sent",null,"navisionexport.log",true);
	}

	private function _sendCityTargets($table_html,$reciepient,$subject){
		$template_id = "navisionexport_hourly_email_template";
	    $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
	    $template_variables = array('msg' =>$table_html);
	    $storeId = Mage::app()->getStore()->getId();
	    $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);
	    // print_r($processedTemplate); exit;
	    $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');

	    //$file = Mage::getBaseDir() . DS .'kaw17842knlpREGdasp9045'. DS .'navision'. DS .'export'. DS .$filename;
	    // $attachment = file_get_contents($file);
	    $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
	    $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
	    //$reciepient = array('web.maheshgurav@gmail.com'); 
	    try {
	        $z_mail = new Zend_Mail('utf-8');

	        $z_mail->setBodyHtml($processedTemplate)
	            ->setSubject($subject)
	            ->addTo($reciepient)
	            ->setFrom($senderEmail, $senderName);

	        // $attach = new Zend_Mime_Part($attachment);
	        // $attach->type = 'application/csv';
	        // $attach->disposition = Zend_Mime::DISPOSITION_INLINE;
	        // $attach->encoding    = Zend_Mime::ENCODING_8BIT;
	        // $attach->filename    = $filename;

	        // $z_mail->addAttachment($attach);
	        $z_mail->send();
	        return $z_mail;
		}catch(Exception $e){

		}
	}

	function updatelastorderid(){
		date_default_timezone_set('Asia/Calcutta');
		$yesterday_from = date('Y-m-d 00:00:00', strtotime('-1 days'));
		$yesterday_to = date('Y-m-d 23:59:59', strtotime('-1 days'));
		$gm_from = gmdate('Y-m-d H:i:s',strtotime($yesterday_from));
		$gm_to = gmdate('Y-m-d H:i:s',strtotime($yesterday_to));
		$read = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sql_q_5 = "SELECT increment_id FROM `sales_flat_order` WHERE `created_at` BETWEEN '".$gm_from."' AND '".$gm_to."' ORDER BY entity_id DESC;";
		$results = $read->query($sql_q_5);
		$results = $results->fetch();
		$last = $count - 1;
		$last_order_id = $results['increment_id']; 
		$last_order_id = $last_order_id;
		Mage::getConfig()->saveConfig('sidebanner_section/dashboard/dailysales',$last_order_id);
	}	
}