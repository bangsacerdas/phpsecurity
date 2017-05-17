<?php

class View
{
	public function htmlHead()
	{
		return <<<EOT
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Password Control</title>
<style>
td {
	border: thin solid black;
}
th {
	font-weight: bold;
	border: thin solid black;
}
</style>
</head>
EOT;
	
	}
	
	public function htmlUserList($userList)
	{
		$end = <<<EOT
<br />
User List:
<pre>
<table>
<tr><th>Email</th><th>Hash</th><th>Question</th><th>Answer</th><th>Status</th></tr>
EOT;

		foreach ($userList as $key => $value) {
			$end .= '<tr>';
			$end .= '<td>' . $value['email'] 	. '</td>';
			$end .= '<td>' . $value['password'] . '</td>';
			$end .= '<td>' . $value['question'] . '</td>';
			$end .= '<td>' . $value['answer'] 	. '</td>';
			$end .= '<td>' . $value['status'] 	. '</td>';
			$end .= '</tr>' . PHP_EOL;
		}

		$end .= '</table></pre>';
		return $end;
	}

	public function htmlFinal()
	{
		return '</body></html>' . PHP_EOL;
	}
	
	public function htmlLogin($match, $user, $output)
	{
		$body = <<<EOT
<body>
<h1>protect_access_password_login.php</h1>

<br />
To Do:
<ul>
<li>Enter username <i>bad@nogood.com</i> with password <i>bogus</i></li>
<li>Login should be unsuccessful</li>
<li>Enter username <i>test@test.com</i> with password <i>password</i></li>
<li>Should be OK</li>
<li>Enter new username + password and check <i>New User</i></li>
<li>Should redirect to <i>protect_access_password_set.php</i>
<li>Enter username <i>test@test.com</i> and check <i>Forgot Password</i></li>
<li>Should redirect to <i>protect_access_password_reset.php</i>
</ul>

<br />
<br />
<form method="post">
<h3>Login</h3>
<table>
<tr>
	<th>Email</th>
	<td><input type="email" name="email" maxlength=256 /></td>
</tr>
<tr>
	<th>Password</th>
	<td><input type="password" name="password" /></td>
</tr>
<tr>
	<th>&nbsp;</th>
	<td><input type="checkbox" name="new" value="1" /> New User?
		<br /><input type="checkbox" name="forgot" value="1" /> Forgot Password?</td>
</tr>
<tr>
	<td colspan=2><input type="submit" name="login" value="Login" /></td>
</tr>
</table>
</form>


<br />
Successful Login: 
EOT;
		$body .= ($match) ? '<b style="color:green;">YES</b>' : '<b style="color:red;">NO</b>' ;
		$body .= '<br />Login Attempt By:<pre>' 
			   . $user['email'] 
			   . '</pre><br />Matching Item:<pre>'
			   . $output
			   . '</pre>';
		return $body;
	}
	
	public function htmlSet($user, $confirmCode, $new, $message)
	{
		$email = htmlspecialchars($user['email']);
		$body = <<<EOT
<body>
<h1>protect_access_password_set.php</h1>

<br />
New passwords should:
<ul>
<li>Contain a mix of UPPER and lowercase letters</li>
<li>Include at least 1 number and 1 special character (i.e. !£$% etc.)</li>
<li>Be between 4 and 16 characters in length</li>
</ul>
<br />

TO TRY:
<ul>
<li>test</li>
<li>123456789</li>
<li>password123</li>
<li>Th!s15Pr0per</li>
</ul>

<br />

<form method="post">
<h3>New User</h3>
<table>
<tr>
	<th>Email</th>
	<td><input type="email" name="email" maxlength=256 value="$email" /></td>
</tr>
<tr>
	<th>Password</th>
	<td><input type="text" name="password" /></td>
</tr>
<tr>
	<th>Confirm Password</th>
	<td><input type="text" name="password2" /></td>
</tr>
<tr>
	<th>Enter Security Question</th>
	<td><input type="text" name="question" maxlength=128 /></td>
</tr>
<tr>
	<th>Enter Answer to Security Question</th>
	<td><input type="text" name="answer" maxlength=128 /></td>
</tr>
<tr>
	<td colspan=2><input type="submit" name="confirm" value="Confirm" /></td>
</tr>
</table>
</form>
EOT;

		if ($new) {
			$body .= 'Normally, at this point, a confirmation code is generated.' . PHP_EOL;
			$body .= '<br />This code would be sent through some "offline" method -- an email or an SMS text message.' . PHP_EOL;
			$body .= '<br />Make a note of the confirm code and use it in the next step:' . PHP_EOL;
			$body .= '<br />Confirm Code: <b>' . $confirmCode . '</b>' . PHP_EOL;
			$body .= '<br /><a href="protect_access_password_confirm.php?code=' . $confirmCode . '">Click Here to confirm</a>' . PHP_EOL;
		}

		$body .= implode('<br />', $message);
		
		return $body;
	}

	public function htmlConfirm($confirmCode, $match)
	{
		$body = <<<EOT
<body>
<h1>protect_access_password_confirm.php</h1>

<br />
To Do:
<ul>
<li></li>
<li>Re-enter username and password</li>
<li>Enter (or accept generated) confirm code</li>
<li>Note that status gets reset to "1"</li>
<li>Try same confirm code but wrong username or password</li>
<li>Note that confirmation is denied</li>
</ul>

<br />
<form method="post">
<h3>Confirm New User</h3>
<table>
<tr>
	<th>Email</th>
	<td><input type="email" name="email" maxlength=256 /></td>
</tr>
<tr>
	<th>Password</th>
	<td><input type="text" name="password" /></td>
</tr>
<tr>
	<th>Confirmation Code</th>
	<td><input type="text" name="confirmCode" maxlength=128 value="$confirmCode"/></td>
</tr>
<tr>
	<td colspan=2><input type="submit" name="confirm" value="Confirm" /></td>
</tr>
</table>
</form>

<br />
Successful Confirmation: 
EOT;

		$body .= ($match) ? '<b style="color:green;">YES</b>' : '<b style="color:red;">NO</b>';

		$body .= <<<EOT
<br />
<br />
Although the confirmation code is known, the user needs to reconfirm their username and password
as entered before.
<br />If an attacker hijacks the confirmation code, it will do them no good as they are
unaware of the original username and password.
<br />
EOT;

		return $body;
	}

	public function htmlReset($user, $question, $match)
	{
		$email = htmlspecialchars($user['email']);
		$body = <<<EOT
<body>
<h1>protect_access_password_reset.php</h1>

<br />
New passwords should:
<ul>
<li>Contain a mix of UPPER and lowercase letters</li>
<li>Include at least 1 number and 1 special character (i.e. !£$% etc.)</li>
<li>Be between 4 and 16 characters in length</li>
</ul>
<br />

<br />
<form method="post">
<h3>Reset Password</h3>
<table>
<tr>
	<th>Email</th>
	<td><input type="email" name="email" maxlength=256 value="$email" /></td>
</tr>
<tr>
	<th>Password</th>
	<td><input type="text" name="password" /></td>
</tr>
<tr>
	<th>Confirm Password</th>
	<td><input type="text" name="password2" /></td>
</tr>
<tr>
	<th>$question</th>
	<td><input type="text" name="answer" maxlength=128 /></td>
</tr>
<tr>
	<td colspan=2><input type="submit" name="confirm" value="Confirm" /></td>
</tr>
</table>
</form>

<br />
Successful Confirmation: 
EOT;

		$body .= ($match) ? '<b style="color:green;">YES</b>' : '<b style="color:red;">NO</b>';
	
		return $body;
	}
}
