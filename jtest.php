<?php 
// $to = "jitendra.patel@neosofttech.com";
// $subject = "Regarding List";
// $txt = "Hello world!";
// $headers = "From: jitendrapatel2286@gmail.com" . "\r\n" .
// "CC: somebodyelse@example.com";

// $success = mail($to,$subject,$txt,$headers);
// if (!$success) {
//     $errorMessage = error_get_last()['message'];
//     echo '<pre>', print_r($errorMessage);
// }
// else
// {
// 	echo "Test".$success;
// }


// echo "TTT New file test"; exit;
require_once('app/Mage.php'); //Path to Magento
umask(0);
Mage::app();


// Transactional Email Template's ID
    $templateId = 18;
    // $templateId = "navisionexport_hourly_email_template"
 
    // Set sender information            
    $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
    $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');        
    $sender = array('name' => $senderName,
                'email' => $senderEmail);
    
    // Set recepient information
    $recepientEmail = 'web.maheshgurav@gmail.com';
    $recepientName = 'Mahesh Gurav';        
    
    // Get Store ID        
    $storeId = Mage::app()->getStore()->getId();
 
    // Set variables that can be used in email template
    $vars = array('customerName' => 'web.maheshgurav@gmail.com',
              'customerEmail' => 'Mr. MG');
            
    $translate  = Mage::getSingleton('core/translate');
 try{

    // Send Transactional Email
    Mage::getModel('core/email_template')
        ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
            
    $translate->setTranslateInline(true);  
}catch(Exception $e){
	echo $e->getMessage();
}


exit('server auth');
$reciepient = array('web.maheshgurav@gmail.com');
$storeId = Mage::app()->getStore()->getId();
$template_id = "navisionexport_hourly_email_template";
$emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
$senderName = Mage::getStoreConfig('trans_email/ident_sales/name');
$senderEmail = Mage::getStoreConfig('trans_email/ident_sales/email');


$openbox_html = 'Test hello';    
$openbox = array('msg' =>$openbox_html);
$openobx_subject = "Stock Report for Openbox products ".date('Y-m-d');
$openboxTemplate = $emailTemplate->getProcessedTemplate($openbox);

$z_mail_openbox = new Zend_Mail('utf-8');
$z_mail_openbox->setBodyHtml($openboxTemplate)
    ->setSubject($openobx_subject)
    ->addTo('jitendra.patel@neosofttech.com')
    ->addBcc($reciepient)
    ->setFrom($senderEmail, $senderName);               

    $z_mail_openbox->send();

    exit('check send zen');

// $_order = Mage::getModel('sales/order')->load(121051);
// $_order->setPaymentMethod('banktransfer');
// $_order->save();

// COD cashondelivery
// Credit Days Payment banktransfer
// Credit/Debit Card or Netbanking billdesk_ccdc_net
// Advanced Payment custompayment

//save tracking number
// $shipmentId = '300082390';
// $trackmodel = Mage::getModel('sales/order_shipment_api')->addTrack($shipmentId,'dhl','DHL','3674504');
// $trackmodel->save();

//save payment method
$orderId = '200111789'; // Incremented Order Id
$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
$order->setData('state','new');
$order->setStatus('pending'); 
// $payment = $order->getPayment();
// $payment->setMethod('cashondelivery'); // Assuming 'test' is updated payment method
// $payment->save();
//$order->save();


exit();
//get product list.
$product_list = array();

//add product sku.
$product_list = array('NEWNBK00176','NEWNBK00175','NEWNBK00174');

//print_r($product_list);

foreach ($product_list as $pro_sku) {        	

$_catalog = Mage::getModel('catalog/product');
$_productId = $_catalog->getIdBySku($pro_sku);
$_product = Mage::getModel('catalog/product')->load($_productId);
//echo $_product->getProductUrl(true);exit;
//echo '<pre>', print_r($_product);exit;

echo'<tr>
	<td style="font-family: arial; font-size: 14px; color: #424242; border-bottom: 1px solid #424242; 
	border-right: 1px solid #424242; padding: 10px 20px; line-height: 20px;">
	<a href="'.$_product->getProductUrl(true).'?utm_source=Emailer&amp;utm_medium=email&amp;utm_campaign=17JAN18&amp;utm_content=Brand-New-Apple-ipad" 
	target="_blank" style="text-decoration: none; color: #424242">
	<span style="color: #424242;">'.$_product->getName().'</span>
	</a>
	</td>
	<td width="25%" align="center" style="font-family: arial; font-size: 14px; color: #424242; border-bottom: 1px solid #424242; border-right: 1px solid #424242; padding: 10px 20px; line-height: 20px;">
	 <a href="'.$_product->getProductUrl(true).'?utm_source=Emailer&amp;utm_medium=email&amp;utm_campaign=17JAN18&amp;utm_content=Brand-New-Apple-ipad"
	 target="_blank" style="text-decoration: none; color: #424242"><span style="color: #424242;">'.$_product->getPrice().'/-</span></a>
	</td>
	</tr>';

echo '<br />';

}

?>
