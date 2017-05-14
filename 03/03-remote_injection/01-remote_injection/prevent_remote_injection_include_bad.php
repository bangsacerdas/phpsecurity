<?php
// FILE: prevent_remote_injection_include_bad.php

// preventing remote code injection attacks
// the vulnerability exists when user input is mixed with one of the "include" commands
// the problem is even worse if unrestricted file uploads are allowed!

/*
 * NOTE: for demo to work, make sure "include" folder is writeable by PHP
 */

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
<h1>prevent_remote_injection_include_bad.php</h1>

<br />
<h3><?php echo htmlspecialchars($header); ?></h3>
<ul>
<li><a href="?cmd=include/home.php">HOME</a></li>
<li><a href="?cmd=include/add.php">ADD</a></li>
<li><a href="?cmd=include/edit.php">EDIT</a></li>
<li><a href="?cmd=include/delete.php">DELETE</a></li>
</ul>

<br />
Try these URLs:
<ul>
<li>For Linux or Mac: <pre>prevent_remote_injection_include_bad.php?cmd=../../../../../../../../etc/passwd</pre></li>
<li>For Windows (assuming c:\xampp): <pre>prevent_remote_injection_include_bad.php?cmd=../../properties.ini</pre></li>
<li>Select ADD and upload the file <i>info.php</i> from /path/to/htdocs/verybadwebsite</li>
<li>Run this URL: <pre>prevent_remote_injection_include_bad.php?cmd=include/info.php</pre></li>
</ul>

<p>
<?php echo $content; ?>
</body>
</html>
