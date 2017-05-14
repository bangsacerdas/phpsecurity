<?php
function myAutoloader($class)
{
	include $class . '.php';
}

// NOTE: alternatively you could change the function name to __autoload() and remove line 14
spl_autoload_register('myAutoloader');
