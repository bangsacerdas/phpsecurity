<?php
// FILE: prevent_remote_injection_include_fixed.php

// preventing remote code injection attacks
// review the comments to see security fixes

// initialize all variables
$header 	= '';
$content 	= 'Home';
$allowed 	= array('home','add','edit','delete');

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

// build a "safe" filename to include
include __DIR__ . '/include/' . $command . '.php';
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
<h3><?php echo htmlspecialchars($header); ?></h3>
<ul>
<li><a href="?cmd=home">HOME</a></li>
<li><a href="?cmd=add">ADD</a></li>
<li><a href="?cmd=edit">EDIT</a></li>
<li><a href="?cmd=delete">DELETE</a></li>
</ul>

<br />
Try these URLs:
<ul>
<li>For Linux or Mac: <pre>prevent_remote_injection_include_bad.php?cmd=../../../../../../../../etc/passwd</pre></li>
<li>For Windows (assuming c:\xampp): <pre>prevent_remote_injection_include_bad.php?cmd=../../properties.ini</pre></li>
<li>Upload the file <i>info.php</i> and then run this URL: <pre>prevent_remote_injection_include_bad.php?cmd=include/info.php</pre></li>
</ul>

<p>
<?php echo $content; ?>
</body>
</html>
