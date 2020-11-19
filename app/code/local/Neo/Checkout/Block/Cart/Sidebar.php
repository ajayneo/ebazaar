<?php
	class Neo_Checkout_Block_Cart_Sidebar extends Mage_Checkout_Block_Cart_Sidebar
	{
		public function getItemCount()
		{
			$count = $this->getData('item_count');
			if (is_null($count)) {
				$count = Mage::helper('checkout/cart')->getCart()->getItemsCount();
				$this->setData('item_count', $count);
			}
			return $count;
		}
		
		/*
		 * @description : This function returns the total payable amount in the minicart header
		 * @author : Pradeep Sanku
		 */
		public function getGrandTotalsNeo(){
			$quote = Mage::getModel('checkout/cart')->getQuote();
			return $quote->getGrandTotal();
		}
	}
?>
