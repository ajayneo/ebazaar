<?php

class Neo_Custom_Block_Adminhtml_Custom_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('custom_form', array('legend'=>Mage::helper('custom')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('custom')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('custom')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('custom')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('custom')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('custom')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('custom')->__('Content'),
          'title'     => Mage::helper('custom')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getCustomData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCustomData());
          Mage::getSingleton('adminhtml/session')->setCustomData(null);
      } elseif ( Mage::registry('custom_data') ) {
          $form->setValues(Mage::registry('custom_data')->getData());
      }
      return parent::_prepareForm();
  }
}