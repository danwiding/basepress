<?php

function junto_mod_sfc_register_add_login_button() {
    global $action;
    $facebook = facebook_adapter::getfb();

    $loginParams = array('scope'=>JUNTO_FB_PERMISSIONS);
    if(!empty( $_REQUEST['redirect_to'] ))
        $loginParams['redirect_uri'] = 'https://'.$_SERVER['HTTP_HOST'] .'/wp-login.php?redirect_to='. $_REQUEST['redirect_to'];

    $loginUrl = $facebook->getLoginUrl($loginParams);

    echo '<p><a href="'.$loginUrl.'">Login with Facebook</a></p>';
}


// do the actual authentication
//
// note: Because of the way auth works in WP, sometimes you may appear to login
// with an incorrect username and password. This is because FB authentication
// worked even though normal auth didn't.
function sfc_login_check($user) {
	if ( is_a($user, 'WP_User') ) {
		return $user;
	} // check if user is already logged in, skip FB stuff
	
	$facebook = facebook_adapter::getfb();
	
	
		$fbuid=$facebook->getUser();
		
	
	if($fbuid) {
		global $wpdb;
		$user_id = $wpdb->get_var( $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'fbuid' AND meta_value = %s", $fbuid) );

		if ($user_id) {
			$_REQUEST['reauth'] = false;
			$user = new WP_User($user_id);
		} else {
			
			try{
				$data = $facebook->api('/me');
			}
			catch (exception $e){
				throw $e;
				return $user;
			}

			if (!empty($data['email'])) {
				$user_id = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_email = %s", $data['email']) );
			}

			if ($user_id) {
				$user = new WP_User($user_id);
				update_usermeta($user->ID, 'fbuid', $fbuid); // connect the account so we don't have to query this again
			}

			if (!$user_id) {
				return $user;
			}
		}
	}

	return $user;
}

// generate facebook avatar code for users who login with Facebook
function sfc_login_avatar($avatar, $id_or_email, $size = '96', $default = '', $alt = false) {

	// handle comments by registered users
	if ( is_object($id_or_email) && isset($id_or_email->user_id) && $id_or_email->user_id != 0) {
		$id_or_email = $id_or_email->user_id;
	}

	// check to be sure this is for a user id
	if ( !is_numeric($id_or_email) ) return $avatar;

	$fbuid = get_user_meta( $id_or_email, 'fbuid', true );
	if ($fbuid) {
		// return the avatar code
		return "<img width='{$size}' height='{$size}' class='avatar avatar-{$size} fbavatar' src='http://graph.facebook.com/{$fbuid}/picture?type=square' />";
	}
	return $avatar;
}

function sfc_login_update_js() {
	?>
	<script type="text/javascript">
	function sfc_login_update_fbuid(disconnect) {
		if (disconnect == 1) {
			var fbuid = 0;
		} else {
			var fbuid = 1; // it gets it from the cookie
		}
		var data = {
			action: 'update_fbuid',
			fbuid: fbuid
		}
		jQuery.post(ajaxurl, data, function(response) {
			if (disconnect == 1) {
				FB.api('/me/permissions', 'delete', function(response) {
					alert('Error occured');
					  if (!response || response.error) {
					    alert('Error occured');
					  }
				});
				FB.logout(function(response) {
					alert('Error occured');
					});
			}
			location.reload(true);
		});
	}
	</script>
	<?php
}

function sfc_login_ajax_update_fbuid() {
	$user = wp_get_current_user();

	$fbuid = (int)($_POST['fbuid']);

	if ($fbuid) {
		// get the id from the cookie
		$cookie = sfc_cookie_parse();
		if (empty($cookie)) {
			echo 1; exit;
		}
		$fbuid = $cookie['user_id'];
	} else {
		if (!SFC_ALLOW_DISCONNECT) {
			echo 1; exit();
		}
		$fbuid = 0;
		$facebook = facebook_adapter::getfb();
		$facebook->ClearSessionData();
	}

	update_usermeta($user->ID, 'fbuid', $fbuid);
	echo 1;
	exit();
}


// add the section on the user profile page
add_action('profile_personal_options','sfc_login_profile_page');

function sfc_login_profile_page($profile) {
	$options = get_option('sfc_options');
	?>
	<table class="form-table">
		<tr>
			<th><label><?php _e('Facebook Connect', 'sfc'); ?></label></th>
<?php
	$fbuid = get_user_meta($profile->ID, 'fbuid', true);
	if (empty($fbuid)) {
		?>
			<td><p><fb:login-button scope="email" v="2" size="large" onlogin="sfc_login_update_fbuid(0);"><fb:intl><?php _e('Connect this WordPress account to Facebook', 'sfc'); ?></fb:intl></fb:login-button></p></td>
		</tr>
	</table>
	<?php
	} else { ?>
		<td><p><?php _e('Connected as', 'sfc'); ?>
		<fb:profile-pic size="square" width="32" height="32" uid="<?php echo $fbuid; ?>" linked="true"></fb:profile-pic>
		<fb:name useyou="false" uid="<?php echo $fbuid; ?>"></fb:name>.
<?php if (SFC_ALLOW_DISCONNECT) { ?>
		<input type="button" class="button-primary" value="<?php _e('Disconnect this account from WordPress', 'sfc'); ?>" onclick="sfc_login_update_fbuid(1); return false;" />
<?php } ?>
		</p></td>
	<?php } ?>
	</tr>
	</table>
	<?php
}
