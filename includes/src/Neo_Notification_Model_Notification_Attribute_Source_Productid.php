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
 * Admin source model for Product
 *
 * @category    Neo
 * @package     Neo_Notification
 * @author      Ultimate Module Creator
 */
class Neo_Notification_Model_Notification_Attribute_Source_Productid extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * get possible values
     *
     * @access public
     * @param bool $withEmpty
     * @param bool $defaultValues
     * @return array
     * @author Ultimate Module Creator
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
       /* $source  = Mage::getModel('eav/config')->getAttribute('catalog_product', 'color');
        return $source->getSource()->getAllOptions($withEmpty, $defaultValues);*/
        $options = array();
        
        $productArray = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect(array('entity_id','name'))
                ->load()
                ->toArray();

        foreach ($productArray as $productId => $product) {
            if (isset($product['name'])) {
                $options[] = array(
                    'label' => $product['name'],
                    'value' => $productId
                );

            }
        }
        return $options;
    }

    /**
     * get options as array
     *
     * @access public
     * @param bool $withEmpty
     * @return string
     * @author Ultimate Module Creator
     */
    public function getOptionsArray($withEmpty = true)
    {
        $options = array();
        foreach ($this->getAllOptions($withEmpty) as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    /**
     * get option text
     *
     * @access public
     * @param mixed $value
     * @return string
     * @author Ultimate Module Creator
     */
    public function getOptionText($value)
    {
        $options = $this->getOptionsArray();
        if (!is_array($value)) {
            $value = explode(',', $value);
        }
        $texts = array();
        foreach ($value as $v) {
            if (isset($options[$v])) {
                $texts[] = $options[$v];
            }
        }
        return implode(', ', $texts);
    }
}
