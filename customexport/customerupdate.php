<?php require_once('../app/Mage.php');
umask(0);
Mage::app();
date_default_timezone_set('Asia/Calcutta');
try{
	$_model = Mage::getModel('customer/customer');
    $collection = $_model->getCollection();

######################## FILTER BY LAST DAY ######################
$date_to = date('Y-m-d' . ' 23:59:59', strtotime("-1 days"));
$date_from = date('Y-m-d' . ' 00:00:00', strtotime("-1 days"));

$gm_from = gmdate('Y-m-d H:i:s',strtotime($date_from));
$gm_to = gmdate('Y-m-d H:i:s',strtotime($date_to));
$collection->addFieldToFilter('website_id', 1);
$collection->addFieldToFilter('store_id', 1);
$collection->addFieldToFilter('group_id', 4);
$collection->addFieldToFilter('updated_at', array('from' => $gm_from, 'to' => $gm_to));

// echo $collection->getSelect();

 // echo "Number of Customers Updated is ".count($collection);
##################################################################
	// echo $collection->getSelect();
	if(count($collection)){
		$objAffiliatescontacts = new Neo_Affiliate_Model_Eav_Entity_Attribute_Source_Affiliatescontacts();
		$filename = "customers_updated_".date('Y_m_d',strtotime("-1 day")).".csv";
	    $file = Mage::getBaseDir() . DS .'var'. DS .'export'. DS .$filename;
	    $filepath = fopen($file,"w");
	    $header = array('ASM','RSM','customer_id','email','store','mobile','pincode','city','state','GST IN','PAN','Regisered Date','Updated on');  
	    fputcsv($filepath, $header);
	    $table_html = '';
     	$table_html = '<table style="border-top:1px solid #ddd; border-left:1px solid #ddd; margin:10px 0 0 0;">';
    	$table_html .= '<tr><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Store Name</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Email</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Mobile</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">City</th></tr>';
		foreach ($collection as $customer) {
			$customer_id = $customer->getEntityId();
			$_customer = $_model->load($customer_id);
			$email = $_customer->getEmail();
			$store = $_customer->getAffiliateStore();
			$mobile = $_customer->getMobile();
			$pincode = $_customer->getPincode();
			$city = $_customer->getCusCity();
			$regionId = $_customer->getCusState();
			$gstin = $_customer->getGstin();
			$pan = $_customer->getTaxvat();
			$asm_map = $_customer->getAsmMap(); 

			$asm_name = $objAffiliatescontacts->getOptionText($asm_map);
			$rsm_name = $objAffiliatescontacts->getRsmbyasm($asm_map);
			$region = Mage::getModel('directory/region')->load($regionId);
			$state = $region->getName();

			$created_at = date('d-m-Y h:i:s', strtotime($_customer->getCreatedAt()));
			$updated_at = date('d-m-Y h:i:s', strtotime($_customer->getUpdatedAt()));

			fputcsv($filepath, array($asm_name,$rsm_name,$customer_id,$email,$store,$mobile,$pincode,$city,$state,$gstin,$pan,$created_at,$updated_at));

			if($store !== ''){
				
		        $table_html .= '<tr>';
		        $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$store.'</td>';
		        $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$email.'</td>';
		        $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$mobile.'</td>';
		        $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$city.'</td>';
		        $table_html .= '</tr>';
			}
		}
		$table_html .= '<table>';
		fclose($filepath);
		$attachment = file_get_contents($file);
		//email 
		$subject = "Customers Updated Report for ".date('d/m/Y',strtotime("-1 days"));
		$template_id = "navisionexport_hourly_email_template";
        $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
        $template_variables = array(
            'filename' => $filename,
            'msg' =>$table_html,
            );
        $storeId = Mage::app()->getStore()->getId();
        $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        $reciepient = array('kushlesh@electronicsbazaar.com');
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

	}
}catch(Exception $e){
	echo $e->getMessage();
}
