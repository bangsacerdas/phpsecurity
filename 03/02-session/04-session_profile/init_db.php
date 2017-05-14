<?php
class InitDb {
    protected $mysql_host 		= "localhost";
    protected $mysql_database 	= "bakeryshop";
    protected $mysql_user 		= "root";
    protected $mysql_password 	= "root";
    protected $unix_socket 		= "";
    protected $dsn 				= NULL;
    protected $pdo 				= NULL;
    protected $options			= array();
	public function __construct()
	{
		$this->dsn = "mysql:host=" . $this->mysql_host
				   . ";dbname=" . $this->mysql_database
				   . $this->unix_socket;
	}
	public function getPdo()
	{
		if (!$this->pdo) {
			$this->pdo = new PDO($this->dsn, $this->mysql_user, $this->mysql_password);
		}
		return $this->pdo;
	}
}
