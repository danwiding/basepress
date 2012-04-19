<?php
class OAuthExtension_Test_Client_Config{
	const consumer_key = 'key';
	const consumer_secret = 'secret';
	const signature_method = OAUTH_SIG_METHOD_HMACSHA1;
	const auth_type = OAUTH_AUTH_TYPE_URI;
	const request_token_url = 'http://term.ie/oauth/example/request_token.php';
	const access_token_url = 'http://term.ie/oauth/example/access_token.php';
}

class OAuthExtensionTestClient extends OAuthExtensionAbstractClient{
	function SetClientValues(){
		$config = new OAuthExtension_Test_Client_Config();
		$this->LoadClientValues($config::consumer_key, 
			$config::consumer_secret, 
			$config::signature_method, 
			$config::auth_type, 
			$config::request_token_url, 
			$config::access_token_url);
	}
}