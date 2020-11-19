<?php
class Neo_Feedback_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function sendAction()
    {
        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParams('feedback');
            // below 2 lines are added by pradeep sanku on the 26th June 2015 as a part of not showing the date value in the database
            $data['feedback']['created_time'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
            $data['feedback']['update_time'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');

            try {
                $feedback = Mage::getModel('feedback/feedback');
                $feedback->setData($data['feedback']);
                $feedback->save();

                echo 'Feedback sent successfully';
            } catch(Exception $e) {
                echo 'Failed to send feedback. Try again later.';
            }
        }
    }

    public function sendfeedbackAction()
    {
       if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParams(); //echo '<pre>', print_r($data);exit;
            // below 2 lines are added by pradeep sanku on the 26th June 2015 as a part of not showing the date value in the database
            if(!empty($data))
            {
              if($data['orderid'] != '' && $data['scale'] != '' && $data['cid'] != '')
                {                
                    $data['created_time'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
                    $data['update_time'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
                    $data['status'] = 1;                

                    //set session.
                    $feedbackcount = '';
                    $orderid = $data['orderid'];
                    $cid = $data['cid'];                    
                    $feedbackcount = Mage::getModel('feedback/feedback')->getCollection()
                                ->addFieldToFilter('orderid',$orderid)
                                ->addFieldToFilter('cid',$cid);                                                                                     
                    if(count($feedbackcount) > 0)
                    {
                    echo json_encode(array("status" => 1, "message" => "You have already submited your feedback!!"));
                    exit;                      
                    }                              

                    try {
                    $feedback = Mage::getModel('feedback/feedback');
                    $feedback->setData($data);
                    $feedback->save();
                    echo json_encode(array("status" => true, "message" => "Thank you for submiting your feedback!!"));
                    exit;
                    } catch(Exception $e) {
                    echo json_encode(array("status" => false, "message" => $e->getMessage()));
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
                echo json_encode(array("status" => false, "message" => "Something went wrong."));
                Mage::log($e->getMessage());
                exit;                 
            }
        }
    }    
}