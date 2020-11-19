<?php require_once('../app/Mage.php');
umask(0);
Mage::app();
    Mage::log("stock report start",null,"customexport.log",true);

$collection = Mage::getModel('catalog/product')
     ->getCollection()
     ->addAttributeToSelect('*')
     ->joinField('qty',
         'cataloginventory/stock_item',
         'qty',
         'product_id=entity_id',
         '{{table}}.stock_id=1',
         'left'
     )
     ->addAttributeToFilter('is_in_stock', array('eq' => 1))
     ->addAttributeToFilter('qty', array('gt'=>0));
Mage::getSingleton('cataloginventory/stock')
    ->addInStockFilterToCollection($collection);
$collection->getSelect()->order('name ASC')->having('qty>0');



$header = array('PRODUCT NAME','CATEGORY','QTY','PRICE');    
$parent_cat2 = 0;
$parent_cat1 = 0;
$product_qty = 0;
$_cat = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId());
$openbox_html ='';
$openbox_html = '<table style="border-top:1px solid #ddd; border-left:1px solid #ddd; margin:10px 0 0 0;">';
$openbox_html .= '<tr><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Product Name</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Category Name</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Total Price(incl tax)</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Qty</th></tr>';

$new_html ='';
$new_html = '<table style="border-top:1px solid #ddd; border-left:1px solid #ddd; margin:10px 0 0 0;">';
$new_html .= '<tr><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Product Name</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Category Name</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Total Price(incl tax)</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Qty</th></tr>';

$refurb_html ='';
$refurb_html = '<table style="border-top:1px solid #ddd; border-left:1px solid #ddd; margin:10px 0 0 0;">';
$refurb_html .= '<tr><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Product Name</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Category Name</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Total Price(incl tax)</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Qty</th></tr>';


foreach ($collection as $product) {
    $categories = $product->getCategoryIds();
    $category_name2 = '';
    if(isset($categories[1])){
        $parent_cat2 = $categories[1];
        $cat2 = $_cat->load($parent_cat2);
        $category_name2 = $cat2->getName();
    }
    $product_qty = (int) $product->getQty();
    // $product_price = (int) $product->getPrice();
    $product_price = (int) round(Mage::helper('tax')->getPrice($product, $product->getFinalPrice()));
    // $product_price = (int) round(Mage::helper('tax')->getPrice($product, $product->getPrice()));
    // echo Mage::helper('tax')->getPrice($product, $product->getFinalPrice());
    // exit;
    $product_name = $product->getName();
    $sku = $product->getSku();
    
    // if($parent_cat1 !== 100 || $parent_cat2 !== 100 || $product_qty !== 0){
    if($category_name2 !== '' && $product_qty !== 0){
        
        if (strpos($category_name2, 'Open Box') !== false) {
            $openbox_html .= '<tr>';
            $openbox_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$product_name.'</td>';
            $openbox_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$category_name2.'</td>';
            $openbox_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$product_price.'</td>';
            $openbox_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$product_qty.'</td>';
            $openbox_html .= '</tr>'; 
        }else if(strpos($category_name2, 'Brand New') !== false){
            $new_html .= '<tr>';
            $new_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$product_name.'</td>';
            $new_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$category_name2.'</td>';
            $new_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$product_price.'</td>';
            $new_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$product_qty.'</td>';
            $new_html .= '</tr>';   
        }else if(strpos($category_name2, 'Pre-Owned') !== false || strpos($category_name2, 'Brand Refurbished') !== false){
            $refurb_html .= '<tr>';
            $refurb_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$product_name.'</td>';
            $refurb_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$category_name2.'</td>';
            $refurb_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$product_price.'</td>';
            $refurb_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$product_qty.'</td>';
            $refurb_html .= '</tr>';   
        }else{
        }
         
    }
    
 }
 $openbox_html .= '</table>';
 $new_html .= '</table>';
 $refurb_html .= '</table>';

Mage::log("stock report collection ready",null,"customexport.log",true);
    
    $vars = array();
    //$reciepient = array('akhilesh@electronicsbazaar.com','kushlesh@electronicsbazaar.com','rajkumarvarma@kaykay.co.in','amritpalsingh.d@electronicsbazaar.com','web.maheshgurav@gmail.com','rsm@electronicsbazaar.com','asm@electronicsbazaar.com','parth@electronicsbazaar.com','syed.a@electronicsbazaar.com','dinesh.s@electronicsbazaar.com','ajay@electronicsbazaar.com','tushar@electronicsbazaar.com','umang@electronicsbazaar.com');
     $reciepient = array('web.maheshgurav@gmail.com');
    
    $storeId = Mage::app()->getStore()->getId();
    $template_id = "navisionexport_hourly_email_template";
    $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
    
    

    

        try {
            //-------------------------------------------------------------------------------------------------- 
            
    $openbox = array('msg' =>$openbox_html);
    $openobx_subject = "Stock Report for Openbox products ".date('Y-m-d');
    $openboxTemplate = $emailTemplate->getProcessedTemplate($openbox);
            $z_mail_openbox = new Zend_Mail('utf-8');
            $z_mail_openbox->setBodyHtml($openboxTemplate)
                ->setSubject($openobx_subject)
                ->addTo('support@electronicsbazaar.com')
                ->addBcc($reciepient)
                ->setFrom($senderEmail, $senderName);
            $z_mail_openbox->send();
            Mage::log("openbox email sent",null,"customexport.log",true);
            //--------------------------------------------------------------------------------------------------------
    $new = array('msg' =>$new_html);
    $new_subject = "Stock Report for Brand New products ".date('Y-m-d');
    $newTemplate = $emailTemplate->getProcessedTemplate($new);
            $z_mail_new = new Zend_Mail('utf-8');
            $z_mail_new->setBodyHtml($newTemplate)
                ->setSubject($new_subject)
                ->addTo('support@electronicsbazaar.com')
                ->addBcc($reciepient)
                ->setFrom($senderEmail, $senderName);
            $z_mail_new->send();
            Mage::log("new email sent",null,"customexport.log",true);

            //------------------------------------------------------------------------------------------------------

    $refurb = array('msg' =>$refurb_html);
    $refurb_subject = "Stock Report for Pre-Owned / Refurbished products ".date('Y-m-d');
    $refurbTemplate = $emailTemplate->getProcessedTemplate($refurb);
            $z_mail_refurb = new Zend_Mail('utf-8');
            $z_mail_refurb->setBodyHtml($refurbTemplate)
                ->setSubject($refurb_subject)
                ->addTo('support@electronicsbazaar.com')
                ->addBcc($reciepient)
                ->setFrom($senderEmail, $senderName);
            $z_mail_refurb->send();
            Mage::log("refurb email sent",null,"customexport.log",true);
            //-------------------------------------------------------------------------------------------------------- 

        }catch(Exception $e){
            Mage::log($e->getMessage(),null,"customexport.log",true);
        }
    Mage::log("stock report end",null,"customexport.log",true);
?>