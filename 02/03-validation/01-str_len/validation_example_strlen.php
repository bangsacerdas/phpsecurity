<?php
// test array of city names, simulates user input
$cityTest = array('This is a very long city name which does not follow the rule where the length of the city name must be less than one hundred twenty eight characters',
			  	  'London',  
			  	  '', 
			  	  'San Francisco');

// here are the "rules" for minimum and maximum lengths for city names
$minLenCityName = 1;
$maxLenCityName = 128;

// strlen() is used to validate the length of the city field
echo '<ul>' . PHP_EOL;
foreach ($cityTest as $city) {
	if (strlen($city) > $maxLenCityName) {
		echo '<li><b style="color: red;">INVALID</b>: City Name Too Long! [' . substr($city, 0, 20) . ']' . PHP_EOL;
		echo '<br /><i>Must be no more than ' . $maxLenCityName . ' letters in length!</i></li>' . PHP_EOL;
	} elseif (strlen($city) < $minLenCityName) {
		echo '<li><b style="color: red;">INVALID</b>: City Name Too Short! [' . substr($city, 0, 20) . ']' . PHP_EOL;
		echo '<br /><i>Must be at at least ' . $minLenCityName . ' letter(s) in length!</i></li>' . PHP_EOL;
	} else {
		echo '<li><b style="color: green;">VALID</b> [' . $city . ']</li>' . PHP_EOL;
	}
}
echo '</ul>' . PHP_EOL;
