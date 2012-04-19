<?php
/*
Plugin Name: Junto Integration
Plugin URI: http://thejun.to
Description: This plugin handles connections to linkedin, twitter, and facebook for junto 
Version: .2
Author: Dan Widing
Author URI: http://thejun.to
License: A "Slug" license name e.g. GPL2
*/
if ( !defined('JUNTO_FB_PERMISSIONS') )
	define('JUNTO_FB_PERMISSIONS', 'email,user_website');

if ( !defined( 'JUNTO_CONNECT_DIR' ) )
	define( 'JUNTO_CONNECT_DIR', WP_PLUGIN_DIR . '/junto-generic-login-integration' );

require_once(LIB_PATH . '/facebook-php-sdk/src/facebook.php');
require_once (JUNTO_CONNECT_DIR . '/facebook/junto-fb-int-sfc-base.php');
require_once (JUNTO_CONNECT_DIR . '/facebook/junto-fb-int-sfc-login.php');
require_once (JUNTO_CONNECT_DIR . '/facebook/junto-fb-int-sfc-register.php');
require_once (JUNTO_CONNECT_DIR . '/facebook/facebook-adapter.php');
require_once (JUNTO_CONNECT_DIR . '/facebook/junto-facebook-hooks.php');

$facebook = facebook_adapter::getfb();

if ( !function_exists('wp_validate_auth_cookie') ) :
function wp_validate_auth_cookie($cookie = '', $scheme = '') {
	if ( ! $cookie_elements = wp_parse_auth_cookie($cookie, $scheme) ) {
		$user = sfc_login_check(false);
		if ($user)
			return $user->ID;
		do_action('auth_cookie_malformed', $cookie, $scheme);
		return false;
	}

	extract($cookie_elements, EXTR_OVERWRITE);

	$expired = $expiration;

	// Allow a grace period for POST and AJAX requests
	if ( defined('DOING_AJAX') || 'POST' == $_SERVER['REQUEST_METHOD'] )
	$expired += 3600;

	// Quick check to see if an honest cookie has expired
	if ( $expired < time() ) {
		do_action('auth_cookie_expired', $cookie_elements);
		return false;
	}

	$user = get_user_by('login', $username);
	if ( ! $user ) {
		do_action('auth_cookie_bad_username', $cookie_elements);
		return false;
	}

	$pass_frag = substr($user->user_pass, 8, 4);

	$key = wp_hash($username . $pass_frag . '|' . $expiration, $scheme);
	$hash = hash_hmac('md5', $username . '|' . $expiration, $key);

	if ( $hmac != $hash ) {
		do_action('auth_cookie_bad_hash', $cookie_elements);
		return false;
	}

	if ( $expiration < time() ) // AJAX/POST grace period set above
	$GLOBALS['login_grace_period'] = 1;

	do_action('auth_cookie_valid', $cookie_elements, $user);

	return $user->ID;
}
endif;

function junto_login_redirect( $redirect_to ) {
	global $bp, $wpdb;
	
	if (!empty( $_REQUEST['redirect_to'] ) &&
		strpos($_REQUEST['redirect_to'], $_SERVER['HTTP_HOST'])===false)
		throw new exception('potential redirect out of junto');

	// Don't mess with the redirect if this is not the root blog
	if ( is_multisite() && $wpdb->blogid != bp_get_root_blog_id() )
		return $redirect_to;

	// If the redirect doesn't contain 'wp-admin', it's OK
	if ( !empty( $_REQUEST['redirect_to'] ) && 
		(false === strpos( $_REQUEST['redirect_to'], 'wp-admin' ) ||  current_user_can('manage_options'))
		)
		return $redirect_to;

	if ( false === strpos( wp_get_referer(), 'wp-login.php' ) && false === strpos( wp_get_referer(), 'activate' ) && empty( $_REQUEST['nr'] ) )
		return wp_get_referer() ? wp_get_referer() : bp_get_root_domain();

	return bp_get_root_domain();
}
remove_filter( 'login_redirect', 'bp_core_login_redirect' );
add_filter( 'login_redirect', 'junto_login_redirect' );

function junto_logout_fb(){
	$facebook = facebook_adapter::getfb();
	$facebook->destroySession();
	
	$cookie = sfc_cookie_parse();
	if (empty($cookie)) return;
	
	// we have an FB login, log them out with a redirect
	add_action('sfc_async_init','sfc_login_logout_js');
	?>
	<html><head></head><body>
	<?php wp_head(); 
	sfc_add_base_js(); ?>
	</body></html>
<?php
exit;
}

// add logout code to async init
function sfc_login_logout_js() {
	$redirect_to = !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : 'wp-login.php?loggedout=true';
	?>
	try{
		FB.getLoginStatus(function(response) {
			if (response.authResponse) {
				FB.logout(function(response) {
					window.location.href = '<?php echo $redirect_to; ?>';
				});
			} else {
				window.location.href = '<?php echo $redirect_to; ?>';
			}
		});
	}
	catch(err){
		window.location.href = '<?php echo $redirect_to; ?>';
	}
<?php
}