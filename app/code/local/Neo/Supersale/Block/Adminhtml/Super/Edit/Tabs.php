<?php
class Neo_Supersale_Block_Adminhtml_Super_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("super_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("supersale")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("supersale")->__("Item Information"),
				"title" => Mage::helper("supersale")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("supersale/adminhtml_super_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
