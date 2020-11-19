<?php


class Neo_Vexpressawb_Block_Adminhtml_Vexpress extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

		$this->_controller = "adminhtml_vexpress";
		$this->_blockGroup = "vexpressawb";
		$this->_headerText = Mage::helper("vexpressawb")->__("Vexpress Manager");
		//$this->_addButtonLabel = Mage::helper("vexpressawb")->__("Add New Item");
		parent::__construct();
		
		$this->_updateButton('add', 'label', 'Add item');

		$FormKey = Mage::getSingleton('core/session')->getFormKey();

		$url = Mage::helper("adminhtml")->getUrl("vexpressawb/adminhtml_vexpress/upload");

		/*$this->_addButton('import', array(
            'label'         => Mage::helper('vexpressawb')->__('Import'),
            'onclick'       => "document.getElementById('uploadTarget').click();",
            'class'         => 'add',
            'after_html'    => '<form method="POST" action="'."$url".'" id="uploadForm" enctype="multipart/form-data">
            <input name="form_key" type="hidden" value="'."$FormKey".'" />
            <input type="file" name="import_file" style="display:none;" id="uploadTarget"/>
        	</form>
	        <script type="text/javascript">
	        document.getElementById("uploadTarget").addEventListener("change", function(){
	            document.getElementById("uploadForm").submit();
	        }, false);
	        </script>',
        ));*/
	}

}