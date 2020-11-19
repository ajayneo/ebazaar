<?php
require_once("../../inc/magmi_defs.php");
require_once("../inc/magmi_datapump.php");
require_once '../../../app/Mage.php';
$code = Mage::app()->getStore()->getCode();
/**
 * create a Product import Datapump using Magmi_DatapumpFactory
 */
$dp = Magmi_DataPumpFactory::getDataPumpInstance("productimport");

/**
* insert item array
*/

$insert_item = array();

/**
* read a csv file for creating insert_item
*/
$fp_csv = fopen("../../../var/import/skusample.csv","r");

// $dp->beginImportSession("default","create");
$dp->beginImportSession("Category on fly","create");

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
			'categories'=>$categories,
			'price'=>$price,
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
}	

?>
