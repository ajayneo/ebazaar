<?php
class Neo_Easyfinance_Block_Adminhtml_Easyfinance_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("easyfinance_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("easyfinance")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("easyfinance")->__("Item Information"),
				"title" => Mage::helper("easyfinance")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("easyfinance/adminhtml_easyfinance_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
