<?php
	
class Neo_Asmdetail_Block_Adminhtml_Asm_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "asmdetail";
				$this->_controller = "adminhtml_asm";
				$this->_updateButton("save", "label", Mage::helper("asmdetail")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("asmdetail")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("asmdetail")->__("Save And Continue Edit"),
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
				if( Mage::registry("asm_data") && Mage::registry("asm_data")->getId() ){

				    return Mage::helper("asmdetail")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("asm_data")->getId()));

				} 
				else{

				     return Mage::helper("asmdetail")->__("Add Item");

				}
		}
}