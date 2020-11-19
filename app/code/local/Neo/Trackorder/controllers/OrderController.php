<?php
class Neo_Trackorder_OrderController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	/*
	@author Nikhil K R
	@date 19-8-13
	@description	Routes the ajax call
	*/
	public function ajaxAction()
	{
		if (!$this->getRequest()->getPost())
		{
		$session = Mage::getSingleton("customer/session");
		// Store The Current Page Url Where User will be redirected once loggedin
		$session->setBeforeAuthUrl(Mage::helper("core/url")->getCurrentUrl());
		$customerLoginURL = Mage::getBaseUrl() . "customer/account/login";
		Mage::app()->getFrontController()->getResponse()->setRedirect($customerLoginURL)->sendResponse();
		}		
		else {
		$this->loadLayout();
		$this->renderLayout();
		}
	}	
}