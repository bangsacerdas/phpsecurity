<?php
// FILE: protect_database_prepared_mysqli_insert.php
// Prepared statements are the single most effective means of preventing SQL injection attacks
// NOTE: Although you should *always* filter and validate all incoming data ...
// 		 in this example we are deliberately *not* doing this

ini_set('display_errors', 1);
date_default_timezone_set('Europe/London');
include_once 'Init.php';
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PWD, DB_NAME);
$output = '';

$data = array(
	'page' 		=> 'Mysqli',
	'position' 	=> 'left',
	'title'		=> 'Mysqli Test ',
	'text'		=> 'This is a test of a mysqli prepared statement insert: ' . date('Y-m-d H:i:s')
);

// SQL INSERT statement with placeholders
$sql1 = "INSERT INTO `contents` (`page`, `position`, `title`, `text`) VALUES (?, ?, ?, ?)";
// send the statement to the database server
$stmt1 = $mysqli->prepare($sql1);
// bind value; 's' = string ('i' = int, 'd' = double, 'b' = blob)
$stmt1->bind_param('ssss', $data['page'], $data['position'], $data['title'], $data['text']);
// execute
$stmt1->execute();

// SQL SELECT statement
$sql2 = "SELECT * FROM `contents`";
// send the statement to the database server
$stmt2 = $mysqli->prepare($sql2);
// execute
$stmt2->execute();
// bind result
$stmt2->bind_result($id, $page, $position, $title, $text);
while ($stmt2->fetch()) {
	$output .= '<tr><td>';
	$output .= implode('</td><td>', array($id, $page, $position, $title, $text));
	$output .= '</td></tr>' . PHP_EOL;
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
<h1>protect_database_prepared_mysqli_insert.php</h1>
<hr />

<br />
<h3>Insert Results</h3>
<hr />
<table>
<?php echo $output; ?>
</table>

</body>
</html>
