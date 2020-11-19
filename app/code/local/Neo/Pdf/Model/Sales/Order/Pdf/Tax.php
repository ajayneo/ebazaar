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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order Total PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Neo_Pdf_Model_Sales_Order_Pdf_Tax extends Mage_Tax_Model_Sales_Pdf_Tax
{
   
    public function getFullTaxInfo()
    { 
        $taxClassAmount = Mage::helper('tax')->getCalculatedTaxes($this->getOrder());
        $fontSize       = $this->getFontSize() ? $this->getFontSize() : 7;

        if (!empty($taxClassAmount)) {
            $shippingTax    = Mage::helper('tax')->getShippingTax($this->getOrder());
            $taxClassAmount = array_merge($shippingTax, $taxClassAmount);

            foreach ($taxClassAmount as &$tax) {
                //$percent          = $tax['percent'] ? ' (' . $tax['percent']. '%)' : '';
                $tax['amount']    = $this->getAmountPrefix().$this->getOrder()->formatPriceTxt($tax['tax_amount']);
                $tax['label']     = Mage::helper('tax')->__($tax['title']) . $percent . ':';
                $tax['font_size'] = $fontSize;
            }
        } else {
            $rates    = Mage::getResourceModel('sales/order_tax_collection')->loadByOrder($this->getOrder())->toArray();
            $fullInfo = Mage::getSingleton('tax/calculation')->reproduceProcess($rates['items']);
            $tax_info = array();

            if ($fullInfo) {
                foreach ($fullInfo as $info) {
                    if (isset($info['hidden']) && $info['hidden']) {
                        continue;
                    }

                    $_amount = $info['amount'];

                    foreach ($info['rates'] as $rate) {
                        //$percent = $rate['percent'] ? ' (' . $rate['percent']. '%)' : '';

                        $tax_info[] = array(
                            'amount'    => $this->getAmountPrefix() . $this->getOrder()->formatPriceTxt($_amount),
                            'label'     => Mage::helper('tax')->__($rate['title']) . $percent . ':',
                            'font_size' => $fontSize
                        );
                    }
                }
            }
            $taxClassAmount = $tax_info;
        }

        return $taxClassAmount;
    }

   
    
}
