<?php
class Neo_Customapiv3_Model_Observer
{
	public function saveEncodedMd5Token($observer)
	{
		$customer = $observer->getEvent()->getCustomer();
		$customer->setMd5CustomerId(md5($customer->getEmail()));
	}
}
?>