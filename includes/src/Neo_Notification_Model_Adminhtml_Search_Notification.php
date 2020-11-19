<?php
/**
 * Neo_Notification extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Neo
 * @package        Neo_Notification
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Admin search model
 *
 * @category    Neo
 * @package     Neo_Notification
 * @author      Ultimate Module Creator
 */
class Neo_Notification_Model_Adminhtml_Search_Notification extends Varien_Object
{
    /**
     * Load search results
     *
     * @access public
     * @return Neo_Notification_Model_Adminhtml_Search_Notification
     * @author Ultimate Module Creator
     */
    public function load()
    {
        $arr = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('neo_notification/notification_collection')
            ->addFieldToFilter('title', array('like' => $this->getQuery().'%'))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        foreach ($collection->getItems() as $notification) {
            $arr[] = array(
                'id'          => 'notification/1/'.$notification->getId(),
                'type'        => Mage::helper('neo_notification')->__('Notification'),
                'name'        => $notification->getTitle(),
                'description' => $notification->getTitle(),
                'url' => Mage::helper('adminhtml')->getUrl(
                    '*/notification_notification/edit',
                    array('id'=>$notification->getId())
                ),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
