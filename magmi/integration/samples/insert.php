<?php require_once("../../inc/magmi_defs.php");
require_once("../inc/magmi_datapump.php");
require_once '../../../app/Mage.php';

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

//time start
$start = get_microtime_float();

$fp_csv = fopen("../../../var/import/insertsku.csv","r");

// $dp->beginImportSession("default","create");
$dp->beginImportSession("Default","create");

if($fp_csv){
	$cnt = 0;
	while (($arr_line_data = fgetcsv($fp_csv, 1000, ",")) !== FALSE) {  
		
		$sku = $arr_line_data[0];
		$type = $arr_line_data[1];
		$name = $arr_line_data[2];
		$attribute_set = $arr_line_data[3];
		$store = $arr_line_data[4];
		$categories = $arr_line_data[5];
		$price = (int) $arr_line_data[6];
		$qty = (int) $arr_line_data[7];
		
		$insert_item = array('store'=>$store,
			'name'=>$name,
			'sku'=>$sku,
			'TypeId'=>$type,
			// 'categories'=>$categories,
			// 'price'=>$price,
			'attribute_set'=>$attribute_set,
			'qty'=>$qty,
			"is_in_stock" => "1",
			"tax_class_id" => "2",
			"weight" => "1",
			"description" => "test",
			"short_description" => "test new",
			"visibility" => "4"
			);


		if($price > 0) {
			// echo 'sku '.$sku,' price'.$price;
			$dp->ingest($insert_item);
			if ($cnt == '500') {
				exit;
			}
			$cnt++;
		}
	}
	/* end import session, will run post import plugins */
	$dp->endImportSession();

	fclose($fp_csv);

	$end = get_microtime_float(); 
	$totaltime=round($end - $start, 3);
	$time_result=getsecondsToTime($totaltime);
	$result="Total ".$cnt." records updated successfully from insertsku.csv file. Script Execution Time: ".$time_result['h']." hours ".$time_result['m']." minutes ".$time_result['s']." seconds";

	Mage::log($result,null,'ebautomation.log',true);
}