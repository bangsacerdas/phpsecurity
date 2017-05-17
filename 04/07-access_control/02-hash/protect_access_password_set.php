<?php
// FILE: protect_access_password_set.php
// when setting a password implement proper controls such as length, mix of characters, etc.
// be careful to avoid annoying your users with too many restrictions!

include 'protect_access_password_user_model.php';
include 'protect_access_password_view.php';

/*
 * NOTE: this example uses a file for passwords, and is only valid for a small number of users!
 */

// initialize variables
$model 		  = new UserModel();
$view  		  = new View();
$match 	 	  = FALSE;
$new		  = FALSE;
$output  	  = 'No Match';
$user 		  = array();
$confirmCode  = hash('ripemd256', date('YmdHis') . rand(1,9999));
$confirmEmail = '';
$newUser	  = array();
$message	  = array();

// accept password and convert to 80 byte hash using "ripemd256" algorithm
$user['status']	  = $confirmCode;
$user['email'] 	  = (isset($_REQUEST['email'])) ? filter_var($_REQUEST['email'], FILTER_SANITIZE_EMAIL) : NULL;
$user['question'] = (isset($_POST['question'])) ? preg_replace('/[^a-zA-Z0-9 ]/', '', $_POST['question']) : NULL;
$user['answer']	  = (isset($_POST['answer']))   ? preg_replace('/[^a-zA-Z0-9 ]/', '', $_POST['answer']) : NULL;
// do not filter or hash the password at this stage
$user['password'] = (isset($_POST['password'])) ? $_POST['password'] : NULL;
$password2		  = (isset($_POST['password2'])) ? $_POST['password2'] : NULL;

// read email, password, security ques + answers from file
$userList = $model->getUserList();

// store in pwd file if new
if (isset($_POST['confirm']) && $user['password'] == $password2) {
	// validate against "rules"
	$message = $model->validatePassword($user['password']);
	// add to user list if no error messages
	if (count($message) == 0) {
		$new = TRUE;
		// convert to 80 byte hash using "ripemd256" algorithm before saving
		$user['password'] = hash('ripemd256', $user['password']);
		$model->addUser($user);
	}
}

echo $view->htmlHead();
echo $view->htmlSet($user, $confirmCode, $new, $message);
echo $view->htmlUserList($userList);
phpinfo(INFO_VARIABLES);
echo $view->htmlFinal();
