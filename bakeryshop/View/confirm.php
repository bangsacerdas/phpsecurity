<?php
// initialize variables
$result = FALSE;
$id     = 0;

// get Members table
require './Model/Members.php';
$memberTable = new Members();

if (isset($_GET['id'])) {
	// *** should filter all user-supplied parameters
	$id = $_GET['id'];
	$result = $memberTable->getDetailsById($id);
	// *** proper access controls: lookup security question + answer
	if ($result) {
		$memberTable->finishConfirm($id);
	}
}

// *** proper access controls: validate security question + answer when form is submitted
?>
<div class="content">

<br/>
<div class="product-list">
	<h2>Confirm Membership</h2>
	<?php if ($result) : ?>
	<!-- // *** proper access controls: ask user to login again -->
	Welcome to the club!
	<br />Thanks for confirming your membership <?php echo $result['name']; ?>.
	<?php else : ?>
	Sorry!!!
	<br />Unable to confirm your membership just yet.
	<br />Check in your email inbox for a confirmation message.
	<?php endif; ?>
</div>
<br class="clear-all"/>
</div><!-- content -->
