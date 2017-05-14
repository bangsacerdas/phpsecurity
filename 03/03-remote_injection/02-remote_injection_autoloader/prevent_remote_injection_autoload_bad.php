<?php
// FILE: prevent_remote_injection_autoload_bad.php

// preventing remote code injection attacks
// the vulnerability is obscured by use of an autoloader

/*
 * NOTE: for demo to work, make sure "include" folder is writeable by PHP
 */

// include autoloader
require __DIR__ . '/include/autoloader.php';

// initialize all variables
$test = '';

// all input should be filtered and validated!!!
$command = (isset($_GET['cmd'])) ? $_GET['cmd'] : 'Test_Home';
$run = new $command();
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
<h3><?php echo htmlspecialchars($command); ?></h3>
<ul>
<li><a href="?cmd=Test_Home">HOME</a></li>
<li><a href="?cmd=Test_Add">ADD</a></li>
<li><a href="?cmd=Test_Edit">EDIT</a></li>
<li><a href="?cmd=Test_Delete">DELETE</a></li>
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
