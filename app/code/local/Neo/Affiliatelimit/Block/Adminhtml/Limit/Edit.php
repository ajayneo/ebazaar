<?php
	
class Neo_Affiliatelimit_Block_Adminhtml_Limit_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "affiliatelimit";
				$this->_controller = "adminhtml_limit";
				$this->_updateButton("save", "label", Mage::helper("affiliatelimit")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("affiliatelimit")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("affiliatelimit")->__("Save And Continue Edit"),
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
				if( Mage::registry("limit_data") && Mage::registry("limit_data")->getId() ){

				    return Mage::helper("affiliatelimit")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("limit_data")->getId()));

				} 
				else{

				     return Mage::helper("affiliatelimit")->__("Add Item");

				}
		}
}