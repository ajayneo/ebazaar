<?php
class Neo_Postcode_Block_Adminhtml_Postcode extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function _construct()
	{
		$this->_controller = 'adminhtml_postcode';
		$this->_blockGroup = 'postcode';
		parent::_construct();
		$this->_headerText = Mage::helper('postcode')->__('Postcode Management');
		$this->_updateButton('add','label',Mage::helper('postcode')->__('Add Postcode'));
	}
}
?>
