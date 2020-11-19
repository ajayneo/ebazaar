<?php
	
class Neo_Orderreturn_Block_Adminhtml_Return_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "orderreturn";
				$this->_controller = "adminhtml_return";
				$this->_updateButton("save", "label", Mage::helper("orderreturn")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("orderreturn")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("orderreturn")->__("Save And Continue Edit"),
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
				if( Mage::registry("return_data") && Mage::registry("return_data")->getId() ){

				    return Mage::helper("orderreturn")->__("Refund/Replace Request ID #%s", $this->htmlEscape(Mage::registry("return_data")->getId()));

				} 
				else{

				     return Mage::helper("orderreturn")->__("Add Item");

				}
		}
}