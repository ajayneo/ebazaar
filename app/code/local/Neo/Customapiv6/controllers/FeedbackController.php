<?php
/* *****************************   Electronics Bazaar Website ************************************* 
   ***************************** Code by Jitendra Patel 24 jan 2018 *******************************
   *********************************** Feedback Form  ********************************************* */

class Neo_Customapiv6_FeedbackController extends Neo_Customapiv6_Controller_HttpAuthController
{
    public function sendfeedbackAction()
    {         
            $data = $this->getRequest()->getParams();            
            if(!empty($data))
            {   
            	$orderid = '';
                $cid = '';
            	if($data['orderid'] != '' && $data['scale'] != '' && $data['cid'] != '')
            	{ 
            		//code for check feedback already submitted in database.
                    $orderid = $data['orderid'];
                    $cid = $data['cid'];
                    $feedbackcount = '';
            		$feedbackcount = Mage::getModel('feedback/feedback')->getCollection()
            		                ->addFieldToFilter('orderid',$orderid)
            		                ->addFieldToFilter('cid',$cid);            		            		           		            		            
            	    if(count($feedbackcount) > 0)
            	    {
						echo json_encode(array("status" => 1, "message" => "You have already submitted feedback form."));
						exit;                      
            	    }	
	            	$data['mobile'] = '';
	            	$data['email'] = '';

	                //get customer mobile no & email id.
	                if($data['cid'] != '')
	                {
	                  $customer = Mage::getModel('customer/customer')->load($data['cid']);	                  
	                  $data['mobile'] = $customer->getCusTelephone();
	                  $data['email'] = $customer->getEmail();
	                  $data['name'] = $customer->getName();
	                }
	                else
	                {
	                  echo json_encode(array("status" => 0, "message" => "Customer id not valid"));
	                  exit;
	                }                                

	                try {

		                $data['created_time'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
		                $data['update_time'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
		                $data['status'] = 1;
		                $data['comments'] = $data['feedback'];                	
	                    $feedback = Mage::getModel('feedback/feedback'); //echo '<pre>', print_r($data);exit;
	                    $feedback->setData($data);
	                    $feedback->save();
	                    echo json_encode(array("status" => 1, "message" => "Thank you for submitting your feedback!!"));
	                    exit;
	                } catch(Exception $e) {
	                    echo json_encode(array("status" => 0, "message" => $e->getMessage()));
	                    Mage::log($e->getMessage());
	                    exit;                
	                }
	            }
	            else
	            {
	                echo json_encode(array("status" => 0, "message" => "Please enter valid proper data."));
	                Mage::log($e->getMessage());
	                exit;                 
	            }	                
            }
            else
            {
                echo json_encode(array("status" => 0, "message" => "Please enter valid proper data."));
                Mage::log($e->getMessage());
                exit;                 
            }        
    }
}
?>