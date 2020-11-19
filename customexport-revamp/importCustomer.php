<?php require_once('/home/electronicsbazaa/public_html/app/Mage.php');
Mage::app();
umask(0);
ini_set('memory_limit', '-1');
ini_set('max_execution_time','1000');  
//ini_set('innodb_lock_wait_timeout','120');  
//innodb_lock_wait_timeout=120         

// echo "fdssf";


//$result = Mage::getModel('ebautomation/customer')->allCustomerMapping();


function readCSV(){
$csvFile = 'mappingrepcodes.csv';
 $file_handle = fopen($csvFile, 'r');
 	while (!feof($file_handle) ) {
  		$line_of_text[] = fgetcsv($file_handle, 1024);
	}
	fclose($file_handle);
	return $line_of_text;
}



// Set path to CSV file
//$csvFile = 'mappingrepcodes.csv';

$csv = readCSV($csvFile);
echo '<pre>';
print_r($csv);
//$cust = array();  $repcodes = array();
$i=1;
foreach ($csv as $row) {

	print_r($row);

	if($i == 1){  $i++; continue;}
	if(!empty($row[1]) && !empty($row[0])){
		echo $row[1];
		Mage::getModel('customer/customer')
		->setWebsiteId(1)
		->loadByEmail($row[0])
		->setRepcode($row[1])
		->setNavMapStatus(0)
		->setNavMapDetails('')
		->save();
	}		

	$i++;

}




echo '**********************************done*********************************************';


?>