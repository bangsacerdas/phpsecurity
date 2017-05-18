<?php
// FILE: protect_database_pdo_quote.php
// Use "mysqli_real_escape_string" to safely quote or escape any of the following:
// NUL (ASCII 0), \n, \r, \, ', ", and Control-Z.

include_once 'Init.php';
$pdo = new PDO(DB_DSN, DB_USER, DB_PWD);

$test = array(
	NULL,
	chr(0),
	"TEST \\ 'Single Quoted' \r\n",
	'TEST \\ "Double Quoted"',
	'This is a normal string',
);

$outputPdo = '';
$outputPdo .= '<table border="1">';
$outputPdo .= '<tr><th>Unescaped</th><th>Escaped</th></tr>'. PHP_EOL;

foreach ($test as $item) {
	$outputPdo .= '<tr>';
	$outputPdo .= '<td><pre>' . var_export($item, TRUE) . '</pre></td>';
	$outputPdo .= '<td><pre>' . var_export($pdo->quote($item), TRUE) . '</pre></td>';
	$outputPdo .= '</tr>' . PHP_EOL;
}
$outputPdo .= '</table>' . PHP_EOL;
echo $outputPdo;
$pdo = NULL;