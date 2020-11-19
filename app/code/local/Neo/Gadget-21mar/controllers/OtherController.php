<?php
class Neo_Gadget_OtherController extends Mage_Core_Controller_Front_Action{

	 /**
     * Retrieve customer session object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

	public function saveAction(){
		//save gadget POST
		if ($this->getRequest()->isPost()) {
			$customer = $this->_getSession()->getCustomer();
			$gadgetData = $this->getRequest()->getParams();
			$gadgetErrors  = Mage::helper('gadget')->validateData($gadgetData);
	        $errors = $gadgetErrors;

	        try{

	        	$gadget_other = Mage::getModel('gadget/other');
	        	$brand = strip_tags($gadgetData['brand']);
	        	$model = strip_tags($gadgetData['model']);
	        	$description = strip_tags($gadgetData['description']);
	        	$data = array('brand'=>$brand,'model'=>$model,'description'=>$description,'customer_id'=>$customer->getId()); 
	        	$gadget_other->setData($data);

                if (count($errors) === 0) {
                    $gadget_other->save();

                    $receiver = "web.maheshgurav@gmail.com";
                    $subject = "Sell your gadget other details for $brand $model";
                    $message = '';
                    $message .='<p>Sell your gadget request for Other Gadget from customer as per below details.</p>';
                    $message .='<p><b>Customer Name:</b> '.$customer->getFirstname().'</p>';
                    $message .='<p><b>Customer Email:</b> '.$customer->getEmail().'</p>';
					$message .= '<table cellpadding="0" cellspacing="0" style="border:1px solid #dddddd; margin:10px 0 0 0;">';
					$message .= '<tbody>';
					$message .= '<tr>';
					$message .= '<td style="font-family: arial; font-size: 14px; border-right: 1px solid #dddddd; border-bottom: 1px solid #dddddd; padding: 10px;"><strong>Brand</strong></td><td style="font-family: arial; font-size: 14px; border-bottom: 1px solid #dddddd; padding: 10px;">'.$brand.'</td>';
					$message .= '</tr>';
					$message .= '<tr>';
					$message .= '<td style="font-family: arial; font-size: 14px; border-right: 1px solid #dddddd; border-bottom: 1px solid #dddddd; padding: 10px;"><strong>Model</strong></td><td style="font-family: arial; font-size: 14px; border-bottom: 1px solid #dddddd; padding: 10px;">'.$model.'</td>';
					$message .= '</tr>';
					$message .= '<tr>';
					$message .= '<td style="font-family: arial; font-size: 14px; border-right: 1px solid #dddddd; padding: 10px;"><strong>Description</strong></td><td style="font-family: arial; font-size: 14px; padding: 10px;">'.$description.'</td>';
					$message .= '</tr>';
					$message .= '</tbody>';
					$message .= '</table>';
                    
                    // echo $message;

                    // exit;
                    Mage::helper('gadget')->sendEmail($receiver,$subject,$message);
                    $this->_getSession()->addSuccess($this->__('The gadget details has been sent'));
                    $this->_redirectSuccess(Mage::getUrl('/gadget', array('_secure'=>true)));
                    return;
                } else {
                    $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
                    foreach ($errors as $errorMessage) {
                        $this->_getSession()->addError($errorMessage);
                    }
                }
	        }catch(Exception $e){
	        	echo $e->getMessage();
	        }
		}

			
	}//end of function

}
?>