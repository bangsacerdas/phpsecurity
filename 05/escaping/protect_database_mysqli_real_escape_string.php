<?php
// FILE: protect_database_mysqli_real_escape_string.php
// Use "mysqli_real_escape_string" to safely quote or escape any of the following:
// NUL (ASCII 0), \n, \r, \, ', ", and Control-Z.

include_once 'Init.php';
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PWD, DB_NAME);

// this setting is required for mysqli::real_escape_string() to work
$mysqli->set_charset('utf8');

$test = array(
	NULL,
	chr(0),
	"TEST \\ 'Single Quoted' \r\n",
	'TEST \\ "Double Quoted"',
	'This is a normal string',
);

$outputMysqli = '';
$outputMysqli .= '<table border="1">';
$outputMysqli .= '<tr><th>Unescaped</th><th>Escaped</th></tr>'. PHP_EOL;

foreach ($test as $item) {
	$outputMysqli .= '<tr>';
	$outputMysqli .= '<td><pre>' . var_export($item, TRUE) . '</pre></td>';
	$outputMysqli .= '<td><pre>' . var_export($mysqli->real_escape_string($item), TRUE) . '</pre></td>';
	$outputMysqli .= '</tr>' . PHP_EOL;
}
$outputMysqli .= '</table>' . PHP_EOL;
echo  $outputMysqli;
$mysqli = NULL;
