<?php
class Neo_Orderreturn_Block_Adminhtml_Return_Edit_Tab_Pickups extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('orderreturn/form/pickups.phtml');
	}

	public function getCustomerAddress(){

		$request_id = $this->getRequest()->getParam("id");

		$address = Mage::helper("orderreturn")->getCustomerAddress($request_id);
		return $address;
	}
}     