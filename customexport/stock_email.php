<?php require_once('../app/Mage.php');
umask(0);
Mage::app();
    // Mage::log("stock report start",null,"customexport.log",true);

    $collection = Mage::getModel('catalog/product')
     ->getCollection()
     ->addAttributeToSelect('*')
     ->joinField('a',
         'catalog_product_entity_int',
         'value',
         'entity_id=entity_id',
         'at_a.attribute_id=96',
         'left'
     )
     ->joinField('qty',
         'cataloginventory/stock_item',
         'qty',
         'product_id=entity_id',
         '{{table}}.stock_id=1',
         'left'
     )
     // ->addFieldToFilter('at_a.value', array('eq'=>1))
     // ->addAttributeToFilter('is_in_stock', array('eq' => 1))
     ->addAttributeToFilter('attribute_set_id', array('neq' => 48));
     // ->addAttributeToFilter('qty', array('gt'=>0));
    Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
    
    // $collection->getSelect()->order('name ASC')->having('qty>0');

// echo "string".$collection->getSelect();exit;

$header = array('SKU','PRODUCT NAME','CATEGORY','QTY','PRICE'); 
//Create file on 15 Feb 2018 Mahesh Gurav
$filename = "stock.csv"; //same file name for dialy report
$filename2 = "stock2.csv"; //same file name for dialy report
$filename3 = "stock3.csv"; //same file name for dialy report
$file = Mage::getBaseDir() . DS .'var'.  DS .'export'. DS .$filename;
$file1 = Mage::getBaseDir() . DS .'var'.  DS .'export'. DS .$filename2;
$file2 = Mage::getBaseDir() . DS .'var'.  DS .'export'. DS .$filename3;
$filepath = fopen($file,"w");
$filepath1 = fopen($file1,"w");
$filepath2 = fopen($file2,"w");
//same header as email body
fputcsv($filepath, $header);
fputcsv($filepath1, $header);
fputcsv($filepath2, $header);

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
    
    $product_price = (int) round(Mage::helper('tax')->getPrice($product, $product->getFinalPrice()));
    
    $product_name = trim($product->getName());
    $sku = $product->getSku();
    
    // if($parent_cat1 !== 100 || $parent_cat2 !== 100 || $product_qty !== 0){
    if($category_name2 !== '' && $product_qty !== 0){
        if (strpos($category_name2, 'Open Box') !== false) {
            fputcsv($filepath, array($sku,$product_name,$category_name2,$product_qty,$product_price));
            $openbox_html .= '<tr>';
            $openbox_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$product_name.'</td>';
            $openbox_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$category_name2.'</td>';
            $openbox_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$product_price.'</td>';
            $openbox_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$product_qty.'</td>';
            $openbox_html .= '</tr>'; 
        }else if(strpos($category_name2, 'Brand New') !== false){
            fputcsv($filepath1, array($sku,$product_name,$category_name2,$product_qty,$product_price));
            $new_html .= '<tr>';
            $new_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$product_name.'</td>';
            $new_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$category_name2.'</td>';
            $new_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$product_price.'</td>';
            $new_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$product_qty.'</td>';
            $new_html .= '</tr>';   
        }else if(strpos($category_name2, 'Pre-Owned') !== false || strpos($category_name2, 'Brand Refurbished') !== false){
            fputcsv($filepath2, array($sku,$product_name,$category_name2,$product_qty,$product_price));
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

    fclose($filepath);
    fclose($filepath1);
    fclose($filepath2);
    $vars = array();
    $reciepient = array('akhilesh@electronicsbazaar.com','kushlesh@electronicsbazaar.com','rajkumarvarma@kaykay.co.in','parth@electronicsbazaar.com','umang@electronicsbazaar.com','ketaki@electronicsbazaar.com','taha.b@electronicsbazaar.com','prakash@kaykay.co.in','chandan.p@electronicsbazaar.com','hari@electronicsbazaar.com','vineeta.y@electronicsbazaar.com','arm@electronicsbazaar.com','crm@electronicsbazaar.com','mahesh.s@electronicsbazaar.com','deepali.v@electronicsbazaar.com','daniel.d@electronicsbazaar.com','roy.g@electronicsbazaar.com','vicky.p@electronicsbazaar.com');
    $reciepient = array('web.maheshgurav@gmail.com');
    $storeId = Mage::app()->getStore()->getId();
    $template_id = "navisionexport_hourly_email_template";
    $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
        $senderName = Mage::getStoreConfig('trans_email/ident_sales/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_sales/email');
        try {
            //-------------------------------------------------------------------------------------------------- 
            //prepare file attachment
            $attachment = file_get_contents($file);
            $attach = new Zend_Mime_Part($attachment);
            $attach->type = 'application/csv';
            $attach->disposition = Zend_Mime::DISPOSITION_INLINE;
            $attach->encoding    = Zend_Mime::ENCODING_8BIT;
            $attach->filename    = $filename;

            //1
            //prepare file attachment
            $attachment1 = file_get_contents($file1);
            $attach1 = new Zend_Mime_Part($attachment1);
            $attach1->type = 'application/csv';
            $attach1->disposition = Zend_Mime::DISPOSITION_INLINE;
            $attach1->encoding    = Zend_Mime::ENCODING_8BIT;
            $attach1->filename    = $filename;

            //2
            //prepare file attachment
            $attachment2 = file_get_contents($file2);
            $attach2 = new Zend_Mime_Part($attachment2);
            $attach2->type = 'application/csv';
            $attach2->disposition = Zend_Mime::DISPOSITION_INLINE;
            $attach2->encoding    = Zend_Mime::ENCODING_8BIT;
            $attach2->filename    = $filename; 

            $openbox = array('msg' =>$openbox_html);
            $openobx_subject = "Stock Report for Openbox products ".date('Y-m-d');
            $openboxTemplate = $emailTemplate->getProcessedTemplate($openbox);
            
            $z_mail_openbox = new Zend_Mail('utf-8');
            $z_mail_openbox->setBodyHtml($openboxTemplate)
                ->setSubject($openobx_subject)
                ->addTo('support@electronicsbazaar.com')
                ->addBcc($reciepient)
                ->setFrom($senderEmail, $senderName);
                //attach 
                $z_mail_openbox->addAttachment($attach);

                $z_mail_openbox->send();
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
            $z_mail_new->addAttachment($attach1);    
            $z_mail_new->send();

            //------------------------------------------------------------------------------------------------------
    $refurb = array('msg' =>$refurb_html);
    $refurb_subject = "Stock Report for Pre-Owned / Refurbished products ".date('Y-m-d');
    $refurbTemplate = $emailTemplate->getProcessedTemplate($refurb);
            $z_mail_refurb = new Zend_Mail('utf-8');
            $z_mail_refurb->setReturnPath($senderEmail);
            $z_mail_refurb->setBodyHtml($refurbTemplate)
                ->setSubject($refurb_subject)
                ->addTo('support@electronicsbazaar.com')
                ->addBcc($reciepient)
                ->setFrom($senderEmail, $senderName);
            $z_mail_refurb->addAttachment($attach2);  
            $z_mail_refurb->send();
            //-------------------------------------------------------------------------------------------------------- 

        }catch(Exception $e){
            Mage::log($e->getMessage(),null,"customexport.log",true);
        }
?>
