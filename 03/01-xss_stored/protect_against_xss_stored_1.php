<?php
// protecting against stored XSS attacks

// All inputs should be tested if they are set, and then properly filtered
$id 	= $_GET['id'];
$name 	= $_GET['name'];
$image 	= $_GET['image'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Protect Against Stored XSS</title>
<style>
table {
	width: 600px;
}
th {
	border: thin solid black;
	font-weight: bold;
}
td {
	border: thin solid black;
}
</style>
</head>
<body>
<h1>protect_against_xss_stored_1.php</h1>
<h2>To Notice:</h2>
<ul>
<li>First time: <i>Undefined Index</i> notices</li>
<li>All fields are subject to stored XSS</li>
</ul>

<br />Try entering the following values:
<table>
<tr><th>Field</th><th>Value</th></tr>
<tr><td><i>id</i><td><pre>&lt;script>alert('XSS ATTACK');&lt;/script></pre></td></tr>
<tr><td><i>name</i></td><td><pre>%26%23x3C%3B%26%23x73%3B%26%23x63%3B%26%23x72%3B%26%23x69%3B%26%23x70%3B%26%23x74%3B%26%23x3E%3B%26%23x61%3B%26%23x6C%3B%26%23x65%3B%26%23x72%3B%26%23x74%3B%26%23x28%3B%26%23x27%3B%26%23x58%3B%26%23x53%3B%26%23x53%3B%26%23x20%3B%26%23x41%3B%26%23x54%3B%26%23x54%3B%26%23x41%3B%26%23x43%3B%26%23x4B%3B%26%23x27%3B%26%23x29%3B%26%23x3B%3B%26%23x3C%3B%26%23x2F%3B%26%23x73%3B%26%23x63%3B%26%23x72%3B%26%23x69%3B%26%23x70%3B%26%23x74%3B%26%23x3E%3B</pre>NOTE: urldecode() shows the value as <?php echo urldecode('%26%23x3C%3B%26%23x73%3B%26%23x63%3B%26%23x72%3B%26%23x69%3B%26%23x70%3B%26%23x74%3B%26%23x3E%3B%26%23x61%3B%26%23x6C%3B%26%23x65%3B%26%23x72%3B%26%23x74%3B%26%23x28%3B%26%23x27%3B%26%23x58%3B%26%23x53%3B%26%23x53%3B%26%23x20%3B%26%23x41%3B%26%23x54%3B%26%23x54%3B%26%23x41%3B%26%23x43%3B%26%23x4B%3B%26%23x27%3B%26%23x29%3B%26%23x3B%3B%26%23x3C%3B%26%23x2F%3B%26%23x73%3B%26%23x63%3B%26%23x72%3B%26%23x69%3B%26%23x70%3B%26%23x74%3B%26%23x3E%3B'); ?></td></tr>
<tr><td><i>image</i></td><td><pre>http://localhost/verybadwebsite/xss.php</pre></td></tr>
</table>

<br />
<form>
<table>
<tr><th>ID</th><td><input type="text" name="id" /></td><td>Current Value: <?php echo $id; ?></td></tr>
<tr><th>Name</th><td><input type="text" name="name" /></td><td>Current Value: <?php echo $name; ?></td></tr>
<tr><th>Image</th><td><input type="text" name="image" /></td><td>Current Value: <img src="<?php echo $image; ?>" /></td></tr>
</table>
<input type="submit" />
</form>
</body>
</html>