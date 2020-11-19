<?php

class Neo_Career_Block_Adminhtml_Career_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'career';
        $this->_controller = 'adminhtml_career';
        
        $this->_updateButton('save', 'label', Mage::helper('career')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('career')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('career_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'career_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'career_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('career_data') && Mage::registry('career_data')->getId() ) {
            return Mage::helper('career')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('career_data')->getTitle()));
        } else {
            return Mage::helper('career')->__('Add Item');
        }
    }
}