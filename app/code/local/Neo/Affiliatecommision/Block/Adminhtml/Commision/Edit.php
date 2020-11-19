<?php
	
class Neo_Affiliatecommision_Block_Adminhtml_Commision_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "affiliatecommision";
				$this->_controller = "adminhtml_commision";
				$this->_updateButton("save", "label", Mage::helper("affiliatecommision")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("affiliatecommision")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("affiliatecommision")->__("Save And Continue Edit"),
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
				if( Mage::registry("commision_data") && Mage::registry("commision_data")->getId() ){

				    return Mage::helper("affiliatecommision")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("commision_data")->getId()));

				} 
				else{

				     return Mage::helper("affiliatecommision")->__("Add Item");

				}
		}
}