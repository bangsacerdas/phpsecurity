<?php
// filtering example showing strip_tags() with and without whitelisting

/*
 * NOTES:
 * -- strip_tags() filters by removing any tags (i.e. <TAG xxx>)
 * -- the second parameter is a string of tags you wish to retain
 * -- this function is extremely useful in protecting your website from injection attacks
 */

$testData = array(	'Test string with no tags',
					'Test <b>string</b> with <i>harmless</i> tags',
					'Test <b>string</b> with bogus image <img src="http://verybadwebsite/badcode.php" />',
					'Test string with javascript <script>alert("XSS Attack");</script>');

$output = '<table border=1>';
$output .= '<tr><th>Escaped Original</th><th>No Whitelisting</th><th>Whitelisting</th></tr>' . PHP_EOL;

foreach ($testData as $item) {
	$output .= '<tr>'
			 . '<td>' . htmlspecialchars($item) 	. '</td>'
			 . '<td>' . strip_tags($item) 			. '</td>'
			 . '<td>' . strip_tags($item,'<b><i>')	. '</td>'
			 . '</tr>' . PHP_EOL;
}

echo $output;
echo '</table>' . PHP_EOL;