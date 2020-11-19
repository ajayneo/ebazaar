<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
ALTER TABLE neo_asmdetail_arm ADD enabled BOOLEAN NOT NULL DEFAULT TRUE;
		
SQLTEXT;

$installer->run($sql);

$installer->endSetup();
	 