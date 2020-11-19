<?php
class Neo_CustomAjax_AjaxController extends Mage_Core_Controller_Front_Action 
{
	public function indexAction() 
	{
		$this->loadLayout();
		$this->renderLayout();
	}
}
