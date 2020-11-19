<?php
/**
 * Neo_AdAccount extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Neo
 * @package        Neo_AdAccount
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Ad Account edit form tab
 *
 * @category    Neo
 * @package     Neo_AdAccount
 * @author      Ultimate Module Creator
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
class Neo_Postcode_Block_Adminhtml_Postcode_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * prepare the form
     * @access protected
     * @return AdAccount_Account_Block_Adminhtml_Account_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm(){
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('postcode_');
        $form->setFieldNameSuffix('postcode');
        $this->setForm($form);
        $fieldset = $form->addFieldset('postcode_form', array('legend'=>Mage::helper('postcode')->__('Postcode')));

       

        $fieldset->addField('area', 'text', array(
            'label' => Mage::helper('postcode')->__('Area'),
            'name'  => 'area',
            'required'  => true,
            'class' => 'required-entry',

        ));

        $fieldset->addField('city', 'text', array(
            'label' => Mage::helper('postcode')->__('City'),
            'name'  => 'city',
            'required'  => true,
            'class' => 'required-entry',

        ));

        $fieldset->addField('state', 'text', array(
            'label' => Mage::helper('postcode')->__('State'),
            'name'  => 'state',
            'required'  => true,
            'class' => 'required-entry',
        ));

        $fieldset->addField('postcode', 'text', array(
            'label' => Mage::helper('postcode')->__('postcode'),
            'name'  => 'postcode',
            'required'  => true,
            'class' => 'required-entry',
        ));

       
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('postcode')->__('Status'),
            'name'  => 'status',
            'values'=> array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('postcode')->__('Enabled'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('postcode')->__('Disabled'),
                ),
            )
        ));

        $formValues = array();
   
        if (Mage::getSingleton('adminhtml/session')->getPostcodeData()){
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getPostcodeData());
            Mage::getSingleton('adminhtml/session')->setPostcodeData(null);
        }
        elseif (Mage::registry('current_postcode')){
            $formValues = array_merge($formValues, Mage::registry('current_postcode')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
