<?php
	
class Neo_Asmdetail_Block_Adminhtml_Asmdetail_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "asmdetail";
				$this->_controller = "adminhtml_asmdetail";
				$this->_updateButton("save", "label", Mage::helper("asmdetail")->__("Save ASM Detail"));
				$this->_updateButton("delete", "label", Mage::helper("asmdetail")->__("Delete ASM Detail"));

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
				if( Mage::registry("asmdetail_data") && Mage::registry("asmdetail_data")->getId() ){

				    return Mage::helper("asmdetail")->__("Edit ASM Detail '%s'", $this->htmlEscape(Mage::registry("asmdetail_data")->getId()));

				} 
				else{

				     return Mage::helper("asmdetail")->__("Add ASM Detail");

				}
		}
}