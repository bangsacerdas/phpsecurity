<?php

class UserModel
{
	public $pwdFile = 'protect_access_password_set_reset.pwd';

	// password control rules
	protected $passwordMaxLength =  16;
	protected $passwordMinLength =  4;

	public function validatePassword($password)
	{
		$message = array();
		// minimum length check
		if (strlen($password) < $this->passwordMinLength) {
			$message[] = 'The password is too short';
		} elseif (strlen($password) > $this->passwordMaxLength) {
		// maximum length check
			$message[] = 'The password is too long';
		}
		// UPPERCASE
		if (!preg_match('/[A-Z]/', $password)) {
			$message[] = 'You must have at least 1 UPPERCASE letter';
		}
		// lowercase
		if (!preg_match('/[a-z]/', $password)) {
			$message[] = 'You must have at least 1 lowercase letter';
		}
		// numbers
		if (!preg_match('/[0-9]/', $password)) {
			$message[] = 'You must have at least 1 number';
		}
		// special characters
		if (!preg_match('/[^\w]/', $password)) {
			$message[] = 'You must have at least 1 special character';
		}
		return $message;
	}
	
	public function getUserList()
	{
		$userList = array();
		if (file_exists($this->pwdFile)) {
			$list = file($this->pwdFile);
			foreach ($list as $item) {
				$details = unserialize($item);
				$userList[$details['email']] = $details;
			}
		}
		return $userList;
	}		
	
	public function addUser($user)
	{
		$fh = new SplFileObject($this->pwdFile, 'a');
		$fh->fwrite(serialize($user) . PHP_EOL);
		$fh = NULL;		// close file handle
	}
	
	public function saveUserList($userList)
	{
		$fh = new SplFileObject($this->pwdFile, 'w');
		foreach ($userList as $item) {
			$fh->fwrite(serialize($item) . PHP_EOL);
		}
		$fh = NULL;		// close file handle
	}
	
}
