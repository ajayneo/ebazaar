<?php

/**
 * Controller Creatuitycorp_Shareyourpurchase_Adminhtml_StatisticsController
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_Adminhtml_StatisticsController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->_title($this->__('Statistics'));
        $this->loadLayout();
        $this->renderLayout();
    }

    public function massDeleteAction() {
        $entityIds = $this->getRequest()->getParam('entity_id');
        if (!is_array($entityIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Please select entity(es).'));
        } else {
            try {
                $rateModel = Mage::getModel('shareyourpurchase/statistics');
                foreach ($entityIds as $taxId) {
                    $rateModel->load($taxId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('shareyourpurchase')->__(
                                'Total of %d record(s) were deleted.', count($entityIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

}