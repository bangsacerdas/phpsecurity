<?php
// FILE: protect_database_prepared_pdo_insert.php
// Prepared statements are the single most effective means of preventing SQL injection attacks
// NOTE: Although you should *always* filter and validate all incoming data ...
// 		 in this example we are deliberately *not* doing this

ini_set('display_errors', 1);
date_default_timezone_set('Europe/London');
include_once 'Init.php';
$pdo 	= new PDO(DB_DSN, DB_USER, DB_PWD);
$output = '';

$data = array(
	'page' 		=> 'PDO',
	'position' 	=> 'left',
	'title'		=> 'PDO Test ',
	'text'		=> 'This is a test of a PDO prepared statement insert: ' . date('Y-m-d H:i:s')
);

// SQL INSERT statement with placeholders
$sql1 = "INSERT INTO `contents` (`page`, `position`, `title`, `text`) "
	  . "VALUES (:page, :position, :title, :text)";
// send the statement to the database server
$stmt1 = $pdo->prepare($sql1);
// execute
$stmt1->execute($data);

// SQL SELECT statement
$sql2 = "SELECT * FROM `contents`";
// send the statement to the database server
$stmt2 = $pdo->prepare($sql2);
// execute
$stmt2->execute();
while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
	$output .= '<tr><td>';
	$output .= implode('</td><td>', $row);
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
<h1>protect_database_prepared_pdo_insert.php</h1>
<hr />

<br />
<h3>Insert Results</h3>
<hr />
<table>
<?php echo $output; ?>
</table>

</body>
</html>
