<?php require_once '/home/electronicsbazaa/public_html/app/Mage.php';
umask(0);
Mage::app();
// 200077517
// 200077498
// 200077482
// 200077479
// 200077103
// 200077075
// 200076895
// 200076773
// 200076474
// 200076471
echo "Executing Sever IP = ".$_SERVER['SERVER_ADDR'].'<br/>';
$order_id = 200078709;
$msg = '';
$merchant_id = Mage::getStoreConfig('payment/billdesk_ccdc_net/merchant_id');
$billdesk_api = 'https://www.billdesk.com/pgidsk/PGIQueryController';
		//$billdesk_api = 'https://www.billdesk.com/pgidsk/pgijsp/MERCHANTIDRedirect.jsp';
		// $billdesk_api = 'https://uat.billdesk.com/pgidsk/pgmerc/EBAZAAR.jsp';  
		$checksum_key = Mage::getStoreConfig('payment/billdesk_ccdc_net/checksum_key');
		$security_id = Mage::getStoreConfig('payment/billdesk_ccdc_net/security_id');
		$currency_type = Mage::getStoreConfig('payment/billdesk_ccdc_net/currency_type');
		$return_url = 	Mage::getStoreConfig('payment/billdesk_ccdc_net/return_url');
		$request_type = '0122';
		date_default_timezone_set('Asia/Kolkata');
/*Bill Desk Info*/
$current_datetime = date("YmdHis");
// $str = "$merchant_id|$order_id|NA|$order_grand_total|NA|NA|NA|$currency_type|DIRECT|R|$security_id|NA|NA|F|NA|NA|NA|NA|NA|NA|NA|$return_url";
$str = "$request_type|$merchant_id|$order_id|$current_datetime";

$checksum = hash_hmac('sha256',$str,$checksum_key,false); 
$checksum = strtoupper($checksum);
$dataWithCheckSumValue = $str."|".$checksum;
$msg = trim($dataWithCheckSumValue);
echo "Request: <br/>";
echo $data ='msg='.$msg;
$ch=curl_init($billdesk_api);
				// curl_setopt($ch, CURLOPT_POST, true);
				// curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

				// curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		        curl_setopt($ch, CURLOPT_POST, 1);
		        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$msg_response =curl_exec($ch);
				echo "<br/>Response: <br/>";
				print_r($msg_response);
