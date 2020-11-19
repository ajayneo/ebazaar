<?php
echo "Testing Going on...";exit;
error_reporting(-1);
ini_set('display_errors', 'On');
//Set unlimited execution and memory time.
ini_set('memory_limit', '-1');
set_time_limit(0);

require_once('app/Mage.php');
Mage::app();

exit('here');
echo "Below custom list of asm need to be update.";

$file = fopen("asm_keval.csv","r");

//count row
//echo "JP:".count($file);

//database write adapter 
$write = Mage::getSingleton('core/resource')->getConnection('core_write');

//parse data from csv file line by line
$count = 0;
$data = array();
while(($line = fgetcsv($file)) !== FALSE) {
	//echo "Test";print_r($line);          
	//update query.
	if($count > 0 && $line[1] != '')
	{ 
		try {

		// $data[$line[0]] = $line[1];
		$data[$line[1]][] = $line[0];
		//echo "UPDATE customer_entity_int SET value=".$line[1]." WHERE entity_id=".$line[0]." AND attribute_id=646"; 
		//$write->query("UPDATE customer_entity_int SET value=".$line[1]." WHERE entity_id=".$line[0]." AND attribute_id=646");
		}
		catch(Exception $e) {
		echo 'Message: ' .$e->getMessage();
		}
	}    
	$count++;
}
echo '<pre>', print_r($data); echo "<pre>"; exit;

foreach($data as $asmid => $customer_array)
    {   
    	$entity_id_str = '';
		$entity_id_str = implode(",", $customer_array);
	   	if($asmid != '' && strlen($entity_id_str) > 0)
	   	{
			try {    	
			echo "UPDATE customer_entity_int SET value=".$asmid." WHERE entity_id IN (".$entity_id_str.") AND attribute_id=646"; 
			//$write->query("UPDATE customer_entity_int SET value=".$asmid." WHERE entity_id IN (".$entity_id_str.") AND attribute_id=646");
			}
			catch(Exception $e) {
			echo 'Message: ' .$e->getMessage();
			}
	    }
    }
//close file.
fclose($file);
echo "Script Complete";	
?>
