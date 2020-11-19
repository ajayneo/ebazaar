<?php

class Neo_Bannericon_Block_Adminhtml_Bannericon_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'bannericon';
        $this->_controller = 'adminhtml_bannericon';
        
        $this->_updateButton('save', 'label', Mage::helper('bannericon')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('bannericon')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('bannericon_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'bannericon_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'bannericon_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('bannericon_data') && Mage::registry('bannericon_data')->getId() ) {
            return Mage::helper('bannericon')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('bannericon_data')->getTitle()));
        } else {
            return Mage::helper('bannericon')->__('Add Item');
        }
    }
}