<?php
class Neo_Shippinge_Model_Fedex_Shipment extends Neo_Shippinge_Model_Fedex
{	
	public function fadex($post) 
	{
		$log_file_name  = "shipment".Mage::getModel('core/date')->date('Y-m-d').".log";
        $incrementId    = $post["incrementid"];
        $no_of_awbs     = $post["no_of_awbs"];
        $weight         = (float)$post["weight"];
        $invoice_id     = $post["invoice_id"];
        $service_type   = $post["service_type"];
        
        $request = array();
		if($incrementId) 
		{
			$order = Mage::getModel("sales/order")->loadByIncrementId($incrementId);
			$invoice = Mage::getModel('sales/order_invoice')->load($invoice_id);
			$shipping_address = $order->getShippingAddress();
            $customer = $order->getCustomer();
            $amount = (int)$invoice->getGrandTotal();
			$recipient = array(
				'Contact' => array(
					'PersonName' => $shipping_address->getPrefix()." ".$shipping_address->getFirstname()." ".$shipping_address->getLastname(),
					'CompanyName' => $shipping_address->getCompany(),
					'PhoneNumber' => $shipping_address->getTelephone()
				),
				'Address' => array(
					'StreetLines' => $shipping_address->getStreet(),
					'City' => $shipping_address->getCity(),
					'StateOrProvinceCode' => $shipping_address->getRegion(),
					'PostalCode' => $shipping_address->getPostcode(),
					'CountryCode' => $shipping_address->getCountryId(),
					'Residential' => true
				)
			);
			
			$shipper = array(
					'Contact' => array(
						'PersonName' => 'Electronics Bazaar',
						'CompanyName' => 'Electronics Bazaar',
						'PhoneNumber' => '18002660786'
					),
					'Address' => array(
						'StreetLines' => array('Building No.D, Gala NO.4/15', 'Harihar Corporation, Dapode'),
						'City' => 'Bhiwandi',
						'StateOrProvinceCode' => 'Maharashtra',
						'PostalCode' => '421302',
						'CountryCode' => 'IN'
					)
			);

			$specialServices = array(
				'CodDetail' => array(
					'CodCollectionAmount' => array(
						'Currency' => 'INR', 
						'Amount' => $amount
					),
					'CollectionType' => 'CASH',// ANY, GUARANTEED_FUNDS
				),
				'SpecialServiceTypes' => array('COD')
			);
			$request['TotalWeight'] = array('Value' => $weight,'Units' => 'KG');
			$purpose = 'SOLD';
			if($service_type == 'PRIORITY_OVERNIGHT') {
				$purpose = 'NOT_SOLD';
			}
			
			$request['Recipient'] = $recipient;
			$request['Shipper'] = $shipper;
			$request['ServiceType'] = $service_type;
			$request['CustomsClearanceDetail'] = $this->addCustomClearanceDetail($amount, $weight, $purpose);
			$request['PackageLineItem1'] = $this->addPackageLineItem1($amount, $weight);
			$request['image_name'] = 'order_'.$incrementId.'_shipment.png';
            $request['SpecialServicesRequested'] = $specialServices;
			$RequestedShipment = array(
				'ShipTimestamp' => date('c'),
				'DropoffType' => 'REGULAR_PICKUP', // valid values REGULAR_PICKUP, REQUEST_COURIER, DROP_BOX, BUSINESS_SERVICE_CENTER and STATION
				'ServiceType' => $request['ServiceType'], // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
				'PackagingType' => 'YOUR_PACKAGING', // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
				'TotalWeight' => $request['TotalWeight'],
				'Shipper' => $request['Shipper'],
				'Recipient' => $request['Recipient'],
				'ShippingChargesPayment' => $this->addShippingChargesPayment(),
				'CustomsClearanceDetail' => $request['CustomsClearanceDetail'],
				'LabelSpecification' => $this->addLabelSpecification(),
				'PackageCount' => 1,
				'RequestedPackageLineItems' => array(
					'0' => $request['PackageLineItem1']
				)
			);
			$request['payment_method'] = $order->getPayment()->getMethodInstance()->getCode();
			if($request['payment_method'] == "cashondelivery") {
				$RequestedShipment['SpecialServicesRequested'] = $request['SpecialServicesRequested'];
			}
			$request['RequestedShipment'] = $RequestedShipment;

			return $this->CreateShipment($request);
		}
	}
	
    public function CreateShipment($data)
    {
        //The WSDL is not included with the sample code.
//Please include and reference in $path_to_wsdl variable.
        //$path_to_wsdl = "../../../wsdl/ShipService_v15.wsdl";
        $path_to_wsdl = Mage::getModuleDir("", "Neo_Shippinge").DS."wsdl".DS."ShipService_v15.wsdl";

        //define('SHIP_LABEL', 'shipexpresslabel.pdf');  // PNG label file. Change to file-extension .pdf for creating a PDF label (e.g. shiplabel.pdf)
        define('SHIP_LABEL', $data['image_name']);  // PNG label file. Change to file-extension .pdf for creating a PDF label (e.g. shiplabel.pdf)
        define('SHIP_CODLABEL', 'CODexpressreturnlabel.pdf');  // PNG label file. Change to file-extension .pdf for creating a PDF label (e.g. CODexpressreturnlabel.pdf)

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
        $request['TransactionDetail'] = array('CustomerTransactionId' => '*** Intra India Shipping Request v15 using PHP ***');
        $request['Version'] = array(
            'ServiceId' => 'ship',
            'Major' => '15',
            'Intermediate' => '0',
            'Minor' => '0'
        );
        $request['RequestedShipment'] = $data['RequestedShipment'];
        /*$request['RequestedShipment'] = array(
            'ShipTimestamp' => date('c'),
            'DropoffType' => 'REGULAR_PICKUP', // valid values REGULAR_PICKUP, REQUEST_COURIER, DROP_BOX, BUSINESS_SERVICE_CENTER and STATION
            'ServiceType' => $data['ServiceType'], // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
            'PackagingType' => 'YOUR_PACKAGING', // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
            'TotalWeight' => $data['TotalWeight'],
            'Shipper' => $data['Shipper'],
            'Recipient' => $data['Recipient'],
            'ShippingChargesPayment' => $this->addShippingChargesPayment(),
            'SpecialServicesRequested' => $data['SpecialServicesRequested'],
            'CustomsClearanceDetail' => $data['CustomsClearanceDetail'],
            'LabelSpecification' => $this->addLabelSpecification(),
            'PackageCount' => 1,
            'RequestedPackageLineItems' => array(
                '0' => $data['PackageLineItem1']
            )
        );
*/
        try {
            if($this->setEndpoint('changeEndpoint')) {
                $newLocation = $client->__setLocation($this->setEndpoint('endpoint'));
            }

Mage::log($request,null,'bhargav.log',true);
            $response = $client->processShipment($request);  // FedEx web service invocation
Mage::log($response,null,'bhargav.log',true);
            if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
                $file_path = Mage::getBaseDir('var').'/fadex_shipment/';
                if($data['payment_method'] == 'cashondelivery') {
					$fp = fopen($file_path.'AssociatedShipments_'.SHIP_LABEL, 'wb');
					fwrite($fp, $response->CompletedShipmentDetail->AssociatedShipments->Label->Parts->Image);
					fclose($fp);
                }
                
                $fp = fopen($file_path.'CompletedPackageDetails_'.SHIP_LABEL, 'wb');
                fwrite($fp, $response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image);
                fclose($fp);
                $result = array();
                $tracking_number = $response->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds->TrackingNumber;
                $DestinationLocationId = $response->CompletedShipmentDetail->OperationalDetail->DestinationLocationId;
                $result = array();
                $result[0] = array(
					"error" => false,
					"awb"   =>  $tracking_number,
					"destination"   => $DestinationLocationId
				);
                return $result;
            }else{
				Mage::log($response,null,'bhargav.log');
				$result = array();
                $result[0] = array(
					"error" => true,
					"message" => $response->Notifications->Message
				);
				return $result;
            }
        } catch (SoapFault $exception) {
			Mage::log($exception->getMessage(),null,'bhargav.log');
        }
    }

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

	function addShippingChargesPayment(){
		$shippingChargesPayment = array(
			'PaymentType' => 'SENDER',
			'Payor' => array(
				'ResponsibleParty' => array(
					'AccountNumber' => $this->getProperty('billaccount'),
					'Contact' => null,
					'Address' => array('CountryCode' => 'IN')
				)
			)
		);
		return $shippingChargesPayment;
	}
	function addLabelSpecification(){
		$labelSpecification = array(
			'LabelFormatType' => 'COMMON2D', // valid values COMMON2D, LABEL_DATA_ONLY
			'ImageType' => 'PNG',  // valid values DPL, EPL2, PDF, ZPLII and PNG
			'LabelStockType' => 'PAPER_8.5X11_TOP_HALF_LABEL'
		);
		return $labelSpecification;
	}
	
	function addCustomClearanceDetail($amount, $weight, $purpose){
		$customerClearanceDetail = array(
			'DutiesPayment' => array(
				'PaymentType' => 'SENDER', // valid values RECIPIENT, SENDER and THIRD_PARTY
				'Payor' => array(
					'ResponsibleParty' => array(
						'AccountNumber' => $this->getProperty('dutyaccount'),
						'Contact' => null,
						'Address' => array(
							'CountryCode' => 'IN'
						)
					)
				)
			),
			'DocumentContent' => 'NON_DOCUMENTS',                                                                                            
			'CustomsValue' => array(
				'Currency' => 'INR', 
				'Amount' => $amount,
			),
			'FreightOnValue' => 'OWN_RISK',
			'CommercialInvoice' => array(
				'Purpose' => $purpose,
				'CustomerReferences' => array(
					'CustomerReferenceType' => 'CUSTOMER_REFERENCE',
					'Value' => '1234'
				)
			),
			'Commodities' => array(
				'NumberOfPieces' => 1,
				'Description' => 'Electronics Items',
				'CountryOfManufacture' => 'IN',
				'Weight' => array(
					'Units' => 'KG', 
					'Value' => $weight
				),
				'Quantity' => 1,
				'QuantityUnits' => 'EA',
				'UnitPrice' => array(
					'Currency' => 'INR', 
					'Amount' => $amount
				),
				'CustomsValue' => array(
					'Currency' => 'INR', 
					'Amount' => $amount
				)
			)
		);
		return $customerClearanceDetail;
	}
	function addPackageLineItem1($amount, $weight){
		$packageLineItem = array(
			'SequenceNumber'=>1,
			'GroupPackageCount'=>1,
			'InsuredValue' => array(
				'Amount' => $amount, 
				'Currency' => 'INR'
			),
			'Weight' => array(
				'Value' => $weight,
				'Units' => 'KG'
			),
			'CustomerReferences' => array(
				'CustomerReferenceType' => 'CUSTOMER_REFERENCE', // valid values CUSTOMER_REFERENCE, INVOICE_NUMBER, P_O_NUMBER and SHIPMENT_INTEGRITY
				'Value' => 'BILL D/T: SHIPPER'
			)
		);
		return $packageLineItem;
	}
}
?>
