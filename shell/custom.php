<?php
require_once 'abstract.php';
class Mage_Shell_Custom extends Mage_Shell_Abstract
{
    public function run() {
        $quoteShippingAddress = Mage::getResourceModel('sales/quote_address_collection')
							->addFieldToSelect('postcode')
							->addFieldToFilter('address_type','shipping')
							->addFieldToFilter('quote_id',211867)
							->getFirstItem();

        echo $quoteShippingAddress['postcode']; exit;
    }
    
}
$shell = new Mage_Shell_Custom();
$shell->run();
