<?php
class Neo_Orderreturn_Block_Adminhtml_Return_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("return_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("orderreturn")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				
				$req_id = $this->getRequest()->getParam("id");
				$status = Mage::helper('orderreturn')->getReuestStatus($req_id);
				$max_status = max($status);
				$this->addTab("form_section", array(
				"label" => Mage::helper("orderreturn")->__("View IMEI"),
				"title" => Mage::helper("orderreturn")->__("View IMEI"),
				"content" => $this->getLayout()->createBlock("orderreturn/adminhtml_return_edit_tab_view")->toHtml(),
				));

				$view_tab = array('2','4','5','6');
				$pickup_tab = array('4');
				$payment_tab = array('5');

			$check_status = count(array_intersect($view_tab, $status)); //die();	
			$check_pickup = count(array_intersect($pickup_tab, $status)); //die();	
			$payment_pickup = count(array_intersect($payment_tab, $status)); //die();	
			//show below tabs only for approved status
			if($check_status){		
				/*$this->addTab("form_section", array(
				"label" => Mage::helper("orderreturn")->__("Item Information"),
				"title" => Mage::helper("orderreturn")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("orderreturn/adminhtml_return_edit_tab_form")->toHtml(),
				));*/
				
				if($max_status >= 2){
					$this->addTab("pickup_section", array(
					"label" => Mage::helper("orderreturn")->__("Pickup Information"),
					"title" => Mage::helper("orderreturn")->__("Pickup Information"),
					"content" => $this->getLayout()->createBlock("orderreturn/adminhtml_return_edit_tab_pickups")->toHtml(),
					));
				}

				/*$this->addTab("refund_section", array(
				"label" => Mage::helper("orderreturn")->__("Refund/Return Information"),
				"title" => Mage::helper("orderreturn")->__("Refund/Return Information"),
				"content" => $this->getLayout()->createBlock("orderreturn/adminhtml_return_edit_tab_refunds")->toHtml(),
				));*/

				if($max_status >= 4){
					$this->addTab("return_pass", array(
					"label" => Mage::helper("orderreturn")->__("Sales Return Pass"),
					"title" => Mage::helper("orderreturn")->__("Sales Return Pass"),
					"content" => $this->getLayout()->createBlock("orderreturn/adminhtml_return_edit_tab_sales")->toHtml(),
					));
				}

				if($max_status >= 5){
					$this->addTab("payment_done", array(
					"label" => Mage::helper("orderreturn")->__("Payment Done"),
					"title" => Mage::helper("orderreturn")->__("Payment Done"),
					"content" => $this->getLayout()->createBlock("orderreturn/adminhtml_return_edit_tab_payment")->toHtml(),
					));
				}
			} //approved check	
				return parent::_beforeToHtml();
		}

}
