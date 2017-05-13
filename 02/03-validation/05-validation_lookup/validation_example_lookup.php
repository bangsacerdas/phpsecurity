<?php
// this demonstrates using a database lookup for validation

/*
 * NOTES:
 * -- Either build an array or do the lookup using SELECT xxx WHERE xxx
 * -- Often associated with HTML SELECT tags
 * -- Filter user supplied input before testing against lookup values
 * -- DO NOT USE USER INPUT DIRECTLY!!!
 * -- Why do this if you have a dropdown list?  Response can be forged!
 */

// set up database connection and get PDO database connection
require 'init_db.php';
$initDb = new InitDb();
$pdo = $initDb->getPdo();

// build a list of country codes + HTML SELECT tag
$pdoList = $pdo->query('SELECT * FROM `iso_country_codes`');
$countryCodes = array();
$countryHtml = '<select name="country">' . PHP_EOL;
while ($row = $pdoList->fetch(PDO::FETCH_ASSOC)) {
	$countryCodes[$row['iso2']] = $row;
	$countryHtml .= '<option value="' . $row['iso2'] . '">' . $row['name'] . '</option>' . PHP_EOL;
}
$countryHtml .= '</select>' . PHP_EOL;

// verify user input against array
$message = '';
if (isset($_GET['submit']) && isset($_GET['country'])) {
	// process input
	$code = strtoupper($_GET['country']);
	// do not use user input directly!
	if (isset($countryCodes[$code])) {
		$message = $countryCodes[$code]['name'];
	} else {
		$message = '<b style="color:red;">Invalid Country Code</b>';
	}
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Validation Lookup</title>
</head>
<body>
<form method="get">
Enter Country: <?php echo $countryHtml; ?>
<br /><input type="submit" name="submit" value="Submit" />
</form>
<br /><?php echo $message; ?>
</body>
</html>