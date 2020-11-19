<?php
/**
 * Neo_Notification extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Neo
 * @package        Neo_Notification
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Notification edit form tab
 *
 * @category    Neo
 * @package     Neo_Notification
 * @author      Ultimate Module Creator
 */
class Neo_Notification_Block_Adminhtml_Notification_Edit_Tab_Categoryform extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Neo_Notification_Block_Adminhtml_Notification_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('catnotification');
        $form->setFieldNameSuffix('catnotification');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'cat_notification_form',
            array('legend' => Mage::helper('neo_notification')->__('Category Notification'))
        );

        $fieldset->addField(
            'title',
            'text',
            array(
                'label' => Mage::helper('neo_notification')->__('Title'),
                'name'  => 'title',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'category_id',
            'select',
            array(
                'label' => Mage::helper('neo_notification')->__('Category'),
                'name'  => 'category_id',
                'required'  => true,
                'class' => 'required-entry',
                'values'=> Mage::getModel('neo_notification/notification_attribute_source_categoryid')->getAllOptions(true),
           )
        );
        
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('neo_notification')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 2,
                        'label' => Mage::helper('neo_notification')->__('Notification Sent'),
                    ),
                    array(
                        'value' => 1,
                        'label' => Mage::helper('neo_notification')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('neo_notification')->__('Disabled'),
                    ),
                ),
            )
        );
        $formValues = Mage::registry('current_notification')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getNotificationData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getNotificationData());
            Mage::getSingleton('adminhtml/session')->setNotificationData(null);
        } elseif (Mage::registry('current_notification')) {
            $formValues = array_merge($formValues, Mage::registry('current_notification')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
