<?php
/*
(c) Copyright 2012 X.commerce, Inc.

All rights reserved. No part of this code shall be reproduced,
stored in a retrieval system, or transmitted by any means,
electronic, mechanical, photocopying, recording, or otherwise,
without written permission from X.commerce, Inc.  04-15-2012

Please be aware that this code is not production ready.
It is distributed to serve as an educational example, but in
some cases error checking or something similar might have been
neglected.
*/

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Up to and including Magento 1.5 tables where created using plain SQL passed to $installer->run().
 * Since Magento 1.6 MMDB (Magento Multi DataBase) was introduced and the preferred way to create table now is
 * through the DDL methods.
 * Cross DB compatible setup scripts should not use the mysql4- prefix for file names since then either.
 */

$installer->run("Alter table tag ADD `tag_page_title` VARCHAR(500);Alter table tag ADD `tag_keywords` VARCHAR(500);Alter table tag ADD `tag_description` VARCHAR(500);");

/** Magento 1.5 and older style, still valid for backward compatibility if needed:
$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('mcd_meeting04/comment')}`;
CREATE TABLE `{$installer->getTable('mcd_meeting04/comment')}` (
 `entity_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `guest_name` VARCHAR(255) NULL,
 `guest_email` VARCHAR(255) NULL,
 `customer_id` INT(10) UNSIGNED NULL,
 `comment` TEXT NOT NULL,
 `page` VARCHAR(255) NULL,
 `store_id` SMALLINT(5) UNSIGNED NOT NULL,
 `created_at` DATETIME NOT NULL,
 `updated_at` DATETIME NULL,
  PRIMARY KEY  (`entity_id`),
  CONSTRAINT `FK_COMMENT_CUSTOMER_ID` FOREIGN KEY (`customer_id`) REFERENCES `{$installer->getTable('customer/entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_COMMENT_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Comment Entities';
");
*/

$installer->endSetup();