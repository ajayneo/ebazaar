<?php
	
class Neo_Supersale_Block_Adminhtml_Super_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "supersale";
				$this->_controller = "adminhtml_super";
				$this->_updateButton("save", "label", Mage::helper("supersale")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("supersale")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("supersale")->__("Save And Continue Edit"),
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
				if( Mage::registry("super_data") && Mage::registry("super_data")->getId() ){

				    return Mage::helper("supersale")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("super_data")->getId()));

				} 
				else{

				     return Mage::helper("supersale")->__("Add Item");

				}
		}
}