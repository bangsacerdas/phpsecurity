<?php
$message = '';
if (isset($_FILES['request'])) {

	// *** file uploads: Check to make sure file uploaded by upload process

	// *** file uploads: sanitize filename
	$fn = $_FILES['request']['name'];

	// *** do not accept PHP files!!!
	
	// *** Build new path + filename with safety measures in place
	$copyfile = realpath(__DIR__ . '/../uploads') . '/' . $fn;

	// Copy file
	if (move_uploaded_file($_FILES['request']['tmp_name'], $copyfile) ) {
		$message .= "<b style='color: green;'>Successfully uploaded file $copyfile\n";
	} else {
		// Trap upload file handle errors
		$message .= "<b style='color: red;'>Unable to upload file " . $_FILES['request']['name'];
	}
}
?>
<div class="content">
	<br/>
	<div class="product-list">

		<h2>Sign Up</h2>
		<br/>

		<b>Please use this form to contact us.</b><br/><br/>
		<form name="contact" method="post" enctype="multipart/form-data">
			<p>
				<label>Name: </label>
				<input type="text" name="name"/>
			<p>
			<p>
				<label>Email Address: </label>
				<input type="text" name="email"/>
			<p>
			<p>
				<label>Comments / Questions: </label>
				<textarea name="comments">I love your products!</textarea>
			<p>
			<p>
				<label>Special Order: </label>
				<input type="file" name="request" />
			<p>
			<p>
				<input type="reset" name="clear" value="Clear" class="button"/>
				<input type="submit" name="submit" value="Submit" class="button marL10"/>
			<p>
		</form>
		<p>
		<?php echo $message; ?>
	</div><!-- product-list -->

<br class="clear-all"/>
</div><!-- content -->

</div><!-- maincontent -->
