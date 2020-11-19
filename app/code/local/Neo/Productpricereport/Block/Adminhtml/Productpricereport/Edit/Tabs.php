<?php
class Neo_Productpricereport_Block_Adminhtml_Productpricereport_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("productpricereport_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("productpricereport")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("productpricereport")->__("Item Information"),
				"title" => Mage::helper("productpricereport")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("productpricereport/adminhtml_productpricereport_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
