<?php
class Neo_Affiliatecommision_Block_Adminhtml_Commision_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("commision_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("affiliatecommision")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("affiliatecommision")->__("Item Information"),
				"title" => Mage::helper("affiliatecommision")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("affiliatecommision/adminhtml_commision_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
