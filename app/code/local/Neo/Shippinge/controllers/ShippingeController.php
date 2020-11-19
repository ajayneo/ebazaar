<?php
/**
 * Created by PhpStorm.
 * User: webwerks
 * Date: 10/10/14
 * Time: 5:16 PM
 */

class Neo_Shippinge_ShippingeController extends Mage_Adminhtml_Controller_Action
{
    
    /**
    * Added this function by pradeep sanku on 12th August since patch SUPEE 6285 was having access to some 3rd party extension
    */
    protected function _isAllowed()
    {
        return true;
    }

    public function indexAction()
    {
        $log_file_name  = "shipment".Mage::getModel('core/date')->date('Y-m-d').".log";

        $service_type = $this->getRequest()->getPost("service_type");
        $post = $this->getRequest()->getPost();

        Mage::log("----- post data -----", 1, $log_file_name, true);
        Mage::log($this->getRequest()->getPost(), 1, $log_file_name, true);

        $result = "";
        if($service_type == "bluedart") {
            //$result = $this->bluedart($post);
            $result = Mage::getModel('shippinge/bluedart')->bluedart($post);
        } else if($service_type == "ecom_express") {
			$result = Mage::getModel('shippinge/ecom')->register($post);
		} elseif($service_type == "FEDEX_EXPRESS_SAVER") {
            //$result = Mage::getModel('shippinge/fedex_shipment')->fadex($post);
            $result = Mage::getModel('shippinge/fedex_shipment')->fadex($post);
        }elseif($service_type == "v_express"){
            $result = Mage::getModel('shippinge/vexpress')->vexpress($post);
            $this->getResponse()->setHeader('Content-type', 'application/json', true);
            $this->getResponse()->setBody('['.Mage::helper('core')->jsonEncode($result).']');
            return;
        } else{
            $result = Mage::getModel('shippinge/fedex_shipmentovr')->fadex($post);
        }

        Mage::log("----- End Response -----", 1, $log_file_name, true);
        Mage::log($result, 1, $log_file_name, true);

        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        return;
    }

	public function downloadAction()
	{
		$file = $this->getRequest()->getParam('file_path');
		$dir_path = Mage::getBaseDir('var').'/fadex_shipment/';
		$yourfile = $dir_path.$file;
		$file_name = basename($yourfile);
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=$file_name");
		header("Content-Length: " . filesize($yourfile));
		readfile($yourfile);
		exit;
	}
}
