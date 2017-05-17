<?php
// FILE: protect_predictable_php_ini_default.php
// change the name of PHPSESSID to something else to make session attacks more difficult

// uncomment one the lines below and refresh the page:
//ini_set('session.name', 'sEcReTfRoMiNi');
//session_name('sEcReTfRoMfUnCtIoN');

session_start();

// check to see if "logout" param is set
$logout = (isset($_GET['Logout'])) ? TRUE : FALSE;
if ($logout) {
	// unset all $_SESSION data
	$_SESSION = array();
	// expire the session cookie
	setcookie('PHPSESSID', 1, time() - 3600, '/');
	setcookie('sEcReTfRoMiNi', 1, time() - 3600, '/');
	setcookie('sEcReTfRoMfUnCtIoN', 1, time() - 3600, '/');
	// destroy session
	session_destroy();
} else {
	$_SESSION['test'] = 'TEST';
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Avoid Predictable Resource</title>
</head>
<body>
<h1>protect_predictable_php_ini_default.php</h1>

<p>
To Do:
<ul>
<li>Refresh the page and notice PHP session identifier in phpinfo()</li>
<li>Click <i>Logout</i></li>
<li>Uncomment the lines indicated above</li>
<li>Click <i>GO BACK</i> and note the change</li>
</ul>
<br /><a href="protect_predictable_php_ini_default.php">GO BACK</a>
</p>

<p>
<form>
<input type="submit" name="Logout" value="Logout" />
</form>
</p>

<p>
Session Data: <?php echo (isset($_SESSION['test'])) ? $_SESSION['test'] : 'NOT SET'; ?>
<?php phpinfo(INFO_VARIABLES); ?>
</p>

</body>
</html>
