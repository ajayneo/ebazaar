<?php
	
class Neo_Cform_Block_Adminhtml_Cst_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "neocform_id";
				$this->_blockGroup = "cform";
				$this->_controller = "adminhtml_cst";
				$this->_updateButton("save", "label", Mage::helper("cform")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("cform")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("cform")->__("Save And Continue Edit"),
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
				if( Mage::registry("cst_data") && Mage::registry("cst_data")->getId() ){

				    return Mage::helper("cform")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("cst_data")->getId()));

				} 
				else{

				     return Mage::helper("cform")->__("Add Item");

				}
		}
}