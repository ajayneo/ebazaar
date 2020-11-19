<?php
	
class Neo_Offers_Block_Adminhtml_Offers_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "cashback_offers_id";
				$this->_blockGroup = "offers";
				$this->_controller = "adminhtml_offers";
				$this->_updateButton("save", "label", Mage::helper("offers")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("offers")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("offers")->__("Save And Continue Edit"),
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
				if( Mage::registry("offers_data") && Mage::registry("offers_data")->getId() ){

				    return Mage::helper("offers")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("offers_data")->getId()));

				} 
				else{

				     return Mage::helper("offers")->__("Add Item");

				}
		}
}