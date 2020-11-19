<?php

class Neo_Feedback_Block_Adminhtml_Feedback_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'feedback';
        $this->_controller = 'adminhtml_feedback';
        
        $this->_updateButton('save', 'label', Mage::helper('feedback')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('feedback')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('feedback_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'feedback_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'feedback_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('feedback_data') && Mage::registry('feedback_data')->getId() ) {
            return Mage::helper('feedback')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('feedback_data')->getTitle()));
        } else {
            return Mage::helper('feedback')->__('Add Item');
        }
    }
}