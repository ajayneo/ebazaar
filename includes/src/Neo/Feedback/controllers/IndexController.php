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
}