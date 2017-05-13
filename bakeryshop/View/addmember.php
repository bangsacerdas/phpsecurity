<?php
// assign defaults
date_default_timezone_set('Europe/London');
$mailStatus = '';
$data = array('email' 		=> 'email',
			  'firstname' 	=> 'firstname',
			  'lastname' 	=> 'lastname',
			  'address' 	=> 'address',
			  'postcode' 	=> 'postcode',
			  'city' 		=> 'city',
			  'stateProv' 	=> 'state or province',
			  // *** validation: implement database lookup for "country" field
			  // ***             use the "iso_country_codes" table
			  'country'		=> 'country',
			  'dob'			=> '', 	// can be built from next 3 fields
			  'dobyear'		=> 0,
			  'dobmonth'	=> 0,
			  'dobday'		=> 0,
			  'telephone' 	=> 'telephone',
			  'password' 	=> 'password',
			  'photo' 		=> 'URL of photo',
);
// *** this is not being used, but needs to be implemented as part of the validation scheme
$error = array('email' 	  => '',
			  'firstname' => '',
			  'lastname'  => '',
			  'address'	  => '',
			  'city'	  => '',
			  'stateProv' => '',
			  'country'	  => '',
			  'postcode'  => '',
			  'telephone' => '',
			  'dob'		  => '',
			  'password'  => '',
			  'photo'	  => '',
);
if (isset($_POST['data'])) {
	$data = $_POST['data'];
	// *** filtering: need to remove unwanted tags from incoming data
	if (isset($data['dobyear']) && isset($data['dobmonth']) && isset($data['dobday'])) {
		try {
			$bdateString = sprintf('%4d-%02d-%02d', $data['dobyear'], $data['dobmonth'], $data['dobday']);
			$bdate = new DateTime($bdateString);
			$today = new DateTime();
			$interval21 = new DateInterval('P21Y');
			$bdate21 = $today->sub($interval21);
			// *** validation: need to check to see if DOB is > 21 years old
			if ($bdate > $bdate21) {
				// *** validation: add message to $error['dob'];
			}
			$data['dob'] = $bdate->format('Y-m-d H:i:s');
		} catch (Exception $e) {
			// *** validation: need to add info to $error['dob']
			// *** security: consider logging the error message rather than displaying it
			echo $e->getMessage();
		}

	} else {
		// *** validation: need to add info to $error['dob']
	}
	// *** security: all incoming data should be filtered and/or validated!
	// *** validation: place error information into $error[] array
	/*
	 * Example:
	if (!preg_match('/^[a-z][a-z0-9._-]+@(\w+\.)+[a-z]{2,6}$/i', $data['email'])) {
		$error['email'] = '<b class="error">Invalid email address</b>';
	}
	 */
	// *** validation: check to see if form data is valid before adding to database
	require_once('./Model/Members.php');
	$member = new Members();
	// add data and retrieve last insert ID
	$newId = $member->add($data);
	// *** validation: check to see if form data is valid before sending email
	// send email confirmation
	$member->confirm($newId, $data);
	// *** redirect to the confirmation page if valid or if 1st time
}
?>
	<div class="content">
	<br/>
	<div class="product-list">

		<h2>Sign Up</h2>
		<br/>

		<b>Please enter your information.</b>
		<br/>
		<br/>
		<?php if ($mailStatus) echo '<br /><b class="confirm">', $mailStatus, '</b><br />'; ?>
		<br />
		<form action="?page=addmember" method="POST">
			<p>
				<!-- // *** validation: birthdate validation is already done -->
				<label>Birthdate: </label>
				<select name="data[dobyear]">
					<?php if ($data['dobyear']) { echo '<option>', $data['dobyear'], '</option>'; } ?>
					<?php $year = date('Y'); ?>
					<?php for($x = $year; $x > ($year - 120); $x--) { ?>
						<option><?php echo $x; ?></option>
					<?php }		?>
				</select>
				<select name="data[dobmonth]">
					<?php
					$month = array(1 => 'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
					if ($data['dobmonth']) {
						printf('<option value="%02d">%s</option>',
							   $data['dobmonth'], $month[(int) $data['dobmonth']]);
					}
					for($x = 1; $x <= 12; $x++) {
						printf('<option value="%02d">%s</option>', $x, $month[$x]);
						echo PHP_EOL;
					}
					?>
				</select>
				<select name="data[dobday]">
					<?php if ($data['dobday']) { echo '<option>', $data['dobday'], '</option>'; } ?>
					<?php for($x = 1; $x < 32; $x++) { ?>
						<option><?php echo $x; ?></option>
					<?php }		?>
				</select>
				<?php if ($error['dob']) echo '<p>', $error['dob']; ?>
			<p>
				<label>Email: </label>
				<!-- // *** security: all values should use output escaping -->
				<!-- // Example:  echo htmlspecialchars($data['email']); -->
				<input type="text" name="data[email]" value="<?php echo $data['email']; ?>" />
				<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
				<?php if ($error['email']) echo '<p>', $error['email']; ?>
			<p>
			<p>
				<label>First Name: </label>
				<input type="text" name="data[firstname]" value="<?php echo $data['firstname']; ?>" />
				<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
				<?php if ($error['firstname']) echo '<p>', $error['firstname']; ?>
			<p>
			<p>
				<label>Last Name: </label>
				<input type="text" name="data[lastname]" value="<?php echo $data['lastname']; ?>" />
				<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
				<?php if ($error['lastname']) echo '<p>', $error['lastname']; ?>
			<p>
			<p>
				<label>Photo: </label>
				<input type="text" name="data[photo]" value="<?php echo $data['photo']; ?>" />
				<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
				<?php if ($error['photo']) echo '<p>', $error['photo']; ?>
			<p>
			<p>
				<label>Address: </label>
				<input type="text" name="data[address]" value="<?php echo $data['address']; ?>" />
				<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
				<?php if ($error['address']) echo '<p>', $error['address']; ?>
			<p>
			<p>
				<label>City: </label>
				<input type="text" name="data[city]" value="<?php echo $data['city']; ?>" />
				<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
				<?php if ($error['city']) echo '<p>', $error['city']; ?>
			<p>
			<p>
				<label>State/Province: </label>
				<input type="text" name="data[stateProv]" value="<?php echo $data['stateProv']; ?>" />
				<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
				<?php if ($error['stateProv']) echo '<p>', $error['stateProv']; ?>
			<p>
			<!-- // *** validation: implement a database lookup -->
			<p>
				<label>Country: </label>
				<input type="text" name="data[country]" value="<?php echo $data['country']; ?>" />
				<!-- // *** make sure your validation checks above add info to $error[] for this field -->
				<?php if ($error['country']) echo '<p>', $error['country']; ?>
			<p>
			<p>
				<label>Postcode: </label>
				<input type="text" name="data[postcode]" value="<?php echo $data['postcode']; ?>" />
				<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
				<?php if ($error['postcode']) echo '<p>', $error['postcode']; ?>
			<p>
			<p>
				<label>Telephone: </label>
				<input type="text" name="data[telephone]" value="<?php echo $data['telephone']; ?>" />
				<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
				<?php if ($error['telephone']) echo '<p>', $error['telephone']; ?>
			<p>
			<p>
				<label>Password: </label>
				<input type="text" name="data[password]" value="<?php echo $data['password']; ?>" />
				<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
				<?php if ($error['password']) echo '<p>', $error['password']; ?>
			<p>
			<!-- // *** proper access controls: add a security question + answer -->
			<p>
				<input type="reset" name="data[clear]" value="Clear" class="button"/>
				<input type="submit" name="data[submit]" value="Submit" class="button marL10"/>
			<p>
		</form>
	</div><!-- product-list -->
</div>
