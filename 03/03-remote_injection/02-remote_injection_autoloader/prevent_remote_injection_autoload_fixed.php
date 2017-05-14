<?php
// FILE: prevent_remote_injection_autoload_fixed.php

// preventing remote code injection attacks
// the vulnerability is obscured by use of an autoloader

/*
 * NOTE: for demo to work, make sure "include" folder is writeable by PHP
 */

// include autoloader
require __DIR__ . '/include/autoloader_fixed.php';

// initialize all variables
$test = '';
$allowed = array('home','add','edit','delete');

// all input should be filtered and validated!!!
$command = (isset($_GET['cmd'])) ? $_GET['cmd'] : 'home';

// filter out any non-alpha non-ASCII characters
$command = preg_replace('/[^a-zA-Z]/', '', $command);

// validate against array of commands
$key = array_search($command, $allowed, TRUE);
if ($key) {
	$command = $allowed[$key];
} else {
	$command = 'home';
}

// build "safe" classname
$class = 'Test_' . ucfirst(strtolower($command));
$run = new $class();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Prevent Code Injection</title>
</head>
<body>
<h1>prevent_remote_injection_include_fixed.php</h1>

<br />
<h3><?php echo htmlspecialchars($command); ?></h3>
<ul>
<li><a href="?cmd=home">HOME</a></li>
<li><a href="?cmd=add">ADD</a></li>
<li><a href="?cmd=edit">EDIT</a></li>
<li><a href="?cmd=delete">DELETE</a></li>
</ul>

<br />
Try this:
<ul>
<li>Upload the file <i>info.php</i></li>
<li>Run this URL: <pre>prevent_remote_injection_autoload_bad.php?cmd=info</pre></li>
</ul>

<p>
<?php echo $run->getTest(); ?>
</body>
</html>
