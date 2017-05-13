<?php
// you can use "strip_tags()" to remove unwanted HTML or javascript tags
$rawData = 'Data with bad <img src="http://localhost/verybadwebsite/bad.php" /> '
 	  	 . 'tags <script>alert("YOU HAVE BEEN HACKED")</script>';

// after filtering
$cleanData = strip_tags($rawData);
var_dump($cleanData);

