<?php
// get product details
require './Model/Products.php';
$productTable = new Products();

// process checkout
if (isset($_POST['checkout']) && isset($_SESSION['cart'])) {
	header('Location: ?page=checkout');
	exit;

// back to shopping
} elseif (isset($_POST['back'])) {
	header('Location: ?page=products');
	exit;

// process removals / updates
} elseif (isset($_POST['change'])) {
	if (isset($_POST['remove'])) {
		foreach ($_POST['remove'] as $key => $id) {
			// *** filtering: need to filter the $id
			$productTable->delProductFromCart($id);
		}
	}
	if (isset($_POST['update'])) {
		foreach ($_POST['update'] as $key => $id) {
			// *** filtering: force to int
			// *** error handling: provide default if not set
			$qty 	= $_POST['qty'][$key];
			$notes 	= $_POST['notes'][$key];
			// *** error handling: check to make sure $qty and $id are set
			$productTable->updateProductInCart($id, $qty, $notes);
		}
	}
}

// pull stuff from cart table using session_id() as key
$cart = $productTable->getShoppingCart(session_id());
?>
<div class="content">
<br/>
	<div class="product-list">
		<h2>Shopping Basket</h2>
		<br/>
		<form action="#" method="POST">
		<table>
			<tr>
				<th>Item No.</th>
				<th>Product</th>
				<th width="40%">Name / Notes</th>
				<th>Amount</th>
				<th width="10%" align="right">Price</th>
				<th width="10%" align="right">Extended</th>
				<th>&nbsp;</th>
			</tr>
			<?php $total = 0;					?>
			<?php $key   = 0;					?>
			<?php foreach ($cart as $item) { 	?>
			<?php // *** filtering: force to int or float ?>
			<?php	$total += $item['qty'] * $item['price'];	?>
			<?php	$link = '?page=detail&id=' . $item['product_id']; ?>
			<tr>
				<td><?php echo $item['sku']; ?></td>
				<td><a href="<?php echo $link; ?>">
					<img src="images/<?php echo $item['link']; ?>.scale_20.JPG" alt="<?php echo $item['title']; ?>" width="60" height="60" />
					</a>
				</td>
				<td><?php echo $item['title']; ?>
					<!-- // *** output escaping needed! -->
					<br />Notes: <?php echo $item['notes']; ?>
					<br /><input type="text" value="Notes" name="notes[<?php echo $key; ?>]" class="s0" size="40" />
				</td>
				<!-- // *** output escaping needed! -->
				<td>Qty: <br /><input type="text" value="<?php echo $item['qty']; ?>" name="qty[<?php echo $key; ?>]" class="s0" size="2" /></td>
				<td align="right"><?php printf('%8.2f', $item['price']); ?></td>
				<td align="right"><?php printf('%8.2f', $item['qty'] * $item['price']); ?></td>
				<td><table>
					<tr>
						<td>Remove</td>
						<td><input type="checkbox" name="remove[<?php echo $key; ?>]" value="<?php echo $item['product_id']; ?>" title="Remove" /></td>
					</tr>
					<tr>
						<td>Update</td>
						<td><input type="checkbox" name="update[<?php echo $key; ?>]" value="<?php echo $item['product_id']; ?>" title="Update" /></td>
					</tr>
					</table>
				</td>
			</tr>
			<?php 	$key++; ?>
			<?php }		?>
			<tr>
				<th colspan="5">Total:</th>
				<th colspan="2"><?php printf('%8.2f', $total); ?></th>
			</tr>
		</table>

		<br/>

		<p align="center">
			<input type="submit" name="back" value="Back to Shopping" class="button"/>
			<input type="submit" name="change" value="Update" class="button"/>
			<input type="submit" name="checkout" value="Checkout" class="button"/>
		<p>
		</form>
	</div>

</div><!-- content -->
