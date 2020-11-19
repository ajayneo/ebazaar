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
class Sunarc_Splitorder_Model_Observer
{
	/**
	* Rewrite necessary classes
	*
	* @param Varien_Event_Observer $observer
	*/
	public function rewriteClasses(Varien_Event_Observer $observer)
	{
		$quote =  Mage::getSingleton('checkout/session')->getQuote();
		$methodCode = $quote->getPayment()->getMethod();
		$grandtotal = $quote->getGrandTotal();
		$max = 50000;

		$isRewriteEnabled = Mage::getStoreConfig('splitorder/options/enable');
		if ($isRewriteEnabled && $methodCode == 'cashondelivery' && $grandtotal > $max) {

			Mage::getConfig()->setNode('global/models/checkout/rewrite/type_onepage',
		'Sunarc_Splitorder_Model_Checkout_Type_Onepage');

		}
	}
}
