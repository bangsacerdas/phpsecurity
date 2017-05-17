<?php
// FILE: protect_access_storage_md5.php
// you can use the "md5()" function to create a quick hash for storage
// advantages: very fast, easy to use, available with all PHP installations
// disadvantages: not as secure as once thought, but at least it will slow the attacker down

// initialize variables
$pwdFile = 'protect_access_storage_md5.pwd';
$match 	 = FALSE;
$output  = 'No Match';

// accept password and convert to md5 hash
$password = (isset($_POST['password'])) ? md5($_POST['password']) 		 : '';
$username = (isset($_POST['username'])) ? strip_tags($_POST['username']) : '';
$composite = $username . ':' . $password;

// store in pwd file if new
if (isset($_POST['new'])) {
	file_put_contents($pwdFile, $composite . '&', FILE_APPEND);
}

// read usernames and passwords from file
$userList = (file_exists($pwdFile)) ? explode('&', file_get_contents($pwdFile)) : array();

// check to see if username/password matches what is stored in file
// NOTE: you never need to reverse the hash!
$found = array_search($composite, $userList, TRUE);
if ($found) {
	$match = TRUE;
	$output = $userList[$found];
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Password Control</title>
</head>
<body>
<h1>protect_access_storage_md5.php</h1>

<br />
To Do:
<ul>
<li>Enter a username + password + check "new"</li>
<li>Enter another username + password + check "new"</li>
<li>Enter the 1st username + password: should be OK</li>
<li>Enter the 1st username with a wrong password: should not match</li>
</ul>

<br />
<form method="post">
Username:
<input type="text" name="username" />
<br />
Password:
<input type="password" name="password" />
<br />
New User? &nbsp;
<input type="checkbox" name="new" value="1" />
<br />
<input type="submit" name="submit" value="Submit" />
</form>

<br />
Successful Login: 
<?php echo ($match) ? '<b style="color:green;">YES</b>' : '<b style="color:red;">NO</b>' ; ?>
<br />
Matching Item:
<?php echo $output; ?>
<br />
User List:
<pre>
<?php echo var_dump($userList); ?>
</pre>

<?php phpinfo(INFO_VARIABLES); ?>
</body>
</html>