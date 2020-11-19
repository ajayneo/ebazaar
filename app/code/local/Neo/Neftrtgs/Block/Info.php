<?php
class Neo_Neftrtgs_Block_Info extends Mage_Payment_Block_Info_Cc
{
    protected function _prepareSpecificInformation($transport = null)
    {
        $info = $this->getInfo();
        $o = Mage::getStoreConfig('payment/neftrtgs/instructions');
        $o = nl2br($o);
        $o = explode('<br />', $o);
        $n = array();
        foreach ($o as $key => $value) {
            $o1 = explode(':',$value);
            $n[$o1['0']] = $o1['1'];
        }
        $transport = new Varien_Object($n);
        return $transport;
    }
}