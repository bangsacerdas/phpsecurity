<?php
// the ctype_* family of functions can be used to validate incoming data
// This examples uses ctype_alpha(), ctype_alnum() and ctype_digit()

/*
 * NOTES:
 * -- ctype = character type
 * -- very fast (especially compared with preg_match())
 * -- does not alter the original data
 * -- doesn't handle foreign characters very well
 * -- each field needs its own validation "rules"
 * -- might want to use with str_replace() to get rid of spaces, periods, commas prior to validation
 * -- don't forget to escape unfiltered data
 */

function validData() 				{ return '<br /><b style="color:green;">VALID</b>'; }
function inValidData($msg = NULL) 	{ return '<br /><b style="color:red;">INVALID: ' . $msg . '</b>'; }

$output = '<table border=1>';
$testData = new SplFileObject('test_address_data.csv', 'r');
// retrieve labels
$labels = $testData->fgetcsv();
$output .= '<tr><th>' . implode('</th><th>', $labels) . '</th></tr>' . PHP_EOL;
// loop through data
$messages = array();
while ($row = $testData->fgetcsv()) {
	// capture original data; make sure raw data is escaped
	if (!(isset($row[1]) && is_array($row) && count($row) == 7)) { continue; }
	$output .= '<tr>';
	foreach ($row as $item) {
		// make sure raw data is escaped
		$output .= '<td>' . htmlspecialchars($item) . '</td>';
	}
	$output .= '</tr><tr>';
		// name validation: need to 1st get rid of spaces and '.' before validating
		$output .= '<td>';
		$output .= (ctype_alpha(str_replace(array(' ','.'), '', $row[0])))
					? validData()
					: inValidData('Can only include letters, periods and spaces');
		$output .= '</td><td>';
		// address1 validation: need to 1st get rid of spaces before validating
		$output .= (ctype_alnum(str_replace(' ', '', $row[1])))
					? validData()
					: inValidData('Can only include letters, numbers and spaces');
		$output .= '</td><td>';
		// address2 validation: need to 1st get rid of spaces before validating
		$output .= (ctype_alnum(str_replace(' ', '', $row[2])))
					? validData()
					: inValidData('Can only include letters, numbers and spaces');
		$output .= '</td><td>';
		// postcode validation: need to 1st get rid of spaces and '-' before validating
		$output .= (ctype_alnum(str_replace(array(' ','-'), '', $row[3])))
					? validData()
					: inValidData('Can only include letters, numbers, spaces or dashes');
		$output .= '</td><td>';
		// phone validation: just numbers
		$output .= (ctype_digit($row[4]))
					? validData()
					: inValidData('Can only include numbers');
		$output .= '</td><td>';
		// country validation: note the addition of strlen() to check for 2 characters
		$output .= (ctype_alpha($row[5]) && strlen($row[5]) == 2)
					? validData()
					: inValidData('Must be 2 letters');
		$output .= '</td><td>&nbsp;</td>';

	$output .= '</tr>' . PHP_EOL;
}
$output .= '</table>' . PHP_EOL;
?>
<!DOCTYPE HTML>
<html>
<head>
<meta content="text/html; charset=UTF-8" http-equiv="content-type">
</head>
<body>
<?php echo $output; ?>
</body>
</html>
