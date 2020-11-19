<?php
/**
 * Created by PhpStorm.
 * User: webwerks
 * Date: 2/10/14
 * Time: 11:13 AM
 */

class Neo_Shippinge_Model_Fedex extends Mage_Core_Model_Abstract
{

    public function getServices()
    {
        $services = array(
            "SO"    =>  "Standard Overnight",
            "XS"    =>  "Economy",
            "PO"    =>  "Priority overnight Night"
        );

        return $services;
    }
    /**
     * these calls all the avaliable services through Api
     * @param $post
     */
    public function init($post)
    {
        $path_to_wsdl = Mage::getModuleDir("", "Neo_Shippinge").DS."wsdl".DS."ValidationAvailabilityAndCommitmentService_v2.wsdl";

        ini_set("soap.wsdl_cache_enabled", "0");

        $client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

        $request['WebAuthenticationDetail'] = array(
            'UserCredential' => array(
                'Key' => $this->getProperty('key'),
                'Password' => $this->getProperty('password')
            )
        );
        $request['ClientDetail'] = array(
            'AccountNumber' => $this->getProperty('shipaccount'),
            'MeterNumber' => $this->getProperty('meter')
        );
        $request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Service Availability Request v5.1 using PHP ***');
        $request['Version'] = array(
            'ServiceId' => 'vacs',
            'Major' => '2',
            'Intermediate' => '0',
            'Minor' => '0'
        );
        $request['Origin'] = array(
            'PostalCode' => '400078', // Origin details
            'CountryCode' => 'IN'
        );
        $request['Destination'] = array(
            'PostalCode' => '416101', // Destination details
            'CountryCode' => 'IN'
        );
        $request['ShipDate'] = $this->getProperty('serviceshipdate');
        $request['CarrierCode'] = 'FDXE'; // valid codes FDXE-Express, FDXG-Ground, FDXC-Cargo, FXCC-Custom Critical and FXFR-Freight
//$request['Service'] = 'PRIORITY_OVERNIGHT'; // valid code STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
        $request['Packaging'] = 'YOUR_PACKAGING'; // valid code FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...



        try {
            /*
            if($this->setEndpoint('changeEndpoint')){
                $newLocation = $client->__setLocation($this->setEndpoint('endpoint'));
            }
*/
            $response = $client->serviceAvailability($request);

            if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR'){
                return $this->convertObjectsToArray($response->Options);
            }else{
                printError($client, $response);
            }
        } catch (SoapFault $exception) {
            echo $exception->getMessage();
        }
    }

    protected function setEndpoint($var)
    {
        if($var == 'changeEndpoint') Return false;
        if($var == 'endpoint') Return '416101';
    }

    protected function getProperty($var)
    {
        if($var == 'key') Return 'e641XGlcpE11yxZ7';
        if($var == 'password') Return '5d1eEdwlRxPSakU2IvEDX9RVH';
        if($var == 'shipaccount') Return '510087720';
        if($var == 'billaccount') Return '510087720';
        if($var == 'dutyaccount') Return '510087720';
        if($var == 'freightaccount') Return '510087720';
        if($var == 'trackaccount') Return '510087720';
        if($var == 'dutiesaccount') Return '510087720';
        if($var == 'importeraccount') Return '510087720';
        if($var == 'brokeraccount') Return '510087720';
        if($var == 'distributionaccount') Return '510087720';
        if($var == 'locationid') Return 'PLBA';
        if($var == 'printlabels') Return false;
        if($var == 'printdocuments') Return true;
        if($var == 'packagecount') Return '4';

        if($var == 'meter') Return '118650290';

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
                'City' => 'Mumbai',
                'StateOrProvinceCode' => 'MH',
                'PostalCode' => '400066',
                'CountryCode' => 'IN',
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
                'City' => 'Mumbai',
                'StateOrProvinceCode' => 'MH',
                'PostalCode' => '400001',
                'CountryCode' => 'IN',
                'Residential' => 1
            )
        );

        if($var == 'address1') Return array(
            'StreetLines' => array('10 Fed Ex Pkwy'),
            'City' => 'Mumbai',
            'StateOrProvinceCode' => 'MH',
            'PostalCode' => '400066',
            'CountryCode' => 'IN'
        );
        if($var == 'address2') Return array(
            'StreetLines' => array('13450 Farmcrest Ct'),
            'City' => 'Mumbai',
            'StateOrProvinceCode' => 'MH',
            'PostalCode' => '400001',
            'CountryCode' => 'IN'
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

    public function convertObjectsToArray($objs){
        $items = array();
        if(!is_array($objs))
            $items[] = $this->convertObjectToArray($objs);
        else
            foreach($objs as $obj){
                $items[] =  $this->convertObjectToArray($obj);
            }

        return $items;
    }

    public function convertObjectToArray($obj){
        $obj =  get_object_vars($obj);
        $result = array();
        foreach($obj as $key=>$value){
            $result[strtolower($key)] = $value;
        }
        return $result;
    }

    public function bluedart($post)
    {
        $log_file_name  = "shipment".Mage::getModel('core/date')->date('Y-m-d').".log";
        $incrementId    = $post["incrementid"];
        $no_of_awbs     = $post["no_of_awbs"];
        $weight         = $post["weight"];

        if($incrementId){
            $order = Mage::getModel("sales/order")->loadByIncrementId($incrementId);


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

            /**
             * type switch based on payment type
             */
            if($order->getPayment()->getMethodInstance()->getCode() == "cashondelivery"){

            }else{

            }

            $result = array();
            /**
             * when order has invoices than it will create that number of AWB
             *
             * if order doesnot has invoice than AWB will be created on the basis of no of AWB Quiered from form.
             */
            if ($order->hasInvoices()) {
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
                }
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
        $DeclaredValue = "23";
        $CollactableAmount = "34234";

        $log_file_name  = "shipment".Mage::getModel('core/date')->date('Y-m-d').".log";

        $customer           =   $request["customer"];
        $weight             =   $request["weight"];
        $invoice            =   $request["invoice"];
        $shipment_comment   =   $request["shipment_comment"];

        $soap = new SoapClient('http://netconnect.bluedart.com/Ver1.2/Demo/ShippingAPI/WayBill/WayBillGeneration.svc?wsdl',
            array(
                'trace'         => 1,
                'style'         => SOAP_DOCUMENT,
                'use'           => SOAP_LITERAL,
                'soap_version'  => SOAP_1_2
            ));

        $soap->__setLocation("http://netconnect.bluedart.com/Ver1.2/Demo/ShippingAPI/WayBill/WayBillGeneration.svc");

        $soap->sendRequest = true;
        $soap->printRequest = false;
        $soap->formatXML = true;

        $actionHeader = new SoapHeader('http://www.w3.org/2005/08/addressing','Action','http://tempuri.org/IWayBillGeneration/GenerateWayBill',true);
        $soap->__setSoapHeaders($actionHeader);

        $Shipper=array(
            'OriginArea'        => Mage::getStoreConfig('shipping/bluedart/origincode'),
            'CustomerCode'      => Mage::getStoreConfig('shipping/bluedart/customercode'),
            'CustomerName'      => Mage::getStoreConfig('shipping/bluedart/sender'),
            'CustomerAddress1'  => str_replace(" ", "_", Mage::getStoreConfig('shipping/bluedart/address1')),
            'CustomerAddress2'  => str_replace(" ", "_", Mage::getStoreConfig('shipping/bluedart/address2')),
            'CustomerAddress3'  => str_replace(" ", "_", Mage::getStoreConfig('shipping/bluedart/address3')),
            'CustomerPincode'   => Mage::getStoreConfig('shipping/bluedart/pickuppincode'),
            'CustomerTelephone' => Mage::getStoreConfig('shipping/bluedart/telephone'),
            'CustomerMobile'    => Mage::getStoreConfig('shipping/bluedart/mobile'),
            'CustomerEmailID'   => Mage::getStoreConfig('shipping/bluedart/email'),
            'Sender'            => str_replace(" ", "_", Mage::getStoreConfig('shipping/bluedart/sender')),
            'isToPayCustomer'   => 'false'
        );

        $Consignee=array(
            'ConsigneeName'     => $customer["CustomerName"],
            'ConsigneeAddress1' => $customer["ConsigneeAddress1"],
            'ConsigneeAddress2' => $customer["ConsigneeAddress2"],
            'ConsigneeAddress3' => $customer["ConsigneeAddress3"],
            'ConsigneePincode'  => $customer["CustomerPincode"],
            'ConsigneeTelephone'=> '',
            'ConsigneeMobile'   => $customer["ConsigneeTelephone"],
            'ConsigneeAttention'=> ''
        );
        $Dimensions=array(
            'Length' => 0,
            'Breadth' => 0,
            'Height' => 0,
            'Count' => 1
        );
        $Commodity=array(
            'CommodityDetail1' => '',
            'CommodityDetail2' => '',
            'CommodityDetail3' => ''
        );
        $Services=array(
            'ProductCode'           => 'A',
            'ProductType'           => 'Dutiables',
            'PieceCount'            => 1,
            'ActualWeight'          => $weight,
            'InvoiceNo'             => $invoice,
            'SpecialInstruction'    => $shipment_comment,
            'DeclaredValue'         => $DeclaredValue,
            'CollactableAmount'     => $CollactableAmount,
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
                "awb"   =>  $result->GenerateWayBillResult->AWBNo
            );
        }
        return $response;
    }
}
