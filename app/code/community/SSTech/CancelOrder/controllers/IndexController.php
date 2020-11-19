<?php
class SSTech_CancelOrder_IndexController extends Mage_Core_Controller_Front_Action
{
    const XML_PATH_CANCEL_ORDER     = 'cancelorder/customer_general/enabled';

    const XML_PATH_EMAIL_RECIPIENT  = 'cancelorder/email/recipient_name';
    const XML_PATH_EMAIL_SENDER     = 'cancelorder/email/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE   = 'cancelorder/email/email_template';
    
    public function indexAction()
    {
        $enabled = Mage::getStoreConfigFlag(self::XML_PATH_CANCEL_ORDER, Mage::app()->getStore()->getStoreId());
        if(!$enabled){
            $this->_redirect('/');
        }else{
            $this->loadLayout();
            $this->renderLayout(); 
        }
    }
    
    public function cancelorderAction()
    {
        //$orderId = $this->getRequest()->getPost()
        $post = $this->getRequest()->getPost();

        if ($post) {
            try {
                if (!Zend_Validate::is(trim($post['order_id']), 'NotEmpty')) {
                    $error = true;
                }
                if (!Zend_Validate::is(trim($post['email']), 'NotEmpty')) {
                    $error = true;
                }
                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }
                if ($error) {
                    throw new Exception();
                }
                $this->initOrder($post);
                $order = Mage::registry('current_order');
                if ($order->getId()) {

                    $this->getResponse()->setBody($this->getLayout()->getMessagesBlock()->getGroupedHtml() . $this->_getGridHtml());
                    return;
                    /* $this->loadLayout();     
                      $this->renderLayout(); */
                } else {
                    Mage::getSingleton('core/session')->addError(Mage::helper('cancelorder')->__('Order Not Found.Please try again later'));
                    $this->getResponse()->setBody($this->getLayout()->getMessagesBlock()->getGroupedHtml());
                    return;
                }
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('cancelorder')->__('Please Enter Order Detail.'));
                $this->getResponse()->setBody($this->getLayout()->getMessagesBlock()->getGroupedHtml());
                return;
            }
        } else {
            $this->_redirect('*/*/');
        }
    }
    
    public function initOrder() {
        if ($data = $this->getRequest()->getPost()) {
            $orderId = $data["order_id"];
            $email = $data["email"];
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
            $cEmail = $order->getCustomerEmail();
            if ($cEmail == trim($email)) {
                Mage::register('current_order', $order);
            } else {
                Mage::register('current_order', Mage::getModel("sales/order"));
            }
        }
    }
    
    protected function _getGridHtml() {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load("cancelorder_index_searchorder");
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }


    public function cancelorderguestAction()
    {

        $postenc = $this->getRequest()->getParam('encrypt');
        $post = unserialize(base64_decode($postenc));
        
        $_SESSION['order_no'] = $post['order_no'];
        $_SESSION['email'] = $post['email'];

        $order = Mage::getModel('sales/order')->load($post['order_id']); 

        if ($order->getId()) {
            if (Mage::getStoreConfigFlag(self::XML_PATH_CANCEL_ORDER, Mage::app()->getStore()->getStoreId())) {
                try {
                    $order->cancel();
                    $store = Mage::app()->getStore()->getStoreId();

                    if ($status = Mage::helper('cancelorder/customer')->getCancelStatus($store)) {
                        $order->addStatusHistoryComment('', $status)
                              ->setIsCustomerNotified(1);
                    }

                    //
                        $postObject = new Varien_Object();
                        $postObject->setData($post);

                        $mailTemplate = Mage::getModel('core/email_template');
                        /* @var $mailTemplate Mage_Core_Model_Email_Template */
                        $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                        ->setReplyTo($post['email'])
                        ->sendTransactional(
                            Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                            Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                            Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
                            null,
                            array('data' => $postObject)
                        );

                    ///

                    $order->save();

                    $order->sendOrderUpdateEmail();

                    Mage::getSingleton('core/session')
                        ->addSuccess($this->__('Your order has been canceled.'));
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')
                        ->addException($e, $this->__('Cannot cancel your order.'));
                }
            } else {
                Mage::getSingleton('core/session')
                    ->addError($this->__('Cannot cancel your order.'));
            }

            $this->_redirect('*/*/');

            return;
        }

        $this->_forward('noRoute');
    }
}
