<?php 	//	MISC JUNTO FUNCTIONS


//**********     print_nice()     **********//
	// Prints Objects/Arrays in more readable format
	
	function print_nice($var, $dump = false){
		echo '<pre>';
		if ($dump)
			var_dump($var);
		else
			print_r($var);
		echo '</pre>';
	}

?>