<?php
require_once(JUNTO_COMMON_PATH . '/oauth/oauth2/oauth2_abstract_client.php');
class AngelList_OAuth2_Client_Config{
	const client_id = ANGELLIST_OAUTH_CLIENT_ID;
	const client_secret = ANGELLIST_OAUTH_CLIENT_TOKEN;
	const auth_endpoint = "https://angel.co/api/oauth/authorize";
	const access_token_endpoint = "https://angel.co/api/oauth/token";
}

class AngelListOAuth2Client extends OAuth2AbstractClient{
	function SetClientValues(){
		$config = new AngelList_OAuth2_Client_Config();
		$this->LoadClientValues($config::client_id, 
			$config::client_secret, 
			$config::auth_endpoint, 
			$config::access_token_endpoint);
	}
}