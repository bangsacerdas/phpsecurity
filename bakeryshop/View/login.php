<?php
// get Members table
require './Model/Members.php';
$memberTable = new Members();

// check to see if login
if (isset($_POST['data'])) {
	// *** take security precautions: filter all incoming data!
	$email 		= $_POST['data']['email'];
	$password 	= $_POST['data']['password'];
	if ($email && $password) {
		$result = $memberTable->loginByName($email, $password);
		// *** session hijacking protection: implement 1 way hash & CAPTCHA
		if ($result) {
			// store user info in session
			$_SESSION['user'] = $result;
			$_SESSION['login'] = TRUE;
		} else {
			$_SESSION['login'] = FALSE;
		}
		// redirect back home
		header('Location: ?page=home');
		exit;
	}
}

?>
<div class="content">
	<br/>
	<div class="product-list">

		<h2>Login</h2>
		<br/>

		<b>Please enter your information.</b><br/><br/>
		<form action="?page=login" method="POST">
			<p>
				<label>Email: </label>
				<!-- // *** consider using the HTML5 "email" type instead of "text" -->
				<input type="text" name="data[email]" />
			<p>
			<p>
				<label>Password: </label>
				<!-- // *** consider using the "password" type instead of "text" -->
				<input type="text" name="data[password]" />
			<p>
			<p>
				<!-- // *** session hijacking prevention: add 1 way hash + CAPTCHA -->
				<input type="reset" name="data[clear]" value="Clear" class="button"/>
				<input type="submit" name="data[submit]" value="Submit" class="button marL10"/>
			<p>
		</form>
	</div><!-- product-list -->
</div>
