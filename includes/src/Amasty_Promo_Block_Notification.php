<?php
/**
 * @copyright   Copyright (c) Amasty
 */
class Amasty_Promo_Block_Notification extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        $products = Mage::helper('ampromo')->getNewItems();

        if ($products){
            return parent::_toHtml();
        }
        else {
            return '';
        }
    }
}
