<?php

define("FacebookJavascriptSdkInitializedEvent", "FacebookJavascriptSdkInitializedEvent");

class facebook_javascript_loader{
	
	private static  $loginAction;
	
	/* Adds the hooks for javascript to wordpress
	 * $loginAction sets the location to redirect to on login
	 * 'null' for reload page
	 * the string value 'none' for do nothing
	 * any other string is the value for .href
	 */
	public static function init_loader_wordpress($loginAction = null){
		//add_action('login_footer','sfc_add_base_js',20);
		//add_action('get_header','sfc_add_base_js',20);
		add_action('wp_footer', array('facebook_javascript_loader', 'fb_add_base_js'),20);
		add_action('login_footer',array('facebook_javascript_loader', 'fb_add_base_js'),20);
		add_action('init',array('facebook_javascript_loader', 'fb_channel_file'));
//		add_action('login_form',array('facebook_javascript_loader', 'print_login_button'));
//		add_action('junto_login_form',array('facebook_javascript_loader', 'print_login_button'));
		add_action('junto_fb_async',array('facebook_javascript_loader', 'loginRedirect'));
		self::$loginAction = $loginAction; 
	}
	
	/* Sets the location to redirect to on login
	 * 
	 */
	public static function loginRedirect($action = null){
		?>
		FB.Event.subscribe('auth.login',
		    	    function(response) {
		    			if (response.status === 'connected'){
		    				var action = <?php echo self::$loginAction ? "'".self::$loginAction."'" : 'null' ?>;
		    				if (action == null)
		    					location.reload();
		    				else if (action != 'none')
		    					window.location.href= action;
		    			}	
		    	    }
		    	);
		 <?php
	}
	
	public static function print_login_button(){
		echo '<div class="fb-login-button" data-show-faces="true" scope="email,user_about_me,friends_about_me,read_mailbox,read_requests" ></div>';
	}
	
	/* loads the fb js api given configured app id
	 * 
	 */
	public static function fb_add_base_js() {
		self::fb_load_js_sdk(JUNTO_FB_APP_ID);
	}

	/* Loads the api given app id
	 * crazy browser stuff from http://stackoverflow.com/questions/6125254/fb-login-dialog-does-not-close-on-google-chrome
	 * bug http://bugs.developers.facebook.net/show_bug.cgi?id=14789
	 * http://stackoverflow.com/questions/5245897/facebook-connect-works-in-firefox-internet-explorer-not-in-chrome-safari-opera
	 * //<?php do_action('junto_fb_async'); // do any other actions sub-plugins might need to do here ?>//Previously thought that facebook would be fully loaded at this time, probably not the case
	 *
	 */
	private static function fb_load_js_sdk($appid) {
		$locale = self::get_fb_locale();
	
		?>
		<div id="fb-root"></div>
        <script type="text/javascript">
            window.fbAsyncInit = function() {
                FB.init({appId: '<?php echo $appid; ?>',
                    channelUrl: '<?php echo home_url('?sfc-channel-file=1'); ?>',
                    status: true,
                    cookie: true,
                    xfbml: true,
                    oauth: true });
                FB.getLoginStatus(function(response){
                    <?php do_action(FacebookJavascriptSdkInitializedEvent);?>
                });

            };
            (function(d){
               var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
               js = d.createElement('script'); js.id = id; js.async = true;
               js.src = "//connect.facebook.net/<?php echo $locale; ?>/all.js";
               d.getElementsByTagName('head')[0].appendChild(js);
            }(document));
		</script>
		<?php
		}
	
	public static function  get_fb_locale() {
		// allow locale overrides using SFC_LOCALE define in the wp-config.php file
		if ( defined( 'SFC_LOCALE' ) ) {
			$locale = SFC_LOCALE;
		} else {
			// validate that they're using a valid locale string
			$sfc_valid_fb_locales = array(
				'ca_ES', 'cs_CZ', 'cy_GB', 'da_DK', 'de_DE', 'eu_ES', 'en_PI', 'en_UD', 'ck_US', 'en_US', 'es_LA', 'es_CL', 'es_CO', 'es_ES', 'es_MX',
				'es_VE', 'fb_FI', 'fi_FI', 'fr_FR', 'gl_ES', 'hu_HU', 'it_IT', 'ja_JP', 'ko_KR', 'nb_NO', 'nn_NO', 'nl_NL', 'pl_PL', 'pt_BR', 'pt_PT',
				'ro_RO', 'ru_RU', 'sk_SK', 'sl_SI', 'sv_SE', 'th_TH', 'tr_TR', 'ku_TR', 'zh_CN', 'zh_HK', 'zh_TW', 'fb_LT', 'af_ZA', 'sq_AL', 'hy_AM',
				'az_AZ', 'be_BY', 'bn_IN', 'bs_BA', 'bg_BG', 'hr_HR', 'nl_BE', 'en_GB', 'eo_EO', 'et_EE', 'fo_FO', 'fr_CA', 'ka_GE', 'el_GR', 'gu_IN',
				'hi_IN', 'is_IS', 'id_ID', 'ga_IE', 'jv_ID', 'kn_IN', 'kk_KZ', 'la_VA', 'lv_LV', 'li_NL', 'lt_LT', 'mk_MK', 'mg_MG', 'ms_MY', 'mt_MT',
				'mr_IN', 'mn_MN', 'ne_NP', 'pa_IN', 'rm_CH', 'sa_IN', 'sr_RS', 'so_SO', 'sw_KE', 'tl_PH', 'ta_IN', 'tt_RU', 'te_IN', 'ml_IN', 'uk_UA',
				'uz_UZ', 'vi_VN', 'xh_ZA', 'zu_ZA', 'km_KH', 'tg_TJ', 'ar_AR', 'he_IL', 'ur_PK', 'fa_IR', 'sy_SY', 'yi_DE', 'gn_PY', 'qu_PE', 'ay_BO',
				'se_NO', 'ps_AF', 'tl_ST'
			);
	
			$locale = get_locale();
	
			// convert locales like "es" to "es_ES", in case that works for the given locale (sometimes it does)
			if (strlen($locale) == 2) {
				$locale = strtolower($locale).'_'.strtoupper($locale);
			}
	
			// convert things like de-DE to de_DE
			$locale = str_replace('-', '_', $locale);
	
			// TODO make a locale conversion list, perhaps?
	
			// check to see if the locale is a valid FB one, if not, use en_US as a fallback
			if ( !in_array($locale, $sfc_valid_fb_locales) ) {
				$locale = 'en_US';
			}
		}
	
		return $locale;
	}
	
	public static function fb_channel_file() {
		if (!empty($_GET['sfc-channel-file'])) {
			$cache_expire = 60*60*24*365;
			header("Pragma: public");
			header("Cache-Control: max-age=".$cache_expire);
			header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_expire) . ' GMT');
			echo '<script src="//connect.facebook.net/'.sfc_get_locale().'/all.js"></script>';
			exit;
		}
	}
}