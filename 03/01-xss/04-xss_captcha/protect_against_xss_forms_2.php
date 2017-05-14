<?php

session_cache_limiter('nocache');	// sets headers to not cache; does not work for IE; see NOTE above
session_start();




// All inputs should be tested if they are set
// if not, provide a default
$default['name']  = 'Guest';
$default['image'] = 'default.png';

// prevent information disclosure: initialize variables
$unique			= date('YmdHis');		// added to <form> action to force IE to not cache
$id 			= 0;
$name 			= $default['name'];
$image 			= $default['image'];
$valid 			= 0;
$error 			= array('id' => '', 'name' => '', 'image' => '', 'captcha' => '');

// check to see if data has been posted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

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
	$maxId = 999999;
	$maxLength['name'] = 128;
	$maxLength['image'] = 128;

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

	// ADDITIONAL REFINEMENT: CAPTCHA


    if(isset($_POST["phrase"])&&$_POST["phrase"]!=""&&$_SESSION["code"]==$_POST["phrase"])
    {
        $valid++;
    }
    else
    {
        $error['captcha'] = 'Please enter CAPTCHA info again';
    }

}


?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='0'>
<meta http-equiv='pragma' content='no-cache'>
<title>Protecting Forms Against XSS</title>
</head>
<body>
<h1>protect_against_xss_forms_2.php</h1>

Same as <i>protect_against_xss_forms_1.php</i> with the
additional refinement of a CAPTCHA element.

<hr />
<!-- There are a total of 4 fields to be validated -->
Data is saved: <b><?php echo ($valid == 4) ? 'YES' : 'NO'; ?></b>
<hr />
<form method="post" action="protect_against_xss_forms_2.php">
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
<tr>
	<th>CAPTCHA</th>
	<td><input type="text" name="phrase" maxlength=128 /></td>
	<td><img src="captcha.php" /></td>
	<td><b style="color:red;"><?php echo $error['captcha']; ?></b></td>
</tr>
</table>
<br /><input type="submit" />
</form>
</body>
</html>
