<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Innoviti
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * innoviti Payment Front Controller
 *
 * @category   Mage
 * @package    Innoviti
 * @name       Innoviti_PaymentController
 * @author     Magento Core Team <core@magentocommerce.com>
*/

class Neo_Innoviti_PaymentController extends Mage_Core_Controller_Front_Action
{
    /**
     * Order instance
     */
    protected $_order;

    /**
     *  Return debug flag
     *
     *  @return  boolean
     */
    public function getDebug ()
    {
        return Mage::getSingleton('innoviti/config')->getDebug();
    }

	public function newsletterAction()
	{
		$email = $this->getRequest()->getParam('email');
		$subscriber = Mage::getModel('newsletter/subscriber');
		$record = $subscriber->loadByEmail($email);
		$id = $record->getId();
		if(empty($id)) {
			$status = Mage::getModel('newsletter/subscriber')->subscribe($email);
			if($status == 1) {
				$this->getResponse()->setBody('Thank you for subscribing to our newsletter.');
			} else {
				$this->getResponse()->setBody('This email is already subscribed.');
			}
		} else {
			$this->getResponse()->setBody('This email is already subscribed.');
		}
	}
	
    /**
     *  Get order
     *
     *  @param    none
     *  @return	  Mage_Sales_Model_Order
     */
    public function getOrder ()
    {
        if ($this->_order == null) {
            $session = Mage::getSingleton('checkout/session');
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($session->getLastRealOrderId());
        }
        return $this->_order;
    }

    /**
     * When a customer chooses innoviti on Checkout/Payment page
     *
     */
   
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setinnovitiPaymentQuoteId($session->getQuoteId());
		
        $order = $this->getOrder();
		$linkedOrderObj	= false;
		if ($order->getLinkedOrderId()){ // in case of split orders
			$linkedOrderObj = Mage::getModel('sales/order')->load($order->getLinkedOrderId());
		}
        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }
		
		/*
		if (!$linkedOrderObj->getId()) {
            $this->norouteAction();
            return;
        }
		*/

        $order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('innoviti')->__('Customer was redirected to innoviti')
        );
        $order->save();  
		if($linkedOrderObj){
			$linkedOrderObj->addStatusToHistory(
			$linkedOrderObj->getStatus(),
			Mage::helper('innoviti')->__('Customer was redirected to innoviti'));
			$linkedOrderObj->save();
		}
        $this->getResponse()
		->setBody($this->getLayout()
		->createBlock('innoviti/redirect')
		->setOrder($order)
		->toHtml());


        $session->unsQuoteId();
    }
	
	public function completeAction()
    {
		$result =$_POST['transresponse'];
		Mage::log('response',null,'innoviti_response.log',true);
		Mage::log($result,null,'innoviti_response.log',true);
		//header("Content-Type: text/xml"); 
		$xml = simplexml_load_string($result);
		$response=$this->xml2array($xml);
		 
		//echo date("m-d",$response['respDet']['txnDate']);
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$date = $response['respDet']['txnDate'];
		$txnTime = $response['respDet']['txnTime'];
		$txndate=substr($date,0,4).'-'.substr($date,4,2).'-'.substr($date,6,2).' '.substr($txnTime,0,2).':'.substr($txnTime,2,2). ':'. substr($txnTime,4,2);
		$order=$this->getOrder();
		$order_id=$order->getId();
		$payment_id=$order->getPayment()->getId();
		
		$linkedOrderObj	= false;
		if ($order->getLinkedOrderId()){ // in case of split orders
			$linkedOrderObj = Mage::getModel('sales/order')->load($order->getLinkedOrderId());
		}
		
		$vpc_TransactionNo=$response['respDet']['UnipayId'];
			$sql = 'INSERT INTO sales_payment_transaction(order_id, payment_id, txn_id, txn_type, is_closed, unipaytransresponse, created_at)
			VALUES('.$order_id.', '.$payment_id.', "'.$vpc_TransactionNo.'", "capture", 0, "'.$result.'", "'.$txndate.'")';
			$connection->query($sql);
		if($linkedOrderObj){
			$linkedPaymentId	= $linkedOrderObj->getPayment()->getId();	
			$sql = 'INSERT INTO sales_payment_transaction(order_id, payment_id, txn_id, txn_type, is_closed, unipaytransresponse, created_at)
			VALUES('.$linkedOrderObj->getId().', '.$linkedPaymentId.', "'.$vpc_TransactionNo.'", "capture", 0, "'.$result.'", "'.$txndate.'")';
			$connection->query($sql);
		}
		
		if($response['resCode']=='00') {
			$this->_redirect('innoviti/payment/success', array('_secure'=>true));
		}else{		
			$this->_redirect('innoviti/payment/failure', array('_secure'=>true));
		}
		
		return '';
	}

	public function xml2array($xml) {
		$arr = array();
		foreach ($xml as $tag => $element) {
			//$tag = $element->getName();
			$e = get_object_vars($element);
			if (!empty($e)) {
			  $arr[$tag] = $element instanceof SimpleXMLElement ? $this->xml2array($element) : $e;
			}
			else {
			  $arr[$tag] = trim($element);
			}
		}
	  
		return $arr;
	}


    /**
     *  Success response from Secure Ebs
     *
     *  @return	  void
     */
    public function  successAction()
    {
		$session = Mage::getSingleton('checkout/session');
		$session->setQuoteId($session->getinnovitiPaymentQuoteId());
		$session->unsinnovitiPaymentQuoteId();
		$order = $this->getOrder();
		$linkedOrderObj	= false;
		if ($order->getLinkedOrderId()){ // in case of split orders
			$linkedOrderObj = Mage::getModel('sales/order')->load($order->getLinkedOrderId());
		}
		$order->getId();
		if (!$order->getId()) {
		$this->norouteAction();
		return;
		}
		if ($linkedOrderObj) {
		$this->norouteAction();
		return;
		}
		$order->addStatusToHistory(
            $order->getStatus(),
            Mage::helper('innoviti')->__('Customer was redirected to Innoviti')
		);
		
		$order->addStatusToHistory(
		$order->getStatus(),
		Mage::helper('innoviti')->__('Customer successfully returned from Innoviti')
		);
		if ($linkedOrderObj) {
			$linkedOrderObj->addStatusToHistory(
            $linkedOrderObj->getStatus(),
            Mage::helper('innoviti')->__('Customer was redirected to Innoviti')
			);
			
			$linkedOrderObj->addStatusToHistory(
			$linkedOrderObj->getStatus(),
			Mage::helper('innoviti')->__('Customer successfully returned from Innoviti')
			);
		}
		$message = Mage::helper('innoviti')->__('Successful Transaction from Innoviti');
		
		//$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
		//$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, CompuindiaCustom_Sales_Model_Order::STATE_CONFIRMATION_STATUS_ONLINE_PAYMENT_APPROVED, $message);
		$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $message);
        $order->setStatus('pendingbilldesk');

		//$order->setState('pendingbilldesk','Finance Approval Pending', $message); // intiall code
		
		
		$order->save();
		
		//set approval order counter
								
		$countFeildName = 'counter';	// Assited order sequence number
		if(!$order->getRepCodeId()){
			$countFeildName = 'autoapprovalcounter';	// Seemless order sequence number
		}
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$select_query = "SELECT max(".$countFeildName.") as max_counter FROM sales_flat_order where 1";
		$result_data = $connection->fetchRow($select_query);
		$maxCounterVal	= $result_data['max_counter'] ? $result_data['max_counter']+1 : 1;
			
		$execute_query = "UPDATE sales_flat_order SET ".$countFeildName." = $maxCounterVal WHERE entity_id=".$order->getId();
		$result_data = $connection->query($execute_query);
		
		if ($countFeildName == 'counter'){
			$execute_query = "UPDATE sales_flat_order_grid SET ".$countFeildName." = $maxCounterVal WHERE entity_id=".$order->getId();
			$result_data = $connection->query($execute_query);
		}
										
		$order->sendNewOrderEmail();
		if ($linkedOrderObj){
			//$linkedOrderObj->setState(Mage_Sales_Model_Order::STATE_NEW, CompuindiaCustom_Sales_Model_Order::STATE_CONFIRMATION_STATUS_ONLINE_PAYMENT_APPROVED, $message);
		
			$linkedOrderObj->save();
			$linkedOrderObj->sendNewOrderEmail();
		}
		$this->_redirect('checkout/onepage/success');
    }
    /**
     *  Notification Action from Secure Ebs
     *
     *  @param    none
     *  @return	  void
     */
    public function notifyAction ()
    {
        $postData = $this->getRequest()->getPost();

        if (!count($postData)) {
            $this->norouteAction();
            return;
        }

        if ($this->getDebug()) {
            $debug = Mage::getModel('innoviti/api_debug');
            if (isset($postData['cs2']) && $postData['cs2'] > 0) {
                $debug->setId($postData['cs2']);
            }
            $debug->setResponseBody(print_r($postData,1))->save();
        }

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId(Mage::helper('core')->decrypt($postData['cs1']));
		$linkedOrderObj	= false;
		if ($order->getLinkedOrderId()){ // in case of split orders
			$linkedOrderObj = Mage::getModel('sales/order')->load($order->getLinkedOrderId());
		}
        if ($order->getId()) {
            $result = $order->getPayment()->getMethodInstance()->setOrder($order)->validateResponse($postData);

            if ($result instanceof Exception) {
                if ($order->getId()) {
                    $order->addStatusToHistory(
                        $order->getStatus(),
                        $result->getMessage()
                    );
                    $order->cancel();
					if ($linkedOrderObj) {
						 $linkedOrderObj->addStatusToHistory(
						$linkedOrderObj->getStatus(),
						$result->getMessage()
						);
						$linkedOrderObj->cancel();
					}
                }
                Mage::throwException($result->getMessage());
                return;
            }

            $order->sendNewOrderEmail();

            $order->getPayment()->getMethodInstance()->setTransactionId($postData['transaction_id']);

            if ($this->saveInvoice($order)) {
                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
            }
            $order->save();
			if ($linkedOrderObj) {
				$linkedOrderObj->sendNewOrderEmail();

				$linkedOrderObj->getPayment()->getMethodInstance()->setTransactionId($postData['transaction_id']);

				if ($this->saveInvoice($order)) {
					$linkedOrderObj->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
				}
				$linkedOrderObj->save();
			}
        }
    }
    /**
     *  Save invoice for order
     *
     *  @param    Mage_Sales_Model_Order $order
     *  @return	  boolean Can save invoice or not
     */
    protected function saveInvoice (Mage_Sales_Model_Order $order)
    {
		$linkedOrderObj	= false;
		if ($order->getLinkedOrderId()){ // in case of split orders
			$linkedOrderObj = Mage::getModel('sales/order')->load($order->getLinkedOrderId());
		}
        if ($order->canInvoice()) {
            $invoice = $order->prepareInvoice();
            $invoice->register()->capture();
            Mage::getModel('core/resource_transaction')
               ->addObject($invoice)
               ->addObject($invoice->getOrder())
               ->save();
			if($linkedOrderObj){
				$invoice = $linkedOrderObj->prepareInvoice();
				$invoice->register()->capture();
				Mage::getModel('core/resource_transaction')
               ->addObject($invoice)
               ->addObject($invoice->getOrder())
               ->save();
			}
            return true;
        }

        return false;
    }
    /**
     *  Failure response from innoviti
     *
     *  @return	  void
     */
    public function failureAction ()
    {
        $errorMsg = Mage::helper('innoviti')->__('There was an error occurred during paying process.');

        $order = $this->getOrder();
		$linkedOrderObj	= false;
		if ($order->getLinkedOrderId()){ // in case of split orders
			$linkedOrderObj = Mage::getModel('sales/order')->load($order->getLinkedOrderId());
		}
        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }
		if($linkedOrderObj){
			if (!$linkedOrderObj->getId()) {
				$this->norouteAction();
				return;
			}
		}
        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {			
            $order->addStatusToHistory($order->getStatus(), $errorMsg);
			$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
            $order->cancel();
            $order->save();
			if($linkedOrderObj){
				$linkedOrderObj->addStatusToHistory($linkedOrderObj->getStatus(), $errorMsg);
				$linkedOrderObj->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
				$linkedOrderObj->cancel();
				$linkedOrderObj->save();
			}
        }
		
		

        $this->loadLayout();
        $this->renderLayout();

        Mage::getSingleton('checkout/session')->unsLastRealOrderId();
		$this->_redirect('checkout/onepage/failure');
    }
	
	/* Vithya */
	
	public function returnAction(){

		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$vpc_TransactionNo = $_GET['vpc_TransactionNo'];
		$vpc_OrderInfo = $_GET['vpc_OrderInfo'];
		/*echo '<pre>';
		print_r($_GET);
		
		exit;*/
		$vpc_TxnResponseCode = $_GET['vpc_TxnResponseCode'];
		
		$lastOrderId = Mage::getSingleton('checkout/session')->getLastOrderId();
		$order = Mage::getSingleton('sales/order');
		$order->load($lastOrderId);
		$linkedOrderObj	= false;
		if ($order->getLinkedOrderId()){ // in case of split orders
			$linkedOrderObj = Mage::getModel('sales/order')->load($order->getLinkedOrderId());
		}
		$_totalData = $order->getData();
		$entity_id = $_totalData["entity_id"];
		
		$sql1 = "SELECT MAX(entity_id) as entity_sfop from sales_flat_order_payment";
		$id1 = $connection->fetchRow($sql1);
		$payment_id = $id1["entity_sfop"];
		$date = date("Y-m-d H:i:s");
		if($linkedOrderObj){
			$_linkedOrderTotalData = $linkedOrderObj->getData();
			$linkedEntityId = $_linkedOrderTotalData["entity_id"];
			
			$sql1 = "SELECT entity_id as entity_sfop from sales_flat_order_payment where parent_id=".$linkedEntityId ;
			$id1 = $connection->fetchRow($sql1);
			$linked_payment_id = $id1["entity_sfop"];
		}
		
		if($vpc_TxnResponseCode=='0') {		
			$sql = 'INSERT INTO sales_payment_transaction(order_id, payment_id, txn_id, txn_type, is_closed, created_at)
			VALUES('.$entity_id.', '.$payment_id.', "'.$vpc_TransactionNo.'", "capture", 0, "'.$date.'")';
			$connection->query($sql);
			if($linkedOrderObj){
				$sql = 'INSERT INTO sales_payment_transaction(order_id, payment_id, txn_id, txn_type, is_closed, created_at)
			VALUES('.$linkedEntityId.', '.$linked_payment_id.', "'.$vpc_TransactionNo.'", "capture", 0, "'.$date.'")';
			$connection->query($sql);
			}
			$this->_redirect('innoviti/payment/success');			
		} else {
			$this->_redirect('innoviti/payment/failure');
		} // end: if	
	}

} 

