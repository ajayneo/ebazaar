<?php

class Neo_Feedback_Model_Mysql4_Feedback extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the feedback_id refers to the key field in your database table.
        $this->_init('feedback/feedback', 'feedback_id');
    }
}