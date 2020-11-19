<?php

/**
 * Block Creatuitycorp_Shareyourpurchase_Block_Social_Pinterest
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_Block_Social_Button_Twitter_Script extends Mage_Core_Block_Template {

    const XPATH_ENABLE_AGGREGATE_PURCHASE = 'shareyourpurchase/twitter_aggregate_purchase/enable';
    const XPATH_ENABLE_ONE_PRODUCT_PURCHASE = 'shareyourpurchase/twitter_one_product/enable';

    protected function _construct() {

        if (Mage::getStoreConfig(Creatuitycorp_Shareyourpurchase_Block_Social_Wrapper_Abstract::XPATH_ENABLE)
                && (Mage::getStoreConfig(self::XPATH_ENABLE_AGGREGATE_PURCHASE) || Mage::getStoreConfig(self::XPATH_ENABLE_ONE_PRODUCT_PURCHASE))) {

            $this->setTemplate('shareyourpurchase/twitter/script.phtml');
        }
    }

}