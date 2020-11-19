<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
 */
class Amasty_Meta_Adminhtml_UrlController extends Mage_Adminhtml_Controller_Action
{
	public function generateUrlsAction()
	{
		$request  = $this->getRequest();
		$template = trim($request->getParam('template'));

		if (! empty($template)) {
			$key       = $request->getParam('store_key');
			$storeKeys = array(($key ? $key : 'admin'));

			Mage::helper('ammeta/urlKeyHandler')->process($template, $storeKeys);

			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ammeta')
				->__('Template was successfully applied for product urls.'));
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ammeta')
				->__('Please specify template for product urls.'));
		}

		$this->_redirectReferer();
	}
}