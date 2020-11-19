<?php 
/**
* Title: Check order shipment status
* Dev: Mahesh Gurav
* Date: 27 Mar'18
**/
//Create a variable for start time.
$time_start = microtime(true);
require_once('../app/Mage.php'); 
Mage::app();
Mage::setIsDeveloperMode(true);

init();
//function to perform status update
function init(){
    $ecom_carrieres = array('ecom_express','ecom');
    $vxpress_carrieres = array('v_express','vexpress');
    $bluedart_carrieres = array('bluedart');

    $collection = Mage::getResourceModel('sales/order_collection');
    $collection->addFieldToSelect(array('entity_id','increment_id','status'));
    $collection->getSelect()->join(array("s" => 'sales_flat_shipment_track'),"main_table.entity_id = s.order_id",array('carrier_code','track_number'));
    $collection->addFieldToFilter('main_table.created_at',array('gteq'=>gmdate('Y-m-d h:i:s', strtotime('-15 day'))));
    // $collection->addFieldToFilter('main_table.increment_id',200083624);
    $collection->addFieldToFilter('main_table.status',array('neq'=>'delivered'));
    $collection->addFieldToFilter('main_table.status',array('eq'=>'shipped'));
    // echo $collection->getSelect();
    $bluedart = array();
    $ecom = array();
    $ecom_i = 0;
    $bluedart_i = 0;
    foreach ($collection as $order) {
        
        //print_r($order->getData());
        if(in_array($order->getCarrierCode(), $ecom_carrieres)){
            // echo "This is ecom = ".$order->getTrackNumber()." <br>";
            $ecom[] = $order->getTrackNumber();
            $ecom_i++;
            if($ecom_i == 19) break;
        }else if(in_array($order->getCarrierCode(), $bluedart_carrieres)){
            $bluedart[] = $order->getTrackNumber();
            $bluedart_i++;
            if($bluedart_i == 19) break;
        }else if(in_array($order->getCarrierCode(), $vxpress_carrieres)){
            // echo "This is vexpress = ".$order->getTrackNumber()." <br>";
        }
    }

    $bluedart_status = bluedart($bluedart);
    // echo "count ecom awb ".count($ecom);
    $ecom_status = ecom($ecom);
    echo "bluedart awb_numbers status <pre>"; print_r($bluedart_status);
    echo "</pre> ecom awb_numbers status <pre>";print_r($ecom_status);

}

//function to check bluedart shipments status
function bluedart($awb)
{
    // echo "Checking ".count($awb)." AWB numbers in bluedart<br/>";
    $lickey = '4ecbd06dc0b9737d69120029cb05c9df';
    $loginid = 'BOM00001';
    // $awbnumbers = $order->getTrackNumber();
    $awbnumbers = implode(",", $awb);
    $url = "http://www.bluedart.com/servlet/RoutingServlet?handler=tnt&action=custawbquery&loginid=$loginid&awb=awb&numbers=$awbnumbers&format=xml&lickey=$lickey&verno=1.3f&scan=1";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_POST, 1); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    // curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 
    $xml = curl_exec($ch);
    $res = simplexml_load_string($xml);
    $trackingArray = (array)$res;
    $awb_numbers_delivered = array();
    foreach ($trackingArray['Shipment'] as $key => $data) {
    	# code...
    	$awb = (array)$data;
    	// echo "<pre>"; print_r($awb); echo "<pre>";
    	$track_number = (array)$awb['@attributes'];
    	$track_number = $track_number['WaybillNo'];
    	// echo $track_number." : ".$awb['Status']." <br>";

        if($awb['Status'] == 'SHIPMENT OUT FOR DELIVERY'){
        	// echo " change status for track # ".$track_number." OUT <br>";
        }

        if($awb['Status'] == 'SHIPMENT DELIVERED'){
        	// echo "<pre>"; print_r($awb);
        	// echo " change status for track # ".$track_number." DELIVERED <br>";
            $awb_numbers_delivered[] = $track_number;
        }
        
        if($awb['Status'] == 'SHIPMENT IN TRANSIT'){
        	// echo "<pre>"; print_r($awb);
        	// echo " change status for track # ".$track_number." DELIVERED <br>";
        }
    }

    return array('delivered'=>$awb_numbers_delivered);
}

//function to check ecom shipments status
function ecom($awb){
    // print_r($awb);
    // echo "Ecom awb ".count($awb)." numbers checking <br>";
    $username = 'amiable92914';
    $pass = 'a4m7ia38b6lel2e3c9tr';
    $order = array();
    // print_r($queryArray);exit;
    // $url = "http://plapi.ecomexpress.in/track_me/api/mawbd/?awb=$awb&order=$order&username=$username&password=$pass";
    $url = "http://plapi.ecomexpress.in/track_me/api/mawbd/";
    $awb_numbers = implode(",", $awb);

    $postData['awb'] = $awb_numbers;
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
    $res = simplexml_load_string($xml);
    // var_dump($res); exit;
    $trackingArray = (array)$res;
    // $trackingArray = json_decode($res,1);
    // echo print_r($trackingArray);
    $ecom_delivered = array();
    foreach ($trackingArray['object'] as $key => $obj) {
        $data = (array) $obj;
        // print_r($data);
        $awb_number = $data['field'][0];
        // $increment_id = $data['field'][1];
        $awb_status = $data['field'][11];
        // $awb_status_detail = $data['field'][12];
        // echo " Order : ".$increment_id." awb : ".$awb_number." : ".$awb_status." : ".$awb_status_detail."<br>";
        // print_r($data['field']);
        // echo "<br>------------------------------<br/>";
        if($awb_status == 'Delivered'){
            $ecom_delivered[] = $awb_number;
        }
    }

    return array('delivered'=>$ecom_delivered);            
}

//Create a variable for end time. 
$time_end = microtime(true);
//Subtract the two times to get seconds.
$time = $time_end - $time_start ;
echo 'Execution time : ' . $time . ' seconds' ;
?>