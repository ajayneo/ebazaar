<?php

class InfoDesires_CashOnDelivery_Model_Quote_Total extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        return $this;
        $address->setBaseCodFee(0);
        $address->setCodFee(0);
        $address->setCodTaxAmount(0);
        $address->setBaseCodTaxAmount(0);
        
        $paymentMethod = Mage::app()->getFrontController()->getRequest()->getParam('payment');
		
        $paymentMethod = Mage::app()->getStore()->isAdmin() && isset($paymentMethod['method']) ? $paymentMethod['method'] : null;
        if ($paymentMethod != 'cashondelivery' && (!count($address->getQuote()->getPaymentsCollection()) || !$address->getQuote()->getPayment()->hasMethodInstance())){            
            return $this;
        }
        
        $paymentMethod = $address->getQuote()->getPayment()->getMethodInstance();

        if ($paymentMethod->getCode() != 'cashondelivery') {            
            return $this;
        }

        $items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }
		
		$subtotal = 0;
		foreach ($items as $item){
    		$order_grand_total += $item->getRowTotalInclTax();
    		//$baseTotal += $item->getRowTotalInclTax();
		}

		//Mage::log('order_grand_total'.$order_grand_total,null,'baseTotal.log');
        $baseTotal = $address->getBaseGrandTotal();
		//Mage::log('baseTotal'.$baseTotal,null,'baseTotal.log');

        //$baseCodFee = $paymentMethod->getAddressCodFee($address);
		$baseCodFee = 0;
		$baseTotal = $address->getGrandTotal();
		//Mage::log('baseTotal'.$baseTotal,null,'baseTotal.log');
		$cod_maxlimit = Mage::getStoreConfig('payment/cashondelivery/cod_maxlimit');
		//Mage::log('cod_maxlimit'.$cod_maxlimit,null,'baseTotal.log');
		if($order_grand_total > $cod_maxlimit){
			$baseCodFee = $order_grand_total - $cod_maxlimit;
		}
			//Mage::log('baseCodFee'.$baseCodFee,null,'baseTotal.log');
        if (!$baseCodFee > 0 ) {
            return $this;
        }

        // adress is the reference for grand total
        $quote = $address->getQuote();

        $store = $quote->getStore();

        //$baseTotal += $baseCodFee;
        $baseTotal =  $baseTotal - $baseCodFee;
		//Mage::log('final baseTotal'.$baseTotal,null,'baseTotal.log');
		
        $address->setBaseCodFee($baseCodFee);
        $address->setCodFee($store->convertPrice($baseCodFee, false));

        // update totals
        $address->setBaseGrandTotal($baseTotal);
        $address->setGrandTotal($store->convertPrice($baseTotal, false));

        //Updating cod tax if it is already included into a COD fee
        $baseCodTaxAmount = $paymentMethod->getAddressCodTaxAmount($address);
        $address->setBaseCodTaxAmount($baseCodTaxAmount);
        $address->setCodTaxAmount($store->convertPrice($baseCodTaxAmount, false));       

        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getCodFee();        
        if ($amount!=0) {
            $quote = $address->getQuote();
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => Mage::helper('cashondelivery')->__('Advance Amount'),
                'value' => $amount,
            ));
        }
        return $this;
    }
}
