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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales Order Email Shipment items
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Order_Email_Shipment_Items extends Mage_Sales_Block_Items_Abstract
{
    /**
     * Prepare item before output
     *
     * @param Mage_Core_Block_Abstract $renderer
     * @return Mage_Sales_Block_Items_Abstract
     */
    protected function _prepareItem(Mage_Core_Block_Abstract $renderer)
    {
        $renderer->getItem()->setOrder($this->getOrder());
        $renderer->getItem()->setSource($this->getShipment());
    }


// Code added by sonali to display shipment totals in shipment email
    protected function _initTotals()
    {
        //print_r($this->getOrder()->getData());
        $itemTotalPrice = 0; $itemTotalTax = 0; $itemTotalRowTotal = 0; $itemTotalDiscount = 0;
            
        foreach ($this->getShipment()->getAllItems() as $item) {
            $orderItem = Mage::getModel('sales/order_item')->load($item->getOrderItemId());
            $itemTotalPrice = $itemTotalPrice + ($orderItem->getPrice() * $orderItem->getQtyShipped());
            $itemTotalTax = $itemTotalTax + $orderItem->getTaxAmount();
            //$itemTotalTax = $itemTotalTax + $orderItem->getTaxAmount();
            $itemTotalDiscount = $itemTotalDiscount + $orderItem->getDiscountAmount();
            $itemTotalRowTotal = $itemTotalRowTotal + Mage::getModel('ebautomation/ebautomation')->getItemTotal($orderItem);

        }


       // die;

        $source = $this->getSource();

        $totals = array();
        $totals['subtotal'] = new Varien_Object(array(
            'code'  => 'subtotal',
            'value' => $itemTotalPrice,
            'label' => $this->__('Subtotal')
        ));


        
        $totals['tax'] = new Varien_Object(array(
            'code'  => 'tax',
            'value' => $itemTotalTax,
            'label' => $this->__('GST')
        ));
        

        /**
         * Add shipping
         */
       /* if (!$source->getIsVirtual() && ((float) $source->getShippingAmount() || $source->getShippingDescription()))
        {
            $this->_totals['shipping'] = new Varien_Object(array(
                'code'  => 'shipping',
                'field' => 'shipping_amount',
                'value' => $this->getSource()->getShippingAmount(),
                'label' => $this->__('Shipping & Handling')
            ));
        }
*/
        /**
         * Add discount
         */
        //print_r($this->getOrder()->getDiscountAmount());
        if (((float)$this->getOrder()->getDiscountAmount()) != 0) {
            if ($this->getOrder()->getDiscountDescription()) {
                $discountLabel = $this->__('Discount (%s)', $this->getOrder()->getDiscountDescription());
            } else {
                $discountLabel = $this->__('Discount');
            }
            $totals['discount'] = new Varien_Object(array(
                'code'  => 'discount',
                'field' => 'discount_amount',
                'value' => $itemTotalDiscount,
                'label' => $discountLabel
            ));
        }

       
        
        $totals['grand_total'] = new Varien_Object(array(
            'code'  => 'grand_total',
            'field'  => 'grand_total',
            'strong'=> true,
            'value' => $itemTotalRowTotal,
            'label' => $this->__('Grand Total')
        ));
        

        /**
         * Base grandtotal
         */
        if ($this->getOrder()->isCurrencyDifferent()) {
            $totals['base_grandtotal'] = new Varien_Object(array(
                'code'  => 'base_grandtotal',
                'value' => $this->getOrder()->formatBasePrice($itemTotalRowTotal),
                'label' => $this->__('Grand Total to be Charged'),
                'is_formated' => true,
            ));
        }


        return $totals;
    }
}
