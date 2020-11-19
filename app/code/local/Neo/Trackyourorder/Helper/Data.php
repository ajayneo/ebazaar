<?php

class Neo_Trackyourorder_Helper_Data extends Mage_Core_Helper_Abstract

{





	protected function getProperty($var){

        if($var == 'key') Return Mage::getStoreConfig('shipping/fedex/key');

        if($var == 'password') Return Mage::getStoreConfig('shipping/fedex/password');

        if($var == 'shipaccount') Return Mage::getStoreConfig('shipping/fedex/shipaccount');

        if($var == 'billaccount') Return Mage::getStoreConfig('shipping/fedex/shipaccount');

        if($var == 'dutyaccount') Return Mage::getStoreConfig('shipping/fedex/shipaccount');

        if($var == 'freightaccount') Return Mage::getStoreConfig('shipping/fedex/shipaccount');

        if($var == 'trackaccount') Return Mage::getStoreConfig('shipping/fedex/shipaccount');

        if($var == 'dutiesaccount') Return Mage::getStoreConfig('shipping/fedex/shipaccount');

        if($var == 'importeraccount') Return Mage::getStoreConfig('shipping/fedex/shipaccount');

        if($var == 'brokeraccount') Return Mage::getStoreConfig('shipping/fedex/shipaccount');

        if($var == 'distributionaccount') Return Mage::getStoreConfig('shipping/fedex/shipaccount');

        if($var == 'locationid') Return 'PLBA';

        if($var == 'printlabels') Return false;

        if($var == 'printdocuments') Return true;

        if($var == 'packagecount') Return '1';



        if($var == 'meter') Return Mage::getStoreConfig('shipping/fedex/meter');



        if($var == 'shiptimestamp') Return mktime(10, 0, 0, date("m"), date("d")+1, date("Y"));



        if($var == 'spodshipdate') Return '2014-07-21';

        if($var == 'serviceshipdate') Return '2017-07-26';



        if($var == 'readydate') Return '2014-07-09T08:44:07';

        //if($var == 'closedate') Return date("Y-m-d");

        if($var == 'closedate') Return '2014-07-17';

        if($var == 'pickupdate') Return date("Y-m-d", mktime(8, 0, 0, date("m")  , date("d")+1, date("Y")));

        if($var == 'pickuptimestamp') Return mktime(8, 0, 0, date("m")  , date("d")+1, date("Y"));

        if($var == 'pickuplocationid') Return 'XXX';

        if($var == 'pickupconfirmationnumber') Return '1';



        if($var == 'dispatchdate') Return date("Y-m-d", mktime(8, 0, 0, date("m")  , date("d")+1, date("Y")));

        if($var == 'dispatchlocationid') Return 'XXX';

        if($var == 'dispatchconfirmationnumber') Return '1';



        if($var == 'tag_readytimestamp') Return mktime(10, 0, 0, date("m"), date("d")+1, date("Y"));

        if($var == 'tag_latesttimestamp') Return mktime(20, 0, 0, date("m"), date("d")+1, date("Y"));



        if($var == 'expirationdate') Return date("Y-m-d", mktime(8, 0, 0, date("m"), date("d")+15, date("Y")));

        if($var == 'begindate') Return '2014-07-22';

        if($var == 'enddate') Return '2014-07-25';



        if($var == 'trackingnumber') Return 'XXX';



        if($var == 'hubid') Return '5531';



        if($var == 'jobid') Return 'XXX';



        if($var == 'searchlocationphonenumber') Return '5555555555';

        if($var == 'customerreference') Return 'Cust_Reference';



        if($var == 'shipper') Return array(

            'Contact' => array(

                'PersonName' => 'Sender Name',

                'CompanyName' => 'Sender Company Name',

                'PhoneNumber' => '1234567890'

            ),

            'Address' => array(

                'StreetLines' => array('Address Line 1'),

                'City' => 'Collierville',

                'StateOrProvinceCode' => 'TN',

                'PostalCode' => '38017',

                'CountryCode' => 'US',

                'Residential' => 1

            )

        );

        if($var == 'recipient') Return array(

            'Contact' => array(

                'PersonName' => 'Recipient Name',

                'CompanyName' => 'Recipient Company Name',

                'PhoneNumber' => '1234567890'

            ),

            'Address' => array(

                'StreetLines' => array('Address Line 1'),

                'City' => 'Herndon',

                'StateOrProvinceCode' => 'VA',

                'PostalCode' => '20171',

                'CountryCode' => 'US',

                'Residential' => 1

            )

        );



        if($var == 'address1') Return array(

            'StreetLines' => array('10 Fed Ex Pkwy'),

            'City' => 'Memphis',

            'StateOrProvinceCode' => 'TN',

            'PostalCode' => '38115',

            'CountryCode' => 'US'

        );

        if($var == 'address2') Return array(

            'StreetLines' => array('13450 Farmcrest Ct'),

            'City' => 'Herndon',

            'StateOrProvinceCode' => 'VA',

            'PostalCode' => '20171',

            'CountryCode' => 'US'

        );

        if($var == 'searchlocationsaddress') Return array(

            'StreetLines'=> array('240 Central Park S'),

            'City'=>'Austin',

            'StateOrProvinceCode'=>'TX',

            'PostalCode'=>'78701',

            'CountryCode'=>'US'

        );



        if($var == 'shippingchargespayment') Return array(

            'PaymentType' => 'SENDER',

            'Payor' => array(

                'ResponsibleParty' => array(

                    'AccountNumber' => getProperty('billaccount'),

                    'Contact' => null,

                    'Address' => array('CountryCode' => 'US')

                )

            )

        );

        if($var == 'freightbilling') Return array(

            'Contact'=>array(

                'ContactId' => 'freight1',

                'PersonName' => 'Big Shipper',

                'Title' => 'Manager',

                'CompanyName' => 'Freight Shipper Co',

                'PhoneNumber' => '1234567890'

            ),

            'Address'=>array(

                'StreetLines'=>array(

                    '1202 Chalet Ln',

                    'Do Not Delete - Test Account'

                ),

                'City' =>'Harrison',

                'StateOrProvinceCode' => 'AR',

                'PostalCode' => '72601-6353',

                'CountryCode' => 'US'

            )

        );

    }

    //check tracking by order id

    //developer Mahesh Gurav

    //functions: trackingDetails ecomexpress bluedart

    //date: 08/11/2017

    public function trackingDetails($increment_id){

        $order = Mage::getModel('sales/order')->loadByIncrementId($increment_id);



        if(is_object($order->getShippingAddress())){

            $shipmentCollection = $order->getShipmentsCollection();

            $count_shipments =  count($shipmentCollection);

            if($count_shipments == 0) return 0;





            $queryArray = array();

 

            $queryArray['order_date'] = Mage::getModel('core/date')->date('g:ia jS F, Y', strtotime($order->getCreatedAt()));



            $queryArray['step'] = 'step1';



            $shipping = $order->getShippingAddress()->getFormated();



            





            foreach($shipmentCollection as $shipment){

                $shipmentIncrementId = $shipment->getIncrementId();

                $shipment=Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentIncrementId);

                foreach ($shipment->getAllTracks() as $track) {

                   $queryArray['track_number'] = $track->getTrackNumber();

                   $queryArray['order_increment_id'] = $orderIncrementId;

                   $queryArray['title'] = $track->getTitle();

                   $queryArray['carier_code'] = $track->getCarrierCode();

                   $queryArray['step'] = 'step2';

                   $queryArray['ship_date'] = Mage::getModel('core/date')->date('g:ia jS F, Y', strtotime($track->getCreatedAt()));

                   $queryArray['ship_addr'] = $shipping;



                   break 2; 

                } 



            }



            $carrier_code = $queryArray['carier_code'];

            $title = $queryArray['title'];

                



                if($carrier_code == 'ecom_express' || $title == 'ecom_express' || strrpos($carrier_code, 'ecom') >= 0){

                    $returnTracking = $this->ecomexpress($queryArray);

                }elseif($carrier_code == 'bluedart' || $title = 'bluedart' || strrpos($carrier_code, 'bluedart') >= 0){

                    $returnTracking = $this->bluedart($queryArray);

                }elseif($carrier_code == 'v_express' || $title == 'v_express'){

                    //$returnTracking = $this->vexpress($queryArray);

                }elseif($carrier_code == 'FEDEX_EXPRESS_SAVER' || $title == 'FEDEX_EXPRESS_SAVER'){

                    //$returnTracking = $this->fedex($queryArray);

                }

                

                return $returnTracking;



        }else{

            return 0;

        }

    }



    protected function ecomexpress($queryArray)

    {

            $username = 'amiable92914';

            $pass = 'a4m7ia38b6lel2e3c9tr';

            $awb = $queryArray['track_number'];

            $order = $queryArray['order_increment_id'];

            // print_r($queryArray);exit;

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

            $res = simplexml_load_string($xml);

            // var_dump($res); exit;

            $trackingArray = (array)$res;

            // echo "<pre>"; print_r($trackingArray); echo "<pre>";

            $returnTracking = array();

            $scanStatus = array();

            $i = 1;

            $j = 0;

            $returnTracking['step'] = $queryArray['step'];



            if(!empty($trackingArray)){

                    $awb = (array) $trackingArray['object'];

                foreach($trackingArray as $key=>$data){

                    foreach ($data as $newkey => $newvalue) {
                        if(empty($newvalue)){
                            continue;
                        }
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

                $returnTracking['awb']['status'] = $awb['field'][12];

                $returnTracking['awb']['tracking_status'] = $awb['field'][11];

                $returnTracking['awb']['receiver'] = $awb['field'][15];

                // echo $awb['field'][18]; exit;

                if(!empty($awb['field'][18])){

                    

                    //$returnTracking['awb']['delivery_date'] = Mage::getModel('core/date')->date('g:ia jS F, Y', strtotime($awb['field'][18]));

                    $returnTracking['awb']['delivery_date'] = $awb['field'][18];

                }

                

                $all_scans = array();

                asort($scanStatus);

                foreach ($scanStatus as $key => $step) {

                    $s = explode(",",$step['date']);

                    $date = $s[0];

                    $region = $step['resion'];

                    $all_scans[$date][$region][] = $step;

                }       

                

                // $returnTracking['status'] = $scanStatus;

                $returnTracking['status'] = $all_scans;

                if($returnTracking['awb']['tracking_status'] == 'Delivered'){

                    $returnTracking['step'] = 'step3';

                }



            }//track



            $returnTracking['awb']['customer'] = 'ECOM EXPRESS LTD';

            $returnTracking['title'] = $queryArray['title'];



            $returnTracking['ship_date'] = $queryArray['ship_date'];

            $returnTracking['order_date'] = $queryArray['order_date'];

            $returnTracking['ship_addr'] = $queryArray['ship_addr'];

            $returnTracking['order'] = $order;

            $returnTracking['awbno'] = $queryArray['track_number'];

            $returnTracking['carrier_code'] = $queryArray['carrier_code'];

            



            return $returnTracking;

    }



    protected function vexpress($queryArray)

    {

            

            $username = 'amiable';

            $pass = 'electronic@vxecom';

            $awb = $queryArray['track_number'];

            $order = $queryArray['order_increment_id'];

            $tracktype = 'AWBNO';

            $docket_no = $queryArray['awbno'];



            //$url = "http://plapi.ecomexpress.in/track_me/api/mawbd/?awb=$awb&order=$order&username=$username&password=$pass";

            // $url = "http://124.153.92.36/vxws/vxecommws.asmx/GetECommDocketTrackTraceData?UserID=$username&PassWord=$pass&TrackType=$tracktype&DocketNo=$awb";

            //"http://124.153.92.36/vxws/vxecommws.asmx/GetECommDocketTrackTraceData?UserID=amiable&PassWord=electronic@vxecom&TrackType=AWBNO&DocketNo=8734338";

            //echo "http://124.153.92.36/vxws/vxecommws.asmx/GetECommDocketTrackTraceData?UserID=$username&PassWord=$pass&TrackType=$tracktype&DocketNo=$docket_no";

            // print_r($queryArray);



            $url = 'http://124.153.92.36/vxws/vxecommws.asmx/GetECommDocketTrackTraceData';



            $soap_url = 'http://124.153.92.36/vxws/vxecommws.asmx?wsdl';

            $request_param = array(

                "userID" => $username,

                "PassWord" => $pass,

                "TrackType" => $tracktype,

                "DocketNo" => $docket_no

            );



            $client     = new SoapClient($soap_url, array("trace" => 1, "exception" => 0)); 



                $response = $client->GetECommDocketTrackTraceData($request_param);

                print_r($response);



                $result = $response->GetECommDocketTrackTraceDataResult;



                // print_r($result);

                $xmlString = $result->any;

                // var_dump($xmlString);



                $xml = new SimpleXMLElement($xmlString);



                // var_dump($xml);



                $xmlArray = $xml->NewDataSet->Table;

                // print_r($xmlArray);

                // echo $xmlArray->Consignee_Name;

                $j = 0;

                $scanStatus[$j]['date'] = (string)  $xmlArray->ECOMTimeStamp;

                $scanStatus[$j]['status'] = (string)  $xmlArray->Status_Code;

                $scanStatus[$j]['resion'] = (string)  $xmlArray->Consignee_City;

                // foreach ($xmlArray as $element) {

                    

                    //echo $tracking_code = $element['Tracking_Code'];

                    // foreach ($element as $key => $value) {

                        //echo "<br>$key => $value";

                    // }

                // }



                $returnTracking['awb']['customer'] = (string) $xmlArray->Vendor_Name;

                $returnTracking['awb']['status'] = (string)  $xmlArray->Status_Code;

                $returnTracking['awb']['tracking_status'] = (string)  $xmlArray->Consignee_Name;

                $returnTracking['awb']['receiver'] = (string)  $xmlArray->Consignee_Name;

                $returnTracking['awb']['delivery_date'] = Mage::getModel('core/date')->date('g:ia jS F, Y', strtotime((string) $xmlArray->ECOMTimeStamp));

                $returnTracking['status'] = $scanStatus;

                $returnTracking['title'] = $queryArray['title'];



                $returnTracking['ship_date'] = $queryArray['ship_date'];

                $returnTracking['order_date'] = $queryArray['order_date'];

                $returnTracking['ship_addr'] = $queryArray['ship_addr'];



                if($returnTracking['awb']['tracking_status'] == 'Delivered'){

                    $returnTracking['step'] = 'step3';

                }else{

                    $returnTracking['step'] = $queryArray['step'];

                }

                $returnTracking['awbno'] = $queryArray['awbno'];

                $returnTracking['order'] = $queryArray['order'];

            return $returnTracking;

    }



    protected function bluedart($queryArray)

    {   

        // echo "<pre>"; print_r($queryArray); echo "</pre>"; exit;

        $lickey = '4ecbd06dc0b9737d69120029cb05c9df';

        $loginid = 'BOM00001';

        $awbnumbers = $queryArray['track_number'];

        $url = "http://www.bluedart.com/servlet/RoutingServlet?handler=tnt&action=custawbquery&loginid=$loginid&awb=awb&numbers=$awbnumbers&format=xml&lickey=$lickey&verno=1.3f&scan=1";



        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url); 

        curl_setopt($ch, CURLOPT_POST, 1); 

        curl_setopt($ch, CURLOPT_TIMEOUT, 100);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 

        $xml = curl_exec($ch);

        $res = simplexml_load_string($xml);

        $trackingArray = (array)$res;

        // echo "<pre>"; print_r($trackingArray); echo "</pre>"; exit;

        $returnTracking = array();

        $scanStatus = array();



        // $xml = new SimpleXMLElement($xml);

        

        $returnTracking['awbno'] = $awbnumbers;

        $returnTracking['carrier_code'] = $queryArray['carrier_code'];



        $i = 1;

        $j = 0;



        foreach($trackingArray as $key=>$data)

        {

            $awb = (array) $trackingArray['Shipment'];



            // print_r($awb);



            $scans = $awb['Scans'];





            // foreach ($data as $newkey => $newvalue) {

            //  foreach ($newvalue as $key => $value) { 

            //      $scan = ( array ) $value;



            //      $scanStatus[$j]['date'] = $scan['ScanDate'].' '.$scan['ScanTime'];

            //      $scanStatus[$j]['status'] = $scan['Scan'];

            //      $scanStatus[$j]['resion'] = $scan['ScannedLocation'];



            //      if($i == 5){

            //           break 2; 

            //      }



            //      $j++;

            //      $i++;

            //  }

            // }

        }



        // echo "<pre>"; print_r($awb); echo "</pre>";

        // $tracking_details = $scans['ScanDetail'];

        // print_r($tracking_details);



        // var_dump($scans);

        $scan_cnt = 0;

        $scanStatus = array();

        foreach ($scans as $key => $value) {

            $scanStatus[$scan_cnt]['date'] = (string) $value->ScanDate;

            $scanStatus[$scan_cnt]['time'] = (string) $value->ScanTime;

            $scanStatus[$scan_cnt]['status'] = (string) $value->Scan;

            $scanStatus[$scan_cnt]['resion'] = (string) $value->ScannedLocation;

            $scanStatus[$scan_cnt]['code'] = (string) $value->ScanCode;

            $scan_cnt++;

        }



        $all_scans = array();

        // asort($scanStatus);

        foreach ($scanStatus as $key => $step) {

            $date = $step['date'];

            $region = $step['resion'];

            $all_scans[$date][$region][] = $step;

        }

        // print_r($all_scans);

        $returnTracking['awb']['customer'] = $awb['CustomerName'];

        $returnTracking['awb']['status'] = $awb['StatusType'];

        $returnTracking['awb']['tracking_status'] = $awb['Status'];

        $returnTracking['awb']['receiver'] = $awb['ReceivedBy'];

        $returnTracking['awb']['delivery_date'] = $awb['ExpectedDeliveryDate'];

        $returnTracking['tracking_details'] = $all_scans;

        $returnTracking['title'] = $queryArray['title'];



        $returnTracking['ship_date'] = $queryArray['ship_date'];

        $returnTracking['order_date'] = $queryArray['order_date'];

        $returnTracking['ship_addr'] = $queryArray['ship_addr'];

        $returnTracking['order'] = $queryArray['order_increment_id'];



        if($returnTracking['awb']['tracking_status'] == 'SHIPMENT DELIVERED'){

            $returnTracking['step'] = 'step3';

        }else{

            $returnTracking['step'] = $queryArray['step'];

        }

        

        // print_r($returnTracking);

        return $returnTracking;

    }

}

	 