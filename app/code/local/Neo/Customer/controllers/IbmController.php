<?php require_once 'Mage/Customer/controllers/AccountController.php';
//extends Mage_Core_Controller_Front_Action
class Neo_Customer_IbmController extends Mage_Customer_AccountController
{
	
	//custom login page
	public function loginAction(){
		//if logged in redirect to specific category
		if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('/accessories');
            return;
        }
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('IBM Login'));
        $this->renderLayout();	
	}

	//custom login form for handling redirectp
    public function loginPostAction(){
        $ibmsale_cat_name = Mage::getStoreConfig('settings/category/name');
        $login = $this->getRequest()->getPost('login');
        $session = Mage::getSingleton('customer/session');
        $customer = Mage::getModel('customer/customer')->setWebsiteId(1);
        $redirect_url = Mage::getUrl($ibmsale_cat_name);
     

        try
        {
            $session->setBeforeAuthUrl($redirect_url);
            if($customer->authenticate($login['username'], $login['password'])) 
            {   
                $session->setCustomerAsLoggedIn($customer);
                $response = Mage::app()->getFrontController()->getResponse();
                //set the redirect header to the response object
                $response->setRedirect($redirect_url);
                //send the response immediately and exit since there is nothing more to do with the current execution
                $response->sendResponse();
            }
            else
            {
                // throw new Exception ($this->__('Invalid login or password.'));
                $session->addError('Invalid login or password.');
            }
        }
        catch (Exception $e)
        {
            // echo $e->getMessage();
            $session->addError($e->getMessage());
            $this->_redirect($ibmsale_cat_name);
        }
    }
    
} ?>