<?php
// filtering example showing the use of (int) and (float) to perform filtering

/*
 * NOTES:
 * -- filtering is used to both safeguard your website as well as to "sanitize" data
 * -- changing the data type is a quick and easy way to filter
 * -- any non-numeric data is simply ignored (and is removed from the final value)
 * -- only applies to numeric data such as prices and numeric IDs
 */

$testData = array(	12345,
					12345.678,
					'Non Numeric',
					'<script>alert("XSS Attack");</script>');

$output = '<table border=1>';
$output .= '<tr><th>Escaped Original</th><th>Int</th><th>Float</th></tr>' . PHP_EOL;

foreach ($testData as $item) {
	$output .= '<tr>'
			 . '<td>' . htmlspecialchars($item) . '</td>'
			 . '<td>' . (int) $item 			. '</td>'
			 . '<td>' . (float) $item			. '</td>'
			 . '</tr>' . PHP_EOL;
}

echo $output;
echo '</table>' . PHP_EOL;