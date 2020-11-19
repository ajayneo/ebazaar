<?php
/*
 * Config Helper
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_Helper_Config
    extends Mage_Core_Helper_Abstract {

    public function isLightboxEnabled() {
        return Mage::getStoreConfig('shareyourpurchase/general/lightbox_enable');
    }
    
}