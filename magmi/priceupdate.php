<?php error_reporting(E_ALL | E_STRICT);
require_once '../app/Mage.php';
Mage::setIsDeveloperMode(true); 
ini_set('display_errors', 1);
umask(0);
Mage::app();

set_time_limit(0);


function get_microtime_float ()
{
    list ($msec, $sec) = explode(' ', microtime());
    $microtime = (float)$msec + (float)$sec;
    return $microtime;
}

function getsecondsToTime($seconds)
{
    // extract hours
    $hours = floor($seconds / (60 * 60));
 
    // extract minutes
    $divisor_for_minutes = $seconds % (60 * 60);
    $minutes = floor($divisor_for_minutes / 60);
 
    // extract the remaining seconds
    $divisor_for_seconds = $divisor_for_minutes % 60;
    $seconds = ceil($divisor_for_seconds);
 
    // return the final array
    $obj = array(
        "h" => (int) $hours,
        "m" => (int) $minutes,
        "s" => (int) $seconds,
    );
    return $obj;
}

//product model to check if sku exists
$productModel = Mage::getSingleton('catalog/product');
$start = get_microtime_float();
//open predefined sku file for price update
$fp_csv = fopen("../var/import/update_price_sample.csv","r");

if($fp_csv){
	$cnt = 0;
	while (($arr_line_data = fgetcsv($fp_csv, 1000, ",")) !== FALSE) {  
		$sku = $arr_line_data[0];
		//get the product id by sku
	   	$id = $productModel->getIdBySku($sku);
	   	if ($id) {
	      //if the id exists then mark it for update
			$price = $arr_line_data[1];
	      if (!isset($productIdsByPrice[$price])) {
	         $productIdsByPrice[$price] = array();
	      }
	      $productIdsByPrice[$price][] = $id;
	      $cnt++;
	   	}
	}
}

fclose($fp_csv);
//now you should have the product ids you need to update grouped by price
//just do the fast update
if(!empty($productIdsByPrice)){
	foreach ($productIdsByPrice as $price => $ids) {
		// print_r($ids); exit;
	    Mage::getSingleton('catalog/product_action')->updateAttributes(
	        $ids, //what products to update
	        array('price' => $price, 'status' => 1), //what attributes to update
	        0 //store id for update
	    );
	}
}
	$end = get_microtime_float(); 
	$totaltime=round($end - $start, 3);
	$time_result=getsecondsToTime($totaltime);
	$result="Total ".$cnt." records updated successfully from update_price_sample.csv file. Script Execution Time: ".$time_result['h']." hours ".$time_result['m']." minutes ".$time_result['s']." seconds";

	Mage::log($result,null,'ebautomation.log',true);	
?>