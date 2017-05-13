<?php
// PHP and MySQL Project
// this file is the initial point of entry for the website

// *** information disclosure: make sure errors are not displayed!

// start output buffering and session
ob_start();

// *** predictable resource location: change name from PHPSESSID

session_start();

// *** session hijacking protection: regenerate session ID

// check to see if logged in
if (isset($_SESSION['login']) && $_SESSION['login']) {
	$userId = $_SESSION['user']['user_id'];
	$name 	= $_SESSION['user']['name'];
} else {
	$userId = 0;
	$name 	= 'Guest';
}

// load init file which defines constants
require './Model/Init.php';
// load View class
require './View/View.php';
$view = new View();

// get page
// *** create a "whitelist" of allowed pages
// *** HINT: use the $menus[] array already defined in View/View.php
if (isset($_GET['page'])) {
	// *** need to perform validation against the whitelist
	$page = $_GET['page'];
	// *** insufficient authorization: guests should not be allowed to launch certain pages
	// *** insufficient authorization: only admin should not be allowed to launch the "admin" page
	// *** HINT: use Model/Acl::hasRightsToPage()
} else {
	$page = 'home';
}
?>
<!DOCTYPE HTML>
<html>
<head>
<!-- // *** Need to escape user supplied info (i.e. $page)  -->
<title><?php echo $view->companyName; ?> | <?php echo ucfirst($page); ?></title>
<!-- // *** Should set character set (utf-8 recommended) -->
<meta http-equiv="Content-Type" content="text/html">
<meta name ="description" content ="bakeryshop">
<meta name="keywords" content="">
<link rel="stylesheet" href="css/main.css" type="text/css">
<link rel="shortcut icon" href="images/favicon.ico?v=2" type="image/x-icon" />
</head>
<body>
<div id="wrapper">
	<div id="maincontent">

	<div id="header">
		<div id="logo" class="left">
			<a href="index.php"><img src="images/logo.png" alt="SweetsComplete.Com"/></a>
		</div>
		<div class="right marT10">
			<b>
			<?php
			$count 	 = 0;
			$topMenu = '';
			// *** improper access controls: figure out another way to check for the admin user!!!
			// *** HINT: use an ACL to check for rights below, and remove the "if ($userId = 99)" statement
			// *** HINT: add the "admin" page to the array $menus['top'] in View/View.php
			if ($userId == 99) {
				$topMenu .= '<a href="?page=admin" ';
				$topMenu .= ($page == 'admin') ? 'class="active" ' : '';
				$topMenu .= '>Admin</a> |';
			}
			// get menus
			foreach ($view->menus['top'] as $key => $value) {
				// *** insufficient authorization: guests should not be allowed to see a list of members
				if ($key == $page) {
					$active = 'class="active" ';
				} else {
					$active = '';
				}
				$topMenu .='<a href="?page=' . $key . '" ' . $active . '>' . $value . '</a> |';
			}
			echo substr($topMenu, 0, -1);
			?>
			</b>
			<br />
			Welcome <?php echo $name; ?>
		</div>
		<ul class="topmenu">
		<?php
			foreach ($view->menus[$page] as $key => $value) {
				echo '<li><a href="?page=' . $key . '">' . $value . '</a></li>' . PHP_EOL;
			}
		?>
		</ul>
		<br>
		<div class="banner"><p></p></div>
		<br class="clear"/>
	</div> <!-- header -->

	<!-- // *** including file from user supplied info ($page) makes code injection attack possible -->
	<?php include "./View/$page.php"; ?>

	</div><!-- maincontent -->

	<div id="footer">
		<div class="footer">
			Copyright &copy; <?php echo date('Y'); ?> bakeryshop.com. All rights reserved. <br/>
		<?php
			$footerMenu = '';
			foreach ($view->menus[$page] as $key => $value) {
				$footerMenu .= '<a href="?page=' . $key . '">' . $value . '</a> | ';
			}
			echo substr($footerMenu, 0, -2);
		?>
		<br />
			<span class="contact">Tel: +44-1234567890&nbsp;
			Fax: +44-1234567891&nbsp;
			<!-- // *** should "obscure" email address to avoid harvesting, which leads to SPAM! -->
			Email:sales@bakeryshop.com</span>
		</div>
	</div><!-- footer -->

</div><!-- wrapper -->

</body>
</html>

