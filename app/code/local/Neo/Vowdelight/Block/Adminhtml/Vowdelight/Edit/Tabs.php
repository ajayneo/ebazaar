<?php
class Neo_Vowdelight_Block_Adminhtml_Vowdelight_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("vowdelight_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("vowdelight")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("vowdelight")->__("Item Information"),
				"title" => Mage::helper("vowdelight")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("vowdelight/adminhtml_vowdelight_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
