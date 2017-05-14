<?php
// preventing session hijacking and forgery attacks
// set up profile file based on composite profile
// can be used with other techniques to perform secondary user validation

// NOTE: (1) make sure profile log directory is writeable by PHP
//		 (2) assumes login names are unique

// database connection
require 'init_db.php';
$init = new InitDb();
$pdo  = $init->getPdo();

// start session
session_start();
session_regenerate_id();

// initialize variables
$validUser 	= TRUE;
$name 		= 'NOT LOGGED IN';
$pwd  		= '';
$maxTime 	= 300;		// sets maximum session time to 5 minutes
$error 	 	= array('Date' => date('Y-m-d H:i:s'));
$oldProfile = array('time' => time(), 'userAgt' => '', 'lang' => '', 'ipAddr' => '');

// build profile
$profile['time'] 	= time();
$profile['userAgt'] = md5($_SERVER['HTTP_USER_AGENT']);
$profile['lang'] 	= md5($_SERVER['HTTP_ACCEPT_LANGUAGE']);
$profile['ipAddr'] 	= md5($_SERVER['REMOTE_ADDR']);

// check to see if user is logging in
if (isset($_POST['login'])) {
	$name = (isset($_POST['name'])) ? strip_tags($_POST['name']) : NULL;
	$pwd  = (isset($_POST['pwd']))  ? md5($_POST['pwd']) 		 : NULL;
	// do database lookup
	$sql = 'SELECT * FROM `members` WHERE `email` = ?';
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array($name));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	// confirm password
	if ($row && $pwd == md5($row['password'])) {
		$name = $row['name'];
		$_SESSION['name'] = $name;
	} else {
		$name = 'NOT LOGGED IN';
		unset($_SESSION['name']);
		$validUser = FALSE;
		$error[] = 'Invalid username or password';
	}
}

// set up profile filename and dir
$profileDir  	= __DIR__ . '/profile/';
$profileFile 	= $profileDir . preg_replace('/[^a-zA-Z]/', '', $name) . '.timer.log';

// if $_SESSION['name'] is set, build profile and validate
if (isset($_SESSION['name'])) {

	$name = $_SESSION['name'];
	// restore old profile file
	if (file_exists($profileFile)) {
		$oldProfile = unserialize(file_get_contents($profileFile));
	} else {
		// 1st time: dump profile and set old to new profile
		file_put_contents($profileFile, serialize($profile));
		$oldProfile = $profile;
	}

	// check for differences in name, time, user agent, language and IP address
	if ($profile['time'] - $oldProfile['time'] > $maxTime) {
		$validUser = FALSE;
		$error[] = 'Time exceeded';
	}
	if ($profile['userAgt'] != $oldProfile['userAgt']) {
		$validUser = FALSE;
		$error[] = 'User agent does not match';
	}
	if ($profile['lang'] != $oldProfile['lang']) {
		$validUser = FALSE;
		$error[] = 'Language does not match';
	}
	if ($profile['ipAddr'] != $oldProfile['ipAddr']) {
		$validUser = FALSE;
		$error[] = 'IP address does not match';
	}
}

if (!$validUser || (isset($_POST['logout']))) {
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
	// log errors
	error_log(implode(':', $error), 0);
	// get rid of profile file
	if (file_exists($profileFile)) {
		unlink($profileFile);
	}
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Prevent Session Attacks</title>
</head>
<body>

<h1>prevent_session_attack_build_profile.php</h1>
<br />

To Try:
<ul>
<li>Test Login Name: <i>conrad.perry@fastmedia.com</i></li>
<li>Test Login Password: <i>listened8591uncl</i></li>
<li>Verify that you are now "logged in"</li>
<li>Try logging in from another browser</li>
<li>See whether or not you are successful</li>
</ul>

<ul>
<li>Time: <?php echo date('Y-m-d H:i:s'); ?></li>
<li>Errors: 	 <pre><?php echo implode("\n", $error); ?>		</pre></li>
<li>New Profile: <pre><?php echo implode("\n", $profile); ?>	</pre></li>
<li>Old Profile: <pre><?php echo implode("\n", $oldProfile); ?>	</pre></li>
</ul>

<br />
<form method="post">
Login Name: <input type="text" name="name" maxlength=128 />
Password: 	<input type="password" name="pwd" />
<br /><input type="submit" value="Login" name="login" /><input type="submit" value="Logout" name="logout" />
<br />Logged In As: <b><?php echo htmlspecialchars($name); ?></b>
</form>

<?php phpinfo(INFO_VARIABLES); ?>
</body>
</html>
