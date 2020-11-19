<?php
require_once('../app/Mage.php');
umask(0);
Mage::app();
// date_default_timezone_set('Asia/Calcutta');


	//month wise asm sales data
	function getMonthSales($n = 1){

		//prepare asm labels
		$asmmodel = Mage::getModel('neoaffiliate/eav_entity_attribute_source_affiliatescontacts');
		$asm_labels = array();
		if($asmmodel){
			$asm_list = $asmmodel->getAllOptions();
			foreach ($asm_list as $key => $asm) {
				$asm_labels[$asm['value']] = $asm['label'];
			}
		}

		//customer asm list
		$model = Mage::getModel('customer/customer');
		$collection = $model->getCollection()
					->addAttributeToSelect('asm_map');
		$asm_customers = array();
		foreach ($collection as $customer) {
			$asm_customers[$customer->getAsmMap()][]=$customer->getEmail();
		}

		//last 1 month dates
		// $n = 1;
		$past_month_1 = '-'.$n.' month';
		$first_day_1 = gmdate('Y-m-d H:i:s', strtotime($past_month_1, strtotime(date('Y-m-01 00:00:00'))));
		$last_day_1 = date('t',strtotime($first_day_1));
		$last_date_1 = 'Y-m-'.$last_day_1.' 23:59:59';
		$last_day_1 = gmdate('Y-m-d H:i:s', strtotime($past_month_1, strtotime(date($last_date_1))));
		$previous_month_1 = gmdate('M Y',strtotime($past_month_1, strtotime(date('Y-m-01 00:00:00'))));

		$previous_month_1;

		$asm_monthly_sales = array();
		foreach ($asm_customers as $asm_map => $customers) {
			// echo 'customer email and asm<br/>';
			$orders = Mage::getModel('sales/order')->getCollection();
			$orders->addFieldToFilter('created_at',array('gteq'=>$first_day_1));
			$orders->addFieldToFilter('created_at',array('lteq'=>$last_day_1));
			$orders->addFieldToFilter('state',array('neq'=>'canceled'));
			$orders->addFieldToFilter('customer_email',array('in'=>$customers));
			$orders->addAttributeToSelect('base_grand_total');
			$orders->getSelect()->columns('SUM(base_grand_total) as total');
			$ordersData = $orders->getData();
			$total = $ordersData[0]['total'];
			//get ASM names from ASM MAP from model neoaffiliate
			$asm_name = '';
			if($asm_map){
				if($asm_labels[$asm_map]){
					$asm_name = $asm_labels[$asm_map];
				}else{
					$asm_name = 'Old ASM#'.$asm_map;
				}
			}else{
				$asm_name = 'Non ASM';
			}
			$asm_monthly_sales[$asm_name] = (int) $total;
		}

		return array($previous_month_1,$asm_monthly_sales);	
	}
// print_r($asm_labels);

	$sales1 = getMonthSales(1);
	$sales2 = getMonthSales(2);
	$sales3 = getMonthSales(3);
	$sales4 = getMonthSales(4);
	$sales5 = getMonthSales(5);
	$sales6 = getMonthSales(6);
	
	$table_html = '';
    $table_html = '<table style="border-top:1px solid #ddd; border-left:1px solid #ddd; margin:10px 0 0 0;">';
    $table_html .= '<tr><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">ASM Name</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Sales '.$sales6[0].'</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Sales '.$sales5[0].'</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Sales '.$sales4[0].'</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Sales '.$sales3[0].'</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Sales '.$sales2[0].'</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Sales '.$sales1[0].'</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Total</th></tr>';
    
    $asm_names = array_keys($sales1[1]);
	asort($asm_names);
	
    foreach ($asm_names as $key => $value) {
    	$order_total = $sales6[1][$value] + $sales5[1][$value] + $sales4[1][$value] + $sales3[1][$value] + $sales2[1][$value] + $sales1[1][$value];
		$table_html .= '<tr>';
		    $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$value.'</td>';
		    $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$sales6[1][$value].'</td>';
		    $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$sales5[1][$value].'</td>';
		    $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$sales4[1][$value].'</td>';
		    $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$sales3[1][$value].'</td>';
		    $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$sales2[1][$value].'</td>';
		    $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$sales1[1][$value].'</td>';
		    $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$order_total.'</td>';
    	$table_html .= '</tr>';
	}
    $table_html .= '<table>';


    	$template_id = "navisionexport_hourly_email_template";
        $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
        $template_variables = array('msg' =>$table_html);
        $storeId = Mage::app()->getStore()->getId();
        $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);
        // print_r($processedTemplate); exit;
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        $subject = "ASM Sales Report for past 6 Months";
        $attachment = file_get_contents($file);
        // $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        // $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        $senderName = "Sales - Electronicsbazaar";
        $senderEmail = "sales@electronicsbazaar.com";
        $reciepient = array('web.maheshgurav@gmail.com'); 
        try {
            $z_mail = new Zend_Mail('utf-8');

            $z_mail->setBodyHtml($processedTemplate)
                ->setSubject($subject)
                ->addTo($reciepient)
                ->setFrom($senderEmail, $senderName);

   //          $attach = new Zend_Mime_Part($attachment);
   //          $attach->type = 'application/csv';
   //          $attach->disposition = Zend_Mime::DISPOSITION_INLINE;
   //          $attach->encoding    = Zend_Mime::ENCODING_8BIT;
   //          $attach->filename    = $filename;
			// $z_mail->addAttachment($attach);
            
            $z_mail->send();
        }catch(Exception $e){
            Mage::log("Bestsellers ".$e->getMessage(),null,"customexport.log",true);
        }
	