<?php
class Neo_Gadget_Block_Adminhtml_Request_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("request_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("gadget")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("gadget")->__("Item Information"),
				"title" => Mage::helper("gadget")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("gadget/adminhtml_request_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}