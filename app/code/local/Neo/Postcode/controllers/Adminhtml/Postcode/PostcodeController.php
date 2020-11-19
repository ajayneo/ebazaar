<?php
class Neo_Postcode_Adminhtml_Postcode_PostcodeController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function gridAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function newAction()
	{
		$this->_forward('edit');
	}
	
	protected function _initPostcode()
	{
		$postcodeId = $this->getRequest()->getParam('id');
		$postcode = Mage::getModel('postcode/postcode')->load($postcodeId);
		if($postcodeId) {
			$postcode->load($postcodeId);
		}
		Mage::register('current_postcode',$postcode);
		return $postcode;
	}
	
	public function editAction()
	{
		$postcodeId = $this->getRequest()->getParam('id');
		$postcode = $this->_initPostcode();
		if ($postcodeId && !$postcode->getId()) {
            $this->_getSession()->addError(Mage::helper('postcode')->__('This Postcode no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getPostcodeData(true);
        if (!empty($data)) {
            $postcode->setData($data);
        }
        Mage::register('postcode_data', $postcode);
        $this->loadLayout();

        $this->renderLayout();
	}
	
	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost('postcode')) 
		{
            try {
                $postcode = $this->_initPostcode();

                $postcode->addData($data);
                
                $postcode->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('postcode')->__('Postcode was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $account->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e) {
                
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPostcodeData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('postcode')->__('There was a problem saving the postcode.'));
                Mage::getSingleton('adminhtml/session')->setAccountData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('postcode')->__('Unable to find postcode to save.'));
        $this->_redirect('*/*/');
	}
}
?>
