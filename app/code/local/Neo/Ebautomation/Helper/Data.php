<?php
class Neo_Ebautomation_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getWarehouseAddress($orderId,$invoice_id)
		{
			
            $stockLocations = Mage::getModel('stocklocation/location')->getCollection()->addFieldToFilter('order_id',$orderId);
       
	        foreach ($stockLocations as $stocklocation) {
	            $warehouse[$stocklocation->getInvoiceId()] = $stocklocation->getStockLocation();
	        }
	       

	        $stockLocationModel['stock_location'] = $warehouse[$invoice_id];
	        

            $warehouseAddress = array();

            if($stockLocationModel['stock_location']=='Nerul Warehouse' || $stockLocationModel['stock_location']=='Kurla Warehouse' || $stockLocationModel['stock_location']=='Bhiwandi Warehouse' || $stockLocationModel['stock_location']=='Andheri HO' || $stockLocationModel['stock_location']=='Bhiwandi EB-REPAIR CENTRE' || $stockLocationModel['stock_location']=='Bhiwandi EB-SERVICE CENTRE' || $stockLocationModel['stock_location']=='Bhiwandi EB-Open Units' || $stockLocationModel['stock_location']=='Bhiwandi EB-Warranty stock' || $stockLocationModel['stock_location']=='Bhiwandi EB-Refurbished' || $stockLocationModel['stock_location']=='Amazon BB – ASIS-Bhiwandi'|| $stockLocationModel['stock_location']=='Amazon BB – QC-Bhiwandi'|| $stockLocationModel['stock_location']=='FlipKart Prexo – ASIS- Bhiwandi'|| $stockLocationModel['stock_location']=='FlipKart Prexo – QC-Bhiwandi'){
                /*
                $warehouseAddress[0] = 'AMIABLE ELECTRONICS PVT LTD';        //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                $warehouseAddress[1] = 'C/O PROCONNECT SUPPLY CHAIN SOLUTIONS';       //MANDATORY FIELD (Pickup location wise address)
                $warehouseAddress[2] = 'LIMITED,BUL NO. D, GALA NO 14 15';      //MANDATORY FIELD  (Same Address) 
                $warehouseAddress[3] = 'Mumbai';      //MANDATORY FIELD (CITY)
                $warehouseAddress[4] = '421302';       //MANDATORY FIELD (PIN Code)
                $warehouseAddress[5] = 'Maharashtra';      //MANDATORY FIELD (State)
                $warehouseAddress[6] = 'India';      //MANDATORY FIELD (India)
                $warehouseAddress[7] = '9325887623';    //phone number
                $warehouseAddress[8] = '9325887623';     //MANDATORY FIELD (Mob. No)
                $warehouseAddress[9] = 'gautam.jadhav@proconnect.co.in';      //      (Email ID)*/
                $warehouseAddress[0] = 'AMIABLE ELECTRONICS PVT LTD';        //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                $warehouseAddress[1] = 'C/o DHL supply chain Pvt Ltd';       //MANDATORY FIELD (Pickup location wise address)
                $warehouseAddress[2] = 'Building no F, Nasik Road Sarvali Goan Thakur Pada';      //MANDATORY FIELD  (Same Address) 
                $warehouseAddress[3] = 'Bhiwandi';      //MANDATORY FIELD (CITY)
                $warehouseAddress[4] = '421302';       //MANDATORY FIELD (PIN Code)
                $warehouseAddress[5] = 'Maharashtra';      //MANDATORY FIELD (State)
                $warehouseAddress[6] = 'India';      //MANDATORY FIELD (India)
                $warehouseAddress[7] = '7276829798';    //phone number
                $warehouseAddress[8] = '7276829798';     //MANDATORY FIELD (Mob. No)
                $warehouseAddress[9] = 'yogesh.lokhande@dhl.com';      //      (Email ID)

            }elseif($stockLocationModel['stock_location']=='Tamilnadu Warehouse'){

                    $warehouseAddress[0] = 'AMIABLE ELECTRONICS PRIVATE LIMITED';        //MANDATORY FIELD (PROCONNECT SUPPLY CHAIN SOLUTIONS LIMITED)
                    $warehouseAddress[1] = 'Kizhmuthalmpedu, Panapakkam Village';       //MANDATORY FIELD (Pickup location wise address)
                    $warehouseAddress[2] = 'Gummindipoondi Taluk Tamilnadu';      //MANDATORY FIELD  (Same Address) 
                    $warehouseAddress[3] = 'Thiruvallur';      //MANDATORY FIELD (CITY)
                    $warehouseAddress[4] = '601201';       //MANDATORY FIELD (PIN Code)
                    $warehouseAddress[5] = 'Tamilnadu';      //MANDATORY FIELD (State)
                    $warehouseAddress[6] = 'India';      //MANDATORY FIELD (India)
                    $warehouseAddress[7] = '9380141516';    //phone number  
                    $warehouseAddress[8] = '9380141516';     //MANDATORY FIELD (Mob. No)    
                    $warehouseAddress[9] = 'venkatesan.madhaiyan@proconnect.co.in';      //      (Email ID)      
                    
            }
            else{
                     
            } 

            return $warehouseAddress;
		}

    /*
    @author : Sonali Kosrabe
    @date : 21-09-2017
    @purpose : To return state code of state
    */


    public function stateCodeMapping($code)
    {
        if($code=='IN-AP'){
            return 'ANP';
        }elseif($code=='IN-AR'){
            return 'ANP';
        }elseif($code=='IN-AS'){
            return 'AS';
        }elseif($code=='IN-BR'){
            return 'BIH';
        }elseif($code=='IN-CH'){
            return 'CH';
        }elseif($code=='IN-CG'){
            return 'CG';
        }elseif($code=='IN-DN'){
            return 'DAD';
        }elseif($code=='IN-DD'){
            return 'DD';
        }elseif($code=='IN-DL'){
            return 'ND';
        }elseif($code=='IN-GA'){
            return 'GA';
        }elseif($code=='IN-GJ'){
            return 'GUJ';
        }elseif($code=='IN-HR'){
            return 'HR';
        }elseif($code=='IN-HP'){
            return 'HP';
        }elseif($code=='IN-JK'){
            return 'JK';
        }elseif($code=='IN-JH'){
            return 'JH';
        }elseif($code=='IN-KA'){
            return 'KAR';
        }elseif($code=='IN-KL'){
            return 'KER';
        }elseif($code=='IN-LD'){
            return 'LA';
        }elseif($code=='IN-MP'){
            return 'MP';
        }elseif($code=='IN-MH'){
            return "MAH";
        }elseif($code=='IN-MN'){
            return 'MAN';
        }elseif($code=='IN-ML'){
            return 'MEG';
        }elseif($code=='IN-MZ'){
            return 'MIZ';
        }elseif($code=='IN-NL'){
            return 'NAG';
        }elseif($code=='IN-OR'){
            return 'ORI';
        }elseif($code=='IN-PY'){
            return 'PON';
        }elseif($code=='IN-PB'){
            return 'PUN';
        }elseif($code=='IN-RJ'){
            return 'RAJ';
        }elseif($code=='IN-SK'){
            return 'SIK';
        }elseif($code=='IN-TN'){
            return 'TN';
        }elseif($code=='IN-TR'){
            return 'TRI';
        }elseif($code=='IN-UP'){
            return 'UP';
        }elseif($code=='UTTAR'){
            return 'UT';
        }elseif($code=='IN-WB'){
            return 'WB';
        }elseif($code=='Andaman Nicobar'){
            return 'AN';
        }elseif($code=='Dadra Nagar Haveli'){
            return 'DAD';
        }elseif($code=='Daman Diu'){
            return 'DD';
        }elseif($code=='Pondicherry'){
            return 'PON';
        }elseif($code=='IN-TS'){
            return 'TL';
        }elseif($code=='IN-UK'){
            return 'UT';   //// Changed from UK to UT as UT is mapped in Navision for tax calculation
        }
    }


    /*
    @author : Sonali Kosrabe
    @date : 30-10-2017
    @purpose : SOAP call function
    */
    public function callNavisionSOAPApi($apiUrl = NULL, $action = NULL, $params = NULL){
        ini_set( 'soap.wsdl_cache_enabled', '0' );  
        try{
            $username = Mage::getStoreConfig('ebautomation/ebautomation_credentials/username');
            $password = Mage::getStoreConfig('ebautomation/ebautomation_credentials/password');
            //echo "credentials => ".$username.":".$password."<br><br>";
            $headers = array( 
                  'Method: POST', 
                  'Connection: Keep-Alive', 
                  'User-Agent: PHP-SOAP-CURL', 
                  'Content-Type: text/xml; charset=utf-8', 
                  'SOAPAction: "'.$action.'"', 
            ); 
            $ch = curl_init($apiUrl); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
            curl_setopt($ch, CURLOPT_POST, true ); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params); 
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); 
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM); 
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password"); 
            $soapResponse = curl_exec($ch); 

            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soapResponse);
            $xml = simplexml_load_string($response);

            $result = (array) $xml;
            return $result;
        }catch(Exception $e){
            return json_encode(array('status' => 'error', 'message' => $ex->getMessage()));
        }

    }

    /*
    @author : Sonali Kosrabe
    @date : 21-09-2017
    @purpose : To get order Invoice details
    */
    public function getOrderInvoiceDetails($orderItemId){
        $items = Mage::getSingleton('sales/order_item')->getCollection();//->setOrderFilter($order);
        $items->getSelect()->joinLeft(
            array('oii' => $items->getTable('sales/invoice_item')),
            'main_table.item_id = oii.order_item_id',
            array('invoice_id' => 'parent_id')
        );
        $items->addFieldToFilter('oii.order_item_id',$orderItemId);
        $items = $items->getData();
        
        $invoice_id = $items[0]['invoice_id'];
        $invoice = Mage::getModel('sales/order_invoice')->load($invoice_id);
        return $invoice;

    }
        
}
	 