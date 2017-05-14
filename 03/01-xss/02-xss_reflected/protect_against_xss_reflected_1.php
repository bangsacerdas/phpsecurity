<?php
// protecting against reflected XSS attacks

// All inputs should be tested if they are set,
// and then properly filtered and validated
$id 	= $_GET['id'];
$name 	= $_GET['name'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Protect Against Reflected XSS</title>
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
<h1>protect_against_xss_reflected_1.php</h1>
<h2>To Notice:</h2>
<ul>
<li>First time: <i>Undefined Index</i> notices</li>
<li>All fields are subject to reflected XSS</li>
</ul>

<br />Try entering the following values:
<pre>&lt;script>alert('XSS ATTACK');&lt;/script></pre>
<pre>try&quot;to'break the quotes</pre>

<br />
<form>
<table>
<tr>
	<th>ID</th>
	<td><input type="text" name="id" value="<?php echo $id; ?>" /></td>
	<td>Value: <?php echo $id; ?></td>
</tr>
<tr>
	<th>Name</th>
	<td><input type="text" name="name" value="<?php echo $name; ?>" /></td>
	<td>Value: <?php echo $name; ?></td>
</tr>
</table>
<input type="submit" />
</form>
<b>NOTE:</b> some browsers will protect against reflected XSS inside INPUT tags
</body>
</html>