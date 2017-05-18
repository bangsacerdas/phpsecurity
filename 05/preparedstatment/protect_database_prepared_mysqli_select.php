<?php
// FILE: protect_database_prepared_mysqli_select.php
// Prepared statements are the single most effective means of preventing SQL injection attacks
// NOTE: Although you should *always* filter and validate all incoming data ...
// 		 in this example we are deliberately *not* doing this

ini_set('display_errors', 1);
date_default_timezone_set('Europe/London');
include_once 'Init.php';
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PWD, DB_NAME);
$output = '';

if (isset($_POST['search']) && isset($_POST['address'])) {
	// SQL statement with placeholders
	$sql = "SELECT * FROM `prospects` WHERE `address` LIKE ? LIMIT 25";
	// send the statement to the database server
	$stmt = $mysqli->prepare($sql);
	// bind value; 's' = string ('i' = int, 'd' = double, 'b' = blob)
	$value = '%' . $_POST['address'] . '%';
	$stmt->bind_param('s', $value);
	// execute
	$stmt->execute();
	// bind result
	$stmt->bind_result($id, $first_name, $last_name, $address, $city, $state, $zip, $phone);
	while ($stmt->fetch()) {
		$output .= '<tr><td>';
		$output .= implode('</td><td>', array($id, $first_name, $last_name, $address, $city, $state, $zip, $phone));
		$output .= '</td></tr>' . PHP_EOL;
	}
}

?>
<!DOCTYPE html>
<html>
<head>
<style>
td { border: thin solid black; }
th { border: thin solid black; font-weight: bold; }
</style>
<meta charset="UTF-8">
<title>Protect Database</title>
</head>
<body>
<h1>protect_database_prepared_mysqli_select.php</h1>
<hr />
To Do:
<ul>
<li>Enter "street" and then "drive"</li>
<li>Note the results</li>
<li>Enter <pre>street%' UNION SELECT `password`,`dob`,`balance`,`phone`,`name` FROM `members`;--</pre></li>
<li>Notice that SQL injection attacks are unsuccessful</li>
</ul>

Enter Search Criteria:
<br />
<form method="post">
<table>
<tr><th>Address</th><td><input type="text" name="address" /></td></tr>
<tr><th>&nbsp;</th><td><input type="submit" name="search" value="Search" /></td></tr>
</table>
</form>

<br />
<h3>Search Results</h3>
<hr />
<table>
<?php echo $output; ?>
</table>

</body>
</html>
