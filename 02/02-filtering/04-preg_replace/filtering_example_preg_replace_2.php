<?php
// using preg_replace() to "repurpose" an HTML page, replacing potentially dangerous tags

/*
 * NOTES:
 * -- often you will need to obtain information from other websites
 * -- as this info originates from outside your own code, it cannot be trusted
 * -- preg_replace() is used to perform an intelligent search and replace
 * -- potentially dangerous tags can be replaced with something less harmful
 */

// retrieve web page to be repurposed
$contents = file_get_contents('http://en.wikipedia.org/wiki/Douglas_Adams');

// search and replace <script>xxxx</script> with '<!--'
// search and replace <a href="xxxx" xxx> with '[xxxx]'
$phase2 = preg_replace(	array(	'#<script#',
								'#</script>#',
								'#<a href="(.*?)"(.*?)>#'),
						array(	'<!-- ',
								' -->',
								'<span style="font-size: 8pt; font-style: italics;">[$1]</span>',),
						$contents);

echo $phase2;
