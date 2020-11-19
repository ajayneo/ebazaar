<?php class Neo_ASM_Model_Observer
{
    public function checkoutTypeOnepageSaveOrder(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $param = Mage::app()->getRequest()->getParam('assisted_by_asm', '');
        $observer->getOrder()->setAssistedByArm((int)$param);
    }
}