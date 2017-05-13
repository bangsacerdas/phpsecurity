<?php
// PROBLEM: unable to create consistent JOIN between `prospects` and `us_states`
//			as the values for `prospects.state` are not "clean"

// set up database connection and get PDO database connection
require 'init_db.php';
$initDb = new InitDb();
$pdo = $initDb->getPdo();

// prepare state code lookup
$stateLookupSql = 'SELECT * FROM `us_states` WHERE `state_name` = :state LIMIT 1';
$stateStmt = $pdo->prepare($stateLookupSql);

// Use str_replace() to cleanup `state` field
$output = '<table border=1>' . PHP_EOL;
$stmt = $pdo->query('SELECT * FROM `prospects`');

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

	// use str_replace() to get rid of the '.' in the `state` field
	$state = strtoupper(trim(str_replace('.', '', $row['state'])));

	// do a name lookup from the `us_states` table and get the state code
	// -- the `state` field can then be updated with the "clean" state code (not shown)
	if (strlen($state) != 2) {
		$stateStmt->execute(array(':state' => $state));
		$lookup = $stateStmt->fetch(PDO::FETCH_ASSOC);
		$state = ($lookup) ? $lookup['state_code'] : '??';
	}

	$row['state'] = $state;
	$output .= '<tr><td>';
	$output .= implode('</td><td>', $row);
	$output .= '</td></tr>' . PHP_EOL;

}

$output .= '</table>' . PHP_EOL;
echo $output;
