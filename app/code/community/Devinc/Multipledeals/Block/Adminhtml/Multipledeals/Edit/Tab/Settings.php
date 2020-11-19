<?php

class Devinc_Multipledeals_Block_Adminhtml_Multipledeals_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('multipledeals_settings', array('legend'=>Mage::helper('multipledeals')->__('Deal Information')));     
    	 
    	//add fields
    	$field = $fieldset->addField('product_id', 'text', array(
            'label'     => Mage::helper('multipledeals')->__('Product Details'),
            'name'      => 'product_id',
            'class'     => 'required-entry',
            'required'  => true,
        ));	    	
    	$field->setRenderer($this->getLayout()->createBlock('multipledeals/adminhtml_multipledeals_edit_renderer_productinfo'));
    	
    	$field = $fieldset->addField('deal_price', 'text', array(
            'label'     => Mage::helper('multipledeals')->__('Deal Price'),
            'name'      => 'deal_price',
            'class'     => 'required-entry',
            'required'  => true,
        ));	
    	 
    	$field = $fieldset->addField('deal_qty', 'text', array(
            'label'     => Mage::helper('multipledeals')->__('Deal Qty'),
            'name'      => 'deal_qty',
            'class'     => 'required-entry',
            'required'  => true,
        ));	
    		
    	$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);  	    
  	    $field = $fieldset->addField('datetime_from', 'date', array(
            'name'      => 'datetime_from',
            'time'      => true,
            'label'     => Mage::helper('multipledeals')->__('Date/Time From'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'class'     => 'required-entry',
            'required'  => true,
            'format'    => $dateFormatIso,	
            'style'	    => 'width:162px !important',
        ));
        
  	    $field = $fieldset->addField('datetime_to', 'date', array(
            'name'      => 'datetime_to',
            'time'      => true,
            'label'     => Mage::helper('multipledeals')->__('Date/Time To'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'class'     => 'required-entry',
            'required'  => true,
            'format'    => $dateFormatIso,	
            'style'	    => 'width:162px !important',
        )); 	   
		
		$stores = Mage::getModel('multipledeals/multipledeals')->getStoreViews();
	    $fieldset->addField('stores', 'multiselect', array(
            'label'     => Mage::helper('multipledeals')->__('Run on Store'),
            'name'      => 'stores',
            'style'     => 'height:100px',
            'values'    => $stores,
            'class'     => 'required-entry',
            'required'  => true,
    		'note'		=> Mage::helper('multipledeals')->__('Select the stores in which you want the deal to run.')
        ));  
    		    		
    	$fieldset->addField('disable', 'select', array(
        	'label'     => Mage::helper('multipledeals')->__('Disable product after deal ends'),
            'name'      => 'disable',
            'values'    => array(
            	array(
                	'value'     => 1,
                    'label'     => Mage::helper('multipledeals')->__('No'),
                ),    
                array(
                    'value'     => 2,
                    'label'     => Mage::helper('multipledeals')->__('Yes'),
                ),
            ),
    		'note'		=> Mage::helper('multipledeals')->__('If Yes - the product will be disabled from the catalog &amp; search after the deal ends to prevent it from appearing on search engines.')
        ));	     
    		
        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('multipledeals')->__('Deal status'),
            'name'      => 'status',
            'class'     => 'required-entry validate-select',
            'required'  => true,
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('multipledeals')->__('Enabled'),
                ),
    
                array(
                    'value'     => 2,
                    'label'     => Mage::helper('multipledeals')->__('Disabled'),
                ),
            ),
        ));     
        
  	    $field = $fieldset->addField('position', 'text', array(
              'label'     => Mage::helper('multipledeals')->__('Position'),
              'name'      => 'position',
              'required'  => false,
        ));	  	  
       
        //set default/session values
        if ($data = Mage::registry('multipledeals_data')) {	
            $form->setValues($data);
        }
        
        return parent::_prepareForm();
    }
}