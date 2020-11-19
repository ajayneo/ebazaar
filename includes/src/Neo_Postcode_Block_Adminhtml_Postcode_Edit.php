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
 * Ad Account admin edit form
 *
 * @category    Neo
 * @package     Neo_AdAccount
 * @author      Ultimate Module Creator
 */
class Neo_Postcode_Block_Adminhtml_Postcode_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container {
    /**
     * constructor
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct(){
		
        parent::__construct();
        $this->_blockGroup = 'postcode';
        $this->_controller = 'adminhtml_postcode';
        $this->_updateButton('save', 'label', Mage::helper('postcode')->__('Save Postcode'));
        $this->_updateButton('delete', 'label', Mage::helper('postcode')->__('Delete Postcode'));
    }
    /**
     * get the edit form header
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getHeaderText(){
		
        if( Mage::registry('postcode_data') && Mage::registry('postcode_data')->getId() ) {
            return Mage::helper('postcode')->__("Edit Postcode '%s'", $this->htmlEscape(Mage::registry('postcode_data')->getArea()));
        }
        else {
            return Mage::helper('postcode')->__('Add Postcode');
        }
    }
}
