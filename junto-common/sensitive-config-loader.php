<?php
function SensitiveConfigLoader($path){
	$sensitiveJSONDataString = file_get_contents($path);
	if (!$sensitiveJSONDataString)
	throw new exception("sensitive data file is missing");
	$sensitiveArray = json_decode($sensitiveJSONDataString, true);
	foreach ($sensitiveArray as $topKey => $topValue){
        if(!defined($topKey))
            define($topKey, $topValue);
	}
}