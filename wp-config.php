<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// modify the config file based on environment
//the prod configuration will not be used until thejun.to is not just a redirect

//function exception_handler($exception) {
//	echo "Uncaught exception: " , $exception->getMessage(), "\n";
//}

//set_exception_handler('exception_handler');

$path = dirname(__FILE__);
$templateDirectory = dirname($path);

require_once($path . '/junto-common/junto-loader.php');



require_once($templateDirectory . '/config/wordpress-app/wp-config-app.php');
if(defined('AUTOMATED_TESTING') && AUTOMATED_TESTING =='On')
    require_once($templateDirectory . '/config/wordpress-app/wp-config-phpunit-test.php');
else
    require_once($templateDirectory . '/config/wordpress-app/wp-config-local.php');

junto_loader::LoadConfiguration();

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');



/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

define('JuntoScriptKey', 'JuntoScripts');
$GLOBALS[JuntoScriptKey]=array();

// debug mode set in wp-locals


define('WP_POST_REVISIONS', false);											// Turn Off Post Revisions
define('AUTOSAVE_INTERVAL', 10000);											// Change Auto-Save Interval to 10 min

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');


/** Sets up WordPress vars and included files. */
if (!defined('AUTOMATED_TESTING') || AUTOMATED_TESTING != 'On')
    require_once(ABSPATH . 'wp-settings.php');
//require_once(JUNTO_COMMON_PATH . '/poly_baseline.php');
