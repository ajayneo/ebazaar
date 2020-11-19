<?php
class Neo_Ordersummary_Model_Cron{	
	
	//ASM or ARM collection
	public function asmCollection(){
		$asm_names = Mage::getResourceModel('asmdetail/asm_collection');
		$asm_names->addFieldToSelect('name');
		$select= $asm_names->getSelect();
		$select->joinLeft(
		    array('asmdetail'=>'neo_asmdetail'),
		    'main_table.id=asmdetail.name',
		    array('email')
		);

		return $asm_names;
	}

	//CRM or RSM collection
	public function rsmCollection(){
		$rsm_names = Mage::getResourceModel('asmdetail/rsm_collection');
		$rsm_names->addFieldToSelect('name');
		$select= $rsm_names->getSelect();
		$select->joinLeft(
		    array('asmdetail'=>'neo_asmdetail'),
		    'main_table.id=asmdetail.rsmname',
		    array('rsmemail')
		);

		return $rsm_names;
	}

	public function allordersCollection(){
		$filename = 'orderssummary_'.date('Ymd').'.csv';
		$file_path = Mage::getBaseDir() . DS .'var/export'. DS .$filename; 
		$row = 1;
	    $crm_array = array();
	    $arm_array = array();
		if(($handle = fopen($file_path, "r")) !== FALSE) {
	    	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		        $num = count($data);
		        if($row !== 1){
			        if(!empty($data[12])){
			        	$CRM = $data[12];
			        }else{
			        	$CRM = 'Info';
			        }
			        if(!empty($data[11])){
			        	$ARM = $data[11]; 
			        }else{
			        	$ARM = 'Info';
			        }
			        $crm_array[$CRM][] = $data; 
			        $arm_array[$ARM][] = $data;
				}
		        $row++;
	    	}
	    	fclose($handle);
		}

		return array('crmorders'=>$crm_array, 'armorders'=>$arm_array);
	}

	//cron function to send ASM order summary every two hours 	
	public function Armorders(){
		$orders = $this->allordersCollection();
		$asm_orders = $orders['armorders'];
		$asm_detail = array();
		$asm_name = '';
		$strin_sr = '';
		$cat_array = array('openbox','new','preowned','accessories','refurbishedlaptops','asismobiles','featurephones','vow','tablets','refurbishedmobiles');
		foreach ($asm_orders as $key => $rows){
			$strin_sr = '%'.$key.'%';
			$asm_names = $this->asmCollection();
			$asm_names->addFieldToFilter('main_table.name', array('like'=>$strin_sr));
			$asm_detail = $asm_names->getFirstItem()->getData();

			if(!empty($asm_detail['name']) && !empty($asm_detail['email'])){
				$asm_name = str_replace(" ", "", $key);
				$filename = 'orderssummary_'.date('Ymd_').$asm_name.'.csv';
				$file_path = Mage::getBaseDir() . DS .'var/export'. DS .$filename; 
				$fp = fopen($file_path, 'w');

				$ik = 0;
				$temp = array();
				$variables = array();
				foreach ($rows as $key => $data) {
					if($ik == 0){
						fputcsv($fp, array('Order Id','SKU','Product Name','Store Name','Amount Total','Qty','Discount','Net Sale','Category','City','State','ASM Name','RSM Name','Payment Method','Customer Email'));
					}else{
						fputcsv($fp,$data);
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
			        	// $variables[strtolower($category_name).'_orders'] += count(array_unique($orders[strtolower($category_name).'_order']));
			        	if(strrpos($data[2], 'VOW') >= 0 && $category_name == 'featurephones'){
			        		$temp['vow_order'][] = $data[0];
				        	$variables['vow_net_sale'] += $data[7];
				        	$variables['vow_discount'] += $data[6];
				        	$variables['vow_qty'] += $data[5];
				        	$variables['vow_price'] += $data[4];
			        	}
					}
			 		$ik++;
				} //orders rows foreach
				fclose($fp);
				foreach ($cat_array as $cat) {
					$variables[$cat.'_orders'] += count(array_unique($temp[$cat.'_order']));
				}
				$variables['partners'] = count(array_unique($temp['total_customer']));
				$variables['total_orders'] = count(array_unique($temp['total_order']));
				$send = $this->sendEmail($variables,$asm_detail,$file_path,$filename);
				if($send){
					Mage::log('Email sent to '.$asm_detail['name'], null, 'cron.log',true);
				}else{
					Mage::log('Email failed to '.$asm_detail['name'], null, 'cron.log',true);
				}
			}//if asm_detail found
		}//foreach asm orders
	} //ASM cron function ends

	//cron function to send RSM order summary every two hours 	
	public function Crmorders(){
		$orders = $this->allordersCollection();
		$rsm_orders = $orders['crmorders'];
		$rsm_detail = array();
		$rsm_name = '';
		$strin_sr = '';
		$cat_array = array('openbox','new','preowned','accessories','refurbishedlaptops','asismobiles','featurephones','vow','tablets','refurbishedmobiles');
		foreach ($rsm_orders as $key => $rows){
			$strin_sr = '%'.$key.'%';
			$rsm_names = $this->rsmCollection();
			$rsm_names->addFieldToFilter('main_table.name', array('like'=>$strin_sr));
			$rsm_detail = $rsm_names->getFirstItem()->getData();
			if(!empty($rsm_detail['name']) && !empty($rsm_detail['rsmemail'])){
				$rsm_name = str_replace(" ", "", $key);
				$filename = 'orderssummary_'.date('Ymd_').$rsm_name.'.csv';
				$file_path = Mage::getBaseDir() . DS .'var/export'. DS .$filename; 
				$fp = fopen($file_path, 'w');
				$temp = array();
				$variables = array();
				$ik = 0;
				$category_name = '';
				foreach ($rows as $key => $data) {
					if($ik == 0){
						fputcsv($fp, array('Order Id','SKU','Product Name','Store Name','Amount Total','Qty','Discount','Net Sale','Category','City','State','RSM Name','RSM Name','Payment Method','Customer Email'));
					}else{
						fputcsv($fp,$data);
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
			        	// $variables[strtolower($category_name).'_orders'] += count(array_unique($orders[strtolower($category_name).'_order']));
			        	if(strrpos($data[2], 'VOW') >= 0 && $category_name == 'featurephones'){
			        		$temp['vow_order'][] = $data[0];
				        	$variables['vow_net_sale'] += $data[7];
				        	$variables['vow_discount'] += $data[6];
				        	$variables['vow_qty'] += $data[5];
				        	$variables['vow_price'] += $data[4];
			        	}
					}
			 		$ik++;
				} //orders rows foreach
				fclose($fp);
				foreach ($cat_array as $cat) {
					$variables[$cat.'_orders'] += count(array_unique($temp[$cat.'_order']));
				}
				$variables['partners'] = count(array_unique($temp['total_customer']));
				$variables['total_orders'] = count(array_unique($temp['total_order']));
				$rsm_detail['email'] = $rsm_detail['rsmemail'];
				$send = $this->sendEmail($variables,$rsm_detail,$file_path,$filename);
				if($send){
					Mage::log('Email sent to '.$rsm_detail['name'], null, 'cron.log',true);
				}else{
					Mage::log('Email failed to '.$rsm_detail['name'], null, 'cron.log',true);
				}
			}//if rsm_detail found
		}//foreach asm orders
	} //RSM cron function ends

	public function sendEmail($variables,$asm_detail,$file_path,$filename){
		date_default_timezone_set('Asia/Calcutta');
		$template_id = "ordersummary_v3_template";
		$senderName = Mage::getStoreConfig('trans_email/ident_support/name');
		$senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
		//files and email variables sent to mail
		$subject = $asm_detail['name']." - "."Orders summary for orders placed till ".date('Y-m-d ha');
		$to_email = array($asm_detail['email']);
		// $to_email = array('sujay.k@electronicsbazaar.com');
		$bcc_email = array('sujay.k@electronicsbazaar.com','web.maheshgurav@gmail.com');
		$emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
		$processedTemplate = $emailTemplate->getProcessedTemplate($variables);
		// echo $asm_detail['name']." ".$asm_detail['email']."<br/>";
		// echo "process".$processedTemplate;
		$attachment = file_get_contents($file_path);
		$z_mail = new Zend_Mail('utf-8');
		$z_mail->setBodyHtml($processedTemplate)
        ->setSubject($subject)
        ->addTo($to_email)
        // ->addBcc($bcc_email)
        ->setFrom($senderEmail, $senderName);
		$attach = new Zend_Mime_Part($attachment);

	    $attach->type = 'application/csv';
	    $attach->disposition = Zend_Mime::DISPOSITION_INLINE;
	    $attach->encoding    = Zend_Mime::ENCODING_8BIT;
	    $attach->filename    = $filename;
		$z_mail->addAttachment($attach);

		try{
	    	$z_mail->send();
	    	return true;
		}catch(Exception $e){
			Mage::log($e->getMessage(),null,'cron.log',true);
			return false;
		}
	}

	//export orders 
	// public function Exportorders(){
	// 	Mage::log('export orders runs',null,'crontab.log',true);
	// } 
}