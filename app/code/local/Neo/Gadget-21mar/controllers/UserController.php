<?php
class Neo_Gadget_UserController extends Mage_Core_Controller_Front_Action
{
   public function registerAction()
   {
       //echo "Testing going on...";exit;
        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParams(); //echo '<pre>', print_r($data);//exit;
            // below 2 lines are added by pradeep sanku on the 26th June 2015 as a part of not showing the date value in the database
            //$data['created_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
            //$data['updated_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');

            $customerCount = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('mobile',$data['mobile']);

            //echo '<pre>', print_r($customerCount);exit;


            if(count($customerCount) > 0)
            {
                // $session->setCustomerFormData($this->getRequest()->getPost());
                // $error = Mage::helper('customer')->__('There is already an account with this mobile number.');
                // Mage::throwException($error);
                echo json_encode(array("status" => false, "message" => "There is already an account with this mobile number."));exit;                
            }

            $session_ebpin = Mage::getSingleton('core/session')->getEBPin(); //echo '<pre>', print_r($session_ebpin);exit;
            

            if($session_ebpin[$data['mobile']] != $data['cus_otp'])
            {
                // $session->setCustomerFormData($this->getRequest()->getPost());            
                // $error = Mage::helper('customer')->__('Otp Entered by you is not correct.');
                // Mage::throwException($error);
                echo json_encode(array("status" => false, "message" => "OTP Entered by you was not correct."));exit;
            }            

            //create customer.
            $websiteId = Mage::app()->getWebsite()->getId();
            $store = Mage::app()->getStore();
             
            $customer = Mage::getModel("customer/customer");
            $customer   ->setWebsiteId($websiteId)
                        ->setStore($store)
                        ->setGroupId(5)
                        ->setPrefix('')
                        ->setFirstname($data['firstname'])
                        ->setMiddleName('')
                        ->setLastname($data['lastname'])
                        ->setSuffix('')
                        ->setEmail($data['emailid'])
                        ->setPassword('123456')
                        ->setTelephone($data['mobile'])
                        ->setCusTelephone($data['mobile'])
                        ->setMobile($data['mobile']);
             
            try{
                $customer->save();
            }
            catch (Exception $e) {
                Zend_Debug::dump($e->getMessage());
            }

            //Add customer address
            $address = Mage::getModel("customer/address");
            $address->setCustomerId($customer->getId())
            ->setFirstname($customer->getFirstname())
            ->setMiddleName($customer->getMiddlename())
            ->setLastname($customer->getLastname())
            ->setCountryId('IN')
            //->setRegionId('1') //state/province, only needed if the country is USA
            ->setPostcode($data['pincode'])
            ->setCity('')
            ->setTelephone($data['mobile'])
            ->setCusTelephone($data['mobile'])
            ->setMobile($data['mobile'])
            ->setFax('')
            ->setCompany('')
            ->setStreet('')
            ->setIsDefaultBilling('1')
            ->setIsDefaultShipping('1')
            ->setSaveInAddressBook('1');

            try{
            $address->save();
            $customer = Mage::getModel('customer/customer');
            $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
            $customer->loadByEmail(trim($data['emailid']));
            Mage::getSingleton('customer/session')->loginById($customer->getId());            
            echo json_encode(array("status" => true, "message" => "Thank you for register with us!!"));exit;
            }
            catch (Exception $e) {
            //Zend_Debug::dump($e->getMessage());
            echo json_encode(array("status" => false, "message" => "Failed to register. Try again later."));exit;    
            }
        }       
   }

   // public function loginAction()
   // {
   //     //echo "Testing going on...";exit;
   //      if($this->getRequest()->isPost()) {
   //          $data = $this->getRequest()->getParams();  //echo '<pre>', print_r($data);//exit;
   //          //$customerCount = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('mobile',$data['mobile']);
   //          //echo '<pre>', print_r($customerCount);       
   //          try {
   //                  $customer = Mage::getModel('customer/customer');
   //                  $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
   //                  $customer->loadByEmail(trim($data['emailid']));
   //                  Mage::getSingleton('customer/session')->loginById($customer->getId());
   //                  if(Mage::getSingleton('customer/session')->isLoggedIn()){
   //                  echo json_encode(array("status" => true));exit;
   //                  }else{
   //                  echo json_encode(array("status" => false, "message" => "Invalid Email ID"));exit;
   //                  }                                        
   //          } catch(Exception $e) {
   //              echo json_encode(array("status" => false, "message" => "Failed to login. Try again later."));exit;
   //          }
   //      }       
   // }

   public function loginAction()
   {
      $data = $this->getRequest()->getParams();
      if($data['mobile'] !== '' && $data['cus_otp'] !== ''){

          if(empty($data['cus_otp'])){
            echo json_encode(array("status" => false, "message" => "Please enter OTP"));exit;
          }
          
          $customerCount = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('mobile',$data['mobile']);
            if(count($customerCount) > 0)
            {
              $session_ebpin = Mage::getSingleton('core/session')->getEBPin(); 
              if($session_ebpin[$data['mobile']] != $data['cus_otp'])
              {
                  echo json_encode(array("status" => false, "message" => "OTP Entered by you was not correct."));exit;
              }
            }
            else{
                echo json_encode(array("status" => false, "message" => "You are not registered with us, Please sign up."));exit;
            }                                    
            try {
                foreach ($customerCount as $customer) {
                  # code...
                  $customer_id = $customer->getEntityId();
                }
                 Mage::getSingleton('customer/session')->loginById($customer_id);
                if(Mage::getSingleton('customer/session')->isLoggedIn()){
                  echo json_encode(array("status" => true,"message" => "Log In Successfully"));exit;
                }else{
                  echo json_encode(array("status" => false, "message" => "Invalid Email ID"));exit;
                }                                        
            } catch(Exception $e) {
                echo json_encode(array("status" => false, "message" => "Failed to login. Try again later."));exit;
            }
          }else{
              echo json_encode(array("status" => false, "message" => "Please enter mobile no."));exit;            
          }
      }   
   
}  