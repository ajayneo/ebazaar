<?php
class Neo_Vowdelight_IndexController extends Mage_Core_Controller_Front_Action{

    // public function preDispatch()
    // {
    //     parent::preDispatch();
     
    //     if (!Mage::getSingleton('customer/session')->authenticate($this)) {
    //         $this->setFlag('', 'no-dispatch', true);
    //     }
    // }

    public function indexAction() 
    {  
       
      if(!Mage::getSingleton('customer/session')->isLoggedIn()){
        Mage::getSingleton('customer/session')->setAfterAuthUrl(Mage::helper('core/url')->getCurrentUrl());
        Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('customer/account/login'));
      }
      else
      {
    	  $this->loadLayout();   
    	  $this->getLayout()->getBlock("head")->setTitle($this->__("Vow Delight Page"));
    	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
          $breadcrumbs->addCrumb("home", array(
                    "label" => $this->__("Home Page"),
                    "title" => $this->__("Home Page"),
                    "link"  => Mage::getBaseUrl()
    		   ));

          $breadcrumbs->addCrumb("vow delight page", array(
                    "label" => $this->__("Vow Delight Page"),
                    "title" => $this->__("Vow Delight Page")
    		   ));
          $this->renderLayout();
          } 	  
    }

    public function searchimeiAction() 
    {  
       //if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParams();
            if(!empty($data))
            {
              if($data['order_no'] != '' && $data['imei'] != '' && $data['reason'] != '')
                {                                    
                  $comment      = '';
                  $reason       = '';
                  $order_no     = '';
                  $comment      = $data['comment'];
                  $reason       = $data['reason'];
                  $order_no     = $data['order_no'];
                  //get the customer id from login.
                  $customer_id  = '';
                  $customerData = Mage::getSingleton('customer/session')->getCustomer();
                  $customer_id  = $customerData->getId();

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
                      $session = Mage::getSingleton('core/session');                                                                                                                           
                      $already_imei_list  = array();
                      $already_imei_check = array();
                      $proid = ''; //200079131 15642 'NEWMOB00055'
                      $sku = '';
                    
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
                              $already_imei_list = $session->getData('imei_list');                                                      
                              if(count($already_imei_list) > 0)
                              {
                                 foreach($already_imei_list as $key => $value)
                                 {
                                    $already_imei_check[] = $key;
                                 }
                              }                                        
                              if(in_array($data['imei'], $already_imei_check))
                              { //echo "In"; exit;
                                echo json_encode(array("status" => 0, "message" => "IMEI number already added in below list."));                    
                                exit;   
                              }
                              else if(trim($data['imei']) == trim($imei))
                              {                                
          										  //echo "AWB:".$oldawbno;exit;
                                $already_imei_list[$data['imei']]['oldorderno'] = $order_no;
                                $already_imei_list[$data['imei']]['reason']     = $reason;
                                $already_imei_list[$data['imei']]['comment']    = $comment;
                                $already_imei_list[$data['imei']]['proid']      = $proid;           										  
                                $already_imei_list[$data['imei']]['sku']        = $sku;
          										  //echo '<pre>', print_r($already_imei_list);exit;
                                Mage::log('Added IMEI NO :'.$data['imei'], null, 'vowdelight-module.log', true);
                                $session->setData('imei_list',$already_imei_list);
                                Mage::log('IMEI Data List : ', null, 'vowdelight-module.log', true);
                                Mage::log($already_imei_list, null, 'vowdelight-module.log', true); 
                                echo json_encode(array("status" => 1, "message" => "IMEI number successfully added", "oldorderno" => $order_no, "imei" => $imei, "reason" => $reason, "comment" => $comment));                    
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
                    echo json_encode(array("status" => 0, "message" => "IMEI or order number can not be empty or select valid reason."));                    
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

    public function removeimeiAction()
    {
        $data = $this->getRequest()->getParams();
        $already_imei_list  = '';
        $already_imei_check = '';
        if($data['imeiid'] != '')
        {
            Mage::log('************* Start Removed IMEI process *****************', null, 'vowdelight-module.log', true);
            $session = Mage::getSingleton('core/session');
            $already_imei_list = $session->getData('imei_list');                                       
            if(count($already_imei_list) > 0)
            {  
               foreach($already_imei_list as $key => $value)
               { 
                 if(trim($key) == trim($data['imeiid']))
                 { //echo "IMEI:".$key;exit;                   
                   //unset($key);
                   //$session->unsImeiList3($key);
                   unset($_SESSION['core']['imei_list'][$key]);                   
                   Mage::log($key, null, 'vowdelight-module.log', true);

                   echo json_encode(array("status" => 1, "message" => "IMEI number removed successfully.", "count" => count($already_imei_list)));                
                   exit;
                 }                   
               }
            } 
        }
        else
        {
          echo json_encode(array("status" => 0, "message" => "IMEI number not found."));                
          exit;                 
        }
    }

    public function saveimeiAction()
    {   
        Mage::log('***** Start IMEI save process *****', null, 'vowdelight-module.log', true);
        $already_imei_list = '';
        $session = Mage::getSingleton('core/session');
        $already_imei_list = $session->getData('imei_list');
        $customer_id = '';
        $oldorder_number = '';
        $product_id = '';
        $qty = ''; 
        $neworder_number = '';
        $new_order_entityid = '';                                       
        if(count($already_imei_list) > 0)
        {
           foreach($already_imei_list as $key => $value)
           {
              $oldorder_number = $value['oldorderno'];
              $product_id = $value['proid'];
              break;
           }
         //createOrder($customer_id,$product_id,$qty,$oderNumber);
          $customer_id =  Mage::getSingleton('customer/session')->getCustomer()->getId();
          $qty = count($already_imei_list);

          if($customer_id != '' && $oldorder_number != '' && $product_id != '' && $qty != '')
          {
            //code for create order.
            $neworder_number = Mage::helper('vowdelight')->createOrder($customer_id,$product_id,$qty,$oldorder_number);

            if($neworder_number != '') 
            { 
              $session->setData('new_order_no',$neworder_number);

              //code for create order invoice & shipment.
              $new_order_entityid = Mage::getModel('sales/order')->loadByIncrementId($neworder_number)->getEntityId();
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
                $recepientEmail = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
                $recepientName  = Mage::getSingleton('customer/session')->getCustomer()->getName();        
                
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
              foreach ($orderdetails->getTracksCollection() as $track){
              $new_awb_no = $track->getNumber();
              $session->setData('new_awb_no',$new_awb_no);
              }

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
             foreach($already_imei_list as $key => $value)
             {
               $vowdelight_model->setRequestId($request_id);
               $vowdelight_model->setSku($value['sku']);
               $vowdelight_model->setOldOrderNo($value['oldorderno']);
               $vowdelight_model->setOldImeiNo($key);               
               $vowdelight_model->setNewOrderNo($neworder_number);
               $vowdelight_model->setNewImeiNo($imei_data[$count]);
               $vowdelight_model->setNewAwbNo($new_awb_no);
               $vowdelight_model->setReason($value['reason']);
               $vowdelight_model->setComment($value['comment']);
               $vowdelight_model->setCustomerId($customer_id);
               $vowdelight_model->setForwardPayload($forward_payloard);
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

             //code for reverse pickup.
             $reverse_response    = array();
             $pincode_serviceable = '';
             if($request_id != '' && $oldorder_number != '')
             {
                Mage::log('Reverse Pickup Old Order ID : '.$oldorder_number, null, 'vowdelight-module.log', true);
                $pincode_serviceable = Mage::helper('orderreturn')->pincodeEcomQcRv($oldorder_number);
                Mage::log('Pincode Serviceable : '.$pincode_serviceable, null, 'vowdelight-module.log', true);
                if($pincode_serviceable == 1)
                {                
                  Mage::log('Reverse Pickup Request ID : '.$request_id, null, 'vowdelight-module.log', true);
                  $reverse_response = $vowdelight_model->ecom_reverse_pickup($request_id);                
                  Mage::log('Reverse Pickup Response : ', null, 'vowdelight-module.log', true);
                  Mage::log($reverse_response, null, 'vowdelight-module.log', true);
                }
             } 
                          
             echo json_encode(array("status" => 1));                
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
           echo json_encode(array("status" => 0, "message" => "Something went wrong."));                
           exit;          
        }  
    }

    public function successAction(){

      if(!Mage::getSingleton('customer/session')->isLoggedIn()){
        Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('customer/account/login'));
      }
      else
      {      
        $this->loadLayout();   
        $this->getLayout()->getBlock("head")->setTitle($this->__("Success"));
        $this->renderLayout();
      }
    }

    public function programAction(){
          $this->loadLayout();   
          $this->getLayout()->getBlock("head")->setTitle($this->__("Landing Page"));
          $this->renderLayout();
    }    

    public function TestAction() 
    {  
      $this->loadLayout();   
      $this->getLayout()->getBlock("head")->setTitle($this->__("Vow Delight Page"));
            $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
                  "label" => $this->__("Home Page"),
                  "title" => $this->__("Home Page"),
                  "link"  => Mage::getBaseUrl()
         ));

        $breadcrumbs->addCrumb("vow delight page", array(
                  "label" => $this->__("Vow Delight Page"),
                  "title" => $this->__("Vow Delight Page")
         ));
        $this->renderLayout();    
    }
}