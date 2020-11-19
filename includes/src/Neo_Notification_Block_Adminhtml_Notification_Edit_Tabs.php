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
 * Notification admin edit tabs
 *
 * @category    Neo
 * @package     Neo_Notification
 * @author      Ultimate Module Creator
 */
class Neo_Notification_Block_Adminhtml_Notification_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('notification_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('neo_notification')->__('Notification'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Neo_Notification_Block_Adminhtml_Notification_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_notification',
            array(
                'label'   => Mage::helper('neo_notification')->__('Notification'),
                'title'   => Mage::helper('neo_notification')->__('Notification'),
                'content' => $this->getLayout()->createBlock(
                    'neo_notification/adminhtml_notification_edit_tab_form'
                )
                ->toHtml(),
            )
        );

        // $this->addTab(
        //     'category_notification',
        //     array(
        //         'label' => Mage::helper('neo_notification')->__('Category Notification'),
        //         'title' => Mage::helper('neo_notification')->__('Category Notification'),
        //         'content' => $this->getLayout()->createBlock('neo_notification/adminhtml_notification_edit_tab_categoryform'
        //         )
        //         ->toHtml(),
        //     )
        // );

        // $this->addTab(
        //     'link_notification',
        //     array(
        //         'label' => Mage::helper('neo_notification')->__('Link Notification'),
        //         'title' => Mage::helper('neo_notification')->__('Link Notification'),
        //         'content' => $this->getLayout()->createBlock('neo_notification/adminhtml_notification_edit_tab_linkform'
        //         )
        //         ->toHtml(),
        //     )
        // );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve notification entity
     *
     * @access public
     * @return Neo_Notification_Model_Notification
     * @author Ultimate Module Creator
     */
    public function getNotification()
    {
        return Mage::registry('current_notification');
    }
}
