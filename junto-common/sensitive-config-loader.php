<?php
function SensitiveConfigLoader($path){
	$sensitiveJSONDataString = file_get_contents($path);
	if (!$sensitiveJSONDataString)
	throw new exception("sensitive data file is missing");
	$sensitiveArray = json_decode($sensitiveJSONDataString, true);
	foreach ($sensitiveArray as $topKey => $topValue){
		if(is_array($topValue)){
			foreach ($sensitiveArrayLevel as $secondaryKey => $secondaryValue){
				define($secondaryKey, $secondaryValue);
			}
		} else {
			define($topKey, $topValue);
		}
	}
}