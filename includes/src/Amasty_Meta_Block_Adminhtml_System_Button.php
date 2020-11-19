<?php

class Amasty_Meta_Block_Adminhtml_System_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		$buttonBlock = Mage::app()->getLayout()->createBlock('adminhtml/widget_button');

		$params = array(
			'store_key' => $buttonBlock->getRequest()->getParam('store')
		);

		$data = array(
			'label'     => Mage::helper('ammeta')->__('Apply Template For Product URLs'),
			'onclick'   => 'setLocation(\''.Mage::helper('adminhtml')->getUrl("ammeta/adminhtml_url/generateUrls", $params)
						   . '?template=\' + $(\'ammeta_product_url_template\').getValue())',
			'class'     => '',
		);

		$html = $buttonBlock->setData($data)->toHtml();

		return $html;
	}
}