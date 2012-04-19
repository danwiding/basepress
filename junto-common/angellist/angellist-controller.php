<?php
require_once(JUNTO_COMMON_PATH . '/oauth/oauth2/angellist-oauth-client.php');
define('JUNTO_REDIRECT_URI', 'http://thejun.to/angellistreturn');
define('ANGELLIST_API_ENDPOINT__ME', 'https://api.angel.co/1/me');


class AngelListController{
	//will try to put in a returnuri ...
	static function GetAuthUrl($redirectUri){
		$angellistOAuthClient = new AngelListOAuth2Client();
		return $angellistOAuthClient->GetOAuthAuthenticationUrlForUser(JUNTO_REDIRECT_URI);
	}
	
	static function GetAngelListUserFromAccessCode($params = null){
		if (isset($params))
			$code = $params[OAUTH2_STANDARD__CODE_QUERY_KEY];
		else
			$code = $_GET[OAUTH2_STANDARD__CODE_QUERY_KEY];
		$angellistOAuthClient = new AngelListOAuth2Client();
		$oauthResult = $angellistOAuthClient->GetAccessCodeAndAccessResource($code, JUNTO_REDIRECT_URI, ANGELLIST_API_ENDPOINT__ME);
		//throw new exception (print_r($oauthResult, true));
		$responseBody = $oauthResult->result;
		//if (preg_match('Unauthorized',$responseBody))
		//	return false;
		//$angellistUser = json_decode($responseBody);
		$roles = $responseBody[roles];
		foreach ($roles as $role){
			if ($role[name] == 'investor')
				return true;
		}
		return false;
		
	}
}