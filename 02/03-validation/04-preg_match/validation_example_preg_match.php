<?php
// use preg_match() when data to be validated can appear in many different forms
// Example: phone numbers: [+country code] [area code] [city code] [personal code]

/*
 * NOTES:
 * -- preg_match() lets you define a pattern which represents the formats you would consider valid
 * -- don't forget to "normalize" your data once validated!
 */
$testData = array(
	// valid numbers
	'+1 123-456-7890',
	'(123) 456-7890',
	'123-456-7890',
	'123.456.7890',
	'01234 567 890',
	'+44 1234567890',
	// invalid
	'123-456-78900',		// too many digits in personal code
	'+1234 123-456-7890',	// too many digits in country code
	'[123] 45-6-7890',  	// only numbers, space, hyphen, parentheses allowed
	'123/456/7890',  		// only numbers, space, hyphen, parentheses allowed
	'01234 567 890.',		// doesn't end with a digit
	'(123) 45-6-789O',  	// letter "O" instead of number "0"
	'+44 01234567890',		// no leading 0 after country code
);

// + and 1 to 3 digits and space | 0 and 1-9 and two to three 0-9 | 1-9 and two to three 0-9
// then: optional separator of space | . | -
// then: 1 or 2 groups of 3 digits and an optional separator
// ending with: a group of 4 digits
$pattern = '/^((\+\d{1,3} )|0[1-9]\d{2,3}|[1-9]\d{2,3})[ .-]?((\d{3}[ .-]?)){1,2}\d{3,4}$/';

// build HTML table
$output = '<table border=1>' . PHP_EOL;
$output .= '<tr><th>Number</th><th>Valid</th></tr>' . PHP_EOL;

// validate data
foreach($testData as $number) {

	// escape raw data!
	$output .= '<tr><td>' . htmlspecialchars($number) . '</td>';

	// use str_replace() to get rid of () then match against pattern
	$output .= (preg_match($pattern, str_replace(array('(',')'), '', $number)))
		 ? '<td><b style="color:green;">VALID</b></td>'
		 : '<td><b style="color:red;">INVALID</b></td>';

	$output .= '</tr>' . PHP_EOL;

}

$output .= '</table>' . PHP_EOL;
echo $output;
