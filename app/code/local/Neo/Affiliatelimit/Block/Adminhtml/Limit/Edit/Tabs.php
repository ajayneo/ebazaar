<?php
class Neo_Affiliatelimit_Block_Adminhtml_Limit_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("limit_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("affiliatelimit")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("affiliatelimit")->__("Item Information"),
				"title" => Mage::helper("affiliatelimit")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("affiliatelimit/adminhtml_limit_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
