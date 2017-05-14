<?php
$header = 'ADD';
$content = <<<EOT
<form enctype="multipart/form-data" method="POST">
<br /><input type="text" name="name" placeholder="Enter Name" />
<br /><input type="file" name="upload" placeholder="File Upload" />
<br /><input type="submit" name="submit" value="Submit" />
</form>
EOT;
// unrestricted file uploads (covered in another video)
// normally you would perform safety checks and filter file types
if (isset($_FILES['upload'])) {
	$fn = $_FILES['upload']['name'];
	move_uploaded_file($_FILES['upload']['tmp_name'], __DIR__ . '/' . $fn);
}