<?php

require_once('../app/Mage.php');

umask(0);

Mage::app();

date_default_timezone_set('Asia/Calcutta');

Mage::log("Bestsellers start",null,"customexport.log",true);

    $from = date('Y-m-d 00:00:00',strtotime("-1 day"));

    $to = date('Y-m-d 23:59:59',strtotime("-1 day"));

    $gmfrom = gmdate('Y-m-d H:i:s', strtotime($from));

    $gmto = gmdate('Y-m-d H:i:s', strtotime($to));

    // number of products to display

    $productCount = 25; 

    

    // store ID

    $storeId    = Mage::app()->getStore()->getId();



    $products = Mage::getResourceModel('reports/product_collection')

                     ->addAttributeToSelect('sku')

                    ->addOrderedQty($gmfrom, $gmto)

                    ->setStoreId($storeId)

                    ->addStoreFilter($storeId)                    

                    ->setOrder('order_items_total','desc')

                    // ->setOrder('ordered_qty', 'desc')

                    ->setPageSize($productCount); 

    

    Mage::getSingleton('catalog/product_status')

            ->addVisibleFilterToCollection($products);

    Mage::getSingleton('catalog/product_visibility')

            ->addVisibleInCatalogFilterToCollection($products);

    $products->getSelect()->order('order_items_total DESC');

    //$query .=" ORDER BY order_items.order_items_total";



    // $products->printLogQuery(true);        

    $ordered_items = $products->getData();

    // echo "<pre>"; print_r($ordered_items); echo "</pre>"; exit;

    $print_array = array();

    $table_html = '';

    $table_html = '<table style="border-top:1px solid #ddd; border-left:1px solid #ddd; margin:10px 0 0 0;">';

    $table_html .= '<tr><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Product Name</th><th style="font-weight:bold; border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Total Price(incl tax)</th></tr>';

    foreach ($ordered_items as $key => $item) {

         $print_array[$key]['sku'] = $sku = $item['sku']; 



        //$print_array[$key]['sku']  = $item['sku'];

        $print_array[$key]['order_items_name'] = $name = $item['order_items_name']; 

        // $print_array[$key]['ordered_qty'] = $qty = $item['ordered_qty'];



        $amount = (int)$item['order_items_total'];

        setlocale(LC_MONETARY, 'en_IN');

        $amount = money_format('%!i', $amount);

        

        

        $print_array[$key]['order_items_price'] = $price = $amount;

        

        

        $table_html .= '<tr>';

         //$table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$sku.'</td>';

        

        $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$name.'</td>';

        

        //$table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$qty.'</td>';

        $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$price.'</td>';

        $table_html .= '</tr>'; 

    }

    $table_html .= '<table>';

    // echo $table_html;

    $filename = "best_sellers_".date('Y_m_d',strtotime("-1 day")).".csv";

    $file = Mage::getBaseDir() . DS .'kaw17842knlpREGdasp9045'. DS .'navision'. DS .'export'. DS .$filename;

    

    $filepath = fopen($file,"w");

    $header = array('SKU','Product Name','Item Total');  

    fputcsv($filepath, $header);

    

    foreach ($print_array as $key => $value) {



         fputcsv($filepath, $value);

    }

    fclose($filepath);





    $vars = array();

    $subject = "Bestsellers for ".date('Y-m-d',strtotime("-1 day")); 

    $reciepient = array('akhilesh@electronicsbazaar.com','kushlesh@electronicsbazaar.com','rajkumarvarma@kaykay.co.in','sharad@compuindia.com','amritpalsingh.d@electronicsbazaar.com');

    $template_id = "navisionexport_hourly_email_template";

        $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);

        $template_variables = array(

            'filename' => $filename,

            'msg' =>$table_html,

            );

        $storeId = Mage::app()->getStore()->getId();

        $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);

        // print_r($processedTemplate); exit;

        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');

        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');



        $attachment = file_get_contents($file);

        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');

        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');

        //$reciepient = array('web.maheshgurav@gmail.com'); 

        try {

            $z_mail = new Zend_Mail('utf-8');



            $z_mail->setBodyHtml($processedTemplate)

                ->setSubject($subject)

                ->addTo($reciepient)
                // ->addBcc($developer)

                ->setFrom($senderEmail, $senderName);



            $attach = new Zend_Mime_Part($attachment);

            $attach->type = 'application/csv';

            $attach->disposition = Zend_Mime::DISPOSITION_INLINE;

            $attach->encoding    = Zend_Mime::ENCODING_8BIT;

            $attach->filename    = $filename;



            $z_mail->addAttachment($attach);

            $z_mail->send();

        }catch(Exception $e){

            Mage::log("Bestsellers ".$e->getMessage(),null,"customexport.log",true);

        }

Mage::log("Bestsellers stop",null,"customexport.log",true);     
