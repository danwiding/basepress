<?php

define('JUNTO_BASE_FOR_POG', dirname(dirname(dirname(dirname(__FILE__)))));
require_once(JUNTO_BASE_FOR_POG . '/config/wordpress-app/wp-config-local.php');
require_once(JUNTO_BASE_FOR_POG . '/juntobasepress/junto-common/sensitive-config-loader.php');
SensitiveConfigLoader(JUNTO_BASE_FOR_POG . '/config/sensitive/wp-sensitive-local.json');

//IMPORTANT:
//Rename this file to configuration.php after having inserted all the correct db information
global $configuration;
$configuration['soap'] = "http://www.phpobjectgenerator.com/services/pog.wsdl";
$configuration['homepage'] = "http://www.phpobjectgenerator.com";
$configuration['revisionNumber'] = "";
$configuration['versionNumber'] = "3.0f";

$configuration['pdoDriver']	= 'mysql';
$configuration['setup_password'] = '';


// to enable automatic data encoding, run setup, go to the manage plugins tab and install the base64 plugin.
// then set db_encoding = 1 below.
// when enabled, db_encoding transparently encodes and decodes data to and from the database without any
// programmatic effort on your part.
$configuration['db_encoding'] = 0;

// edit the information below to match your database settings

$configuration['db']	= DB_NAME;		//	<- database name
$configuration['host'] 	= DB_HOST;	//	<- database host
$configuration['user'] 	= DB_USER;		//	<- database user
$configuration['pass']	= DB_PASSWORD;		//	<- database password
$configuration['port']	= '3306';		//	<- database port


//proxy settings - if you are behnd a proxy, change the settings below
$configuration['proxy_host'] = false;
$configuration['proxy_port'] = false;
$configuration['proxy_username'] = false;
$configuration['proxy_password'] = false;


//plugin settings
$configuration['plugins_path'] = JUNTO_BASE_FOR_POG . '/juntobasepress/lib/php-object-generator/plugins';  //absolute path to plugins folder, e.g c:/mycode/test/plugins or /home/phpobj/public_html/plugins


?>