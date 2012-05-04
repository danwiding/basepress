<?php


function log_friendly_exception(exception $exception){
	if(defined("LOG_PATH")){
		
		$destination=LOG_PATH;
		$line= $exception->getline();
		$message = $exception->getMessage();
		$msg="-----NEW ERROR---- \n ";
		$msg.=date('l jS \of F Y h:i:s A');
		$msg.= " \n  Error on line: \n";
		$msg.= $line;
		$msg.="\n  Error Message:  \n";
		$msg.= "$message";
		$msg.="\n ----begin backtrace----\n";
		$msg.= $exception->getTraceasString();
		$msg.= " \n   ----end backtrace ----\n";
		$msg.=" \n  ----CLOSE ERROR ----   \n  ";
		//error_log($msg);
		error_log($msg,3,$destination);
	}
	else{
		
		$line= $exception->getline();
		$message = $exception->getMessage();
		$msg="-----NEW ERROR---- \n ";
		$msg.=date('l jS \of F Y h:i:s A');
		$msg.= " \n  Error on line: \n";
		$msg.= $line;
		$msg.="\n  Error Message:  \n";
		$msg.= "$message";
		$msg.="\n ----begin backtrace----\n";
		$msg.= $exception->getTraceasString();
		$msg.= " \n   ----end backtrace ----\n";
		$msg.=" \n  ----CLOSE ERROR ----   \n  ";
		error_log($msg);
		
	}
	
	
	if (VIA_ENVIRONMENT == 'prod') {
		echo htmlentities($msg);
	}
	else {
		
		echo htmlentities("Placeholder error for production (staging and dev get full readout)");
	}
	
}

//<!-- function log_friendly_exception(exception $exception){
//	$msg = print_r($exception, true);

//	if (VIA_ENVIRONMENT !='prod')
//	echo htmlentities($msg);
//    throw new exception ("wtf");
//} -->




set_exception_handler('log_friendly_exception');
set_error_handler('log_friendly_exception');


//throw new exception("WHY SO SERIOUS?!");