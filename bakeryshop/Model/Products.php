<?php
// *** This should be rewritten using PDO, parameterized queries, and prepared statements
// PHP and mysqli Project
// products table data class

class Products
{
	public $companyName = 'bakeryshop';
	public $page 		= 'Home';
	public $debug		= TRUE;
	public $productsPerPage = 9;
	public $howManyProducts = 0;

	protected $dbh_internal = NULL;

	public function __destruct()
	{
		if ($this->dbh_internal) {
			mysqli_close($this->dbh_internal);
		}
	}

	/*
	 * Returns database row for $productsPerPage number of products
	 * @param int $offset
	 * @return array(array $row[] = array('title' => title, 'description' => description, etc.))
	 */
	public function getProducts($offset = 0)
	{
		$dbh = $this->getDbh();
		$sql = 'SELECT * FROM `products` ORDER BY `title` LIMIT ' . $this->productsPerPage . ' OFFSET ' . $offset;
		$stmt = mysqli_query($this->dbh_internal,$sql);
		$content = array();
		while ($row = mysqli_fetch_assoc($stmt)) {
			$content[] = $row;
		}
		return $content;
	}
	/*
	 * Returns database rows for all products
	 * @return array(array $row[] = array('title' => title, 'description' => description, etc.))
	 */
	public function getAllProducts()
	{
		$dbh = $this->getDbh();
		$sql = 'SELECT * FROM `products`';
		$stmt = mysqli_query($this->dbh_internal,$sql);
		$content = array();
		while ($row = mysqli_fetch_assoc($stmt)) {
			$content[] = $row;
		}
		return $content;
	}
	/*
	 * Returns an associative array with product_id as key and title as value for all products
	 * @return array['product_id'] = title
	 */
	public function getProductTitles()
	{
		$dbh = $this->getDbh();
		$sql = 'SELECT `product_id`, `title` FROM `products`';
		$stmt = mysqli_query($this->dbh_internal,$sql);
		$content = array();
		while ($row = mysqli_fetch_assoc($stmt)) {
			$content[$row['product_id']] = $row['title'];
		}
		asort($content, SORT_STRING);
		return $content;
	}
	/*
	 * Returns database row for 1 product
	 * @param int $id = product ID
	 * @return array $row[] = array('title' => title, 'description' => description, etc.)
	 */
	public function getDetailsById($id)
	{
		$dbh = $this->getDbh();
		// *** should use a prepared statement
		$sql = 'SELECT * FROM `products` WHERE `product_id` = ' . $id;
		$stmt = mysqli_query($this->dbh_internal,$sql);
		$result = mysqli_fetch_assoc($stmt);
		return $result;
	}
	/**
	 * Returns a count of how many products are in the products table
	 * @return int COUNT(*)
	 */
	public function getHowManyProducts()
	{
		if (!$this->howManyProducts) {
			$dbh = $this->getDbh();
			$sql = 'SELECT COUNT(*) FROM `products`';
			$stmt = mysqli_query($this->dbh_internal,$sql);
			// fetches as a numeric array
			$result = mysqli_fetch_row($stmt);
			$this->howManyProducts = $result[0];
		}
		return $this->howManyProducts;
	}
	/*
	 * Returns array of arrays where each sub-array = 1 database row of products
	 * Returns only those products which are on special
	 * @param int $limit = how many specials to show
	 * @return array $row[] = array('title' => title, 'description' => description, etc.)
	 */
	public function getProductsOnSpecial($limit = 0)
	{
		$dbh = $this->getDbh();
		$sql = 'SELECT * FROM `products` WHERE `special` = 1 ORDER BY `title`';
		if ($limit) {
			$sql .= ' LIMIT ' . $limit;
		}
		$stmt = mysqli_query($this->dbh_internal,$sql);
		$content = array();
		while ($row = mysqli_fetch_assoc($stmt)) {
			$content[] = $row;
		}
		return $content;
	}
	/*
	 * Returns array of arrays where each sub-array = 1 database row of products
	 * Searches title and description fields
	 * @param string $search
	 * @return array $row[] = array('title' => title, 'description' => description, etc.)
	 */
	public function getProductsByTitleOrDescription($search)
	{
		// *** filtering: strip out any unwanted characters to help prevent SQL injection
		$search = "'%" . $search . "%'";
		$dbh = $this->getDbh();
		// *** should use a prepared statement
		$sql = 'SELECT * FROM `products` WHERE '
			  . '`title` LIKE ' . $search . ' OR '
			  . '`description` LIKE ' . $search . ' ORDER BY `title`';
		$stmt = mysqli_query($this->dbh_internal,$sql);
		$content = array();
		while ($row = mysqli_fetch_assoc($stmt)) {
			$content[] = $row;
		}
		return $content;
	}
	/*
	 * Returns all products in shopping cart from $_SESSION
	 * @return array $row[] = array('title' => title, 'description' => description, etc.)
	 */
	public function getShoppingCart()
	{
		$content = (isset($_SESSION['cart'])) ? $_SESSION['cart'] : array();
		return $content;
	}
	/*
	 * Adds purchase to basket
	 * @param int $id = product ID
	 * @param int $quantity
	 * @param float $price (NOTE: sale_price in the `purchases` table = $quantity * $price
	 * @return boolean $success
	 */
	public function addProductToCart($id, $quantity, $price)
	{
		$item = $this->getDetailsById($id);
		$item['qty'] 		= $quantity;
		$item['price']		= $price;
		$item['notes']		= 'Notes';
		$_SESSION['cart'][] = $item;
		return TRUE;
	}
	/*
	 * Removes purchase from basket
	 * @param int $productID
	 * @return boolean $success
	 */
	public function delProductFromCart($productID)
	{
		$removed = FALSE;
		if (isset($_SESSION['cart'])) {
			foreach ($_SESSION['cart'] as $key => $row) {
				if ($row['product_id'] == $productID) {
					unset($_SESSION['cart'][$key]);
					$removed = TRUE;
				}
			}
		}
		return $removed;
	}
	/*
	 * Updates purchase from basket
	 * @param int $productID
	 * @param string $notes
	 * @param int $qty
	 * @return boolean $success
	 */
	public function updateProductInCart($productID, $qty, $notes)
	{
		$updated = FALSE;
		if (isset($_SESSION['cart'])) {
			foreach ($_SESSION['cart'] as $key => $row) {
				if ($row['product_id'] == $productID) {
					$_SESSION['cart'][$key]['qty'] 	 = $qty;
					$_SESSION['cart'][$key]['notes'] = $notes;
					$updated = TRUE;
				}
			}
		}
		return $updated;
	}

	/*
	 * Returns a safely quoted value
	 * @param string $value
	 * @return string $quotedValue
	 */
	public function quoteValue($value)
	{

		return mysqli_real_escape_string($value);
	}

	/**
	 * Returns a mysqli database handle
	 * @throws Exception
	 * @return resource $dbh
	 */
	public function getDbh()
	{
		if (!$this->dbh_internal) {
			// *** warnings should be suppressed in production
			$this->dbh_internal = mysqli_connect(DB_HOST, DB_USER, DB_PWD);
			if (!$this->dbh_internal) {
				throw new Exception(mysqli_error());
			}
		}
		mysqli_select_db($this->dbh_internal,DB_NAME);
		return $this->dbh_internal;
	}

}
