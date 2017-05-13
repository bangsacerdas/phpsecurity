<?php
// using filter_var() with FILTER_VALIDATE_IP

/*
 * NOTES:
 * -- does not catch leading "0" in IPv4 address
 * -- use extra flags if you want to check for private or reserved IPv4 address blocks
 */
$testData = array(
	// valid IPv4 addresses
	'88.92.166.14',
	'22.198.14.88',
	'127.0.0.1',
	// valid IPv4 addresses (private)
	'192.168.1.1',
	'10.0.0.1',
	// valid IPv6 addresses
	'2001:0db8:0000:0000:0000:ff00:0042:8329',
	'2001:db8:0:0:0:ff00:42:8329',
	'2001:db8::ff00:42:8329',
	'::1',
	// invalid IPv4 addresses
	'256.192.168.1',	// 255 is max value
	'0.128.1.1',		// cannot have leading 0
	'10.19.22.44.8'		// only 4 sections allowed
);

// build HTML table
$output = '<table border=1>' . PHP_EOL;
$output .= '<tr><th>Number</th><th>Valid IP v4</th><th>IPv4 No Private</th><th>Valid IP v6</th></tr>' . PHP_EOL;

// validate data
foreach($testData as $ipAddress) {

	// escape raw data!
	$output .= '<tr><td>' . htmlspecialchars($ipAddress) . '</td>';

	// IPv4 validation
	$output .= (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		 ? '<td><b style="color:green;">VALID</b></td>'
		 : '<td><b style="color:red;">INVALID</b></td>';

	// IPv4 validation -- no private address blocks
	$output .= (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE))
		 ? '<td><b style="color:green;">VALID</b></td>'
		 : '<td><b style="color:red;">INVALID</b></td>';

	// IPv6 validation
	$output .= (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
		 ? '<td><b style="color:green;">VALID</b></td>'
		 : '<td><b style="color:red;">INVALID</b></td>';

	$output .= '</tr>' . PHP_EOL;

}

$output .= '</table>' . PHP_EOL;
echo $output;
