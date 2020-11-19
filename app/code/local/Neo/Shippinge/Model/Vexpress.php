<?php
/**
 * Created by PhpStorm.
 * User: webwerks
 * Date: 2/10/14
 * Time: 11:13 AM
 */

class Neo_Shippinge_Model_Vexpress extends Mage_Core_Model_Abstract
{
    public function vexpress($post)
    {
        $incrementId    = $post["incrementid"];
        $no_of_awbs     = $post["no_of_awbs"];
        $weight         = $post["weight"];
        $invoice_id     = $post["invoice_id"];

        if($incrementId){
            $order = Mage::getModel("sales/order")->loadByIncrementId($incrementId);
            $invoice = Mage::getModel("sales/order_invoice")->load($invoice_id);
   

            $invoice_items = $invoice->getAllItems();
            $item_description = '';
            if(count($invoice_items) > 1){
                foreach ($invoice_items as $item) {
                    $item_description .= $item->getName().' +';
                }
            }else{
                foreach ($invoice_items as $item) {   
                    $item_description = $item->getName();
                }   
            }

            $item_description = rtrim($item_description,'+');

           
            $warehouseAddress = Mage::helper('ebautomation')->getWarehouseAddress($invoice->getOrderId(),$invoice->getId());


            $shipping_address = $order->getShippingAddress();  

            $vexpressModel = Mage::getModel("vexpressawb/vexpress");
            $collectionVexpress = $vexpressModel->getCollection()->addFieldToFilter('status',0)->getFirstItem();

            //$url = 'http://123.252.202.55/order_generation/order_generation_create.php'; 
            //$url = 'http://124.153.92.36/vxws/vxecommws.asmx';
            $url = 'http://180.179.114.148/vxpickupApi/api/getorder/'; //http://124.153.92.36/vxpickupApi/api/getorder/';
            $pmt = '';

            if($order->getPayment()->getMethod() == 'cashondelivery'){
                $pmt = 'COD';
            }else{
                //$pmt = 'Pre-Paid';
                $pmt = 'PRE-PAID';
            } 

            $shippingAddress = trim(implode(' ',$shipping_address->getStreet()));
            if(strlen($shippingAddress) < 5){
                for($i = 1; $i <= (5 - strlen($shippingAddress)); $i++){
                    $shippingAddress = $shippingAddress.'.';
                }
            }else{
                $shippingAddress = substr($shippingAddress, 0,255);
            }


            if(strlen($item_description) < 3){
                for($i = 1; $i <= (3 - strlen($item_description)); $i++){
                    $item_description = $item_description.'.';
                }
            }else{
                $item_description = substr($item_description, 0,50);
            }

            //echo $shipping_address->getFax();exit;
            Mage::log("check pincode for shipment ".$shipping_address->getPostcode(),null,'vexpress_new.log',true);
            $date_order_created = date('m-d-Y', strtotime($order->getCreatedAt()));
            $Parameters = array(
                'api_key' => 'VX0006',//'VX0006', //'VX001',                //MANDATORY FIELD (VX001) 
                'transaction_id'=> $order->getIncrementId().substr(mt_rand(), 1, 5),              //MANDATORY FIELD (Order No. or any other No.)
                //MANDATORY FIELD (AWB Series mention in mail) 
                // 'airwaybilno' => $collectionVexpress->getAwb(),//'1002504',                   
                'airwaybilno' => $collectionVexpress->getAwb(),//'1002504',                   
                'courier_code' => 'V-xpress', //$order->getIncrementId(),           //Vxpress 
                'order_no' => $order->getIncrementId(),               //MANDATORY FIELD (Given by Amiable)
                'order_date' => $date_order_created,//strtok($order->getCreatedAt(),' '),             //MANDATORY FIELD (Current Date)
                'consignee_first_name' => $shipping_address->getFirstname(),           //MANDATORY FIELD (Shipment Receiver First Name)
                'consignee_last_name' => $shipping_address->getLastname(),            //MANDATORY FIELD (Shipment Receiver Last Name)
                'consignee_address1' => stripslashes(str_replace( '/', '-', $shippingAddress )),//stripslashes($shippingAddress),  //MANDATORY FIELD (Shipment Receiver Address1, 50 character in a field)
                'consignee_address2' => stripslashes(str_replace( '/', '-', $shippingAddress )),    //$shipping_address->getCompany(),          //MANDATORY FIELD (Shipment Receiver Address2, 50 character in a field)
                'destination_city' => $shipping_address->getCity(),           //MANDATORY FIELD (Shipment Receiver City)
                'destination_pincode' => $shipping_address->getPostcode(),//$shipping_address->getPostcode(), //MANDATORY FIELD (Shipment Receiver PIN Code)
                'state' => $shipping_address->getRegion(),              //MANDATORY FIELD (Shipment Receiver State)    
                'consignee_country' => 'India',          //MANDATORY FIELD (India)
                'telephone1' => $shipping_address->getTelephone(), //MANDATORY FIELD (Shipment Receiver Mob. No.)
                'telephone2' => $shipping_address->getTelephone(),                        //  (Shipment Receiver Landline No. if available)
                'consignee_emailid' => $order->getCustomerEmail(),          //MANDATORY FIELD (Shipment Receiver email Id)
                'vendor_id' => '500201', //'500203',              //MANDATORY FIELD (500203) 
                'vendor_name' => 'AMIABLE ELECTRONICS PVT LTD',                //MANDATORY FIELD (AMIABLE ELECTRONICS PVT LTD)
                'subvendor_name' => substr($warehouseAddress[0], 0, 29) ,         //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                'subvendor_address1' => stripslashes(str_replace( '/', '-', $warehouseAddress[1])),         //MANDATORY FIELD (Pickup location wise address)
                'subvendor_address2' => stripslashes(str_replace( '/', '-', $warehouseAddress[2])),         //MANDATORY FIELD  (Same Address) 
                'subvendor_city' => $warehouseAddress[3],         //MANDATORY FIELD (CITY)
                'subvendor_pincode' => $warehouseAddress[4],          //MANDATORY FIELD (PIN Code)
                'subvendor_state' => $warehouseAddress[5],            //MANDATORY FIELD (State)
                'subvendor_country' => $warehouseAddress[6],          //MANDATORY FIELD (India) 
                'subvendor_phone1' => $warehouseAddress[8],           //MANDATORY FIELD (Mob. No)
                'subvendor_emailid' => $warehouseAddress[9],                //      (Email ID)
                'pay_type' => $pmt,//$order->getPayment()->getMethod(),               //MANDATORY FIELD     (Pre-Paid or COD) 
                'item_description' => substr(trim(htmlentities($item_description)),0,50),//$resultName,     //MANDATORY FIELD (Product Name)
                'qty' => (int)$invoice->getTotalQty(),                    //MANDATORY FIELD (1)
                'collectable_value' => $invoice->getGrandTotal(),          //MANDATORY FIELD (COD Value)
                'product_value' => $invoice->getGrandTotal(),              //MANDATORY FIELD (Product Cost)
                'actual_weight' => $weight,              //MANDATORY FIELD (Product Weight, only numeric i.e. 500 gms mention 0.5)
                'volumetric_weight' => '0',
                'length' => '0',
                'breadth' => '0',
                'height' => '0',
                'booking_mode' => 'Air', //$pmt,                       //    (Surface)
                'consignor_code' => 'CORVX-A0003',//'AMIABLE ELECTRONICS PVT LTD',                     //    (AMIABLE ELECTRONICS PVT LTD)
                'charge_weight' => $weight,
                'freight_paymode' => $pmt         //MANDATORY FIELD

            );

        }
        $newdataArray = array();
        //print_r($Parameters);die;
        $payload = json_encode($Parameters);
        //$payload = serialize($Parameters);
       // echo $params; //die;
        // $newparams = json_encode(array("NewDataSet"=>$Parameters));
        Mage::log($params,null,'vexpress_new.log',true);
        $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS,$params); 
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch); 
        //print_r($result);die;
        Mage::log($result,null,'vexpress_new.log',true);
        $responseSend = array();
        $response = (array) (json_decode($result));
       // print_r($response);die;
//response is array
        //$response['ModelState'] is json object with error
        //$response['Message'] is always there
       
        if (strpos($response['Message'], 'Successfully') !== false) {
            $responseSend = array(
                "error" => false,
                "awb"   =>  $collectionVexpress->getAwb(),
                "title"   =>  'V-Express',
                "destination"   => $shipping_address->getRegion()." ".$shipping_address->getCity(),
                "payload"   => $payload
            );
            
            $collectionVexpress->setStatus(1)->save(); 
        }else{
            $responseSend = array(
                "error" => true,
                "msg"   =>  $response['Message'] ." Payload : ".$payload
            );
        }

        /*$response1 = (array) $response['response'];
        
        if(array_key_exists('success', $response1)){
            $responseSend = array(
                "error" => false,
                "awb"   =>  $collectionVexpress->getAwb(),
                "title"   =>  'V-Express',
                "destination"   => $shipping_address->getRegion().$shipping_address->getCity()
            );
            
            $collectionVexpress->setStatus(1)->save(); 

        }else{
            $responseSend = array(
                "error" => true,
                "msg"   =>  $response['Message'] 
            );
        }*/
        //print_r($responseSend);die;
        return $responseSend;

    }

    public function bluedart($post)
    {
        $log_file_name  = "shipment".Mage::getModel('core/date')->date('Y-m-d').".log";
        $incrementId    = $post["incrementid"];
        $no_of_awbs     = $post["no_of_awbs"];
        $weight         = $post["weight"];
        $invoice_id     = $post["invoice_id"];

        if($incrementId){
            $order = Mage::getModel("sales/order")->loadByIncrementId($incrementId);
            $invoice = Mage::getModel("sales/order_invoice")->load($invoice_id);


            $shipping_address = $order->getShippingAddress();

            $customer = array(
                "CustomerName"      =>  $shipping_address->getPrefix()." ".$shipping_address->getFirstname()." ".$shipping_address->getLastname(),
                "ConsigneeAddress1" =>  implode($shipping_address->getStreet(), " "),
                "ConsigneeAddress2" =>  $shipping_address->getCity(),
                "ConsigneeAddress3" =>  $shipping_address->getRegion(),
                "CustomerPincode"   =>  $shipping_address->getPostcode(),
                "ConsigneeTelephone"   =>  $shipping_address->getTelephone(),
            );

            $request["customer"] = $customer;
			$request["total_charged"] = number_format((float)$invoice->getGrandTotal(),'2','.','');
            /**
             * type switch based on payment type
             */
            if($order->getPayment()->getMethodInstance()->getCode() == "cashondelivery") {
                $request["customerCode"] = Mage::getStoreConfig('shipping/bluedart/customercodecod');
                $request['Sender'] = Mage::getStoreConfig('shipping/bluedart/sender_cod');
                $request["SubProductCode"]		= 'C';
                
                if($order->getGrandTotal() >= 50000) {
					$request["total_due"] = 50000;
				} else {
					$request["total_due"] = number_format((float)$invoice->getGrandTotal(),'2','.','');
				}
            } else {
                $request["customerCode"] = Mage::getStoreConfig('shipping/bluedart/customercodeprepaid');
                $request['Sender'] = Mage::getStoreConfig('shipping/bluedart/sender');
                $request["total_due"]       = 0;
                $request["SubProductCode"]		= 'P';
            }

            /**
             *adding total due and total charged to request
             */
            //$request["total_due"]       = $order->getTotalDue();

            $result = array();
            /**
             * when order has invoices than it will create that number of AWB
             *
             * if order doesnot has invoice than AWB will be created on the basis of no of AWB Quiered from form.
             */
            if ($order->hasInvoices()) {
				
				$request["weight"] = $weight;
                $request["invoice"] =  Mage::getModel('sales/order_invoice')->load($invoice_id)->getIncrementId();
                $result[] = $this->getAwb($request);
				/*
                $invoices = array();
                foreach ($order->getInvoiceCollection() as $inv) {
                    if($inv->getIncrementId()){
                        $invoices[] = $inv->getIncrementId();
                    }
                }

                $request["weight"] = $weight / count($invoices); //equally distributing the weight into numbers of AWB
                
                foreach($invoices as $invoice){
                    $request["invoice"] = $invoice;

                    Mage::log("----- Request -----", 1, $log_file_name, true);
                    Mage::log($request, 1, $log_file_name, true);

                    $result[] = $this->getAwb($request);
                }*/
            }else{
                $request["invoice"] = $incrementId;
                $request["weight"] = $weight / $no_of_awbs;

                for($i=1;$i<=$no_of_awbs;$i++){
                    Mage::log("----- Request Not Invoice-----", 1, $log_file_name, true);
                    Mage::log($request, 1, $log_file_name, true);

                    $result[] = $this->getAwb($request);
                }
            }
            Mage::log("----- Result -----", 1, $log_file_name, true);
            Mage::log($result, 1, $log_file_name, true);
        }

        return $result;
    }

    protected function getAwb($request)
    {
        $DeclaredValue      = $request["total_charged"];
        $CollactableAmount  = $request["total_due"];

        $log_file_name  = "shipment".Mage::getModel('core/date')->date('Y-m-d').".log";

        $customer           =   $request["customer"];
        $weight             =   $request["weight"];
        $invoice            =   $request["invoice"];
        $shipment_comment   =   $request["shipment_comment"];

        $soap = new SoapClient('http://netconnect.bluedart.com/Ver1.7/ShippingAPI/WayBill/WayBillGeneration.svc?wsdl',
        //$soap = new SoapClient('http://netconnect.bluedart.com/Ver1.2/Demo/ShippingAPI/waybill/waybillGeneration.svc?wsdl',
            array(
                'trace'         => 1,
                'style'         => SOAP_DOCUMENT,
                'use'           => SOAP_LITERAL,
                'soap_version'  => SOAP_1_2
            ));

        //$soap->__setLocation("http://netconnect.bluedart.com/Ver1.2/Demo/ShippingAPI/waybill/waybillGeneration.svc");
        $soap->__setLocation("http://netconnect.bluedart.com/Ver1.7/ShippingAPI/WayBill/WayBillGeneration.svc");

        $soap->sendRequest = true;
        $soap->printRequest = false;
        $soap->formatXML = true;

        $actionHeader = new SoapHeader('http://www.w3.org/2005/08/addressing','Action','http://tempuri.org/IWayBillGeneration/GenerateWayBill',true);
        $soap->__setSoapHeaders($actionHeader);

        $Shipper=array(
            'OriginArea'        => Mage::getStoreConfig('shipping/bluedart/origincode'),
            //'CustomerCode'      => Mage::getStoreConfig('shipping/bluedart/customercode'),
            'CustomerCode'      => $request["customerCode"],
            'CustomerName'      => $request['Sender'],
            'CustomerAddress1'  => substr(Mage::getStoreConfig('shipping/bluedart/address1'), 0, 30),
            //'CustomerAddress1'  => str_replace(" ", "_", Mage::getStoreConfig('shipping/bluedart/address1')),
            'CustomerAddress2'  => substr(Mage::getStoreConfig('shipping/bluedart/address2'), 0, 30),
            //'CustomerAddress2'  => str_replace(" ", "_", Mage::getStoreConfig('shipping/bluedart/address2')),
            'CustomerAddress3'  => substr(Mage::getStoreConfig('shipping/bluedart/address3'),0, 30),
            //'CustomerAddress3'  => str_replace(" ", "_", Mage::getStoreConfig('shipping/bluedart/address3')),
            'CustomerPincode'   => Mage::getStoreConfig('shipping/bluedart/pickuppincode'),
            'CustomerTelephone' => Mage::getStoreConfig('shipping/bluedart/telephone'),
            'CustomerMobile'    => Mage::getStoreConfig('shipping/bluedart/mobile'),
            'CustomerEmailID'   => Mage::getStoreConfig('shipping/bluedart/email'),
            //'Sender'            => str_replace(" ", "_", Mage::getStoreConfig('shipping/bluedart/sender')),
            //'Sender'            => $request['Sender'],
            'Sender'            => '',
            'isToPayCustomer'   => 'false'
        );

        $Consignee=array(
            'ConsigneeName'     => $customer["CustomerName"],
            'ConsigneeAddress1' => substr($customer["ConsigneeAddress1"],0, 30),
            'ConsigneeAddress2' => substr($customer["ConsigneeAddress2"],0,30),
            'ConsigneeAddress3' => substr($customer["ConsigneeAddress3"], 0, 30),
            'ConsigneePincode'  => $customer["CustomerPincode"],
            'ConsigneeTelephone'=> '',
            'ConsigneeMobile'   => '99999999999',//$customer["ConsigneeTelephone"],
            'ConsigneeAttention'=> ''
        );
        $Dimensions=array(
            'Length' => 10,
            'Breadth' => 10,
            'Height' => 10,
            'Count' => 1
        );
        $Commodity=array(
            'CommodityDetail1' => 'A',
            'CommodityDetail2' => 'A',
            'CommodityDetail3' => 'A'
        );
        $Services=array(
            'ProductCode'           => 'A',
            'SubProductCode'        => $request["SubProductCode"],
            'ProductType'           => 'Dutiables',
            'PieceCount'            => 1,
            'ActualWeight'          => $weight,
            'InvoiceNo'             => $invoice,
            'SpecialInstruction'    => $shipment_comment,
            'DeclaredValue'         => $DeclaredValue,
            'CollectableAmount'     => $CollactableAmount,
            'CreditReferenceNo'     => "CR".Mage::getModel('core/date')->date('His').$invoice,
            'PickupDate'            => Mage::getModel('core/date')->date('Y-m-d'),
            'PickupTime'            => Mage::getModel('core/date')->date('Hi'),
            'Dimensions'            => $Dimensions,
            'Commodity'             => $Commodity
        );
        $Profile=array(
            'LoginID'   => Mage::getStoreConfig('shipping/bluedart/loginid'),
            'LicenceKey'=> Mage::getStoreConfig('shipping/bluedart/licencekey'),
            'Api_type'  => Mage::getStoreConfig('shipping/bluedart/apitype'),
            'Version'	=> '1.3'
        );
        $params = array(
            'Shipper'   => $Shipper,
            'Consignee' => $Consignee,
            'Services'  => $Services
        );

        $parameters = array(
            'Request' => $params,
            'Profile' => $Profile
        );
        Mage::log("----- Service Request -----", 1, $log_file_name, true);
        Mage::log($parameters, 1, $log_file_name, true);

        //return $parameters;
        $actionHeader = new SoapHeader('http://www.w3.org/2005/08/addressing','Action','http://tempuri.org/IWayBillGeneration/GenerateWayBill',true);

        try{
            $soap->__setSoapHeaders($actionHeader);
            $result = $soap->__soapCall('GenerateWayBill',array($parameters));
            mage::log($result,null,'sandeep.log');
        }catch (Exception $e){

            $response = array(
                "error" => false,
                "awb"   =>  $e->getMessage()
            );
            return $response;
        }


        Mage::log("----- Service Response -----", 1, $log_file_name, true);
        Mage::log($result->GenerateWayBillResult, 1, $log_file_name, true);

        $response = array();
        if($result->GenerateWayBillResult->IsError){
            $response = array(
                "error" => true,
                "msg"   =>  $result->GenerateWayBillResult->Status
            );
        }else{
            $response = array(
                "error" => false,
                "awb"   =>  $result->GenerateWayBillResult->AWBNo,
                "destination"   => $result->GenerateWayBillResult->DestinationArea."-".$result->GenerateWayBillResult->DestinationLocation
            );
        }
        return $response;
    }
}
