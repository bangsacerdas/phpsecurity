<?php
// str_replace() searches for a string and replaces with another string

// PROBLEM: unable to create consistent JOIN between `prospects` and `us_states`
//			as the values for `prospects.state` are not "clean"
// SOLUTION: str_replace() will be used to "sanitize" the `state` field to allow a proper JOIN to take place
// 			 this is a security consideration in that without this process,
//			 an Information Disclosure vulnerability is possible
//			 this is also useful in promoting better database efficiency

// set up database connection and get PDO database connection
require 'init_db.php';
$initDb = new InitDb();
$pdo = $initDb->getPdo();

// Display current contents of `prospects`; notice values in `state` field
$output = '<table border=1>' . PHP_EOL;
$stmt = $pdo->query('SELECT * FROM `prospects`');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$output .= '<tr><td>';
	$output .= implode('</td><td>', $row);
	$output .= '</td></tr>' . PHP_EOL;
}
$output .= '</table>' . PHP_EOL;
echo $output;
