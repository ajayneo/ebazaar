<?php //auto cancel orders 5 days old
//author: Mahesh Gurav
//date: 01/09/2017
require_once('../app/Mage.php'); 
Mage::app();

	$days_ago = date('Y-m-d 00:00:00', strtotime('-5 days', strtotime(date('Y-m-d 23:59:59'))));
	//5 days earlier date UTC method
	$gm_five_days = gmdate('Y-m-d H:i:s', strtotime($days_ago));
	
	//order ids more than 5 days aged
	$_orders = Mage::getModel('sales/order')->getCollection()
	->addFieldToFilter('status',array('neq' => 'complete'))
	->addFieldToFilter('state',array('eq' => 'new'))
	->addAttributeToSelect('entity_id')
	->addFieldToFilter('created_at',array('lt' => $gm_five_days));
	
	$order_numbers = '';
	$table_html = '';
	$table_html .= '<p>Auto canceled '.count($_orders).' orders.</p>';
	$table_html .= '<table style="border-top:1px solid #ddd; border-left:1px solid #ddd; margin:10px 0 0 0;">';
	$table_html .= '<tr><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Order No</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Item Name</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Item Sku</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Qty</th></tr>';
	foreach ($_orders as $order) {
		$_order = Mage::getModel('sales/order')->load($order->getEntityId());
		$items = $_order->getAllItems();
		$_increment_id = $_order->getIncrementId();
		$order_numbers .= $_increment_id.' ';
		foreach ($items as $item) {
			$table_html .= '<tr>';
	        $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$_increment_id.'</td>';
	        $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$item->getName().'</td>';
	        $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$item->getSku().'</td>';
	        $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.(int)$item->getQtyOrdered().'</td>';
	        $table_html .= '</tr>';
		}
		$_order->setData('state', 'canceled');   
		$_order->setStatus('canceled');
		//this will auto add stock
		$_order->cancel(); 
		$history = $_order->addStatusHistoryComment('Auto Cancel 5 Days Old Orders', false);
		$history->setIsCustomerNotified(false);
		$_order->save();
		$history->save();
		// break;
	}
	$table_html .= '</table>';

	//send email for cancelled orders
	$subject = "Auto Cancelled Orders ".date('Y-m-d');
	$template_id = "navisionexport_hourly_email_template";
    $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
    $template_variables = array('msg' =>$table_html);
    $storeId = Mage::app()->getStore()->getId();
    $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);
    $senderName = 'Cancellation - Sales';
	$senderEmail = 'sales@electronicsbazaar.com';
	$reciepient = array('web.maheshgurav@gmail.com','akhilesh@electronicsbazaar.com','ajay@electronicsbazaar.com','zeenat.s@electronicsbazaar.com','kushlesh@electronicsbazaar.com','amritpalsingh.d@electronicsbazaar.com','rajkumarvarma@kaykay.co.in'); 
	// $reciepient = array('web.maheshgurav@gmail.com'); 
    try {
        $z_mail = new Zend_Mail('utf-8');

        $z_mail->setBodyHtml($processedTemplate)
            ->setSubject($subject)
            ->addTo($reciepient)
            ->setFrom($senderEmail, $senderName);
		$z_mail->send();
		Mage::log('Email sent for cancelled orders '.$order_numbers,null,"cancel_orders.log",true);
	}catch(Exception $e){
		Mage::log($e->getMessage(),null,"cancel_orders.log",true);
	}
exit;