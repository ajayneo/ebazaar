<?php
class Neo_Suvidha_Block_Adminhtml_Creditsuvidha_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("creditsuvidha_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("suvidha")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("suvidha")->__("Item Information"),
				"title" => Mage::helper("suvidha")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("suvidha/adminhtml_creditsuvidha_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
