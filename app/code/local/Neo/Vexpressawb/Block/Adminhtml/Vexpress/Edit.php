<?php
	
class Neo_Vexpressawb_Block_Adminhtml_Vexpress_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "vexpressawb";
				$this->_controller = "adminhtml_vexpress";
				$this->_updateButton("save", "label", Mage::helper("vexpressawb")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("vexpressawb")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("vexpressawb")->__("Save And Continue Edit"),
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
				if( Mage::registry("vexpress_data") && Mage::registry("vexpress_data")->getId() ){

				    return Mage::helper("vexpressawb")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("vexpress_data")->getId()));

				} 
				else{

				     return Mage::helper("vexpressawb")->__("Add Item");

				}
		}
}