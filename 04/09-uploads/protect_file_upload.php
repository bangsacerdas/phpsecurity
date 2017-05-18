<?php

// Initialize Variables
$message = "";
$safeDir = __DIR__ . DIRECTORY_SEPARATOR . 'img_uploads' . DIRECTORY_SEPARATOR;

// Check to see if OK button was pressed
if (isset($_POST['OK'])) {

	// Check to see if upload parameter specified
	if ($_FILES['file_to_upload']['error'] == UPLOAD_ERR_OK ) {

		// Check to make sure file uploaded by upload process
		if ( is_uploaded_file ($_FILES['file_to_upload']['tmp_name'] ) ) {
			
			// Capture filename and strip out any directory path info
			$fn = basename($_FILES['file_to_upload']['name']);

			// Build new filename with safety measures in place
			$copyfile = $safeDir . 'safe_prefix_' . strip_tags($fn);
		
			// Copy file to safe directory
			if ( move_uploaded_file ($_FILES['file_to_upload']['tmp_name'], $copyfile) ) {
				$message .= "<br>Successfully uploaded file $copyfile\n";
			} else {
				// Trap upload file handle errors
				$message .= "<br>Unable to upload file " . $_FILES['file_to_upload']['name'];
			}
			
		} else {
			// Failed security check
			$message .= "<br>File Not Uploaded!";
		}
		
	} else {
		// No upload file
		$message .= "<br>No Upload File Specified\n";
	}
}	

// Scan directory
$list = glob($safeDir . "*");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Protect File Upload</title>
<style>
TD {
	font: 10pt helvetica, sans-serif;
	border: thin solid black;
	}
TH {
	font: bold 10pt helvetica, sans-serif;
	border: thin solid black;
	}
</style>
</head>
<body>
<h1>protect_file_upload.php</h1>
<form name="upload" method="POST" enctype="multipart/form-data">
<!-- NOTE: you could also use the HTML5 "accept=xxx" attribute -->
<input type="file" size=50 maxlength=255 name="file_to_upload" value="" />
<br />
<input type="submit" name="OK" value="OK" />
</form>
<table cellspacing=4>
<tr><th>Filename</th><th>Last Modified</th><th>Size</th></tr>
<?php
if (isset($list)) {
	foreach ($list as $item) {
		echo "<tr><td>$item</td>";
		echo "<td>" . date("F d Y H:i:s", filemtime($item)) . "</td>";
		echo "<td align=right>" . filesize($item) . "</td>";
		echo "</tr>\n";
	}
}
echo "</table><br />\n";
phpinfo(INFO_VARIABLES);
?>
</body>
</html>
