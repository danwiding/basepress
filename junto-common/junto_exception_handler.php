<?php
function log_friendly_exception(exception $exception){
	$msg = print_r($exception, true);

	if (VIA_ENVIRONMENT !='prod')
	echo htmlentities($msg);
    throw new exception ("wtf");
}

set_exception_handler('log_friendly_exception');