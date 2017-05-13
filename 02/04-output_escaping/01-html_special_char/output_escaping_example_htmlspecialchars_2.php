<?php
// output escaping example showing htmlspecialchars()
$testData = array(	'Test string with no tags',
					'The price of a an all-day bus pass is £4.10 or approximately $6.00',
					'Hur mår du idag min vän?',			// how are you today my friend (Swedish)
					'Test <b>string</b> with <i>harmless</i> tags',
					'Test <b>string</b> with bogus image <img src="http://verybadwebsite/badcode.php" />',
					"Test string with javascript <script>alert('XSS Attack');</script>");

$output = '<h1>View Page Source to See Results</h1>' . PHP_EOL;
$output = '<ul>' . PHP_EOL;

foreach ($testData as $item) {
	$output .= '<li>' . htmlspecialchars($item, ENT_QUOTES, "UTF-8") . '</li>' . PHP_EOL;
}

echo $output;
echo '</ul>' . PHP_EOL;
