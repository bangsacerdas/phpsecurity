<?php
// preventing session hijacking and forgery attacks
// for sensitive operations (i.e. financial transactions) limit the session expiration time
// default is 180 minutes

// set up timer file using session ID for a filename
// NOTE: (1) do not combine this technique with session_regenerate_id()!!!
//       (2) make sure timer log directory is writeable by PHP

session_start();

// initialize variables
$maxTime 	= 15;		// sets maximum session time to 15 seconds
$newTime 	= time();
$oldTime 	= $newTime;
$timerDir  	= __DIR__ . '/time/';
$timerFile 	= $timerDir . session_id() . '.timer.log';

// read or write time to the timer file
if (file_exists($timerFile)) {
	$oldTime = file_get_contents($timerFile);
} else {
	file_put_contents($timerFile, $oldTime);
}

// check to see if difference is > $maxTime
$timeDiff = $newTime - $oldTime;
if ($timeDiff > $maxTime) {
	// get rid of timer file
	if (file_exists($timerFile)) { unlink($timerFile); }
	// unset all $_SESSION data
	$_SESSION = array();
	// expire the session cookie
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(	session_name(), '',
				  	time() - 3600,
					$params["path"],
					$params["domain"],
					$params["secure"],
					$params["httponly"]
		);
	}
	// destroy session
	session_destroy();
}


var_dump($_SESSION);
$name="NOT LOGGED IN";
// initialize variable
// check to see if $_POST['name'] is set
if (isset($_POST['name'])) {
	$name = strip_tags($_POST['name']);
    $_SESSION['name']=$name;
}
//check sessio
if (!array_key_exists('name',$_SESSION) && empty( $_SESSION['name'])){
    $name="NOT LOGGED IN";
}
else {

    $name=$_SESSION['name'];
}

// store name into $_SESSION
if ($name != 'NOT LOGGED IN') {
	$_SESSION['name'] = $name;
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Prevent Session Attacks</title>
</head>
<body>

<h1>prevent_session_attack_keep_session_short.php</h1>
<br />

To Try:
<ul>
<li>Enter a name and press "Login"</li>
<li>Verify that you are now "logged in"</li>
<li>Refresh the page</li>
<li>Note that the session has expired after 15 seconds </li>
</ul>

<ul>
<li>Time: <?php echo date('Y-m-d H:i:s'); ?></li>
<li>Difference: <?php echo $timeDiff; ?></li>
</ul>
<br />
<form method="post">
Name: <input type="text" name="name" maxlength=128 />
<br /><input type="submit" value="Login" />
<br />Logged In As: <b><?php echo htmlspecialchars($name); ?></b>
</form>
<?php phpinfo(INFO_VARIABLES); ?>
</body>
</html>
