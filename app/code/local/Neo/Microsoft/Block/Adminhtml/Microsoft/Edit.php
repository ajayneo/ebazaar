<?php
	
class Neo_Microsoft_Block_Adminhtml_Microsoft_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "microsoft";
				$this->_controller = "adminhtml_microsoft";
				$this->_updateButton("save", "label", Mage::helper("microsoft")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("microsoft")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("microsoft")->__("Save And Continue Edit"),
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
				if( Mage::registry("microsoft_data") && Mage::registry("microsoft_data")->getId() ){

				    return Mage::helper("microsoft")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("microsoft_data")->getId()));

				} 
				else{

				     return Mage::helper("microsoft")->__("Add Item");

				}
		}
}