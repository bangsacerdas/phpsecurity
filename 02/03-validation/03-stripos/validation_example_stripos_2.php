<?php
// using stripos() for validation: check for the existance of SQL injection code

// set up database connection and get PDO database connection
require 'init_db.php';
$initDb = new InitDb();
$pdo = $initDb->getPdo();

// "normal" search criterion
//$searchTerm = 'A';

// SQL injection
$searchTerm = "A%' UNION SELECT `password`,`dob`,`balance`,`phone`,`name` FROM `members`;--";

// validate the search term using stripos()
if (stripos($searchTerm, 'UNION ') === FALSE) {
	$output = '<table border=1>' . PHP_EOL;
	$search = "'%" . $searchTerm. "%'";
	$sql = 'SELECT `user_id`,`photo`,`name`,`city`,`email` FROM `members` WHERE '
	     . '`name` LIKE ' . $search . ' OR '
	     . '`city` LIKE ' . $search . ' OR '
	     . '`email` LIKE ' . $search . ' ORDER BY `name`';
	$stmt = $pdo->query($sql);
	while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
		$output .= '<tr><td>' . implode('</td><td>', $row) . '</td></tr>' . PHP_EOL;
	}
	$output .= '</table>' . PHP_EOL;
} else {
	$output = '<b style="color:red;">Please retry the search!</b>';
}
echo $output;
