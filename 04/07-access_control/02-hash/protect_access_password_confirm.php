<?php
// FILE: protect_access_password_confirm.php
// when confirming a new account, re-authenticate
// then get confirmation code + have them answer a security question

include 'protect_access_password_user_model.php';
include 'protect_access_password_view.php';

/*
 * NOTE: this example uses a file for passwords, and is only valid for a small number of users!
 */

// initialize variables
$model 		 = new UserModel();
$view 		 = new View();
$match 	 	 = 0;
$new		 = FALSE;
$user 		 = array();
$confirmCode = (isset($_GET['code'])) ? strip_tags($_GET['code']) : '';

// accept password and convert to 80 byte hash using "ripemd256" algorithm
$user['email'] 	  = (isset($_POST['email']))    ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : NULL;
$user['password'] = (isset($_POST['password'])) ? hash('ripemd256', $_POST['password']) : NULL;
$user['status']	  = (isset($_POST['confirmCode']))	? $_POST['confirmCode'] : NULL;

// read email, password, security ques + answers from file
$userList = $model->getUserList();

// check to see if "confirm" button pressed
if (isset($_POST['confirm'])) {
	// check to see if username/password matches what is stored in file
	if (isset($userList[$user['email']]) 
			&& $userList[$user['email']]['password'] == $user['password']
			&& $userList[$user['email']]['status'] == $user['status']) {
		$match = 1;
		// reset status and save
		$userList[$user['email']]['status'] = 1;
		$model->saveUserList($userList);		
	}
}
echo $view->htmlHead();
echo $view->htmlConfirm($confirmCode, $match);
echo $view->htmlUserList($userList);
phpinfo(INFO_VARIABLES);
echo $view->htmlFinal();
