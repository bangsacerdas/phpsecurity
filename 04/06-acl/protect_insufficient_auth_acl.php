<?php
// FILE: protect_insufficient_auth_acl.php
// consider building an "ACL" mechanism to only allow access to certain users or user types

// database connection
require 'init_db.php';
$initDb = new InitDb();
$pdo = $initDb->getPdo();

// initialize variables
$email 		= '';
$userType 	= 'guest';

// define base arrays
$guest 	= array('home', 'products', 'specials', 'contact', 'login');
$normal	= array_merge($guest, array('detail', 'cart', 'members'));
$admin	= array_merge($normal, array('admin'));

// define ACL where key = user type
$acl = array('guest' 	=> $guest,
			 'normal'	=> $normal,
			 'admin'	=> $admin,
);

if (isset($_POST['login'])) {
	$email 	  = (isset($_POST['email'])) 		? strip_tags($_POST['email']) 		: 'guest';
	$password = (isset($_POST['credentials'])) 	? strip_tags($_POST['credentials']) : '';
	$sql = "SELECT * FROM `members` WHERE `email` = :name AND `password` = :password";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(':name' => $email, ':password' => $password));
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($result) {
		$userType = ($result['user_id'] == 99999999) ? 'admin' : 'normal';
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Insufficient Authorization</title>
<style>
td {
	border: thin solid gray;
}
th {
	border: thin solid gray;
	font-weight: bold;
}
</style>
</head>
<body>
<h1>protect_insufficient_auth_acl.php</h1>

<p>
Login as these users and view allowed pages:
<table>
<tr><th>Email</th><th>Password</th></tr>
<tr><td>admin@sweetscomplete.com</td><td>password</td></tr>
<tr><td>lucille.bradford@westmedia.com</td><td>the3399sat</td></tr>
<tr><td>guest</td><td>password</td></tr>
</table>
</p>

<p>
<form method="post">
Email: <input type="text" name="email" />
<br />
Password: <input type="password" name="credentials" />
<br />
<input type="submit" name="login" value="login" />
</form>
</p>

<p>
Logged In As: <?php echo $email; ?>
<br />User Type: <?php echo $userType; ?>
<br />Allowed Pages:
<ul><li>
<?php echo implode('</li><li>', $acl[$userType]); ?>
</li></ul>
</p>

</body>
</html>