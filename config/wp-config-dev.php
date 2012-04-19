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

define('JUNTO_FB_FANPAGE', '214331338590757');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

// used to determine environment from easily accessible constant
if ( !defined('VIA_ENVIRONMENT') )
	define('VIA_ENVIRONMENT', 'dev');

define('JUNTO_HOURLY_RATE', 75.00);
define('IS_JUNTO', true);							// Used to determine if on Junto Network