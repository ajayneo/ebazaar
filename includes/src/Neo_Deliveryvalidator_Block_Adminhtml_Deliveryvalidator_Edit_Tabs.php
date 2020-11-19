<?php
class Neo_Deliveryvalidator_Block_Adminhtml_Deliveryvalidator_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("deliveryvalidator_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("deliveryvalidator")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("deliveryvalidator")->__("Item Information"),
				"title" => Mage::helper("deliveryvalidator")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("deliveryvalidator/adminhtml_deliveryvalidator_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
