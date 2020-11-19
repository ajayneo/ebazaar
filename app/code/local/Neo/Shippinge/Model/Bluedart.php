<?php
/**
 * Created by PhpStorm.
 * User: webwerks
 * Date: 2/10/14
 * Time: 11:13 AM
 */

class Neo_Shippinge_Model_Bluedart extends Mage_Core_Model_Abstract
{
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
             //print_r($shipping_address->getData());die;
            $customer = array(
                "CustomerName"      =>  $shipping_address->getPrefix()." ".$shipping_address->getFirstname()." ".$shipping_address->getLastname(),
                "ConsigneeAddress1" =>  implode($shipping_address->getStreet(), " "),
                "ConsigneeAddress2" =>  $shipping_address->getCity(),
                "ConsigneeAddress3" =>  $shipping_address->getRegion(),
                "CustomerPincode"   =>  $shipping_address->getPostcode(),
                "ConsigneeTelephone"   =>  $shipping_address->getTelephone(),
                "ConsigneeMobile" => $shipping_address->getFax(), // added by pradeep sanku as mobile number was coming 99999999999
            );

                      
            $request["customer"] = $customer;
            //$request["total_charged"] = 49999; this code is added to make under value of the invoice because bluedart is not allowing the above 50000 even it is prepaid. msg was "Declare value should be less than 50000"
			$request["total_charged"] = number_format((float)$invoice->getGrandTotal(),'2','.','');
            /**
             * type switch based on payment type
             */
            if($order->getPayment()->getMethodInstance()->getCode() == "cashondelivery") {
                $request["customerCode"] = Mage::getStoreConfig('shipping/bluedart/customercodecod');
                $request['Sender'] = Mage::getStoreConfig('shipping/bluedart/sender_cod');
                $request["SubProductCode"]		= 'C';
                
                if($order->getGrandTotal() >= 50000) {
                    $request["total_due"] = $order->getBaseGrandTotal();
					//$request["total_due"] = 50000;
				} else {
					$request["total_due"] = number_format((float)$invoice->getGrandTotal(),'2','.','');
				}
            } else {
                $request["customerCode"] = Mage::getStoreConfig('shipping/bluedart/customercodeprepaid');
                $request['Sender'] = Mage::getStoreConfig('shipping/bluedart/sender');
                $request["total_due"]       = 0;
                if($order->getGrandTotal() >= 200000) {
                    $request["SubProductCode"]		= '';
                }else{
                    $request["SubProductCode"]      = 'P';
                }
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
        //print_r($result);die;
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

        //ini_set('default_socket_timeout', 600);

        //$soap = new SoapClient('http://netconnect.bluedart.com/Ver1.2/ShippingAPI/WayBill/WayBillGeneration.svc?wsdl',  // commented by pradeep sanku on sept 4th 2015 (it was live and working and sometimes was giving issue and added below new url)
        //$soap = new SoapClient('https://netconnect.bluedart.com/Ver1.7/ShippingAPI/Waybill/WayBillGeneration.svc?wsdl',
        $soap = new SoapClient('http://netconnect.bluedart.com/Ver1.7/ShippingAPI/WayBill/WayBillGeneration.svc?wsdl',
        //$soap = new SoapClient('http://netconnect.bluedart.com/Ver1.2/Demo/ShippingAPI/waybill/waybillGeneration.svc?wsdl', 

            array(
                'trace'         => 1,
                'style'         => SOAP_DOCUMENT,
                'use'           => SOAP_LITERAL,
                'soap_version'  => SOAP_1_2
            ));

        //$soap->__setLocation("http://netconnect.bluedart.com/Ver1.2/Demo/ShippingAPI/waybill/waybillGeneration.svc");
        //$soap->__setLocation("http://netconnect.bluedart.com/Ver1.2/ShippingAPI/WayBill/WayBillGeneration.svc");
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
            'ConsigneeName'     => substr($customer["CustomerName"],0,30),
            'ConsigneeAddress1' => substr($customer["ConsigneeAddress1"],0, 30),
            'ConsigneeAddress2' => substr($customer["ConsigneeAddress1"],30,30),
            'ConsigneeAddress3' => substr($customer["ConsigneeAddress2"],0,30).', '.substr($customer["ConsigneeAddress3"], 0, 30),
            'ConsigneePincode'  => $customer["CustomerPincode"],
            'ConsigneeTelephone'=> $customer['ConsigneeTelephone'],
            'ConsigneeMobile'   => $customer["ConsigneeMobile"], //'99999999999',//$customer["ConsigneeTelephone"],
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
        //E- Groubd
        //A- Air
        $Services=array(
            'ProductCode'           => 'E',
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

       
        $payload = json_encode($parameters);


        //return $parameters;
        $actionHeader = new SoapHeader('http://www.w3.org/2005/08/addressing','Action','http://tempuri.org/IWayBillGeneration/GenerateWayBill',true);

        try{
            $soap->__setSoapHeaders($actionHeader);
            $result = $soap->__soapCall('GenerateWayBill',array($parameters));
        }catch (Exception $e){

            $response = array(
                "error" => false,
                "awb"   =>  $e->getMessage()." Payload : ".$payload
            );

            return $response;
        }


        Mage::log("----- Service Response -----", 1, $log_file_name, true);
        Mage::log($result->GenerateWayBillResult, 1, $log_file_name, true);

        $response = array();
        /*echo "<pre>";
        print_r($result);die;*/
        if($result->GenerateWayBillResult->IsError){
            $response = array(
                "error" => true,
                "msg"   =>  $result->GenerateWayBillResult->Status->WayBillGenerationStatus->StatusInformation." Payload : ".$payload
            );
        }else{
            $response = array(
                "error" => false,
                "awb"   =>  $result->GenerateWayBillResult->AWBNo,
                "destination"   => $result->GenerateWayBillResult->DestinationArea."-".$result->GenerateWayBillResult->DestinationLocation,
                'payload' => $payload
            );
        }

        //echo $payload;
        //print_r($response);


        //die;

        return $response;
    }
}
