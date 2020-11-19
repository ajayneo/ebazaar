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
class Neo_Notification_Block_Adminhtml_Notification_Edit_Tab_Linkform extends Mage_Adminhtml_Block_Widget_Form
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
        $form->setHtmlIdPrefix('linknotification');
        $form->setFieldNameSuffix('linknotification');
        $this->setForm($form);
        $fieldSet = $form->addFieldset(
            'link_notification_form',
            array('legend' => Mage::helper('neo_notification')->__('Link Notification'))
        );

        $status = $fieldSet->addField('status', 'select', array(
            'label'     => Mage::helper('neo_notification')->__('Status'),
            'name'      => 'status',
            'values'    => array(
                'Approved' => 'Approved',
                'Denied'   => 'Denied'
            )
        ));

        $title = $fieldSet->addField(
            'title',
            'text',
            array(
                'label' => Mage::helper('neo_notification')->__('Title'),
                'name'  => 'title',
            'required'  => true,
            'class' => 'required-entry',
           )
        );

        $link = $fieldSet->addField(
            'link',
            'text',
            array(
                'label' => Mage::helper('neo_notification')->__('Link Url'),
                'name'  => 'link',
                'required'  => true,
                'class' => 'required-entry',
            )
        );

        $notification_type = $fieldSet->addField(
            'notification_type',
            'select',
            array(
                'label'  => Mage::helper('neo_notification')->__('Notification Type'),
                'name'   => 'notification_type',
                'values' => array(
                    array(
                        'value' => -1,
                        'label' => Mage::helper('neo_notification')->__('Please Select Notification Type'),
                    ),
                    array(
                        'value' => 3,
                        'label' => Mage::helper('neo_notification')->__('Link'),
                    ),
                    array(
                        'value' => 2,
                        'label' => Mage::helper('neo_notification')->__('Image'),
                    ),
                    array(
                        'value' => 1,
                        'label' => Mage::helper('neo_notification')->__('Text'),
                    ),
                ),
            )
        );
        
        // $status = $fieldset->addField(
        //     'status',
        //     'select',
        //     array(
        //         'label'  => Mage::helper('neo_notification')->__('Status'),
        //         'name'   => 'status',
        //         'values' => array(
        //             array(
        //                 'value' => 2,
        //                 'label' => Mage::helper('neo_notification')->__('Notification Sent'),
        //             ),
        //             array(
        //                 'value' => 1,
        //                 'label' => Mage::helper('neo_notification')->__('Enabled'),
        //             ),
        //             array(
        //                 'value' => 0,
        //                 'label' => Mage::helper('neo_notification')->__('Disabled'),
        //             ),
        //         ),
        //     )
        // );
 
        $denyReason = $fieldSet->addField('denial_reason', 'textarea', array(
            'label'     => Mage::helper('neo_notification')->__('Denial Reason'),
            'name'      => 'denial_reason'
        ));
 
        $approveInfo = $fieldSet->addField('approve_info', 'text', array(
            'label'     => Mage::helper('neo_notification')->__('Approve Information'),
            'name'      => 'approve_info'
        ));

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

        $this->setForm($form);
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($status->getHtmlId(), $status->getName())
            ->addFieldMap($denyReason->getHtmlId(), $denyReason->getName())
            ->addFieldMap($approveInfo->getHtmlId(), $approveInfo->getName())
            ->addFieldMap($title->getHtmlId(), $title->getName())
            ->addFieldMap($link->getHtmlId(), $link->getName())
            ->addFieldMap($notification_type->getHtmlId(), $notification_type->getName())
            ->addFieldDependence(
                $approveInfo->getName(),
                $status->getName(),
                'Approved'
            )
            ->addFieldDependence(
                $title->getName(),
                $status->getName(),
                'Approved'
            )
            ->addFieldDependence(
                $denyReason->getName(),
                $status->getName(),
                'Denied'
            )
            ->addFieldDependence(
                $link->getName(),
                $status->getName(),
                'Denied'
            )
            ->addFieldDependence(
                $notification_type->getName(),
                $status->getName(),
                'Denied'
            )
        );

        return parent::_prepareForm();
    }
}
