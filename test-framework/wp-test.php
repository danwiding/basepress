<?php
define('AUTOMATED_TESTING', 'On');

$options = 'v:t:r:msflndq';
if (is_callable('getopt')) {
    $opts = getopt($options);
} else {
    include( dirname(__FILE__) . '/wp-testlib/getopt.php' );
    $opts = getoptParser::getopt($options);
}

define('DIR_TESTROOT', realpath(dirname(__FILE__)));
if (!defined('DIR_TESTCASE')) {
    define('DIR_TESTCASE', dirname(dirname(DIR_TESTROOT)) . '/tests');
}

define('TEST_WP', true);
define('WP_DEBUG', array_key_exists('d', $opts) );
define('SAVEQUERIES', array_key_exists('q', $opts));
define('DIR_WP', dirname(DIR_TESTROOT) .'/wordpress');

// make sure all useful errors are displayed during setup
error_reporting(E_ALL & ~E_DEPRECATED );
ini_set('display_errors', true);

require_once(DIR_TESTROOT.'/wp-testlib/base.php');
require_once(DIR_TESTROOT.'/wp-testlib/utils.php');

define('ABSPATH', realpath(DIR_WP).'/');

if (!defined('DIR_TESTPLUGINS'))
    define('DIR_TESTPLUGINS', './wp-plugins');

require_once(dirname(DIR_WP) .'/wp-config.php');//must be after abspath is defined

// install wp
define('WP_BLOG_TITLE', rand_str());
define('WP_USER_NAME', rand_str());
define('WP_USER_EMAIL', rand_str().'@example.com');

//drop_tables();

//run db migrations
define('MPM_PATH', REPO_PATH . '/juntobasepress/tools/mysql-php-migrations');
define('MPM_VERSION', '2.1.4');
require_once(MPM_PATH . '/lib/init.php');
$GLOBALS['db_config']=$db_config;
$latestController = new MpmBuildController('build', array('--force'));
$latestController->doAction(true);

// initialize wp
define('WP_INSTALLING', 1);
$_SERVER['PATH_INFO'] = $_SERVER['SCRIPT_NAME']; // prevent a warning from some sloppy code in wp-settings.php
require_once(ABSPATH.'wp-settings.php');


// override stuff
require_once(DIR_TESTROOT.'/wp-testlib/mock-mailer.php');
$GLOBALS['phpmailer'] = new MockPHPMailer();

// Allow tests to override wp_die
add_filter( 'wp_die_handler', '_wp_die_handler_filter' );

require_once(ABSPATH.'wp-admin/includes/upgrade.php');
wp_install(WP_BLOG_TITLE, WP_USER_NAME, WP_USER_EMAIL, true);

// make sure we're installed
assert(true == is_blog_installed());

// include plugins for testing, if any
if (is_dir(DIR_TESTPLUGINS)) {
    $plugins = glob(realpath(DIR_TESTPLUGINS).'/*.php');
    foreach ($plugins as $plugin)
        include_once($plugin);
}

//load the current theme
//switch_theme(WP_DEFAULT_THEME, WP_DEFAULT_THEME);
require_once( get_template_directory() . '/functions.php' );


// include all files in DIR_TESTCASE, and fetch all the WPTestCase descendents
    $files = wptest_get_all_test_files(DIR_TESTCASE);
    foreach ($files as $file) {
        require_once($file);
    }
    $classes = wptest_get_all_test_cases();


if ( isset($opts['l']) ) {
    wptest_listall_testcases($classes);
} else if (isset($opts['t']) || isset($opts['a']) || isset($opts['g'])){
    do_action('test_start');
// hide warnings during testing, since that's the normal WP behaviour
    if ( !WP_DEBUG ) {
        error_reporting( E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE );
    }
    // run the tests and print the results
    list ($result, $printer) = wptest_run_tests($classes, isset($opts['t']) ? $opts['t'] : array(), isset($opts['g']) ? $opts['g'] : null );
    wptest_print_result($printer,$result);
}
//if ( !isset($opts['n']) ) {
//    // clean up the database
//    drop_tables();
//}
