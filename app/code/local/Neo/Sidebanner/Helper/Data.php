<?php //code revised to remove side banner store config dependencey
//Date: 20-Feb-2018
//Auth: Mahesh Gurav
class Neo_Sidebanner_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getNewMobile()
	{
		$proid = Mage::getStoreConfig('sidebanner_section/deals/newmobile');
		return $proIdArr = explode(',', $proid);
	}
	public function getNewLaptop()
	{
		$proid = Mage::getStoreConfig('sidebanner_section/deals/newlaptop');
		return $proIdArr = explode(',', $proid);
	}

	public function getOpenMobile()
	{
		$proid = Mage::getStoreConfig('sidebanner_section/deals/openmobile');
		return $proIdArr = explode(',', $proid);
	}

	public function getPreOwnedMobile()
	{
		$proid = Mage::getStoreConfig('sidebanner_section/deals/preownmob');
		return $proIdArr = explode(',', $proid);
	}

	public function getPreOwnedLap()
	{
		$proid = Mage::getStoreConfig('sidebanner_section/deals/preownlap');
		return $proIdArr = explode(',', $proid);
	}

	public function getAccessories()
	{
		$proid = Mage::getStoreConfig('sidebanner_section/deals/access');
		return $proIdArr = explode(',', $proid);
	}

	public function getDealsHeaderImage()
	{
		return  Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'homepagebanner/images/'.Mage::getStoreConfig('sidebanner_section/deals/image');
	}

	public function dailyOrderValue()
	{
		$orderTotals = Mage::getModel('sales/order')->getCollection()
		    ->addAttributeToFilter('created_at', array('from'  => date('Y-m-d')))
		    ->addAttributeToSelect('base_grand_total')
		    ->getColumnValues('base_grand_total');
		$totalSum = array_sum($orderTotals);
		return $this->amountformat($totalSum);
	}

	public function getRegisterUser()
	{
		$collection = Mage::getModel('customer/customer')->getCollection();
		$collection->addAttributeToFilter('created_at', array('from'  => date('Y-m-d')))
		->addAttributeToSelect('entity_id')
		->getColumnValues('entity_id');
		$count = 0;
		if(count($collection) > 0){
			$count = count($collection);
		}
		return $count;
	} 

	public function getTopFiveAffiliate()
	{
		
		$totalSalesAffiliate = array();
		$collection = Mage::getResourceModel('sales/order_grid_collection')
		    ->addAttributeToFilter('created_at', array('from'  => date('Y-m-d')));

		foreach($collection as $order)
		{ 
			$totalSalesAffiliate[$order['shipping_name']] = $totalSalesAffiliate[$order['shipping_name']]+$order['base_grand_total'];
		}

		arsort($totalSalesAffiliate);
		return array_slice($totalSalesAffiliate, 0, 5, true);
	}  

	public function noOfUniquePartherBilledMonth()
	{
		$collection = Mage::getResourceModel('sales/order_grid_collection')
		    ->addAttributeToFilter('created_at', array('like'  => date('Y-m').'%'));

		$totalSalesAffiliate = array();

		foreach($collection as $order)  
		{ 			     
				$totalSalesAffiliate[$order['shipping_name']] = $totalSalesAffiliate[$order['shipping_name']]+$order['grand_total'];
				
		}

		return count($totalSalesAffiliate);
	}

	public function noOfUniquePartherBilled()
	{
		$collection = Mage::getResourceModel('sales/order_grid_collection')
		    ->addAttributeToFilter('created_at', array('from'  => date('Y-m-d')));

		$totalSalesAffiliate = array();

		foreach($collection as $order)
		{ 
			$totalSalesAffiliate[$order['shipping_name']] = $totalSalesAffiliate[$order['shipping_name']]+$order['grand_total'];
				
		}

		return count($totalSalesAffiliate);
	}

	public function amountformat($amt)
	{
		$amount =  Mage::getModel('directory/currency')->format($amt);
		return str_replace('$', 'Rs', $amount);
	}

	public function topFiveBillingCity()
	{
		$collection = Mage::getResourceModel('sales/order_grid_collection')
		    ->addAttributeToFilter('created_at', array('from'  => date('Y-m-d')));

		$topFiveBillingCity = array();

		foreach($collection as $order)
		{ 
			$order = Mage::getModel('sales/order')->loadByIncrementId($order['increment_id']);
			$billingAddress = $order->getBillingAddress()->getData();
			
			$topFiveBillingCity[$billingAddress['city'].','.$billingAddress['region']] = $topFiveBillingCity[$billingAddress['city'].','.$billingAddress['region']] + $order['grand_total'];
				
		}

		arsort($topFiveBillingCity);
		return array_slice($topFiveBillingCity, 0, 5, true);
	}

	public function topFivebrandsunboxed()
	{
        $storeId = 1;
        $collection = Mage::getResourceModel('reports/product_collection')
            ->addOrderedQty()
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)
            ->setOrder('ordered_qty', 'desc');
        if (Mage::helper('catalog/product_flat')->isEnabled()) {
            $collection->getSelect()
                ->joinInner(array('e2' => 'catalog_product_flat_' . $storeId), 'e2.entity_id = e.entity_id');
        } else {
            $collection->addAttributeToSelect('*')
                ->addAttributeToSelect(array('name', 'price', 'small_image','brands'));
            
        }
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        if ($categoryId = 4) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            $collection->addCategoryFilter($category);
        }
 	
 		$i=0;
 		$return = array();

        foreach ($collection as $p) {
        	if($i<5)
        	{
        		$return[$p->getEntityId()]['brands'] = $p->getAttributeText('brands');
	        	$return[$p->getEntityId()]['qty'] = $p->getOrderedQty();
	        	$return[$p->getEntityId()]['item'] = $p->getOrderItemsName();
	        	$i++;
        	}
        	else
        	{
        		break;
        	}
        }

        return $return;
	}  
  	
  	public function getAccessoriesCollection()
  	{
  		$proid = Mage::getStoreConfig('sidebanner_section/deals/access');
		return $proIdArr = explode(',', $proid);
  	}

	public function getopenBoxCollection()
	{
		$proid = Mage::getStoreConfig('sidebanner_section/deals/open');
		return $proIdArr = explode(',', $proid);
	}

	public function getsealPackCollection()
	{
		$proid = Mage::getStoreConfig('sidebanner_section/deals/seal');
		return $proIdArr = explode(',', $proid);
	}

	public function getPreOwned()
	{
		$proid = Mage::getStoreConfig('sidebanner_section/deals/preownmob');
		return $proIdArr = explode(',', $proid);
	}

	
}
?>