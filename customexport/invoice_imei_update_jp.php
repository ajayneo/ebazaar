<?php
/* ********************** EB Website(17-Feb-2018) *****************
************************* Code for IMEI update ********************
************************* Code by Jitendra Patel ****************** */
ini_set('memory_limit','-1');
set_time_limit(0);
require_once('../app/Mage.php');
error_reporting(E_ALL & ~E_NOTICE);
umask(0);
Mage::app();
$row = 1;
if (($handle = fopen("Live-sheet-18-Feb-2018-demo-2.csv", "r")) !== FALSE) {
  $invoice_array = array();
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        if($row !== 1){ if($row == 9291){ echo "Best Called"; break; }
            // $invoice_array[$data[0]][$data[1]]['serail'] .= $data[2].' '; 
            // echo "<pre>";
            // print_r($data); 
            // echo "</pre>";
            $order_number = '';
            $empty_string = '';
            $sku_list = array();
            $imei_list = array();            
            $order_number = $data[0];
            $sku_list = explode('|',$data[1]);
            $imei_list = explode('|',$data[2]);

            //echo "Order :".$order_number;
            //echo "SKU :"; echo '<pre>', print_r($sku_list);
            //echo "IMEI :"; echo '<pre>', print_r($imei_list);

            //code for get order & invoice items.
            $_order = Mage::getModel('sales/order')->loadByIncrementId($order_number);
            //print_r($_order->getData());
            
            if($_order->getStatus() !== 'canceled')
            {   $empty_string = '';
                foreach ($_order->getInvoiceCollection() as $invoice) 
                {
                    $count = 0;
                    foreach ($invoice->getAllItems() as $item) 
                        {   
                            if($sku_list[$count] === $item->getSku() && $item->getSerial() == '')
                            {   
                                $empty_string .= " Order ID :". $order_number;
                                $empty_string .= " Invoice Item ID :". $item->getEntityId();
                                $empty_string .= " SKU :". $sku_list[$count];
                                //echo " IMEI :". $imei_list[$count];                                
                                //echo "<br>";
                                try
                                {
                                  $update_imei = '';
                                  $update_imei = $imei_list[$count];
                                  if($update_imei != '')
                                  { $empty_string .= " IMEI :". $update_imei;
                                    $item->setSerial($update_imei);
                                    $item->save();
                                  $empty_string .= " Updated"; 
                                  }
                                  else
                                  {
                                    $empty_string .= " IMEI can not be empty.";
                                  }
                                  $count++;
                                } catch (Exception $e) { 
                                  $empty_string .= " Not Updated";
                                }
                                Mage::log($empty_string,null,'imei_update_jp.log',true);//exit;
                            }                                                        
                        }
                 }
            }      
        }

        $row++;
    }     
    fclose($handle);
}

/* ********************** EB Website(17-Feb-2018) *****************
************************* Code for IMEI update ********************
************************* Code by Jitendra Patel ****************** */