<?php

class Neo_Ticker_Block_Adminhtml_Ticker_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'ticker';
        $this->_controller = 'adminhtml_ticker';
        
        $this->_updateButton('save', 'label', Mage::helper('ticker')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('ticker')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('ticker_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'ticker_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'ticker_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('ticker_data') && Mage::registry('ticker_data')->getId() ) {
            return Mage::helper('ticker')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('ticker_data')->getTitle()));
        } else {
            return Mage::helper('ticker')->__('Add Item');
        }
    }
}