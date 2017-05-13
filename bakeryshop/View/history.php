<?php
// must be logged in
$user = (isset($_SESSION['user'])) ? $_SESSION['user'] : FALSE;

// get product details
require './Model/Products.php';
$productTable = new Products();

// retrieve id
if (isset($_GET['id'])) {
	// *** filtering: force to int
	$id = $_GET['id'];
} else {
	$id = 1;
}

// pull stuff from cart table using session_id() as key
$cart = $productTable->getShoppingCart(session_id());

// back to shopping
if (isset($_POST['back'])) {
	header('Location: ?page=products');
	exit;
// process purchase
} elseif (isset($_POST['proceed'])) {
	$productTable->invoicePurchase($cart, $user);
}
?>
<div class="content">
<br/>
	<div class="product-list">
	<?php if ($user === FALSE) : ?>
	<b style="color:red;">SORRY!!!</b> You must be a member to purchase by invoice!!!
	<?php include 'checkout.php'; ?>
	<?php else : ?>
		<h2>Invoice Purchase</h2>
		<br/>
		<table>
			<tr>
				<th>Item No.</th>
				<th>Product</th>
				<th width="40%">Name</th>
				<th>Amount</th>
				<th width="10%" align="right">Price</th>
				<th width="10%" align="right">Extended</th>
			</tr>
			<?php $total = 0;					?>
			<?php foreach ($cart as $item) { 	?>
			<!-- // *** filtering: change data type of quantity and price -->
			<?php	$total += $item['quantity'] * $item['price'];	?>
			<?php	$link = '?page=detail&id=' . $item['product_id']; ?>
			<tr>
				<td><?php echo $item['sku']; ?></td>
				<td><a href="<?php echo $link; ?>">
					<img src="images/<?php echo $item['link']; ?>.scale_20.JPG" alt="<?php echo $item['title']; ?>" width="60" height="60" />
					</a>
				</td>
				<td><?php echo $item['title']; ?></td>
				<td>Qty: <span class="s0"><?php echo $item['qty']; ?></span></td>
				<td align="right"><?php printf('%8.2f', $item['price']); ?></td>
				<td align="right"><?php printf('%8.2f', $item['qty'] * $item['price']); ?></td>
			</tr>
			<?php }		?>
			<tr>
				<th colspan="4">Total:</th>
				<th colspan="2"><?php printf('%8.2f', $total); ?></th>
			</tr>
		</table>

		<br/>

		<p align="center">
			<form action="?page=checkout" method="post">
			<input type="submit" name="back" value="Back to Shopping" class="button"/>
			</form>
			<form action="?page=invoice" method="post">
			<input type="submit" name="proceed" value="Process Purchase" class="button"/>
			</form>
		<p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		</form>
	<?php endif; ?>
	</div>

</div><!-- content -->
