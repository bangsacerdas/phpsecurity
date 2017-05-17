<?php
// FILE: protect_access_password_reset.php
// when resetting a password, re-authenticate using a security question

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
$question     = '';

// accept password and convert to 80 byte hash using "ripemd256" algorithm
$user['email'] 	  = (isset($_REQUEST['email'])) ? filter_var($_REQUEST['email'], FILTER_SANITIZE_EMAIL) : NULL;
$user['answer']	  = (isset($_POST['answer']))   ? preg_replace('/[^a-zA-Z0-9 ]/', '', $_POST['answer']) : NULL;
// do not filter or hash the password at this stage
$user['password'] = (isset($_POST['password'])) ? $_POST['password'] : NULL;
$password2		  = (isset($_POST['password2'])) ? $_POST['password2'] : NULL;

// read email, password, security ques + answers from file
$userList = $model->getUserList();

// pull up security question and answer
if ($user['email'] && isset($userList[$user['email']])) {
	$question = $userList[$user['email']]['question'];
	$answer   = $userList[$user['email']]['answer'];
	// check to see if security answer matches
	if (isset($_POST['confirm']) && $user['answer'] == $answer) {
		// validate against "rules"
		$message = $model->validatePassword($user['password']);
		// add to user list if no error messages
		if (count($message) == 0) {
			$match = TRUE;
			// convert to 80 byte hash using "ripemd256" algorithm before saving
			$userList[$user['email']]['password'] = hash('ripemd256', $user['password']);
			$model->saveUserList($userList);
		}
	}
} else {
	$message[] = 'Unable to locate this user';
}	

echo $view->htmlHead();
echo $view->htmlReset($user, $question, $match);
echo $view->htmlUserList($userList);
phpinfo(INFO_VARIABLES);
echo $view->htmlFinal();
