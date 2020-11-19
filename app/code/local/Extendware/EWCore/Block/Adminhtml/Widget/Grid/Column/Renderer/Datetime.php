<?php

class Extendware_EWCore_Block_Adminhtml_Widget_Grid_Column_Renderer_Datetime extends Extendware_EWCore_Block_Mage_Adminhtml_Widget_Grid_Column_Renderer_Datetime {

	public function render(Varien_Object $row)
	{
		if ($this->getColumn()->getEditable()) {
			parent::render($row);
		}
		
		$value = $this->_getValue($row);
		if ($value and is_empty_date($value) === false) {
			return parent::render($row);
		}
		return $this->getColumn()->getDefault();
	}
}
