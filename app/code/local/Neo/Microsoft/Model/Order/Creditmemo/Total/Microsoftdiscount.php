<?php
class Neo_Microsoft_Model_Order_Creditmemo_Total_Microsoftdiscount 
extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {

		return $this;

        $order = $creditmemo->getOrder();
        $orderMicrosoftdiscountTotal        = $order->getMicrosoftdiscountTotal();

        if ($orderMicrosoftdiscountTotal) {
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal()+$orderMicrosoftdiscountTotal);
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()+$orderMicrosoftdiscountTotal);
        }

        return $this;
    }
}