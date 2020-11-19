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
$file_name = 'imei_nov-feb.csv';
// if (($handle = fopen("Live-IMEI-17-Feb-2018.csv", "r")) !== FALSE) {
if (($handle = fopen($file_name, "r")) !== FALSE) {
	$invoice_array = array(); 
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
		if($row > 1)
		{
         $invoice_array[$data[0]][$data[2]][] = $data[1];         
         $row++;
        }
		$row++;
    }  
    $count_imei = '';
    foreach ($invoice_array as $order_number => $sku_array) {
        $imei_array = array();
        $Order_item_sku_list = array();
        foreach ($sku_array as $key => $value) {

            //code for get order item sku list.
			$count_imei = count($value);
			$Order_item_sku_list[] = $key;
			$imei_string = '';

            foreach($value as $key => $imei){                      
			if($count_imei > 1)
			{
			 $imei_string .= $imei." ";
			}
			else
			{
			 $imei_string = $imei;
			}
		  }
		  $imei_array[] = $imei_string;
        }
            //code for get order & invoice items.
            $_order = Mage::getModel('sales/order')->loadByIncrementId($order_number);
            //print_r($_order->getData());
            
            if($_order->getStatus() !== 'canceled')
            {   
                foreach ($_order->getInvoiceCollection() as $invoice) 
				{
					$count = 0;
                    foreach ($invoice->getAllItems() as $item) 
						{
                            if($Order_item_sku_list[$count] === $item->getSku() && $item->setSerial() == '')
                            {   
                                echo " Order ID :". $order_number;
                                echo " Invoice Item ID :". $item->getEntityId();
                                echo " SKU :". $Order_item_sku_list[$count];
                                echo " IMEI :". $imei_array[$count];                                
                                //echo "<br>";
								try
								{
								  $update_imei = '';
								  $update_imei = $imei_array[$count];
                                  //$item->setSerial($update_imei);
                                  //$item->save();
								  echo "Updated"; 
                                  $count++;
                                  if($count == 10) exit;
								} catch (Exception $e) { 
								  echo "Not Updated";
								}
                            }
							
							echo '<br>';
                        }
                 }
            }
    }
  fclose($handle);        
}

/* ********************** EB Website(17-Feb-2018) *****************
************************* Code for IMEI update ********************
************************* Code by Jitendra Patel ****************** */