<?php
// FILE: protect_access_password_login.php
// when logging in, you should filter all user input
// validating a username and password might give an attacker too many clues

include 'protect_access_password_user_model.php';
include 'protect_access_password_view.php';

/*
 * NOTE: this example uses a file for passwords, and is only valid for a small number of users!
 */

// initialize variables
$model 	= new UserModel();
$view  	= new View();
$match 	= FALSE;
$user 	= array();
$output = '';

// accept password and convert to 80 byte hash using "ripemd256" algorithm
$user['email'] 	  = (isset($_POST['email']))    ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : NULL;
$user['password'] = (isset($_POST['password'])) ? hash('ripemd256', $_POST['password']) : NULL;

// read email, password, security ques + answers from user list
$userList = $model->getUserList();

// is there a username (email address)?
if (isset($userList[$user['email']])) {
	// check to see if username/password matches
	if ($userList[$user['email']]['password'] == $user['password']) {
		// make sure user is confirmed!
		if ($userList[$user['email']]['status'] == 1) {
			$match = TRUE;
		}
	}
}

// check to see if "new user" checked
if (isset($_POST['new'])) {
	header('Location: protect_access_password_set.php?email=' . $user['email']);
	exit;
}

// check to see if "forgot password" checked
if (isset($_POST['forgot'])) {
	header('Location: protect_access_password_reset.php?email=' . $user['email']);
	exit;
}

echo $view->htmlHead();
echo $view->htmlLogin($match, $user, $output);
echo $view->htmlUserList($userList);
phpinfo(INFO_VARIABLES);
echo $view->htmlFinal();
