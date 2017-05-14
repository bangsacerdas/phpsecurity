<?php
// FILE:  prevent_remote_injection_other_bad.php

// other, more obscure, forms of injection attack: eval(), exec() and system()
// best practice: DO NOT USE THESE unless absolutely needed!!!

// eval() is used to "evaluate" PHP commands,
// often used for testing, diagnostics and running code "on the fly"
// dangerous when combined with user input!

// exec() and system() both run operating system commands, but return output slightly differently
// very resource intensive and dangerous

// all user input should be filtered and validated!
$command = (isset($_GET['cmd'])) ? $_GET['cmd'] : 'default';
$input 	 = (isset($_GET['input'])) ? $_GET['input'] : '';

ob_start();
switch ($command) {
	case 'eval' :
		$output = eval($input);
		echo $output;
		break;
	case 'exec' :
		exec($input, $output);
		var_dump($output);
		break;
	case 'system' :
		$output = system($input);
		echo $output;
		break;
	default :
		echo 'default';
}
$output = ob_get_clean();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Prevent Code Injection</title>
</head>
<body>
<h1>prevent_remote_injection_other_bad.php</h1>

<br />
<h3><?php echo htmlspecialchars($command); ?></h3>
<ul>
<li>Linux / Mac:
	<ul>
	<li><a href="?cmd=eval&input=echo 'TEST';">EVAL</a></li>
	<li><a href="?cmd=eval&input=phpinfo();">EVAL</a></li>
	<li><a href="?cmd=exec&input=cat /etc/hosts">EXEC</a></li>
	<li><a href="?cmd=system&input=ls -l /*">SYSTEM</a></li>
	</ul>
	</li>
<li>Windows:
	<ul>
	<li><a href="?cmd=eval&input=echo 'TEST';">EVAL</a></li>
	<li><a href="?cmd=eval&input=phpinfo();">EVAL</a></li>
	<li><a href="?cmd=exec&input=type c:\Windows\System32\drivers\etc\hosts">EXEC</a></li>
	<li><a href="?cmd=system&input=dir \*.*">SYSTEM</a></li>
	</ul>
	</li>
</ul>

<p>
<pre>
<?php echo $output; ?>
</pre>
</body>
</html>
