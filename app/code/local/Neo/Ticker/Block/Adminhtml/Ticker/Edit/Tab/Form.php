<?php

class Neo_Ticker_Block_Adminhtml_Ticker_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('ticker_form', array('legend'=>Mage::helper('ticker')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('ticker')->__('Title'),
          'name'      => 'title',
      ));

      $fieldset->addField('category', 'text', array(
          'label'     => Mage::helper('ticker')->__('Category'),
          'name'      => 'category',
      ));

      $fieldset->addField('order', 'text', array(
          'label'     => Mage::helper('ticker')->__('Order'),
          'name'      => 'order',
      ));

      $fieldset->addField('link', 'text', array(
          'label'     => Mage::helper('ticker')->__('Link'),
          'name'      => 'link',
      ));

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('ticker')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('ticker')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('ticker')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('ticker')->__('Content'),
          'title'     => Mage::helper('ticker')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getTickerData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTickerData());
          Mage::getSingleton('adminhtml/session')->setTickerData(null);
      } elseif ( Mage::registry('ticker_data') ) {
          $form->setValues(Mage::registry('ticker_data')->getData());
      }
      return parent::_prepareForm();
  }
}