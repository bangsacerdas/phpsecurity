<?php
// protecting against stored XSS attacks
// for more information on ways attackers bypass your filters:
// https://www.owasp.org/index.php/XSS_Filter_Evasion_Cheat_Sheet

// prevent information disclosure: initialize variables
$id 	= 0;
$name 	= '';
$image 	= '';

// All inputs should be tested if they are set
// if not, provide a default
$default['name']  = 'Guest';
$default['image'] = 'default.png';

// FILTERING:
// $id = numeric which means you an force the data type to (int)
$id 	= (isset($_GET['id'])) ? (int) $_GET['id'] : 0;
// $name = defaults to "Guest"; strip_tags() removes HTML or javascript tags
$name 	= (isset($_GET['name'])) ? strip_tags($_GET['name']) : $default['name'];
// strip out any non-numeric characters
$name   = preg_replace('/[^a-zA-Z0-9,. ]/', '', $name);
// $image = defaults to "default.png"
$image 	= (isset($_GET['image'])) ? strip_tags($_GET['image']) : $default['image'];

// VALIDATION:
$valid 				= 0;
$maxId 				= 999999;
$maxLength['name'] 	= 128;
$maxLength['image'] = 128;
$error 				= array('id' => '', 'name' => '', 'image' => '');

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
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Protect Against Stored XSS</title>
</head>
<body>
<h1>protect_against_xss_stored_2.php</h1>
<h2>To Notice:</h2>
<ul>
<li>Notices are gone</li>
<li><i>ID</i> now is either <i>int</i> or zero</li>
<li><i>Name</i> and <i>Image</i> now filter tags</li>
<li>Non "name" characters are removed from <i>Name</i></li>
<li>Length validation added using <i>strlen()</i></li>
<li>Look at the page source: note the use of <i>maxlength</i></li>
</ul>

<br />Try entering the following values:
<table>
<tr><th>Field</th><th>Value</th></tr>
<tr><td><i>id</i><td><pre>&lt;script>alert('XSS ATTACK');&lt;/script></pre></td></tr>
<tr><td><i>name</i></td><td><pre>%26%23x3C%3B%26%23x73%3B%26%23x63%3B%26%23x72%3B%26%23x69%3B%26%23x70%3B%26%23x74%3B%26%23x3E%3B%26%23x61%3B%26%23x6C%3B%26%23x65%3B%26%23x72%3B%26%23x74%3B%26%23x28%3B%26%23x27%3B%26%23x58%3B%26%23x53%3B%26%23x53%3B%26%23x20%3B%26%23x41%3B%26%23x54%3B%26%23x54%3B%26%23x41%3B%26%23x43%3B%26%23x4B%3B%26%23x27%3B%26%23x29%3B%26%23x3B%3B%26%23x3C%3B%26%23x2F%3B%26%23x73%3B%26%23x63%3B%26%23x72%3B%26%23x69%3B%26%23x70%3B%26%23x74%3B%26%23x3E%3B</pre></td></tr>
<tr><td><i>image</i></td><td><pre>http://localhost/verybadwebsite/xss.php</pre></td></tr>
</table>

<br />
<!-- There are a total of 3 fields to be validated -->
Data is saved: <b><?php echo ($valid == 3) ? 'YES' : 'NO'; ?></b>
<form>
<table>
<tr>
	<th>ID</th>
	<!-- NOTE: maxlength -->
	<td><input type="text" name="id" size=8 maxlength=8 /></td>
	<td>Current Value: <?php echo $id; ?></td>
	<td><?php echo $error['id']; ?></td>
</tr>
<tr>
	<th>Name</th>
	<td><input type="text" name="name" maxlength=128 /></td>
	<!-- NOTE: user-supplied output is escaped -->
	<td>Current Value: <?php echo htmlspecialchars($name); ?></td>
	<td><?php echo $error['name']; ?></td>
</tr>
<tr>
	<th>Image</th>
	<td><input type="text" name="image" maxlength=128 /></td>
	<td>Current Value: <img src="<?php echo htmlspecialchars($image); ?>" /></td>
	<td><?php echo $error['image']; ?></td>
</tr>
</table>
<input type="submit" />
</form>
</body>
</html>
