<?php 
libxml_disable_entity_loader(false);
ini_set("default_socket_timeout", 6000);
	$repcode = 'AFF__4401569827';
    $apiUrl = 'http://111.119.217.101:7047/DynamicsNAV71/WS/Amiable%20Electronics%20Pvt.%20Ltd./Page/Rapcode_Wise_Credit_Limit';

	//$apiUrl = 'http://111.119.217.101:8013/EBWebAutomation/WS/Amiable%20Electronics%20Pvt.%20Ltd./Page/Rapcode';
	$action = 'Read';
  	$params = '<x:Envelope xmlns:x="http://schemas.xmlsoap.org/soap/envelope/" xmlns:rap="urn:microsoft-dynamics-schemas/page/rapcode_wise_credit_limit">
    <x:Header/>
    <x:Body>
        <rap:Read>
            <rap:Repcode>AFF__4401569827</rap:Repcode>
        </rap:Read>
    </x:Body>
</x:Envelope>';

      // $apiUrl = Mage::getStoreConfig('ebautomation/ebautomation_credentials/nav_credit_limit_url');
    

    		$username = 'EBAPI';
            $password = 'eb$api*#';
            // $username = 'nav1';
            // $password = 'Win$123';
            //echo "credentials => ".$username.":".$password."<br><br>";
            $headers = array( 
                  'Method: POST', 
                  'Connection: Keep-Alive', 
                  'User-Agent: PHP-SOAP-CURL', 
                  'Content-Type: text/xml; charset=utf-8', 
                  'SOAPAction: "'.$action.'"', 
            );
        
        try{

            $ch = curl_init($apiUrl); 

            if (FALSE === $ch) throw new Exception('failed to initialize');

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
            curl_setopt($ch, CURLOPT_POST, true ); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params); 
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); 
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM); 
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password"); 
            $soapResponse = curl_exec($ch); 

            if (FALSE === $soapResponse)
    	    throw new Exception(curl_error($ch), curl_errno($ch));


            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $soapResponse);
            // var_dump($response);
            

            $xml = simplexml_load_string($response);

            $result = (array) $xml;
            // echo "<pre>"; print_r($result);
            if($result['sBody']->sFault){
                $response = $result['sBody']->sFault->faultstring;
                //$response = $customerData->getNavMapDetails()."\r\n#".Date("Y-m-d").": ".$response;//die;
                //$customerData->setnav_map_details($response)->save();
                // print_r($response);
            }else{
                $total_credit = implode((array)$result['SoapBody']->Read_Result->Rapcode_Wise_Credit_Limit->Credit_Limit_LCY);
                $balance_credit = implode((array)$result['SoapBody']->Read_Result->Rapcode_Wise_Credit_Limit->Balance_LCY);
                // $total_credit = $balance_credit = 0; 
                $credit['total_credit'] = $total_credit;
                    $credit['balance_credit'] = $balance_credit;
                    $credit['utilized_credit'] = $total_credit - $balance_credit;
                    $credit['total_label'] = 'Total Credit Limit';
                    $credit['utilized_label'] = 'Used Credit Limit';
                    $credit['balance_label'] = 'Balance Credit Limit';
                // print_r($credit);
            }

        }catch(Exception $e){
        	trigger_error(sprintf(
	        'Curl failed with error #%d: %s',
	        $e->getCode(), $e->getMessage()),
	        E_USER_ERROR);
        }

   //          if (is_soap_fault($soapResponse)) {
   //  			trigger_error("SOAP Fault: (faultcode: {$result->faultcode}, faultstring: {$result->faultstring})", E_USER_ERROR);
			// }
