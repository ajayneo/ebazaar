<?php
require_once Mage::getBaseDir().'/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
class Neo_Suvidha_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function sendRequestEmail($file_array, $variables, $entity_id, $asm_email, $rsm_email){
			$subject = "Shubh Labh Credit Request - ". $variables['name'];
	      	$reciepient = array('finance@electronicsbazaar.com','kushlesh@electronicsbazaar.com');
	      
	      // if($asm_map > 0){
	      //   $asmdetailResource = Mage::getResourceModel("asmdetail/asmdetail_collection");
	      //   $asmdetailResource->addFieldToFilter('name',$asm_map);
	      //   foreach ($asmdetailResource as $asmdetail) {
	      //     $asm_email = $asmdetail->getEmail();
	      //     $rsm_email = $asmdetail->getRsmEmail();
	      //   }
	      // }

	      if(strlen($asm_email) != ''){
	        $reciepient[] = $asm_email;
	      }
	      if(strlen($rsm_email) != ''){
	        $reciepient[] = $rsm_email;
	      }

	      $template_id = "suvidha_welcome_email_template";
	      $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
	      // $variables = array();
	      // $variables['hello'] = 'Admin'; 
	      $template_variables = $variables;
	      $storeId = Mage::app()->getStore()->getId();
	      $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);
	      // echo $processedTemplate; exit;
	      $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
	      $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');

	      //$file = Mage::getBaseDir() . DS .'kaw17842knlpREGdasp9045'. DS .'navision'. DS .'export'. DS .$filename;
	      // $filename = 'new-year.jpg';
	      // $file_array = array('new-year.jpg','new-year.jpg','CCAF-cum-Agreement-Annex1.docx');
	      $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
	      $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
	      try {
	          $z_mail = new Zend_Mail('utf-8');

	          $z_mail->setBodyHtml($processedTemplate)
	              ->setSubject($subject)
	              ->addTo($reciepient)
	              // ->addBcc(array())
	              ->setFrom($senderEmail, $senderName);

	          foreach ($file_array as $key => $filename) {
	            $file = Mage::getBaseDir() . DS .'kaw17842knlpREGdasp9045'. DS .'suvidha'. DS .'creditsuvidha'. DS .$entity_id. DS .$filename;
	            $attachment = file_get_contents($file);
	            $attach = new Zend_Mime_Part($attachment);
	            $attach->disposition =  Zend_Mime::DISPOSITION_ATTACHMENT;;
	            $attach->encoding    = Zend_Mime::ENCODING_BASE64;
	            $attach->filename    = $filename;
	            $z_mail->addAttachment($attach);
	          }

	        //attach default file Retail Evaluation -Annex2.docx
	        $default_file = Mage::getBaseDir() . DS .'kaw17842knlpREGdasp9045'. DS .'RetailEvaluation-Annex.docx';
	        $att2 = new Zend_Mime_Part(file_get_contents($default_file));
            $att2->disposition =  Zend_Mime::DISPOSITION_ATTACHMENT;;
            $att2->encoding    = Zend_Mime::ENCODING_BASE64;
            $att2->filename    = 'RetailEvaluation-Annex.docx';
            $z_mail->addAttachment($att2);

          	$z_mail->send();
          	return true;
	      } catch (Exception $e) {
	      	return 	false;
	      }
	}

	//code for generate pdf
    public function generatePdf($entity_id)
    {		
		// Instantiate and use the dompdf class    	
		$dompdf = new Dompdf();
		
		$suvidhaCust = Mage::getResourceModel('suvidha/creditsuvidha_collection')->addFieldToFilter('id',$entity_id)->getData();
		$suvidhaCust = $suvidhaCust[0];

		$partnership_type = '';
		if($suvidhaCust['partnership_type'] == 0)
		{ 
		  $partnership_type =  'Sole proprietorship';
		}
		elseif($suvidhaCust['partnership_type'] == 1)
		{ 
		  $partnership_type =  'Partnership';
		}
		elseif($suvidhaCust['partnership_type'] == 2)
		{ 
		  $partnership_type =  'Corporation';
		}
		elseif($suvidhaCust['partnership_type'] == 3)
		{ 
		  $partnership_type =  'Other';
		}

		$html = '<div class="suvidha-container" style="padding: 10px;">
    <div style="color: #13447e;text-align: center;"><h2 style="font-size: 17px; text-transform: uppercase; font-family: verdana; margin: 0 0 20px 0;">Customer Credit Application Form (CCAF)</h2></div>    
    <div class="form-container" style="border: 1px solid #e0e0e0; padding: 10px 0; margin: 0 0 10px 0;">    	
    		<div class="default-title">
    			<div style="text-align: center; border-bottom: 1px solid #a1a1a1; position:relative;"><h3 style="font-family: verdana; color: #13447e; font-size: 17px; text-transform: uppercase; margin: 0; padding: 0 0 10px 0;">Business Information</h3> </div>
    		</div>
    		<div class="form-content" style="padding: 10px 10px 0;">
    			<ul class="clearfix" style="margin: 0; padding: 0; ">
	    			<li style="list-style: none; margin: 0 0 5px 0;">
	          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Company Name : </label>	          		
	              <div style="border: 1px solid #e1e1e1; padding: 5px 10px; font-family: verdana; color: #868686; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['company_name'].'	</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
	          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">GST No : </label>
	          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['gst_no'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
	          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Proprietor/Partners/Directors details : </label>
	              <div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['partner_details'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
	      				<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Business Type : </label>
	      		    <div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$partnership_type.'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Billing Address : </label>
          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['billing_add'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">City : </label>
              <div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['billing_city'].'</div>
	      		</li>    						
	      		<li style="list-style: none; margin: 0 0 5px 0;">
          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">State : </label>
          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['billing_state'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Zip Code : </label>
          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['billing_zip_code'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Registered Company Address : </label>
          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['company_add'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">City : </label>
          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['company_city'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">State : </label>
          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['company_state'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Zip Code : </label>
          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['company_zip_code'].'</div>
	      		</li>
      			<li style="list-style: none; margin: 0 0 5px 0;">
          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">E-mail | Fax : </label>
          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['email_id'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Credit Requested : </label>
          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['credit_requested'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Date business commenced : </label>
          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['business_commenced'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Nature of Business : </label>
          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['nature_of_business'].'</div>
	      		</li>
	    		</ul>
    		</div>    	
    </div>

    <div class="form-container" style="border: 1px solid #e0e0e0; padding: 10px 0; margin: 0 0 10px 0;">    	
    		<div class="default-title">
    			<div style="color: blue;text-align: center; border-bottom: 1px solid #a1a1a1; position:relative;"><h3 style="font-family: verdana; color: #13447e; font-size: 17px; text-transform: uppercase;margin: 0; padding: 0 0 10px 0;">Bank Information</h3> </div>
    		</div>
    		<div class="form-content" style="padding: 10px 10px 0;">
    			<ul class="clearfix" style="margin: 0; padding: 0; ">
	    			<li style="list-style: none; margin: 0 0 5px 0;">
	          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Account Name : </label>
	          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['bank_acc_name'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
	          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Account Number : </label>
	          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['bank_acc_no'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
	          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Bank Name : </label>
	          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['bank_name'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
	          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Branch : </label>
	          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['bank_branch'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
	      				<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Type of Account : </label>
                <div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$account_type.'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
          		  <label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Email : </label>
                <div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;"> '.$suvidhaCust['email_id'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
          			<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">IFSC Code : </label>
          			<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['bank_ifsc'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
          			<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Phone : </label>
          			<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['mobile'].'</div>
	      		</li>
	    		</ul>	    		
    		</div>    	
    </div>
	
    <div class="form-container" style="border: 1px solid #e0e0e0; padding: 10px 0; margin: 0 0 10px 0;">    	
    		<div class="default-title">
    			<div style="color: blue;text-align: center; border-bottom: 1px solid #a1a1a1; position:relative;"><h3 style="font-family: verdana; color: #13447e; font-size: 17px; text-transform: uppercase;margin: 0; padding: 0 0 10px 0;">Business / Trade References</h3> </div>
    		</div>
    		<div class="form-content two-columns clearfix" style="padding: 20px 20px 0;">
    			<ul class="clearfix" style="margin: 0; padding: 0; ">
	    		<li style="list-style: none; margin: 0 0 5px 0;">
	          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Company name : </label>
	          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['ref_company1'].'</div>
	      		</li>	      		
	      		<li style="list-style: none; margin: 0 0 5px 0;">
	          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Address : </label>
	          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['ref_address1'].'</div>
	      		</li>  		
	      		<li style="list-style: none; margin: 0 0 5px 0;">
	          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Nature of Business : </label>
	          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['ref_nature_of_business1'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
	          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Phone : </label>
	          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['ref_phone1'].'</div>
	      		</li>	      		
	    		</ul>';
	    		if($suvidhaCust['ref_company2'] != ''):
	 $html .=   '<ul class="clearfix" style="margin: 0 0 0 20px; padding: 0; ">                                	   	          
	      		<li style="list-style: none; margin: 0 0 5px 0;">
	          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Company name : </label>
	          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['ref_company2'].'</div>
	      		</li>	      		
	      		<li style="list-style: none; margin: 0 0 5px 0;">
	          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Address : </label>
	          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['ref_address2'].'</div>
	      		</li>	
	      		<li style="list-style: none; margin: 0 0 5px 0;">
	          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Nature of Business : </label>
	          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['ref_nature_of_business2'].'</div>
	      		</li>
	      		<li style="list-style: none; margin: 0 0 5px 0;">
	          		<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Phone : </label>
	          		<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['ref_phone2'].'</div>
	      		</li>   	      		
	    		</ul>';
	    		endif;	    		
      $html .=  '</div>    	
    </div>

	    <div class="form-container" style="border: 1px solid #e0e0e0; padding: 10px 0; margin: 0 0 10px 0;">    	
	  		<div class="default-title">
	  			<div style="color: blue;text-align: center; border-bottom: 1px solid #a1a1a1; position:relative;"><h3 style="font-family: verdana; color: #13447e; font-size: 17px; text-transform: uppercase;margin: 0; padding: 0 0 10px 0;">Agreement</h3> </div>
	  		</div>
	  		<div class="agreement-container" style="padding: 20px 20px 0;">
	  			<ul style="margin: 0; padding: 0; ">
	  				<li style="font-family:arial; font-size: 12px; color: #868686; list-style-type: decimal; margin: 0 0 10px 20px; padding: 0 20px 0 5px;">All invoices are to be paid 21 days from the date of the delivery</li>
	  				<li style="font-family:arial; font-size: 12px; color: #868686; list-style-type: decimal; margin: 0 0 10px 20px; padding: 0 20px 0 5px;">Claims arising from invoices must be made within seven working days.</li>
	  				<li style="font-family:arial; font-size: 12px; color: #868686; list-style-type: decimal; margin: 0 0 10px 20px; padding: 0 20px 0 5px;">Finance charge of 1.5% per month will be assessed on all balance outstanding past terms</li>
	  				<li style="font-family:arial; font-size: 12px; color: #868686; list-style-type: decimal; margin: 0 0 10px 20px; padding: 0 20px 0 5px;">By submitting this application, you authorize to make inquiries into the banking and business/trade references that you have supplied.</li>
	  				<li style="font-family:arial; font-size: 12px; color: #868686; list-style-type: decimal; margin: 0 0 10px 20px; padding: 0 20px 0 5px;">In case of cheque bounce, all bank charges should be borne by the customer along with penalty fee of INR 500 per cheque.</li>
	  			</ul>
	  			<p style="font-family:arial; font-size: 12px; color: #868686; line-height:22px; margin: 20px 0 0 0;">I HAVE READ AND UNDERSTOOD THE TERMS AND CONDITIONS MENTIONED ABOVE AND HEREBY AGREES TO ABIDE BY THEM. BY COMPLETING AND RETURNING THIS APPLICATION TO ELECTRONICS BAZAAR. THE APPLICANT REPRESENTS THAT ALL OF THE INFORMATION CONTAINED IN THIS APPLICATION IS TRUE AND CORRECT. THE APPLICANT WILL ALSO AGREE TO NOTIFY ELECTRONICS BAZAAR OF ANY CHANGE IN COMPANY OWNERSHIP OR MANAGEMENT</p>
	  		</div>
	  	</div>

	  	<div class="form-container" style="border: 1px solid #e0e0e0; padding: 10px 0; margin: 0 0 10px 0;">    	
	  		<div class="default-title">
	  			<div style="color: blue;text-align: center; border-bottom: 1px solid #a1a1a1; position: relative;"><h3 style="font-family: verdana; color: #13447e; font-size: 17px; text-transform: uppercase;margin: 0; padding: 0 0 10px 0;">Signatures</h3> </div>
	  		</div>
	  		<div class="form-content three-columns clearfix" style="padding: 10px 10px 0;">
	  			<ul class="signatures-container clearfix" style="margin: 0; padding: 0; ">
	  				<li style="list-style: none; margin: 0 0 5px 0;">
	  					<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Name and Title : </label>
	  					<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['sign_name1'].'</div>
	  				</li>
	  				<li style="list-style: none; margin: 0 0 5px 0;">
	  					<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Date : </label>
	  					<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['sign_date1'].'</div>
	  				</li>
	  			</ul>';
                if($suvidhaCust['sign_name2'] != ''): 
	$html .=    '<ul class="signatures-container clearfix" style="">	  					  				
	  							<li style="list-style: none; margin: 0 0 5px 0;">
		  							<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Name and Title</label>
		  							<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['sign_name2'].'</div>
				  				</li>
				  				<li style="list-style: none; margin: 0 0 5px 0;">
				  					<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Date</label>
				  					<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['sign_date2'].'</div>
				  				</li>	  				
				  			</ul>

	  			<ul class="signatures-container clearfix" style="margin: 0 0 0 10px; padding: 0; ">
	  				<li style="list-style: none; margin: 0 0 5px 0;">
	  					<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Name and Title</label>
	  					<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['sign_name3'].'</div>
	  				</li>
	  				<li style="list-style: none; margin: 0 0 5px 0;">
	  					<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Date</label>
	  					<div style="border: 1px solid #e1e1e1; padding: 5px 10px; color: #868686; font-family: verdana; font-size: 12px; margin: 5px 0 0 0;">'.$suvidhaCust['sign_date3'].'</div>
	  				</li>
	  			</ul>';
	  			endif;
	$html .= '</div>
	  	</div>

	  	<div class="form-container no-display" style="border: 1px solid #e0e0e0; padding: 10px 0;">    	
	  		<div class="default-title">
	  			<div style="color: blue;text-align: center; border-bottom: 1px solid #a1a1a1; position: relative;"><h3 style="font-family: verdana; color: #13447e; font-size: 17px; text-transform: uppercase;margin: 0; padding: 0 0 10px 0;">(For Official Use Only)</h3> </div>
	  		</div>
	  		<div class="form-content" style="padding: 10px 10px 0;">
	  			<ul class="clearfix" style="margin: 0; padding: 0; ">
	  				<li style="list-style: none; margin: 0 0 5px 0;">
	  					<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Sales Rep Name Employee Code : </label>
	  				</li>
	  				<li style="list-style: none; margin: 0 0 5px 0;">
	  					<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Credit Limit : </label>
	  				</li>
	  				<li style="list-style: none; margin: 0 0 5px 0;">
	  					<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Customer No : </label>
	  				</li>
	  				<li style="list-style: none; margin: 0 0 5px 0;">
	  					<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Zone/State : </label>
	  				</li>
	  				<li style="list-style: none; margin: 0 0 5px 0;">
	  					<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Approved By : </label>
	  				</li>
	  				<li style="list-style: none;">
	  					<label class="required" style="font-family: verdana; color: #868686; font-size: 12px; margin: 0 0 5px 0; display: block; font-weight: bold;">Date of Approval : </label>
	  				</li>
	  			</ul>
	  		</div>
	  	</div>	  	    
</div>';

		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->render();
		$output = $dompdf->output();
		file_put_contents(Mage::getBaseDir() . DS .'kaw17842knlpREGdasp9045'. DS .'suvidha'. DS .'creditsuvidha'. DS .$entity_id. DS .'CCAForm'.$entity_id.'.pdf', $output);
		return 'CCAForm'.$entity_id.'.pdf';
		
    }	

    //adminhtml approval email to customer
    //Mahesh Gurav
    //09 Mar 2018
    public function sendApproval($template_variables){
    	$reciepient = array($template_variables['email'], $template_variables['arm'], $template_variables['crm']);
    	$subject = "Shubh Labh Credit Approval";
	    $template_id = "suvidha_credit_approve_email_template";
	    $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
	    $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);
	    $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
	    $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
      	try {
          	$z_mail = new Zend_Mail('utf-8');
          	$z_mail->setBodyHtml($processedTemplate)
              	->setSubject($subject)
              	->addTo($reciepient)
              	// ->addBcc($team)
              	->setFrom($senderEmail, $senderName);
			$z_mail->send();
			return true;
      	}catch (Exception $e) {
      		return false;
    	}  
    }

    public function approvalSMS($customer_mobile,$credit){
    		// $error = 1;
    	if($customer_mobile !=='' && $credit !== ''){
	      	$error = 0;
	    	$uname = Mage::getStoreConfig('ebpin_settings/ebpin_login/username');
            $passwd = Mage::getStoreConfig('ebpin_settings/ebpin_login/password');
            $feedid = Mage::getStoreConfig('ebpin_settings/ebpin_login/feedid');
	      	$to=$customer_mobile;
	      	$msg='We are pleased to inform that you have been granted with credit limit of Rs. '.$credit.'. Click on the below link or login to our app to avail the credit. https://www.electronicsbazaar.com/';
	      	$msg=urlencode($msg);
	      	$data='feedid='.$feedid.'&username='.$uname.'&password='.$passwd.'&To='.$to.'&Text='.$msg;
	      	$ch=curl_init('http://bulkpush.mytoday.com/BulkSms/SingleMsgApi');
	      	curl_setopt($ch, CURLOPT_POST, true);
	      	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	      	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	      	$result=curl_exec($ch);
	      	curl_close($ch);
	      	$xml = simplexml_load_string($result);
	      	$json = json_encode($xml);
	      	$arr = json_decode($json,true);
	      	foreach ($arr as $key => $value) {
	        	foreach ($value as $params) {
	          		if($params[ERROR]){
	            		$error = 1;
	          		}
	        	}
	      	}
    	}
    	return $error;
    }

    public function sendRemoval($template_variables){
    	$reciepient = array($template_variables['email'], $template_variables['arm'], $template_variables['crm']);
    	$subject = "Shubh Labh Credit Request Rejected";
	    $template_id = "suvidha_credit_reject_email_template";
	    $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
	    $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);
	    $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
	    $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
      	try {
          	$z_mail = new Zend_Mail('utf-8');
          	$z_mail->setBodyHtml($processedTemplate)
              	->setSubject($subject)
              	->addTo($reciepient)
              	// ->addBcc($team)
              	->setFrom($senderEmail, $senderName);
			$z_mail->send();
			return true;
      	}catch (Exception $e) {
      		return false;
    	}  
    }

    public function removalSMS($customer_mobile){
    		// $error = 1;
    	if($customer_mobile !==''){
	      	$error = 0;
	    	$uname = Mage::getStoreConfig('ebpin_settings/ebpin_login/username');
            $passwd = Mage::getStoreConfig('ebpin_settings/ebpin_login/password');
            $feedid = Mage::getStoreConfig('ebpin_settings/ebpin_login/feedid');
	      	$to= $customer_mobile;
	      	$msg='Unable to approve Credit request on EB, resubmit after 3 months. https://www.electronicsbazaar.com/';
	      	$msg=urlencode($msg);
	      	$data='feedid='.$feedid.'&username='.$uname.'&password='.$passwd.'&To='.$to.'&Text='.$msg;
	      	$ch=curl_init('http://bulkpush.mytoday.com/BulkSms/SingleMsgApi');
	      	curl_setopt($ch, CURLOPT_POST, true);
	      	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	      	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	      	$result=curl_exec($ch);
	      	curl_close($ch);
	      	$xml = simplexml_load_string($result);
	      	$json = json_encode($xml);
	      	$arr = json_decode($json,true);
	      	foreach ($arr as $key => $value) {
	        	foreach ($value as $params) {
	          		if($params[ERROR]){
	            		$error = 1;
	          		}
	        	}
	      	}
    	}
    	return $error;
    }

    public function getArmCrmNameEmail($arm_id)
    {

		$armcrm = Mage::getResourceModel('asmdetail/asmdetail_collection')->addFieldToFilter('name', $arm_id)->getFirstItem()->getData();
		$arm = Mage::getResourceModel('asmdetail/asm_collection')->addFieldToFilter('id', $arm_id)->getFirstItem()->getData();
		$crm = Mage::getResourceModel('asmdetail/rsm_collection')->addFieldToFilter('id', $armcrm['rsmname'])->getFirstItem()->getData();

		$data = array(	
					'arm_name' => $arm['name'], 
					'arm_email' => $armcrm['email'], 
					'crm_name' => $crm['name'], 
					'crm_email' => $armcrm['rsmemail']
				);

		return $data;

    }
}
	 