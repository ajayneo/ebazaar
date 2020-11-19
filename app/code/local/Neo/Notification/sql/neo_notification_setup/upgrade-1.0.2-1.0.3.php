<?php
	$installer = $this;
	$installer->startSetup();
	$table = $installer->getTable('neo_notification_notification');
	$installer->run("ALTER TABLE ".$table." ADD COLUMN image_link_type smallint");
	$installer->endSetup();