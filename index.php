<?php

//ini_set('max_execution_time','1000');  
//ini_set('mysql.connect_timeout', 300);   
//ini_set('default_socket_timeout', 300); 
//ini_set('mysql.allow_persistent','on'); 
//ini_set('mysql.max_links', 500);
//ini_set('memory_limit', '256M');
//ini_set('mysql.reconnect', 1); 
//ini_set('mysql.allow_persistent', TRUE);  

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if (version_compare(phpversion(), '5.2.0', '<')===true) {
    echo  '<div style="font:12px/1.35em arial, helvetica, sans-serif;">
<div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
<h3 style="margin:0; font-size:1.7em; font-weight:normal; text-transform:none; text-align:left; color:#2f2f2f;">
Whoops, it looks like you have an invalid PHP version.</h3></div><p>Magento supports PHP 5.2.0 or newer.
<a href="http://www.magentocommerce.com/install" target="">Find out</a> how to install</a>
 Magento using PHP-CGI as a work-around.</p></div>';
    exit;
}

/**
 * Error reporting
 */
//error_reporting(E_ALL | E_STRICT);

/**
 * Compilation includes configuration file
 */
define('MAGENTO_ROOT', getcwd());

$compilerConfig = MAGENTO_ROOT . '/includes/config.php';
if (file_exists($compilerConfig)) {
    include $compilerConfig;
}

$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
$maintenanceFile = 'maintenance.flag';

if (!file_exists($mageFilename)) {
    if (is_dir('downloader')) {
        header("Location: downloader");
    } else {
        echo $mageFilename." was not found";
    }
    exit;
}


//for app
/*
$array_status = array();
if(file_exists($maintenanceFile) && strpos($_SERVER['REQUEST_URI'],'customapiv6') !== false){
    $array_status['status'] = 0;
    $array_status['message'] = "This APP is under maintenance mode.";

    echo json_encode($array_status);
    exit;
}*/

$ip = $_SERVER['REMOTE_ADDR'];
$allowed = array();
// $allowed = array('113.193.83.252');
//27.106.105.214
// $allowed = array('27.106.105.218','49.248.210.138','111.119.217.101');
// $allowed = array('27.106.105.214','206.183.111.25');
// $allowed = array('27.106.105.203'); //KKOC
// $allowed = array('113.193.90.232'); //TIKONA
// $allowed = array('103.224.241.11','27.4.34.52','150.107.164.205','150.107.182.102','103.61.196.50'); //DCTAC,Sneha,Rakshita,Tushar
// $allowed = array('206.183.111.25','223.182.168.42','106.209.145.101'); //Ruby Rakshita
//mahesh tikona
// $allowed[] = '113.193.85.155';
// $allowed[] = '103.224.241.11'; //rabale mahesh
// $allowed[] = '114.79.152.202'; //nerul daniel
//&& in_array($ip, $allowed)
//&& !in_array($ip, $allowed)
if (file_exists($maintenanceFile) && !in_array($ip, $allowed)) {
    //fixing app webservice maintenance mode banner issue
    if(strrpos($_SERVER['REQUEST_URI'], 'home/flag') !== false && !in_array($ip, $allowed)){
        $array_status['status'] = 0;
        $array_status['message'] = "This APP is under maintenance mode.";
        // $array_status['url'] = "https://www.electronicsbazaar.com/website_under_construction.png";
        $array_status['url'] = "https://electronicsbazaar.com/mobile-app/maintenance.html";
        echo json_encode($array_status);
        exit;
    }
    //include_once dirname(__FILE__) . '/errors/503.php';
    include_once dirname(__FILE__) . '/maintenance.html';
    exit;
}

require_once $mageFilename;

//Varien_Profiler::enable();

if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
    Mage::setIsDeveloperMode(true);
}

ini_set('display_errors', 1); 

umask(0);

/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';

/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';
  
Mage::run($mageRunCode, $mageRunType);
