<?php
// protecting against reflected XSS attacks
// the primary protection comes from output escaping

// prevent information disclosure: initialize variables
$id 	= 0;
$name 	= '';

// All inputs should be filtered and validated as mentioned before
$id 	= (isset($_GET['id'])) ? (int) $_GET['id'] : 0;
// $name = defaults to "Guest"; strip_tags() removes HTML or javascript tags
$name 	= (isset($_GET['name'])) ? strip_tags($_GET['name']) : 'Guest';

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Protect Against Reflected XSS</title>
</head>
<body>
<h1>protect_against_xss_reflected_2.php</h1>
<h2>To Notice:</h2>
<ul>
<li>Notices are gone</li>
<li>Inputs are filtered</li>
</ul>

<br />Try entering the following values:
<pre>&lt;script>alert('XSS ATTACK');&lt;/script></pre>
<pre>try&quot;to'break the quotes</pre>

<br />
<form>
<table>
<tr>
	<th>ID</th>
	<td><input type="text" name="id" value="<?php echo htmlspecialchars($id); ?>" /></td>
	<td>Value: <?php echo htmlspecialchars($id); ?></td>
</tr>
<tr>
	<th>Name</th>
	<td><input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" /></td>
	<td>Value: <?php echo htmlspecialchars($name); ?></td>
</tr>
</table>
<input type="submit" />
</form>
</body>
</html>