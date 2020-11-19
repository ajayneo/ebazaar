<?php

class Neo_Career_Block_Adminhtml_Career_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('career_form', array('legend'=>Mage::helper('career')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('career')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('jde', 'editor', array(
          'name'      => 'jde',
          'label'     => Mage::helper('career')->__('JDE'),
          'title'     => Mage::helper('career')->__('JDE'),
          'style'     => 'width:700px; height:300px;',
          'wysiwyg'   => true,
          'required'  => true,
      ));

      $fieldset->addField('num_positions', 'text', array(
          'label'     => Mage::helper('career')->__('No. of positions'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'num_positions',
      ));

      $fieldset->addField('experience', 'text', array(
          'label'     => Mage::helper('career')->__('Experience'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'experience',
      ));

      $fieldset->addField('email', 'text', array(
          'label'     => Mage::helper('career')->__('Email'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'email',
      ));

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('career')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('career')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('career')->__('Disabled'),
              ),
          ),
      ));
     

      if ( Mage::getSingleton('adminhtml/session')->getCareerData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCareerData());
          Mage::getSingleton('adminhtml/session')->setCareerData(null);
      } elseif ( Mage::registry('career_data') ) {
          $form->setValues(Mage::registry('career_data')->getData());
      }
      return parent::_prepareForm();
  }
}