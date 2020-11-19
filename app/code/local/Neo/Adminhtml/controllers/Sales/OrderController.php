<?php
	require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';
    class Neo_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
    {
    	public function addCommentAction()
	    {
	        if ($order = $this->_initOrder()) {
	            try {
	                $response = false;
	                $data = $this->getRequest()->getPost('history');
	                $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
	                $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;
	                 
	                //getting username
	                $session = Mage::getSingleton('admin/session');
	                $username = $session->getUser()->getUsername();
	                $append = " [name](Comment added by ".$username.")[/name]";
	                 
	                //appending username with markup to comment
	                $order->addStatusHistoryComment($data['comment'].$append, $data['status'])
	                    ->setIsVisibleOnFront($visible)
	                    ->setIsCustomerNotified($notify);
	 
	                $comment = trim(strip_tags($data['comment']));
	 
	                $order->save();
	                $order->sendOrderUpdateEmail($notify, $comment);
	 
	                $this->loadLayout('empty');
	                $this->renderLayout();
	            }
	            catch (Mage_Core_Exception $e) {
	                $response = array(
	                    'error'     => true,
	                    'message'   => $e->getMessage(),
	                );
	            }
	            catch (Exception $e) {
	                $response = array(
	                    'error'     => true,
	                    'message'   => $this->__('Cannot add order history.')
	                );
	            }
	            if (is_array($response)) {
	                $response = Mage::helper('core')->jsonEncode($response);
	                $this->getResponse()->setBody($response);
	            }
	        }
	    }
    } 
?>