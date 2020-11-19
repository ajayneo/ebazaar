<?php
class Neo_Cform_Block_Adminhtml_Cst_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("cst_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("cform")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("cform")->__("Item Information"),
				"title" => Mage::helper("cform")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("cform/adminhtml_cst_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
