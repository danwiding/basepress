<?php





function log_friendly_exception(exception $exception){
	
		$line= $exception->getline();
		$message = $exception->getMessage();
		$msg="-----NEW EXCEPTION---- \n ";
		$msg.=date('l jS \of F Y h:i:s A');
		$msg.= " \n  Exception on line: \n";
		$msg.= $line;
		$msg.="\n  Exception Message:  \n";
		$msg.= "$message";
		$msg.="\n ----begin backtrace----\n";
		$msg.= $exception->getTraceasString();
		$msg.= " \n   ----end backtrace ----\n";
		$msg.=" \n  ----CLOSE EXCEPTION ----   \n  ";
		error_log($msg,3,ERRLOG_PATH);
	
			
	
	
	
	if (VIA_ENVIRONMENT != 'prod') {
		echo htmlentities($msg);
	}
	else {
		
		echo htmlentities("Placeholder error for production (staging and dev get full readout)");
	}
	
	
}

function my_Error_Handler($errno, $errstr, $errfile, $errline)
{

	if ($errno !=8 && $errno !=2 && $errno != 2048)
throw new exception(" ERROR FILE " . $errfile . " ERR NO " . $errno);


}

function var_error_handler($output)
{
    $error = error_get_last();
    $errno = $error[type];
    
    $output = "";
    
   if ( $errno != 1) {
    
    foreach ($error as $info => $string){
        $output .= "{$info}: {$string}\n";
       
        }
   return $output;
  
     }
    

   
}


//ob_start('var_error_handler');

set_exception_handler('log_friendly_exception');
set_error_handler('my_error_handler');

