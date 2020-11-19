<?php require_once('../app/Mage.php');
umask(0);
Mage::app();
date_default_timezone_set('Asia/Calcutta');
ini_set('memory_limit', '-1');


$file = fopen("script_2_correct_acustomers_290118.csv","r");

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
		$data[$line[0]] = $line[1];
		//echo "UPDATE customer_entity_int SET value=".$line[1]." WHERE entity_id=".$line[0]." AND attribute_id=646"; 
		//$write->query("UPDATE customer_entity_int SET value=".$line[1]." WHERE entity_id=".$line[0]." AND attribute_id=646");
		}
		catch(Exception $e) {
		echo 'Message: ' .$e->getMessage();
		}
	}    
	$count++;
}
// echo '<pre>', print_r($data); echo "<pre>"; exit;
$query = '';
foreach($data as $email_id => $created_at)
    {   
	   	// echo $email_id;
	   	// echo $created_at;
	   	// exit;
	   	if(strlen($email_id) > 0 && strlen($created_at) > 0)
	   	{
			$digital_date = date('Y-m-d h:i:s', strtotime($created_at));
			$digital_date = gmdate('Y-m-d h:i:s', strtotime($created_at));
			try {    	
			$query = "UPDATE customer_entity SET created_at = '".$digital_date."' WHERE email = '".$email_id."'"; 
			$write->query($query);
			// echo $email_id;
			// exit;
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