<?php
function myAutoloader($class)
{
	// check to see if file exists
	$loadFile = __DIR__ . '/' . $class . '.php';
	if (file_exists($loadFile)) {
		include $loadFile;
	}
}

// NOTE: alternatively you could change the function name to __autoload() and remove line 14
spl_autoload_register('myAutoloader');
