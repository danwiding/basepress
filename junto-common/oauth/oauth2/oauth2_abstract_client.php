<?php
require_once(LIB_PATH . "/oauth2/GrantType/IGrantType.php");
require_once(LIB_PATH . "/oauth2/GrantType/AuthorizationCode.php");
require_once(LIB_PATH . "/oauth2/Client.php");

define('OAUTH2_STANDARD__CODE_QUERY_KEY', 'code');

abstract class OAuth2AbstractClient{
	protected $client_id;
	protected $client_secret;
	//http://www.php.net/manual/en/oauth.setauthtype.php
	protected $auth_endpoint;
	protected $access_token_endpoint;
	protected $grantType = 'authorization_code';
	protected $client_auth;
	protected $certificate_file;
	
	protected static $oauthClientInstance;
	function GetOauthObject(){
		if (!isset($this->oauthClientInstance))
			$this->oauthClientInstance = new OAuth2\Client($this->client_id, $this->client_secret, $this->client_auth, $this->certificate_file);//$client_auth too
		return $this->oauthClientInstance;
	}
	
	function __construct() {
		$this->SetClientValues();
	}
	
	//should call loadclientvalues
	abstract function SetClientValues();
	
	protected function LoadClientValues($client_id, $client_secret, $auth_endpoint, $access_token_endpoint, $client_auth = OAuth2\Client::AUTH_TYPE_URI, $certificate_file = null){
		$this->client_id = $client_id;
		$this->client_secret = $client_secret;
		$this->auth_endpoint = $auth_endpoint;
		$this->access_token_endpoint = $access_token_endpoint;
		$this->client_auth = $client_auth;
		$this->certificate_file = $certificate_file;
	}
	
	function GetOAuthAuthenticationUrlForUser($redirectUri){
		$client = self::GetOauthObject();
		return $client->getAuthenticationUrl($this->auth_endpoint, $redirectUri);
	}
	
	function GetAccessToken($code, $redirectUri){
		$client = self::GetOauthObject();
		$params = array(OAUTH2_STANDARD__CODE_QUERY_KEY => $code, 'redirect_uri' => $redirectUri); //$_GET['code']
		$response = $client->getAccessToken($this->access_token_endpoint, $this->grantType, $params);
		if($response['code'] == '400'){
			throw new exception(print_r($response,true));
		}
		$info = $response['result'];
		$access_token = $info['access_token'];  
		if (empty($access_token))
			throw new exception("Client oauth failed fetching access token from {$this->access_token_endpoint}, response was: {$response}");
		return $access_token;
	}
	
	function AccessProtectedResource($access_token, $protected_resource_url, $parameters = array(), $http_method = OAuth2\Client::HTTP_METHOD_GET, $http_headers = array(), $form_content_type = OAuth2\Client::HTTP_FORM_CONTENT_TYPE_MULTIPART){
		$client = self::GetOauthObject();
		$client->setAccessToken($access_token);
		$response = $client->fetch($protected_resource_url, $parameters, $http_method, $http_headers, $form_content_type);
		return new OAuthClientResponse($response);
	}
	
	function GetAccessCodeAndAccessResource($code, $redirectUri, $protected_resource_url, $parameters = array(), $http_method = OAuth2\Client::HTTP_METHOD_GET, $http_headers = array(), $form_content_type = OAuth2\Client::HTTP_FORM_CONTENT_TYPE_MULTIPART){
		$access_token = $this->GetAccessToken($code, $redirectUri);
		return $this->AccessProtectedResource($access_token, $protected_resource_url, $parameters, $http_method, $http_headers, $form_content_type);
	}
}

class OAuthClientResponse{
	public $result;
	public $code;
	public $content_type;
	
	function __construct(array $resultFromOAuth2){
		$this->result = $resultFromOAuth2['result'];
		$this->code = $resultFromOAuth2['code'];
		$this->content_type = $resultFromOAuth2['content_type'];
	}
}