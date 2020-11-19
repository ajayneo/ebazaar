<?php
class Neo_Postcode_Model_Resource_Postcode_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct() {
        parent::_construct();
        $this->_init('postcode/postcode');
    }
}
?>
