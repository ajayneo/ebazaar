<?php require_once('/home/electronicsbazaa/public_html/app/Mage.php');
Mage::app();
umask(0);
ini_set('memory_limit', '-1');
ini_set('max_execution_time','1000');  

function readCSV(){
$csvFile = 'update_imei.csv';
 $file_handle = fopen($csvFile, 'r');
 	while (!feof($file_handle) ) {
  		$line_of_text[] = fgetcsv($file_handle, 1024);
	}
	fclose($file_handle);
	return $line_of_text;
}

$csv = readCSV($csvFile);

$i=1;
foreach ($csv as $row) {

	// print_r($row);

	if($i == 1){  $i++; continue;}
	if(!empty($row[1]) && !empty($row[0])){
		// echo $row[1];
		$invoiceIncrementId = $row[0];
		$item_sku = $row[1];
		$item_imei = $row[2];

		$invoice = Mage::getModel("sales/order_invoice")->loadByIncrementId($invoiceIncrementId);

		$items = $invoice->getAllItems();	
		$itemupdate = array();
		foreach ($items as $item) {
			# code...
			echo $item->getId()." : ".$item->getSku()." : ".$item_sku." : ".$item_imei." <br/>";
			if($item->getSku() == $item_sku){
				$itemupdate['item_id'] = $item->getId();
				$itemupdate['sku'][] = $item_sku;
				$itemupdate['serial'] .= $item_imei." ";
			}

			break;
		}
	}		

	$i++;

}
?>