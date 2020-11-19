<?php
class Neo_Vexpressawb_Block_Adminhtml_Vexpress_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("vexpress_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("vexpressawb")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("vexpressawb")->__("Item Information"),
				"title" => Mage::helper("vexpressawb")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("vexpressawb/adminhtml_vexpress_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
