<?php
	
class Neo_Productpricereport_Block_Adminhtml_Productpricereport_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "productpricereport";
				$this->_controller = "adminhtml_productpricereport";
				$this->_updateButton("save", "label", Mage::helper("productpricereport")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("productpricereport")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("productpricereport")->__("Save And Continue Edit"),
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
				if( Mage::registry("productpricereport_data") && Mage::registry("productpricereport_data")->getId() ){

				    return Mage::helper("productpricereport")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("productpricereport_data")->getId()));

				} 
				else{

				     return Mage::helper("productpricereport")->__("Add Item");

				}
		}
}