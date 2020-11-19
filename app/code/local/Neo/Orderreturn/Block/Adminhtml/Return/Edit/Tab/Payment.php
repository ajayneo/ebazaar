<?php
class Neo_Orderreturn_Block_Adminhtml_Return_Edit_Tab_Payment extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('orderreturn/form/payment_done.phtml');
	}
}     