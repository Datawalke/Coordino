Hi <?php echo $user['User']['username'];?>,

You requested your password for your account.
We've assigned a new password which you can change along with your other user settings 
at http://<?php echo $_SERVER['SERVER_NAME'];?>/users/settings/<?php echo $user['User']['public_key'];?>

Your password is now: <?php echo $password;?>

Use the above URL to go to the settings page after logging in using this email address and password.

As always, thank you for using our site!