<p>Hi <?php echo $user['User']['username']; ?>,</p>

<p>You requested your password for your account.
	We've assigned a new password which you can change along with your other user settings 
	<a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/users/settings/<?php echo $user['User']['public_key']; ?>">here</a>.
</p>

<p>Your password is now: <?php echo $password; ?></p>

<p>Use the above link to go to the login page, and login using this email address and password.</p>

As always, thank you for using our site!