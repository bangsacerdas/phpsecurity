<?php
class Acl
{
	// assign three roles:
	public $role  = '';
	public $roles = array(0 => 'guest', 1 => 'user', 9 => 'admin');
	public $acl = array();
	// create arrays based upon View/View::$menus
	public $admin= array(
			'admin'    ,
			'change'   ,
	);
	public $user = array(
			'invoice'  ,
			'members'  ,
			'logout'   ,
			'top'  	   ,
	);
	public $guest = array(
			'thanks'   ,
			'about'    ,
			'products' ,
			'specials' ,
			'contact'  ,
			'detail'   ,
			'search'   ,
			'purchase' ,
			'cart' 	   ,
			'checkout' ,
			'home'	   ,
			'login'    ,
			'addmember',
			'confirm'  ,
	);
	// user = user + guest; admin = user + guest + admin
	public function __construct()
	{
		$this->acl['guest'] = $this->guest;
		$this->acl['user']  = array_merge($this->guest, $this->user);
		$this->acl['admin'] = array_merge($this->guest, $this->user, $this->admin);
	}
	// Checks $_SESSION['user']['status']
	public function checkStatus()
	{
		return (isset($_SESSION['user']['status'])) ? (int) $_SESSION['user']['status'] : 0;
	}
	// Gets the user role
	public function getRole()
	{
		if (!$this->role) {
			$this->role = $this->roles[$this->checkStatus()];
		}
		return $this->role;
	}
	/**
	 * Returns TRUE or FALSE if this status level is assigned this page
	 * Checks $_SESSION['user']['status']
	 * @param string $page = menu page you wish to check
	 * @return boolean
	 */
	public function hasRightsToPage($page)
	{
		if (in_array($page, $this->acl[$this->getRole()], TRUE)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	/**
	 * Returns TRUE or FALSE if this status level is assigned this page
	 * Checks $_SESSION['user']['status']
	 * @param string $filename = filename of page you want to check for rights
	 * @return boolean
	 */
	public function hasRightsToFile($filename)
	{
		$page = strtolower(basename($filename));
		$page = str_replace('.php', '', $page);
		if (in_array($page, $this->acl[$this->getRole()], TRUE)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}