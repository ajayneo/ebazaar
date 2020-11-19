<?php 
require_once("../../inc/magmi_defs.php");
require_once("../inc/magmi_datapump.php");
require("../../../app/Mage.php");

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
$fp_csv = fopen("../../../AutomationExports/Category_importer.csv","r");

// $dp->beginImportSession("default","create");
$dp->beginImportSession("Category on fly","create");

if($fp_csv){
	try{
		while (($arr_line_data = fgetcsv($fp_csv, 1000, ",")) !== FALSE) {  
			$arr_line_data = array_map("utf8_encode", $arr_line_data); //added
			echo $sku = $arr_line_data[0];
			//$type = $arr_line_data[1];
			$name = $arr_line_data[1];
			$attribute_set = $arr_line_data[2];
			$store = $arr_line_data[3];
			$categories = $arr_line_data[4];
			/*$price = (int) $arr_line_data[6];
			$qty = (int) $arr_line_data[7];*/
		
			$insert_item = array('store'=>$store,
				'name'=>$name,
				'sku'=>$sku,
				//'TypeId'=>$type,
				'categories'=>$categories,
				//'price'=>$price,
				'attribute_set'=>$attribute_set
				//'qty'=>$qty,
				//"is_in_stock" => "1",
				//"tax_class_id" => "2",
				//"weight" => "1",
				//"description" => "test",
				//"short_description" => "test new",
				//"visibility" => "4"
				);


			print_r($insert_item);
				$dp->ingest($insert_item);
			
			
		}
	}catch(Exception $e){
		echo $e->getMessage();	
	}
	/* end import session, will run post import plugins */
	//$dp->endImportSession();

	fclose($fp_csv);
	
}	

?>
