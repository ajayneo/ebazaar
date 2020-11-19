<?php
class BT_Custompayment_Block_Form_Advanced extends Mage_Payment_Block_Form
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('custompayment/form/advanced.phtml');
	}
}   