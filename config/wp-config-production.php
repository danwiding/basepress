<?php

// ** MySQL settings - You can get this info from your web host ** //

/** The name of the database for WordPress */
//in sensitive file
/** MySQL database username */
//in sensitive file
/** MySQL database password */
//in sensitive file
/** MySQL hostname */
//in sensitive file

define( 'DOMAIN_CURRENT_SITE', 'thejun.to' );

define('FORCE_SSL_LOGIN', true);
define('FORCE_SSL_ADMIN', true);

// turn on caching
define('WP_CACHE', true);

// used to determine environment from easily accessible constant
if ( !defined('VIA_ENVIRONMENT') )
	define('VIA_ENVIRONMENT', 'prod');

//secret in sensitive file

define('JUNTO_FB_FANPAGE', '214331338590757');

define('JUNTO_HOURLY_RATE', 75.00);
define('IS_JUNTO', true);							// Used to determine if on Junto Network