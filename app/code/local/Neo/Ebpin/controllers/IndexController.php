<?php
class Neo_Ebpin_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	   $post = $this->getRequest()->getPost();


        try{       
            $customer = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail($post["email"]);
            if($customer->getId())
            {
                echo json_encode(array("status" => false, "message" => "Customer already exists"));
                exit;
            }
            else
            {
                $mobile = $this->getRequest()->getParam('mobile');
                
                if(!empty($mobile)){
                  $customer_mobile = $mobile;
                }else{
                    echo json_encode(array("status" => false, "message" => "Please Enter Mobile number"));
                    exit;
                }

                $otp_code = Mage::helper('ebpin')->addOTP($customer_mobile);
                
                $session_ebpin = Mage::getSingleton('core/session')->getEBPin();

                if ($otp_code)
                {
                    
                    $uname = Mage::getStoreConfig('ebpin_settings/ebpin_login/username');
                    $passwd = Mage::getStoreConfig('ebpin_settings/ebpin_login/password');
                    $feedid = Mage::getStoreConfig('ebpin_settings/ebpin_login/feedid');

                    $to=$customer_mobile;
                    $msg='Your Single-use EB PIN is '.$otp_code;
                    $msg=urlencode($msg);
                    $data='feedid='.$feedid.'&username='.$uname.'&password='.$passwd.'&To='.$to.'&Text='.$msg;

                    $ch=curl_init('http://bulkpush.mytoday.com/BulkSms/SingleMsgApi');
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $result=curl_exec($ch);


                    curl_close($ch);
                    
                }
                echo json_encode(array("status" => true, "message" => "EB PIN sent to mobile sucessfully."));
                exit;
            }
        } catch (Exception $e) {
            echo json_encode(array("status" => false, "message" => $e->getMessage()));
            Mage::log($e->getMessage());
            exit;
        }
  }

  public function getEbpinAction(){

      try{
        $mobile = $this->getRequest()->getParam('mobile');
        if(!empty($mobile)){
          $session_ebpin = Mage::getSingleton('core/session')->getEBPin();
          if(!empty($session_ebpin)){
             $ebpin =  $session_ebpin[$mobile];
          }
        }

        echo json_encode(array("ebpin" => $ebpin));
      }catch(Exception $e){
        Mage::log($e->getMessage());
      }
  }

  public function mobileAction() {

    $mobile = $this->getRequest()->getParam('mobile');   
    $customerCount = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('mobile',$mobile);

    if(count($customerCount) > 0)
    {
        echo 0;

    }
    else
    {
        echo 1;
    }

  }

  public function emailAction() {

    $email = $this->getRequest()->getParam('email');    
    $customerCount = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('email',$email);

    if(count($customerCount) > 0)
    {
        echo 0;

    }
    else
    {
        echo 1;
    }

  }

  public function pincodeAction() {

    $pincode = $this->getRequest()->getParam('pincode');    
    $response = array();
    $availableflag = false;

    $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

    $query = 'SELECT entity_id,state_code,city FROM city_pincodes WHERE pincode="'.$pincode.'"';
    $result = $connection->fetchRow($query);
    if(isset($result['entity_id'])){
        $availableflag = true;
    }

    if($availableflag){
        $query_region = 'SELECT region_id,default_name FROM directory_country_region WHERE code="'.$result['state_code'].'"';
        $result_region = $connection->fetchRow($query_region);
        $temp["region_id"] = $result_region['region_id'];
        if(!empty($result['state_code'])){
            $exploded_values = explode("-", $result['state_code']);
            $country_code = $exploded_values[0];
        }
        $temp["country_code"] = $country_code;
        $temp["default_name"] = $result_region['default_name'];
        echo $temp["city"] = $result['city'];
    }else{
        echo '';
    }

  }

    public function LenovopinAction() {
      
       $post = $this->getRequest()->getPost();


        try{       
            $customer = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail($post["email"]);
            if($customer->getId())
            {
                echo json_encode(array("status" => false, "message" => "Customer already exists"));
                exit;
            }
            else
            {
                $mobile = $this->getRequest()->getParam('mobile');
                
                if(!empty($mobile)){
                  $customer_mobile = $mobile;
                }else{
                    echo json_encode(array("status" => false, "message" => "Please Enter Mobile number"));
                    exit;
                }

                $otp_code = Mage::helper('ebpin')->addOTP($customer_mobile);
                
                $session_ebpin = Mage::getSingleton('core/session')->getEBPin();

                if ($otp_code)
                {
                    
                    $uname = Mage::getStoreConfig('ebpin_settings/ebpin_login/username');
                    $passwd = Mage::getStoreConfig('ebpin_settings/ebpin_login/password');
                    $feedid = Mage::getStoreConfig('ebpin_settings/ebpin_login/feedid');

                    $to=$customer_mobile;
                    $msg='Your Single-use EB PIN is '.$otp_code;
                    $msg=urlencode($msg);
                    $data='feedid='.$feedid.'&username='.$uname.'&password='.$passwd.'&To='.$to.'&Text='.$msg;

                    $ch=curl_init('http://bulkpush.mytoday.com/BulkSms/SingleMsgApi');
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $result=curl_exec($ch);


                    curl_close($ch);
                    
                }
                echo json_encode(array("status" => true, "message" => "OTP sent to your mobile sucessfully."));
                exit;
            }
        } catch (Exception $e) {
            echo json_encode(array("status" => false, "message" => $e->getMessage()));
            Mage::log($e->getMessage());
            exit;
        }
  }

  public function sendOrderEbPinAction()
    {
      
	$mobile = $this->getRequest()->getParam('mobile');
	try{			
		if(!empty($mobile)){
		  $customer_mobile = $mobile;
		}else{
			echo json_encode(array("status" => false, "message" => "Please Enter Mobile number"));
			exit;
		}
		$otp_code = Mage::helper('ebpin')->addOTP($customer_mobile);			
		$session_ebpin = Mage::getSingleton('core/session')->getEBPin();
		if($otp_code)
		{
			$uname = Mage::getStoreConfig('ebpin_settings/ebpin_login/username');
			$passwd = Mage::getStoreConfig('ebpin_settings/ebpin_login/password');
			$feedid = Mage::getStoreConfig('ebpin_settings/ebpin_login/feedid');

			$to=$customer_mobile;
			$msg='Your Single-use EB PIN is '.$otp_code;
			$msg=urlencode($msg);
			$data='feedid='.$feedid.'&username='.$uname.'&password='.$passwd.'&To='.$to.'&Text='.$msg;

			$ch=curl_init('http://bulkpush.mytoday.com/BulkSms/SingleMsgApi');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result=curl_exec($ch);


			curl_close($ch);

			//code for send order email.
			// Transactional Email Template's ID
			$templateId = 34;
		 
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
			$vars = array('otp' => $otp_code);
				
			$translate  = Mage::getSingleton('core/translate');
		 
			// Send Transactional Email
			Mage::getModel('core/email_template')
				->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);

			try{
			$translate->setTranslateInline(true);
			}catch (Exception $e) {                
			}                        				
		}
		echo json_encode(array("status" => true, "message" => "Please enter your EB PIN sent to you via SMS and Email."));
		exit;
	} catch (Exception $e) {
		echo json_encode(array("status" => false, "message" => $e->getMessage()));
		Mage::log($e->getMessage());
		exit;
	}
   }
    
}
