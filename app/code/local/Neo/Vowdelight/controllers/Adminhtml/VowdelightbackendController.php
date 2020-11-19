<?php
class Neo_Vowdelight_Adminhtml_VowdelightbackendController extends Mage_Adminhtml_Controller_Action
{

	protected function _isAllowed()
	{
		//return Mage::getSingleton('admin/session')->isAllowed('vowdelight/vowdelightbackend');
		return true;
	}

	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Vow Delight Page"));
	   $this->renderLayout();
    }
}