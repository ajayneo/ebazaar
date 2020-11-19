<?php
class Neo_Gadget_Model_Request extends Mage_Core_Model_Abstract
{
    protected function _construct(){
       $this->_init("gadget/request");
    }
    public function sellBackSygOdrer($orderId,$customerId){
       // $productId = $this->getRequest()->getParam('productId');
        $gadget = Mage::getModel('gadget/request')->load($orderId);
        $response = Mage::getModel('shippinge/ecom')->sendRvpRequestGadget($orderId);
        $message = trim($response['message'],',');
        $awb_response = serialize($response);
        $awb_request_date = date('Y-m-d H:i:s');
        $awb_number = $response['airwaybill_number'];
        if($response['success'] == 0){
            $data['awb_number'] = $gadget->getAwbNumber();
            $data['status'] = $gadget->getStatus();
            $data['message'] = "Error : ".$message." ". $response['result'][0]['reason'];
            $data['success'] = $response['success'];
            Mage::getSingleton("core/session")->addError(Mage::helper("gadget")->__($data['message']));
            return array('status'=>1,'message'=>'Your request processed successfully. Your shipment will process by admin.');
        }else{
            $gadget->setAwbNumber($awb_number);
            $gadget->setAwbRequestDate($awb_request_date);
            $gadget->setAwbResponse($awb_response);
            $gadget->setStatus('processing');
            $gadget->save();
            $data['awb_number'] = $gadget->getAwbNumber();
            $data['status'] = $gadget->getStatus();
            $data['message'] = $message;
            $data['success'] = $response['success'];
            
            // Check if any customer is logged in or not
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $customerName = $customer->getName();
            $customerEmail = $customer->getEmail();
            $templateId = 32;
            $emailTemplate = Mage::getModel('core/email_template')->load($templateId);
            $template_variables['customerName'] = $customerName;
            $template_variables['order_id'] = $gadget->getId();
            $template_variables['tracking_name'] = "ECOM";
            $template_variables['tracking_number'] = $data['awb_number'];
            $storeId = Mage::app()->getStore()->getId();
            $processedTemplate = $emailTemplate->getProcessedTemplate($template_variables);
            $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
            $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
            // Set sender information           
            $sender = array('name' => $senderName,'email' => $senderEmail);
            $recepientEmail = array('sonalik2788@gmail.com',$customerEmail); //
            
            $subject = $emailTemplate->getTemplateSubject();
            $z_mail = new Zend_Mail('utf-8');
            $z_mail->setBodyHtml($processedTemplate)
            ->setSubject($subject)
            ->setFrom($senderEmail, $senderName);
            $z_mail->addTo($recepientEmail);
            //try{
            $z_mail->send();
            return array('status'=>1,'message'=>'Your details have been submitted successfully.');
            
        }
        
    }
    /* ############## Start custom code for send email to Retailers. (code by JP.) ################### */        
    public function sendSygRetailerNotification($customerId,$orderid)
    {                                            
          $orderdetails = Mage::getModel('gadget/request')->load($orderid);
          $orderdetails = $orderdetails->getData();
          Mage::log('**************** Strat Retailer Notification Process ****************', null, 'syg-retailers.log', true);
          Mage::log('Order ID : '.$orderid, null, 'syg-retailers.log', true);
          //get the customer zone.
          //if (Mage::getSingleton('customer/session')->isLoggedIn()) {
             $customerPincode = '';
             $customerData = Mage::getSingleton('customer/session')->getCustomer(); 
             //$customerPincode = Mage::getSingleton('customer/customer')->load($customerId)->getPincode();             
             $collection = Mage::getResourceModel('customer/customer_collection')->addNameToSelect();         
             $customerPincode = $orderdetails['pincode']; 
             $collection->addAttributeToFilter('pincode',$customerPincode);
             $collection->addAttributeToFilter('zone', array('neq'=>NULL));
             $collection->addAttributeToFilter('entity_id', array('neq'=>$customerId));             
             $zoneList = array_unique($collection->getColumnValues('zone')); //echo '<pre>', print_r($zone);
             //print_r($zoneList);
             $zonecollection = Mage::getResourceModel('customer/customer_collection')->addNameToSelect();
             //$zonecollection->addAttributeToSelect(array('name','pincode'));
             $zonecollection->addAttributeToFilter('zone', array('in'=>$zoneList));
             /*echo '<pre>';$zonecollection = $zonecollection->getData();
             print_r($zonecollection);
             exit;*/
             foreach($zonecollection as $customerdata)
             {                
                //code for send email.
                $templateId = 30;
             
                // Set sender information            
                $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
                $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');        
                $sender = array('name' => $senderName,
                            'email' => $senderEmail);
                
                // Set recepient information
                $recepientEmail = $customerdata['email'];
                $recepientName = $customerdata['firstname'];;        
                
                // Get Store ID        
                $store = Mage::app()->getStore()->getId();
             
                // Set variables that can be used in email template
                $link = '';
                //echo $link = Mage::getBaseUrl().'confirm-page?orderid='.$orderid.'&userid='.$customerdata['entity_id'];
                $link = "<p><a href=".Mage::getBaseUrl().'confirm-page?orderid='.$orderid.'&userid='.$customerdata['entity_id']." target='blank'>Click here for confirmation</a></p>";
                $vars = array(
                    'order_id'     => $orderid,
                    'customername' => $customerdata['firstname'],
                    'proname'      => $orderdetails['proname'],
                    'price'        => str_replace('?','',$orderdetails['price']),
                    'link'         => $link
                    );
                        
                $translate  = Mage::getSingleton('core/translate');
             
                // Send Transactional Email
                /*Mage::getModel('core/email_template')
                ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);*/
                        
                $translate->setTranslateInline(true); //echo "Send"; //exit;
                Mage::log('Email ID : '.$customerdata['email'], null, 'syg-retailers.log', true);
                //code for get device id for users.
                $user_device_id_data = '';
                $user_device_id      = '';
                $user_device_type    = '';
                $user_device_id_data = Mage::getModel('neo_notification/fcmpush')->getCollection()
                                    ->addFieldToSelect('device_Id')                                
                                    ->addFieldToSelect('device_type')
                                    ->addFieldToFilter('user_id',$customerdata['entity_id']);
                                                                
                $user_device_id_data = $user_device_id_data->getData();
                if(count($user_device_id_data) > 0)
                {
                    foreach($user_device_id_data as $data)
                    {
                        $user_device_id   = $data['device_Id'];
                        $user_device_type = $data['device_type'];                
                        //echo "Device ID:";echo '<pre>', print_r($user_device_id);exit;
                        if($user_device_id != '' && $user_device_type != '')
                        {
                            //$user_device_id = array('37162');
                            Mage::log('Device ID : '.$user_device_id, null, 'syg-retailers.log', true);
                            $message = 
                            "Dear ".$customerdata['firstname'].",\nThe following device is available for exchange in your area. Please do confirm if you want to pick up the same. \nOrder Number – " .$orderid. "\nDevice – " .$orderdetails['proname']. "\nExchange Price to be Paid – INR ".str_replace('?','',$orderdetails['price'])."";
                           $notification_data = array(
                                'title' => $message,
                                'type' => 'syg_link',
                                'user_id' => $customerdata['entity_id'],
                                'order_id' => $orderid,                                                    
                            );
                            $ios = $user_device_type;                    
                           // $fcm_response = Mage::helper('neo_notification')->sygFCMNotification($user_device_id, $productData, $notification_data, $ios);                    
                        }
                        else
                        {
                            Mage::log('Device ID OR Type NoT Found', null, 'syg-retailers.log', true);
                        }
                    }
                }
                else
                {
                   Mage::log('Device ID NoT Found', null, 'syg-retailers.log', true); 
                }                                 
             }
          //}          
    }
    /* ############## End custom code for send email to Retailers. (code by JP.) ################### */
}
	 