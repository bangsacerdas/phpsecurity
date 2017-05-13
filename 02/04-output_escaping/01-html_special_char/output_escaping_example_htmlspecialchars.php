<?php
// here is some test data with a benign XSS attack example
$rawData = '<script>alert("XSS Attack");</script>';

$output = '';
if (isset($_POST['raw'])) {
	$output .= '<h1>Raw Data</h1>';
	$output .= '<br />';
	$output .= $rawData;
}

if (isset($_POST['escaped'])) {
	$output .= '<h1>Escaped Data</h1>';
	$output .= '<br />';
	$output .= htmlspecialchars($rawData);
}
?>
<!DOCTYPE HTML>
<head>
<title>Output Escape Test</title>
<meta charset="utf-8">
</head>
<body>
<?php echo $output; ?>
<form method="post">
<input type="submit" name="raw" value="Raw" />
<input type="submit" name="escaped" value="Escaped" />
</form>
</body>
