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
 * Notification admin controller
 *
 * @category    Neo
 * @package     Neo_Notification
 * @author      Ultimate Module Creator
 */
class Neo_Notification_Adminhtml_Notification_NotificationController extends Neo_Notification_Controller_Adminhtml_Notification
{
    /**
     * init the notification
     *
     * @access protected
     * @return Neo_Notification_Model_Notification
     */
    protected function _initNotification()
    {
        $notificationId  = (int) $this->getRequest()->getParam('id');
        $notification    = Mage::getModel('neo_notification/notification');
        if ($notificationId) {
            $notification->load($notificationId);
        }
        Mage::register('current_notification', $notification);
        return $notification;
    }

    /**
     * default action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('neo_notification')->__('Notification'))
             ->_title(Mage::helper('neo_notification')->__('Notifications'));
        $this->renderLayout();
    }

    /**
     * grid action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * edit notification - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $notificationId    = $this->getRequest()->getParam('id');
        $notification      = $this->_initNotification();
        if ($notificationId && !$notification->getId()) {
            $this->_getSession()->addError(
                Mage::helper('neo_notification')->__('This notification no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getNotificationData(true);
        if (!empty($data)) {
            $notification->setData($data);
        }
        Mage::register('notification_data', $notification);
        $this->loadLayout();
        $this->_title(Mage::helper('neo_notification')->__('Notification'))
             ->_title(Mage::helper('neo_notification')->__('Notifications'));
        if ($notification->getId()) {
            $this->_title($notification->getTitle());
        } else {
            $this->_title(Mage::helper('neo_notification')->__('Add notification'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new notification action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save notification - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('notification')) {
            try {
                // banner image upload
                if(isset($_FILES['image_name']['name']) && !empty($_FILES['image_name']['name'])) {
                        $uploader = new Varien_File_Uploader('image_name');
                        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);
                        $path = Mage::getBaseDir('media').'/notificationbanner/';
                        $uploader->save($path, $_FILES['image_name']['name']);
                        $data['image_name'] ='notificationbanner/'.$_FILES['image_name']['name'];
                } else {
                    $data['image_name'] = $data['image_name']['value'];
                }

                $notification = $this->_initNotification();

                $notification->addData($data);
                $notification->save();
                if($data['status'] == 2) {
                    Mage::helper('neo_notification')->divideRequestWithinAndroidAndIphone($data);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('neo_notification')->__('Notification was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $notification->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setNotificationData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('neo_notification')->__('There was a problem saving the notification.')
                );
                Mage::getSingleton('adminhtml/session')->setNotificationData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('neo_notification')->__('Unable to find notification to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete notification - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $notification = Mage::getModel('neo_notification/notification');
                $notification->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('neo_notification')->__('Notification was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('neo_notification')->__('There was an error deleting notification.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('neo_notification')->__('Could not find notification to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete notification - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $notificationIds = $this->getRequest()->getParam('notification');
        if (!is_array($notificationIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('neo_notification')->__('Please select notifications to delete.')
            );
        } else {
            try {
                foreach ($notificationIds as $notificationId) {
                    $notification = Mage::getModel('neo_notification/notification');
                    $notification->setId($notificationId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('neo_notification')->__('Total of %d notifications were successfully deleted.', count($notificationIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('neo_notification')->__('There was an error deleting notifications.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass status change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massStatusAction()
    {
        $notificationIds = $this->getRequest()->getParam('notification');
        if (!is_array($notificationIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('neo_notification')->__('Please select notifications.')
            );
        } else {
            try {
                foreach ($notificationIds as $notificationId) {
                $notification = Mage::getSingleton('neo_notification/notification')->load($notificationId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d notifications were successfully updated.', count($notificationIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('neo_notification')->__('There was an error updating notifications.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass Product change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massProductIdAction()
    {
        $notificationIds = $this->getRequest()->getParam('notification');
        if (!is_array($notificationIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('neo_notification')->__('Please select notifications.')
            );
        } else {
            try {
                foreach ($notificationIds as $notificationId) {
                $notification = Mage::getSingleton('neo_notification/notification')->load($notificationId)
                    ->setProductId($this->getRequest()->getParam('flag_product_id'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d notifications were successfully updated.', count($notificationIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('neo_notification')->__('There was an error updating notifications.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export as csv - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportCsvAction()
    {
        $fileName   = 'notification.csv';
        $content    = $this->getLayout()->createBlock('neo_notification/adminhtml_notification_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as MsExcel - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportExcelAction()
    {
        $fileName   = 'notification.xls';
        $content    = $this->getLayout()->createBlock('neo_notification/adminhtml_notification_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as xml - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportXmlAction()
    {
        $fileName   = 'notification.xml';
        $content    = $this->getLayout()->createBlock('neo_notification/adminhtml_notification_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @access protected
     * @return boolean
     * @author Ultimate Module Creator
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('neo_notification/notification');
    }
}
