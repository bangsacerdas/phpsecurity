<?php
// preventing session hijacking and forgery attacks
// provide a logout option!!!

session_regenerate_id();
session_start();
//session_regenerate_id();
// initialize variable
$name  = 'NOT LOGGED IN';
$value = 0;

// check to see if $_POST['name'] is set
if (isset($_POST['name'])) {
	$name = strip_tags($_POST['name']);
	$_SESSION['name'] = $name;
// check session
} elseif (isset($_SESSION['name'])) {
	$name = $_SESSION['name'];
}

// check to see if $_POST['value'] is set
if (isset($_POST['value'])) {
	$value = (float) $_POST['value'];
	$_SESSION['value'] = $value;
// check session
} elseif (isset($_SESSION['value'])) {
	$value = $_SESSION['value'];
}

// check to see if "logout" param is set
$logout = (isset($_GET['logout'])) ? TRUE : FALSE;
if ($logout) {
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

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Prevent Session Attacks</title>
</head>
<body>

<h1>prevent_session_attack_provide_logout.php</h1>
<br />

To Try:
<ul>
<li>Enter a name and press "Login"</li>
<li>Verify that you are now "logged in"</li>
<li>Refresh the page a couple of times</li>
<li>Note that the session is active</li>
<li>Click on the <i>logout</i> link</li>
<li>Verify that the old session has been destroyed</li>
</ul>

<br />Session Data:
<ul>
<li><a href="?logout=1">CLICK HERE</a> to logout</li>
<li>Time: <?php echo date('Y-m-d H:i:s'); ?></li>
<li>Name: <b><?php echo htmlspecialchars($name); ?></b></li>
<li>Value: <b><?php echo (float) $value; ?></b></li>
</ul>

<br />
<form method="post">
<br />Name: <input type="text" name="name" maxlength=128 />
<br />Value: <input type="text" name="value" />
<br /><input type="submit" value="Login" />
</form>
<?php phpinfo(INFO_VARIABLES); ?>
</body>
</html>