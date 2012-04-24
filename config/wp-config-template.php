<?php

//will check if a local version of this file exists first
//this file is ignored in source control but is just the lines inside the else statement below

// MySQL settings
/** The name of the database for WordPress */

/** MySQL database username */

/** MySQL database password */

/** MySQL hostname */

define( 'DOMAIN_CURRENT_SITE', $_SERVER['HTTP_HOST'] );
define('FORCE_SSL_LOGIN', true);
define('FORCE_SSL_ADMIN', true);


/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);
$table_prefix  = 'whatever';


// used to determine environment from easily accessible constant
//options are 'dev' 'staging' or 'prod'
if ( !defined('VIA_ENVIRONMENT') )
	define('VIA_ENVIRONMENT', 'dev');

						// Used to determine if on Junto Network
define('WP_ALLOW_MULTISITE', true);
define( 'MULTISITE', true );

define ('WP_PLUGIN_DIR', dirname(dirname(__DIR__)) . '/plugins');
//define( 'WPMU_PLUGIN_DIR', dirname(__DIR__) . '/plugins');