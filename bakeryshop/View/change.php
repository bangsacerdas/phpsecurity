<?php
// *** insufficient authorization: need to add security check to make sure user is admin
// *** insufficient authorization: check user status in $_SESSION['user']
// *** use Model/Acl::hasRightsToFile()

require_once('./Model/Members.php');
$member = new Members();

// *** this is not being used, but needs to be implemented as part of the validation scheme
$error = array(	  'user_id'  		=> 0,
				  'balance'  		=> 0,
				  'email' 	  		=> '',
				  'name' 			=> '',
				  'firstname' 		=> '',
				  'lastname'  		=> '',
				  'address'	  		=> '',
				  'city'	  		=> '',
				  'state_province' 	=> '',
				  'country'	  		=> '',
				  'postal_code'  	=> '',
				  'phone' 			=> '',
				  'dob'		  		=> '',
				  'password'  		=> '',
				  'photo'	  		=> '',
);

if (isset($_POST['data'])) {
	foreach ($_POST['data'] as $id => $data) {
		if ($data['del'] == 'Y') {
			$member->remove($id);
		} elseif ($data['update'] == 'Y') {
			$member->remove($id);
			$member->adminAdd($data);
		}
	}
}


?>
	<div class="content">
	<br/>
	<div class="product-list">

		<h2>Edit Members</h2>
		<br/>

		<form action="?page=change" method="POST">
		<?php if (!isset($_POST['change'])) : ?>
			No Changes!
		<?php else : ?>
			<?php require_once __DIR__ . '/../Model/Purchases.php'; ?>
			<?php $purchases = new Purchases(); ?>
			<?php foreach ($_POST['change'] as $id => $value) : ?>
				<?php if ($value == 'ok') { continue; } ?>
				<?php $data = $member->getDetailsById($id);?>
				<?php if ($value == 'history') : ?>
					<?php $history = $purchases->getHistoryById($id); ?>
					<b>Member Purchase History</b>
					<table>
					<?php
					$first = TRUE;
					foreach ($history as $purchase) {
						if ($first) {
							$first = FALSE;
							echo '<tr><th>';
							echo implode('</th><th>', array_keys($purchase));
							echo '</th></tr>' . PHP_EOL;
						}
						echo '<tr><td>';
						echo implode('</td><td>', $purchase);
						echo '</td></tr>' . PHP_EOL;
					}
					?>
					</table>
					<p>
				<?php elseif ($value == 'del') : ?>
					<b>Delete Member</b>
					<p>
						<label>Delete Member: </label>
						<!-- // *** security: all values should use output escaping -->
						<!-- // Example:  echo htmlspecialchars($data['email']); -->
						<?php echo $data['email']; ?>
					<p>
					<p>
						<label>Yes</label>
						<input type="radio" name="data[<?php echo $data['user_id']; ?>][del]" value="Y" />
					<p>
					<p>
						<label>No</label>
						<input type="radio" name="data[<?php echo $data['user_id']; ?>][del]" value="N" checked />
					<p>
				<?php else : ?>
					<b>Update Member</b>
					<p>
						<label>ID: </label>
						<!-- // *** security: all values should use output escaping -->
						<!-- // Example:  echo htmlspecialchars($data['email']); -->
						<input type="text" name="data[<?php echo $data['user_id']; ?>][user_id]" value="<?php echo $data['user_id']; ?>" />
						<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
						<?php if ($error['user_id']) echo '<p>', $error['user_id']; ?>
					<p>
					<p>
						<label>Email: </label>
						<!-- // *** security: all values should use output escaping -->
						<!-- // Example:  echo htmlspecialchars($data['email']); -->
						<input type="text" name="data[<?php echo $data['user_id']; ?>][email]" value="<?php echo $data['email']; ?>" />
						<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
						<?php if ($error['email']) echo '<p>', $error['email']; ?>
					<p>
					<p>
						<label>Name: </label>
						<input type="text" name="data[<?php echo $data['user_id']; ?>][name]" value="<?php echo $data['name']; ?>" />
						<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
						<?php if ($error['name']) echo '<p>', $error['name']; ?>
					<p>
					<p>
						<label>Date of Birth: </label>
						<input type="text" name="data[<?php echo $data['user_id']; ?>][dob]" value="<?php echo $data['dob']; ?>" />
						<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
						<?php if ($error['dob']) echo '<p>', $error['dob']; ?>
					<p>
					<p>
						<label>Account Balance: </label>
						<input type="text" name="data[<?php echo $data['user_id']; ?>][balance]" value="<?php echo $data['balance']; ?>" />
						<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
						<?php if ($error['balance']) echo '<p>', $error['balance']; ?>
					<p>
					<p>
						<label>Photo: </label>
						<input type="text" name="data[<?php echo $data['user_id']; ?>][photo]" value="<?php echo $data['photo']; ?>" />
						<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
						<?php if ($error['photo']) echo '<p>', $error['photo']; ?>
					<p>
					<p>
						<label>Address: </label>
						<input type="text" name="data[<?php echo $data['user_id']; ?>][address]" value="<?php echo $data['address']; ?>" />
						<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
						<?php if ($error['address']) echo '<p>', $error['address']; ?>
					<p>
					<p>
						<label>City: </label>
						<input type="text" name="data[<?php echo $data['user_id']; ?>][city]" value="<?php echo $data['city']; ?>" />
						<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
						<?php if ($error['city']) echo '<p>', $error['city']; ?>
					<p>
					<p>
						<label>State/Province: </label>
						<input type="text" name="data[<?php echo $data['user_id']; ?>][state_province]" value="<?php echo $data['state_province']; ?>" />
						<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
						<?php if ($error['state_province']) echo '<p>', $error['state_province']; ?>
					<p>
					<!-- // *** validation: implement a database lookup -->
					<p>
						<label>Country: </label>
						<input type="text" name="data[<?php echo $data['user_id']; ?>][country]" value="<?php echo $data['country']; ?>" />
						<!-- // *** make sure your validation checks above add info to $error[] for this field -->
						<?php if ($error['country']) echo '<p>', $error['country']; ?>
					<p>
					<p>
						<label>Postcode: </label>
						<input type="text" name="data[<?php echo $data['user_id']; ?>][postal_code]" value="<?php echo $data['postal_code']; ?>" />
						<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
						<?php if ($error['postal_code']) echo '<p>', $error['postal_code']; ?>
					<p>
					<p>
						<label>Telephone: </label>
						<input type="text" name="data[<?php echo $data['user_id']; ?>][phone]" value="<?php echo $data['phone']; ?>" />
						<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
						<?php if ($error['phone']) echo '<p>', $error['phone']; ?>
					<p>
					<p>
						<label>Password: </label>
						<input type="text" name="data[<?php echo $data['user_id']; ?>][password]" value="<?php echo $data['password']; ?>" />
						<!-- // *** validation: make sure your validation checks above add info to $error[] for this field -->
						<?php if ($error['password']) echo '<p>', $error['password']; ?>
					<p>
					<p>
						<label>Update Member: </label>
						<?php echo $data['email']; ?>
					<p>
					<p>
						<label>Yes</label>
						<input type="radio" name="data[<?php echo $data['user_id']; ?>][update]" value="Y" />
					<p>
					<p>
						<label>No</label>
						<input type="radio" name="data[<?php echo $data['user_id']; ?>][update]" value="N" checked />
					<p>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
		<p>
			<input type="reset" name="data[clear]" value="Clear" class="button"/>
			<input type="submit" name="data[submit]" value="Submit" class="button marL10"/>
		<p>
		</form>
	</div><!-- product-list -->
</div>
