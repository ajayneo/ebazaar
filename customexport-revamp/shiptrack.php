<?php
require_once('../app/Mage.php'); 
Mage::app();
$username = 'amiable92914';
$pass = 'a4m7ia38b6lel2e3c9tr';
$awb = 207737413;
$order = 200068307;

// $url = "http://plapi.ecomexpress.in/track_me/api/mawbd/?awb=$awb&order=$order&username=$username&password=$pass";
$url = "http://plapi.ecomexpress.in/track_me/api/mawbd/";

$postData['awb'] = $awb;
$postData['order'] = $order;
$postData['username'] = $username;
$postData['password'] = $pass;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url); 
curl_setopt($ch, CURLOPT_POST, 1); 
curl_setopt($ch, CURLOPT_TIMEOUT, 100);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 
$xml = curl_exec($ch);

$str_test = (string) $xml;

libxml_use_internal_errors(true);
$res = simplexml_load_string($str_test);
if ($res === false) {
    echo "Failed loading XML\n";
    foreach(libxml_get_errors() as $error) {
        echo "\t", $error->message;
    }
}

$trackingArray = (array)$res;
// echo "<pre>"; print_r($trackingArray);
$returnTracking = array();
$scanStatus = array();
$i = 1;
$j = 0;
// print_r($trackingArray);
foreach($trackingArray as $key=>$data)
{
	$awb = (array) $trackingArray['object'];

	foreach ($data as $newkey => $newvalue) {
		foreach ($newvalue as $key => $value) { 
			$scan = ( array ) $value;

			foreach ($scan as $key => $value) {
				if($i % 2 == 0){
					$code = (int) $value[3];
					if($code > 0){
						$scanStatus[$j]['date'] = $value[0];
						$scanStatus[$j]['status'] = $value[1];
						$scanStatus[$j]['resion'] = $value[8];
						$scanStatus[$j]['code'] = $value[3];
						$j++;
					}

					if($j == 5){
						 // break 2; 
					}
				}

				$i++;
			}
		}
	}
}
$returnTracking['awb']['customer'] = 'ECOM EXPRESS LTD';
$returnTracking['awb']['status'] = $awb['field'][12];
$returnTracking['awb']['tracking_status'] = $awb['field'][11];
$returnTracking['awb']['receiver'] = $awb['field'][15];
$returnTracking['awb']['delivery_date'] = Mage::getModel('core/date')->date('g:ia jS F, Y', strtotime($awb['field'][18]));

$all_scans = array();
foreach ($scanStatus as $key => $step) {
	$s = explode(",",$step['date']);
	$date = $s[0];
	$region = $step['resion'];
	$all_scans[$date][$region][] = $step;
}
// $returnTracking['status'] = $scanStatus;
$returnTracking['status'] = $all_scans;

$returnTracking['delivered'] = $delivered;
$returnTracking['title'] = $queryArray['title'];

$returnTracking['ship_date'] = $queryArray['ship_date'];
$returnTracking['order_date'] = $queryArray['order_date'];
$returnTracking['ship_addr'] = $queryArray['ship_addr'];

if($returnTracking['awb']['tracking_status'] == 'Delivered'){
	$returnTracking['step'] = 'step3';
}else{
	$returnTracking['step'] = $queryArray['step'];
}

// asort($scanStatus);
// echo "<pre>";
// print_r($scanStatus);
// echo "</pre>";

// print_r($dispatches);
// $tracking = $returnTracking;
echo "<pre>";
print_r($returnTracking);
?>
