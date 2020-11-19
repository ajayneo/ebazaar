<?php
class Devinc_Multipledeals_Block_Rss_Catalog_Special extends Mage_Rss_Block_Catalog_Special
{
    const STATUS_RUNNING = Devinc_Multipledeals_Model_Source_Status::STATUS_RUNNING;

    protected function _toHtml()
    {
         //store id is store view id
        $storeId = $this->_getStoreId();
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();

        //customer group id
        $customerGroupId = $this->_getCustomerGroupId();

        $product = Mage::getModel('catalog/product');

        $fields = array(
            'final_price',
            'price'
        );

        //load active deal ids
        $dealCollection = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', array('eq'=>self::STATUS_RUNNING));
        $dealProductIds = array(0);   
        $dealData = array();      
        if (count($dealCollection)) {     
            foreach ($dealCollection as $deal) {
                if (Mage::helper('multipledeals')->runOnStore($deal, $storeId) && !array_key_exists($deal->getProductId(), $dealData)) {
                    $dealData[$deal->getProductId()]['final_price'] = $deal->getDealPrice();
                    $dealData[$deal->getProductId()]['special_to_date'] = $deal->getDatetimeTo();
                    $dealProductIds[] = $deal->getProductId();
                }
            }
        }

        $resource = Mage::getSingleton('core/resource');
        $specials = $product->setStoreId($storeId)->getResourceCollection()            
            ->addPriceDataFieldFilter('%s < %s', $fields)
            ->addPriceData($customerGroupId, $websiteId);

        $mergedIds = array_merge($specials->getAllIds(), $dealProductIds);
        $specials = $product->setStoreId($storeId)->getResourceCollection()    
            ->addFieldToFilter('entity_id', $mergedIds)
            ->addPriceData($customerGroupId, $websiteId)
            ->addAttributeToSelect(
                    array('name', 'short_description', 'description', 'price', 'thumbnail',
                    'special_price', 'special_to_date'), 
                'left')
            ->addAttributeToSort('name', 'asc');

        $newurl = Mage::getUrl('rss/catalog/special/store_id/' . $storeId);
        $title = Mage::helper('rss')->__('%s - Special Products', Mage::app()->getStore()->getFrontendName());
        $lang = Mage::getStoreConfig('general/locale/code');

        $rssObj = Mage::getModel('rss/rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                'language'    => $lang
                );
        $rssObj->_addHeader($data);

        $results = array();
        /*
        using resource iterator to load the data one by one
        instead of loading all at the same time. loading all data at the same time can cause the big memory allocation.
        */
        Mage::getSingleton('core/resource_iterator')
            ->walk($specials->getSelect(), array(array($this, 'addSpecialXmlCallback')), array('rssObj'=> $rssObj, 'results'=> &$results));

        if (sizeof($results)>0) {
            foreach($results as $result){
                // render a row for RSS feed
                $product->setData($result);
                $html = sprintf('<table><tr>
                    <td><a href="%s"><img src="%s" alt="" border="0" align="left" height="75" width="75" /></a></td>
                    <td style="text-decoration:none;">%s',
                    $product->getProductUrl(),
                    $this->helper('catalog/image')->init($product, 'thumbnail')->resize(75, 75),
                    $this->helper('catalog/output')->productAttribute($product, $product->getDescription(), 'description')
                );

                // add price data if needed
                if ($product->getAllowedPriceInRss()) {
                    $special = '';
                    if (in_array($product->getId(), $dealProductIds)) {
                        $special = '<br />' . Mage::helper('catalog')->__('Special Expires On: %s', $this->formatDate($dealData[$product->getId()]['special_to_date'], Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM));
                        $html .= sprintf('<p>%s %s%s</p>',
                            Mage::helper('catalog')->__('Price: %s', Mage::helper('core')->currency($result['price'])),
                            Mage::helper('catalog')->__('Special Price: %s', Mage::helper('core')->currency($dealData[$product->getId()]['final_price'])),
                            $special
                        );
                    } else {
                        if ($result['use_special']) {
                            $special = '<br />' . Mage::helper('catalog')->__('Special Expires On: %s', $this->formatDate($result['special_to_date'], Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM));
                        }
                        $html .= sprintf('<p>%s %s%s</p>',
                            Mage::helper('catalog')->__('Price: %s', Mage::helper('core')->currency($result['price'])),
                            Mage::helper('catalog')->__('Special Price: %s', Mage::helper('core')->currency($result['final_price'])),
                            $special
                        );
                    }
                }

                $html .= '</td></tr></table>';

                $rssObj->_addEntry(array(
                    'title'       => $product->getName(),
                    'link'        => $product->getProductUrl(),
                    'description' => $html
                ));
            }
        }
        return $rssObj->createRssXml();
    }
}
