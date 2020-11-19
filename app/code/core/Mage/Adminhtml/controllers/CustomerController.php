<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer admin controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_CustomerController extends Mage_Adminhtml_Controller_Action
{

    protected function _initCustomer($idFieldName = 'id')
    {
        $this->_title($this->__('Customers'))->_title($this->__('Manage Customers'));

        $customerId = (int) $this->getRequest()->getParam($idFieldName);
        $customer = Mage::getModel('customer/customer');

        if ($customerId) {
            $customer->load($customerId);
        }

        Mage::register('current_customer', $customer);
        return $this;
    }

    /**
     * Customers list action
     */
    public function indexAction()
    {
        $this->_title($this->__('Customers'))->_title($this->__('Manage Customers'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->loadLayout();

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('customer/manage');

        /**
         * Append customers block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/customer', 'customer')
        );

        /**
         * Add breadcrumb item
         */
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Customers'), Mage::helper('adminhtml')->__('Customers'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Customers'), Mage::helper('adminhtml')->__('Manage Customers'));

        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Customer edit action
     */
    public function editAction()
    {
        $this->_initCustomer();
        $this->loadLayout();

        /* @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::registry('current_customer');

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getCustomerData(true);

        // restore data from SESSION
        if ($data) {
            $request = clone $this->getRequest();
            $request->setParams($data);

            if (isset($data['account'])) {
                /* @var $customerForm Mage_Customer_Model_Form */
                $customerForm = Mage::getModel('customer/form');
                $customerForm->setEntity($customer)
                    ->setFormCode('adminhtml_customer')
                    ->setIsAjaxRequest(true);
                $formData = $customerForm->extractData($request, 'account');
                $customerForm->restoreData($formData);
            }

            if (isset($data['address']) && is_array($data['address'])) {
                /* @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('adminhtml_customer_address');

                foreach (array_keys($data['address']) as $addressId) {
                    if ($addressId == '_template_') {
                        continue;
                    }

                    $address = $customer->getAddressItemById($addressId);
                    if (!$address) {
                        $address = Mage::getModel('customer/address');
                        $customer->addAddress($address);
                    }

                    $formData = $addressForm->setEntity($address)
                        ->extractData($request);
                    $addressForm->restoreData($formData);
                }
            }
        }

        $this->_title($customer->getId() ? $customer->getName() : $this->__('New Customer'));

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('customer/new');

        $this->renderLayout();
    }

    /**
     * Create new customer action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Delete customer action
     */
    public function deleteAction()
    {
        $this->_initCustomer();
        $customer = Mage::registry('current_customer');
        if ($customer->getId()) {
            try {
                $customer->load($customer->getId());
                $customer->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The customer has been deleted.'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/customer');
    }

    /**
     * Save customer action
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $redirectBack = $this->getRequest()->getParam('back', false);
            $this->_initCustomer('customer_id');

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::registry('current_customer');

            /** @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setEntity($customer)
                ->setFormCode('adminhtml_customer')
                ->ignoreInvisible(false)
            ;

            $formData = $customerForm->extractData($this->getRequest(), 'account');

            // Handle 'disable auto_group_change' attribute
            if (isset($formData['disable_auto_group_change'])) {
                $formData['disable_auto_group_change'] = empty($formData['disable_auto_group_change']) ? '0' : '1';
            }

            $errors = $customerForm->validateData($formData);
            if ($errors !== true) {
                foreach ($errors as $error) {
                    $this->_getSession()->addError($error);
                }
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id' => $customer->getId())));
                return;
            }

            $customerForm->compactData($formData);

            // Unset template data
            if (isset($data['address']['_template_'])) {
                unset($data['address']['_template_']);
            }

            $modifiedAddresses = array();
            if (!empty($data['address'])) {
                /** @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('adminhtml_customer_address')->ignoreInvisible(false);

                foreach (array_keys($data['address']) as $index) {
                    $address = $customer->getAddressItemById($index);
                    if (!$address) {
                        $address = Mage::getModel('customer/address');
                    }

                    $requestScope = sprintf('address/%s', $index);
                    $formData = $addressForm->setEntity($address)
                        ->extractData($this->getRequest(), $requestScope);

                    // Set default billing and shipping flags to address
                    $isDefaultBilling = isset($data['account']['default_billing'])
                        && $data['account']['default_billing'] == $index;
                    $address->setIsDefaultBilling($isDefaultBilling);
                    $isDefaultShipping = isset($data['account']['default_shipping'])
                        && $data['account']['default_shipping'] == $index;
                    $address->setIsDefaultShipping($isDefaultShipping);

                    $errors = $addressForm->validateData($formData);
                    if ($errors !== true) {
                        foreach ($errors as $error) {
                            $this->_getSession()->addError($error);
                        }
                        $this->_getSession()->setCustomerData($data);
                        $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array(
                            'id' => $customer->getId())
                        ));
                        return;
                    }

                    $addressForm->compactData($formData);

                    // Set post_index for detect default billing and shipping addresses
                    $address->setPostIndex($index);

                    if ($address->getId()) {
                        $modifiedAddresses[] = $address->getId();
                    } else {
                        $customer->addAddress($address);
                    }
                }
            }

            // Default billing and shipping
            if (isset($data['account']['default_billing'])) {
                $customer->setData('default_billing', $data['account']['default_billing']);
            }
            if (isset($data['account']['default_shipping'])) {
                $customer->setData('default_shipping', $data['account']['default_shipping']);
            }
            if (isset($data['account']['confirmation'])) {
                $customer->setData('confirmation', $data['account']['confirmation']);
            }

            // Mark not modified customer addresses for delete
            foreach ($customer->getAddressesCollection() as $customerAddress) {
                if ($customerAddress->getId() && !in_array($customerAddress->getId(), $modifiedAddresses)) {
                    $customerAddress->setData('_deleted', true);
                }
            }

            if (Mage::getSingleton('admin/session')->isAllowed('customer/newsletter')
                && !$customer->getConfirmation()
            ) {
                $customer->setIsSubscribed(isset($data['subscription']));
            }

            if (isset($data['account']['sendemail_store_id'])) {
                $customer->setSendemailStoreId($data['account']['sendemail_store_id']);
            }

            $isNewCustomer = $customer->isObjectNew();
            try {
                $sendPassToEmail = false;
                // Force new customer confirmation
                if ($isNewCustomer) {
                    $customer->setPassword($data['account']['password']);
                    $customer->setForceConfirmed(true);
                    if ($customer->getPassword() == 'auto') {
                        $sendPassToEmail = true;
                        $customer->setPassword($customer->generatePassword());
                    }
                }

                Mage::dispatchEvent('adminhtml_customer_prepare_save', array(
                    'customer'  => $customer,
                    'request'   => $this->getRequest()
                ));

                $customer->save();

                // Send welcome email
                if ($customer->getWebsiteId() && (isset($data['account']['sendemail']) || $sendPassToEmail)) {
                    $storeId = $customer->getSendemailStoreId();
                    if ($isNewCustomer) {
                        $customer->sendNewAccountEmail('registered', '', $storeId);
                    } elseif ((!$customer->getConfirmation())) {
                        // Confirm not confirmed customer
                        $customer->sendNewAccountEmail('confirmed', '', $storeId);
                    }
                }

                if (!empty($data['account']['new_password'])) {
                    $newPassword = $data['account']['new_password'];
                    if ($newPassword == 'auto') {
                        $newPassword = $customer->generatePassword();
                    }
                    $customer->changePassword($newPassword);
                    $customer->sendPasswordReminderEmail();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The customer has been saved.')
                );
                Mage::dispatchEvent('adminhtml_customer_save_after', array(
                    'customer'  => $customer,
                    'request'   => $this->getRequest()
                ));

                if ($redirectBack) {
                    $this->_redirect('*/*/edit', array(
                        'id' => $customer->getId(),
                        '_current' => true
                    ));
                    return;
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id' => $customer->getId())));
            } catch (Exception $e) {
                // echo $e->getMessage(); exit;
                $this->_getSession()->addException($e,
                    Mage::helper('adminhtml')->__('An error occurred while saving the customer.'));
                $this->_getSession()->setCustomerData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/customer/edit', array('id'=>$customer->getId())));
                return;
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/customer'));
    }

    /**
     * Export customer grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'customers.csv';
        $content    = $this->getLayout()->createBlock('adminhtml/customer_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export customer grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName   = 'customers.xml';
        $content    = $this->getLayout()->createBlock('adminhtml/customer_grid')
            ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Prepare file download response
     *
     * @todo remove in 1.3
     * @deprecated please use $this->_prepareDownloadResponse()
     * @see Mage_Adminhtml_Controller_Action::_prepareDownloadResponse()
     * @param string $fileName
     * @param string $content
     * @param string $contentType
     */
    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $this->_prepareDownloadResponse($fileName, $content, $contentType);
    }

    /**
     * Customer orders grid
     *
     */
    public function ordersAction() {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Customer last orders grid for ajax
     *
     */
    public function lastOrdersAction() {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Customer newsletter grid
     *
     */
    public function newsletterAction()
    {
        $this->_initCustomer();
        $subscriber = Mage::getModel('newsletter/subscriber')
            ->loadByCustomer(Mage::registry('current_customer'));

        Mage::register('subscriber', $subscriber);
        $this->loadLayout();
        $this->renderLayout();
    }

    public function wishlistAction()
    {
        $this->_initCustomer();
        $customer = Mage::registry('current_customer');
        if ($customer->getId()) {
            if($itemId = (int) $this->getRequest()->getParam('delete')) {
                try {
                    Mage::getModel('wishlist/item')->load($itemId)
                        ->delete();
                }
                catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }

        $this->getLayout()->getUpdate()
            ->addHandle(strtolower($this->getFullActionName()));
        $this->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();

        $this->renderLayout();
    }

    /**
     * Customer last view wishlist for ajax
     *
     */
    public function viewWishlistAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * [Handle and then] get a cart grid contents
     *
     * @return string
     */
    public function cartAction()
    {
        $this->_initCustomer();
        $websiteId = $this->getRequest()->getParam('website_id');

        // delete an item from cart
        $deleteItemId = $this->getRequest()->getPost('delete');
        if ($deleteItemId) {
            $quote = Mage::getModel('sales/quote')
                ->setWebsite(Mage::app()->getWebsite($websiteId))
                ->loadByCustomer(Mage::registry('current_customer'));
            $item = $quote->getItemById($deleteItemId);
            if ($item && $item->getId()) {
                $quote->removeItem($deleteItemId);
                $quote->collectTotals()->save();
            }
        }

        $this->loadLayout();
        $this->getLayout()->getBlock('admin.customer.view.edit.cart')->setWebsiteId($websiteId);
        $this->renderLayout();
    }

    /**
     * Get shopping cart to view only
     *
     */
    public function viewCartAction()
    {
        $this->_initCustomer();
        $this->loadLayout()
            ->getLayout()
            ->getBlock('admin.customer.view.cart')
            ->setWebsiteId($this->getRequest()->getParam('website_id'));
        $this->renderLayout();
    }

    /**
     * Get shopping carts from all websites for specified client
     *
     */
    public function cartsAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Get customer's product reviews list
     *
     */
    public function productReviewsAction()
    {
        $this->_initCustomer();
        $this->loadLayout()
            ->getLayout()
            ->getBlock('admin.customer.reviews')
            ->setCustomerId(Mage::registry('current_customer')->getId())
            ->setUseAjax(true);
        $this->renderLayout();
    }

    /**
     * Get customer's tags list
     *
     */
    public function productTagsAction()
    {
        $this->_initCustomer();
        $this->loadLayout()
            ->getLayout()
            ->getBlock('admin.customer.tags')
            ->setCustomerId(Mage::registry('current_customer')->getId())
            ->setUseAjax(true);
        $this->renderLayout();
    }

    public function tagGridAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->getLayout()->getBlock('admin.customer.tags')->setCustomerId(
            Mage::registry('current_customer')
        );
        $this->renderLayout();
    }

    public function validateAction()
    {
        $response       = new Varien_Object();
        $response->setError(0);
        $websiteId      = Mage::app()->getStore()->getWebsiteId();
        $accountData    = $this->getRequest()->getPost('account');

        $customer = Mage::getModel('customer/customer');
        $customerId = $this->getRequest()->getParam('id');
        if ($customerId) {
            $customer->load($customerId);
            $websiteId = $customer->getWebsiteId();
        } else if (isset($accountData['website_id'])) {
            $websiteId = $accountData['website_id'];
        }

        /* @var $customerForm Mage_Customer_Model_Form */
        $customerForm = Mage::getModel('customer/form');
        $customerForm->setEntity($customer)
            ->setFormCode('adminhtml_customer')
            ->setIsAjaxRequest(true)
            ->ignoreInvisible(false)
        ;

        $data   = $customerForm->extractData($this->getRequest(), 'account');
        $errors = $customerForm->validateData($data);
        if ($errors !== true) {
            foreach ($errors as $error) {
                $this->_getSession()->addError($error);
            }
            $response->setError(1);
        }

        # additional validate email
        if (!$response->getError()) {
            # Trying to load customer with the same email and return error message
            # if customer with the same email address exisits
            $checkCustomer = Mage::getModel('customer/customer')
                ->setWebsiteId($websiteId);
            $checkCustomer->loadByEmail($accountData['email']);
            if ($checkCustomer->getId() && ($checkCustomer->getId() != $customer->getId())) {
                $response->setError(1);
                $this->_getSession()->addError(
                    Mage::helper('adminhtml')->__('Customer with the same email already exists.')
                );
            }
        }

        $addressesData = $this->getRequest()->getParam('address');
        if (is_array($addressesData)) {
            /* @var $addressForm Mage_Customer_Model_Form */
            $addressForm = Mage::getModel('customer/form');
            $addressForm->setFormCode('adminhtml_customer_address')->ignoreInvisible(false);
            foreach (array_keys($addressesData) as $index) {
                if ($index == '_template_') {
                    continue;
                }
                $address = $customer->getAddressItemById($index);
                if (!$address) {
                    $address   = Mage::getModel('customer/address');
                }

                $requestScope = sprintf('address/%s', $index);
                $formData = $addressForm->setEntity($address)
                    ->extractData($this->getRequest(), $requestScope);

                $errors = $addressForm->validateData($formData);
                if ($errors !== true) {
                    foreach ($errors as $error) {
                        $this->_getSession()->addError($error);
                    }
                    $response->setError(1);
                }
            }
        }

        if ($response->getError()) {
            $this->_initLayoutMessages('adminhtml/session');
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }

    public function massSubscribeAction()
    {
        $customersIds = $this->getRequest()->getParam('customer');
        if(!is_array($customersIds)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));

        } else {
            try {
                foreach ($customersIds as $customerId) {
                    $customer = Mage::getModel('customer/customer')->load($customerId);
                    $customer->setIsSubscribed(true);
                    $customer->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were updated.', count($customersIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massUnsubscribeAction()
    {
        $customersIds = $this->getRequest()->getParam('customer');
        if(!is_array($customersIds)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
        } else {
            try {
                foreach ($customersIds as $customerId) {
                    $customer = Mage::getModel('customer/customer')->load($customerId);
                    $customer->setIsSubscribed(false);
                    $customer->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were updated.', count($customersIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {
        $customersIds = $this->getRequest()->getParam('customer');
        if(!is_array($customersIds)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
        } else {
            try {
                $customer = Mage::getModel('customer/customer');
                foreach ($customersIds as $customerId) {
                    $customer->reset()
                        ->load($customerId)
                        ->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were deleted.', count($customersIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function massAssignGroupAction()
    {
        $customersIds = $this->getRequest()->getParam('customer');
        if(!is_array($customersIds)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
        } else {
            try {
                foreach ($customersIds as $customerId) {
                    $customer = Mage::getModel('customer/customer')->load($customerId);
                    $customer->setGroupId($this->getRequest()->getParam('group'));
                    $customer->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were updated.', count($customersIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function viewfileAction()
    {
        $file   = null;
        $plain  = false;
        if ($this->getRequest()->getParam('file')) {
            // download file
            $file   = Mage::helper('core')->urlDecode($this->getRequest()->getParam('file'));
        } else if ($this->getRequest()->getParam('image')) {
            // show plain image
            $file   = Mage::helper('core')->urlDecode($this->getRequest()->getParam('image'));
            $plain  = true;
        } else {
            return $this->norouteAction();
        }

        $path = Mage::getBaseDir('media') . DS . 'customer';

        $ioFile = new Varien_Io_File();
        $ioFile->open(array('path' => $path));
        $fileName   = $ioFile->getCleanPath($path . $file);
        $path       = $ioFile->getCleanPath($path);

        if ((!$ioFile->fileExists($fileName) || strpos($fileName, $path) !== 0)
            && !Mage::helper('core/file_storage')->processStorageFile(str_replace('/', DS, $fileName))
        ) {
            return $this->norouteAction();
        }

        if ($plain) {
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            switch (strtolower($extension)) {
                case 'gif':
                    $contentType = 'image/gif';
                    break;
                case 'jpg':
                    $contentType = 'image/jpeg';
                    break;
                case 'png':
                    $contentType = 'image/png';
                    break;
                default:
                    $contentType = 'application/octet-stream';
                    break;
            }

            $ioFile->streamOpen($fileName, 'r');
            $contentLength = $ioFile->streamStat('size');
            $contentModify = $ioFile->streamStat('mtime');

            $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Content-type', $contentType, true)
                ->setHeader('Content-Length', $contentLength)
                ->setHeader('Last-Modified', date('r', $contentModify))
                ->clearBody();
            $this->getResponse()->sendHeaders();

            while (false !== ($buffer = $ioFile->streamRead())) {
                echo $buffer;
            }
        } else {
            $name = pathinfo($fileName, PATHINFO_BASENAME);
            $this->_prepareDownloadResponse($name, array(
                'type'  => 'filename',
                'value' => $fileName
            ));
        }

        exit();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/manage');
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data['account'] = $this->_filterDates($data['account'], array('dob'));
        return $data;
    }

    public function exportCustomerAction()
    {
        ini_set('max_execution_time','1000');   
        ini_set('memory_limit', '-1');
        
        $customersIds = $this->getRequest()->getParam('customer');
        // $customersIds = array(3636,2626,3310,28544,15326,14606,17317,14085,15311,2694,5479,1342,15807,9044,14721,16002,1257,583,6457,5851,16435,17282,12587,3826,4636,17058,15408,6883,17039,10908,12700,3118,1343,16599,3146,5047,15808,2577,16367,4633,9293,3958,1445,17265,10152,15617,3286,15858,2543,14505,15950,16991,16443,14374,5118,2570,4948);
        // $customersIds = array(29624,29440,30467,16429,36758,13602,18823,18925,36791,15311,984,6449,15441,13906,10300,8171,31645,28800,2256,36748,29310,29646,3069,1257,22795,16668,16930,37607,36757,21059,6918,4684,30805,37393,4636,15263,37038,24122,2831,27089,2142,29492,9670,18188,29005,15225,27903,2810,29842,4759,12700,3838,28731,17327,17708,14555,37235,5267,16157,2830,20878,1908,16540,6909,34843,30228,3958,1466,6606,28837,3362,36529,8849,28118,18142,2837,20070,20157);
        $collectionObject = Mage::getModel('sales/order');

        if(!is_array($customersIds)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
        } else {
            try {
                $customers = Mage::getModel('customer/customer')->getCollection();
                $customers->addAttributeToSelect('*');
                $customers->addAttributeToFilter('entity_id',$customersIds);

                $i = 1;
                foreach ($customers as $key => $customer) {

                    $asm = '';
                    $asm_map = $customer->getResource()->getAttribute('asm_map')->getFrontend()->getValue($customer);
                    // var_dump($asm_map); exit;
                    if(strtolower($asm_map) !== 'no'){
                        $asm = $asm_map;
                    }
                    $customersArray[$i]['entity_id'] = $customer->getData('entity_id');
                    $customersArray[$i]['email'] = $customer->getData('email');
                    $customersArray[$i]['prefix'] = $customer->getData('prefix');
                    $customersArray[$i]['mobile'] = $customer->getData('mobile');
                    $customersArray[$i]['firstname'] = $customer->getData('firstname');
                    $customersArray[$i]['middlename'] = $customer->getData('middlename');
                    $customersArray[$i]['lastname'] = $customer->getData('lastname');
                    $customersArray[$i]['asm_map'] = $asm;
                    $customersArray[$i]['taxvat'] = $customer->getData('taxvat');
                    $customersArray[$i]['suffix'] = $customer->getData('suffix');
                    $customersArray[$i]['website_id'] = $customer->getData('website_id');
                    $customersArray[$i]['store_id'] = $customer->getData('store_id');
                    $customersArray[$i]['group_id'] = $customer->getData('group_id');
                    $customersArray[$i]['created_at'] = $customer->getData('created_at');
                    $customersArray[$i]['updated_at'] = $customer->getData('updated_at');
                    $customersArray[$i]['is_active'] = $customer->getData('is_active');
                    $customersArray[$i]['repcode'] = $customer->getData('repcode');
                    $customersArray[$i]['device_type'] = $customer->getData('device_type');
                    $customersArray[$i]['city'] = $customer->getData('cus_city');
                    $customersArray[$i]['bussiness_type'] = $customer->getResource()->getAttribute('business_type')->getFrontend()->getValue($customer);
                    $customersArray[$i]['gstin'] = $customer->getData('gstin');
                    $orders = $collectionObject->getCollection()->addFieldToFilter('customer_id',$customer->getData('entity_id'));   
                    $orderCnt = $orders->count();

                    $total=0;
                    $collectionOrders = $orders->addFieldToFilter('status','complete');
                   foreach($collectionOrders as $order){
                           $total += $order->getGrandTotal();
                   }
                       
                    
                    $customersArray[$i]['order_count'] = $orderCnt;

                    $customersArray[$i]['order_amount'] = 'Rs. '.$total; 
                 
                    $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($customersArray[$i]['email']);
                    $subscriberStatus = $subscriber->isSubscribed();
                 
                    $customersArray[$i]['is_subscribed'] = ($subscriberStatus? 1 : 0);

                    $billing_id = 0;
                    if($customer->getDefaultBilling()) {
                        $billing_id = $customer->getDefaultBilling();
                    }else{
                        foreach ($customer->getAddresses() as $address) {
                            $billing_id = $address->getId();
                            continue;
                        }
                    }

                    $shipping_id = 0;
                    if($customer->getDefaultBilling()) {
                        $shipping_id = $customer->getDefaultShippin();
                    }else{
                        foreach ($customer->getAddresses() as $address) {
                            $shipping_id = $address->getId();
                            continue;
                        }
                    }
                 
                    if($billing_id > 0) {
                    // if($customer->getDefaultBilling()) {
                        // $billingAddress = Mage::getModel('customer/address')->load($customer->getDefaultBilling());
                        $billingAddress = Mage::getModel('customer/address')->load($billing_id);
                        if($billingAddress->getId()) {

                            $customersArray[$i]['billing_store'] = $billingAddress->getData('company');
                            $customersArray[$i]['billing_firstname'] = $billingAddress->getData('firstname');
                            $customersArray[$i]['billing_lastname'] = $billingAddress->getData('lastname');
                            $customersArray[$i]['billing_street_1'] = str_replace(',', ' ', $billingAddress->getStreet1());
                            $customersArray[$i]['billing_street_2'] = str_replace(',', ' ', $billingAddress->getStreet2());
                            $customersArray[$i]['billing_city'] = $billingAddress->getData('city');
                            $customersArray[$i]['billing_region_id'] = $billingAddress->getData('region_id');
                            // $customersArray[$i]['billing_region'] = $this->stateCodeMapping($billingAddress->getData('region'));
                            $customersArray[$i]['billing_region'] = $billingAddress->getData('region');
                            $customersArray[$i]['billing_postcode'] = $billingAddress->getData('postcode');
                            $customersArray[$i]['billing_country_id'] = $billingAddress->getData('country_id');
                            $customersArray[$i]['billing_telephone'] = $billingAddress->getData('telephone');
                            //$customersArray[$i]['billing_company'] = $billingAddress->getData('company');
                        }
                    }
                    if($shipping_id > 0) {
                    // if($customer->getDefaultShipping()) {
                    //     $shippingAddress = Mage::getModel('customer/address')->load($customer->getDefaultShipping());
                        $shippingAddress = Mage::getModel('customer/address')->load($shipping_id);
                        if($shippingAddress->getId()) {
                            $customersArray[$i]['shipping_firstname'] = $shippingAddress->getData('firstname');
                            $customersArray[$i]['shipping_lastname'] = $shippingAddress->getData('lastname');
                            $customersArray[$i]['shipping_street_1'] = str_replace(',', ' ', $shippingAddress->getStreet1());
                            $customersArray[$i]['shipping_street_2'] = str_replace(',', ' ', $shippingAddress->getStreet2()); 
                            $customersArray[$i]['shipping_city'] = $shippingAddress->getData('city');
                            $customersArray[$i]['shipping_region_id'] = $shippingAddress->getData('region_id');
                            // $customersArray[$i]['shipping_region'] =    $this->stateCodeMapping($shippingAddress->getData('region'));
                            $customersArray[$i]['shipping_region'] =  $shippingAddress->getData('region');
                            $customersArray[$i]['shipping_postcode'] = $shippingAddress->getData('postcode');
                            $customersArray[$i]['shipping_country_id'] = $shippingAddress->getData('country_id');
                            $customersArray[$i]['shipping_telephone'] = $shippingAddress->getData('telephone');
                            //$customersArray[$i]['shipping_company'] = $shippingAddress->getData('company');
                        }
                    } 
                    $i++;   
                }

                $csvHead = array('entity_id','email','prefix','mobile','firstname','middlename','lastname','asm map','taxvat','suffix','website_id','store_id','group_id','created_at','updated_at','is_active','repcode','device_type','city','bussiness_type','GST IN','order_count','order_amount','is_subscribed','store_name','billing_firstname','billing_lastname','billing_street_1','billing_street_2','billing_city','billing_region_id','billing_region','billing_postcode','billing_country_id','billing_telephone','shipping_firstname','shipping_lastname','shipping_street_1','shipping_street_2','shipping_city','shipping_region_id','shipping_region','shipping_postcode','shipping_country_id','shipping_telephone'); 
   
                $this->convert_to_csv($csvHead,$customersArray, 'EB_CUSTOMER_DATA.csv', ',');  

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        //$this->_redirect('*/*/index');
    }

    protected function convert_to_csv($csvHead,$input_array, $output_file_name, $delimiter){
        /** open raw memory as file, no need for temp files, be careful not to run out of memory thought */
        $f = fopen('php://memory', 'w');
        /** loop through array  */
        fputcsv($f, $csvHead, $delimiter);
        foreach ($input_array as $line) {

            fputcsv($f, $line, $delimiter);
        }

        fseek($f, 0);
        /** modify header to be downloadable csv file **/
        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
        /** Send file to browser for download */
        fpassthru($f);
    }

    protected function stateCodeMapping($code)
    {
        if($code=='Andra Pradesh'){
            return 'ANP';
        }elseif($code=='Arunachal Pradesh'){
            return 'ANP';
        }elseif($code=='Assam'){
            return 'AS';
        }elseif($code=='Bihar'){
            return 'BIH';
        }elseif($code=='Chandigarh'){
            return 'CH';
        }elseif($code=='Chhattisgarh'){
            return 'CG';
        }elseif($code=='Dadra and Nagar Haveli'){
            return 'DAD';
        }elseif($code=='Daman and Diu'){
            return 'DD';
        }elseif($code=='Delhi'){
            return 'ND';
        }elseif($code=='Goa'){
            return 'GA';
        }elseif($code=='Gujarat'){
            return 'GUJ';
        }elseif($code=='Haryana'){
            return 'HR';
        }elseif($code=='Himachal Pradesh'){
            return 'HP';
        }elseif($code=='Jammu and Kashmir'){
            return 'JK';
        }elseif($code=='Jharkhand'){
            return 'JH';
        }elseif($code=='Karnataka'){
            return 'KAR';
        }elseif($code=='Kerala'){
            return 'KER';
        }elseif($code=='Lakshadeep'){
            return 'LA';
        }elseif($code=='Madya Pradesh'){
            return 'MP';
        }elseif($code=='Maharashtra'){
            return 'MAH';
        }elseif($code=='Manipur'){
            return 'MAN';
        }elseif($code=='Meghalaya'){
            return 'MEG';
        }elseif($code=='Mizoram'){
            return 'MIZ';
        }elseif($code=='Nagaland'){
            return 'NAG';
        }elseif($code=='Orissa'){
            return 'ORI';
        }elseif($code=='Pondicherry'){
            return 'PON';
        }elseif($code=='Punjab'){
            return 'PUN';
        }elseif($code=='Rajasthan'){
            return 'RAJ';
        }elseif($code=='Sikkim'){
            return 'SIK';
        }elseif($code=='Tamil Nadu'){
            return 'TN';
        }elseif($code=='Tripura'){
            return 'TRI';
        }elseif($code=='Uttar Pradesh'){
            return 'UP';
        }elseif($code=='Uttaranchal'){
            return 'UT';
        }elseif($code=='West Bengal'){
            return 'WB';
        }elseif($code=='Andaman Nicobar'){
            return 'AN';
        }elseif($code=='Dadra Nagar Haveli'){
            return 'DAD';
        }elseif($code=='Daman Diu'){
            return 'DD';
        }elseif($code=='Pondicherry'){
            return 'PON';
        }elseif($code=='Telangana'){
            return 'TL';
        }
    }

    //@active customers of last 365 days
    //@deve Mahesh Gurav
    //@date 14 Nov 2017 7:30 PM

    public function exportAffiliateAction(){
        ini_set('max_execution_time','1000');   
        ini_set('memory_limit', '-1');

        $utc_date = Mage::getModel('core/date')->date('Y-m-d H:i:s');
        // echo $gm_date = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');
        $year_back = date("Y-m-d H:i:s", strtotime("-365 days", strtotime($utc_date)));

        // $april16 = Mage::getModel('core/date')->date('2016-04-01 00:00:00');
        // $april17 = Mage::getModel('core/date')->date('2017-04-01 00:00:00');
        // $novl17 = Mage::getModel('core/date')->date('2017-11-23 00:00:00');

        // $april16_date = date("Y-m-d H:i:s", strtotime($april16));
        // $april17_date = date("Y-m-d H:i:s", strtotime($april17));
        // $novl17_date = date("Y-m-d H:i:s", strtotime($novl17));


        /**
         * Get the resource model
         */
        $resource = Mage::getSingleton('core/resource');
        
        /**
         * Retrieve the read connection
         */
        $readConnection = $resource->getConnection('core_read');
        
        //$query = 'SELECT * FROM ' . $resource->getTableName('catalog/product');
        $query = "SELECT o.increment_id as order_number, o.customer_email, a.region as State, a.city as City, o.grand_total as order_total,  MONTH(o.created_at) as month, YEAR(o.created_at) as Year, a.postcode as postcode, a.telephone as mobile, c.created_at as 'Customer Since' FROM sales_flat_order as o LEFT JOIN customer_entity as c ON c.email = o.customer_email LEFT JOIN sales_flat_order_address as a ON a.parent_id = o.entity_id AND a.address_type = 'shipping' WHERE (o.`created_at` >= '".$year_back."') ORDER BY o.created_at DESC";
        
        // echo $query; exit;
        /**
         * Execute the query and store the results in $results
         */
        $results = $readConnection->fetchAll($query);
        
        /**
         * Print out the results
         */
         // var_dump($results);
    // echo "<pre>";
        $customer_orders = array();
         foreach ($results as $key => $value) {
            # code...
            // print_r($value);
            $email_id = $value['customer_email'];
            // $customer_orders[$email_id][] = $value;
            if($email_id !== ''){
                if(!in_array($value['month'], $customer_orders[$email_id]['months'])){
                    $customer_orders[$email_id]['months'][] = $value['month'];
                }

                $customer_orders[$email_id]['state'] = $value['State'];
                $customer_orders[$email_id]['customer_created'] = $value['Customer Since'];
                $customer_orders[$email_id]['city'] = $value['City'];
                $customer_orders[$email_id]['mobile'] = $value['mobile'];
                $customer_orders[$email_id]['postcode'] = $value['postcode'];
                $customer_orders[$email_id]['order_total'] += $value['order_total'];
                $customer_orders[$email_id]['count_order'] += 1;
            }
            // exit;
         }

     // print_r($customer_orders);  
        $qtr1 = array(1, 2, 3);
        $qtr2 = array(4, 5, 6);
        $qtr3 = array(7, 8, 9);
        $qtr4 = array(10, 11, 12);

        $filename = "active_customers_since_year_on_".date('Ymdhis').".csv";

        // $file_path = Mage::getBaseDir() . DS .'var'.  DS .'export'. DS .$filename; 
        $file = Mage::getBaseDir() . DS .'var'.  DS .'export'. DS .$filename;

        $filepath = fopen($file, 'w');

        // var_dump($fp);

        // fputcsv($filepath, array('Email','Mobile','State','City','Order Total','Postcode','Customer Status','Count Orders','Months'));
        fputcsv($filepath, array('Email','Mobile','Affiliate Name','City','State','Pin Code','ASM', 'Number of Orders', 'Status(Active/Semi Active/Inactive/Dormant)','Date of Creation','Total Business (Amount)','Months of Orders'));

        $objAffiliatescontacts = new Neo_Affiliate_Model_Eav_Entity_Attribute_Source_Affiliatescontacts();
        $customer_model = Mage::getModel('customer/customer');

         foreach ($customer_orders as $email => $orders) {
            $customer_load = $customer_model->setWebsiteId(1)->loadByEmail($email);
            // print_r($customer_load->getData()); exit;
            $asm_map = $customer_load->getAsmMap();
            $asm_name = $objAffiliatescontacts->getOptionText($asm_map);
            // $rsm_name = $objAffiliatescontacts->getRsmbyasm($asm_map);
            // $customer_name = $customer_load->getFirstname().' '.$customer_load->getLastname();
            $customer_storename = $customer_load->getAffiliateStore();
            $customer_mobile = $customer_load->getMobile();
            
            $all_months = $orders['months'];
            $list_months = implode(",", $all_months);
            $count_months = count($all_months);
            if($count_months >= 10){
                $status = 'Active';
            }elseif($count_months <= 10 && $count_months >= 3 && array_intersect($all_months, $qtr1) && array_intersect($all_months, $qtr2) && array_intersect($all_months, $qtr3) && array_intersect($all_months, $qtr4)){
                $status = 'Semi Active';
            }else{
                $status = 'Inactive';
            }
            // echo $status;
            if($email !== ''){
                // fputcsv($filepath, array($email,$orders['mobile'],$orders['state'],$orders['city'],$orders['order_total'],$orders['postcode'],$status,$orders['count_order'],'"'.$list_months.'"'));
                fputcsv($filepath, array($email,$customer_mobile,$customer_storename,$orders['city'],$orders['state'],$orders['postcode'],$asm_name, $orders['count_order'], $status,$orders['customer_created'],$orders['order_total'],$list_months));
            }
         }

         $ordering_customers = array_keys($customer_orders);

         
         $all_customers = $customer_model->getCollection();
         // $all_customers->AttributeTo
         $region = Mage::getModel('directory/region');
         foreach ($all_customers as $customer) {
            # code...
            // print_r($customer->getData());
            $customer_email = $customer->getEmail();

            if(!in_array($customer_email, $ordering_customers)){
                $customer_id = $customer->getEntityId();
                $customer_load = $customer_model->setWebsiteId(1)->load($customer_id);
                $asm_map = $customer_load->getAsmMap();
                $asm_name = $objAffiliatescontacts->getOptionText($asm_map);
                $rsm_name = $objAffiliatescontacts->getRsmbyasm($asm_map);
                $customer_name = $customer_load->getFirstname().' '.$customer_load->getLastname();
                $customer_storename = $customer_load->getAffiliateStore();
                $customer_created_at = $customer_load->getCreatedAt();

                $shipping_address = $customer_load->getPrimaryShippingAddress();
                // print_r($shipping_address);
                $state = '';
                $city = '';
                $postcode = '';
                $mobile = '';

                if($shipping_address){
                    // print_r($shipping_address->getData());
                    if($shipping_address->getRegion()){
                        $state = $shipping_address->getRegion();
                    }

                    if($shipping_address->getCity()){
                        $city = $shipping_address->getCity();
                    }

                    if($shipping_address->getPostcode()){
                        $postcode = $shipping_address->getPostcode();
                    }

                    if($shipping_address->getTelephone()){
                        $mobile = $shipping_address->getTelephone();
                    }
                }else{
                    if($region_id = $customer_load->getCusState()){
                        $state = $region->load($region_id)->getName();
                    }

                    if($customer_load->getCusCity()){
                        $city = $customer_load->getCusCity();
                    }

                    if($customer_load->getPincode()){
                        $postcode = $customer_load->getPincode();
                    }

                    if($customer_load->getMobile()){
                        $mobile = $customer_load->getMobile();
                    }

                }

                    // fputcsv($filepath, array($customer_email,$mobile,$state,$city,"",$postcode,'Dormant',"",""));
                    fputcsv($filepath, array($customer_email,$mobile,$customer_name,$city,$state,$postcode,$asm_name, "", 'Dormant',$customer_created_at,"",""));
                    //break;
                
            }
         }



        fclose($filepath);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($file)); 
        echo readfile($file);
    }
}
