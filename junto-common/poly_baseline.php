<?php 	//	BASELINE POLYMATHIC FUNCTIONS

//**********     Include View File     **********//

	function views($include, $once = false){
		$uri = POLY_THEME_PATH.'/'.get_option('template');
		return ($once ? include_once($uri.'/__views/'.$include) : include($uri.'/__views/'.$include));
	}

//**********     Force SSL     **********//

	function force_ssl(){
		if (!is_ssl() && defined('VIA_ENVIRONMENT') && VIA_ENVIRONMENT == 'prod'){
			if (0 === strpos($_SERVER['REQUEST_URI'], 'http')):
				wp_redirect(preg_replace('|^http://|', 'https://', $_SERVER['REQUEST_URI']));
				exit();
			else:
				wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
				exit();
			endif;
		}
	}

//**********     Print Nice / Krumo     **********//

	include_once('junto-content/krumo/class.krumo.php');

	function print_nice($var, $dump = false){
		echo '<pre>';
		($dump ? var_dump($var) : print_r($var));
		echo '</pre>';
	}

//**********     Browser Specific Body Classes     **********//

    add_filter('body_class', 'polymathic_browser_body_classes');
    function polymathic_browser_body_classes($classes){
        global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;
        if 		($is_lynx)		$classes[] = 'lynx';
        elseif 	($is_gecko)		$classes[] = 'gecko';
        elseif	($is_opera)		$classes[] = 'opera';
        elseif 	($is_NS4)		$classes[] = 'ns4';
        elseif 	($is_safari)	$classes[] = 'safari';
        elseif	($is_chrome)	$classes[] = 'chrome';
        elseif	($is_IE){
            $classes[] = 'ie';
            if (preg_match('/MSIE ( [0-9]+ )( [a-zA-Z0-9.]+ )/', $_SERVER['HTTP_USER_AGENT'], $browser_version)):
           		$classes[] = 'ie' . $browser_version[1];
           	endif;
        } else 	$classes[] = 'unknown';
        if		($is_iphone)	$classes[] = 'iphone';
        return $classes;
    }

//**********     Disable WP Admin Bar     **********//

	if (!defined('ALLOW_ADMINBAR')):
		wp_deregister_script('admin-bar');
		wp_deregister_style('admin-bar');

		remove_action('wp_footer','wp_admin_bar_render',1000);
	 	remove_action('admin_footer', 'wp_admin_bar_render', 1000);
		remove_action('personal_options', '_admin_bar_preferences');

	    add_filter('admin_head','remove_admin_bar_style_backend');
	    function remove_admin_bar_style_backend(){
			echo '<style>body.admin-bar #wpcontent, body.admin-bar #adminmenu { padding-top: 0px !important; }</style>';
	    }

	    add_filter('wp_head','remove_admin_bar_style_frontend', 99);
	    function remove_admin_bar_style_frontend(){
			echo '<style type="text/css" media="screen">
			html { margin-top: 0px !important; }
			* html body { margin-top: 0px !important; }
			</style>';
	    }
	endif;
?>