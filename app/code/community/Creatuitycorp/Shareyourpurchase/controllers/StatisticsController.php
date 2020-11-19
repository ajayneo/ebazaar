<?php

/**
 * Controller Creatuitycorp_Shareyourpurchase_StatisticsController
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_StatisticsController extends Mage_Core_Controller_Front_Action {

    public function saveAction() {
        
        if (Mage::app()->getRequest()->getPost()) {
        
            $provider = Mage::app()->getRequest()->getPost('provider');
            $orderIncrementId = Mage::app()->getRequest()->getPost('orderIncrementId');
            $productName = Mage::app()->getRequest()->getPost('productName');

            $statistics = Mage::getModel('shareyourpurchase/statistics');
            $statistics->setProvider($provider);
            $statistics->setProductName($productName);
            $statistics->setOrderIncrementId($orderIncrementId);
            $statistics->setSharedAt(Mage::getModel('core/date')->gmtDate());
            $statistics->save();
        }
    }

}