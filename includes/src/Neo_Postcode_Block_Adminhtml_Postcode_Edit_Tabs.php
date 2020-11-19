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
 * Ad Account admin edit tabs
 *
 * @category    Neo
 * @package     Neo_AdAccount
 * @author      Ultimate Module Creator
 */
 error_reporting(E_ALL);
ini_set('display_errors', 1);
class Neo_Postcode_Block_Adminhtml_Postcode_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs {
    /**
     * Initialize Tabs
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct() {
        parent::__construct();
        $this->setId('postcode_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('postcode')->__('Postcode'));
    }
    /**
     * before render html
     * @access protected
     * @return Neo_AdAccount_Block_Adminhtml_Account_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml(){
        $this->addTab('form_postcode', array(
            'label'        => Mage::helper('postcode')->__('Postcode'),
            'title'        => Mage::helper('postcode')->__('Postcode'),
            'content'     => $this->getLayout()->createBlock('postcode/adminhtml_postcode_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
    /**
     * Retrieve ad account entity
     * @access public
     * @return Neo_AdAccount_Model_Account
     * @author Ultimate Module Creator
     */
    public function getPostcode(){
        return Mage::registry('current_postcode');
    }
}
