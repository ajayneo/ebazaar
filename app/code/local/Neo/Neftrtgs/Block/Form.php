<?php
class Neo_Neftrtgs_Block_Form extends Mage_Payment_Block_Form_Cc
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('neftrtgs/neftrtgs.phtml');
    }
}