<?php

class Wyomind_Advancedinventory_Model_Mysql4_Advancedinventory_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {

        parent::_construct();

        $this->_init('advancedinventory/advancedinventory');
    }

}