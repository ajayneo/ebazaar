<?php

class Neo_Feedback_Block_Adminhtml_Feedback_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('feedback_form', array('legend'=>Mage::helper('feedback')->__('Item information')));
     
      $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('feedback')->__('Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'name',
      ));

      $fieldset->addField('email', 'text', array(
          'label'     => Mage::helper('feedback')->__('Email'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'email',
      ));

      $fieldset->addField('mobile', 'text', array(
          'label'     => Mage::helper('feedback')->__('Mobile'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'mobile',
      ));

      $fieldset->addField('comments', 'editor', array(
          'name'      => 'comments',
          'label'     => Mage::helper('feedback')->__('Comments'),
          'title'     => Mage::helper('feedback')->__('Comments'),
          'style'     => 'width:700px; height:300px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('feedback')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('feedback')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('feedback')->__('Disabled'),
              ),
          ),
      ));
     

      if ( Mage::getSingleton('adminhtml/session')->getFeedbackData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getFeedbackData());
          Mage::getSingleton('adminhtml/session')->setFeedbackData(null);
      } elseif ( Mage::registry('feedback_data') ) {
          $form->setValues(Mage::registry('feedback_data')->getData());
      }
      return parent::_prepareForm();
  }
}