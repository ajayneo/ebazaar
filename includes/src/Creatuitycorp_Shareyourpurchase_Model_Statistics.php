<?php
/*
 * Statistics model
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_Model_Statistics extends Mage_Core_Model_Abstract {
    
    protected function _construct() {
        parent::_construct();
        $this->_init('shareyourpurchase/statistics');
    }
    
}
