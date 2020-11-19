<?php
class Neo_Offers_Block_Adminhtml_Offers_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("offers_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("offers")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("offers")->__("Item Information"),
				"title" => Mage::helper("offers")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("offers/adminhtml_offers_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
