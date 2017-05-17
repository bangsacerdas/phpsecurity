<?php
// FILE: protect_access_password_control.php
// it is important to enforce proper password controls ...
// without annoying website users!

// initialize variables
$message = '';
$valid = FALSE;

// password control rules
$passwordMaxLength =  16;
$passwordMinLength =  4;

// cannot filter password as special characters are used
$password1 = (isset($_POST['password1'])) ? $_POST['password1'] : '';
$password2 = (isset($_POST['password2'])) ? $_POST['password2'] : '';

if ($password1 && $password2) {
	// make sure the 2 match
	if ($password1 === $password2) {
		// minimum length check
		if (strlen($password1) < $passwordMinLength) {
			$message[] = 'The password is too short';
		} elseif (strlen($password1) > $passwordMaxLength) {
		// maximum length check
			$message[] = 'The password is too long';
		}
		// UPPERCASE
		if (!preg_match('/[A-Z]/', $password1)) {
			$message[] = 'You must have at least 1 UPPERCASE letter';
		}
		// lowercase
		if (!preg_match('/[a-z]/', $password1)) {
			$message[] = 'You must have at least 1 lowercase letter';
		}
		// numbers
		if (!preg_match('/[0-9]/', $password1)) {
			$message[] = 'You must have at least 1 number';
		}
		// special characters
		if (!preg_match('/[^\w]/', $password1)) {
			$message[] = 'You must have at least 1 special character';
		}
	} else {
		$message[] = 'The password and confirmation do not match';
	}
} else {
	$message[] = 'Please enter a new password and confirm';
}
if ($message) {
	$valid = FALSE;
} else {
	$valid = TRUE;
	$message = array('<b style="color: green;">Password is OK!</b>');
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Password Control</title>
</head>
<body>
<h1>protect_access_password_control.php</h1>

<form method="post">
Enter New Password:
<input type="password" name="password1" />
<br />
Confirm New Password:
<input type="password" name="password2" />
<br />
<input type="submit" name="submit" value="Submit" />
</form>
<br />
<!-- state rules for creating a password -->
<!-- note that unfortunately this gives information to attackers -->
New passwords should:
<ul>
<li>Contain a mix of UPPER and lowercase letters</li>
<li>Include at least 1 number and 1 special character (i.e. !£$% etc.)</li>
<li>Be between 4 and 16 characters in length</li>
</ul>
<br />

TO TRY:
<ul>
<li>test</li>
<li>123456789</li>
<li>password123</li>
<li>Th!s15Pr0per</li>
</ul>
<br />

<ul>
<?php foreach ($message as $item) { ?>
<li><b style="color: red;"><?php echo $item; ?></b></li>
<?php } ?>
</ul>
</body>
</html>