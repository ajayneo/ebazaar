<?php
	
class Neo_Gadget_Block_Adminhtml_Request_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "gadget";
				$this->_controller = "adminhtml_request";
				$this->_updateButton("save", "label", Mage::helper("gadget")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("gadget")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("gadget")->__("Save And Continue Edit"),
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
				if( Mage::registry("request_data") && Mage::registry("request_data")->getId() ){

				    return Mage::helper("gadget")->__("Ticket ID %s", $this->htmlEscape(Mage::registry("request_data")->getId()));

				} 
				else{

				     return Mage::helper("gadget")->__("Add Item");

				}
		}
}