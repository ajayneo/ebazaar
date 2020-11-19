<?php
/*
 * Statistics resource model
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_Model_Mysql4_Statistics extends Mage_Core_Model_Mysql4_Abstract {
    
    protected function _construct() {
        $this->_init('shareyourpurchase/statistics', 'entity_id');
    }
    
}
