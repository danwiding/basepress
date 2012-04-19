<?php

add_action('login_enqueue_scripts','sfc_register_enqueue_scripts');
function sfc_register_enqueue_scripts() {
	wp_enqueue_script('jquery');
}

add_action('sfc_login_new_fb_user', 'sfc_register_redirect');
function sfc_register_redirect() {
	wp_redirect(site_url('wp-login.php?action=register', 'login'));
	exit;
}

add_action('junto_register_form','junto_add_register_async');
function junto_add_register_async(){
	$cookie = sfc_cookie_parse();
	if (empty($cookie)) return;

	// we have an FB login, log them out with a redirect
	add_action('sfc_async_init','junto_fb_register_fields_js');
}

// add register fields code to async init for register page
function junto_fb_register_fields_js() {
	?>
	$('#email').val('2');
	FB.getLoginStatus(function(response) {
		if (response.status === 'unknown')
			return;
		//if (response.status === 'connected')
		FB.api('/me', function(response) {
			$('input[name="email"]').val(response.email);
			$('input[name="email"]').disabled=true;
			$('input[name="first-name"]').val(response.first_name);
			$('input[name="last-name"]').val(response.last_name);
			$('input[name="website"]').val(response.user_website);
		});
	});
	<?php
}
