<?php
$installer = $this;

$installer->startSetup();

//add refresh rate default value
$installer->setConfigData('multipledeals/configuration/refresh_rate',   30);

$installer->endSetup(); 