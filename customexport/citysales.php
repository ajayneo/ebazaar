<?php
require_once('../app/Mage.php');
umask(0);
Mage::app();
date_default_timezone_set('Asia/Calcutta');
 Mage::log("Citysales start",null,"customexport.log",true);   
//Mage::getModel('navisionexport/customexport')->bestsellers();
//database read adapter 
$read = Mage::getSingleton('core/resource')->getConnection('core_read'); 
// $last_order_id = Mage::getStoreConfig('sidebanner_section/dashboard/dailysales');
$date_from = date('Y-m-d 00:00:00');
$gm_from = gmdate('Y-m-d H:i:s', strtotime($date_from));
$query = "SELECT a.city, SUM(i.row_total_incl_tax) as amount FROM sales_flat_order_item as i INNER JOIN catalog_category_product as c ON c.product_id = i.product_id LEFT JOIN sales_flat_order as o ON o.entity_id = i.order_id INNER JOIN sales_flat_order_address as a ON a.parent_id = o.entity_id AND a.city IN ('Mumbai','Chennai','Hyderabad','Bangalore','Calcutta','Pune','Ahmedabad','Jaipur','Surat','Lucknow','Delhi-NCR','Patna','Bhopal','Indore','Kanpur') AND a.address_type='shipping' WHERE o.created_at > '".$gm_from."' GROUP BY a.city";

$results = $read->fetchAll($query); 
$cat_array = array();
$target = 5000000;
foreach($results as $r)
{
    $cat_array[$r['city']]['Item Total'] = $r['amount'];
    $cat_array[$r['city']]['Target'] = $target;
    $percent = round(($r['amount']/$target * 100),2);   
    $cat_array[$r['city']]['Percent'] = $percent."%";
}
// echo "<pre>";
// print_r($cat_array);
$table_html ='';
$table_html .= '<table style="border-top:1px solid #ddd; border-left:1px solid #ddd; margin:10px 0 0 0;">';
$table_html .= '<tr>';
$table_html .= '<th style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">City</th><th style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Item Total</th><th style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Target</th><th style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">Target Achieved</th>';
$table_html .= '</tr>';
foreach ($cat_array as $city => $value) {
    $table_html .= '<tr>';
    $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$city.'</td>';
    $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.(int)$value['Item Total'].'</td>';
    $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$value['Target'].'</td>';
    $table_html .= '<td style="border-bottom:1px solid #ddd; border-right:1px solid #ddd; padding: 10px;">'.$value['Percent'].'</td>';
    $table_html .= '</tr>';
}

    $table_html .="</table>";
    $subject = "City wise sales and target summary for today ".date('Y-m-d');
    $template_id = "navisionexport_hourly_email_template";
    $emailTemplate = Mage::getModel('core/email_template')->loadDefault($template_id);
    $template_variables = array('msg' =>$table_html);
    $storeId = Mage::app()->getStore()->getId();
    $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);

    $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
    $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');

    $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
    $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
    $reciepient = array('web.maheshgurav@gmail.com'); 
    try {
        $z_mail = new Zend_Mail('utf-8');

        $z_mail->setBodyHtml($processedTemplate)
            ->setSubject($subject)
            ->addTo($reciepient)
            ->setFrom($senderEmail, $senderName);
        $z_mail->send();
    } catch (Exception $e) {
        
 Mage::log("Citysales ".$e->getMessage(),null,"customexport.log",true);   
    }
 Mage::log("Citysales stop",null,"customexport.log",true);   