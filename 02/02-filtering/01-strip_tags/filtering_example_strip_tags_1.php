<?php
// you can use "strip_tags()" to remove unwanted HTML or javascript tags

$rawData = 'Data with bad <img src="http://localhost/verybadwebsite/bad.php" /> '
 	  	 . 'tags <script>alert("YOU HAVE BEEN HACKED")</script>';

// unfiltered
//var_dump($rawData);

// filtered
//var_dump(strip_tags($rawData));
