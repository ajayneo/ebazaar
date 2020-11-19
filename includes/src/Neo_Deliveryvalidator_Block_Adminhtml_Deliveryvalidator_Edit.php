<?php
	
class Neo_Deliveryvalidator_Block_Adminhtml_Deliveryvalidator_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "pincode_id";
				$this->_blockGroup = "deliveryvalidator";
				$this->_controller = "adminhtml_deliveryvalidator";
				$this->_updateButton("save", "label", Mage::helper("deliveryvalidator")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("deliveryvalidator")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("deliveryvalidator")->__("Save And Continue Edit"),
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
				if( Mage::registry("deliveryvalidator_data") && Mage::registry("deliveryvalidator_data")->getId() ){

				    return Mage::helper("deliveryvalidator")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("deliveryvalidator_data")->getId()));

				} 
				else{

				     return Mage::helper("deliveryvalidator")->__("Add Item");

				}
		}
}