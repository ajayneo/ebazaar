<?php
class Neo_Asmdetail_Block_Adminhtml_Rsm_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("rsm_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("asmdetail")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("asmdetail")->__("Item Information"),
				"title" => Mage::helper("asmdetail")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("asmdetail/adminhtml_rsm_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
