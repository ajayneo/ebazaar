<?php

class Neo_Feedback_Model_Feedback extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('feedback/feedback');
    }
}