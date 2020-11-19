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
 * Notification admin edit form
 *
 * @category    Neo
 * @package     Neo_Notification
 * @author      Ultimate Module Creator
 */
class Neo_Notification_Block_Adminhtml_Notification_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'neo_notification';
        $this->_controller = 'adminhtml_notification';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('neo_notification')->__('Save Notification')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('neo_notification')->__('Delete Notification')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('neo_notification')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ),
            -100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * get the edit form header
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_notification') && Mage::registry('current_notification')->getId()) {
            return Mage::helper('neo_notification')->__(
                "Edit Notification '%s'",
                $this->escapeHtml(Mage::registry('current_notification')->getTitle())
            );
        } else {
            return Mage::helper('neo_notification')->__('Add Notification');
        }
    }
}
