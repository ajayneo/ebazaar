<?php
class Neo_Operations_Adminhtml_OperationsbackendController extends Mage_Adminhtml_Controller_Action
{

	protected function _isAllowed()
	{
		//return Mage::getSingleton('admin/session')->isAllowed('operations/operationsbackend');
		return true;
	}

	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Cancel Order"));
	   $this->renderLayout();
    }

    public function cancelOrderAction()
    {
    	$posts = $this->getRequest()->getParams();
    	$orderIds = explode(',',$posts['increment_ids']);                 
        if($posts['increment_ids'] != NULL){                                              
			foreach($orderIds as $orderId)  
			{
				$order_num = Mage::getModel('sales/order')->loadByIncrementID($orderId);  
				
				$order =  Mage::getModel('sales/order')->load($order_num->getId());

				if($order->getState() == 'canceled'){
					Mage::getSingleton("adminhtml/session")->addError("Order Id #".$order->getIncrementId()." has been already canceled.");
				}else/*if($order->getStatus() == 'shipped'){
					Mage::getSingleton("adminhtml/session")->addError("Order Id #".$order->getIncrementId()." has been already shipped.");
				}elseif($order->canShip()) */{
					Mage::getModel('operations/serviceablepincodes')->orderCancelBackToStock($order,$posts);
				}

			}
		}else{
			Mage::getSingleton("adminhtml/session")->addError("Enter Order Ids.");
		}
			
			$this->_redirect("*/*/");
    }
}