<?php
abstract class OAuthExtensionAbstractClient{
	protected $consumerKey;
	protected $consumerSecret;
	protected $signatureMethod;
	//http://www.php.net/manual/en/oauth.setauthtype.php
	protected $authType;
	protected $requestTokenUrl;
	protected $accessTokenUrl;
	
	protected static $oauthInstance;
	function GetOauthObject(){
		if (!isset($this->oauthInstance))
			$this->oauthInstance = new OAuth($this->consumerKey, $this->consumerSecret, $this->signatureMethod, $this->authType);
		return $this->oauthInstance;
	}
	
	function __construct() {
		$this->SetClientValues();
	}
	
	//should call loadclientvalues
	abstract function SetClientValues();
	
	protected function LoadClientValues($consumerKey, $consumerSecret, $signatureMethod, $authType, $requestTokenUrl, $accessTokenUrl){
		$this->consumerKey = $consumerKey;
		$this->consumerSecret = $consumerSecret;
		$this->signatureMethod = $signatureMethod;
		$this->authType = $authType;
		$this->requestTokenUrl = $requestTokenUrl;
		$this->accessTokenUrl = $accessTokenUrl;
	}
	
	function GetRequestToken($returnUrl = 'oob'){
		$oauth = self::GetOauthObject();
		$request_token_info = $oauth->getRequestToken($this->requestTokenUrl, $returnUrl);
		
		if (empty($request_token_info))
			throw new exception("Client oauth failed fetching request token from {$this->requestTokenUrl}, response was: {$oauth->getLastResponse()}");
		return $request_token_info;
	}
	
	function GetAccessToken($authorized_request_token_info){
		$oauth = self::GetOauthObject();
		$requestToken = $authorized_request_token_info[OAUTH1_TOKEN_INFO__TOKEN];
		$requestSecret = $authorized_request_token_info[OAUTH1_TOKEN_INFO__SECRET];
		$oauth->setToken($requestToken,$requestSecret);
		$access_token_info = $oauth->getAccessToken($this->accessTokenUrl);
		
		if (empty($access_token_info))
			throw new exception("Client oauth failed fetching access token from {$this->accessTokenUrl}, response was: {$oauth->getLastResponse()}");
		return $access_token_info;
	}
	
	function AccessProtectedResource($access_token_info, $url, $extra_params = null, $method = OAUTH_HTTP_METHOD_GET, $http_headers = array()){
		$oauth = self::GetOauthObject();
		$accessToken = $access_token_info[OAUTH1_TOKEN_INFO__TOKEN];
		$accessSecret = $access_token_info[OAUTH1_TOKEN_INFO__SECRET];
		$oauth->setToken($accessToken,$accessSecret);
		$oauth->fetch($url, $extra_params, $method, $http_headers);
		//$response_info = $oauth->getLastResponseInfo();
		$response = $oauth->getLastResponse();
		return $response;
	}
}