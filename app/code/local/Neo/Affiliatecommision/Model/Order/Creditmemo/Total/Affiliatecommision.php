<?php
class Neo_Affiliatecommision_Model_Order_Creditmemo_Total_Affiliatecommision 
extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {

		return $this;

        $order = $creditmemo->getOrder();
        $orderAffiliatecommisionTotal        = $order->getAffiliatecommisionTotal();

        if ($orderAffiliatecommisionTotal) {
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal()+$orderAffiliatecommisionTotal);
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()+$orderAffiliatecommisionTotal);
        }

        return $this;
    }
}