<?php //test controller
class Neo_Suvidha_EmailController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      // echo "Email";

      $subject = "Shubh Labh Credit Request";
      // $reciepient = array('web.maheshgurav@gmail.com','sandeepmukherjee0488@gmail.com');
      $reciepient = array('web.maheshgurav@gmail.com');
      $template_id = "suvidha_welcome_email_template";
      $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
      $variables = array();
      $variables['hello'] = 'Admin'; 
      $template_variables = $variables;
      $storeId = Mage::app()->getStore()->getId();
      $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);
      // echo $processedTemplate; exit;
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

}    
?>