<?php
class Devinc_Multipledeals_IndexController extends Mage_Core_Controller_Front_Action
{	 
    public function indexAction()
    {       
        $helper = Mage::helper('multipledeals');
		$storeId = Mage::app()->getStore()->getId();
		
		if ($helper->isEnabled()) {
			//Mage::getModel('multipledeals/multipledeals')->refreshDeals();
        	if ($mainDeal = $helper->getDeal()) {
        	    $product = Mage::getModel('catalog/product')->setStore($storeId)->load($mainDeal->getProductId());	
        	    $product->setDoNotUseCategoryId(true);
        	    
        	    $this->_redirectUrl($product->getProductUrl());
        	    return;		
        	} else {	
				if (Mage::getStoreConfig('multipledeals/configuration/notify')) {	
					$subject = Mage::helper('multipledeals')->__('There are no deals setup at the moment.');
					$content = Mage::helper('multipledeals')->__('A customer tried to view the deals page.');
					$replyTo = Mage::getStoreConfig('trans_email/ident_general/email', $storeId);
							
					$mail = new Zend_Mail();
					$mail->setBodyHtml($content);
					$mail->setFrom($replyTo);
					$mail->addTo(Mage::getStoreConfig('multipledeals/configuration/admin_email'));
					$mail->setSubject($subject);	
					$mail->send();
				}
				$this->_redirect('multipledeals/index/list');
			}
        } else {
			$this->_redirect('no-route');		
		}
    }
	
	public function listAction()
    {      
   		if (Mage::helper('multipledeals')->isEnabled()) {
			//Mage::getModel('multipledeals/multipledeals')->refreshDeals();
			$this->loadLayout();	
			if (Mage::helper('multipledeals')->getMagentoVersion()>1411 && Mage::helper('multipledeals')->getMagentoVersion()<1800) {	
				$this->initLayoutMessages(array('catalog/session', 'checkout/session'));
			}
			$this->renderLayout();      
		} else {
			$this->_redirect('no-route');		
		}
    }
	
	public function recentAction()
    {      
        if (Mage::helper('multipledeals')->isEnabled() && Mage::getStoreConfig('multipledeals/configuration/past_deals')) {
			//Mage::getModel('multipledeals/multipledeals')->refreshDeals();
			$this->loadLayout();	
			if (Mage::helper('multipledeals')->getMagentoVersion()>1411 && Mage::helper('multipledeals')->getMagentoVersion()<1800) {	
				$this->initLayoutMessages(array('catalog/session', 'checkout/session'));
			}
			$this->renderLayout(); 
		} else {
			$this->_redirect('no-route');		
		}
    }
}