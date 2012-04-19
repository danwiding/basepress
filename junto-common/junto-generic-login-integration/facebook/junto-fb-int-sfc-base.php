<?php

// fix up the html tag to have the FBML extensions


function sfc_add_base_js() {
	sfc_load_api(JUNTO_FB_APP_ID);
}

function sfc_get_locale() {
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

//crazy browser stuff from http://stackoverflow.com/questions/6125254/fb-login-dialog-does-not-close-on-google-chrome
//bug http://bugs.developers.facebook.net/show_bug.cgi?id=14789
//http://stackoverflow.com/questions/5245897/facebook-connect-works-in-firefox-internet-explorer-not-in-chrome-safari-opera
function sfc_load_api($appid) {
	$locale = sfc_get_locale();

	?>
<div id="fb-root"></div>
<script type="text/javascript">
  window.fbAsyncInit = function() {
    FB.init({appId: '<?php echo $appid; ?>', channelUrl: '<?php echo home_url('?sfc-channel-file=1'); ?>', status: true, cookie: true, xfbml: true, oauth: true });
    $.browser.chrome = /chrome/.test(navigator.userAgent.toLowerCase());
    if ($.browser.chrome || $.browser.msie) {
        FB.XD._origin = window.location.protocol + "//" + document.domain + "/" + FB.guid();
        FB.XD.Flash.init();
        FB.XD._transport = "flash";
      } else if ($.browser.opera) {
        FB.XD._transport = "fragment";
        FB.XD.Fragment._channelUrl = window.location.protocol + "//" + window.location.host + "/";
      }
    <?php do_action('sfc_async_init'); // do any other actions sub-plugins might need to do here ?>
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

function sfc_channel_file() {
	if (!empty($_GET['sfc-channel-file'])) {
		$cache_expire = 60*60*24*365;
		header("Pragma: public");
		header("Cache-Control: max-age=".$cache_expire);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_expire) . ' GMT');
		echo '<script src="//connect.facebook.net/'.sfc_get_locale().'/all.js"></script>';
		exit;
	}
}

// the cookie is signed using our application secret, so it's unfakable as long as you don't give away the secret
function sfc_cookie_parse() {
	$args = array();

	if (!empty($_COOKIE['fbsr_'. JUNTO_FB_APP_ID])) {
		if (list($encoded_sig, $payload) = explode('.', $_COOKIE['fbsr_'. JUNTO_FB_APP_ID], 2) ) {
			$sig = sfc_base64_url_decode($encoded_sig);
			if (hash_hmac('sha256', $payload, JUNTO_FB_APP_SECRET, true) == $sig) {
				$args = json_decode(sfc_base64_url_decode($payload), true);
			}
		}
	}

	return $args;
}

// this is not a hack or a dangerous function.. the base64 decode is required because Facebook is sending back base64 encoded data in the signed_request bits.
// See http://developers.facebook.com/docs/authentication/signed_request/ for more info
function sfc_base64_url_decode($input) {
	return base64_decode(strtr($input, '-_', '+/'));
}