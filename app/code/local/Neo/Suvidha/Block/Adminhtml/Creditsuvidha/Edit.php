<?php
	
class Neo_Suvidha_Block_Adminhtml_Creditsuvidha_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "suvidha";
				$this->_controller = "adminhtml_creditsuvidha";
				$this->_updateButton("save", "label", Mage::helper("suvidha")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("suvidha")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("suvidha")->__("Save And Continue Edit"),
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
				if( Mage::registry("creditsuvidha_data") && Mage::registry("creditsuvidha_data")->getId() ){

				    return Mage::helper("suvidha")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("creditsuvidha_data")->getId()));

				} 
				else{

				     return Mage::helper("suvidha")->__("Add Item");

				}
		}
}