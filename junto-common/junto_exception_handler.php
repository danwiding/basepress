<?php
function log_friendly_exception(exception $exception){
	// these are our templates
	$traceline = "#%s %s(%s): %s(%s)";
	$msg = "PHP Fatal error:  Uncaught exception '%s' with message '%s' in %s:%s\nStack trace:\n%s\n  thrown in %s on line %s";

	// alter your trace as you please, here
	$trace = $exception->getTrace();
	foreach ($trace as $key => $stackPoint) {
		// I'm converting arguments to their type
		// (prevents passwords from ever getting logged as anything other than 'string')
		$trace[$key]['args'] = array_map('gettype', $trace[$key]['args']!==null ? $trace[$key]['args'] : array(null));
	}

	// build your tracelines
	$result = array();
	foreach ($trace as $key => $stackPoint) {
		$result[] = sprintf(
		$traceline,
		$key,
		$stackPoint['file'],
		$stackPoint['line'],
		$stackPoint['function'],
		implode(', ', $stackPoint['args'])
		);
	}
	// trace always ends with {main}
	$result[] = '#' . ++$key . ' {main}';

	// write tracelines into main template
	$msg = sprintf(
	$msg,
	get_class($exception),
	$exception->getMessage(),
	$exception->getFile(),
	$exception->getLine(),
	implode("\n", $result),
	$exception->getFile(),
	$exception->getLine()
	);

	// log or echo as you please
	error_log($msg);

	if (VIA_ENVIRONMENT !='prod')
	echo htmlentities($msg);
}

set_exception_handler('log_friendly_exception');