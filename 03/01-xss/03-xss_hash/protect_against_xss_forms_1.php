<?php
// protecting forms against XSS attacks
// refinement: one time use hash
// why use: prevents automated systems from hijacking and auto-submitting the same form repeatedly

// prevent information disclosure: initialize variables
$id 	= 0;
$name 	= '';
$image 	= '';
$hash 	= '';

// All inputs should be tested if they are set
// if not, provide a default
$default['name']  = 'Guest';
$default['image'] = 'default.png';

// FILTERING:
// $id = numeric which means you an force the data type to (int)
$id 	= (isset($_POST['id'])) ? (int) $_POST['id'] : 0;
// $name = defaults to "Guest"; strip_tags() removes HTML or javascript tags
$name 	= (isset($_POST['name'])) ? strip_tags($_POST['name']) : $default['name'];
// strip out any non-numeric characters
$name   = preg_replace('/[^a-zA-Z0-9,. ]/', '', $name);
// $image = defaults to "default.png"
$image 	= (isset($_POST['image'])) ? strip_tags($_POST['image']) : $default['image'];

// VALIDATION:
$valid 				= 0;
$maxId 				= 999999;
$maxLength['name'] 	= 128;
$maxLength['image'] = 128;
$error 				= array('id' => '', 'name' => '', 'image' => '', 'hash' => '');

// remember: $id has already been filtered
if ($id > $maxId) {
	$error['id'] = 'Please re-enter your ID number';
} else {
	$valid++;
}

// $name = length check
if (strlen($name) > $maxLength['name']) {
	$name = $default['name'];
	$error['name'] = 'Name must not exceed 128 letters or characters in length';
} else {
	$valid++;
}

// $image = length check + check filename extension
if (strlen($image) > $maxLength['image']) {
	$image = $default['image'];
	$error['image'] = 'Image reference must not exceed 128 letters or characters in length';
} elseif (!preg_match('/.*(jpg|png)$/i', $image)) {
	$image = $default['image'];
	$error['image'] = 'Only "jpg" and "png" images are accepted';
} else {
	$valid++;
}

// ADDITIONAL REFINEMENT: one time use hash
session_start();
// check to see if hash is stored in the session
if (isset($_SESSION['hash'])) {
	// if so, does the posted hash match the stored hash?
	if (isset($_POST['hash']) && $_POST['hash'] == $_SESSION['hash']) {
		$valid++;
	} else {
		$error['hash'] = 'Please re-submit the form';
	}
}
// generate 1 time hash from UNIX timestamp + visitor's IP address
// do this each time form is generated
$hash = md5(time() . $_SERVER['REMOTE_ADDR']);
$_SESSION['hash'] = $hash;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Protecting Forms Against XSS</title>
</head>
<body>
<h1>protect_against_xss_forms_1.php</h1>

Same as <i>protect_against_xss_stored_2.php</i> with the
additional refinement of a one-time hidden hash element.

<hr />
<!-- There are a total of 4 fields to be validated -->
Data is saved: <b><?php echo ($valid == 4) ? 'YES' : 'NO'; ?></b>
<hr />
<form method="post" action="protect_against_xss_forms_1.php">
<table>
<tr>
	<th>ID</th>
	<!-- NOTE: maxlength -->
	<td><input type="text" name="id" size=8 maxlength=8 value="<?php echo $id; ?>" /></td>
	<td>Current Value: <?php echo $id; ?></td>
	<td><b style="color:red;"><?php echo $error['id']; ?></b></td>
</tr>
<tr>
	<th>Name</th>
	<td><input type="text" name="name" maxlength=128 value="<?php echo htmlspecialchars($name); ?>" /></td>
	<!-- NOTE: user-supplied output is escaped -->
	<td>Current Value: <?php echo htmlspecialchars($name); ?></td>
	<td><b style="color:red;"><?php echo $error['name']; ?></b></td>
</tr>
<tr>
	<th>Image</th>
	<td><input type="text" name="image" maxlength=128 value="<?php echo htmlspecialchars($image); ?>" /></td>
	<td>Current Value: <img src="<?php echo htmlspecialchars($image); ?>" /></td>
	<td><b style="color:red;"><?php echo $error['image']; ?></b></td>
</tr>
</table>
<!-- NOTE: $hash does NOT involve user input and should *not* be escaped -->
<input type="hidden" name="hash" value="<?php echo $hash; ?>" />
<br /><b style="color:red;"><?php echo $error['hash']; ?></b>
<br /><input type="submit" />
</form>
</body>
</html>