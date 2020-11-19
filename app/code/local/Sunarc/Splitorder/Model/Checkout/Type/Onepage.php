<?php
/**
 * 
 *
 * @category Sunarc
 * @package Splitorder-magento
 * @author Sunarc Team <info@sunarctechnologies.com>
 * @copyright Sunarc (http://sunarctechnologies.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Sunarc_Splitorder_Model_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
    /**
     * Create order based on checkout type. Create customer if necessary.
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function saveOrder()
    {
        $isNewCustomer = $this->validateCustomer();

        $cart = $this->getQuote();
        $key  = 0;
        list($item, $temparray) = $this->getItemInfo($cart, $key, $temparray);
        $cart->save();
        $customerId = Mage::getSingleton('customer/session')->getId();
        $customerObj = Mage::getModel('customer/customer')->load($customerId);
        $quoteObj    = $cart;
        $storeId    = Mage::app()->getStore()->getId();
        $storeObj    = $quoteObj->getStore()->load($storeId);

        foreach ($temparray as $key => $item) {
            list($tempPrice, $tempTax) = $this->setItemAttributes($item, $quoteObj, $storeObj, $storeId);
            $this->setEachItemInfo($quoteObj, $item, $tempPrice, $tempTax);

            $this->_quote = $quoteObj;
            $service      = Mage::getModel('sales/service_quote', $quoteObj);
            $service->submitAll();
            if ($isNewCustomer) {
                try {
                    $this->_involveNewCustomer();
                }
                catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            $this->_checkoutSession->setLastQuoteId($quoteObj->getId())
                ->setLastSuccessQuoteId($quoteObj->getId())->clearHelperData();
            $order = $service->getOrder();
            $this->dispatchOrder($order, $quoteObj);

            /**
             * a flag to set that there will be redirect to third party after confirmation
             * eg: paypal standard ipn
             */
            $redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
            /**
             * we only want to send to customer about new order when there is no redirect to third party
             */
            if (!$redirectUrl && $order->getCanSendNewEmailFlag()) {
                try {
                    $order->sendNewOrderEmail();
                }
                catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            // add order information to the session
            $this->_checkoutSession->setLastOrderId($order->getId())->setRedirectUrl($redirectUrl)
                ->setLastRealOrderId($order->getIncrementId());
            // as well a billing agreement can be created
            $agreement = $order->getPayment()->getBillingAgreement();
            if ($agreement) {
                $this->_checkoutSession->setLastBillingAgreementId($agreement->getId());
            }

            $orderIds[$order->getId()] = $order->getIncrementId();
            Mage::getSingleton('core/session')->setOrderIds($orderIds);
        }

        $profiles = $service->getRecurringPaymentProfiles();
        if ($profiles) {
            $ids = array();
            foreach ($profiles as $profile) {
                $ids[] = $profile->getId();
            }

            $this->_checkoutSession->setLastRecurringProfileIds($ids);
        }

        Mage::dispatchEvent(
            'checkout_submit_all_after', array(
            'order' => $order,
            'quote' => $this->getQuote(),
            'recurring_profiles' => $profiles
            )
        );
        return $this;
    }
    public function getOrderIds($asAssoc = false)
    {
        $idsAssoc = Mage::getSingleton('core/session')->getOrderIds();
        return $asAssoc ? $idsAssoc : array_keys($idsAssoc);
    }

    /**
     * @return bool
     */
    protected function validateCustomer()
    {
        $this->validate();
        $isNewCustomer = false;
        switch ($this->getCheckoutMethod()) {
            case self::METHOD_GUEST:
                $this->_prepareGuestQuote();
                break;
            case self::METHOD_REGISTER:
                $this->_prepareNewCustomerQuote();
                $isNewCustomer = true;
                break;
            default:
                $this->_prepareCustomerQuote();
                break;
        }

        return $isNewCustomer;
    }

    /**
     * @param $cart
     * @param $key
     * @param $temparray
     * @return array
     */
    protected function getItemInfo($cart, $key, $temparray)
    {
        /*foreach ($cart->getAllItems() as $item) {
            $key++;
            $qty = $item->getQty();
            $itemPrice = $item->getPrice();
            $temparray[$key]['product_id'] = $item->getProduct()->getId();
            $temparray[$key]['qty'] = $item->getQty();
            $temparray[$key]['opt'] = $item->getOptions();
            $taxClassId = $item->getData("tax_class_id");
            $taxClasses = Mage::helper("core")->jsonDecode(Mage::helper("tax")->getAllRatesByProductClass());
            $taxRate = $taxClasses["value_" . $taxClassId];
            $taxAmount = (($qty * $itemPrice * $taxRate) / 100);
            $temparray[$key]['tax'] = $taxAmount;
            $temparray[$key]['shipping'] =
                Mage::getSingleton('checkout/cart')->getQuote()->getTotals()['shipping']->getValue();
            $temparray[$key]['price'] = $item->getPrice();
            $cart->removeItem($item->getId());
            $cart->setSubtotal(0);
            $cart->setBaseSubtotal(0);
            $cart->setSubtotalWithDiscount(0);
            $cart->setBaseSubtotalWithDiscount(0);
            $cart->setGrandTotal(0);
            $cart->setBaseGrandTotal(0);
            $cart->setTotalsCollectedFlag(false);
            $cart->collectTotals();
        }*/

        $max = 50000;
        foreach ($cart->getAllItems() as $item) {
            $itemPrice = $item->getPrice();
            $item_qty = $item->getQty();
            // $taxAmount = 0;
            if($itemPrice < $max){
                $qty = 1;
                $taxClassId = $item->getData("tax_class_id");
                $taxClasses = Mage::helper("core")->jsonDecode(Mage::helper("tax")->getAllRatesByProductClass());
                $taxRate = $taxClasses["value_" . $taxClassId];
                $taxAmount = (($qty * $itemPrice * $taxRate) / 100);
                $discAmount = 0;
                $coupon_code = '';
                $dis_desc = '';
                if($item->getDiscountAmount()){
                    $discAmount = $item->getDiscountAmount() / $item_qty;
                    $coupon_code = $item->getCouponCode();
                    $dis_desc = $item->getDiscountDescription();
                }
                //Mage::log('TaxId = '.$taxClassId.' taxRate = '.$taxRate.' taxAmt = '.$taxAmount,null,'splitorder.log',true);
                //exit('check tax class');
                for($j = 1; $j <= $item_qty; $j++){
                    $temparray[$key]['product_id'] = $item->getProduct()->getId();
                    $temparray[$key]['qty'] = $qty;
                    $temparray[$key]['opt'] = $item->getOptions();
                    $temparray[$key]['tax'] = $taxAmount;
                    $temparray[$key]['tax_class_id'] = $taxClassId;
                    $temparray[$key]['tax_percent'] = $taxRate;
                    $temparray[$key]['discount_amount'] = $discAmount;
                    $temparray[$key]['coupon_code'] = $coupon_code;
                    $temparray[$key]['discount_description'] = $dis_desc;
                    $temparray[$key]['shipping'] =
                        Mage::getSingleton('checkout/cart')->getQuote()->getTotals()['shipping']->getValue();
                    $temparray[$key]['price'] = $item->getPrice();
                    $key++;
                }

                $cart->removeItem($item->getId());
                $cart->setSubtotal(0);
                $cart->setBaseSubtotal(0);
                $cart->setSubtotalWithDiscount(0);
                $cart->setBaseSubtotalWithDiscount(0);
                $cart->setGrandTotal(0);
                $cart->setBaseGrandTotal(0);
                $cart->setTotalsCollectedFlag(false);
                $cart->collectTotals();
            }
        }

        return array($item, $temparray);
    }

    /**
     * @param $quoteObj
     * @param $item
     * @param $tempPrice
     * @param $tempTax
     */
    protected function setEachItemInfo($quoteObj, $item, $tempPrice, $tempTax)
    {
        foreach ($quoteObj->getAllAddresses() as $address) {
            $address->setBaseSubtotal($item['price']);

            $address->setDiscountAmount(-$item['discount_amount']);
            $address->setBaseDiscountAmount(-$item['discount_amount']);
            
            $address->setSubtotal($tempPrice);
            $address->setBaseGrandTotal($tempTax);
            $address->setGrandTotal($tempTax);
            $address->setTaxAmount($item['tax']);
            $address->setBaseTaxAmount($item['tax']);
        }
    }

    /**
     * @param $item
     * @param $quoteObj
     * @param $storeObj
     * @param $storeId
     * @return array
     */
    protected function setItemAttributes($item, $quoteObj, $storeObj, $storeId)
    {
        // $tempPrice = $item['qty'] * $item['price'];
        $tempPrice = $item['price'];
        $discountAmount = $item['discount_amount'];
        $tempTax = $tempPrice + $item['tax'] + $item['shipping'] - $discountAmount;
        $quoteObj->setStore($storeObj);
        $productModel = Mage::getModel('catalog/product');
        $productObj = $productModel->load($item['product_id']);
        $quoteItem = Mage::getModel('sales/quote_item')->setProduct($productObj);
        $quoteItem->setBasePrice($item['price']);
        $quoteItem->setPriceInclTax($item['price']);
        $quoteItem->setData('original_price', $item['price']);
        $quoteItem->setData('price', $item['price']);
        $quoteItem->setRowTotal($tempPrice);
        $quoteItem->setQuote($quoteObj);
        // $quoteItem->setQty($item['qty']);
        $quoteItem->setQty(1);
        $quoteItem->setStoreId($storeId);
        $quoteItem->setOptions($item['opt']);
        $quoteItem->setTaxAmount($item['tax']);
        $quoteItem->setTaxClassId($item['tax_class_id']);
        $quoteItem->setTaxPercent($item['tax_percent']);

        $quoteItem->setCouponCode($item['coupon_code']);
        $quoteItem->setDiscountAmount($item['discount_amount']);
        $quoteItem->setBaseDiscountAmount($item['discount_amount']);
        $quoteItem->setDiscountDescription($item['discount_description']);

        $quoteObj->addItem($quoteItem);

        // $quoteObj->setCouponCode($item['coupon_code']);
        // $quoteObj->setDiscountAmount($item['discount_amount']);
        // $quoteObj->setBaseDiscountAmount($item['discount_amount']);
        // $quoteObj->setDiscountDescription($item['discount_description']);

        $quoteObj->setSubtotalWithDiscount($tempPrice - $discountAmount);
        $quoteObj->setBaseSubtotalWithDiscount($tempPrice - $discountAmount);

        $quoteObj->setBaseTaxAmount($item['tax']);
        $quoteObj->setTaxAmount($item['tax']);
        $quoteObj->setBaseSubtotal($item['price'] - $discountAmount);
        $quoteObj->setSubtotal($tempPrice - $discountAmount);
        $quoteObj->setBaseGrandTotal($tempTax);
        $quoteObj->setGrandTotal($tempTax);
        $quoteObj->setCustomPrice($tempPrice);
        $quoteObj->setOriginalCustomPrice($tempPrice);
        $quoteObj->setStoreId($storeId);
        $quoteObj->collectTotals();
        $quoteObj->save();
        return array($tempPrice, $tempTax);
    }

    /**
     * @param $order
     * @param $quoteObj
     */
    protected function dispatchOrder($order, $quoteObj)
    {
        if ($order) {
            Mage::dispatchEvent(
                'checkout_type_onepage_save_order_after', array(
                    'order' => $order,
                    'quote' => $quoteObj
                )
            );
            $quoteObj->removeAllItems();
            $quoteObj->setTotalsCollectedFlag(false);
            $quoteObj->collectTotals();
        }
    }
}
