<?php
	$installer = $this;
	$installer->startSetup();
	$table = $installer->getTable('neo_notification_notification');
	$installer->run("ALTER TABLE ".$table." ADD COLUMN image_name VARCHAR(255)");
	$installer->run("ALTER TABLE ".$table." ADD COLUMN category_id smallint");
	$installer->run("ALTER TABLE ".$table." ADD COLUMN link_url VARCHAR(255)");
	$installer->run("ALTER TABLE ".$table." ADD COLUMN notfication_type smallint");
	$installer->endSetup();