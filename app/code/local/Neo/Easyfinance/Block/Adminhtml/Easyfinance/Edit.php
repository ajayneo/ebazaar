<?php
	
class Neo_Easyfinance_Block_Adminhtml_Easyfinance_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "easy_finance_id";
				$this->_blockGroup = "easyfinance";
				$this->_controller = "adminhtml_easyfinance";
				$this->_updateButton("save", "label", Mage::helper("easyfinance")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("easyfinance")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("easyfinance")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("easyfinance_data") && Mage::registry("easyfinance_data")->getId() ){

				    return Mage::helper("easyfinance")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("easyfinance_data")->getId()));

				} 
				else{

				     return Mage::helper("easyfinance")->__("Add Item");

				}
		}
}