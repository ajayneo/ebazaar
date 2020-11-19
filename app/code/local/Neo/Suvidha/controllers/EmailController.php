<?php //test controller

class Neo_Suvidha_EmailController extends Mage_Core_Controller_Front_Action{

    public function IndexAction() {

      // echo "Email";



      $subject = "Shubh Labh Credit Approval";

      // $reciepient = array('web.maheshgurav@gmail.com','sandeepmukherjee0488@gmail.com');

      $reciepient = array('web.maheshgurav@gmail.com');

      $customerEmail = 'web.maheshgurav@gmail.com';

      $websiteId = Mage::app()->getWebsite()->getId();

      // $_customer = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->loadByEmailId('web.maheshgurav@gmail.com');

      $customer = Mage::getModel('customer/customer')

      ->setWebsiteId($websiteId)

      ->loadByEmail($customerEmail);

      $asm_id = $customer->getAsmMap();

      

      // print_r($customer->getData());

      $asm_email = '';

      $rsm_email = '';

      if($asm_id > 0){

        $asmdetailResource = Mage::getResourceModel("asmdetail/asmdetail_collection");

        $asmdetailResource->addFieldToFilter('name',$asm_id);

        foreach ($asmdetailResource as $asmdetail) {

          $asm_email = $asmdetail->getEmail();

          $rsm_email = $asmdetail->getRsmEmail();

        }

      }



      if(strlen($asm_email) > 0){

        $reciepient[] = $asm_email;

      }

      if(strlen($rsm_email) > 0){

        $reciepient[] = $rsm_email;

      }

      // exit;



      $template_id = "suvidha_welcome_email_template";

      $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);

      $variables = array();

      $variables['hello'] = 'Admin'; 

      $template_variables = $variables;

      $storeId = Mage::app()->getStore()->getId();

      $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);

      echo $processedTemplate; exit;

      $senderName = Mage::getStoreConfig('trans_email/ident_support/name');

      $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');



      //$file = Mage::getBaseDir() . DS .'kaw17842knlpREGdasp9045'. DS .'navision'. DS .'export'. DS .$filename;

      $filename = 'new-year.jpg';

      $file_array = array('new-year.jpg','new-year.jpg','CCAF-cum-Agreement-Annex1.docx');

      $senderName = Mage::getStoreConfig('trans_email/ident_support/name');

      $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');

      try {

          $z_mail = new Zend_Mail('utf-8');



          $z_mail->setBodyHtml($processedTemplate)

              ->setSubject($subject)

              ->addTo($reciepient)

              ->setFrom($senderEmail, $senderName);



          foreach ($file_array as $key => $filename) {

            $file = Mage::getBaseDir() . DS .'kaw17842knlpREGdasp9045'. DS .$filename;

            $attachment = file_get_contents($file);

            $attach = new Zend_Mime_Part($attachment);

            $attach->disposition =  Zend_Mime::DISPOSITION_ATTACHMENT;;

            $attach->encoding    = Zend_Mime::ENCODING_BASE64;

            $attach->filename    = $filename;

            $z_mail->addAttachment($attach);

          }





          $z_mail->send();

      } catch (Exception $e) {

      }

    }



  public function creditapproveAction(){

      $subject = "Shubh Labh Credit Approval";

      $reciepient = array('web.maheshgurav@gmail.com');

      $customerEmail = 'web.maheshgurav@gmail.com';

      $websiteId = Mage::app()->getWebsite()->getId();

      $customer = Mage::getModel('customer/customer')

      ->setWebsiteId($websiteId)

      ->loadByEmail($customerEmail);

      $asm_id = $customer->getAsmMap();

      $asm_email = '';

      $rsm_email = '';

      if($asm_id > 0){

        $asmdetailResource = Mage::getResourceModel("asmdetail/asmdetail_collection");

        $asmdetailResource->addFieldToFilter('name',$asm_id);

        foreach ($asmdetailResource as $asmdetail) {

          $asm_email = $asmdetail->getEmail();

          $rsm_email = $asmdetail->getRsmEmail();

        }

      }



      if(strlen($asm_email) > 0){

        $reciepient[] = $asm_email;

      }

      if(strlen($rsm_email) > 0){

        $reciepient[] = $rsm_email;

      }

      $template_id = "suvidha_credit_approve_email_template";

      $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);

      $variables = array();

      $variables['hello'] = 'Admin'; 

      $template_variables = $variables;

      $storeId = Mage::app()->getStore()->getId();

      $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);

      echo $processedTemplate; exit;

      $senderName = Mage::getStoreConfig('trans_email/ident_support/name');

      $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');

      $filename = 'new-year.jpg';

      $file_array = array('new-year.jpg','new-year.jpg','CCAF-cum-Agreement-Annex1.docx');

      $senderName = Mage::getStoreConfig('trans_email/ident_support/name');

      $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');

      try {

          $z_mail = new Zend_Mail('utf-8');



          $z_mail->setBodyHtml($processedTemplate)

              ->setSubject($subject)

              ->addTo($reciepient)

              ->setFrom($senderEmail, $senderName);



          foreach ($file_array as $key => $filename) {

            $file = Mage::getBaseDir() . DS .'kaw17842knlpREGdasp9045'. DS .$filename;

            $attachment = file_get_contents($file);

            $attach = new Zend_Mime_Part($attachment);

            $attach->disposition =  Zend_Mime::DISPOSITION_ATTACHMENT;;

            $attach->encoding    = Zend_Mime::ENCODING_BASE64;

            $attach->filename    = $filename;

            $z_mail->addAttachment($attach);

          }





          $z_mail->send();

      }catch (Exception $e) {

        }  

  }

  public function sendsmsAction(){
      $uname = Mage::getStoreConfig('ebpin_settings/ebpin_login/username');
      $passwd = Mage::getStoreConfig('ebpin_settings/ebpin_login/password');
      $feedid = Mage::getStoreConfig('ebpin_settings/ebpin_login/feedid');
      $customer_mobile = 814910130911;
      $credit = 2;
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
      $error = 0;
      foreach ($arr as $key => $value) {
        foreach ($value as $params) {
          if($params[ERROR]){
            $error = 1;
          }
        }
      }

      return $error;
  }

  public function emailtriggerAction()
  {
    // $array = array(75,71,65,64,63,54,53);  add entity id's over here to manually trigger emails
    foreach($array as $entity_id) {
      $modelS = Mage::getModel('suvidha/creditsuvidha')->load($entity_id);

      $variables = array();    
      $variables['name'] = $modelS['company_name'];
      $variables['mobile'] = $modelS['mobile'];
      $variables['email'] = $modelS['email_id'];
      $asm_email = $modelS['arm_email'];
      $rsm_email = $modelS['crm_email'];

      $files_array = array();
      $files_array[] =  substr(strrchr($modelS['aadhar'], "/"), 1);
      $files_array[] =  substr(strrchr($modelS['pancard'], "/"), 1);
      $files_array[] =  substr(strrchr($modelS['postcheque'], "/"), 1);
      $files_array[] =  substr(strrchr($modelS['bankst'], "/"), 1);
      $files_array[] = Mage::helper('suvidha')->generatePdf($entity_id);

      $this->sendRequestEmail1($files_array, $variables, $entity_id, $asm_email, $rsm_email);
      echo "done";
    }
  }

  public function sendRequestEmail1($file_array, $variables, $entity_id, $asm_email, $rsm_email){
        $subject = "Shubh Labh Credit Request - ". $variables['name'];
        $reciepient = array('finance@electronicsbazaar.com','kushlesh@electronicsbazaar.com');

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

        try {
            $z_mail = new Zend_Mail('utf-8');

            $z_mail->setBodyHtml($processedTemplate)
                ->setSubject($subject)
                ->addTo($reciepient)
                // ->addBcc(array('sujay.k@electronicsbazaar.com','snehasvb@gmail.com'))
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
          return  false;
        }
  }

}    

?>