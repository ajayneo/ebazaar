<?php
/*
 * Helper Data
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_Helper_Data
    extends Mage_Core_Helper_Abstract {
    
    const ICONS_STYLE_CLASS = 'shareyourpurchase/general/social_media_styles';
    
    public function canDisplay(){
        $page = $_SERVER['REQUEST_URI'];
        $allowedSitesToDisplayItemsToShare = array('checkout/onepage/success','sales/order/view');
        foreach($allowedSitesToDisplayItemsToShare as $item){
            if(strpos($page,$item)){
                return true;
            };
        }
    }
    
    public function getIconsStyleClass() {   
        switch (Mage::getStoreConfig(self::ICONS_STYLE_CLASS)) {
            case 0:
                return 'icon-set-1';
                break;
            case 1:
                return 'icon-set-2';
                break;
        }
    }

}