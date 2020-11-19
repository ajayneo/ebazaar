<?php 

/** ************************************************************************** **
** Name: Vow Delight Services
** Auth: Jitendra Patel
** Date: 16th March 208
** ********************************************************************************* **/

class Neo_Customapiv6_VowController extends Neo_Customapiv6_Controller_HttpAuthController
{
    public function validateimeiAction() 
    {  
       //if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParams();
            if(!empty($data))
            {
              if($data['order_no'] != '' && $data['imei'] != '')
                {                                    
                  $comment      = '';
                  $reason       = '';
                  $order_no     = '';
                  $comment      = $data['comment'];
                  $reason       = $data['reason'];
                  $order_no     = $data['order_no'];

                  //code for check IMEI already used or not.
                  $collectioncheck = Mage::getModel('vowdelight/vowdelight')->getCollection();                
                  $collectioncheck->addFieldToFilter('old_imei_no',$data['imei']);                          
                  $collectioncheck = $collectioncheck->getData();
                  if(count($collectioncheck) > 0)
                  {
                    echo json_encode(array("status" => 0, "message" => "This IMEI number already used."));                    
                    exit;                             
                  }
                                        
                  $order = Mage::getModel("sales/order")->loadByIncrementId($order_no);
                  if($order->getIncrementId())
                    {                                                                                                    
                      $already_imei_list = '';
                      $already_imei_check = '';
                      $proid = ''; //200079131 15642 'NEWMOB00055'
                      $sku = '';
                      $oldawbno = '';
                    
                        //code for check order has shipment.                        
                        if($order->getStatus() != 'shipped')
                        { 
                          echo json_encode(array("status" => 0, "message" => "Your order dont have shipment details."));                    
                          exit;                          
                        }
                        //code for get the prod id and sku from order id.
                        $ordered_items = $order->getAllItems();
                        foreach($ordered_items as $item)
                        { $pid =  $item->getProductId();   
                          $brand = '';                    
                          $product = Mage::getModel("catalog/product")->load($pid);
                          $brand = $product->getAttributeText('brands');
                          if($brand == 'Vow')
                          {
                            $proid = $product->getEntityId();
                            $sku   = $product->getSku();
                          }
                         }                        
                         if($proid != '' && $sku != '')
                         {                         
                          //code for validate IMEI data from table.
                          $collection = Mage::getModel('vowdelight/imeinumbers')->getCollection();                
                          $collection->addFieldToFilter('imei_numbers',$data['imei']);                
                          $collection->addFieldToSelect('imei_numbers');
                          $collection = $collection->getData(); //echo '<pre>', print_r($collection);exit;
                          $imei = '';
                          foreach($collection as $key => $value)
                           {
                              $imei = $value['imei_numbers'];
                           }
                          if($imei != '')
                          {
                              Mage::log('***** Start vow delight imei process *****', null, 'vowdelight-module.log', true);
                              if(trim($data['imei']) == trim($imei))
                              {                                
                                Mage::log('API Added IMEI NO :'.$data['imei'], null, 'vowdelight-module.log', true);                                
                                Mage::log('API IMEI Data List : ', null, 'vowdelight-module.log', true);                                                    
                                echo json_encode(array('status'=>1,'message'=>'Valid'));                                                
                                exit;                                       
                              }
                          }
                          else
                          {
                            echo json_encode(array("status" => 0, "message" => "IMEI number not found."));exit;                            
                          }                          
                        }
                        else
                        {
                          echo json_encode(array("status" => 0, "message" => "Not found vow mobile in order items."));exit;
                        }                                            
                    }
                    else
                    {
                      echo json_encode(array("status" => 0, "message" => "This order is not valid."));                    
                      exit;                           
                    }
                }
                else
                {
                    echo json_encode(array("status" => 0, "message" => "Imei or order number can not be empty or select valid reason."));                    
                    exit;                 
                }                   
            }
            else
            {
                echo json_encode(array("status" => 0, "message" => "Data not found."));                
                exit;                 
            }
        //}
    }

    public function saverequestAction()
    {   

        $data = $this->getRequest()->getParams();                   
        Mage::log('***** Start IMEI save process *****', null, 'vowdelight-module.log', true);
        $already_imei_list = '';
        $customer_id = '';
        $oldorder_number = '';
        $product_id = '';
        $sku = '';
        $qty = ''; 
        $neworder_number = '';
        $new_order_entityid = '';                                       
        if(!empty($data))
        {

       $datas = json_decode($data['orders'], TRUE);                      
           foreach($datas as $key => $value)
           {  
              $oldorder_number = $value['order_no'];              
              break;
           }

           //get order item pro id. 
           $order = Mage::getModel("sales/order")->loadByIncrementId($oldorder_number);
          $ordered_items = $order->getAllItems();
          foreach($ordered_items as $item)
          { $pid =  $item->getProductId();   
            $brand = '';                    
            $product = Mage::getModel("catalog/product")->load($pid);
            $brand = $product->getAttributeText('brands');
            if($brand == 'Vow')
            {
              $product_id = $product->getEntityId();
              $sku        = $product->getSku();
            }
           }  

         //createOrder($customer_id,$product_id,$qty,$oderNumber);
          $customer_id =  $data['user_id'];
          $qty = count($datas);

          if($customer_id != '' && $oldorder_number != '' && $product_id != '' && $qty != '')
          {
            //code for create order.
            $neworder_number = Mage::helper('vowdelight')->createOrder($customer_id,$product_id,$qty,$oldorder_number);
            Mage::log('API New Order'.$neworder_number, null, 'vowdelight-module.log', true);

            if($neworder_number != '') 
            {               
              //code for create order invoice & shipment.
              $new_order_entityid = Mage::getModel('sales/order')->loadByIncrementId($neworder_number)->getEntityId();
              Mage::log('API New Entity ID'.$new_order_entityid, null, 'vowdelight-module.log', true);
              $forward_payloard = '';
              $forward_payloard = Mage::getModel('ebautomation/ebautomation')->orderProcessAutomation($new_order_entityid);

              //code for get IMEI number from new order.
               $imei_data = array();
               $orderdetails = Mage::getModel('sales/order')->loadByIncrementId($neworder_number); //echo "Check:".$orderdetails->hasInvoices();//exit;                     
               //$orderdetails->getSendConfirmation(null);
               //$orderdetails->sendNewOrderEmail(); 

                //code for send order email.
                // Transactional Email Template's ID
                $templateId = 33;
             
                // Set sender information            
                $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
                $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');        
                $sender = array('name' => $senderName,
                            'email' => $senderEmail);
                
                // Set recepient information
                $customer = Mage::getModel('customer/customer')->load($customer_id);
                $recepientEmail = $customer->getEmail();
                $recepientName  = $customer->getName();        
                
                // Get Store ID        
                $storeId = Mage::app()->getStore()->getId();
             
                // Set variables that can be used in email template
                $vars = array(
                            'order'        => $orderdetails,
                            'billing'      => $orderdetails->getBillingAddress(),
                            'payment_html' => 'Cash On Delivery'
                        );
                        
                $translate  = Mage::getSingleton('core/translate');
             
                // Send Transactional Email
                Mage::getModel('core/email_template')
                    ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);

                try{
                $translate->setTranslateInline(true);
                }catch (Exception $e) {                
                }                                         


               if($orderdetails->hasInvoices()) {                    
                    foreach ($orderdetails->getInvoiceCollection() as $invoice) {
                        foreach ($invoice->getAllItems() as $item)
                        { 
                          $imei_data[] = $item->getSerial();
                        }
                      }
                    }             

              //code for get AWB number from new order.
              $new_awb_no = '';   
              foreach($orderdetails->getTracksCollection() as $track){
              $new_awb_no = $track->getNumber();
              Mage::log('API New AWB No IN: '.$new_awb_no, null, 'vowdelight-module.log', true);             
              }
              Mage::log('API New AWB No OUT: '.$new_awb_no, null, 'vowdelight-module.log', true);
             //code for set request id.
              $request_id      = 1001;
              $last_request_id = '';
              $collection = Mage::getModel('vowdelight/vowdelight')->getCollection();                                
              $collection->addFieldToSelect('request_id');
              $collection->getSelect()->order('id DESC');
              $collection->getSelect()->limit(1);
              $collection = $collection->getData();
              //echo '<pre>', print_r($collection);exit;
              foreach($collection as $data)
              {
                $last_request_id = $data['request_id'];
              }                                      
              if($last_request_id != '')
              {
                $request_id = ++$last_request_id;
              } 
              Mage::log('New Order ID : '.$request_id, null, 'vowdelight-module.log', true);
              Mage::log('Last Order ID : '.$last_request_id, null, 'vowdelight-module.log', true);
              //code for save data in table.
             $vowdelight_model = Mage::getModel('vowdelight/vowdelight');
             $count = 0;
             foreach($datas as $key => $value)
             {
               $vowdelight_model->setRequestId($request_id); //
               $vowdelight_model->setSku($sku); //
               $vowdelight_model->setOldOrderNo($value['order_no']); //
               $vowdelight_model->setOldImeiNo($value['imei']); //               
               $vowdelight_model->setNewOrderNo($neworder_number); //
               $vowdelight_model->setNewImeiNo($imei_data[$count]); //
               $vowdelight_model->setNewAwbNo($new_awb_no); //
               $vowdelight_model->setReason($value['reason']); //
               $vowdelight_model->setComment($value['comments']); //
               $vowdelight_model->setCustomerId($customer_id); //
               $vowdelight_model->setForwardPayload($forward_payloard); //
               $vowdelight_model->setReversePayload();
               $vowdelight_model->setCreatedAt(date('Y-m-d H:i:s'));
               $vowdelight_model->setUpdatedAt(date('Y-m-d H:i:s'));
                try{
                $vowdelight_model->save();
                }catch (Exception $e) {                
                }               
               $vowdelight_model->unsetData();
               $count++;
             }
             Mage::log('API New AWB No Final: '.$new_awb_no, null, 'vowdelight-module.log', true);
             //code for reverse pickup.
             $reverse_response    = array();
             $pincode_serviceable = '';
             if($request_id != '' && $oldorder_number != '')
             {
                Mage::log('API Reverse Pickup Old Order ID : '.$oldorder_number, null, 'vowdelight-module.log', true);
                $pincode_serviceable = Mage::helper('orderreturn')->pincodeEcomQcRv($oldorder_number);
                Mage::log('API Pincode Serviceable : '.$pincode_serviceable, null, 'vowdelight-module.log', true);
                if($pincode_serviceable == 1)
                {                
                  Mage::log('API Reverse Pickup Request ID : '.$request_id, null, 'vowdelight-module.log', true);
                  $reverse_response = $vowdelight_model->ecom_reverse_pickup($request_id);                
                  Mage::log('API Reverse Pickup Response : ', null, 'vowdelight-module.log', true);
                  Mage::log($reverse_response, null, 'vowdelight-module.log', true);
                }
             }               
             echo json_encode(array('status'=>1,'message'=>'Your request has been successfully submitted. : Your replacement device/s will be delivered within 3-5 working days','data'=>array('increment_id'=>$neworder_number, 'awb'=>$new_awb_no, 'entity_id' => $new_order_entityid)));
             exit;
            }
            else
            {
             echo json_encode(array("status" => 0, "message" => "New Order number not found."));                
             exit;
            }             
          }
          else
          {
           echo json_encode(array("status" => 0, "message" => "Data not found."));                
           exit;             
          }
        }
        else
        {
           echo json_encode(array("status" => 0, "message" => "Data not found."));                
           exit;          
        }  
    }     
} 

/** ************************************************************************** **
** Name: Vow Delight Services
** Auth: Jitendra Patel
** Date: 16th March 208
** ********************************************************************************* **/

?>