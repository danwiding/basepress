<?php

class facebook_adapter{
	private static $facebook;
	
	public static function getfb(){
		if(!isset(self::$facebook)){
			self::$facebook = new junto_facebook( array(
					    'appId'  => JUNTO_FB_APP_ID,
					    'secret' => JUNTO_FB_APP_SECRET));
		}
		return self::$facebook;
	}
}

class junto_facebook extends Facebook{
	public function api(/* polymorphic */) {
		try {
			$args = func_get_args();
			if (is_array($args[0])) {
				return $this->_restserver($args[0]);
			} else {
				return call_user_func_array(array($this, '_graph'), $args);
			}
		} catch (OAuthException $e) {
			$this->ClearSessionData();
			log_friendly_exception($e);
		} catch (FacebookApiException $e){
			if (strpos($e->getMessage(), 'name lookup timed out') !== false ||
			strpos($e->getMessage(), 'timeout') !== false ||
			strpos($e->getMessage(), 'Error validating access token: Session has expired') !== false ||
            strpos($e->getMessage(), 'An active access token must be used to query information about the current user') !== false){
				$this->ClearSessionData();
				log_friendly_exception($e);
			} else{
				throw $e;
			}
		}
	}

	public function ClearSessionData(){
		$this->destroySession();
		setcookie($this->getSignedRequestCookieName(),'', time()-3600, '/', '.'.$_SERVER['HTTP_HOST']);
	}
}

