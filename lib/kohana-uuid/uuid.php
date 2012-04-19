<?php
require_once 'kohana/uuid.php';

class UUID extends Kohana_UUID {  
	public static function v5combo($name){
		return self::v5(self::v4(), $name);
	}
}
