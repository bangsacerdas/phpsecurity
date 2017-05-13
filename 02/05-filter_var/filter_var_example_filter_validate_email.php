<?php
// using filter_var() with FILTER_VALIDATE_EMAIL

/*
 * NOTES:
 * -- does not try to validate top level domain
 */
$testData = array(
	// valid email addresses
	'doug@unlikelysource.com',
	'julia.roberts@fans.paramount.com',
	'DouglasAdams@hitchhikersguide.co.uk',
	'richard.gere@his-own-domain.co.au',
	// invalid
	'joe',											// missing domain
	'george@of-the@jungle.com',						// too many "@"
	'somebody@bad.dns.DomainThatDoesNotExist',		// last part doesn't exist (but valid according to new rules)
	'too-many-dots@bad.dns.com.',					// trailing '.'
	'missing_domain@bad..com',						// missing domain
	'too-many-dots@invalid_dns_domain.com',			// '_' not allowed in domain name
);

// build HTML table
$output = '<table border=1>' . PHP_EOL;
$output .= '<tr><th>Number</th><th>Valid</th></tr>' . PHP_EOL;

// validate data
foreach($testData as $email) {

	// escape raw data!
	$output .= '<tr><td>' . htmlspecialchars($email) . '</td>';

	// use str_replace() to get rid of () then match against pattern
	$output .= (filter_var($email, FILTER_VALIDATE_EMAIL))
		 ? '<td><b style="color:green;">VALID</b></td>'
		 : '<td><b style="color:red;">INVALID</b></td>';

	$output .= '</tr>' . PHP_EOL;

}

$output .= '</table>' . PHP_EOL;
echo $output;
