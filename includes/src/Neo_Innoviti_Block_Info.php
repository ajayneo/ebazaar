<?php
class Neo_Innoviti_Block_Info extends Mage_Payment_Block_Info
{
	protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $info = $this->getInfo();
        $transport = new Varien_Object();
        $transport = parent::_prepareSpecificInformation($transport);
        $transport->addData(array('bankcode' => $info->getBankcode(), 'tanurecode' => $info->getTenurecode()));
        return $transport;
    }
}
?>
