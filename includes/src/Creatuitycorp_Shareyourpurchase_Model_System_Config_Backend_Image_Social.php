<?php

/**
 * Model Creatuitycorp_Shareyourpurchase_Model_System_Config_Backend_Image_Social
 * to storage and retrive images
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_Model_System_Config_Backend_Image_Social
    extends Mage_Adminhtml_Model_System_Config_Backend_Image {
    
    /**
     * The tail part of directory path for uploading
     *
     */
    const UPLOAD_DIR = 'social';

    /**
     * Token for the root part of directory path for uploading
     *
     */
    const UPLOAD_ROOT = 'media';

    /**
     * Return path to directory for upload file
     *
     * @return string
     * @throw Mage_Core_Exception
     */
    protected function _getUploadDir() {
        $uploadDir = $this->_appendScopeInfo(self::UPLOAD_DIR);
        $uploadRoot = $this->_getUploadRoot(self::UPLOAD_ROOT);
        return $uploadRoot . '/' . $uploadDir;
    }

    /**
     * Makes a decision about whether to add info about the scope.
     *
     * @return boolean
     */
    protected function _addWhetherScopeInfo() {
        return true;
    }

    /**
     * Getter for allowed extensions of uploaded files.
     *
     * @return array
     */
    protected function _getAllowedExtensions() {
        return array('ico', 'png', 'gif', 'jpg', 'jpeg', 'apng', 'svg');
    }

    /**
     * Get real media dir path
     *
     * @param  $token
     * @return string
     */
    protected function _getUploadRoot($token) {
        return Mage::getBaseDir($token);
    }

    /**
     * Return path where images are stored
     * 
     * @return string
     */
    public function getBaseMediaUrl() {
        return Mage::getBaseUrl('media') . self::UPLOAD_DIR . '/';
    }

}
