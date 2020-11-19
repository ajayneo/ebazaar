<?php
class Neo_Replace_Block_Form_Replace extends Mage_Payment_Block_Form
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('replace/form/replace.phtml');
	}
}