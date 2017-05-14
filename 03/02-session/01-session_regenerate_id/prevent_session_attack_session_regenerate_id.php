<?php
// preventing session hijacking and forgery attacks
// use session_regenerate_id() to maintain session data but generate a new ID

session_start();
// 2nd time: uncomment the line below
//session_regenerate_id();

// initialize variable
$name = NULL;

// check to see if $_GET['name'] is set
if (isset($_GET['name'])) {
	$name = strip_tags($_GET['name']);
// check session
} elseif (isset($_SESSION['name'])) {
	$name = $_SESSION['name'];
}

// store name into $_SESSION
if ($name) {
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

<h1>prevent_session_attack_session_regenerate_id.php</h1>
<br />

To Try:
<ul>
<li>Enter a name and press "Login"</li>
<li>Make note of the session identifier PHPSESSID</li>
<li>Open another browser and go to this web page</li>
<li>Set the value of the PHPSESSID cookie to the session identifier noted above</li>
<li>Verify that you are now "logged in"</li>
</ul>

<br />Next Time:
<ul>
<li>Uncomment the command <i>session_regenerate_id()</i></li>
<li>Try the procedure above again and note the difference</li>
</ul>
<br />

<form>
Name: <input type="text" name="name" maxlength=128 />
<br /><input type="submit" value="Login" />
<br />Logged In As: <b><?php echo htmlspecialchars($name); ?></b>
</form>
<?php phpinfo(INFO_VARIABLES); ?>
</body>
</html>
