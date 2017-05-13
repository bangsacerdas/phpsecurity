<?php
// using filter_var() to "sanitize" data
// Example: phone numbers: [+country code] [area code] [city code] [personal code]
// 			produces a value with an optional leading "+" followed only by digits
/*
 * NOTES:
 * -- can't use (int) in this case as most of the phone number would be removed!
 * -- in this example uses str_replace() to get rid of '-'
 */
$testData = array(
	// validated numbers
	'+1 123-456-7890',
	'(123) 456-7890',
	'123-456-7890',
	'123.456.7890',
	'01234 567 890',
	'+44 1234567890',
);

$pattern = '/(+\d{1,3} )?([1-9]\d{2})(\()?([ .-)])?(\d{3})([ .-])?(\d{4})([ .-])?/';

$output = '<table border=1>' . PHP_EOL;
$output .= '<tr><th>Number</th><th>Pass 1</th><th>Pass 2</th></tr>' . PHP_EOL;
foreach($testData as $number) {
	// use filter_var() to strip out any non-numeric values
	$pass1 = filter_var($number, FILTER_SANITIZE_NUMBER_INT);
	// use str_replace() to get rid of '-'
	$pass2 = str_replace('-', '', $pass1);
	$output .= '<tr><td>' . htmlspecialchars($number) . '</td>';
	$output .= '<td>' . $pass1 . '</td>';
	$output .= '<td>' . $pass2 . '</td></tr>' . PHP_EOL;
}
$output .= '</table>' . PHP_EOL;
echo $output;