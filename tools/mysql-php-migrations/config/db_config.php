<?php
$_SERVER['HTTP_HOST'] = 'localhost';
define('JUNTO_BASE_FOR_MIGRATE', dirname(dirname(dirname(MPM_PATH))));
require_once(JUNTO_BASE_FOR_MIGRATE . '/config/wordpress-app/wp-config-local.php');
require_once(JUNTO_BASE_FOR_MIGRATE . '/juntobasepress/junto-common/sensitive-config-loader.php');
SensitiveConfigLoader(JUNTO_BASE_FOR_MIGRATE . '/config/sensitive/wp-sensitive-local.json');

$db_config = (object) array();
$db_config->host = DB_HOST;
$db_config->port = '3306';
$db_config->name = DB_NAME;
$db_config->db_path = dirname(dirname(MPM_PATH)) . '/db/migrations/';
$db_config->method = 1;
$db_config->migrations_table = 'mpm_migrations';

//if (defined('DB_MIGRATION_USER') && defined('DB_MIGRATION_USER')){
//	$db_config->user = DB_MIGRATION_USER;
//	$db_config->pass = DB_MIGRATION_USER_PASSWORD;
//}
//else{
	$db_config->user = DB_USER;
	$db_config->pass = DB_PASSWORD;
//}

?>