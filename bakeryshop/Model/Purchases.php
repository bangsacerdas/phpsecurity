<?php

class Purchases
{

	protected $db_pdo;
	/*
	 * Returns array of arrays where each sub-array = Purchase info + product info
	 * @param int $id = member ID
	 * @return array $row[] = array('title' => title, 'description' => description, etc.)
	 */
	public function getHistoryById($id)
	{
		$pdo = $this->getPdo();
		$sql = 'SELECT * FROM `purchases` AS u JOIN `products` AS p ON u.product_id = p.product_id WHERE u.user_id = ' . $id;
		$stmt = $pdo->query($sql);
		$content = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$content[] = $row;
		}
		return $content;
	}

	/*
	 * Returns a safely quoted value
	* @param string $value
	* @return string $quotedValue
	*/
	public function pdoQuoteValue($value)
	{
		$pdo = $this->getPdo();
		return $pdo->quote($value);
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