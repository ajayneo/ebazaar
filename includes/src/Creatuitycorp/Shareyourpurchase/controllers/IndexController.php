<?php
/**
 * Controller Creatuitycorp_Shareyourpurchase_IndexController
 *
 * @category   Creatuitycorp
 * @package    Creatuitycorp_Shareyourpurchase
 * @copyright  Copyright (c) 2013 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license/
 */
class Creatuitycorp_Shareyourpurchase_IndexController
    extends Mage_Core_Controller_Front_Action {

    public function indexAction() {

        $this->loadLayout();

        $this->renderLayout();
    }

}