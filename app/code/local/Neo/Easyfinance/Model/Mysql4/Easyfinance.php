<?php
class Neo_Easyfinance_Model_Mysql4_Easyfinance extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("easyfinance/easyfinance", "easy_finance_id");
    }
}