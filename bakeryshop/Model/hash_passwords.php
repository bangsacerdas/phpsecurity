<?php
// *** Protect against improper access controls:
/*
 * Make sure the "password" field has increased in size to varchar(128)!!!
 * Run this file once and then rename the .php extension it so that it cannot be run again
 * Converts all passwords using the "hash()" function
 * Saves the list of passwords as "plain_text_passwords.txt"
 * Be sure to move that file someplace outside of the document root!!!
 */

ini_set('display_errors', 1);
require 'Init.php';
require 'Members.php';
$members = new Members();
$pdo = $members->getPdo();
$pwdFileName = 'plain_text_passwords.txt';

// fetch list of member IDs and passwords
$memberIDs = array();
$stmt1 = $pdo->query('SELECT `user_id`, `email`, `password` FROM `members`');
while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
	$memberIDs[] = $row;
}

// save plain text passwords to a file
$file = new SplFileObject($pwdFileName, 'w');
foreach ($memberIDs as $item) {
	$file->fwrite(implode(':', $item) . PHP_EOL);
}
$file = NULL;

// run through members table 1 ID at a time resetting password
$stmt2 = $pdo->prepare('UPDATE `members` SET `password` = :password WHERE `user_id` = :id');
echo '<pre>';
foreach ($memberIDs as $item) {
	$stmt2->execute(array(':id' 		=> $item['user_id'],
						  ':password' 	=> hash('ripemd256', $item['password'])));
	echo $item['user_id'] . ':' . $item['password'] . PHP_EOL;
}
echo '</pre>';
