<?php
class Mage_Adminhtml_Block_Sales_Order_Renderer_CustomerGroup extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    // Holds an associative array with customer_group_id and the associated label
    private static $_customerGroups = array(); // "singleton"

    public static function getCustomerGroupsArray() {
        // Make sure the static property is only populated once
        if (count(self::$_customerGroups) == 0) {
            $customer_group = new Mage_Customer_Model_Group();
            $customer_groups = $customer_group->getCollection()->toOptionHash();
            self::$_customerGroups = $customer_groups;
        }

        return self::$_customerGroups;
    }

    // Transforms the customer_group_id into corresponding label
    public function render(Varien_Object $row)
    {
        $val = $this->_getValue($row);
        $customer_groups = self::getCustomerGroupsArray();
        return isset($customer_groups[$val]) ? $customer_groups[$val] : false;
    }

}