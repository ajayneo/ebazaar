<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order history block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_View_History extends Mage_Adminhtml_Block_Template
{
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('order_history_block').parentNode, '".$this->getSubmitUrl()."')";
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'   => Mage::helper('sales')->__('Submit Comment'),
                'class'   => 'save',
                'onclick' => $onclick
            ));
        $this->setChild('submit_button', $button);
        return parent::_prepareLayout();
    }

    public function getStatuses()
    {
        $state = $this->getOrder()->getState();  
        $statuses = $this->getOrder()->getConfig()->getStateStatuses($state);
        //Mage::log(print_r($statuses,1), null, 'order.log');      
        $adminUser = Mage::getSingleton('admin/session')->getUser()->getRole()->getData(); 
        if ($adminUser['role_id'] == 11) {
            $arrStatuses = array();
            foreach ($statuses as $key => $value) {
                if ($key == 'pendingbilldesk' || $key == 'financeapproved' || $key == 'codverified' || $key == 'codprocessing') {
                    if ($key == 'pendingbilldesk') {
                        $arrStatuses['financeapproved'] = 'Finance Approved';
                        
                    }else{
                        $arrStatuses[$key] = $value;
                    }
                }
            }
            if ($this->getOrder()->getStatus() == 'pendingbilldesk') {
                $arrStatuses = array();
                $arrStatuses['financeapproved'] = 'Finance Approved';
            }elseif ($this->getOrder()->getStatus() == 'codpaymentpending') {
                $arrStatuses = array();
                $arrStatuses['complete'] = 'Complete';
            }elseif ($this->getOrder()->getStatus() == 'pendingcod') {
                $arrStatuses = array();
                $arrStatuses['codverified'] = 'COD Verified';
            }elseif ($this->getOrder()->getStatus() == 'financeapproved') {
                $arrStatuses = array();
                //$arrStatuses['codverified'] = 'COD Verified';
            }              
            return $arrStatuses;
        }
        return $statuses;
    }

    public function canSendCommentEmail()
    {
        return Mage::helper('sales')->canSendOrderCommentEmail($this->getOrder()->getStore()->getId());
    }

    /**
     * Retrieve order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('sales_order');
    }

    public function canAddComment()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/comment') &&
               $this->getOrder()->canComment();
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/addComment', array('order_id'=>$this->getOrder()->getId()));
    }

    /**
     * Customer Notification Applicable check method
     *
     * @param  Mage_Sales_Model_Order_Status_History $history
     * @return boolean
     */
    public function isCustomerNotificationNotApplicable(Mage_Sales_Model_Order_Status_History $history)
    {
        return $history->isCustomerNotificationNotApplicable();
    }
}
