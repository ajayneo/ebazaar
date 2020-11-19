<?php
class Neo_Asmdetail_Block_Adminhtml_Asmdetail_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("asmdetail_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("asmdetail")->__("ASM Detail Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("asmdetail")->__("ASM Detail Information"),
				"title" => Mage::helper("asmdetail")->__("ASM Detail Information"),
				"content" => $this->getLayout()->createBlock("asmdetail/adminhtml_asmdetail_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
