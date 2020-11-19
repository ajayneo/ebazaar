<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute("quote_address", "microsoftdiscount_total", array("type"=>"varchar"));
$installer->addAttribute("order", "microsoftdiscount_total", array("type"=>"varchar"));
$installer->endSetup();
	 