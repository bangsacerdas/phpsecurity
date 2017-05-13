<?php
// *** sql injection: Even though this uses PDO, there are many badly written statements + unfiltered parameters
// PHP and MySQL Project
// members table data class

class Members
{
	public $debug = TRUE;
	protected $db_pdo;
	public $membersPerPage = 12;
	public $howManyMembers = 0;

	/*
	 * Returns array of arrays where each sub-array = 1 database row of Members
	 * @param int $offset [optional]
	 * @return array $row[] = array('title' => title, 'description' => description, etc.)
	 */
	public function getAllMembers($offset = 0)
	{
		$pdo = $this->getPdo();
		// *** Need to filter $offset by setting data type to int
		$sql = 'SELECT `user_id`,`photo`,`name`,`city`,`email` FROM `members` ORDER BY `name` LIMIT ' . $this->membersPerPage . ' OFFSET ' . $offset;
		$stmt = $pdo->query($sql);
		$content = array();
		// *** sql injection: use FETCH_ASSOC to get more precision
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$content[] = $row;
		}
		return $content;
	}
	/*
	 * Returns database row for 1 member
	 * @param int $id = member ID
	 * @return array $row[] = array('title' => title, 'description' => description, etc.)
	 */
	public function getDetailsById($id)
	{
		$pdo = $this->getPdo();
		// *** should use parameterized query with a prepared statement
		$sql = 'SELECT * FROM `members` WHERE `user_id` = ' . $id;
		$stmt = $pdo->query($sql);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
	}
	/*
	 * Returns database row for 1 member
	 * @param string $email
	 * @return array $row[] = array('title' => title, 'description' => description, etc.)
	 */
	public function loginByName($email, $password)
	{
		$pdo = $this->getPdo();
		// *** protect the database: rewrite using a prepared statement
		// *** protect the database: make sure all data is safely quoted
		$sql = "SELECT * FROM `members` WHERE `email` = '" . $email . "' AND `password` = '" . $password . "'";
		$stmt = $pdo->query($sql);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
	}
	public function getHowManyMembers()
	{
		if (!$this->howManyMembers) {
			$pdo = $this->getPdo();
			$sql = 'SELECT COUNT(*) FROM `members`';
			$stmt = $pdo->query($sql);
			// fetches as a numeric array
			$result = $stmt->fetch(PDO::FETCH_NUM);
			$this->howManyMembers = $result[0];
		}
		return $this->howManyMembers;
	}
	/*
	 * Returns array of arrays where each sub-array = 1 database row of Members
	 * Searches name, address, city, state_province, country, email
	 * @param string $search
	 * @return array $row[] = array('title' => title, 'description' => description, etc.)
	 */
	public function getMembersByKeyword($search)
	{
		// *** Need to perform filtering on $search to safeguard against SQL injection
		$search = "'%" . $search . "%'";
		$pdo = $this->getPdo();
		// *** protect the database: should have a sanity check to make sure record exists before deleting
		// *** protect the database: should use parameterized query with a prepared statement
		$sql = 'SELECT `user_id`,`photo`,`name`,`city`,`email` FROM `members` WHERE '
			  . '`name` LIKE ' . $search . ' OR '
			  . '`city` LIKE ' . $search . ' OR '
			  . '`email` LIKE ' . $search . ' ORDER BY `name`';
		$stmt = $pdo->query($sql);
		$content = array();
		// *** sql injection: use FETCH_ASSOC to get more precision
		while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$content[] = $row;
		}
		return $content;
	}
	/*
	 * Removes database row for 1 member
	 * @param int $id = member ID
	 * @return int rows affected
	 */
	public function remove($id)
	{
		$result = FALSE;
		$pdo = $this->getPdo();
		// *** protect the database: should have a sanity check to make sure record exists before deleting
		// *** protect the database: filter the $id to (int)
		return $pdo->exec('DELETE FROM `members` WHERE `user_id` = ' . $id);
	}
	/**
	 * Adds member to database (from admin page)
	 * @param array $data
	 * @return boolean $success
	 */
	public function adminAdd($data)
	{
		// *** protect the database: make sure all data is safely quoted
		// *** protect the database: rewrite using a prepared statement
		$sql = 'INSERT INTO `members` SET ';
		$sql .= "`user_id` = '" 	. $data['user_id'] 			. "',";
		$sql .= "`name` = '" 		. $data['name'] 			. "',";
		$sql .= "`address` = '" 	. $data['address'] 			. "',";
		$sql .= "`city` = '" 		. $data['city'] 			. "',";
		$sql .= "`state_province`='". $data['state_province'] 	. "',";
		$sql .= "`country` = '" 	. $data['country']	 		. "',";
		$sql .= "`postal_code` = '" . $data['postal_code'] 		. "',";
		$sql .= "`phone` = '" 		. $data['phone'] 			. "',";
		$sql .= "`email` = '" 		. $data['email'] 			. "',";
		$sql .= "`dob` = '" 		. $data['dob']	 			. "',";
		$sql .= "`photo` = '" 		. $data['photo']			. "',";
		// *** password: do not store as plain text!
		$sql .= "`password` = '" 	. $data['password'] 		. "',";
		$sql .= "`balance` = '" 	. $data['balance'] 			. "';";
		$pdo = $this->getPdo();
		return $pdo->query($sql);
	}
	/**
	 * Adds member to database
	 * @param array $data
	 * @return boolean $success
	 */
	public function add($data)
	{
		// *** protect the database: make sure all data is safely quoted
		// *** protect the database: rewrite using a prepared statement
		$sql = 'INSERT INTO `members` SET ';
		$sql .= "`name` = '" 		. $data['firstname'] . ' ' . $data['lastname'] 	. "',";
		$sql .= "`address` = '" 	. $data['address'] 								. "',";
		$sql .= "`city` = '" 		. $data['city'] 								. "',";
		$sql .= "`state_province`='". $data['stateProv'] 							. "',";
		$sql .= "`country` = '" 	. $data['country']	 							. "',";
		$sql .= "`postal_code` = '" . $data['postcode'] 							. "',";
		$sql .= "`phone` = '" 		. $data['telephone'] 							. "',";
		$sql .= "`email` = '" 		. $data['email'] 								. "',";
		$sql .= "`dob` = '" 		. $data['dob']	 								. "',";
		$sql .= "`photo` = '" 		. $data['photo']	 								. "',";
		// *** password: do not store as plain text!
		$sql .= "`password` = '" 	. $data['password'] 							. "',";
		$sql .= "`balance` = '0';";
		$pdo = $this->getPdo();
		$result = FALSE;
		if ($pdo->query($sql)) {
			// get last insert id
			$result = $pdo->lastInsertId();
		}
		return $result;
	}
	/*
	 * Sends out email confirmation
	 * @param int $newId
	 * @param array $data
	 * @return string $mailStatus
	 */
	public function confirm($newId, $data)
	{
		require_once __DIR__ . '/../PHPMailer/class.phpmailer.php';
		$address = "info@sweetscomplete.com";
		$newName = $data['firstname'] . ' ' . $data['lastname'];
		$mail = new PHPMailer(); // defaults to using php "mail()"
		$body = 'Welcome to SweetsComplete ' . $newName . '!'
				. '<br />To confirm your membership just reply to this email and we\'ll do the rest.'
				. '<br />'
			    . '<a href="' . HOME_URL . '?page=confirm&id=' . $newId . '">CLICK HERE</a> '
			    . 'CLICK HERE</a> to confirm your new membership account.'
				. '<br />Happy eating!';
		$mail->AddReplyTo($address,"SweetsComplete");
		$mail->SetFrom($address,"SweetsComplete");
		$mail->AddAddress($data['email'], $newName);
		$mail->AddBCC($address,"SweetsComplete");
		$mail->Subject = 'SweetsComplete Membership Confirmation';
		$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		$mail->MsgHTML($body);
		if(!$mail->Send()) {
			$mailStatus = 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
			$mailStatus = 'Confirmation Email Message sent!';
		}
		return $mailStatus;
	}
	/*
	 * Confirms membership based on ID
	 * @param int $id = member ID
	 * @return boolean
	 */
	// *** protect database: add confirmation code
	public function finishConfirm($id)
	{
		$pdo = $this->getPdo();
		// *** protect database: filter $id as (int)
		$sql = 'UPDATE `members` SET `status` = 1 WHERE `user_id` = ' . $id;
		return $pdo->exec($sql);
	}
	/*
	 * Returns a PDO connection
	 * If connection already made, returns that instance
	 * @return PDO $pdo
	 */
	public function getPdo()
	{
		// *** Need to turn off error mode in production
		if (!$this->db_pdo) {
			$this->db_pdo = new PDO(DB_DSN, DB_USER, DB_PWD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
		}
		return $this->db_pdo;
	}

}
